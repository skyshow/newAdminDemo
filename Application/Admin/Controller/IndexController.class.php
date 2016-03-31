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
use \Common\Lib\Pclass as p;

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
    
    public function menu(){

    	$_SESSION['admin_menu_index'] = "Menu/index";
    	$result = D("AdminMenu")->order(array("listorder" => "ASC"))->select();
    	$tree = new p\Tree();
    	$tree->icon = array('&nbsp;│ ', '&nbsp;├─ ', '&nbsp;└─ ');
    	$tree->nbsp = '&nbsp;';
    	foreach ($result as $r) {
    		$r['str_manage'] = '<a href="' . UC("Menu/menu_add", array("parentid" => $r['id'], "menuid" => $_GET['menuid'])) . '">添加子菜单</a> | <a href="' . UC("Menu/edit", array("id" => $r['id'], "menuid" => $_GET['menuid'])) . '">修改</a> | <a class="J_ajax_del" href="' . UC("Menu/delete", array("id" => $r['id'], "menuid" => I("get.menuid"))) . '">删除</a> ';
    		$r['status'] = $r['status'] ? "显示" : "不显示";
    		$array[] = $r;
    	}
    	 
    	$tree->init($array);
    	 
    	$str = "<tr id='div_[\$id]'>
						<td>
    						<div class='checker'><span><input type='checkbox' class='checkboxes' name='[\$id]'></span></div>
						</td>
						<td>\$id</td>
						<td>\$spacer\$name</td>
						<td>\$status</td>
						<td>
							<div class='visible-md visible-lg hidden-sm hidden-xs action-buttons'>								
								<a class='btn btn-outline btn-circle green btn-sm green' href='".U("Index/menu_add")."?parentid=\$id' title='添加子菜单'>
									<i class='fa fa-plus'>子菜单</i>
								</a>
								<a class='btn btn-outline btn-circle green btn-sm purple' href='".U("Index/menu_edit")."?id=\$id' title='编辑'>
									<i class='fa fa-edit'>编辑</i>
								</a>
								<a class='btn btn-outline btn-circle dark btn-sm red' href='javascript:;' onclick='remove_vote(\$id)' title='删除'>
									<i class='fa fa-trash-o'>删除</i>
								</a>
							</div>
						</td>
					</tr>";
    	 
    	 
    	$categorys = $tree->get_tree(0, $str);
    	$this->assign("categorys", $categorys);
    	$this->display();   		
    	
    }
    
    /**
     *  添加菜单
     */
    public function menu_add(){
    	if (IS_POST) {
    		if (D("AdminMenu")->create()) {
    			if (D("AdminMenu")->add() !== false) {
    				$this->success("添加成功！", U("Index/menu"));
    			} else {
    				$this->error("添加失败！");
    			}
    		} else {
    			$this->error(D("AdminMenu")->getError());
    		}
    	} else {
    		$result = D("AdminMenu")->field('id,name,parentid')->order(array("listorder" => "ASC"))->select();
    		$tree = new p\Tree();
    		$parentid = intval(I("get.parentid"));
    		foreach ($result as $r) {
    			$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
    			$array[] = $r;
    		}
    		$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
    		$tree->init($array);
    		$select_categorys = $tree->get_tree(0, $str);
    		$this->assign("select_categorys", $select_categorys);
    		$this->assign("headline",    "添加菜单");
    		$this->assign("jump_url", U('Admin/Index/'.ACTION_NAME));
    		$this->display("Index:menu_oper");
    	}
    }
    
    /**
     *  编辑菜单
     */
    public function menu_edit(){
    	if (IS_POST) {
    		if (D("AdminMenu")->create()) {
    			if (D("AdminMenu")->save() !== false) {
    				$this->success("更新成功！", U("Admin/Index/menu"));
    			} else {
    				$this->error("更新失败！");
    			}
    		} else {
    			$this->error(D("AdminMenu")->getError());
    		}
    	} else {
    		$id = intval(I("get.id"));
    		if(!$id){
    			$this->error('编辑项不存在！');
    		}
    		$rs = D("AdminMenu")->where(array("id" => $id))->find();
    		$result = D("AdminMenu")->field('id,name,parentid')->order(array("listorder" => "ASC"))->select();
    		$tree = new p\Tree();
    		foreach ($result as $r) {
    			$r['selected'] = $r['id'] == $rs['parentid'] ? 'selected' : '';
    			$array[] = $r;
    		}
    		$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
    		$tree->init($array);
    		$select_categorys = $tree->get_tree(0, $str);
    		$this->assign("info", $rs);
    		$this->assign("select_categorys", $select_categorys);
    		$this->assign("headline",    "编辑菜单");
    		$this->assign("jump_url", U('Admin/Index/'.ACTION_NAME));
    		$this->display("Index:menu_oper");
    	}
    }
    
    
    /**
     *  删除
     */
    public function del_menu() {
    	$id = intval(I("post.id"));
    	if(!$id){
    		$this->error("删除项不存在！");
    	}
    	$count = D("AdminMenu")->where(array("parentid" => $id))->count();
    	if ($count > 0) {
    		$this->error("该菜单下还有子菜单，无法删除！");
    	}
    	if (D("AdminMenu")->delete($id)!==false) {
    		$this->success("删除菜单成功！");
    	} else {
    		$this->error("删除失败！");
    	}
    }
    
    /**
     * 后台用户管理
     */
    public function user(){

    	$list = D('AdminAccount')->get_all_account();
    	$this->assign('list', $list);
    	
    	$this->display();
    	
    }
    
    public function account_add(){
    	
    	if (IS_POST) {
    		$data = D("AdminAccount")->create();
    		if ($data) {
    			$data['adduid'] = $this->_account['uid'];
    			$data['username'] = $data['account'];
    			$insert_id = D("AdminAccount")->add($data);
    			if ($insert_id !== false) {
    				$this->success("添加成功", U("Index/user"));
    			} else {
    				$this->error("添加失败！", U("Index/user"));
    			}
    		} else {
    			$this->error(D("AdminAccount")->getError());
    		}
    	} else {
    		$role_list = D('AdminRole')->simple_role_list();
    
    		$this->assign("headline", "新增管理员");
    		$this->assign("role_list", $role_list);
    		$this->assign("jump_url", U('Admin/Index/'.ACTION_NAME));
    		$this->display("Index:account_oper");
    	}
    }
    
    /**
     * 编辑管理员
     */
    public function account_edit(){
    	
    	if (IS_POST) {
    		$id = intval(I("post.uid"));
    		if (empty($_POST['password'])) {
    			unset($_POST['password']);
    		}
    		$data = D("AdminAccount")->create();
    		if ($data) {
    			if (empty($data['password'])) unset($data['password']);
    			if (D("AdminAccount")->save($data) !== false) {
    				
    				$this->success("修改成功！", U('Index/user'));
    			} else {
    				$this->error("修改失败！" . D("AdminAccount")->getlastsql() . print_r($data, true));
    			}
    		} else {
    			$this->error(D("AdminAccount")->getError());
    		}
    	} else {
    		$uid = intval(I("get.uid"));
    		if (!$uid) {
    			$this->error("非法操作！");
    		}
    		$role_id = D('AdminAccount')->where(array('uid' => $uid))->getField('role_id');
    		if ($role_id == 1) {
    			$this->error("超级管理员不能编辑！", U('Index/user'));
    		}
    		$data = D("AdminAccount")->field('uid, account, role_id')->where(array("uid" => $uid))->find();
    		if (!$data) {
    			$this->error("编辑项不存在！");
    		}
    		
    		$role_list = D('AdminRole')->simple_role_list($data['role_id']);
    		$this->assign("headline", "编辑管理员");
    		$this->assign("role_list", $role_list);
    		$this->assign("jump_url", U('Admin/Index/'.ACTION_NAME));
    		$this->assign("info", $data);
    		$this->display("Index:account_oper");
    	}
    }
    
    /**
     * 删除
     */
    public function del_account(){
    	
    	$id = I("post.uid");
    	if (!$id) {
    		$this->error("删除项不存在！", U('Index/user'));
    	}
    	$role_id = D('AdminAccount')->where(array('uid' => $id))->getField('role_id');
    	if ($role_id == 1) {
    		$this->error("超级管理员不能删除！", U('Index/user'));
    	}
    	$status = D("AdminAccount")->delete($id);
    	if ($status !== false) {
    		$this->success("删除成功！", U('Index/user'));
    	} else {
    		$this->error("删除失败！", U('Index/user'));
    	}
    }
    
    /**
     * 角色管理
     */
    public function role(){
    	
    	$data = D("AdminRole")->get_role_list();
    	$this->assign("list", $data);

    	$this->display();
    	
    }
    
    /**
     * 添加角色
     */
    public function role_add() {
    	if (IS_POST) {
    		if (D("AdminRole")->create()) {
    			$id = D("AdminRole")->add();
    			if ($id) {
    				$this->success("添加角色成功",U("Index/role"));
    			} else {
    				$this->error("添加失败！",U("Index/role"));
    			}
    		} else {
    			$this->error(D("AdminRole")->getError());
    		}
    	} else {
    
    		$this->assign("headline", "新增角色");
    		$this->assign("jump_url", U('Admin/Index/'.ACTION_NAME));
    		$this->display("Index:role_oper");
    	}
    }
    
    /**
     * 删除角色 角色只能单个删除
     */
    public function del_role() {
    	$id = intval(I("post.id"));
    	if ($id == 1) {
    		$this->error("超级管理员角色不能被删除！");
    	}
    	$count = D('AdminAccount')->where(array('role_id' => $id))->count();
    	if($count){
    		$this->error("该角色已经有用户！");
    	}else{
    		$status = D("AdminRole")->delete($id);
    		if ($status !== false) {
    			$this->success("删除成功！", U('Index/role'));
    		} else {
    			$this->error("删除失败！");
    		}
    	}
    }
    
    /**
     * 编辑角色
     */
    public function role_edit() {
    	if (IS_POST) {
    		$id = intval(I("post.id"));
    		if ($id == 1) {
    			$this->error("超级管理员角色不能被修改！");
    		}
    		$data = D("AdminRole")->create();
    		if ($data) {
    			if (D("AdminRole")->save($data)!== false) {
    				$this->success("修改成功！", U('Index/role'));
    			} else {
    				$this->error("修改失败！".D("AdminRole")->getlastsql().print_r($data, true));
    			}
    		} else {
    			$this->error(D("AdminRole")->getError());
    		}
    	}else{
    		$id = intval(I("get.id"));
    		if (!$id) {
    			$this->error("非法操作！");
    		}
    		if ($id == 1) {
    			$this->error("超级管理员角色不能被修改！");
    		}
    		$data = D("AdminRole")->field('id, status, remark, name')->where(array("id" => $id))->find();
    		if (!$data) {
    			$this->error("该角色不存在！");
    		}
    
    		$this->assign("headline", "编辑角色");
    		$this->assign("jump_url", U('Admin/Index/'.ACTION_NAME));
    		$this->assign("info",  $data);
    		$this->display("Index:role_oper");
    	}
    }
    
   
    
    

    
}