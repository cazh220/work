<?php 
/**
 * 用户处理类
 * 
 */
require_once('./common.inc.php');

class user extends Action {
	
	/**
	 * 默认执行的方法(用户登录页面)
	 */
	public function doDefault(){
		$register	= !empty($_GET['register']) ? intval($_GET['register']) : 0;
		
		if(empty($register))
		{
			//判断是否再登录有效期
			$login_info = $_COOKIE['access'];
			$login_arr = json_decode($login_info, true);
			file_put_contents("user.txt", date("Y-m-d H:i:s")."Login：".json_encode($_COOKIE)."\n", FILE_APPEND); 
			if(!empty($login_arr['user_id']))
			{
				//判断账号是否异常
				importModule("userInfo","class");
				$obj_user = new userInfo;
				//查询表的password
				$user_query = $obj_user->get_user_detail($login_arr['user_id']);
				if($user_query[0]['password'] == $login_arr['password'])
				{
					$res = $obj_user->check_user_status($login_arr['user_id']);
				
					if($res)
					{
						file_put_contents("user.txt", date("Y-m-d H:i:s")."获取WX：111111\n", FILE_APPEND); 
						//跳转到个人中心
						if(empty($_SESSION['wx']))
						{
							$this->get_wx_base();//获取微信基本用户信息
						}
						$this->update_wx_user($login_arr['user_id']);
						$redirect_url = HOST."/user.php?do=ucenter&user_id=".$login_arr['user_id'];
						header("location:".$redirect_url);
						exit();
					}
					else
					{
						setcookie("access", "", time()-3600);
						echo "<script>alert('您的账号异常！');</script>";
					}
					
				}
				
			}
			
			/*
			if(!empty($login_arr['user_id']))
			{
				
				$res = $obj_user->check_user_status($login_arr['user_id']);
				
				if($res)
				{
					file_put_contents("user.txt", date("Y-m-d H:i:s")."获取WX：111111\n", FILE_APPEND); 
					//跳转到个人中心
					if(empty($_SESSION['wx']))
					{
						$this->get_wx_base();//获取微信基本用户信息
					}
					$this->update_wx_user($login_arr['user_id']);
					$redirect_url = HOST."/user.php?do=ucenter&user_id=".$login_arr['user_id'];
					header("location:".$redirect_url);
					exit();
				}
				else
				{
					setcookie("access", "", time()-3600);
					echo "<script>alert('您的账号异常！');</script>";
				}
				
			}*/
		}
		
		file_put_contents("user.txt", date("Y-m-d H:i:s")."获取SESSION：".json_encode($_SESSION)."\n", FILE_APPEND); 
		if(empty($_SESSION['wx']))
		{
			$this->get_wx_base();//获取微信基本用户信息
		}
		$page = $this->app->page();
		$page->value('host',HOST);
		$page->params['template'] = 'login.php';
		$page->output();
	}
	
	private function get_wx_base()
	{
		$code = $_GET['code'];
		$state = $_GET['state'];
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxa02a6a965b89a9c0&secret=93039a23ef6f5bfd3b6f6f39c636ad78&code={$code}&grant_type=authorization_code";
		import('util.RequestCurl');
		$json_ticket = RequestCurl::curl_get($url);
		$ticket = json_decode($json_ticket, true);
		//$access_token = $ticket['access_token'];
		$open_id = $ticket['openid'];

		//获取基本信息
		import('util.Jssdk');
		$jssdk = new JSSDK();
		$access_token = $jssdk->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$open_id}";
		$json_wx_user = RequestCurl::curl_get($url);
		file_put_contents("user.txt", date("Y-m-d H:i:s")."微信接口获取：".$json_wx_user."\n", FILE_APPEND); 
		$wx_user = json_decode($json_wx_user, true);
		if(!empty($wx_user['openid']))
		{
			$_SESSION['wx'] = $wx_user;
		}
	}
	
	//更新微信基本信息
	private function update_wx_user($user_id)
	{
		if ($_SESSION['wx']['nickname'] && $_SESSION['wx']['openid'] &&  $_SESSION['wx']['headimgurl'])
		{
			importModule("userInfo","class");
			$obj_user = new userInfo;
			$wx_user = array(
				'wx_nick_name'	=>	$_SESSION['wx']['nickname'],
				'wx_open_id'	=>  $_SESSION['wx']['openid'],
				'wx_headimgurl'	=>  $_SESSION['wx']['headimgurl'],
				'user_id'		=>  $user_id
			);
			$res = $obj_user->update_wx_user($wx_user);
		}
		
	}
	
	/**
	 * 登录处理
	 */
	public function doLogin(){
		/*
		$this->s_sessionid = $_SESSION['sess_id'];
		
		if(empty($this->s_sessionid)){
			$this->app->redirect('user.php',0);
		}*/
		unset($_SESSION);
		import('util.Clean');
		
		//用户名
		$s_username = !empty($_POST['username']) ? Clean::htmlSafe($_POST['username']) : '';
		
		//密码
		$s_password = !empty($_POST['password']) ?  		Clean::htmlSafe($_POST['password']) : '';

		if(empty($s_username) || empty($s_password)){
			exit(json_encode(array('status'=>false,'info'=>'帐号和密码不能为空！')));
		}
		importModule("userInfo","class");
		$obj_user = new userInfo;

		$res_login = $obj_user->findLogin($s_username,$s_password);
		
		if($res_login === false || !is_array($res_login)){
			$this->_log(array( __CLASS__ . '.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' result error : '.$res_login, date("Y-m-d H:i:s")));
			
			$reg_user = $obj_user->get_reg_user($s_username);
			$res_unpass_user = $obj_user->get_unpass_user($s_username);
			if(empty($reg_user) || !empty($res_unpass_user))
			{
				exit(json_encode(array('status'=>false,'info'=>'账户名不存在、请重新输入或注册！')));
			}
			else
			{
				exit(json_encode(array('status'=>false,'info'=>'您输入的帐号或密码不正确！')));
			}
		}

		if($res_login['user_id'] == 0 || empty($res_login)){
			$status = $obj_user->check_status($s_username);
			if($status == 0)
			{
				exit(json_encode(array('status'=>false,'info'=>'您还未通过审核，请耐心等待审核或通过沪鸽微信平台联系沪鸽客服人员')));
			}
			elseif($status == 2)
			{
				exit(json_encode(array('status'=>false,'info'=>'抱歉，您的账号未通过，请联系客服')));
			}
			else
			{
			exit(json_encode(array('status'=>false,'info'=>'您输入的帐号或密码不正确！')));
		}
		}
		
		//获取客户端Ip
		import('util.Ip','class');
		$obj_ip = new Ip;
		
		//登录成功更新最后登录时间和IP
		$res = $obj_user->updateLoginInfo($res_login['user_id'],$obj_ip->get());

		if ($res === false) {
			$this->_log(array( __CLASS__ . '.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' update is fail : '.$res, date("Y-m-d H:i:s")));
		}

        unset($_SESSION);
        //更新微信信息
        $this->update_wx_user($res_login['user_id']);

		//登录成功，设置用户到session
		$_SESSION = $res_login;
		$_SESSION['login_time']  = time();
		
		//插入日志
		$param = array(
			'user_id'	=> $res_login['user_id'],
			'username'	=> $s_username,
			'content'	=> $s_username.'登录成功'
		);
		$obj_user->action_log($param);
		
		//写入到cookie
		//$accecss_token = md5($s_username.$s_password);
		//Cookie::set('token',$accecss_token, 120);
		setcookie("access", json_encode($res_login), time()+3600*24*30);

		exit(json_encode(array('status'=>true,'info'=>'登录成功！', 'user_id'=>$res_login['user_id'])));
	}
	
	//注册账号
	public function doRegisterUser()
	{
		//file_put_contents("1.txt", json_encode($_SESSION));unset($_SESSION);die;
		$param = $_POST['param'] ? $_POST['param'] : '';
		$params = explode(":", $param);
		//print_r($params);die;
		
		$mobile 		= $params[17] ? trim($params[17]) : '';
		$username 		= $params[18] ? trim($params[18]) : '';
		$realname 		= $params[0] ? trim($params[0]) : '';
		$pwd1			= $params[1] ? trim($params[1]) : '';
		$pwd2			= $params[2] ? trim($params[2]) : '';
		$user_type 		= $params[3] ? intval($params[3]) : 1;
		$email 			= $params[4] ? trim($params[4]) : '';
		$company_name 	= $params[5] ? trim($params[5]) : '';
		$department 	= $params[6] ? trim($params[6]) : '';
		$position 		= $params[7] ? trim($params[7]) : '';
		$birthday 		= $params[8] ? trim($params[8]) : '';
		$persons_num 	= $params[9] ? intval($params[9]) : 0;
		$seats 			= $params[10] ? intval($params[10]) : 0;
		$province 		= $params[11] ? intval($params[11]) : '';
		$city 			= $params[12] ? intval($params[12]) : '';
		$district 		= $params[13] ? trim($params[13]) : '';
		$company_addr 	= $params[14] ? trim($params[14]) : '';
		$company_pic 	= $params[15] ? trim($params[15]) : '';
		$company_info 	= $params[16] ? trim($params[16]) : '';

		//上传图片
		//$upload_pic = $this->_upload_pic();
		
		import('util.Ip');
		$obj_ip = new Ip;
		$s_ip = $obj_ip->get();
		$s_ip = $s_ip ? $s_ip : $_SERVER['REMOTE_ADDR'];
		//插入基本信息
		$data = array(
			'mobile'		=> $mobile,
			'username'		=> $username,
			'realname'		=> $realname,
			'password'		=> $pwd1,
			'user_type'		=> $user_type,//1技工   2医生
			'email'	    	=> $email,
			'company_name'	=> $company_name,
			'department'	=> $department,
			'position'		=> $position,
			'birthday' 		=> $birthday ? $birthday.' 00:00:00' : '',
			'persons_num' 	=> $persons_num,
			'seats'			=> $seats,
			'province'		=> $province,
			'city'			=> $city,
			'district'		=> $district,
			'company_addr' 	=> $company_addr,
			'company_info'	=> $company_info,
			'head_img'		=> $company_pic,
			'create_time' 	=> date("Y-m-d H:i:s", time()),
			'last_login'	=> date("Y-m-d H:i:s", time()),
			'last_ip'		=> $s_ip,
			'wx_nick_name'	=> $_SESSION['wx']['nickname'],
			'wx_open_id'	=> $_SESSION['wx']['openid'],
			'wx_headimgurl'	=> $_SESSION['wx']['headimgurl']
		);
		
		/*
		if (!empty($upload_pic))
		{
			$data['head_img'] = date("Ymd").'/'.$upload_pic;
		}
		*/
		
		//积分
		//获取积分参考值
		importModule("CreditInfo","class");
		$obj_credit = new CreditInfo;
		$credit_list = $obj_credit->get_credits_list();
		$score = 700;//积分
		if(!empty($credit_list))
		{
			$credit_item = array();
			foreach($credit_list as $key => $item)
			{
				$credit_item[$item['id']] = $item['credits'];
			}
			
			if(!empty($company_pic))
			{
				$score += $credit_item[1];
			}
			if(!empty($company_info))
			{
				$score += $credit_item[2];
			}
			/*
			if(!empty($realname))
			{
				$score += $credit_item[3];
			}
			if(!empty($birthday))
			{
				$score += $credit_item[4];
			}
			*/
			if(!empty($position))
			{
				$score += $credit_item[5];
			}
			/*
			if(!empty($email))
			{
				$score += $credit_item[6];
			}
			*/
		}
		
		if($department && $position && $seats && $persons_num && $company_info && $company_pic)
		{
			$score = 1000;
		}
		$data['total_credits'] = $score;
		$data['left_credits'] = $score;
		
		//医生免审
		if($user_type == 2)
		{
			$data['status'] = 1;
		}
		//print_r($data);die;
		importModule("userInfo","class");
		$obj_user = new userInfo;
		$user_id = $obj_user->insert_user($data);

		if ($user_id) {
			//插入地址
			$param = array(
				'user_id'		=> $user_id,
				'province'		=> $province,
				'city'			=> $city,
				'district'		=> $district,
				'address'		=> $company_addr,
				'receiver'		=> $realname,
				'mobile'		=> $mobile
			);
			importModule("AddressInfo","class");
			$obj_address = new AddressInfo;
			$address_id = $obj_address->insert_address($param);
			//更新user表的address_id
			if($address_id)
			{
				$obj_user->update_user_address_id($user_id, $address_id);
			}
		
			//注册成功
			if($user_type == 2)
			{
				$return = array("status"=>1, "message"=>'注册成功，请登录');
				exit(json_encode($return));
				//echo "<script>alert('注册成功，请登录');window.location.href='user.php?register=1';</script>";
			}
			else
			{
				$return = array("status"=>1, "message"=>'恭喜您,您已注册成功！账号需要沪鸽工作人员审核通过后才能登录，审核时限8小时');
				exit(json_encode($return));
				//echo "<script>alert('恭喜您,您已注册成功！账号需要沪鸽工作人员审核通过后才能登录，审核时限8小时');window.location.href='user.php?register=1';</script>";
			}
		}
		else 
		{
			$return = array("status"=>0, "message"=>'注册失败，请重试');
			exit(json_encode($return));
			//echo "<script>alert('注册失败，请重试');history.go(-1);</script>";
			//exit(json_encode(array('status'=>0, 'message'=>'failed')));
		}

	}
	
	//注册
	public function doRegister()
	{
		$mobile 		= $_POST['mobile'] ? trim($_POST['mobile']) : '';
		$username 		= $_POST['username'] ? trim($_POST['username']) : '';
		$realname 		= $_POST['realname'] ? trim($_POST['realname']) : '';
		$pwd1			= $_POST['password1'] ? trim($_POST['password1']) : '';
		$pwd2			= $_POST['password2'] ? trim($_POST['password2']) : '';;
		$user_type 		= $_POST['typer'] ? intval($_POST['typer']) : 0;
		$email 			= $_POST['email'] ? trim($_POST['email']) : '';
		$company_name 	= $_POST['company_name'] ? trim($_POST['company_name']) : '';
		$department 	= $_POST['department'] ? trim($_POST['department']) : '';
		$position 		= $_POST['position'] ? trim($_POST['position']) : '';
		$birthday 		= $_POST['birthday'] ? trim($_POST['birthday']) : '';
		$persons_num 	= $_POST['persons_num'] ? intval($_POST['persons_num']) : 0;
		$seats 			= $_POST['seats'] ? intval($_POST['seats']) : 0;
		$province 		= $_POST['province'] ? intval($_POST['province']) : 0;
		$city 			= $_POST['city'] ? intval($_POST['city']) : 0;
		$district 		= $_POST['district'] ? intval($_POST['district']) : 0;
		$company_addr 	= $_POST['address'] ? trim($_POST['address']) : '';
		$company_info 	= $_POST['addinfo'] ? trim($_POST['addinfo']) : '';

		//上传图片
		$upload_pic = $this->_upload_pic();
		
		import('util.Ip');
		$obj_ip = new Ip;
		$s_ip = $obj_ip->get();
		$s_ip = $s_ip ? $s_ip : $_SERVER['REMOTE_ADDR'];
		//插入基本信息
		$data = array(
			'mobile'		=> $mobile,
			'username'		=> $username,
			'realname'		=> $realname,
			'password'		=> $pwd1,
			'user_type'		=> $user_type,//1技工   2医生
			'email'	    	=> $email,
			'company_name'	=> $company_name,
			'department'	=> $department,
			'position'		=> $position,
			'birthday' 		=> $birthday ? $birthday.' 00:00:00' : '',
			'persons_num' 	=> $persons_num,
			'seats'			=> $seats,
			'province'		=> $province,
			'city'			=> $city,
			'district'		=> $district,
			'company_addr' 	=> $company_addr,
			'company_info'	=> $company_info,
			//'head_img'		=> date("Ymd").'/'.$upload_pic,
			'create_time' 	=> date("Y-m-d H:i:s", time()),
			'last_login'	=> date("Y-m-d H:i:s", time()),
			'last_ip'		=> $s_ip,
			'wx_nick_name'	=> $_SESSION['wx']['nickname'],
			'wx_open_id'	=> $_SESSION['wx']['openid'],
			'wx_headimgurl'	=> $_SESSION['wx']['headimgurl']
		);
		
		if (!empty($upload_pic))
		{
			$data['head_img'] = date("Ymd").'/'.$upload_pic;
		}
		
		//积分
		//获取积分参考值
		importModule("CreditInfo","class");
		$obj_credit = new CreditInfo;
		$credit_list = $obj_credit->get_credits_list();
		$score = 0;//积分
		if(!empty($credit_list))
		{
			$credit_item = array();
			foreach($credit_list as $key => $item)
			{
				$credit_item[$item['id']] = $item['credits'];
			}
			
			if(!empty($upload_pic))
			{
				$score += $credit_item[1];
			}
			if(!empty($company_info))
			{
				$score += $credit_item[2];
			}
			if(!empty($realname))
			{
				$score += $credit_item[3];
			}
			if(!empty($birthday))
			{
				$score += $credit_item[4];
			}
			if(!empty($position))
			{
				$score += $credit_item[5];
			}
			if(!empty($email))
			{
				$score += $credit_item[6];
			}
		}
		//echo $department.':'.$position.':'.$seats.':'.$persons_num.':'.$company_info.':'.$upload_pic;die;
		if($department && $position && $seats && $persons_num && $company_info && $upload_pic)
		{
			$score = 1000;
		}
		
		
		$data['total_credits'] = $score;
		$data['left_credits'] = $score;
		
		//医生免审
		if($user_type == 2)
		{
			$data['status'] = 1;
		}

		importModule("userInfo","class");
		$obj_user = new userInfo;
		$user_id = $obj_user->insert_user($data);

		if ($user_id) {
			//插入地址
			$param = array(
				'user_id'		=> $user_id,
				'province'		=> $province,
				'city'			=> $city,
				'district'		=> $district,
				'address'		=> $company_addr,
				'receiver'		=> $realname,
				'mobile'		=> $mobile
			);
			importModule("AddressInfo","class");
			$obj_address = new AddressInfo;
			$address_id = $obj_address->insert_address($param);
			//更新user表的address_id
			if($address_id)
			{
				$obj_user->update_user_address_id($user_id, $address_id);
			}
		
			//注册成功
			if($user_type == 2)
			{
				echo "<script>alert('注册成功，请登录');window.location.href='user.php?register=1';</script>";
			}
			else
			{
				echo "<script>alert('恭喜您,您已注册成功！账号需要沪鸽工作人员审核通过后才能登录，审核时限8小时');window.location.href='user.php?register=1';</script>";
			}
		}
		else 
		{
			echo "<script>alert('注册失败，请重试');history.go(-1);</script>";
			//exit(json_encode(array('status'=>0, 'message'=>'failed')));
		}

	}

	//用户中心
	public function doUcenter()
	{
		$user_id = $_GET['user_id'];
		importModule("userInfo","class");
		$obj_user = new userInfo;

		$user = $obj_user->get_user_detail($user_id);

		//获取录入患者的数量
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$patient_count = $obj_patient->patient_count(array('operate_user_id'=>$user_id));
		
		//获取未读消息数量
		importModule("MessageInfo","class");
		$obj_message = new MessageInfo;
		$message_count = $obj_message->get_unread_count($user_id);
	    //var_dump($message_count);die;
	    if(empty($user[0]['wx_headimgurl']))
	    {
	    	$user[0]['wx_headimgurl'] = "http://www.yrsyc.cn/wx/public/themes/images/head.png";
	    }
	    
	    
		$page = $this->app->page();
		$page->value('user',$user[0]);
		$page->value('message_count',$message_count);
		$page->value('patient_count',$patient_count);
		$page->params['template'] = 'user.php';
		$page->output();
	}

	//质保卡积分录入
	public function doPatientIn()
	{
		$user_id = $_GET['user_id'] ? intval($_GET['user_id']) : 0;
		$qrcode = $_GET['qrcode'] ? trim($_GET['qrcode']) : '';
		file_put_contents("qrcode.txt", date("Y-m-d H:i:s")."技工录入二维码：".$qrcode.'\n\r', FILE_APPEND);
		if(empty($qrcode))
		{ 
			echo "<script>alert('未知防伪码');history.go(-1);</script>";
			exit();
		}
		else
		{
			//校验防伪码是否存在；不存在则后退
			importModule("ScodeInfo","class");
			$obj_scode = new ScodeInfo;
			$security_code_detail = $obj_scode->get_security_code_detail($qrcode);
			if(empty($security_code_detail))
			{
				echo "<script>alert('未知防伪码，请换一个');history.go(-1);</script>";
				exit();
			}
			
		}
		
		//获取当前用户信息
		importModule("userInfo","class");
		$obj_user = new userInfo;
		$user = $obj_user->get_user_detail($user_id);
		
		//获取患者信息
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$patient = $obj_patient->get_patient($qrcode);

		//通过防伪码码获取出库单位
		$out_company = $obj_patient->get_out_company($qrcode);
		$user[0]['out_company'] = $out_company;
		
		//如果是技工，判断录入人是否一致，否则隐藏按钮
		$previlege = 0;
		if(!empty($patient[0]))
		{
			if($patient[0]['tech_id'] != $user_id)
			{
				echo "<script>alert('对不起，该防伪码已使用');history.go(-1);</script>";
				exit();
			}
			//是否有用处理权限的录入用户
			if($user_id != $patient[0]['operate_user_id'])
			{
				$previlege = 1;
			}
			//编辑、已录入
			$hospital = $patient[0]['hospital'];
			$doctor   = $patient[0]['doctor'];
			$name	  = $patient[0]['name'];
			$sex	  = $patient[0]['sex'];
			$birthday = $patient[0]['birthday'];
			$tooth_position_arr = explode('|',$patient[0]['tooth_position']);
			$tooth_position1 = $tooth_position_arr[0];
			$tooth_position2 = $tooth_position_arr[1];
			$tooth_position3 = $tooth_position_arr[2];
			$tooth_position4 = $tooth_position_arr[3];
			
			$false_tooth = $patient[0]['false_tooth'];
			$product_detail_id = $patient[0]['product_detail_id'];
			$repairosome_pic = $patient[0]['repairosome_pic'];
			$action = 1;
		}
		else
		{
			//新增
			$hospital = '';
			$doctor   = '';
			$name	  = '';
			$sex	  = 0;
			$birthday = '';
			$tooth_position = '';
			$false_tooth = 1;
			$repairosome_pic = '';
			$action = 0;
			$tooth_position1 = '';
			$tooth_position2 = '';
			$tooth_position3 = '';
			$tooth_position4 = '';
			$product_detail_id = '';
		}
		
		//获取 修复体
		importModule("FixtypeInfo","class");
		$obj_fixtype = new FixtypeInfo;
		$fix_type = $obj_fixtype->get_repaire_type();   
		
		//通过防伪码获取对应的修复体
		$false_tooth = $obj_scode->get_repaire_type($qrcode); 
		//获取关联的产品明细
		$product_detail_arr = array();
		if($false_tooth)
		{
			$product_detail_arr = $obj_fixtype->get_product_detail($false_tooth);  
		}

		$page = $this->app->page();
		$page->value('user_id',$user_id);
		$page->value('qrcode',$qrcode);
		$page->value('user',$user[0]);
		$page->value('hospital',$hospital);
		$page->value('doctor',$doctor);
		$page->value('name',$name);
		$page->value('sex',$sex);
		$page->value('birthday',$birthday);
		$page->value('tooth_position',$tooth_position);
		$page->value('tooth_position1',$tooth_position1);
		$page->value('tooth_position2',$tooth_position2);
		$page->value('tooth_position3',$tooth_position3);
		$page->value('tooth_position4',$tooth_position4);
		$page->value('false_tooth',$false_tooth);
		$page->value('repaire_type',$fix_type);
		$page->value('repairosome_pic',$repairosome_pic);
		$page->value('product_detail',$product_detail_arr);
		$page->value('product_detail_id',$product_detail_id);
		$page->value('action',$action);
		$page->value('previlege',$previlege);
		$page->params['template'] = 'patient.php';
		$page->output();
	}

	//医生录入
	public function doDoctorIn()
	{
		$user_id = $_GET['user_id'] ? intval($_GET['user_id']) : 0;
		$qrcode = $_GET['qrcode'] ? trim($_GET['qrcode']) : '';
		if(empty($qrcode))
		{ 
			echo "<script>alert('未知防伪码');history.go(-1);</script>";
			exit();
		}
		else
		{
			//校验防伪码是否存在；不存在则后退
			importModule("ScodeInfo","class");
			$obj_scode = new ScodeInfo;
			$security_code_detail = $obj_scode->get_security_code_detail($qrcode);
			if(empty($security_code_detail))
			{
				echo "<script>alert('未知防伪码，请换一个');history.go(-1);</script>";
				exit();
			}
			
		}
		//获取患者信息
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$patient = $obj_patient->get_patient($qrcode);
		if(empty($patient))
		{
			echo "<script>alert('对不起，该防伪码还未关联患者，请联系技工录入');history.go(-1);</script>";
			exit();
		}
		if(!empty($patient[0]['doctor_id']) && $patient[0]['doctor_id'] != $user_id)
		{
			echo "<script>alert('对不起，该防伪码已使用');history.go(-1);</script>";
			exit();
		}
		
		$tooth_position_arr = explode('|',$patient[0]['tooth_position']);
		$tooth_position1 = $tooth_position_arr[0];
		$tooth_position2 = $tooth_position_arr[1];
		$tooth_position3 = $tooth_position_arr[2];
		$tooth_position4 = $tooth_position_arr[3];
		
		$product_detail_id = $patient[0]['product_detail_id'];

		importModule("ScodeInfo","class");
		$obj_scode = new ScodeInfo;
		//获取防伪码关联的客户单位
		$company_name = $obj_scode->get_company_name($qrcode);
		
		//获取 修复体
		importModule("FixtypeInfo","class");
		$obj_fixtype = new FixtypeInfo;
		$fix_type = $obj_fixtype->get_repaire_type();   
		
		//通过防伪码获取对应的修复体
		$false_tooth = $obj_scode->get_repaire_type($qrcode); 
		
		//获取关联的产品明细
		$product_detail_arr = array();
		if($false_tooth)
		{
			$product_detail_arr = $obj_fixtype->get_product_detail($false_tooth);  
		}
		
		$page = $this->app->page();
		$page->value('patient',$patient[0]);
		$page->value('doctor',$_SESSION);
		$page->value('qrcode',$qrcode);
		$page->value('user_id',$user_id);
		$page->value('tooth_position1',$tooth_position1);
		$page->value('tooth_position2',$tooth_position2);
		$page->value('tooth_position3',$tooth_position3);
		$page->value('tooth_position4',$tooth_position4);
		$page->value('company_name',$company_name);
		$page->value('repaire_type',$fix_type);
		$page->value('false_tooth',$false_tooth);
		$page->value('product_detail',$product_detail_arr);
		$page->value('product_detail_id',$product_detail_id);
		$page->params['template'] = 'doctor.php';
		$page->output();
	}

	//录入查询
	public function doRecordQuery()
	{
		$page = $this->app->page();
		$page->params['template'] = 'card_record.php';
		$page->output();
	}

	//忘记密码
	public function doFindPwd()
	{
		$page = $this->app->page();
		$page->params['template'] = 'findpwd.php';
		$page->output();
	}

	//修改密码
	public function doUpdatePwd()
	{
		$mobile = !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
		$vcode  = !empty($_POST['vcode']) ? trim($_POST['vcode']) : '';
		$pwd1   = !empty($_POST['password1']) ? trim($_POST['password1']) : '';
		$pwd2   = !empty($_POST['password2']) ? trim($_POST['password2']) : '';

		//验证验证码
		//todo

		importModule("userInfo","class");
		$obj_user = new userInfo;
		$res = $obj_user->update_pwd($mobile, $pwd1);
		
		if ($res) {
			header('Location: '.HOST.'/user.php');
		}

	}
	
	//绑定手机
	public function doBind_mobile()
	{
		import('util.RequestCurl');
		
		$page = $this->app->page();
		$page->params['template'] = 'register.php';
		$page->output();
	}
	
	//验证手机
	public function doValidateMobile()
	{
		$mobile = trim($_GET['mobile']);
		$vcode  = trim($_GET['vcode']);
		
		importModule("SmsCode","class");
		$obj_code = new SmsCode;
		
		$code = $obj_code->validate_code($mobile);
		
		if (!empty($code[0]['code']) && $code[0]['code'] == $vcode)
		{
			exit(json_encode(array('status'=>true, 'message'=>'一致')));
		}
		else
		{
			exit(json_encode(array('status'=>false, 'message'=>'不一致')));
		}
		
	}
	
	//注册页面
	public function doShowRegister()
	{
		$mobile = $_GET['mobile'];
		//获取 省份
		importModule("AreaInfo","class");
		$obj_area = new AreaInfo;
		$province = $obj_area->get_province();

		$page = $this->app->page();
		$page->value('province',$province);
		$page->value('mobile',$mobile);
		$page->value('username',$mobile);
		$page->params['template'] = 'register_t.php';
		$page->output();
	}
	
	//协议
	public function doViewXy()
	{
		$page = $this->app->page();
		$page->params['template'] = 'xy.php';
		$page->output();
	}
	
	//发短信息
	public function doSendSms()
	{
		$mobile = trim($_GET['mobile']);
		$send 	= intval($_GET['send']);
		
		importModule("userInfo","class");
		$obj_user = new userInfo;
		
		if(empty($send))
		{
			$result = $obj_user->check_mobile($mobile);
			if(empty($result))
			{
				exit(json_encode(array('status'=>false, 'message'=>'手机号未注册')));
			}
		}

		//生成验证码
		import('util.Vcode');
		$code = Vcode::generate_num();
		//写入数据库
		importModule("SmsCode","class");
		$obj_code = new SmsCode;
		$res = $obj_code->generate_code($mobile, $code);
		
		//调用接口发短信
		if($res === true)
		{
			import('util.SendSms');
			$result = SendSms::send_vcode($mobile, $code);
			if ($result->returnstatus == 'Success')
			{
				exit(json_encode(array('status'=>true, 'message'=>'验证码已发送')));
			}
		}
		exit(json_encode(array('status'=>false, 'message'=>'验证码发送失败')));
		
	}
	
	
	//ajax上传
	public function  doUploadFile()
	{
		$upload_pic = $this->_upload_pic();
		$file_path = date("Ymd").'/'.$upload_pic;
		if($file_path)
		{
			exit(json_encode(array('status'=>true, 'path'=>$file_path)));
		}
		else
		{
			exit(json_encode(array('status'=>false, 'path'=>'')));
		}
	}
	
	
	//上传图片
	private function _upload_pic()
	{
		if (!empty($_FILES['cfile']['name']))
		{
			$ar_type  = explode('.',$_FILES['cfile']['name']); 
			$s_type   = strtolower($ar_type[1]);
			
			if(!in_array($s_type,array('jpg','png','bmp','gif','jpeg'))){
				echo "<script>alert('上传图片格式不正确(".$s_type.")');history.go(-1);</script>";
				
				exit();
				//exit(json_encode(array('status'=>0, 'info'=>'文件类型不正确')));
			}
			
			if($_FILES["cfile"]["size"] > 2000000)
			{
				echo "<script>alert('图片不大于1M');history.go(-1);</script>";
				exit();
				//echo "<script>alert('上传图片格式不正确');history.go(-1);</script>";
			}
			
			$dirname = date("Ymd", time());
			$pic_path = '../app/public/uploads/'.$dirname;
			if(!file_exists($pic_path)) {
				if(!mkdir($pic_path, 0777, true)) {
					echo "<script>alert('创建新图片目录失败');history.go(-1);</script>";
					exit();
					//exit(json_encode(array('status'=>0, 'info'=>'创建目录失败')));
				}
				chmod($pic_path.'/',0777);
			}
			
			import('util.UploadFile');
			$obj_upload = new UploadFile;
			//$destFolder = '../app/public/uploads/20171020/';
			$res = $obj_upload->upload($_FILES['cfile'], $pic_path.'/', 1);

			if($res === false) {
				echo "<script>alert('图片上传失败失败');history.go(-1);</script>";
				exit();
				//exit(json_encode(array('status'=>0, 'info'=>'文件上传失败')));
			}
			
			return $res;
		}
		else
		{
			return '';
		}
		
	}
	
	//设置
	public function doSetting()
	{
		$user_id = $_GET['user_id'];
		$page = $this->app->page();
		$page->value('user_id',$user_id);
		$page->params['template'] = 'setting.php';
		$page->output();
	}
	
	//个人中心
	public function doMember()
	{
		$user_id = $_GET['user_id'] ? $_GET['user_id'] : 0;

		//获取个人信息
		importModule("userInfo","class");
		$obj_user = new userInfo;
		$user = $obj_user->get_user_detail($user_id);
		$user[0]['birthday'] = date("Y-m-d", strtotime($user[0]['birthday']));
		
		importModule("AreaInfo","class");
		$obj_area = new AreaInfo;
		$province = $obj_area->get_province();
		//获取市
		$city = $obj_area->get_city($user[0]['province']);
		//获取区域
		$district = $obj_area->get_district($user[0]['city']);
		
		importModule("AddressInfo","class");
		$obj_address = new AddressInfo;
	
		$address_select = array();
		$address = $obj_address->address_list($user_id);
		if($address)
		{
			foreach($address as $key => $val)
			{
				if($user[0]['address_id'] == $val['address_id'])
				{
					$address_select = $val;
				}
			}
		}
		
		$page = $this->app->page();
		$page->value('mine',$user[0]);
		$page->value('province',$province);
		$page->value('city',$city);
		$page->value('district',$district);
		$page->value('address',$address);
		$page->value('address_select',$address_select);
		$page->params['template'] = 'member.php';
		$page->output();
	}
	
	//修改个人信息
	public function doUpdateUser()
	{
		$mobile			= !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
		$province 		= !empty($_POST['province']) ? intval($_POST['province']) : 0;
		$city 			= !empty($_POST['city']) ? intval($_POST['city']) : 0;
		$district 		= !empty($_POST['district']) ? intval($_POST['district']) : 0;
		$user_id 		= !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
		$address_id 	= !empty($_POST['address_id']) ? intval($_POST['address_id']) : 0;
		$username		= !empty($_POST['username']) ? trim($_POST['username']) : '';
		$realname		= !empty($_POST['realname']) ? trim($_POST['realname']) : '';
		$email			= !empty($_POST['email']) ? trim($_POST['email']) : '';
		$company_name	= !empty($_POST['company_name']) ? trim($_POST['company_name']) : '';
		$position		= !empty($_POST['job']) ? trim($_POST['job']) : '';
		$create_time	= !empty($_POST['create_time']) ? trim($_POST['create_time']) : '';
		$persons_num	= !empty($_POST['employee_num']) ? intval($_POST['employee_num']) : '';
		$address		= !empty($_POST['address']) ? trim($_POST['address']) : '';
		$company_pic	= !empty($_POST['company_pic']) ? trim($_POST['company_pic']) : '';
		$info			= !empty($_POST['info']) ? trim($_POST['info']) : '';
		
		$data = array(
			'realname'		=> $realname,
			'position'		=> $position,
			'persons_num'	=> $persons_num,
			'email'			=> $email,
			'company_name'	=> $company_name,
			'address'		=> $address,
			'company_pic'	=> $company_pic,
			'info'			=> $info,
			'province'		=> $province,
			'city'			=> $city,
			'district'		=> $district,
			'address_id'	=> $address_id,
			'user_id'		=> $user_id
		);
		
		//插入新的到地址列表
		//获取手机号
		importModule("userInfo","class");
		$obj_user = new userInfo;
		$user = $obj_user->get_user_detail($user_id);
		$mobile = $user[0]['mobile'] ? $user[0]['mobile'] : 0;
		//更新地址
		$param = array(
			'user_id'		=> $user_id,
			'province'		=> $province,
			'city'			=> $city,
			'district'		=> $district,
			'address'		=> $address,
			'receiver'		=> $realname,
			'mobile'		=> $mobile
		);
		importModule("AddressInfo","class");
		$obj_address = new AddressInfo;
		$obj_address->update_address($param, $address_id);
		
		//跟新用户信息
		$res = $obj_user->update_user($data);

		/*
		//插入地址
		$param = array(
			'user_id'		=> $user_id,
			'province'		=> $province,
			'city'			=> $city,
			'district'		=> $district,
			'address'		=> $address,
			'receiver'		=> $realname,
			'mobile'		=> $mobile
		);
		importModule("AddressInfo","class");
		$obj_address = new AddressInfo;
		$address_id = $obj_address->insert_address($param);
		//更新user表的address_id
		if($address_id)
		{
			$obj_user->update_user_address_id($user_id, $address_id);
		}
		*/
		
		if($res)
		{
			$url = 'user.php?do=ucenter&user_id='.$user_id;
			exit(json_encode(array('status'=>true, 'message'=>'更新会员资料成功', 'url'=>$url)));
		}
		else
		{
			exit(json_encode(array('status'=>false, 'message'=>'更新会员资料失败', 'url'=>'')));
		}
	}
	
	//检查手机号是否已注册
	public function doCheckMobile()
	{
		$mobile =	!empty($_GET['mobile']) ? trim($_GET['mobile']) : '';
		
		if(empty($mobile))
		{
			exit(json_encode(array('status'=>false, 'message'=>'参数有误')));
		}

		if(!preg_match("/^1[34578]{1}\d{9}$/",$mobile))
		{
			exit(json_encode(array('status'=>true, 'message'=>'填写错误，请重新输入')));
		}
		
		importModule("userInfo","class");
		$obj_user = new userInfo;
		
		$result = $obj_user->check_mobile($mobile);
		if($result > 0)
		{
			exit(json_encode(array('status'=>true, 'message'=>'手机已注册，请换个手机号')));
		}
		else
		{
			exit(json_encode(array('status'=>false, 'message'=>'success')));
		}
		
	}
	
	//注销
	public function doLogOut()
	{
		unset($_SESSION);
		//请登录cookies
		setcookie("access", "", time()-3600);
		header("Location:user.php");
	}
	
	/**
	 * 数据更新失败记录日志，并标识操作失败
	 *
	 * @param Array $data
	 * @return false
	 */
	private function _log($data){
	    $log = $this->app->log(); 
	    $log->reset()->setPath("user")->setData($data)->write();	
	}
}
$app->run();
	
?>
