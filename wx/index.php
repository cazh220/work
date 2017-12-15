<?php 
/**
 * 入口
 */
require_once('./common.inc.php');

class index extends Action {
	private static $body = '{
		"button":[
			{
			"name":"关于沪鸽",
			"sub_button":[{"type":"view","name":"沪鸽简介","url":"http://www.baidu.com"},{"type":"view","name":"沪鸽招聘","url":"http://www.baidu.com"},{"type":"view","name":"牙线定制","url":"http://www.baidu.com"},{"type":"view","name":"联系沪鸽","url":"http://www.baidu.com"}]
		},
		{
			"name":"沪鸽产品",
			"sub_button":[{"type":"view","name":"口腔临床产品","url":"http://www.baidu.com"},{"type":"view","name":"技工系列产品","url":"http://www.baidu.com"},{"type":"view","name":"树脂牙牙托粉","url":"http://www.baidu.com"},{"type":"view","name":"诺必灵合金等","url":"http://www.baidu.com"},{"type":"view","name":"口腔护理产品","url":"http://www.baidu.com"}]
		},
		{
			"name":"沪鸽服务",
			"sub_button":[{"type":"view","name":"会员登录","url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa02a6a965b89a9c0&redirect_uri=http%3a%2f%2fwww.yrsyc.cn%2fwx%2fuser.php&response_type=code&scope=snsapi_base&state=26#wechat_redirect"},{"type":"scancode_push","name":"防伪查询","key":"rselfmenu_0_1","sub_button": [ ]},{"type":"view","name":"沪鸽商城","url":"http://www.yrsyc.cn/wx/shop.php"},{"type":"view","name":"售后服务","url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa02a6a965b89a9c0&redirect_uri=http%3a%2f%2fwww.yrsyc.cn%2fwx%2fuser.php&response_type=code&scope=snsapi_base&state=123#wechat_redirect"}]
		}]
	 }';
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		//微信入口
		/*
		import('util.Jssdk');
		$jssdk = new JSSDK();
		$signPackage = $jssdk->GetSignPackage();
		*/
		//$this->authorization();
		
	    $page = $this->app->page();
		$page->params['template'] = 'canvas/index.html';
		$page->output();
	}
	
	
	
	//获取wx授权
	/*
	private function authorization()
	{
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa02a6a965b89a9c0&redirect_uri=http%3a%2f%2fwww.yrsyc.cn%2fwx%2fuser.php&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
		import('util.Jssdk');
		$jssdk = new JSSDK();
		$res = $jssdk->httpGet($url);
		var_dump($res);
	}*/
	
	//创建微信菜单
	public function doWxMenu()
	{
		$body = $_GET['body'];
		//echo $body;
		//exit();
		$body = urldecode($body);
		//var_dump($body);die;
		import('util.Jssdk');
		$jssdk = new JSSDK();
		$access_token = $jssdk->getAccessToken();
		//生成菜单
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		import('util.RequestCurl');
		$res = RequestCurl::curl_post($url, $body);
		exit($res);
		/*
		var_dump($res);die;
		if ($res['errcode'] == 0) {
			echo "菜单生成成功！";
		}else{
			echo "菜单生成失败！";
		}*/
		
	}
	
	
	//关于页面
	public function doAbout()
	{
		$page = $this->app->page();
		$page->params['template'] = 'about.php';
		$page->output();
	}
	
	//反馈页面
	public function doFeedBack()
	{
		
		if($_POST['action']==1)
		{
			$feedback = $_POST['feedback'];
			$user_id = $_POST['user_id'];
			importModule("userInfo","class");
			$obj_user = new userInfo;
			//上传截图
			$upload_pic = $this->_upload_pic();
			if($upload_pic)
			{
				$upload_pic = date("Ymd").'/'.$upload_pic;
			}
			
			$res = $obj_user->feedback($user_id, $feedback, $upload_pic);
			
			if($res)
			{
				header("Location:user.php?do=setting&user_id=".$user_id);
				exit();
			}
		}
		$user_id = $_GET['user_id'];
		$page = $this->app->page();
		$page->value('user_id',$user_id);
		$page->params['template'] = 'feedback.php';
		$page->output();
	}
	
	
	private function get_wx_base()
	{
		if(!isset($_SESSION['wx']['headimgurl']))
		{
			$code = $_GET['code'];
			$state = $_GET['state'];
			//$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxa02a6a965b89a9c0&secret=93039a23ef6f5bfd3b6f6f39c636ad78&code={$code}&grant_type=authorization_code";
			$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx2ccfb6af79958896&secret=90e0b21ebb265d871d22a57d7c1162d0&code={$code}&grant_type=authorization_code";
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
			$wx_user = json_decode($json_wx_user, true);
			$_SESSION['wx'] = $wx_user;
		}
	}
	
	
	//中间页
	public function doMiddlePage()
	{
		//判断是否已关注
		
		
		$security_code = !empty($_GET['security_code']) ? trim($_GET['security_code']) : '';
		$headimgurl = !empty($_GET['headimgurl']) ? trim($_GET['headimgurl']) : '';
		$urlencode_head = urlencode($headimgurl);
		//$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa02a6a965b89a9c0&redirect_uri=".urlencode("http://www.yrsyc.cn/wx/index.php?do=share&wx=1&security_code=".$security_code."&headimgurl=".$headimgurl)."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx2ccfb6af79958896&redirect_uri=".urlencode("http://www.yrsyc.cn/wx/index.php?do=share&wx=1&security_code=".$security_code."&headimgurl=".$headimgurl)."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
		header("Location:".$url);
	}
	
	
	//分享页面
	public function doShare()
	{
		$wx 			= !empty($_GET['wx']) ? intval($_GET['wx']) : 0;
		$headimgurl 	= !empty($_GET['headimgurl']) ? trim($_GET['headimgurl']) : '';
		$security_code 	= !empty($_GET['security_code']) ? trim($_GET['security_code']) : '';
		if($wx)
		{
			$this->get_wx_base();//获取微信信息
			//usleep(500);
		}
		
		importModule("userInfo","class");
		$obj_user = new userInfo;	
		if(!empty($_SESSION[$security_code]['head']))
		{
			$head_img = $_SESSION[$security_code]['head'];
		}
		else
		{
			$user_share = $obj_user->get_share_user($security_code);
			//var_dump($user_share);var_dump($_SESSION['wx']['headimgurl']);die;
			$head_img = !empty($user_share['headimgurl']) ? $user_share['headimgurl'] : $_SESSION['wx']['headimgurl'];
			$_SESSION[$security_code]['head'] = $head_img;
			
		}

		//查询防伪详情
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$patient = $obj_patient->get_patient($security_code);

		if(empty($patient))
		{
			$page = $this->app->page();
			$page->params['template'] = '404.php';
			$page->output();
		}
		else
		{
			//获取技工信息
			//获取扫描人微信信息
			//$wx_user = $obj_user->get_wx_user($security_code);
			$wx_user[0]['wx_headimgurl'] = $headimgurl ? $headimgurl : $head_img;

			$teach = $obj_user->get_user_detail($patient[0]['operate_user_id']);
			$patient[0]['tech'] = $teach[0];
			
			//获取医生信息
			if(!empty($patient[0]['doctor_id']))
			{
				$doctor = $obj_user->get_user_detail($patient[0]['doctor_id']);
				$patient[0]['doc'] = $doctor[0];
				$patient[0]['confirm'] = 1;
			}
			else
			{
				$patient[0]['confirm'] = 0;
			}
			//获取二维码修复体名
			importModule("FixtypeInfo","class");
			$obj_fix_type = new FixtypeInfo;	
			
			importModule("ScodeInfo","class");
			$obj_scode = new ScodeInfo;
			$false_tooth_res_id = $obj_scode->get_repaire_type($security_code); 
			if($false_tooth_res_id)
			{
				$false_tooth_res = $obj_fix_type->get_repaire_type_detail($false_tooth_res_id);
			}
			$patient[0]['false_tooth_name'] = !empty($false_tooth_res['false_tooth_name']) ? $false_tooth_res['false_tooth_name'] : '';
			//$res_repaire = $obj_patient->get_repaire_tooth($patient[0]['false_tooth']);
			//$patient[0]['false_tooth_name'] = $res_repaire[0]['false_tooth_name'];
		    //获取修复体明细
			$product_detail = $obj_fix_type->get_fix_type_product_detail($patient[0]['product_detail_id']);
			
			//$patient[0]['wxname'] = mb_substr($patient[0]['name'], 0, 1, 'utf8').'**';
			//$patient[0]['wxname'] = substr_replace($patient[0]['name'],"*",1);
			$len = mb_strlen($patient[0]['name'], 'utf-8');
			$a = explode('', $patient[0]['name']);
			$repalce_name = '';
			for($i=0; $i<$len; $i++)
			{
				if($len <= 2 )
				{
					if($i==0)
					{
						$repalce_name .= mb_substr($patient[0]['name'], 0, 1, 'utf-8');
					}
					else
					{
						$repalce_name .= '*';
					}
				}
				else
				{
					if($i==0)
					{
						$repalce_name .= mb_substr($patient[0]['name'], 0, 1, 'utf-8');
					}
					elseif($i == $len-1)
					{
						$repalce_name .= mb_substr($patient[0]['name'], -1, 1, 'utf-8');
					}
					else
					{
						$repalce_name .= '*';
					}
				}
			}

			//$patient[0]['wxname'] = $repalce_name;
			$patient[0]['tech']['head_img'] = !empty($patient[0]['repairosome_pic']) ? $patient[0]['repairosome_pic'] : $teach[0]['head_img'];

			$patient[0]['name'] = $repalce_name;
			//获取公众号参数
			import('util.Jssdk');
			$jssdk = new JSSDK();
			$signPackage = $jssdk->GetSignPackage();
			
			//获取点击人的图像
			if($wx)
			{
				$head_img = !empty($_SESSION['wx']['headimgurl']) ? $_SESSION['wx']['headimgurl'] : '';
			}
			$patient_pic = $patient[0]['case_pic'] ? $patient[0]['case_pic'] : $patient[0]['doc']['head_img'];
			
			//print_r($patient[0]);die;
			//加工单位图片
			if(!empty($patient[0]['repairosome_pic']))
			{
				$type = 1;
				$unit_pic =  $patient[0]['repairosome_pic'];//修复体图片
			}
			elseif(!empty($patient[0]['tech']['head_img']))
			{
				$type = 1;
				$unit_pic =  $patient[0]['tech']['head_img'];
			}
			else
			{
				$type = 0;
				$unit_pic =  $wx_user[0]['wx_headimgurl'];
			}
			
			$page = $this->app->page();
			$page->value('patient',$patient[0]);
			$page->value('patient_pic',$patient_pic);
			$page->value('wx_user',$wx_user[0]);
			$page->value('sign',$signPackage);
			$page->value('res_repaire',$false_tooth_res);
			$page->value('product_detail',$product_detail);
			$page->value('head_img',$head_img);
			$page->value('security_code',$security_code);
			$page->value('unit_pic',$unit_pic);
			$page->value('type',$type);
			$page->params['template'] = 'share.php';
			$page->output();
		}

		
	}
	
	private function _upload_pic()
	{
		$ar_type  = explode('.',$_FILES['cfile']['name']); 
		$s_type   = strtolower($ar_type[1]);

		if(!in_array($s_type,array('jpg','png','bmp','gif'))){
			exit(json_encode(array('status'=>0, 'info'=>'文件类型不正确')));
		}
		
		$path = date("Ymd", time());
		if(!file_exists('../app/public/uploads/'.$path)) {
			if(!mkdir($path)) {
				exit(json_encode(array('status'=>0, 'info'=>'创建目录失败')));
			}
			
			chmod($path.'/',0777);
		}
		
		import('util.UploadFile');
		$obj_upload = new UploadFile;
		
		$res = $obj_upload->upload($_FILES['cfile'],'../app/public/uploads/'.$path.'/', 1);

		if($res === false) {
			exit(json_encode(array('status'=>0, 'info'=>'文件上传失败')));
		}
		
		return $res;
	}
	
	//更新积分
	public function doUpdateCredits()
	{
		$user_id = $_SESSION['user_id'] ? $_SESSION['user_id'] : 0;
		importModule("CreditInfo","class");
		$obj_credit = new CreditInfo;
		$a = json_encode($_SESSION);
		file_put_contents("1.txt", $a);//die;
		//echo "1111";die;
		if($user_id)
		{
			$score = 0;
			
			$credit_list = $obj_credit->get_credits_list();
			if($credit_list)
			{
				$credit_item = array();
				foreach($credit_list as $key => $item)
				{
					$credit_item[$item['id']] = $item['credits'];
				}
				
				$score += $credit_item[7];
				$obj_credit->update_credits($user_id, $score);
				
			}
		}
		
	}


	
}
$app->run();
	
?>
