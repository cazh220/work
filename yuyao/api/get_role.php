<?php 
/**
 * 获取用户角色
 */
require_once('./../common.inc.php');

class get_role extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		
		//获取待确认的订单信息
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
	    $list = $obj_role->get_all_roles();
	    
		if(empty($list))
		{
			$return = array('status'=>0, 'message'=>'角色');
			exit(json_encode($return));
		}
		
		$return = array('status'=>1, 'message'=>'success', 'list'=>$list['list']);
		
		exit(json_encode($return));
		
	}
	

}
$app->run();
	
?>
