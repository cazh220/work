<?php 
/**
 * app订单信息
 */
require_once('./../common.inc.php');

class order_list extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		$user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
		$current = !empty($_POST['current_page']) ? intval($_POST['current_page']) : 1;
		$page_size = !empty($_POST['page_size']) ? intval($_POST['page_size']) : 20;
		
		$user_id = 1;
		if(empty($user_id))
		{
			$return = array('status'=>0, 'message'=>'参数错误');
			exit(json_encode($return));
		}
		
		//获取待确认的订单信息
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
	    $list = $obj_order->get_effect_order($user_id);
	    
		if(empty($list))
		{
			$return = array('status'=>0, 'message'=>'没有发现订单');
			exit(json_encode($return));
		}
		
		foreach($list as $key => $val)
		{
			//获取订单商品
			if($val['order_status']==0)
			{
				$list[$key]['order_status_name'] = '订单待确认';
			}
			else if($val['order_status']==1)
			{
				$list[$key]['order_status_name'] = '订单已确认';
			}
			else if($val['order_status']==2)
			{
				$list[$key]['order_status_name'] = '订单已撤销';
			}
			else if($val['order_status']==3)
			{
				$list[$key]['order_status_name'] = '订单已发货';
			}
			else
			{
				$list[$key]['order_status_name'] = '订单已完成';
			}
			
			$order_goods = $obj_order->get_simple_order_goods($val['order_id']);
			$list[$key]['children'] = $order_goods;
		}

		$return = array('status'=>1, 'message'=>'success', 'list'=>$list);
		
		exit(json_encode($return));
		
	}
	

}
$app->run();
	
?>
