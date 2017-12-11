<?php 
/**
 * app商品分类
 */
require_once('./../common.inc.php');

class add_buy_list extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){
		$goods_id 	= !empty($_POST['goods_id']) ? intval($_POST['goods_id']) : 0;
		$user_id	= !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
		$price		= !empty($_POST['price']) ? intval($_POST['price']) : 0;
		$operator	= !empty($_POST['operator']) ? trim($_POST['operator']) : '';
		//exit(json_encode($_POST));
		/*
		$goods_id = 5;
		$user_id = 1;
		$operator = '曹政';
		$price = 1;
		*/
		if(empty($goods_id) || empty($user_id))
		{
			$return = array('status'=>0, 'message'=>'参数错误');
			exit(json_encode($return));
		}
		
	    //先获取购物车是否待确认的订单
	    importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
	    $unconfirm_order = $obj_order->get_unconfirm_order($user_id);	
	    
	    //判断商品是否存在
	    $res_goods = $obj_order->get_unconfirm_order_goods(array('user_id'=>$user_id,'goods_id'=>$goods_id));
	    if($res_goods)
	    {
	    	$return = array('status'=>0, 'message'=>'商品已放入购物车');
			exit(json_encode($return));
	    }
	    	
		if($unconfirm_order)
		{
			//存在待确认的订单
			$order_goods = array(
				'order_id'		=> $unconfirm_order['order_id'],
				'goods_id'		=> $goods_id,
				'goods_num'		=> 1,
				'goods_price'	=> $price
			);
			
			$order = array(
				'order_id'		=> $unconfirm_order['order_id'],
				'total_amount'	=> $unconfirm_order['total_amount']+$price,
				'total_num'		=> $unconfirm_order['total_num']+1
			);
			
			$res = $obj_order->add_new_order_goods($order, array($order_goods));
		}
		else
		{
			//新订单
			$order_no = date("YmdHis").rand(1000,9999);
			//订单总览
			$order = array(
				'order_no'		=> $order_no,
				'operator_id'	=> $user_id,
				'operator'		=> $operator,
				'order_status'	=> 0,
			);

			$order_goods = array();
			$total_num = 0;
			$total_amount = 0;
			$id_set = array($goods_id);
			foreach($id_set as $key => $val)
			{
				//获取商品
				importModule("GoodsInfo","class");
				$obj_good = new GoodsInfo;
				$price = $obj_good->get_good_price($val);
				$order_goods[$key] = array(
					'goods_id'	=> $val,
					'goods_num'	=> 1,
					'good_price'	=> $price,
				);
				$total_num++;
				$total_amount += $price;
			}
			
			$order['total_num'] = $total_num;
			$order['total_amount'] = $total_amount;
			//print_r($order);print_r($order_goods);die;
			$res = $obj_order->add_order($order, $order_goods);
		}
		
		if($res)
		{
			$return = array('status'=>1, 'message'=>'添加成功');
		}
		else
		{
			$return = array('status'=>0, 'message'=>'添加失败');
		}
		//$return = array('goods_id'=>$goods_id);
		exit(json_encode($return));
	}
	

}
$app->run();
	
?>
