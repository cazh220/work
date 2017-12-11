<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Shop extends Model
{
	
	//获取列表
	public function goods_list($param)
	{
		$start = ($param['page']-1)*$param['page_size'];
		$size = $param['page_size'];
		$res = Db::query("SELECT * FROM hg_gift ORDER BY gift_id DESC LIMIT $start, $size");
		
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
	
	//获取商品详情
	public function get_goods_detail($gift_id)
	{
		if($gift_id)
		{
			$sql = "SELECT * FROM hg_gift WHERE gift_id = ".$gift_id;
			$res = Db::query($sql);
		}
		return !empty($res) ? $res : array();
	}
	

	
}

	