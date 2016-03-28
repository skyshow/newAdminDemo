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

class IndexController extends CommonController {
    
	/**
	 * 后台首页
	 */
    public function index(){
		
    	
        $this->assign('info',1);
    	
    	$this->display();
    	
    }
    
    public function login(){
    	
    	if(IS_POST){
    		
    		$aClean = array();
    		$aClean['account']  = I('post.account');
    		$aClean['password'] = I('post.password');
    		$aClean['captcha']  = I('post.captcha');
    		//$aClean['remember'] = I('post.remember', 'intval');
    		 
    		if(empty($aClean['account'])) {
    			$this->error('账号必须');
    		}elseif (empty($aClean['password'])){
    			$this->error('密码必须');
    		}elseif (empty($aClean['captcha'])){
    			$this->error('验证码必须输入');
    		}elseif(!check_verify($aClean['captcha'])) {
    			$this->error('验证码输入错误');
    		}
    		
    		//生成认证条件
    		$userInfo = D('AdminAccount')->verifyLogin($aClean['account'], $aClean['password']);
    		
    		//使用用户名、密码和状态的方式进行认证
    		if(false === $userInfo) {
    			$this->error('账号不存在');
    		}elseif(empty($userInfo)){
    			$this->error('密码错误');
    		}else {
    			$this->auth->login($userInfo['uid'], $userInfo);
    			$check = D('AdminAccount')->where(array('account'=>I('post.account')))->find();
    			if($check['status'] == 'disable') $this->error('此账号没有权限登陆哦！');   				
    			// 记录最后登录信息
    			D('AdminAccount')->lastLogin($userInfo['uid']);
    			 
    			// 是否记住用户名
    			if($aClean['remember']){
    				cookie('remember', $userInfo['account'], time()+3600*30);
    			}else{
    				cookie('remember', 0);
    			}
    			$this->success('登录成功！', U('Admin/Index/index'));
    		}
    	}
    	
    	$this->display();
    }
    
    // 验证码
    public function captcha() {
    	$verify = new \Think\Verify();
    	$verify->entry(1,90,30);
    }
    
   
    
    

    
}