<?php 
/**
 * app注册
 */
require_once('./../common.inc.php');

class register extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		$username	= !empty($_POST['account']) ? trim($_POST['account']) : '';
		$password	= !empty($_POST['password']) ? trim($_POST['password']) : '';
		$realname	= !empty($_POST['realname']) ? trim($_POST['realname']) : '';
		$mobile		= !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
		$company_name	= !empty($_POST['company_name']) ? trim($_POST['company_name']) : '';
		$address	= !empty($_POST['address']) ? trim($_POST['address']) : '';
		$role_id	= !empty($_POST['role_id']) ? intval($_POST['role_id']) : 0;

		if(empty($username) || empty($password) || empty($mobile) || empty($role_id))
		{
			$return = array('status'=>0, 'message'=>'参数错误');
			exit(json_encode($return));
		}
		
		$data = array(
			'username'		=> $username,
			'password'		=> md5($password),
			'realname'		=> $realname,
			'mobile'		=> $mobile,
			'company_name'	=> $company_name,
			'address'		=> $address,
			'role_id'		=> $role_id
		);
		
		//获取待确认的订单信息
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
	    $res = $obj_user->add_new_user($data);
	    
		if($res)
		{
			$return = array('status'=>1, 'message'=>'注册成功');
		}
		else
		{
			$return = array('status'=>1, 'message'=>'注册失败');
		}
		
		
		exit(json_encode($return));
		
	}
	

}
$app->run();
	
?>
