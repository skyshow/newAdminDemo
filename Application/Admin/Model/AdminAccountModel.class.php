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
namespace Admin\Model;
use Think\Model;

class AdminAccountModel extends Model {
	
	protected $tableName = 'admin_account';
	
	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('account', 'require', '用户名不能为空！', 1, 'regex', 1 ),
			array('account', '', '用户名已经存在！', 0, 'unique', 1 ),
			array('password', 'require', '密码不能为空！', 1, 'regex', 1),
	);
	
	//自动完成
	protected $_auto = array(
			array('password','md5',3,'function'),
			array('addtime', 'mGetDate', 1, 'callback'),
			array('updatetime', 'mGetDate', 2, 'callback'),
			array('logintotal', '0'),
			array('adduid', '0'),
	);
	
	public function mGetDate() {
		return date('Y-m-d H:i:s');
	}
	
	public function adminExists($account){
		return $this->where(array('account'=>$account))->find();
	}
	
	protected function _encodePassword($password){
		return md5($password);
	}
	
	protected function _verifyPassword($storedPassword, $inputPassword){
		return !strcmp($storedPassword, $this->_encodePassword($inputPassword));
	}
	
	public function verifyLogin($account, $password){
		$userInfo = $this->where(array('account'=>$account))->find();
		if(empty($userInfo)){
			return false;
		}else if($this->_verifyPassword($userInfo['password'], $password)){
			$this->setInc('logintotal', 1);

			$data = array();
			$data['lastlogin'] = date('Y-m-d H:i:s');
			$data['loginip']   = get_client_ip();
			$this->where(array('uid'=>$userInfo['uid']))->save($data);
				
			unset($userInfo['password']);
				
			return $userInfo;
		}else{
			return array();
		}
	}
	
	public function lastLogin($uid){
		$data = array();
		$data['logintotal'] = array('exp', 'logintotal+1');
		$data['lastlogin']  = date('Y-m-d H:i:s');
		$data['loginip']    = get_client_ip();
		$this->where(array('uid'=>$uid))->save($data);
	}
	
	public function get_all_account(){
		$res = $this->field('uid, account,status, adduid, lastlogin, loginip, role_id')->select();
		foreach($res AS $k => $v){
			if($v['adduid']){
				$res[$k]['add_account'] = $this->where(array('uid' => $v['adduid']))->getField('account');
			}else{
				$res[$k]['add_account'] = '系统';
			}
			if($v['role_id']){
				$res[$k]['role_name'] = D('AdminRole')->where(array('id' => $v['role_id']))->getField('name');
			}

		}
		return $res;
	}
	
	public function changePassword($uid, $password){
		return $this->where(array('uid'=>$uid))->save(array('password'=>$this->_encodePassword($password)));
	}

	/**
	 * 获取地区分组ID
	 * @param int $uid 默认是session中存的uid
	 * @return int $groupId
	 * @author Qiugh 2015-12-23 16:28:57 
	 */
	public function getAreaGroupId($uid=null) {
		//通过session获取用户id
		$session = session();
		$sessionUid = $session["uid"];

		$map["uid"] = $uid ? $uid : $sessionUid;
		$areaGroupId = $this->where($map)->getField("area_group_id");
		return $areaGroupId;
	}

}