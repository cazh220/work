<?php 
/**
 * 订单
 */
require_once('./common.inc.php');

class order extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		$page = $this->app->page();
		$page->params['template'] = 'order_menu.html';
		$page->output();
	}
	
	//加入购物清单
	public function doAddBuyList()
	{
		if(isset($_COOKIE['last_sess_id']))
		{
			$sess_id = $_COOKIE['last_sess_id'];
		}
		else
		{
			$sess_id = $_SESSION['sess_id'];
		}
		//$sess_id		= $_SESSION['sess_id'];
		$operator_id 	= $_SESSION[$sess_id]['user_id'];
		$operator 		= $_SESSION[$sess_id]['username'];
		$truck_id		= $_SESSION[$sess_id]['truck'];
		$role_id 		= !empty($_SESSION[$sess_id]['offer_role']) ? $_SESSION[$sess_id]['offer_role'] : $_SESSION[$sess_id]['role_id'];
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
		$search			= !empty($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
		$role_id		= !empty($_REQUEST['role_id']) ? intval($_REQUEST['role_id']) : '';
		
		if(empty($search))
		{
			$start_time = date("Y-m-d", strtotime("+1 days"))." 00:00:00";
			$end_time   = date("Y-m-d", strtotime("+1 days"))." 23:59:59";
		}
		
		//所有订单
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		$param = array(
			'page'			=> $current_page,
			'page_size'		=> $page_size,
			'order_status'	=> $order_status,
			'operator'		=> $operator,
			'start_time'	=> $start_time,
			'end_time'		=> $end_time,
			'role_id'		=> $role_id
		);	
		//print_r($param);
		$order_list = $obj_order->get_order_list($param); 
		/*
		$page_num = ceil($order_list['count']/$page_size);
		$page_info = array(
			'total'		=> $order_list['count'],
			'page_num'	=> $page_num,
			'page_size'	=> $page_size,
			'current_page'=>$current_page
		);*/
		$total_amount = 0;
		if(!empty($order_list['list']))
		{
			foreach($order_list['list'] as $key => $val)
			{
				$total_amount += $val['total_amount'];
			}
		}
		
		$order_list['total_amount'] = $total_amount;
		
		
		//获取客户分类
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		$role_list = $obj_role->get_all_roles(array(), 0);
		
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		
		import('util.RoleShow');
		$role_show = RoleShow::role_show($role_list);
		//print_r($role_show);
		//获取分类和客户
		if(!empty($role_show))
		{
			foreach($role_show as $key => $val)
			{
				$users = array();
				$users = $obj_user->get_role_users($val['role_id']);
				$role_show[$key]['users'] = !empty($users) ? $users : array();
				if(!empty($val['child']))
				{
					foreach($val['child'] as $k => $v)
					{
						$users_c = array();
						$users_c = $obj_user->get_role_users($v['role_id']);
						$role_show[$key]['child'][$k]['users'] = !empty($users_c) ? $users_c : array();
						if(!empty($v['child']))
						{
							foreach($v['child'] as $kk => $vv)
							{
								$users_cc = array();
								$users_cc = $obj_user->get_role_users($vv['role_id']);
								$role_show[$key]['child'][$k]['child'][$kk]['users'] = !empty($users_cc) ? $users_cc : array();
							}
						}
					}
				} 
			}
		}

		$page = $this->app->page();
		$page->value('list',$order_list['list']);
		$page->value('total',$order_list['count']);
		$page->value('total_amount',$order_list['total_amount']);
		//$page->value('page',$page_info);
		$page->value('role_show',$role_show);
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
		
		//销售金额格式化
		if($order_goods)
		{
			foreach($order_goods as $key => $val)
			{
				$order_goods[$key]['sales_amount'] = number_format($val['good_price']*$val['received_num'], 2, ".", "");
				//差额
				$diff_amount = $val['good_price']*($val['goods_num']-$val['received_num']);
				$order_goods[$key]['diff_amount'] = number_format($diff_amount, 2, ".", "");
			}
		}
		

		//权限处理
		if(isset($_COOKIE['last_sess_id']))
		{
			$sess_id = $_COOKIE['last_sess_id'];
		}
		else
		{
			$sess_id = $_SESSION['sess_id'];
		}
		$user_type  = !empty($_SESSION[$sess_id]['type']) ? intval($_SESSION[$sess_id]['type']) : 0;//1管理员 0普通会员
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
		$page->value('user_type',$_SESSION[$sess_id]['type']);
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
		//print_r($order_list);die;
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
			'order_id'			=> $order_id,
			'send_no'			=> $send_no,
			'update_time'		=> $update_time,
			'pay_type'			=> $pay_type,
			'send_time'			=> $send_time,
			'processing_time'	=> $processing_time,
			'order_time'		=> $processing_time,
			'update_time'		=> date("Y-m-d H:i:s", time())
		);
		//$sess_id		= $_SESSION['sess_id'];
		if(isset($_COOKIE['last_sess_id']))
		{
			$sess_id = $_COOKIE['last_sess_id'];
		}
		else
		{
			$sess_id = $_SESSION['sess_id'];
		}
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
					$role_id = $_SESSION[$sess_id]['role_id'];
					$offer_price = $obj_good->get_role_good_price($role_id, $goods[0]);

					if(empty($offer_price))
					{
						$offer_price = $obj_good->get_good_price($goods[0]);
					}
					
					$offer_price = !empty($goods[6]) ? $goods[6] : '';

					//更新订单
					$items[] = array(
						'goods_id'		=> $goods[0],
						'goods_num'		=> $goods[1],
						'send_num'		=> $goods[2],
						'received_num'	=> $goods[3],
						'good_note'		=> $goods[4],
						'n_good_note'	=> $goods[5],
						'good_price'	=> $offer_price,
						'send_status'	=> $goods[7],
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
						'n_good_note'	=> $order_goods['n_good_note'],
						'good_price'	=> $order_goods['good_price'],
						'send_status'	=> $order_goods['send_status'],
					);
				}
			}
		}
		//print_r($order_title);print_r($items);die;
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
		
		if($send_status)
		{
			$send_status_name = '已送';
		}
		else
		{
			$send_status_name = '未送';
		}
		
		if($res)
		{
			$data = array(
				'status' => true,
				'msg'	 => '状态更新为：'.$send_status_name
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
		$order_goods = array();
		$order_goods = $obj_order->get_order_goods($order_id);
		
		$rows = 60;
		
		if($order_goods)
		{
			if(count($order_goods) < $rows)
			{
				foreach($order_goods as $key => $val)
				{
					$order_goods[$key]['goods_num'] = $val['goods_num'] ? $val['goods_num'] : '';
					$order_goods[$key]['good_price'] = $val['good_price'] ? number_format($val['good_price'], 2, ".", "") : '';
					$total_amount += $val['good_price']*$val['goods_num'];
					$order_goods[$key]['amount'] = $val['price']*$val['goods_num'];
					$order_goods[$key]['amount'] = $order_goods[$key]['amount'] ? number_format($order_goods[$key]['amount'], 2, ".", "") : '';
					$order_goods[$key]['check_amount'] = $val['good_price']*$val['received_num'];
				}
				$general['total_amount'] = $total_amount ? number_format($total_amount, 2, ".", "") : 0;
			}
			else
			{
				$order_goods_first = array();
				foreach($order_goods as $key => $val)
				{
					if($key < $rows)
					{
						$order_goods_first[] = $val;
					}
				}
				
				foreach($order_goods_first as $key => $val)
				{
					$order_goods_first[$key]['goods_num'] = $val['goods_num'] ? $val['goods_num'] : '';
					$order_goods_first[$key]['good_price'] = $val['good_price'] ? number_format($val['good_price'], 2, ".", "") : '';
					$total_amount += $val['good_price']*$val['goods_num'];
					$order_goods_first[$key]['amount'] = $val['price']*$val['goods_num'];
					$order_goods_first[$key]['amount'] = $order_goods_first[$key]['amount'] ? number_format($order_goods_first[$key]['amount'], 2, ".", "") : '';
					$order_goods_first[$key]['check_amount'] = $val['good_price']*$val['received_num'];
				}
				
				$general['total_amount'] = $total_amount ? number_format($total_amount, 2, ".", "") : 0;
				
				$first = array('page_head'=>$general, 'list'	=> $order_goods_first);
				
				$order_goods_second = array();
				foreach($order_goods as $key => $val)
				{
					if($key >= $rows && $key < $rows + $rows)
					{
						$order_goods_second[] = $val;
					}
				}
				
				$total_amount = 0;
				foreach($order_goods_second as $keys => $vals)
				{
					$order_goods_second[$keys]['goods_num'] = $vals['goods_num'] ? $vals['goods_num'] : '';
					$order_goods_second[$keys]['good_price'] = $vals['good_price'] ? number_format($vals['good_price'], 2, ".", "") : '';
					$total_amount += $vals['good_price']*$vals['goods_num'];
					$order_goods_second[$keys]['amount'] = $vals['price']*$vals['goods_num'];
					$order_goods_second[$keys]['amount'] = $order_goods_second[$keys]['amount'] ? number_format($order_goods_second[$keys]['amount'], 2, ".", "") : '';
					$order_goods_second[$keys]['check_amount'] = $vals['good_price']*$vals['received_num'];
				}
				
				$general['total_amount'] = $total_amount ? number_format($total_amount, 2, ".", "") : 0;
				
				$second = array('page_head'=>$general, 'list'	=> $order_goods_second);
				
				$order_goods = array();
				$order_goods = array_merge(array($first), array($second));
			}
		}
//print_r($order_goods);die;

		$page = $this->app->page();
		$page->value('order_goods',$order_goods);
		$page->value('general',$general);
		$page->params['template'] = 'send_detail.html';
		$page->output();
	}
	
	//获取客户明细模式打印
	public function doCustomerBuy()
	{
		//获取日期
		$confirm_time = !empty($_REQUEST['confirm_time']) ? trim($_REQUEST['confirm_time']) : date("Y-m-d", strtotime("+1 days"));//当前时间
		$category_id  = !empty($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : 0;
		$order_role_id	= !empty($_REQUEST['order_role_id']) ? intval($_REQUEST['order_role_id']) : 0;
		$user_id		= !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		
		importModule("StatisticsInfo","class");
		$obj_statistics = new StatisticsInfo;
		
		$data = $obj_statistics->get_day_unsend_goods($confirm_time, $category_id, $order_role_id, 0, $user_id);
		
		$goods = array();
		if($data)
		{
			foreach($data as $key => $val)
			{
				$goods[$val['goods_id']][] = $val;
			}
		}
		
		$customer_goods = array();
		
		$data = array();
		if($goods)
		{
			foreach($goods as $kk => $vv)
			{
				if($vv)
				{
					$num = 0;
					$detail = '其中：';
					foreach($vv as $k => $v)
					{
						$num += $v['goods_num'];
						$detail .= "(".$v['order_user_name'].")".$v['goods_num'].$v['unit']."+";
					}
				}
				$data[$kk]['total'] = $num;
				$data[$kk]['detail'] = rtrim($detail, '+');
				$data[$kk]['list'] = $vv[0];
			}
		}
		
		
		//print_r($data);die;
		$page = $this->app->page();
		$page->value('data',$data);
		$page->params['template'] = 'customer_buy.html';
		$page->output();
	}
	
	//添加订单商品
	public function doAddGoods()
	{
		$category_id 	= !empty($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : 0;
		$goods_name		= !empty($_REQUEST['goods_name']) ? trim($_REQUEST['goods_name']) : '';
		$order_role_id	= !empty($_REQUEST['order_role_id']) ? intval($_REQUEST['order_role_id']) : '';
		$order_user_id	= !empty($_REQUEST['order_user_id']) ? intval($_REQUEST['order_user_id']) : '';
		
		if(empty($order_role_id) || empty($order_user_id))
		{
			echo "<script>alert('请选择客户新建或查询订单，方可添加商品');$.pdialog.closeCurrent();</script>";
			exit();
		}
	
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;

		$param = array(
			'category_id'	=> $category_id,
			'goods_name'	=> $goods_name
		);	

		//获取订单商品id
		$ids = array();
		if(!empty($order_id))
		{
			$info = $obj_order->get_order_goods($order_id);
			if($info)
			{
				foreach($info as $key => $val)
				{
					array_push($ids, $val['goods_id']);
				}
			}
			$param['ids'] = $ids;
		}
		
		$list = $obj_good->get_goods_list($param);
		if($list['list'])
		{
			//获取商品报价
			importModule("OfferInfo","class");
			$obj_offer = new OfferInfo;
			foreach($list['list'] as $key => $value)
			{
				$role_price = $obj_offer->get_good_offer_price($value['goods_id'], $order_user_id, $order_role_id);
				$list['list'][$key]['price'] = $role_price ? $role_price : 0;
			}
		}

		$page = $this->app->page();
		$page->value('goods',$list['list']);
		$page->value('param',$param);
		$page->value('order_user_id',$order_user_id);
		$page->value('order_role_id',$order_role_id);
		$page->params['template'] = 'goods_template.html';
		$page->output();
	}
	
	//添加订单商品
	public function doAddGoodsAct()
	{
		$goods_id		= !empty($_GET['goods_id']) ? intval($_GET['goods_id']) : 0;
		$goods_num		= !empty($_GET['goods_num']) ? intval($_GET['goods_num']) : 0;
		$good_note		= !empty($_GET['good_note']) ? trim($_GET['good_note']) : '';
		$n_good_note	= !empty($_GET['n_good_note']) ? trim($_GET['n_good_note']) : '';
		$goods_price	= !empty($_GET['goods_price']) ? floatval($_GET['goods_price']) : '';
		
		$order_user_id	= !empty($_GET['order_user_id']) ? $_GET['order_user_id'] : '';
		$order_role_id	= !empty($_GET['order_role_id']) ? $_GET['order_role_id'] : '';
		//$unit			= !empty($_POST['unit']) ? trim($_POST['unit']) : '';
		//获取商品详情和报价
		$good = array();
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		$good = $obj_good->get_good_detail($goods_id);
		
		$good['goods_num']	= $goods_num;
		$good['good_note']	= $good_note;
		$good['n_good_note']= $n_good_note;
		if($good)
		{
			//获取报价
			importModule("OfferInfo","class");
			$obj_offer = new OfferInfo;
			$role_price = $obj_offer->get_good_offer_price($goods_id, $order_user_id, $order_role_id);
			$good['price']	= !empty($goods_price) ? $goods_price : $role_price;
				
			$sales = 0;
			$diff = 0;
			$sales = $good['price']*$good['goods_num'];
			$actual_sales = $good['price']*$good['received_num'];
			$diff = $sales-$actual_sales;
			
			$good['sales'] = $sales;
			$good['diff'] = $diff;
			exit(json_encode(array('status'=>1, 'list'=>$good)));
		}
		else
		{
			exit(json_encode(array('status'=>0, 'list'=>$good)));
		}
		
		//exit(json_encode($good));
		//echo '<a class="button" href="javascript:;" onclick="alertMsg.correct('您的数据提交成功！')"><span>成功提示</span></a>';
		//echo "<script>alert('xx');</script>";
	}
	
	//批量添加订单商品
	public function doAddGoodsBatchAct()
	{
		//print_r($_GET['goods_list']);die;
		$goods_list		= !empty($_GET['goods_list']) ? $_GET['goods_list'] : '';
		$order_user_id	= !empty($_GET['order_user_id']) ? $_GET['order_user_id'] : '';
		$order_role_id	= !empty($_GET['order_role_id']) ? $_GET['order_role_id'] : '';
		
		$goods = array();
		$goods_list_arr = json_decode($goods_list, true);
		if($goods_list_arr)
		{
			foreach($goods_list_arr as $key => $val)
			{
				$good = explode('@', $val);
				$goods[$key] = $good;
			}
		}
		//获取商品详情和报价
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
	
		//获取商品报价
		importModule("OfferInfo","class");
		$obj_offer = new OfferInfo;
		
		if($goods)
		{
			foreach($goods as $key => $val)
			{
				$good = $obj_good->get_good_detail($val[0]);
				//获取报价
				$role_price = $obj_offer->get_good_offer_price($val[0], $order_user_id, $order_role_id);
				$good['price']	=  !empty($val[4]) ? $val[4] : $role_price;

				$sales = 0;
				$diff = 0;
				$sales = $val[4]*$val[1];//$good['price']*$good['goods_num'];
				$sales = number_format($sales, 2, ".", "");
				$actual_sales = $good['price']*$good['received_num'];
				$diff = $sales-$actual_sales;
				
				$goods[$key] = $good;
				$goods[$key]['sales'] = $sales;
				$goods[$key]['diff'] = $diff;
				$goods[$key]['goods_num']	= $val[1];
				$goods[$key]['good_note']	= $val[2];
				$goods[$key]['n_good_note']	= $val[3];
			}
			
			exit(json_encode(array('status'=>1, 'list'=>$goods)));
		}
		else
		{
			exit(json_encode(array('status'=>0, 'list'=>$goods)));
		}
	}
	
	//判定订单序号
	public function doCheckOrderId()
	{
		$id 				= !empty($_GET['id']) ? intval($_GET['id']) : 0;
		$confirm_time		= !empty($_GET['confirm_time']) ? trim($_GET['confirm_time']) : '';
		$customer_id		= !empty($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
		
		$start_time = $confirm_time. " 00:00:00";
		$end_time	= $confirm_time. " 23:59:59";
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		
		$res = $obj_order->check_order_id($id, $start_time, $end_time, $customer_id);
		if($res)
		{
			exit(json_encode(array('status'=>true, 'message'=>'已存在此序号，请换一个')));
		}
		else
		{
			exit(json_encode(array('status'=>false, 'message'=>"")));
		}
		
	}
	
	
	//订单处理
	public function doManageOrder()
	{
		//$sess_id			= $_SESSION['sess_id'];
		if(isset($_COOKIE['last_sess_id']))
		{
			$sess_id = $_COOKIE['last_sess_id'];
		}
		else
		{
			$sess_id = $_SESSION['sess_id'];
		}
		$order_id 			= !empty($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		$send_no			= !empty($_GET['send_no']) ? trim($_GET['send_no']) : '';
		$update_time		= !empty($_GET['update_time']) ? trim($_GET['update_time']) : '';
		$send_time			= !empty($_GET['send_time']) ? trim($_GET['send_time']) : '';
		$pay_type			= !empty($_GET['pay_type']) ? intval($_GET['pay_type']) : 1;
		$processing_time 	= !empty($_GET['processing_time']) ? trim($_GET['processing_time']) : '';
		$customer_id		= !empty($_GET['customer_id']) ? intval($_GET['customer_id']) : '';
		$customer_name		= !empty($_GET['customer']) ? trim($_GET['customer']) : '';
		$order_day_id		= !empty($_GET['order_day_id']) ? intval($_GET['order_day_id']) : '';
		$operator_id 		= !empty($_SESSION[$sess_id]['user_id']) ? intval($_SESSION[$sess_id]['user_id']) : '';
		$operator 			= !empty($_SESSION[$sess_id]['username']) ? trim($_SESSION[$sess_id]['username']) : '';
		$operator_role_id	= !empty($_SESSION[$sess_id]['role_id']) ? intval($_SESSION[$sess_id]['role_id']) : '';
		//print_r($_GET);
		$items = array();
		if($update_time != $send_time || $update_time != $processing_time || $send_time != $processing_time )
		{
			exit(json_encode(array('status'=>false, 'msg'=>'送货日期、采购日期、发送日期不一致', 'data'=>$items)));
		}

		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;

		//获取用户角色和分车
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		$customer_role = $obj_role->get_user_role($customer_id);

		$truck_id					= !empty($customer_role['truck']) ? $customer_role['truck'] : 0;
		$order_role_id		 		= !empty($customer_role['role_id']) ? $customer_role['role_id'] : 0;//客户role_id
		$order_role_name			= !empty($customer_role['role_name']) ? $customer_role['role_name'] : '';
		//print_r($_GET);die;
		$order_main = array(
			'order_id'			=> $order_id,
			'send_no'			=> $send_no,
			'update_time'		=> $update_time,
			'pay_type'			=> $pay_type,
			'send_time'			=> $send_time,
			'processing_time'	=> $processing_time
		);

		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		
		$goods_list = $_GET['goods_list'];
		$goods_list_arr = json_decode($goods_list, true);
		
		if(empty($goods_list_arr))
		{
			exit(json_encode(array('status'=>false, 'msg'=>'获取商品失败', 'data'=>$items)));
		}

		if(empty($order_id))
		{
			//判断user_id  order_day_id  day 是否存在，若存在则不可添加
			$day_order_info = $obj_order->get_day_order_detail($customer_id, $order_day_id, $processing_time);
			if($day_order_info)
			{
				exit(json_encode(array('status'=>false, 'msg'=>'不可重复下订单', 'data'=>$items)));
			}
			
			//新建
			$total_amount = 0;
			$total_num = 0;
			foreach($goods_list_arr as $key => $val)
			{
				$goods = explode(':', $val);
				//获取报价
				//$role_id = $_SESSION['role_id'];
				$offer_price = $obj_good->get_role_good_price($role_id, $goods[0]);

				if(empty($offer_price))
				{
					$offer_price = $obj_good->get_good_price($goods[0]);
				}
				
				$offer_price = !empty($goods[7]) ? $goods[7] : $offer_price;
				//更新订单
				$items[] = array(
					'goods_id'		=> $goods[0],
					'goods_num'		=> $goods[1],
					'send_num'		=> $goods[2],
					'received_num'	=> $goods[3],
					'good_note'		=> $goods[4],
					'send_status'	=> $goods[5],
					'n_good_note'	=> $goods[6],
					'good_price'	=> $offer_price,
				);
				$total_num++;
				$total_amount += $goods[1]*$offer_price;
				
			}
			
			$order_no = date("YmdHis").rand(1000,9999);
			//总金额
			
			//订单总览
			$order_title = array(
				//'order_id'			=> $order_id,
				'order_no'			=> $order_no,
				'total_amount'		=> $total_amount,
				'total_num'			=> $total_num,
				'operator_id'		=> $operator_id,
				'operator'			=> $operator,
				'order_status'		=> 1,
				'confirm_time'		=> date("Y-m-d H:i:s", time()),
				'send_time'			=> $order_main['send_time'],
				'send_no'			=> $order_main['send_no'],
				'pay_type'			=> $order_main['pay_type'],
				'jg_date'			=> $order_main['processing_time'],
				'processing_time'	=> $order_main['update_time'],
				'order_role_id'		=> $order_role_id,
				'order_truck_id'	=> $truck_id,
				'order_day_id'		=> $order_day_id,
				'order_role_name'	=> $order_role_name,
				'order_user_id'		=> $customer_id,
				'order_user_name'	=> $customer_name,
				'order_time'		=> date("Y-m-d H:i:s", strtotime("+1 day"))
			);
			//print_r($order_title);print_r($items);die;
			$res = $obj_order->create_user_order($order_title, $items);
			if($res)
			{
				exit(json_encode(array('status'=>true, 'msg'=>'下单成功', 'data'=>$items)));
			}
			else
			{
				exit(json_encode(array('status'=>false, 'msg'=>'下单失败', 'data'=>$items)));
			}
			
		}
		else
		{
			//更新
			$total_amount = 0;
			$total_num = 0;
			foreach($goods_list_arr as $key => $val)
			{
				$goods = explode(':', $val);
				//获取报价
				//$role_id = $_SESSION['role_id'];
				$offer_price = $obj_good->get_role_good_price($role_id, $goods[0]);

				if(empty($offer_price))
				{
					$offer_price = $obj_good->get_good_price($goods[0]);
				}

				$offer_price = !empty($goods[7]) ? $goods[7] : $offer_price;
				//更新订单
				$items[] = array(
					'goods_id'		=> $goods[0],
					'goods_num'		=> $goods[1],
					'send_num'		=> $goods[2],
					'received_num'	=> $goods[3],
					'good_note'		=> $goods[4],
					'send_status'	=> $goods[5],
					'n_good_note'	=> $goods[6],
					'good_price'	=> $offer_price,
				);
				$total_num++;
				$total_amount += $goods[1]*$offer_price;
			}
			
			$order_title = array(
				'order_id'			=> $order_id,
				'order_no'			=> $order_no,
				'total_amount'		=> $total_amount,
				'total_num'			=> $total_num,
				'operator_id'		=> $operator_id,
				'operator'			=> $operator,
				'order_status'		=> 1,
				'confirm_time'		=> date("Y-m-d H:i:s", time()),
				'send_time'			=> $order_main['send_time'],
				'send_no'			=> $order_main['send_no'],
				'pay_type'			=> $order_main['pay_type'],
				'jg_date'			=> $order_main['processing_time'],
				'processing_time'	=> $order_main['update_time'],
				'order_role_id'		=> $role_id,
				'order_truck_id'	=> $truck_id,
				'order_time'		=> $order_main['processing_time']
			);
			
			//print_r($order_title);print_r($items);die;
			
			$res = $obj_order->update_sales_order($order_title, $items);
			
			
			if($res)
			{
				exit(json_encode(array('status'=>true, 'msg'=>'修改订单成功', 'data'=>$items)));
			}
			else
			{
				exit(json_encode(array('status'=>false, 'msg'=>'修改订单失败', 'data'=>$items)));
			}
		}
	
	}
	
}
$app->run();
	
?>
