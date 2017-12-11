<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Paginator;

class Order extends Model
{	
	public function get_order($param=array())
	{
		$obj_data = Db::name('hg_order');
		
		if (!empty($param['keyword']))
		{
			$obj_data = $obj_data->where('order_no', 'like', $param['keyword'].'%');
		}
		
		if(!empty($param['type']))
		{
			$obj_data = $obj_data->where('order_status', $param['type']);
		}

		
		return $obj_data->paginate(10);
	}
	
	public function order_detail($order_id)
	{
		$res = Db::query("SELECT *,a.address_id as address_id FROM hg_order a left join hg_order_gift b ON a.order_id = b.order_id left join hg_user c ON a.user_id = c.user_id WHERE a.order_id = :order_id", ['order_id'=>$order_id]);
		return !empty($res) ? $res : array();
	}
	
	//获取订单详情
	public function get_order_detail($address_id=0)
	{
		if(empty($address_id))
		{
			return array();
		}
		$res = Db::query("SELECT a.*,b.`name` as province_name, c.`name` as city_name, d.`name` as district_name  FROM hg_address a LEFT JOIN hg_region b on a.province = b.id LEFT JOIN hg_region c ON a.city = c.id LEFT JOIN hg_region d ON a.district = d.id WHERE address_id = {$address_id}");
		
		return !empty($res) ? $res : array();
	}
	
	//更改订单状态
	public function update_order_status($status=0, $order_id=0)
	{
		$res = 0;
		if(!empty($order_id))
		{
			if ($status == 1)
			{//确认
				$res = Db::query("UPDATE hg_order SET order_status = :order_status, confirm_time = :confirm_time WHERE order_id = :order_id", ['order_status'=>$status, 'confirm_time'=>date("Y-m-d H:i:s"), 'order_id'=>$order_id]);
			}
			elseif($status == 2)
			{//发货
				$res = Db::query("UPDATE hg_order SET order_status = :order_status, send_time = :send_time WHERE order_id = :order_id", ['order_status'=>$status, 'send_time'=>date("Y-m-d H:i:s"), 'order_id'=>$order_id]);
			}
			else
			{
				$res = Db::query("UPDATE hg_order SET order_status = :order_status WHERE order_id = :order_id", ['order_status'=>$status, 'order_id'=>$order_id]);
			}
		}
		return $res;
	}
	
	//更新物流信息
	public function update_ship($data = array())
	{
		$res = 0;
		if(!empty($data))
		{
			$res = Db::query("UPDATE hg_order SET consignee = :consignee, ship_way = :ship_way, mobile = :mobile, ship_company = :ship_company, address = :address, ship_no = :ship_no, update_ship_time = :update_ship_time WHERE order_id = :order_id", ['consignee'=>$data['consignee'], 'ship_way'=>$data['ship_way'], 'mobile'=>$data['mobile'], 'ship_company'=>$data['ship_company'], 'address'=>$data['address'], 'ship_no'=>$data['ship_no'], 'update_ship_time'=>$data['update_ship_time'],'order_id'=>$data['order_id']]);
		}
		return $res;
	}
	
}