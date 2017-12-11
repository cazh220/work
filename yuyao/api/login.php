<?php 
/**
 * app登录
 */
require_once('./../common.inc.php');

class login extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		$username	= !empty($_POST['account']) ? trim($_POST['account']) : '';
		$password	= !empty($_POST['password']) ? trim($_POST['password']) : '';

		if(empty($username) || empty($password))
		{
			$return = array('status'=>0, 'message'=>'参数错误');
			exit(json_encode($return));
		}
		
		//获取待确认的订单信息
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
	    $res = $obj_user->check_login($username, $password);
	    
		if($res)
		{
			$return = array('status'=>1, 'message'=>'登录成功');
		}
		else
		{
			$return = array('status'=>1, 'message'=>'登录失败');
		}
		
		
		exit(json_encode($return));
		
	}
	

}
$app->run();
	
?>
