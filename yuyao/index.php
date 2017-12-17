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
			echo "<script>alert('账号或密码不为空');history.go(-1);</script>";
			exit();
		}
		
		if(isset($_COOKIE['last_sess_id']))
		{
			$this->s_sessionid = $_COOKIE['last_sess_id'];
			$times = 1;
		}
		else
		{
			$this->s_sessionid = $_SESSION['sess_id'];
			$times = 0;
		}
			
		
		
		//获取登录信息
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$user = $obj_user->get_user_info($username, $password);
		if(empty($user))
		{
			echo "<script>alert('账号或者密码错误');window.location.href='index.php?do=login'</script>";
		}
		
		if(!empty($user['sess_id']) && ($this->s_sessionid != $user['sess_id']))
		{
			echo "<script>alert('此账号已有人占用，请在线者退出系统您再登录');window.location.href='index.php?do=login'</script>";
		}
		else
		{
			//更新sess_id
			$obj_user->update_sess_id($user['user_id'], $this->s_sessionid);//更新sess_id
			$user['sess_id'] = $this->s_sessionid;
			$_SESSION[$this->s_sessionid] = $user;
			//更新cookie
			setcookie("last_sess_id", $this->s_sessionid, time()+3600*24*3650);
			//不需要重新校验直接登录
			echo "<script>window.location.href='myorder.php';</script>";
		}
		
		
		
		/*
		if(!empty($_SESSION[$this->s_sessionid]['user_id']))
		{
			//存在session且不一致
			$user_id = $_SESSION[$this->s_sessionid]['user_id'];
			
		}
		else
		{
			//
			importModule("UserInfo","class");
			$obj_user = new UserInfo;
			$user = $obj_user->get_user_info($username, $password);
			if(empty($user))
			{
				echo "<script>alert('账号或者密码错误');window.location.href='index.php?do=login'</script>";
			}
		}
		
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$user_detail = $obj_user->get_user_detail($user_id);

		if(!empty($_SESSION[$this->s_sessionid]['user_id']))
		{
			$user_id = $_SESSION[$this->s_sessionid]['user_id'];
			importModule("UserInfo","class");
			$obj_user = new UserInfo;
			$user_detail = $obj_user->get_user_detail($user_id);
		}
		

		if(empty($_SESSION[$this->s_sessionid]) || (!empty($user_detail) && $user_detail['sess_id'] != $this->s_sessionid))
		{
			$username	= !empty($_POST['username']) ? trim($_POST['username']) : '';
			$password	= !empty($_POST['password']) ? trim($_POST['password']) : '';
			if(empty($username) || empty($password))
			{
				echo "<script>alert('账号或密码错误');history.go(-1);</script>";
				exit();
			}
			//import('util.Ip');
			//$client_ip = Ip::get();
			//获取登录信息
			importModule("UserInfo","class");
			$obj_user = new UserInfo;
			$user = $obj_user->get_user_info($username, $password);
			if(empty($user))
			{
				echo "<script>alert('账号或者密码错误');window.location.href='index.php?do=login'</script>";
			}
			//先销毁掉已存在的session
			$old_sess_id = $user['sess_id'];
			if($old_sess_id)
			{
				//销毁掉session
				unset($_SESSION[$old_sess_id]);
			}
			//判断sess_id 是否空或者不相等则则更新
			//更新ip
			//$obj_user->update_client_ip($user['user_id'], $client_ip);
			$obj_user->update_sess_id($user['user_id'], $this->s_sessionid);//更新sess_id
			$user['sess_id'] = $this->s_sessionid;
			$_SESSION[$this->s_sessionid] = $user;
		}
		*/

		//不需要重新校验直接登录
		//echo "<script>window.location.href='myorder.php';</script>";
 	}
 	
 	//退出
 	public function doLogout()
 	{
 		//print_r($_SESSION);
 		if(isset($_COOKIE['last_sess_id']))
 		{
 			$sess_id = $_COOKIE['last_sess_id'];
 		}
 		else
 		{
 			$sess_id = $_SESSION['sess_id'];
 		}
 		importModule("UserInfo","class");
 		//$obj_user->update_client_ip($_SESSION['user_id'], '');
 		$user_id = $_SESSION[$sess_id]['user_id'];
 		$obj_user = new UserInfo;
		$obj_user->update_sess_id($user_id, '');
		
 		unset($_SESSION[$sess_id]);
 		//销毁cookie
 		setcookie("last_sess_id", "", time()-3600);
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
 		
 		$sess_id = $_SESSION['sess_id'];
 		//检查老密码
 		importModule("UserInfo", "class");
 		$obj_user = new UserInfo;
 		$res = $obj_user->check_user_id($_SESSION[$sess_id]['user_id'], $oldpassword);
 		
 		if(empty($res))
 		{
 			echo "<script>alert('老密码输入的不正确');history.go(-1);</script>";
			exit();
 		}
 		
 		//修改密码
 		$res = $obj_user->doUpdatePwd($_SESSION[$sess_id]['user_id'], $newPassword);
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
		
		$role_list = $obj_role->get_all_roles(array(), 0);
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		import('util.RoleShow');
		$role_show = RoleShow::role_show($role_list);
		//print_r($role_show);
		//获取分类和客户
		if(!empty($role_show))
		{
			foreach($role_show as $key => $val)
			{
				$users = array();
				$users = $obj_user->get_role_users($val['role_id']);
				$role_show[$key]['users'] = !empty($users) ? $users : array();
				if(!empty($val['child']))
				{
					foreach($val['child'] as $k => $v)
					{
						$users_c = array();
						$users_c = $obj_user->get_role_users($v['role_id']);
						$role_show[$key]['child'][$k]['users'] = !empty($users_c) ? $users_c : array();
						if(!empty($v['child']))
						{
							foreach($v['child'] as $kk => $vv)
							{
								$users_cc = array();
								$users_cc = $obj_user->get_role_users($vv['role_id']);
								$role_show[$key]['child'][$k]['child'][$kk]['users'] = !empty($users_cc) ? $users_cc : array();
							}
						}
					}
				} 
			}
		}
		
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		$trucks = $obj_truck->get_all_trucks();
 		$page = $this->app->page();
 		$page->value('roles',$roles['list']);
 		$page->value('trucks',$trucks);
 		$page->value('role_show',$role_show);
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
