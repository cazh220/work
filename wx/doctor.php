<?php 
/**
 * 医生类
 * 
 */
require_once('./common.inc.php');

class doctor extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		
		$page = $this->app->page();
		$page->value('user',$_SESSION);
		$page->params['template'] = 'user.php';
		$page->output();
	}
	
	//纠错页面
	public function doCorrection()
	{
		$qrcode 	= !empty($_GET['qrcode']) ? trim($_GET['qrcode']) : '';
		$user_id	= !empty($_GET['user_id']) ? intval($_GET['user_id']) : 0;
		
		$page = $this->app->page();
		$page->value('user',$_SESSION);
		$page->value('qrcode',$qrcode);
		$page->value('user_id',$user_id);
		$page->params['template'] = 'correction.php';
		$page->output();

	}
	
	//发送消息
	public function doWriteCorrction()
	{
		$error 		= $_GET['errorinfo'] ? trim($_GET['errorinfo']) : '';
		$correct 	= $_GET['correction'] ? trim($_GET['correction']) : '';
		$qrcode 	= $_GET['qrcode'] ? trim($_GET['qrcode']) : '';
		$user_id 	= $_GET['user_id'] ? intval($_GET['user_id']) : 0;
		
		
		//获取录入的技工
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$patient = $obj_patient->get_patient($qrcode);

		$param = array(
			'type'			=> 1,
			'from'			=> $user_id,
			'to'			=> $patient[0]['operate_user_id'],
			'error_info'	=> $error,
			'correct_info'	=> $correct,
			'create_time'	=> date("Y-m-d H:i:s", time()),
			'qrcode'		=> $qrcode
		);
		
		importModule("MessageInfo","class");
		$obj_message = new MessageInfo;
		$res = $obj_message->write_message($param);
		if($res)
		{
			$data = array(
				'status'	=> 1,
				'msg'		=> 'success',
				'result'	=> array('user_id'=>$user_id)
			);
			exit(json_encode($data));
		}
		else
		{
			exit(json_encode(array('status'=>0, 'msg'=>'failed')));
		}
				
	}

	//病人录入
	public function doAddPatient()
	{

		$hospital	= trim($_POST['hospital']);
		$doctor = trim($_POST['doctor']);
		$patient_name = trim($_POST['patient_name']);
		$sex = $_POST['gender'];
		$patient_age = intval($_POST['patient_age']);
		$tooth_position = trim($_POST['tooth_position']);
		//$production_unit = trim($_POST['production_unit']);
		//$create_time = trim($_POST['create_time']);
		//$recorder = trim($_POST['recorder']);
		$repaire_type = intval($_POST['repaire_type']);
		$repaire_pic = $_FILES['repaire_pic'];
		$user_id	= intval($_POST['user_id']);
		$qrcode     = trim($_POST['qrcode']);
	    
	    //上传图片
		$upload_pic = $this->_upload_pic();
		if (!empty($upload_pic))
		{
			//插入基本信息
			$data = array(
				'name'	=> $patient_name,
				'sex'   => $sex,
				'birthday' => $patient_age,
				'hospital' => $hospital,
				'doctor' => $doctor,
				'tooth_position' => $tooth_position,
				'production_unit' => $_SESSION['company_name'],
				'create_time' => date("Y-m-d H:i:s", time()),
				'operator' => $_SESSION['realname'],
				'false_tooth' => $repaire_type,
				'repaire_pic' => $upload_pic,
				'security_code' => $qrcode,
				'mobile'	=> $_SESSION['mobile'],
				'email'		=> $_SESSION['email'],
				'update_time'	=> date("Y-m-d H:i:s", time()),
			);
	
			importModule("PatientInfo","class");
			$obj_patient = new PatientInfo;
			$res = $obj_patient->insert_patient($data);

			if ($res) {
				$_SESSION['qrcode'] = $qrcode;
				header('Location: patient.php?do=recordsuccess&user_id='.$user_id);
			}
		}
		else 
		{
			echo json_encode(array('status'=>0, 'message'=>'failed'));
		}

	}
	
	//录入成功
	public function doRecordSuccess()
	{
		$page = $this->app->page();
		$page->value('user',$_SESSION);
		$page->params['template'] = 'card_success.php';
		$page->output();
	}
	
	//医生确认
	public function doConfirm()
	{
		$param 			= $_POST['param'] ? trim($_POST['param']) : '';
		$params			= explode(':', $param);
		
		$doctor_id		= !empty($params[2]) ? trim($params[2]) : '';
		$patient_id		= !empty($params[1]) ? trim($params[1]) : '';
		$case_pic_yl	= !empty($params[0]) ? trim($params[0]) : '';

	    $where['patient_id'] = $patient_id;
	    $data = array(
	    	'doctor_id'		=> $doctor_id,
			'confirm_time'	=> date("Y-m-d H:i:s", time()),
			'case_pic'		=> $case_pic_yl
	    );

	    importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		//获取病人信息
	    $patient_info = $obj_patient->get_patient_by_id($patient_id);
		if($patient_info['false_tooth'] == 1)
		{
			$credits = 30;
		}
		elseif($patient_info['false_tooth'] == 4)
		{
			$credits = 10;
		}
		else
		{
			$credits = 30;
		}
		
		$res = $obj_patient->confirm_patient($data, $where);
	    if ($res) {
			//更新积分
			if($patient_info['doctor_id'] != $doctor_id)
			{
				$res_credit = $obj_patient->update_credits($doctor_id, $credits);//更新积分
				$action = 0;
			}
			else
			{
				$action = 1;//第二次
				$credits = 0;
			}
			

			//插入操作日志
			importModule("userInfo","class");
			$obj_user = new userInfo;
			$doctor_info = $obj_user->get_user_detail($doctor_id);
			$username = $doctor_info[0]['username'];
			$param = array(
				'user_id'	=> $doctor_id,
				'username'	=> $username,
				'content'	=> '增加积分'.$credits,
			);
			$obj_user->action_log($param);
			
			$url = 'doctor.php?do=confirmnote&user_id='.$doctor_id.'&action='.$action."&credits=".$credits.'&security_code='.$patient_info['security_code'].'&left_credits='.$doctor_info[0]['left_credits'];
			echo json_encode(array('status'=>1, 'message'=>'提交成功', 'url'=>$url));
			exit();
			//header('Location: doctor.php?do=confirmnote&user_id='.$doctor_id);
		}
		else 
		{
			echo json_encode(array('status'=>0, 'message'=>'failed', 'url'=>''));
			exit();
		}
	
	}
	
	//录入成功
	public function doConfirmNote()
	{
		$user_id = $_GET['user_id'];
		$action = $_GET['action'];
		$credits = $_GET['credits'];
		$left_credits = $_GET['left_credits'];
		$security_code = $_GET['security_code'];
		//获取录入的patient的信息
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$param = array(
			'operate_user_id'	=> $user_id,
			'page'				=> 1,
			'page_size'			=> 30
		);
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
		
		
		
		$page = $this->app->page();
		$page->value('user_id',$user_id);
		$page->value('action',$action);
		$page->value('credits',$credits);
		$page->value('left_credits',$left_credits);
		$page->value('security_code',$security_code);
		$page->value('patient',$data);
		$page->params['template'] = 'confirm_success_new.php';
		$page->output();
	}
	
	
	//上传案例图片
	private function _upload_case_pic()
	{
		$ar_type  = explode('.',$_FILES['case_pic']['name']); 
		$s_type   = strtolower($ar_type[1]);
		
		if(!in_array($s_type,array('jpg','png','bmp','gif', 'jpeg'))){
			echo "<script>alert('文件类型不正确(".$s_type.")');history.go(-1);</script>";
			exit();
			//exit(json_encode(array('status'=>0, 'info'=>'文件类型不正确('.$s_type.')')));
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
		
		$res = $obj_upload->upload($_FILES['case_pic'], $pic_path.'/', 1);
		if($res === false) {
			echo "<script>alert('文件上传失败');history.go(-1);</script>";
			exit();
			//exit(json_encode(array('status'=>0, 'info'=>'文件上传失败')));
		}

		/*
		if(!file_exists('public/upload/data/')) {
			if(!mkdir('data')) {
				exit(json_encode(array('status'=>0, 'info'=>'创建目录失败')));
			}
			
			chmod('data/',0777);
		}
		
		import('util.UploadFile');
		$obj_upload = new UploadFile;
		
		$res = $obj_upload->upload($_FILES['case_pic'],'./public/upload/data/', 1);

		if($res === false) {
			exit(json_encode(array('status'=>0, 'info'=>'文件上传失败')));
		}
		*/
		
		return $res;
	}
	
	
	//上传图片
	private function _upload_pic()
	{
		$ar_type  = explode('.',$_FILES['repaire_pic']['name']); 
		$s_type   = strtolower($ar_type[1]);
		
		if(!in_array($s_type,array('jpg','png','bmp','gif'))){
			exit(json_encode(array('status'=>0, 'info'=>'文件类型不正确')));
		}
		
		if(!file_exists('public/upload/data/')) {
			if(!mkdir('data')) {
				exit(json_encode(array('status'=>0, 'info'=>'创建目录失败')));
			}
			
			chmod('data/',0777);
		}
		
		import('util.UploadFile');
		$obj_upload = new UploadFile;
		
		$res = $obj_upload->upload($_FILES['repaire_pic'],'./public/upload/data/', 1);

		if($res === false) {
			exit(json_encode(array('status'=>0, 'info'=>'文件上传失败')));
		}
		
		return $res;
	}

	
}
$app->run();
	
?>
