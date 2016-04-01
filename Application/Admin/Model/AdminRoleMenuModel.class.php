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

class AdminRolemenuModel extends Model {
	
	protected $tableName = 'admin_role_menu';
	
	/**
	 * 获取某角色的菜单权限
	 * @param int $role_id 角色id
	 * @return array
	 */
	public function getRoleAuth($role_id){
		 
		$menu_ids = $this->where(array("role_id" => $role_id))->getField('menu_id',true);
		if($menu_ids){
			$cond['id'] = array('in',$menu_ids);
			$data = $this->where($cond)->select();
			if($data){
				$arr = array();
				foreach($data as $k=>$v){
					$arr['role_id'] = $role_id;
					$arr['g'] = $v['app'];
					$arr['m'] = $v['model'];
					$arr['a'] = $v['action'];
				}
				return $arr;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * 获取某角色的所有操作权限
	 * @param int $role_id 角色id
	 * @return array
	 */
	public function getRoleOperationAuth($role_id){
		 
		$menu_ids = $this->where(array("role_id" => $role_id))->getField('menu_id',true);
		if($menu_ids){
			$cond['id'] = array('in',$menu_ids);
			$cond['type'] = 2;
			$action = D('AdminMenu')->where($cond)->getField('action',true);
			if($action){
				$arr = array();
				foreach($action as $k=>$v){
					$arr[$v] = 1;
				}
				return $arr;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	
	
	

}