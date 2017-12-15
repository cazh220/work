<?php 
/**
 * index入口文件
 */
require_once('./common.inc.php');

class index extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){
		//跳转到登录
		header("location:index.php?do=login");
		exit();
		/*
		$page = $this->app->page();
		//$page->value('user_id',$user_id);
		$page->params['template'] = 'index_menu.html';
		$page->output();
		*/
	}
	
	//登录
	public function doLogin()
	{
		$page = $this->app->page();
		//$page->value('user_id',$user_id);
		$page->params['template'] = 'login.html';
		$page->output();
	}
	
	//登录操作
	public function doLoginAct()
	{
		$username	= !empty($_POST['username']) ? trim($_POST['username']) : '';
		$password	= !empty($_POST['password']) ? trim($_POST['password']) : '';
		
		if(empty($username) || empty($password))
		{
			echo "<script>alert('账号或密码错误');history.go(-1);</script>";
			exit();
		}
		$clent_ip = $_SERVER['REMOTE_ADDR'];
		
		//获取登录信息
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$user = $obj_user->get_user_info($username, $password);
		if(!empty($user['login_ip']) && $clent_ip != $user['login_ip'])
		{
			echo "<script>alert('此账号已在另一台电脑上登录');window.location.href='index.php?do=login'</script>";
			exit();
		}

		if(empty($user))
		{
			echo "<script>alert('账号或者密码错误');window.location.href='index.php?do=login'</script>";
			exit();
		}

		//更新ip
		$obj_user->update_client_ip($user['user_id'], $clent_ip);
		
		$_SESSION = $user;
		echo "<script>window.location.href='myorder.php'</script>";
		exit();
 	}
 	
 	//退出
 	public function doLogout()
 	{
 		importModule("UserInfo","class");
		$obj_user = new UserInfo;
 		$obj_user->update_client_ip($_SESSION['user_id'], '');
 		unset($_SESSION);
 		
 		$page = $this->app->page();
		//$page->value('user_id',$user_id);
		$page->params['template'] = 'login.html';
		$page->output();
 	}
 	
 	//登录
 	public function doHome()
 	{
 		$page = $this->app->page();
		//$page->value('user_id',$user_id);
		$page->params['template'] = 'login.html';
		$page->output();
 	}
 	
 	//修改密码
 	public function doChangePwd()
 	{
 		$page = $this->app->page();
		$page->params['template'] = 'changepwd.html';
		$page->output();
 	}
 	
 	
 	//修改密码act
 	public function doChangePwdAct()
 	{
 		$oldpassword	= !empty($_POST['oldPassword']) ? trim($_POST['oldPassword']) : '';
 		$newPassword	= !empty($_POST['newPassword']) ? trim($_POST['newPassword']) : '';
 		$rnewPassword	= !empty($_POST['rnewPassword']) ? trim($_POST['rnewPassword']) : '';
 		
 		if(empty($oldpassword) || empty($newPassword) || empty($newPassword))
 		{
 			echo "<script>alert('信息填写不完整');history.go(-1);</script>";
			exit();
 		}
 		
 		if($newPassword != $rnewPassword)
 		{
 			echo "<script>alert('密码不一致');history.go(-1);</script>";
			exit();
 		}
 		
 		//检查老密码
 		importModule("UserInfo", "class");
 		$obj_user = new UserInfo;
 		$res = $obj_user->check_user_id($_SESSION['user_id'], $oldpassword);
 		
 		if(empty($res))
 		{
 			echo "<script>alert('老密码输入的不正确');history.go(-1);</script>";
			exit();
 		}
 		
 		//修改密码
 		$res = $obj_user->doUpdatePwd($_SESSION['user_id'], $newPassword);
 		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '修改成功',
				'navTabId'		=> 'pagination1',
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
				'message'		=> '修改失败',
				'navTabId'		=> 'pagination1',
				'rel'			=> '',
				'callbackType'	=> '',
				'forwardUrl'	=> 'closeCurrent',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
 	}
 	
 	//注册
 	public function doRegister()
 	{
 		//获取客户单位
 		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		$roles = $obj_role->get_all_roles();
		
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		$trucks = $obj_truck->get_all_trucks();
		
 		$page = $this->app->page();
 		$page->value('roles',$roles['list']);
 		$page->value('trucks',$trucks);
		$page->params['template'] = 'register.html';
		$page->output();
 	}
 	
 	public function doRegisterAct()
 	{
 		$username 		= !empty($_POST['username']) ? trim($_POST['username']) : '';
 		$password 		= !empty($_POST['password']) ? trim($_POST['password']) : '';
 		$repassword 	= !empty($_POST['repassword']) ? trim($_POST['repassword']) : '';
 		$realname 		= !empty($_POST['realname']) ? trim($_POST['realname']) : '';
 		$role_id 		= !empty($_POST['role_id']) ? intval($_POST['role_id']) : 0;
 		$company_name 	= !empty($_POST['company_name']) ? trim($_POST['company_name']) : '';
 		$address 		= !empty($_POST['address']) ? trim($_POST['address']) : '';
 		$mobile 		= !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
 		$truck 			= !empty($_POST['truck']) ? intval($_POST['truck']) : 0;
 		
 		if(empty($username) || empty($password) || empty($repassword) || empty($role_id) || empty($mobile))
 		{
 			echo "<script>alert('有必填项未完成呢过');history.go(-1);</script>";
 			exit();
 		}
 		
 		if($password != $repassword)
 		{
 			echo "<script>alert('密码和确认密码不一致');history.go(-1);</script>";
 			exit();
 		}
 		
 		$data = array(
 			'username'		=> $username,
 			'password'		=> md5($password),
 			'realname'		=> $realname,
 			'role_id'		=> $role_id,
 			'company_name'	=> $company_name,
 			'address'		=> $address,
 			'mobile'		=> $mobile,
 			'truck'			=> $truck
 		);
 		importModule("UserInfo", "class");
 		$obj_user = new UserInfo;
 		
 		$res = $obj_user->add_new_user($data);
 		
 		if($res)
 		{
 			//调至登录页
 			echo "<script>alert('注册成功，请登陆');window.location.href='index.php?do=login';</script>";
 			exit();
 		}
 		else
 		{
 			echo "<script>alert('注册失败');history.go(-1);</script>";
 			exit();
 		}
 		
 	}

}
$app->run();
	
?>
