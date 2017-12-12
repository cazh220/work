<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Order extends Model
{
	
	//插入订单
	public function create_order($order, $order_goods)
	{
		Db::startTrans();
		try{
			$sql = "INSERT INTO hg_order(order_no,order_status,user_id,username,province,city,district,address,zipcode,consignee,mobile,total_credits,create_time)VALUES('".$order['order_no']."','".$order['order_status']."','".$order['user_id']."','".$order['username']."','".$order['province']."','".$order['city']."','".$order['district']."','".$order['address']."','".$order['zipcode']."','".$order['consignee']."','".$order['mobile']."','".$order['total_credits']."', NOW())";
			//echo $sql;die;
			Db::execute($sql);
			$order_id = Db::name('hg_order')->getLastInsID();
			$order_no = $order['order_no'];
			//插入订单商品
			foreach($order_goods as $key => $val)
			{
				$sql = "INSERT INTO hg_order_gift(order_id,gift_id,gift_name,amount,price,gift_pic,order_no, num)VALUES('".$order_id."','".$val['gift_id']."','".$val['gift_name']."','".$val['amount']."','".$val['price']."','".$val['gift_pic']."','".$order_no."', '".$val['num']."')";
				Db::execute($sql);
			}
			
			//减积分
			$sql = "UPDATE hg_user SET exchanged_credits = exchanged_credits + ".$order['total_credits'].", left_credits = left_credits - ".$order['total_credits']." WHERE user_id = ".$order['user_id'];
			Db::execute($sql);
			
			//积分流水日志
			$sql = "INSERT INTO hg_credits_flow(type,credits, user_id,create_time,order_id)VALUES(2, '".$order['total_credits']."', '".$order['user_id']."',NOW(),'".$order_id."')";
			Db::execute($sql);
			
			//清空购物车
			$sql = "DELETE FROM hg_cart WHERE user_id = ".$order['user_id'];
			Db::execute($sql);
			
			//扣积分
			$admin_id = $user_id = !empty($order['user_id']) ? $order['user_id'] : 0;
			$username	= !empty($order['username']) ? $order['username'] : '';
			$content = '消耗积分'.$order['total_credits'];
			$ip = $_SERVER['REMOTE_ADDR'];
			
			$sql = "INSERT INTO hg_user_actions(admin_id,user_id,username,content,action_create_time,ip,channel)VAlUES($admin_id,$user_id,'".$username."','".$content."',NOW(),'".$ip."',1)";
			Db::execute($sql);
				
			// 提交事务
			Db::commit();    
			return true;
		} catch (\Exception $e) {
			//echo $e->getMessage();die;
			// 回滚事务
			Db::rollback();
			return false;
		}
	}
	
	//获取用户可兑换积分
	public function get_left_credits($user_id)
	{
		$sql = "SELECT left_credits FROM hg_user WHERE user_id = ".$user_id;
		$res = Db::query($sql);
		
		return !empty($res[0]['left_credits']) ? $res[0]['left_credits'] : 0;
	}
	
	//获取订单列表
	public function order_list($param)
	{
		$start = ($param['page']-1)*$param['page_size'];
		$size = $param['page_size'];
		$user_id = $param['user_id'];
		$sql = "SELECT * FROM hg_order ";
		if(!empty($user_id))
		{
			$sql .= " WHERE user_id = ".$user_id;
		}
		$sql .= " ORDER BY order_id DESC LIMIT $start, $size";

		$res = Db::query($sql);
		return !empty($res) ? $res : array();
	}
	
	//获取订单商品
	public function get_order_goods($order_id)
	{
		$sql = "SELECT * FROM hg_order_gift WHERE order_id = ".$order_id;
		$res = Db::query($sql);
		return !empty($res) ? $res : array();
	}
	
	
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

	

	
}

	