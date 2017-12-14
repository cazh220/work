<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;
use think\Session;

class Cart
{	
	//购物车列表
	public function index()
	{
		$user_id = Session::get('user.user_id');
		$param = array('page'=>1, 'page_size'=>10);
		$Cart = model('Cart');
		$cart_goods_list = $Cart->good_cart_goods($param);
		
		//消耗总积分
		$need_total_credits = 0;
		if($cart_goods_list)
		{
			foreach($cart_goods_list as $key => $val)
			{
				$need_total_credits += $val['goods_num']*$val['credits'];
			}
		}
		
		//获取省
		$Region = model('Region');
		$province = $Region->get_province();
		
		//print_r($cart_goods_list);die;
		$num_goods = count($cart_goods_list);
		$view = new View();
		$view->assign('user', Session::get('user.mobile'));
		$view->assign('province', $province);
		$view->assign('goods', $cart_goods_list);
		$view->assign('num_goods', $num_goods);//商品种类数
		$view->assign('need_total_credits', $need_total_credits);//所需积分
		return $view->fetch('index');
	}
	
	//添加购物车
	public function add_cart()
	{
		$user_id = Session::get('user.user_id');
		$goods_id = $_POST['id'];
		$param = array(
			'goods_id'	=> $goods_id,
			'user_id'	=> $user_id
		);
		
		$Shop = model('Shop');
		//判断购物车里是否已有
		$res = $Shop->is_in_cart($param);
		if(!empty($res))
		{
			exit(json_encode(array('status'=>0, 'message'=>'已加入购物车了')));
		}
		
		$result = $Shop->add_cart($param);
		
		if($result)
		{
			exit(json_encode(array('status'=>1, 'message'=>'添加成功')));
		}
		else
		{
			exit(json_encode(array('status'=>0, 'message'=>'添加失败')));
		}
	}
	
	//删除购物车商品
	public function delete_cart_goods()
	{
		$user_id = Session::get('user.user_id');
		$goods_id = $_POST['id'];
		$param  = array(
			'goods_id'	=> $goods_id,
			'user_id'	=> $user_id
		);
		//删除购物车商品
		$Cart = model('Cart');
		$result = $Cart->delete_cart_goods($param);
		
		if($result)
		{
			exit(json_encode(array('status'=>1, 'message'=>'移除成功')));
		}
		else
		{
			exit(json_encode(array('status'=>0, 'message'=>'移除失败')));
		}
	}
	
	//清空购物车
	public function clear_cart_goods()
	{
		$user_id = Session::get('user.user_id');
		//删除购物车商品
		$Cart = model('Cart');
		$result = $Cart->clear_cart_goods($user_id);
		
		if($result)
		{
			exit(json_encode(array('status'=>1, 'message'=>'清空成功')));
		}
		else
		{
			exit(json_encode(array('status'=>0, 'message'=>'清空失败')));
		}
	}
	
	//兑换
	public function order_create()
	{
		$detail = json_decode($_POST['detail'], true);
		$address = json_decode($_POST['address'], true);
		
		$user_id = Session::get('user.user_id');
		$username = Session::get('user.mobile');
		$Order = model('Order');
		$Cart = model('Cart');
		
		$left_credits = $Order->get_left_credits($user_id);
		
		$total_credits = 0;
		$order_goods = array();
		if(!empty($detail))
		{
			foreach($detail as $key => $val)
			{
				$temp = explode(':', $val);
				$gift_id = $temp[0];
				$gift_num = $temp[1];
				//获取商品信息
				$good_detail = $Cart->goods_detail($gift_id);
				$total_credits += $good_detail[0]['credits']*$gift_num;
				$order_goods[$key] = array(
					'gift_id'	=> $good_detail[0]['gift_id'],
					'gift_name' => $good_detail[0]['gift_name'],
					'amount'	=> $good_detail[0]['credits']*$gift_num,
					'price'		=> $good_detail[0]['credits'],
					'gift_pic'	=> $good_detail[0]['gift_photo'],
					'num'		=> $gift_num
				);
			}
		}
		
		if($total_credits > $left_credits)
		{
			exit(json_encode(array('status'=>0, 'message'=>'积分不足')));
		}
		
		//生成订单号
		$order_no = $this->get_orderno();
		$order = array(
			'order_no'		=> $order_no,
			'order_status'	=> 0,
			'user_id'		=> $user_id,
			'username'		=> $username,
			'province'		=> $address['province'],
			'city'			=> $address['city'],
			'district'		=> $address['district'],
			'address'		=> $address['address'],
			'zipcode'		=> $address['zipcode'],
			'consignee'		=> $address['consignee'],
			'mobile'		=> $address['mobile'],
			'total_credits' => $total_credits,
			'create_time'	=> date("Y-m-d H:i:s")	
		);
		
		$res = $Order->create_order($order, $order_goods);
		
		if($res)
		{
			exit(json_encode(array('status'=>1, 'message'=>'兑换成功')));
		}
		else
		{
			exit(json_encode(array('status'=>0, 'message'=>'兑换失败')));
		}
	}
	
	//生成订单号
	public function get_orderno()
    {
    	return '10'.date("YmdHis",time()).rand(1000,9999);
    }

	

}
