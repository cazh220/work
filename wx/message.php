<?php 
/**
 * 消息类
 * 
 */
require_once('./common.inc.php');

class message extends Action {
	
	/**
	 * 默认执行的方法消息中心
	 */
	public function doDefault(){	
		$user_id = $_GET['user'];
		//获取
		importModule("MessageInfo","class");
		$obj_message = new MessageInfo;
		$list = $obj_message->get_message_list($user_id);
		//print_r($list);die;
		$page = $this->app->page();
		$page->value('user',$_SESSION);
		$page->value('list', $list);
		$page->params['template'] = 'mymessage.php';
		$page->output();
	}
	
	//读消息
	public function doMessageDetail()
	{
		$id = $_GET['msg_id'];
		
		importModule("MessageInfo","class");
		$obj_message = new MessageInfo;
		
		$detail = $obj_message->get_message_detail($id);
		$obj_message->read_message($id);//已读

		importModule("userInfo","class");
		$obj_user = new userInfo;
		//获取技工信息
		$techer = $obj_user->get_user_detail($detail[0]['to_user_id']);
		//获取医生信息
		$doctor = $obj_user->get_user_detail($detail[0]['from_user_id']);
		
		//获取患者
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$patient = $obj_patient->get_patient($detail[0]['qrcode']);

		$page = $this->app->page();
		$page->value('message', $detail[0]);
		$page->value('techer', $techer[0]);
		$page->value('doctor', $doctor[0]);
		$page->value('patient', $patient[0]);
		$page->params['template'] = 'message_detail.php';
		$page->output();
	}

	
}
$app->run();
	
?>
