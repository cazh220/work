<?php
namespace app\admin\controller;
use think\Controller;
use think\View;
use think\Db;
use think\Session;
use think\Log;


class Message extends Controller
{
	//获取消息列表
    public function index()
    {
    	$sender		= !empty($_GET['sender']) ? trim($_GET['sender']) : '';
    	$receiver	= !empty($_GET['receiver']) ? trim($_GET['receiver']) : '';
    	$start_time	= !empty($_GET['start_time']) ? trim($_GET['start_time']) : '';
    	$end_time	= !empty($_GET['end_time']) ? trim($_GET['end_time']) : '';
    	
    	$param = array(
    		'sender'	=> $sender,
    		'receiver'	=> $receiver,
    		'start_time'=> $start_time,
    		'end_time'	=> $end_time
    	);
    	
		$Message = model('Message');
		$obj_data = $Message->getMessageList($param);
		$data = $obj_data->toArray();
		$page = $obj_data->render();

        $view = new View();
		$view->assign('list', $data['data']);
		$view->assign('page', $page);
		return $view->fetch('message/index');
    }
	
	public function send_message()
	{
		//获取权限列表

		$view = new View();
		return $view->fetch('message/send');
	}
	
	
	//发送信息
	public function send()
	{
		$type		= !empty($_POST['receiver_type']) ? intval($_POST['receiver_type']) : 0;
		$mobile 	= !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
		$content 	= !empty($_POST['content']) ? trim($_POST['content']) : '';
		
		$Message = model('Message');
		if($type == 1)
		{
			//指定用户
			$mobileArr = explode("\n", $mobile);
			$res = $Message->insert_message($mobileArr, $content);
		}
		else
		{
			//所有人
			$res = $Message->send_all_message($content);
		}
		
		
		if($res)
		{
			echo "<script>alert('发送成功');window.location.href='../message/index';</script>";
			exit();
		}
		else
		{
			echo "<script>alert('发送失败');history.go(-1);</script>";
			exit();
		}
	}
	
}