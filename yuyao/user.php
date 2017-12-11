<?php 
/**
 * 用户
 */
require_once('./common.inc.php');

class user extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	

		$page = $this->app->page();
		$page->value('main','myorder');
		$page->params['template'] = 'user_menu.html';
		$page->output();
	}
	
	//用户列表
	public function doUserList()
	{
		$role_id 		= !empty($_REQUEST['role_id']) ? intval($_REQUEST['role_id']) : 0;
		$current_page 	= !empty($_REQUEST['pageNum']) ? intval($_REQUEST['pageNum']) : 1;
		$page_size		= !empty($_REQUEST['numPerPage']) ? intval($_REQUEST['numPerPage']) : 10;
		$username		= !empty($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
		$role_id		= !empty($_REQUEST['role_id']) ? trim($_REQUEST['role_id']) : ROLE_TOP;
		
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$param = array(
			'page'			=> $current_page,
			'page_size'		=> $page_size,
			'username'		=> $username,
			'role_id'		=> $role_id
		);	
		$list = $obj_user->get_user_list($param);
		/*
		$count = $list['count'];
		$page_num = ceil($count/$page_size);
		
		$page_info = array(
			'total'		=> $count,
			'page_num'	=> $page_num,
			'page_size'	=> $page_size,
			'current_page'=>$current_page
		);
		*/
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		$role_list = $obj_role->get_all_roles(array(), 0);
		
		import('util.RoleShow');
		$role_show = RoleShow::role_show($role_list);

		$page = $this->app->page();
		$page->value('category',$role_show);
		$page->value('users',$list['list']);
		$page->value('total',$list['count']);
		$page->value('page',$page_info);
		$page->value('param',$param);
		$page->value('type','user');
		$page->params['template'] = 'user_list.html';
		$page->output();
	}
	
	//添加用户
	public function doAddUser()
	{
		$type = !empty($_GET['type']) ? trim($_GET['type']) : 'user';
		$user_type = ($type=='admin') ? 1 : 0;
		//获取客户分类
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		
		$roles = $obj_role->get_all_roles();
		//获取分车
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		
		$trucks = $obj_truck->get_all_trucks();
		
		//print_R($roles);print_r($trucks);die;
		$role_list = $obj_role->get_all_roles(array(), 0);
		
		import('util.RoleShow');
		$role_show = RoleShow::role_show($role_list);
		
		$page = $this->app->page();
		$page->value('category',$role_show);
		$page->value('roles',$roles['list']);
		$page->value('trucks',$trucks);
		$page->value('type',$user_type);
		$page->params['template'] = 'user_add.html';
		$page->output();
	}
	
	//添加用户
	public function doAddUserAct()
	{
		$username	= !empty($_POST['username']) ? trim($_POST['username']) : '';
		$pwd		= !empty($_POST['password']) ? trim($_POST['password']) : '';
		$realname	= !empty($_POST['realname']) ? trim($_POST['realname']) : '';
		$role_id	= !empty($_POST['role']) ? trim($_POST['role']) : 0;
		$company_name=!empty($_POST['company_name']) ? trim($_POST['company_name']) : '';
		$address	=!empty($_POST['address']) ? trim($_POST['address']) : '';
		$mobile		= !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
		$truck		= !empty($_POST['truck']) ? trim($_POST['truck']) : '';
		$type		= !empty($_POST['type']) ? intval($_POST['type']) : 0;
		
		$param = array(
			'username'		=> $username,
			'password'		=> md5($pwd),
			'realname'		=> $realname,
			'role_id'		=> $role_id,
			'company_name'	=> $company_name,
			'address'		=> $address,
			'mobile'		=> $mobile,
			'truck'			=> $truck,
			'type'			=> $type
		);
		
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		
		$res = $obj_user->add_new_user($param);
		
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '添加成功',
				'navTabId'		=> ($type == 0) ? 'user_list' : 'admin_list',
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
				'navTabId'		=> ($type == 0) ? 'user_list' : 'admin_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
	}
	
	//编辑用户
	public function doEditUser()
	{
		$user_id = $_GET['user_id'] ? intval($_GET['user_id']) : 0;

		//获取客户分类
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		$roles = $obj_role->get_all_roles();
		//获取分车
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		$trucks = $obj_truck->get_all_trucks();
		
		//获取用户信息
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		
		$user = $obj_user->get_user_detail($user_id);
		//print_r($user);die;
		$page = $this->app->page();
		$page->value('roles',$roles['list']);
		$page->value('trucks',$trucks);
		$page->value('user',$user);
		$page->params['template'] = 'user_edit.html';
		$page->output();
	}
	
	//编辑
	public function doEditUserAct()
	{
		$user_id	= !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
		$username	= !empty($_POST['username']) ? trim($_POST['username']) : '';
		$realname	= !empty($_POST['realname']) ? trim($_POST['realname']) : '';
		$role_id	= !empty($_POST['role']) ? trim($_POST['role']) : 0;
		$company_name=!empty($_POST['company_name']) ? trim($_POST['company_name']) : '';
		$address	=!empty($_POST['address']) ? trim($_POST['address']) : '';
		$mobile		= !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
		$truck		= !empty($_POST['truck']) ? trim($_POST['truck']) : '';
		$type		= !empty($_POST['type']) ? intval($_POST['type']) : 0;
		
		$param = array(
			'username'		=> $username,
			'role_id'		=> $role_id,
			'company_name'	=> $company_name,
			'address'		=> $address,
			'mobile'		=> $mobile,
			'truck'			=> $truck,
			'type'			=> $type
		);
		
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$res = $obj_user->edit_user($param, $user_id);
		
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '编辑成功',
				'navTabId'		=> ($type == 0) ? 'user_list' : 'admin_list',
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
				'navTabId'		=> ($type == 0) ? 'user_list' : 'admin_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
		
	}
	
	//删除用户
	public function doRemoveUser()
	{
		$user_id	= !empty($_GET['user_id']) ? intval($_GET['user_id']) : 0;
		$type		= !empty($_GET['type']) ? trim($_GET['type']) : 'user';
		
		$url 		= ($type=='admin') ? 'adminlist' : 'userlist'; 
		
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$res = $obj_user->remove_user($user_id);
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '编辑成功',
				'navTabId'		=> 'user_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> HOST.'/user.php?do='.$url,
				'confirmMsg'	=> ''
			);
		}
		else
		{
			//失败
			$return = array(
				'statusCode'	=> 0,
				'message'		=> '编辑失败',
				'navTabId'		=> 'user_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> HOST.'/user.php?do='.$url,
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
	
	//获取管理员列表
	public function doAdminList()
	{
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		
		$list = $obj_user->get_admin_list();

		$page = $this->app->page();
		$page->value('users',$list);
		$page->value('type','admin');
		$page->value('cat',1);
		$page->params['template'] = 'user_list.html';
		$page->output();
	}
	
	//获取订单商品明细
	public function doOrderGoods()
	{
		$order_id	= !empty($_GET['order_id']) ? $_GET['order_id'] : 0;
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		
		$order_goods = $obj_order->get_order_goods($order_id);
		//print_r($order_goods);
		
		$page = $this->app->page();
		$page->value('order_goods',$order_goods);
		//$page->value('main','myorder');
		$page->params['template'] = 'order_goods.html';
		$page->output();
	}
	
	//获取用户列表
	public function doUsersAll()
	{
		$current_page 	= !empty($_REQUEST['pageNum']) ? intval($_REQUEST['pageNum']) : 1;
		$page_size		= !empty($_REQUEST['numPerPage']) ? intval($_REQUEST['numPerPage']) : 10;
		$role_id		= !empty($_REQUEST['role_id']) ? trim($_REQUEST['role_id']) : ROLE_TOP;
		
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$param = array(
			'page'			=> $current_page,
			'page_size'		=> $page_size,
			'role_id'		=> $role_id
		);
		$list = $obj_user->get_user_list($param);
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		$role_list = $obj_role->get_all_roles(array(), 0);
		
		import('util.RoleShow');
		$role_show = RoleShow::role_show($role_list);

		$page = $this->app->page();
		$page->value('category',$role_show);
		$page->value('users',$list['list']);
		$page->value('total',$list['count']);
		$page->value('param',$param);
		$page->value('type','user');
		$page->params['template'] = 'user_all.html';
		$page->output();
	}
	
	//获取用户详情
	public function doGetUserDetail()
	{
		$user_id	= !empty($_GET['user_id']) ? intval($_GET['user_id']) : 0;
		
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$user_detail = $obj_user->get_user_detail($user_id);
		exit(json_encode($user_detail));
	}
	

}
$app->run();
	
?>
