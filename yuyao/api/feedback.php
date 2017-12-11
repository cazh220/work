<?php 
/**
 * app反馈
 */
require_once('./../common.inc.php');

class feedback extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		$appid	= !empty($_REQUEST['appid']) ? trim($_REQUEST['appid']) : '';
		$imei	= !empty($_REQUEST['imei']) ? trim($_REQUEST['imei']) : '';
		$images	= !empty($_REQUEST['images']) ? trim($_REQUEST['images']) : '';
		$p		= !empty($_REQUEST['p']) ? trim($_REQUEST['p']) : '';
		$md	= !empty($_REQUEST['md']) ? trim($_REQUEST['md']) : '';
		$app_version	= !empty($_REQUEST['app_version']) ? trim($_REQUEST['app_version']) : '';
		$plus_version	= !empty($_REQUEST['plus_version']) ? trim($_REQUEST['plus_version']) : '';
		$os	= !empty($_REQUEST['os']) ? trim($_REQUEST['os']) : '';
		$net	= !empty($_REQUEST['net']) ? trim($_REQUEST['net']) : '';
		$content	= !empty($_REQUEST['content']) ? trim($_REQUEST['content']) : '';
		$contact	= !empty($_REQUEST['contact']) ? trim($_REQUEST['contact']) : '';
		$score = !empty($_REQUEST['score']) ? intval($_REQUEST['score']) : '';
//print_r($_GET);die;		
		$data = array(
			'appid'		=> $appid,
			'imei'		=> $imei,
			'images'	=> $images,
			'p'			=> $p,
			'md'		=> $md,
			'app_version'	=> $app_version,
			'plus_version'	=> $plus_version,
			'os'		=> $os,
			'net'		=> $net,
			'content'	=> $content,
			'contact'	=> $contact,
			'score'		=> $score
		);

		if(empty($data))
		{
			$return = array('status'=>0, 'message'=>'参数错误');
			exit(json_encode($return));
		}
		
		//获取待确认的订单信息
		importModule("FeedbackInfo","class");
		$obj_feedback = new FeedbackInfo;
	    $res = $obj_feedback->insert_feedback($data);
	    
		if($res)
		{
			$return = array('status'=>1, 'message'=>'success');
		}
		else
		{
			$return = array('status'=>1, 'message'=>'failed');
		}
		
		
		exit(json_encode($return));
		
	}
	

}
$app->run();
	
?>
