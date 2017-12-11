<?php 
/**
 * 分类模块
 */
require_once('./common.inc.php');

class role_category extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){
		//获取用户分类列表
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		
		$role_list = $obj_role->get_all_roles(array(), 0);
		
		import('util.RoleShow');
		$role_show = RoleShow::role_show($role_list);	
		
		$page = $this->app->page();
		$page->value('role_category',$role_show);
		$page->params['template'] = 'role_category_menu.html';
		$page->output();
		
		
		/*
		//获取分类列表
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		
		$category_list = $obj_category->get_categoty_list();
		
		//导入工具类
		import('util.CategoryShow');
		$category_show = CategoryShow::category_show($category_list);
		//print_r($category_show);die;
		$page = $this->app->page();
		$page->value('category',$category_show);
		$page->value('main','category_main');
		$page->value('user',$_SESSION);
		$page->params['template'] = 'index_menu.html';
		$page->output();
		*/
	}
	
	//展示分类
	public function doRoleCategoryMenu(){
		//获取分类列表
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		
		$role_category_list = $obj_role->get_all_roles(array(), 0);
		
		//导入工具类
		import('util.RoleShow');
		$role_show = RoleShow::role_show($role_category_list);
		//print_r($role_show);die;
		$page = $this->app->page();
		$page->value('role_category',$role_show);
		$page->value('main','category_main');
		$page->value('user',$_SESSION);
		$page->params['template'] = 'role_index_menu.html';
		$page->output();
	}
	
	
	
	//添加节点
	public function doAddCategory()
	{
		$last_cat_id = $_GET['last_cat_id'];
		$current_cat_id = $_GET['current_cat_id'];
		
		//获取上级分类名称
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		$last_detail = $obj_role->get_role($last_cat_id);
		//获取当前分类信息
		$current_detail = $obj_role->get_role($current_cat_id);
		//print_r($last_detail);die;
		$page = $this->app->page();
		$page->value('last_category',$last_detail);
		$page->value('current_category',$current_detail);
		$page->params['template'] = 'add_role_category.html';
		$page->output();
	}
	
	//更新分类
	public function doRoleCategoryAct()
	{	
		$type 			= intval($_POST['type']);
		$role_name	    = $_POST['role_category_name'] ? trim($_POST['role_category_name']) : '';

		$parent_id		= $_POST['last_category_id'] ? intval($_POST['last_category_id']) : 0;
		$deepth			= $_POST['deepth'] ? intval($_POST['deepth']) : 0;
		$role_id	    = $_POST['role_id'] ? intval($_POST['role_id']) : 0;
		
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		if($type == 1)
		{
			//添加同类
			$deepth = $deepth + 1;
			$param = array(
				'role_name'	    => $role_name,
				'parent_id'		=> $parent_id,
				'deepth'		=> $deepth
			);
			
			$res = $obj_role->add_role_category($param);
			
			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '添加成功',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/role_category.php?do=rolecategorymenu',
					'confirmMsg'	=> ''
				);
			}
			else
			{
				//失败
				$return = array(
					'statusCode'	=> 0,
					'message'		=> '添加失败',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/role_category.php?do=rolecategorymenu',
					'confirmMsg'	=> ''
				);
			}
		}
		elseif($type == 2)
		{
			//添加子类
			$deepth = $deepth + 2;
			$param = array(
				'role_name'		=> $role_name,
				'parent_id'		=> $role_id,
				'deepth'		=> $deepth
			);

			$res = $obj_role->add_role_category($param);
			
			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '添加成功',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/role_category.php?do=rolecategorymenu',
					'confirmMsg'	=> ''
				);
			}
			else
			{
				//失败
				$return = array(
					'statusCode'	=> 0,
					'message'		=> '添加失败',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/role_category.php?do=rolecategorymenu',
					'confirmMsg'	=> ''
				);
			}
		}
		elseif($type == 3)
		{
			//编辑
			/*
			$param = array(
				'role_name'		=> $role_name,
				'parent_id'		=> $parent_id
			);
			*/
			$res = $obj_role->edit_role($role_name, $role_id, $parent_id);

			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '编辑成功',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/role_category.php?do=rolecategorymenu',
					'confirmMsg'	=> ''
				);
			}
			else
			{
				//失败
				$return = array(
					'statusCode'	=> 0,
					'message'		=> '编辑失败',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/role_category.php?do=rolecategorymenu',
					'confirmMsg'	=> ''
				);
			}
		}
		else
		{
			//删除
			/*
			$param = array(
				'role_id'	=> $role_id
			);
			*/
			
			$res = $obj_role->remove_role($role_id);
			
			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '删除成功',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/role_category.php?do=rolecategorymenu',
					'confirmMsg'	=> ''
				);
			}
			else
			{
				//失败
				$return = array(
					'statusCode'	=> 0,
					'message'		=> '删除失败',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/role_category.php?do=rolecategorymenu',
					'confirmMsg'	=> ''
				);
			}
		}
		exit(json_encode($return));
	}

}
$app->run();
	
?>
