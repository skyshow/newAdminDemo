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

class AdminRoleModel extends Model {
	
	protected $tableName = 'admin_role';
	
	
	const SUPER_ADMINISTRATOR = 1; //超级管理员
	
	//自动验证
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('name', 'require', '角色名称不能为空！', 1, 'regex', 3 ),
	);
	
	//自动完成
	protected $_auto = array(
			array('create_time', 'time', 1, 'function'),
			array('update_time', 'time', 2, 'function'),
	);
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
	
	public function get_role_list(){
		$data = $this->order(array("listorder" => "asc", "id" => "desc"))->select();
		foreach($data as $k=>$v){
			//$provider_id = D('RoleProvider')->where(array("role_id"=>$v['id']))->getField('provider_id');
			$data[$k]['provider_id'] = $provider_id;
		}
		return $data;
	}
	
	public function simple_role_list($currentid = null){
		$cond['status'] = 1;
		$cond['id'] = array('NEQ', 1);
		$data = $this->where($cond)->field('id, name')->select();
		if($currentid){
			foreach($data AS $k => $v){
				if($v['id'] == $currentid){
					$data[$k]['current'] = 1;
				}else{
					$data[$k]['current'] = 0;
				}
			}
		}
		return $data;
	}
	

}