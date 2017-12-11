<?php 
/**
 * 获取用户
 */
require_once('./../common.inc.php');

class get_user extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){//exit(json_encode($_POST));
		$username = !empty($_POST['username']) ? trim($_POST['username']) : '';
		//获取待确认的订单信息
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
	    $list = $obj_user->get_user($username);
	    
		if(empty($list))
		{
			$return = array('status'=>0, 'message'=>'获取用户失败');
			exit(json_encode($return));
		}
		
		$return = array('status'=>1, 'message'=>'success', 'list'=>$list);
		
		exit(json_encode($return));
		
	}
	

}
$app->run();
	
?>
