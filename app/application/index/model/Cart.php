<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Cart extends Model
{
	
	//获取购物车信息
	public function good_cart_goods($param)
	{
		$start = ($param['page']-1)*$param['page_size'];
		$size = $param['page_size'];
		$res = Db::query("SELECT * FROM hg_cart a LEFT JOIN hg_gift b ON a.goods_id = b.gift_id ORDER BY a.add_time DESC LIMIT $start, $size");
		
		return !empty($res) ? $res : array();
	}
	
	//加入购物车
	public function add_cart($param)
	{
		$sql = "INSERT INTO hg_cart(user_id, goods_id, goods_num, add_time)VALUES(".$param['user_id'].", ".$param['goods_id'].", 1, NOW())";
		$res = Db::execute($sql);
		
		return !empty($res) ? $res : 0;
	}
	
	//是否已在购物车
	public function is_in_cart($param)
	{
		$sql = "SELECT * FROM hg_cart WHERE user_id = ".$param['user_id'].' AND goods_id = '.$param['goods_id'];
		$res = Db::query($sql);
		
		return !empty($res) ? $res : array();
	}
	
	//删除购物车商品
	public function delete_cart_goods($param)
	{
		$sql = "DELETE FROM hg_cart WHERE user_id = ".$param['user_id']." AND goods_id = ".$param['goods_id'];
		$res = Db::execute($sql);
		
		return !empty($res) ? $res : 0;
	}
	
	//清空购物车商品
	public function clear_cart_goods($user_id)
	{
		$sql = "DELETE FROM hg_cart WHERE user_id = ".$user_id;
		$res = Db::execute($sql);
		
		return !empty($res) ? $res : 0;
	}
	
	//获取商品详情
	public function goods_detail($goods_id)
	{
		$sql = "SELECT * FROM hg_gift WHERE gift_id = {$goods_id}";

		$res = Db::query($sql);
		return !empty($res) ? $res : array();
	}

	
}

	