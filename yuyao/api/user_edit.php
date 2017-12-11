<?php 
/**
 * app修改用户信息
 */
require_once('./../common.inc.php');

class user_edit extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		$username	= !empty($_POST['account']) ? trim($_POST['account']) : '';
		$realname	= !empty($_POST['realname']) ? trim($_POST['realname']) : '';
		$mobile		= !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
		$company_name	= !empty($_POST['company_name']) ? trim($_POST['company_name']) : '';
		$address	= !empty($_POST['address']) ? trim($_POST['address']) : '';
		$role_id	= !empty($_POST['role_id']) ? intval($_POST['role_id']) : 0;

		if(empty($username) || empty($mobile) || empty($role_id))
		{
			$return = array('status'=>0, 'message'=>'参数错误');
			exit(json_encode($return));
		}
		
		$data = array(
			'realname'		=> $realname,
			'mobile'		=> $mobile,
			'company_name'	=> $company_name,
			'address'		=> $address,
			'role_id'		=> $role_id
		);
		
		//修改会员信息
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
	    $res = $obj_user->user_edit($data, $username);
	    
		if($res)
		{
			$return = array('status'=>1, 'message'=>'修改成功');
		}
		else
		{
			$return = array('status'=>1, 'message'=>'修改失败');
		}
		
		
		exit(json_encode($return));
		
	}
	

}
$app->run();
	
?>
