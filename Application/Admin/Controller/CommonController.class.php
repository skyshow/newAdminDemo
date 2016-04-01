<?php

/**
 * 
 * PHP version 5.5
 *
 * @category	Admin
 * @package     Admin
 * @subpackage  Model
 * @copyright   2016 GZSD
 * @version     GIT: index.php 2016-03-10 14:23:59 Huangzj $
 */

namespace Admin\Controller;
use \Admin\Auth as t;
use Think\Controller;
class CommonController extends Controller {
	
	protected $auth           = null;
	protected $_account       = array();
	protected $_page          = 1;
	protected $_pagesize      = 10;
	
    //初始化
    public function _initialize() {
    	
    	// 登录认证判断
    	$oAminAuth = $this->auth = new t\AdminAuth();
    	if($oAminAuth->isLogin()){

    		//用户信息
    		$this->_account = $oAminAuth->getUser();
    		$this->assign('ACCOUNT', $this->_account);
    		
    		//菜单
    		$menu_data = D("AdminMenu")->menu_json(); 
    		$this->assign('menu_data',$menu_data);

    		if(!strcasecmp(CONTROLLER_NAME, 'Index') && in_array(ACTION_NAME,array('index','login','captcha'))){
    	
    		}else{
    			if(!$this->check_access($this->_account['role_id'])){
    				$this->error("您没有访问权限！", null, -1); //不需要跳转
    			}
    			
    			//角色的操作权限
    			$operation = D('AdminRoleMenu')->getRoleOperationAuth($this->_account['role_id']);
    			if($operation) $this->assign('operation',$operation);
    			
    		}
    	}else{  
    		 	
    		$authAction = array('login','captcha');
    		if(!in_array(ACTION_NAME,$authAction)){
    			$tipMsg = (strcasecmp(ACTION_NAME, 'login'))?"操作需要登录才能进行":'';
    			$oAminAuth->redirectLogin($tipMsg);	
    		}
    	}
        
    }
    
    protected function check_access($roleid, $group = MODULE_NAME, $model = CONTROLLER_NAME, $action = ACTION_NAME){
    	//如果用户角色是1，则无需判断
    	if($roleid == 1) return true;
    	$role = D("AdminRole")->field("status")->where("id=$roleid")->find();
    
    	if(!empty($role) && $role['status']==1){
    		if($group.$model.$action != "AdminIndexindex"){
    			$menuInfo = D('AdminMenu')->field("id")->where(array("app"=>$group,"model"=>$model,"action"=>$action))->find();
    			//如果菜单有添加，则需要权限控制
    			if($menuInfo){
    				$count = D("AdminRoleMenu")->where(array("role_id"=>$roleid,"menu_id"=>$menuInfo['id']))->count(1);
    				return $count;
    			}else{
    				return true;
    			}
    		}else{
    			return true;
    		}
    	}else{
    		return false;
    	}
    }

    
}