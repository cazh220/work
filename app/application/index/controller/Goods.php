<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;
use think\Session;

class Goods
{	
	public function index()
	{
		$gift_id = isset($_GET['gift_id']) ? intval($_GET['gift_id']) : 0;
		$user_id = Session::get('user.user_id');
		//$param = array('page'=>1, 'page_size'=>10);
		$Shop = model('Shop');
		$detail = $Shop->get_goods_detail($gift_id);
		//判断是否库存为0 或者过了兑换日期
		$buy = 1;
		if($detail[0]['num'] <= 0 || time() > strtotime($detail[0]['validity_time']))
		{
			$buy = 0;
		}
		$view = new View();
		$view->assign('user', Session::get('user.mobile'));
		$view->assign('good', $detail[0]);
		$view->assign('pics', array($detail[0]['gift_photo']));
		$view->assign('buy', $buy);
		return $view->fetch('goods/good_detail');
	}
	
	//加入购物车
	public function add_cart()
	{
		$gift_id = isset($_GET['gift_id']) ? intval($_GET['gift_id']) : 0;
		
		//判断用户是否已登录
		$user_id = Session::get('user.user_id');
		if(empty($user_id))
		{
			//未登录跳转到登录页面
			exit(json_encode(array('status'=>0, 'message'=>'请先登录')));
			
		}
		
		
		$param = array(
			'goods_id'	=> $gift_id,
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
			exit(json_encode(array('status'=>1, 'message'=>'添加成功,请到我的购物车下单')));
		}
		else
		{
			exit(json_encode(array('status'=>0, 'message'=>'添加失败')));
		}
		
	}
	

	

}
