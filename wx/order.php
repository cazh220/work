<?php 
/**
 * 订单处理类

 */
require_once('./common.inc.php');

class order extends Action {
	
	/**
	 * 默认执行的方法(用户登录页面)
	 */
	public function doDefault(){	

		importModule("ShopInfo","class");
		$obj_shop = new ShopInfo;
		$products = $obj_shop->get_products();

		//print_r($products);die;
		
		$page = $this->app->page();
		//print_r($_SESSION);
		$page->value('list', $products);
		$page->value('user',$_SESSION);
		$page->params['template'] = 'shop.php';
		$page->output();
	}
	
	//订单确认
	public function doOrderConfirm()
	{
		$id = $_GET['id'];
		$id_arr = explode(',', $id);
		$user_id = $_GET['user_id'];
		//获取商品信息
		$goods_list = array();
		importModule("ShopInfo","class");
		$obj_shop = new ShopInfo;
		$products = $obj_shop->get_product_list($id);
		//需要的积分
		$need_credits = 0;
		if(!empty($products))
		{
			foreach($products as $key => $val)
			{
				$need_credits += $val['credits'];
			}
		}
		
		importModule("AreaInfo","class");
		$obj_area = new AreaInfo;
		$province = $obj_area->get_province();

		//获取积分
		importModule("userInfo","class");
		$obj_user = new userInfo;
		$user = $obj_user->get_user_detail($user_id);
		$left_credits = $user[0]['left_credits'];
		//print_r($user);die;
		importModule("AddressInfo","class");
		$obj_address = new AddressInfo;
		
		$address_select = array();
		$address = $obj_address->address_list($user_id);
		if($address)
		{
			foreach($address as $key => $val)
			{
				if($user[0]['address_id'] == $val['address_id'])
				{
					$address_select = $val;
				}
			}
		}

		$page = $this->app->page();
		$page->value('list', $products);
		$page->value('user',$user[0]);
		$page->value('left_credits',$left_credits);
		$page->value('need_credits',$need_credits);
		$page->value('province',$province);
		$page->value('order_id',$id);
		$page->value('address',$address);
		$page->value('address_select',$address_select);
		$page->params['template'] = 'order_detail.php';
		$page->output();
	}
	
	//生成订单
	public function doCreateOrder()
	{
		$gift_id_arr = $_POST['gift_id'];
		$gift_num    = $_POST['gift_num'];
		//print_r($_POST);die;
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		//生成订单号
		$order_no = $obj_order->get_orderno();
		
		importModule("ShopInfo","class");
		$obj_shop = new ShopInfo;
		//订单商品
		$order_goods = array();
		$total_credits = 0;
		foreach($gift_id_arr as $key => $value)
		{
			$gift_detail = $obj_shop->get_gift_detail($value);
			
			$order_goods[] = array(
				'order_id'	=> 1,
				'gift_id'	=> $value,
				'gift_name'	=> $gift_detail['gift_name'],
				'amount'	=> $gift_num[$key],
				'price'		=> $gift_detail['credits'],
				'gift_pic'	=> $gift_detail['gift_photo'],
			);
			
			$total_credits += $gift_detail['credits']*$gift_num[$key];
		}
		$user_id = $_POST['user_id'];
		$user_name = $_POST['username'];
		//生成订单基本信息
		$order = array(
			'order_no'	=>  $order_no,
			'order_status'	=> 0,
			'user_id'		=> $user_id,
			'username'		=> $user_name,
			//'address'		=> $_POST['address'],
			//'consignee'		=> $_POST['receiver'],
			//'mobile'		=> $_POST['mobile'],
			'address_id'	=> $_POST['address_select'],
			'total_credits'	=> $total_credits,
			'create_time'	=> date('Y-m-d H:i:s', time())
		);
		
		//print_r($order);print_r($order_goods);die;
		$res = $obj_order->create_order($order, $order_goods);
		if($res)
		{
			header('Location: order.php?do=ordersuccess&order_no='.$order_no.'&user_id='.$user_id);
		}
		else
		{
			echo "failed";
		}
	}
	
	//订单成功页
	public function doOrderSuccess()
	{
		$order_no = $_GET['order_no'];
		$user_id = $_GET['user_id'];
		
		//获取会员积分余额
		importModule("userInfo","class");
		$obj_user = new userInfo;
		$left_credits = $obj_user->get_user_credits($user_id);
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		$data = $obj_order->get_order_info($order_no);
		
		$info = "";
		foreach($data as $key => $val)
		{
			$info .= $val['gift_name']."，数量".$val['amount']."，";
		}
		
		$order_info = array(
			'create_time'	=> $data[0]['create_time'],
			'order_no'		=> $data[0]['order_no'],
			'total_credits'	=> $data[0]['total_credits'],
			'info'			=> $info,
			'left_credits'	=> $left_credits
		);

		//获取订单信息
		$page = $this->app->page();
		
		$page->value('info', $order_info);
		$page->value('user',$_SESSION);
		$page->value('user_id',$user_id);
		$page->value('used_credits',$data[0]['total_credits']);
		$page->value('left_credits',$left_credits);
		$page->params['template'] = 'order_success.php';
		$page->output();
	}
	
	public function doMyOrder()
	{
		$order_no = $_GET['order_no'];
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		$data = $obj_order->get_order_info($order_no);
		
		if(!empty($data[0]['address_id']))
		{
			$address_info = $obj_order->get_order_address($data[0]['address_id']);
		}
		$data[0]['consignee'] = !empty($address_info['receiver']) ? $address_info['receiver'] : '';
		$data[0]['province'] = !empty($address_info['province_name']) ? $address_info['province_name'] : '';
		$data[0]['city'] = !empty($address_info['city_name']) ? $address_info['city_name'] : '';
		$data[0]['district'] = !empty($address_info['district_name']) ? $address_info['district_name'] : '';
		$data[0]['mobile'] = !empty($address_info['mobile']) ? $address_info['mobile'] : '';
		//print_r($data);die;
		$address = $data[0]['province']." ".$data[0]['city']." ".$data[0]['district']." ".$address_info['address'];
		$page = $this->app->page();
		$page->value('list', $data);
		$page->value('address',$address);
		$page->value('consignee',isset($data[0]['consignee']) ? $data[0]['consignee'] : '');
		$page->value('mobile',isset($data[0]['mobile']) ? $data[0]['mobile'] : '');
		$page->value('create_time',isset($data[0]['create_time']) ? $data[0]['create_time'] : '');
		$page->value('send_time',isset($data[0]['send_time']) ? '已发货' : '待发货');
		$page->params['template'] = 'myorder.php';
		$page->output();
	}
	
	public function doAddNewAddress()
	{
		//获取会员信息
		$user_id = !empty($_GET['user_id']) ? intval($_GET['user_id']) : 0;
		$order_id = !empty($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		$act		= !empty($_GET['act']) ? trim($_GET['act']) : '';
		
		importModule("userInfo","class");
		$obj_user = new userInfo;
		$user = $obj_user->get_user_detail($user_id);

		importModule("AreaInfo","class");
		$obj_area = new AreaInfo;
		$province = $obj_area->get_province();
		
		$page = $this->app->page();
		$page->value('user',$user[0]);
		$page->value('province',$province);
		$page->value('order_id',$order_id);
		$page->value('act',$act);
		$page->params['template'] = 'new_address.php';
		$page->output();
	}
	
	public function doSaveAddress()
	{
		//获取会员信息
		$user_id 	= !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
		$receiver 	= !empty($_POST['receiver']) ? trim($_POST['receiver']) : '';
		$mobile		= !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
		$province_id= !empty($_POST['province']) ? intval($_POST['province']) : '';
		$city_id	= !empty($_POST['city']) ? intval($_POST['city']) : '';
		$district_id= !empty($_POST['district']) ? intval($_POST['district']) : '';
		$address	= !empty($_POST['address']) ? trim($_POST['address']) : '';
		$order_id	= !empty($_POST['order_id']) ? intval($_POST['order_id']) : '';
		$act		= !empty($_POST['act']) ? trim($_POST['act']) : '';
		
		importModule("userInfo","class");
		$obj_user = new userInfo;
		$user = $obj_user->get_user_detail($user_id);

		importModule("AreaInfo","class");
		$obj_area = new AreaInfo;
		$province = $obj_area->get_province();
		
		$data = array(
			'user_id'	=> $user_id,
			'receiver'	=> $receiver,
			'mobile'	=> $mobile,
			'province'	=> $province_id,
			'city'		=> $city_id,
			'district'	=> $district_id,
			'address'	=> $address
		);

		$res = $obj_user->insert_address($data);
		if($res)
		{
			$url = "area.php?do=addresslist&user_id=".$user_id."&act=".$act."&order_id=".$order_id;
			//header('Location: order.php?do=orderconfirm&id='.$order_id.'&user_id='.$user_id);
			header('Location:'.$url);
		}
		else
		{
			echo "failed";
		}

	}
	
	//我的订单列表
	public function doOrderList()
	{	
		$data = array();
		$user_id = $_GET['user_id'];
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		$data = $obj_order->get_my_order($user_id);
		
		if($data)
		{
			foreach($data as $key => $v)
			{
				if($v['order_status'] == 0)
				{
					$status_name = '待确认';
				}	
				elseif($v['order_status'] == 1)
				{
					$status_name = '待发货';
				}
				elseif($v['order_status'] == 2)
				{
					$status_name = '已发货';
				}
				else
				{
					$status_name = '未知';
				}
				$data[$key]['order_status_name'] = $status_name;
			}
		}

		$page = $this->app->page();
		$page->value('user',$user_id);
		$page->value('list',$data);
		$page->params['template'] = 'order_list.php';
		$page->output();
	}
	
	
}
$app->run();
	
?>
