<?php 
/**
 * app购物车商品信息
 */
require_once('./../common.inc.php');

class cart_goods extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		$user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
		$user_id = 1;
		if(empty($user_id))
		{
			$return = array('status'=>0, 'message'=>'参数错误');
			exit(json_encode($return));
		}
		
		//获取待确认的订单信息
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
	    $unconfirm_order = $obj_order->get_unconfirm_order($user_id);
	    
		if(empty($unconfirm_order))
		{
			$return = array('status'=>0, 'message'=>'购物车无商品');
			exit(json_encode($return));
		}
		
		//获取待确认订单商品
		$order_goods = $obj_order->get_order_goods($unconfirm_order['order_id']);
		
		if(empty($order_goods))
		{
			$return = array('status'=>0, 'message'=>'购物车无商品');
			
		}
		else
		{
			$return = array('status'=>1, 'message'=>'success', 'list'=>$order_goods, 'order_id'=>$unconfirm_order['order_id']);
		}
		
		exit(json_encode($return));
		
	}
	

}
$app->run();
	
?>
