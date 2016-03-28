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
	protected $_start_runtime = 0;
	
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

    		if(!strcasecmp(CONTROLLER_NAME, 'Index')){
    	
    		}else{
    			if(!$this->check_access($this->_account['role_id'])){
    				$this->error("您没有访问权限！", null, -1); //不需要跳转
    				exit();
    			}
    	
    			//角色的操作权限
    			$operation = D('Access')->getRoleOperationAuth($this->_account['role_id']);
    			if($operation) $this->assign('operation',$operation);
    	
    			$this->_start_runtime = getMillisecond();
    		}
    	}else{  
    		 	
    		$authAction = array('login','captcha');
    		if(!in_array(ACTION_NAME,$authAction)){
    			$tipMsg = (strcasecmp(ACTION_NAME, 'login'))?"操作需要登录才能进行":'';
    			$oAminAuth->redirectLogin($tipMsg);	
    		}
    	}
        
    }

    
}