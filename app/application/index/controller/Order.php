<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;
use think\Session;

class Order
{	
	//我的兑换订单
	public function index()
	{
		$user_id = Session::get('user.user_id');
		$param = array('page'=>1, 'page_size'=>10, 'user_id'=>$user_id);
		$Order = model('Order');
		$order_list = $Order->order_list($param);
		if($order_list)
		{
			foreach($order_list as $key => $val)
			{
				$goods = $Order->get_order_goods($val['order_id']);
				$order_list[$key]['goods'] = $goods;
				$order_list[$key]['colums'] = count($goods);
			}
		}
		$view = new View();
		$view->assign('list', $order_list);
		$view->assign('user', Session::get('user.mobile'));
		return $view->fetch('index');
	}
	

	

}
