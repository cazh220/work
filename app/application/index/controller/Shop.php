<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;
use think\Session;

class Shop
{	
	public function index()
	{
		$user_id = Session::get('user.user_id');
		$param = array('page'=>1, 'page_size'=>10);
		$Shop = model('Shop');
		$goods_list = $Shop->goods_list($param);
		//print_r($goods_list);die;
		$view = new View();
		$view->assign('user', Session::get('user.mobile'));
		$view->assign('goods', $goods_list);
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


	

}
