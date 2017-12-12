<?php 
/**
 * 订单
 */
require_once('./common.inc.php');

class customer extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		$page = $this->app->page();
		$page->params['template'] = 'customer_menu.html';
		$page->output();
	}
	
	//客户订单
	public function doCustomerOrderIndex()
	{
		$user 		= !empty($_POST['customer']) ? trim($_POST['customer']) : '';
		$date		= !empty($_POST['confirm_time']) ? trim($_POST['confirm_time']) : date("Y-m-d",strtotime("+1 day"));
		$user_id	= !empty($_POST['operater_id']) ? intval($_POST['operater_id']) : 0;
		$act		= !empty($_POST['act']) ? intval($_POST['act']) : 0;
		$s_order_no	= !empty($_POST['order_no']) ? trim($_POST['order_no']) : '';
		$s_order_id	= !empty($_POST['order_id']) ? intval($_POST['order_id']) : 0;
		$order_day_id = !empty($_POST['order_day_id']) ? intval($_POST['order_day_id']) : 1;
		$order_role_id= !empty($_POST['order_role_id']) ? intval($_POST['order_role_id']) : '';
		$order_user_id= !empty($_POST['order_user_id']) ? intval($_POST['order_user_id']) : '';
		
		$param = array(
			'customer'		=> $user,
			'order_time'	=> $date,
			'customer_id'	=> $user_id,
			'order_no'		=> $s_order_no,
			'order_id'		=> $s_order_id,
			'order_day_id'	=> $order_day_id,
		);
		
		//获取所有订单号
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		//$order_no = $obj_order->get_all_order_no();
		$order_id_set = $obj_order->get_all_order_id();
		$list = array();
		$general = array();

		if($act == 1)
		{
			//新建		
			//生成新订单号
			$order_no = date("YmdHis").rand(1,9);
			$order_no = substr($order_no, 2);
			$general['order_id'] = '';
			$general['order_id_no'] = 1;
			$general['customer'] = $user;
			$general['date'] = $date;
			$general['send_time'] = $date;
			$general['update_time'] = $date;
			$general['processing_time'] = $date;
				
		}
		else
		{
			if(!empty($s_order_id))
			{
				$param['order_id'] = $s_order_id;
			}
			elseif(!empty($order_id_set))
			{
				$param['order_id'] = $order_id_set[0]['order_id'];
				$s_order_id = $param['order_id'];
			}
			//print_r($param);
			//查询客户订单
			if($param['customer_id'] != 0)
			{
				$list = $obj_order->get_sales_order($param);
			
				$general = array(
					'order_no'		=> $list[0]['order_no'],
					'order_id'		=> $list[0]['order_id'],
					'order_id_no'	=> 1,
					'operator'		=> $list[0]['operator'],
					'date'			=> $date,
					'send_no'		=> $list[0]['send_no'],
					'pay_type'		=> $list[0]['pay_type'],
					'send_time'		=> $list[0]['send_time'],
					'update_time'	=> $list[0]['processing_time'],
					'processing_time'=> $list[0]['jg_date'],
					'customer'		=> $user
				);
			}
			else
			{
				$list = array();
				$general = array();
			}
			
			
		}
		
		$user_order_id_set = range(1,10);

		$page = $this->app->page();
		$page->value('param',$param);
		$page->value('list',$list);
		$page->value('act',$act);
		$page->value('order_no',$order_no);
		$page->value('s_order_no',$s_order_no);
		$page->value('order_id_set',$order_id_set);
		$page->value('s_order_id',$s_order_id);
		$page->value('order_day_id',$order_day_id);
		$page->value('user_order',$user_order_id_set);
		$page->value('general',$general);
		$page->value('order_user_id',$order_user_id);
		$page->value('order_role_id',$order_role_id);
		$page->params['template'] = 'customer_order_index.html';
		$page->output();
	}
	
	//加入购物清单
	public function doAddBuyList()
	{
		$operator_id 	= $_SESSION['user_id'];
		$operator 		= $_SESSION['username'];
		$truck_id		= $_SESSION['truck'];
		$role_id 		= !empty($_SESSION['offer_role']) ? $_SESSION['offer_role'] : $_SESSION['role_id'];
		$ids 			= !empty($_REQUEST['ids']) ? trim($_REQUEST['ids']) : '';
		$id_set 		= explode(",", $ids);
		
		if (empty($role_id))
		{
			$return = array(
				'statusCode'	=> 0,
				'message'		=> '用户信息有误！请退出重试',
				'navTabId'		=> 'pagination1',
				'rel'			=> '',
				'callbackType'	=> '',
				'forwardUrl'	=> 'closeCurrent',
				'confirmMsg'	=> ''
			);
			exit(json_encode($return));
		}
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		//检查是否待处理的订单
		$order = $obj_order->get_unconfirm_order($operator_id);
		if($order)
		{
			//添加到购物商品表
			$order_goods = array();
			$total_num = 0;
			$total_amount = 0;
			foreach($id_set as $key => $val)
			{
				//获取商品实际价格
				$offer_price = $obj_good->get_role_good_price($role_id, $val);
				if(empty($offer_price))
				{
					$offer_price = $obj_good->get_good_price($val);
				}
				
				$order_goods[$key] = array(
					'goods_id'	=> $val,
					'goods_num'	=> 1,
					'order_id'	=> $order['order_id'],
					'good_price'	=> $offer_price
				);
				
				$total_num++;
				$total_amount += $offer_price;
			}
			$_order = array(
				'total_amount'	=> number_format($order['total_amount']+$total_amount, 2, '.', ''),
				'total_num'		=> $order['total_num']+$total_num,
				'order_id'		=> $order['order_id']
			);
			//更新订单
			$res = $obj_order->add_new_order_goods($_order, $order_goods);
		}
		else
		{
			$order_no = date("YmdHis").rand(1000,9999);
			//订单总览
			$order = array(
				'order_no'		=> $order_no,
				'operator_id'	=> $operator_id,
				'operator'		=> $operator,
				'order_status'	=> 0,
				'order_role_id'	=> $role_id,
				'order_truck_id'=> $truck_id
			);
			
			$order_goods = array();
			$total_num = 0;
			$total_amount = 0;
			foreach($id_set as $key => $val)
			{
				//获取商品
				//$price = $obj_good->get_good_price($val);
				//获取商品实际价格
				$offer_price = $obj_good->get_role_good_price($role_id, $val);
				if(empty($offer_price))
				{
					$offer_price = $obj_good->get_good_price($val);
				}
				$order_goods[$key] = array(
					'goods_id'	=> $val,
					'goods_num'	=> 1,
					'good_price'	=> $offer_price
				);
				$total_num++;
				$total_amount += $offer_price;
			}
			
			$order['total_num'] = $total_num;
			$order['total_amount'] = $total_amount;
			//print_r($order);print_r($order_goods);die;
			$res = $obj_order->add_order($order, $order_goods);
		}
		
		if($res)
		{
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '添加成功，请到我的订单确认！',
				'navTabId'		=> 'pagination1',
				'rel'			=> '',
				'callbackType'	=> '',
				'forwardUrl'	=> 'closeCurrent',
				'confirmMsg'	=> ''
			);
		}
		else
		{
			$return = array(
				'statusCode'	=> 0,
				'message'		=> '添加失败，请重新添加',
				'navTabId'		=> 'pagination1',
				'rel'			=> '',
				'callbackType'	=> '',
				'forwardUrl'	=> 'closeCurrent',
				'confirmMsg'	=> ''
			);
		}
		
		exit(json_encode($return));
		
	}
	
	public function doCustomerOrder(){
		$current_page 	= !empty($_REQUEST['pageNum']) ? intval($_REQUEST['pageNum']) : 1;
		$page_size		= !empty($_REQUEST['numPerPage']) ? intval($_REQUEST['numPerPage']) : 10;
		$order_status	= !empty($_REQUEST['order_status']) ? intval($_REQUEST['order_status']) : -1;//默认确认
		$operator		= !empty($_REQUEST['operator']) ? trim($_REQUEST['operator']) : '';//下单人
		$start_time		= !empty($_REQUEST['start_time']) ? trim($_REQUEST['start_time']) : '';//下单开始时间
		$end_time		= !empty($_REQUEST['end_time']) ? trim($_REQUEST['end_time']) : '';//下单结束时间
		
		//所有订单
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		$param = array(
			'page'			=> $current_page,
			'page_size'		=> $page_size,
			'order_status'	=> $order_status,
			'operator'		=> $operator,
			'start_time'	=> $start_time,
			'end_time'		=> $end_time
		);	
		$order_list = $obj_order->get_order_list($param); 
		/*
		$page_num = ceil($order_list['count']/$page_size);
		$page_info = array(
			'total'		=> $order_list['count'],
			'page_num'	=> $page_num,
			'page_size'	=> $page_size,
			'current_page'=>$current_page
		);*/

		$page = $this->app->page();
		$page->value('list',$order_list['list']);
		$page->value('total',$order_list['count']);
		//$page->value('page',$page_info);
		$page->value('param',$param);
		$page->params['template'] = 'customer_order.html';
		$page->output();
	}
	
	//获取订单商品
	public function doOrderGoods()
	{
		$order_id 	= !empty($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		
		$order_goods = $obj_order->get_order_goods($order_id);
		$page = $this->app->page();
		$page->value('order_goods',$order_goods);
		$page->params['template'] = 'order_goods_detail.html';
		$page->output();
	}
	
	//获取销售清单
	public function doSalesOrder()
	{
		$order_id	= !empty($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		//获取订单总览
		$general = $obj_order->get_general_info($order_id);

		//获取商品信息
		$order_goods = $obj_order->get_order_goods($order_id);
		//print_r($order_goods);die;
		//权限处理
		$user_type  = !empty($_SESSION['type']) ? intval($_SESSION['type']) : 0;//1管理员 0普通会员
		//获取订单
		$order_time = !empty($general['confirm_time']) ? strtotime($general['confirm_time']) : time();//订单确认时间
		if($user_type == 1)
		{
			$permission = 1;
		}
		else
		{
			if(time()-$order_time > 8*3600)
			{
				$permission = 0;
			}
			else
			{
				$permission = 2;
			}
		}
		
		$page = $this->app->page();
		$page->value('order_goods',$order_goods);
		$page->value('general',$general);
		$page->value('user_type',$_SESSION['type']);
		$page->value('permission',$permission);//0 无权限  1有权限
		$page->params['template'] = 'sales_detail.html';
		$page->output();
	}
	
	
	//修改订单状态
	public function doUpdateOrderStatus()
	{
		$order_id 		= !empty($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;
		$order_status 	= !empty($_REQUEST['order_status']) ? intval($_REQUEST['order_status']) : 0;
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		
		$res = $obj_order->update_order_status($order_id, $order_status);
		
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '成功,请刷新',
				'navTabId'		=> 'pagination1',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		else
		{
			//失败
			$return = array(
				'statusCode'	=> 0,
				'message'		=> '失败,请刷新',
				'navTabId'		=> 'pagination1',
				'rel'			=> '',
				'callbackType'	=> '',
				'forwardUrl'	=> 'closeCurrent',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
	}
	
	
	//获取分车订单信息
	public function doTruckOrder()
	{
		$current_page 	= !empty($_REQUEST['pageNum']) ? intval($_REQUEST['pageNum']) : 1;
		$page_size		= !empty($_REQUEST['numPerPage']) ? intval($_REQUEST['numPerPage']) : 10;
		$truck_id		= !empty($_REQUEST['truck_id']) ? intval($_REQUEST['truck_id']) : 0;
	
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;

		$param = array(
			'page'			=> $current_page,
			'page_size'		=> $page_size,
			'truck_id'		=> $truck_id,
		);	
		$order_list = $obj_order->get_truck_order_list($param);
		print_r($order_list);die;
		/*
		$page_num = ceil($order_list['count']/$page_size);
		$page_info = array(
			'total'		=> $order_list['count'],
			'page_num'	=> $page_num,
			'page_size'	=> $page_size,
			'current_page'=>$current_page
		);*/
		
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		$trucks = $obj_truck->get_all_trucks();
		
		$page = $this->app->page();
		$page->value('list',$order_list['list']);
		$page->value('total',$order_list['count']);
		//$page->value('page',$page_info);
		$page->value('param',$param);
		$page->value('trucks',$trucks);
		$page->params['template'] = 'truck_order_list.html';
		$page->output();
		
	}
	
	
	//获取货车订单
	public function doOrderTruck()
	{
		$send_no = !empty($_REQUEST['send_no']) ? trim($_REQUEST['send_no']) : '';
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		
		$res_order = $obj_order->get_truck_orders($send_no);//订单
		$order_no_set = array();
		foreach($res_order as $key => $val)
		{
			array_push($order_no_set, $val['order_no']);
		}

		//订单商品
		$res = $obj_order->get_truck_goods($send_no);
		
		$return = $this->order_manage($res);
		
		$return['order'] = implode(',', $order_no_set);
		
		$page = $this->app->page();
		$page->value('list',$return);
		$page->params['template'] = 'truck_order_goods.html';
		$page->output();
	}
	
	
	//订单统计处理
	private function order_manage($data)
	{
		if(empty($data))
		{
			return array();
		}
		$info = array();
		$info = array(
			'truck_name' 	=>  $data[0]['truck_name'],
			'create_time'	=>  $data[0]['create_time']
		);
		
		$goods_ids = array();
		foreach($data as $key => $val)
		{
			array_push($goods_ids, $val['goods_id']);
		}
		
		array_unique($goods_ids);
		
		$temp = array();
		foreach($goods_ids as $k => $v)
		{
			if(!empty($v))
			{
				$temp[$v]['goods_num'] = 0;
				$temp[$v]['total_amount'] = 0;
				foreach($data as $key => $val)
				{
					if($v == $val['goods_id'])
					{
						$temp[$v]['goods_name'] = $val['goods_name'];
						$temp[$v]['good_price'] = $val['good_price'];
						$temp[$v]['unit'] = $val['unit'];
						$temp[$v]['goods_num'] += $val['goods_num'];
						$temp[$v]['total_amount'] += ($val['goods_num']*$val['good_price']);
					}
				}
			}
			
			
		}
		
		$info['goods'] = $temp;
		
		return $info;
		
	}
	
	
	//产品统计
	public function doStatisticsGoods()
	{
		$current_page 	= !empty($_REQUEST['pageNum']) ? intval($_REQUEST['pageNum']) : 1;
		$page_size		= !empty($_REQUEST['numPerPage']) ? intval($_REQUEST['numPerPage']) : 10;
		$order_status	= !empty($_REQUEST['order_status']) ? intval($_REQUEST['order_status']) : 1;//默认确认
		$operator		= !empty($_REQUEST['operator']) ? trim($_REQUEST['operator']) : '';//下单人
		$start_time		= !empty($_REQUEST['start_time']) ? trim($_REQUEST['start_time']) : '';//下单开始时间
		$end_time		= !empty($_REQUEST['end_time']) ? trim($_REQUEST['end_time']) : '';//下单结束时间
		
		//所有订单
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		$param = array(
			'page'			=> $current_page,
			'page_size'		=> $page_size,
			'order_status'	=> $order_status,
			'operator'		=> $operator,
			'start_time'	=> $start_time,
			'end_time'		=> $end_time
		);	

		$order_list = $obj_order->get_order_list($param); 

		$page_num = ceil($order_list['count']/$page_size);
		$page_info = array(
			'total'		=> $order_list['count'],
			'page_num'	=> $page_num,
			'page_size'	=> $page_size,
			'current_page'=>$current_page
		);

		$page = $this->app->page();
		$page->value('list',$order_list['list']);
		$page->value('total',$order_list['count']);
		$page->value('page',$page_info);
		$page->value('param',$param);
		$page->params['template'] = 'goods_statistic.html';
		$page->output();
	}
	
	//修改订单
	public function doUpdateOrder()
	{
		$order_id 	= !empty($_GET['order_id']) ? $_GET['order_id'] : 0;
		$send_no	= !empty($_GET['send_no']) ? $_GET['send_no'] : '';
		$update_time= !empty($_GET['update_time']) ? $_GET['update_time'] : '';
		$send_time= !empty($_GET['send_time']) ? $_GET['send_time'] : '';
		$pay_type	= !empty($_GET['pay_type']) ? intval($_GET['pay_type']) : 1;
		$processing_time = !empty($_GET['processing_time']) ? $_GET['processing_time'] : '';
		
		$order_title = array(
			'order_id'		=> $order_id,
			'send_no'		=> $send_no,
			'update_time'	=> $update_time,
			'pay_type'		=> $pay_type,
			'send_time'		=> $send_time,
			'processing_time'	=> $processing_time
		);
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		$items = array();
		$goods_list = $_GET['goods_list'];
		$goods_list_arr = json_decode($goods_list, true);
		//更新销售单
		if(!empty($goods_list_arr))
		{
			foreach($goods_list_arr as $key => $val)
			{
				$goods = explode(':', $val);
				if(!empty($goods[1]))
				{
					//获取报价
					$role_id = $_SESSION['role_id'];
					$offer_price = $obj_good->get_role_good_price($role_id, $goods[0]);

					if(empty($offer_price))
					{
						$offer_price = $obj_good->get_good_price($goods[0]);
					}

					//更新订单
					$items[] = array(
						'goods_id'		=> $goods[0],
						'goods_num'		=> $goods[1],
						'send_num'		=> $goods[2],
						'received_num'	=> $goods[3],
						'good_note'		=> $goods[4],
						'good_price'	=> $offer_price
					);

				}
				else
				{
					//获取原数据
					$order_goods = $obj_order->get_order_goods_detail($order_id, $goods[0]);
					$items[] = array(
						'goods_id'		=> $order_goods['goods_id'],
						'goods_num'		=> $order_goods['goods_num'],
						'send_num'		=> $order_goods['send_num'],
						'received_num'	=> $order_goods['received_num'],
						'good_note'		=> $order_goods['good_note'],
						'good_price'	=> $order_goods['good_price']
					);
				}
			}
		}

		$res = $obj_order->replace_order($order_title, $items);
		
		if($res)
		{
			exit(json_encode(array('status'=>true, 'msg'=>'更新成功', 'data'=>$items)));
		}
		else
		{
			exit(json_encode(array('status'=>true, 'msg'=>'更新失败', 'data'=>$items)));
		}
		
	}
	
	//修改订单商品发送状态
	public function doUpdateGoodsSendStatus()
	{
		$order_id 	= !empty($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		$goods_id	= !empty($_GET['goods_id']) ? intval($_GET['goods_id']) : 0;
		$send_status= !empty($_GET['send_status']) ? intval($_GET['send_status']) : 0;
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		
		$res = $obj_order->update_send_status($order_id, $goods_id, $send_status);

		if($res)
		{
			$data = array(
				'status' => true,
				'msg'	 => '状态更新成功'
			);
		}
		else
		{
			$data = array(
				'status' => false,
				'msg'	 => '状态更新失败'
			);
		}
		
		exit(json_encode($data));
	}
	
	//获取送货清单
	public function doSendList()
	{
		$order_id	= !empty($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		//获取订单总览
		$general = $obj_order->get_general_info($order_id);
		//整理总览信息
		//获取地址
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$user_detail = $obj_user->get_role_users($general['order_role_id']);
		$general['address'] = $user_detail[0]['address'];
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		$role_info = $obj_role->get_role($general['order_role_id']);
		$general['role_name'] = $role_info['role_name'];
		//获取商品信息
		$total_amount = 0;
		$order_goods = $obj_order->get_order_goods($order_id);
		if($order_goods)
		{
			foreach($order_goods as $key => $val)
			{
				$order_goods[$key]['good_price'] = sprintf("%1\$.2f", $val['good_price']);
				$total_amount += $val['good_price']*$val['goods_num'];
				$order_goods[$key]['amount'] = $val['price']*$val['goods_num'];
				$order_goods[$key]['amount'] = sprintf("%1\$.2f", $order_goods[$key]['amount']);
				$order_goods[$key]['check_amount'] = $val['good_price']*$val['received_num'];
			}
		}
		$general['total_amount'] = $total_amount;
		//print_r($general);die;
		//print_r($order_goods);die;
		
		$page = $this->app->page();
		$page->value('order_goods',$order_goods);
		$page->value('general',$general);
		$page->params['template'] = 'send_detail.html';
		$page->output();
	}
	
}
$app->run();
	
?>
