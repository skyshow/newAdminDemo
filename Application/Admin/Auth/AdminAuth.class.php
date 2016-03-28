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
namespace Admin\Auth;

class AdminAuth extends WebAuth{
	
	protected $_loginPath = '/admin/index/login';
	
	/**
	 * 判断是否登陆
	 *
	 * @return bool 已经登陆返回true, 尚未登陆返回false
	 */
	public function isLogin()
	{
		if ($this->isEmpty('uid')) return false;
	
		$uid       = $this->getUid();
		$loginTime = $this->getLoginTime();
		$loginIp   = $this->getLoginIp();
		$signin    = $this->read('signin');
		if ($signin && $uid && !strcmp($signin, crc32($uid.$loginTime.$loginIp))){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 跳转到登入页面
	 *
	 * 注：本函数会立即退出程序执行
	 *
	 * @param string $redirect 回跳链接
	 */
	public function redirectLogin($tips=array(), $redirect=null)
	{
		if(!$redirect) $redirect = U('Admin/' . CONTROLLER_NAME . '/' . ACTION_NAME);
		
		$goto = U('Admin/Index/login');
		redirect($goto);
	}
	
	/**
	 * 写入登入session
	 *
	 * @param string $uid uid
	 * @param string $uname 登入名
	 */
	public function login($uid, $user=NULL)
	{
		$sin_time = time();
		$sin_ip = get_client_ip();
	
		$array = array(
				'platform'        => 'gzsd',       
				'uid' 			  => $uid,
				'user'            => $user,
				'signin'	      => crc32($uid.$sin_time.$sin_ip),
				'loginTime'       => $sin_time,
				'loginIp'         => $sin_ip,
		);
		
		$this->write($array);
	}
	
	/**
	 * 销毁登入session
	 *
	 * @return void
	 * @access public
	 */
	public function logout()
	{
		$this->destroy();
	}
	
	public function getUid(){return $this->read('uid');}
	
	public function getUser($field=NULL){
		$userInfo = $this->read('user');
		return !$field?$userInfo:$userInfo[$field];
	}
	public function getLoginTime(){return $this->read('loginTime');}
	public function getLoginIp(){return $this->read('loginIp');}
	
	public function checkAccess($type){
		$allow = false;
		$userInfo = $this->getUser();
		$allow = $type && !empty($userInfo) && (in_array('all', $userInfo['permissions']) || in_array($type, $userInfo['permissions']));
		return $allow;
	}
	
	public function isSuper(){
		return !strcasecmp($this->getUser('type'), 'super');
	}
	
	public function isAdmin(){
		return !strcasecmp($this->getUser('type'), 'admin');
	}
}