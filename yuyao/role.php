<?php 
/**
 * 客户分类
 */
require_once('./common.inc.php');

class role extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		$current_page 	= !empty($_REQUEST['pageNum']) ? intval($_REQUEST['pageNum']) : 1;
		$page_size		= !empty($_REQUEST['numPerPage']) ? intval($_REQUEST['numPerPage']) : 10;
		$role_name		= !empty($_REQUEST['role_name']) ? trim($_REQUEST['role_name']) : '';
		$role_id		= !empty($_REQUEST['role_id']) ? intval($_REQUEST['role_id']) : ROLE_TOP;
		
		//获取客户分类
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		$param = array(
			'page'			=> $current_page,
			'page_size'		=> $page_size,
			'role_name'		=> $role_name,
			'role_id'		=> $role_id
		);	
		$roles = $obj_role->get_all_roles($param);
		$count = $roles['count'];
		$page_num = ceil($count/$page_size);
		
		$page_info = array(
			'total'		=> $count,
			'page_num'	=> $page_num,
			'page_size'	=> $page_size,
			'current_page'=>$current_page
		);
		
		$role_list = $obj_role->get_all_roles(array(), 0);
		
		import('util.RoleShow');
		$role_show = RoleShow::role_show($role_list);
		
		if($roles['list'])
		{
			foreach($roles['list'] as $key => $val)
			{
				$role_t = $obj_role->get_role($val['parent_id']);
				$roles['list'][$key]['parent_name'] = $role_t['role_name'];
			}
		}
		$page = $this->app->page();
		$page->value('category',$role_show);
		$page->value('roles',$roles['list']);
		$page->value('page',$page_info);
		$page->value('param',$param);
		$page->params['template'] = 'role_list.html';
		$page->output();
	}
	
	//添加角色
	public function doAddRole()
	{
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		$role_list = $obj_role->get_all_roles(array(), 0);
		
		import('util.RoleShow');
		$role_show = RoleShow::role_show($role_list);
		
		$page = $this->app->page();
		$page->value('category',$role_show);
		$page->params['template'] = 'role_add.html';
		$page->output();
	}
	
	//添加jiaose
	public function doAddRoleAct()
	{
		$role_name	= !empty($_POST['role_name']) ? trim($_POST['role_name']) : '';
		$parent_id	= !empty($_POST['role_id']) ? trim($_POST['role_id']) : 0;
		
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		
		$res = $obj_role->add_new_role($role_name, $parent_id);
		
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '添加成功',
				'navTabId'		=> 'role_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		else
		{
			//失败
			$return = array(
				'statusCode'	=> 0,
				'message'		=> '添加失败',
				'navTabId'		=> 'role_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
	}
	
	//编辑role
	public function doEditRole()
	{
		$role_id = !empty($_GET['role_id']) ? intval($_GET['role_id']) : 0;
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		
		$role = $obj_role->get_role($role_id);

		$role_list = $obj_role->get_all_roles(array(), 0);
		
		import('util.RoleShow');
		$role_show = RoleShow::role_show($role_list);
		
		$page = $this->app->page();
		$page->value('role',$role);
		$page->value('category',$role_show);
		$page->params['template'] = 'role_edit.html';
		$page->output();
	}
	
	//编辑
	public function doEditRoleAct()
	{
		$role_id = !empty($_POST['role_id']) ? intval($_POST['role_id']) : 0;
		$role_name	= !empty($_POST['role_name']) ? trim($_POST['role_name']) : '';
		$parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
		
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		
		$res = $obj_role->edit_role($role_name, $role_id, $parent_id);
		
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '编辑成功',
				'navTabId'		=> 'role_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		else
		{
			//失败
			$return = array(
				'statusCode'	=> 0,
				'message'		=> '编辑失败',
				'navTabId'		=> 'role_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
	}
	
	//移除角色
	public function doRemoveRole()
	{
		$role_id = !empty($_GET['role_id']) ? intval($_GET['role_id']) : 0;
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		
		$res = $obj_role->remove_role($role_id);
		
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '删除成功',
				'navTabId'		=> 'role_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> HOST.'/role.php',
				'confirmMsg'	=> ''
			);
		}
		else
		{
			//失败
			$return = array(
				'statusCode'	=> 0,
				'message'		=> '删除失败',
				'navTabId'		=> 'role_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> HOST.'/role.php',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
		
		/*
		if(!$res)
		{
			//成功
			echo "<script>alert('删除失败');window.go(-1);</script>";
		}*/

	}
	
	

	
	
	

}
$app->run();
	
?>
