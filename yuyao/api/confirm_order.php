<?php 
/**
 * app 下订单
 */
require_once('./../common.inc.php');

class confirm_order extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){
		$data = !empty($_POST['info']) ? trim($_POST['info']) : '';
		$order_id = !empty($_POST['order_id']) ? intval($_POST['order_id']) : 0;
		if(empty($data) || empty($order_id))
		{
			$return = array('status'=>0, 'message'=>'参数错误');
			exit(json_encode($return));
		}
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		$data_arr = json_decode($data, true);
		
		$total_num = 0;
		$total_amount = 0;
		foreach($data_arr as $key => $val)
		{
			$goods = explode(':', $val);
			//获取订单中商品价格
			$detail = $obj_order->get_order_goods_detail($order_id, $goods[0]);
			$total_num += $goods[1];
			$total_amount += ($goods[1]*$detail['good_price']);
			
			$order_goods[$key] = array(
				'order_id'	=> $order_id,
				'goods_id'	=> $goods[0],
				'goods_num'	=> $goods[1]
			);
		}
		
		$order = array(
			'order_id'	=> $order_id,
			'total_num' => $total_num,
			'total_amount' => $total_amount
		);
		
		$res = $obj_order->new_confirm_order($order, $order_goods);
		if(empty($res))
		{
			$return = array('status'=>0, 'message'=>'下单失败');
			
		}
		else
		{
			$return = array('status'=>1, 'message'=>'下单成功');
		}
		
		exit(json_encode($return));
		
	}
	

}
$app->run();
	
?>
