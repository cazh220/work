<?php 
/**
 * 医生类
 * 
 */
require_once('./common.inc.php');

class patient extends Action {
	static $credits = 30;
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		
		$page = $this->app->page();
		$page->value('user',$_SESSION);
		$page->params['template'] = 'user.php';
		$page->output();
	}
	
	//编辑技工录入
	public function doTechRecord()
	{
		$qrcode = $_GET['security_code'];
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$patient = $obj_patient->get_patient($qrcode);

		$user_id = $patient[0]['operate_user_id'];
		importModule("userInfo","class");
		$obj_user = new userInfo;
	    $user = $obj_user->get_user_detail($user_id);
	    
	    if($patient[0]['tooth_position'])
	    {
	    	$tooth_position_arr = explode('|', $patient[0]['tooth_position']);
	    	$patient[0]['tooth_position1'] = $tooth_position_arr[0];
	    	$patient[0]['tooth_position2'] = $tooth_position_arr[1];
	    	$patient[0]['tooth_position3'] = $tooth_position_arr[2];
	    	$patient[0]['tooth_position4'] = $tooth_position_arr[3];
	    }
	    
	    
	    //获取 修复体
		importModule("FixtypeInfo","class");
		$obj_fixtype = new FixtypeInfo;
		$fix_type = $obj_fixtype->get_repaire_type();   
		//通过防伪码获取对应的修复体
		importModule("ScodeInfo","class");
		$obj_scode = new ScodeInfo;
		$false_tooth = $obj_scode->get_repaire_type($qrcode); 
		//获取关联的产品明细
		$product_detail_arr = array();
		if($false_tooth)
		{
			$product_detail_arr = $obj_fixtype->get_product_detail($false_tooth);  
		}
	    

		$page = $this->app->page();
		$page->value('user',$user[0]);
		$page->value('patient',$patient[0]);
		$page->value('fix_type',$fix_type);
		$page->value('false_tooth',$false_tooth);
		$page->value('product_detail',$product_detail_arr);
		$page->value('product_detail_id',$patient[0]['product_detail_id']);
		$page->value('user_id',$user_id);
		$page->params['template'] = 'patient_update.php';
		$page->output();
	}
	
	//病人录入
	public function doAddNewPatient()
	{
		
		$param 				= $_POST['param'] ?  $_POST['param'] : '';
		$params 			= explode(':', $param); 
		//print_r($params);//die;
		$action 			= $params[14] ? intval($params[14]) : 0;

		$hospital			= $params[2] ? trim($params[2]) : '';
		$doctor 			= $params[3] ? trim($params[3]) : '';
		$patient_name 		= $params[4] ? trim($params[4]) : '';
		$sex 				= $params[15] ? intval($params[15]) : 0;
		$patient_age 		= $params[5] ? intval($params[5]) : 0;
		$tooth_position1 	= $params[6] ? trim($params[6]) : '';
		$tooth_position2 	= $params[7] ? trim($params[7]) : '';
		$tooth_position3 	= $params[8] ? trim($params[8]) : '';
		$tooth_position4 	= $params[9] ? trim($params[9]) : '';
		$tooth_position 	= $tooth_position1.'|'.$tooth_position2.'|'.$tooth_position3.'|'.$tooth_position4;
		$product_detail_id 	= $params[11] ? intval($params[11]) : 0;
		
		$repaire_type 		= $params[12] ? intval($params[12]) : 0;
		$repaire_pic 		= $params[13] ? trim($params[13]) : '';
		$user_id			= $params[0] ? intval($params[0]) : 0;
		$qrcode     		= $params[1] ? trim($params[1]) : '';
		
		$out_company        = $params[17] ? trim($params[17]) : '';
		//获取个人信息
		importModule("userInfo","class");
		$obj_user = new userInfo;
	    $user = $obj_user->get_user_detail($user_id);
	    
	    //上传图片
	    /*
	    if(!empty($_FILES['repaire_pic']['name']))
	    {
	    	$upload_pic = $this->_upload_pic();
			//添加且图片未成功
			if($action == 0 && empty($upload_pic))
			{
				echo "<script>alert('图片上传失败');history.go(-1);</script>";
				exit();
				//echo json_encode(array('status'=>0, 'message'=>'pic failed'));
				//exit();
			}
	    }
	    else
	    {
	    	$upload_pic = '';
	    }*/
		
		
		//基本信息
		$data = array(
				'name'				=> $patient_name,
				'sex'   			=> $sex,
				'birthday' 			=> $patient_age,
				'hospital' 			=> $hospital,
				'doctor' 			=> $doctor,
				'tooth_position' 	=> $tooth_position,
				'production_unit' 	=> $user[0]['company_name'],
				'create_time' 		=> date("Y-m-d H:i:s", time()),
				'operator' 			=> $user[0]['realname'],
				'false_tooth' 		=> $repaire_type,
				'product_detail_id'	=> $product_detail_id,
				'repaire_pic' 		=> $repaire_pic,//$upload_pic ? date("Ymd", time()).'/'.$upload_pic : '',
				'security_code' 	=> $qrcode,
				'mobile'			=> $user[0]['mobile'],
				'email'				=> $user[0]['email'],
				'update_time'		=> date("Y-m-d H:i:s", time()),
				'credits'			=> self::$credits,
				'user_id'			=> $user_id,
				'tech_id'			=> $user_id,
				'out_company'		=> $out_company
		);
		
		importModule("CreditInfo","class");
		$obj_credit = new CreditInfo;
		$credit_list = $obj_credit->get_credits_list();
		//print_r($credit_list);var_dump($tooth_position);
		if(!empty($credit_list))
		{
			$credit_item = array();
			foreach($credit_list as $key => $item)
			{
				$credit_item[$item['id']] = $item['credits'];
			}
			
			if($repaire_type == 7)
			{
				$data['credits'] = $credit_item[8];
			}
			elseif($repaire_type == 4)
			{
				$data['credits'] = $credit_item[9];
			}
			
		}
		
		/*
		if(!empty($repaire_pic))
		{
			$data['repaire_pic'] = $repaire_pic;
		}*/
		//print_r($data);var_dump($action);die;
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		
		if($action == 1)
		{
			//编辑
			$param = array(
				'patient_name'		=> $patient_name,
				'sex'   			=> $sex,
				'patient_age' 		=> $patient_age,
				'hospital' 			=> $hospital,
				'doctor' 			=> $doctor,
				'tooth_position' 	=> $tooth_position,
				'product_detail_id'	=> $product_detail_id,
				'false_tooth' 		=> $repaire_type,
				'security_code' 	=> $qrcode,
				'user_id'			=> $user_id,
				'repaire_pic'		=> $repaire_pic
			);
			$res = $obj_patient->update_patient($param);
		}
		else
		{
			//新加
			$res = $obj_patient->insert_patient($data);
		}

		if ($res) 
		{
			$_SESSION['qrcode'] = $qrcode;
			$url = 'patient.php?do=recordsuccess&user_id='.$user_id."&security_code=".$qrcode."&action=".$action;
			$return = array('status'=>1, 'url'=>$url, 'message'=>'录入成功');
			exit(json_encode($return));
			//header('Location: '.$url);
		}
		else
		{
			$return = array('status'=>0, 'url'=>'', 'message'=>'录入失败');
			exit(json_encode($return));
			//echo "<script>alert('录入失败');history.go(-1);</script>";
			//echo json_encode(array('status'=>0, 'message'=>'failed'));
			//exit();
		}
	
	}
	

	//病人录入
	public function doAddPatient()
	{
		$action 			= $_POST['action'] ? intval($_POST['action']) : 0;

		$hospital			= $_POST['hospital'] ? trim($_POST['hospital']) : ''; 
		$doctor 			= $_POST['doctor'] ? trim($_POST['doctor']) : '';
		$patient_name 		= $_POST['patient_name'] ? trim($_POST['patient_name']) : '';
		$sex 				= $_POST['gender'] ? intval($_POST['gender']) : 0;
		$patient_age 		= $_POST['patient_age'] ? intval($_POST['patient_age']) : 0;
		$tooth_position1 	= $_POST['tooth_position1'] ? trim($_POST['tooth_position1']) : '';
		$tooth_position2 	= $_POST['tooth_position2'] ? trim($_POST['tooth_position2']) : '';
		$tooth_position3 	= $_POST['tooth_position3'] ? trim($_POST['tooth_position3']) : '';
		$tooth_position4 	= $_POST['tooth_position4'] ? trim($_POST['tooth_position4']) : '';
		$tooth_position 	= $tooth_position1.'|'.$tooth_position2.'|'.$tooth_position3.'|'.$tooth_position4;
		$product_detail_id 	= $_POST['product_detail'] ? trim($_POST['product_detail']) : '';
		//$production_unit = trim($_POST['production_unit']);
		//$create_time = trim($_POST['create_time']);
		//$recorder = trim($_POST['recorder']);
		$repaire_type 		= $_POST['repaire_type'] ? intval($_POST['repaire_type']) : 0;
		$repaire_pic 		= $_FILES['repaire_pic'] ? $_FILES['repaire_pic'] : '';
		$user_id			= $_POST['user_id'] ? intval($_POST['user_id']) : 0;
		$qrcode     		= $_POST['qrcode'] ? trim($_POST['qrcode']) : '';
		$out_company        = $_POST['out_company'] ? trim($_POST['out_company']) : '';
		//获取个人信息
		importModule("userInfo","class");
		$obj_user = new userInfo;
	    $user = $obj_user->get_user_detail($user_id);
	    
	    //上传图片
	    if(!empty($_FILES['repaire_pic']['name']))
	    {
	    	$upload_pic = $this->_upload_pic();
			//添加且图片未成功
			if($action == 0 && empty($upload_pic))
			{
				echo "<script>alert('图片上传失败');history.go(-1);</script>";
				exit();
				//echo json_encode(array('status'=>0, 'message'=>'pic failed'));
				//exit();
			}
	    }
	    else
	    {
	    	$upload_pic = '';
	    }
		
		
		//基本信息
		$data = array(
				'name'				=> $patient_name,
				'sex'   			=> $sex,
				'birthday' 			=> $patient_age,
				'hospital' 			=> $hospital,
				'doctor' 			=> $doctor,
				'tooth_position' 	=> $tooth_position,
				'production_unit' 	=> $user[0]['company_name'],
				'create_time' 		=> date("Y-m-d H:i:s", time()),
				'operator' 			=> $user[0]['realname'],
				'false_tooth' 		=> $repaire_type,
				'product_detail_id'	=> $product_detail_id,
				'repaire_pic' 		=> $upload_pic ? date("Ymd", time()).'/'.$upload_pic : '',
				'security_code' 	=> $qrcode,
				'mobile'			=> $user[0]['mobile'],
				'email'				=> $user[0]['email'],
				'update_time'		=> date("Y-m-d H:i:s", time()),
				'credits'			=> self::$credits,
				'user_id'			=> $user_id,
				'tech_id'			=> $user_id,
				'out_company'		=> $out_company
		);
		
		importModule("CreditInfo","class");
		$obj_credit = new CreditInfo;
		$credit_list = $obj_credit->get_credits_list();
		if(!empty($credit_list))
		{
			$credit_item = array();
			foreach($credit_list as $key => $item)
			{
				$credit_item[$item['id']] = $item['credits'];
			}
			
			if($tooth_position == 1)
			{
				$data['credits'] = $credit_item[8];
			}
			elseif($tooth_position == 2)
			{
				$data['credits'] = $credit_item[9];
			}
			
		}
		
		if(!empty($upload_pic))
		{
			$data['repaire_pic'] = date("Ymd", time()).'/'.$upload_pic;
		}
		//print_r($data);var_dump($action);die;
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		if($action == 1)
		{
			//编辑
			$param = array(
				'patient_name'		=> $patient_name,
				'sex'   			=> $sex,
				'patient_age' 		=> $patient_age,
				'hospital' 			=> $hospital,
				'doctor' 			=> $doctor,
				'tooth_position' 	=> $tooth_position,
				'product_detail_id'	=> $product_detail_id,
				'false_tooth' 		=> $repaire_type,
				'security_code' 	=> $qrcode,
				'user_id'			=> $user_id
			);
			if(!empty($upload_pic))
			{
				$param['repaire_pic'] = date("Ymd", time()).'/'.$upload_pic;
			}
			
			$res = $obj_patient->update_patient($param);
		}
		else
		{
			//新加
			$res = $obj_patient->insert_patient($data);
		}

		if ($res) 
		{
			$_SESSION['qrcode'] = $qrcode;
			$url = 'patient.php?do=recordsuccess&user_id='.$user_id."&security_code=".$qrcode."&action=".$action;
			header('Location: '.$url);
		}
		else
		{
			echo "<script>alert('录入失败');history.go(-1);</script>";
			//echo json_encode(array('status'=>0, 'message'=>'failed'));
			exit();
		}
	
	}
	
	//病人更新
	public function doUpdatePatient()
	{
		$hospital		= $_GET['hospital'] ? trim($_GET['hospital']) : '';
		$doctor 		= $_GET['doctor'] ? trim($_GET['doctor']) : '';
		$patient_name 	= $_GET['patient_name'] ? trim($_GET['patient_name']) : '';
		$sex 			= $_GET['sex'] ? intval($_GET['sex']) : 0;
		$patient_age 	= $_GET['patient_age'] ? intval($_GET['patient_age']) : 0;
		$tooth_position1= $_GET['tooth_position1'] ? trim($_GET['tooth_position1']) : '';
		$tooth_position2= $_GET['tooth_position2'] ? trim($_GET['tooth_position2']) : '';
		$tooth_position3= $_GET['tooth_position3'] ? trim($_GET['tooth_position3']) : '';
		$tooth_position4= $_GET['tooth_position4'] ? trim($_GET['tooth_position4']) : '';
		$product_detail_id= $_GET['product_detail_id'] ? trim($_GET['product_detail_id']) : '';
		$repaire_pic 	= $_GET['file'] ? $_GET['file'] : '';
		$false_tooth 	= $_GET['false_tooth'] ? intval($_GET['false_tooth']) : 0;
		$user_id		= $_GET['user_id'] ? intval($_GET['user_id']) : 0;
		$qrcode     	= $_GET['qrcode'] ? trim($_GET['qrcode']) : '';
		$out_company    = $_GET['out_company'] ? trim($_GET['out_company']) : '';	
		

	    $data = array(
				'patient_name'		=> $patient_name,
				'sex'   			=> $sex,
				'patient_age' 		=> $patient_age,
				'hospital' 			=> $hospital,
				'doctor' 			=> $doctor,
				'tooth_position' 	=> $tooth_position1.'|'.$tooth_position2.'|'.$tooth_position3.'|'.$tooth_position4,
				'false_tooth' 		=> $false_tooth,
				'product_detail_id' => $product_detail_id,
				'repaire_pic' 		=> $repaire_pic,
				'security_code' 	=> $qrcode,
				'user_id'			=> $user_id,
				'out_company'		=> $out_company
			);

		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$res = $obj_patient->update_patient($data);

		if ($res) {
			exit(json_encode(array('status'=>true, 'message'=>'更新成功', url=>'user.php?do=ucenter&user_id='.$user_id.'&qrcode='.$qrcode)));
			//header('Location: user.php?do=ucenter&user_id='.$user_id."&qrcode="+$qrcode);
		}
		else
		{
			exit(json_encode(array('status'=>false, 'message'=>'更新失败')));
		}
		
	}
	
	//录入成功
	public function doRecordSuccess()
	{
		$user_id = $_GET['user_id'];
		$security_code = $_GET['security_code'];
		$action = !empty($_GET['action']) ? intval($_GET['action']) : 0;

		//积分详情
		importModule("userInfo","class");
		$obj_user = new userInfo;
		$left_credits = $obj_user->get_user_credits($user_id);
		
		//查看用户类型
		$user_detail = $obj_user->get_user_detail($user_id);
		
		$param = array(
			'operate_user_id'	=> $user_id,
			'page'				=> 1,
			'page_size'			=> 30
		);
		
		//获取最近录入的信息
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$patient = $obj_patient->patient_list($param);

		$data = array();
		if(!empty($patient))
		{
			foreach($patient as $key => $val)
			{
				$data[$key]['create_time'] = date("Y/m/d", strtotime($val['create_time']));
				$data[$key]['security_code'] = $val['security_code'];
				$data[$key]['hospital'] = $val['hospital'];
				$data[$key]['doctor'] = $val['doctor'];
				if($val['security_code'] == $security_code)
				{
					$tooth_position = $val['false_tooth'];
					break;
				}
			}
		}

		
		//修复体积分判断
		$credits = self::$credits;
		importModule("CreditInfo","class");
		$obj_credit = new CreditInfo;
		$credit_list = $obj_credit->get_credits_list();
		if(!empty($credit_list))
		{
			$credit_item = array();
			foreach($credit_list as $key => $item)
			{
				$credit_item[$item['id']] = $item['credits'];
			}
			
			if($tooth_position == 1)
			{
				$credits = $credit_item[8];
			}
			elseif($tooth_position == 4)
			{
				$credits = $credit_item[9];
			}
			
		}
		
		//print_r($data);die;
		$page = $this->app->page();
		$page->value('user_id',$user_id);
		$page->value('security_code',$security_code);
		$page->value('user_type',$user_detail[0]['user_type']);
		$page->value('credits',$credits);
		$page->value('left_credits',$left_credits);
		$page->value('patient',$data);
		$page->value('action',$action);
		$page->params['template'] = 'card_success.php';
		$page->output();
	}
	
	//上传图片
	private function _upload_pic()
	{
		if(empty($_FILES['repaire_pic']['name']))
		{
			return '';
		}
		$ar_type  = explode('.',$_FILES['repaire_pic']['name']); 
		$s_type   = strtolower($ar_type[1]);
		$file_path = date("Ymd", time());

		if(!in_array($s_type,array('jpg','png','bmp','gif', 'jpeg'))){
			echo "<script>alert('文件类型不正确(".$s_type.")');history.go(-1);</script>";
			exit();
			//exit(json_encode(array('status'=>0, 'info'=>'文件类型不正确')));
		}
		//echo "<script>alert('文件大小(".$_FILES["repaire_pic"]["size"].")');</script>";
		if($_FILES["repaire_pic"]["size"] > 5000000)
		{
			echo "<script>alert('图片不大于5M');history.go(-1);</script>";
			exit();
			//exit(json_encode(array('status'=>0, 'info'=>'图片不大于1M')));
		}
		
		
		$dirname = date("Ymd", time());
		$pic_path = '../app/public/uploads/'.$dirname;
		
		if(!file_exists($pic_path)) {
			if(!mkdir($pic_path, 0777, true)) {
				echo "<script>alert('创建目录失败');history.go(-1);</script>";
				exit();
				//exit(json_encode(array('status'=>0, 'info'=>'创建目录失败')));
			}
			
			chmod($pic_path.'/',0777);
		}
		
		import('util.UploadFile');
		$obj_upload = new UploadFile;
		
		$res = $obj_upload->upload($_FILES['repaire_pic'], $pic_path.'/', 1);

		if($res === false) {
			echo "<script>alert('文件上传失败');history.go(-1);</script>";
			exit();
			//exit(json_encode(array('status'=>0, 'info'=>'文件上传失败')));
		}
		
		return $res;
	}

	
}
$app->run();
	
?>
