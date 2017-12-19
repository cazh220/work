<?php 
/**
 * 统计
 */
require_once('./common.inc.php');

class statistics extends Action {
	public function doTest()
	{
		$result = range(0,1000);
		import('util.ArrayUtil');
		$result_s = ArrayUtil::ptint_web($result, 40, 50, 3);
		
		$res = ArrayUtil::transposition($result_s);
		
		print_r($res);
	}
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		$page = $this->app->page();
		$page->params['template'] = 'order_menu.html';
		$page->output();
	}
	
	//产品统计
	public function doGoodsStatistic()
	{
		//获取日期
		$confirm_time 		= !empty($_REQUEST['confirm_time']) ? trim($_REQUEST['confirm_time']) : date("Y-m-d", strtotime("+1 days"));//当前时间
		$category_id  		= !empty($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : 0;
		$order_role_id		= !empty($_REQUEST['order_role_id']) ? intval($_REQUEST['order_role_id']) : 0;
		$order_user_id		= !empty($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : 0;
		//var_dump($order_role_id);var_dump($order_user_id);die;
		if(!empty($order_user_id))
		{
			$prefix = substr($order_user_id,0,3);
			if($prefix == 'rid')
			{
				$rid = substr($order_user_id, 3);
				if($rid == 9)
				{
					$rid = 0;
					$order_user_id = 0;
				}
			}
		}

		if($rid)
		{
			//客户分类
			$order_role_id = $rid;
			$order_user_id = 0;
		}

		importModule("StatisticsInfo","class");
		$obj_statistics = new StatisticsInfo;
		
		$data = $obj_statistics->get_day_unsend_goods($confirm_time, $category_id, 0,  0,  $order_user_id, $order_role_id);
		//print_r($data);
		//分客户统计
		$data_role = array();
		$total_amount = 0;
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		if(!empty($data))
		{
			foreach($data as $key => $val)
			{
				$role_detail = $obj_role->get_role($val['order_role_id']);
				$val['order_role_name'] = !empty($role_detail['role_name']) ? $role_detail['role_name'] : '';
				//$data_role[$val['role_id']][] = $val;
				
				$data_role[$val['order_user_id']]['order_user_name'] = $val['order_user_name'];
				$data_role[$val['order_user_id']]['list'][] = $val;
				
				$total_amount += ($val['goods_num']*$val['good_price']);
				
				
				
			}
		}
		
		//print_r($data_role);//die;
		//导入工具类
		//import('util.Tongji');
		//$return = Tongji::goods_statistics($data);
		//$goods_detail = $return;
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		$category_list = $obj_category->get_categoty_list();
		//获取客户
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		$customer = $obj_order->get_order_users($confirm_time);
		$customer_arr = array();
		if($customer)
		{
			foreach($customer as $key => $val)
			{
				$customer_arr[$val['order_role_id']] = $val['role_name'];
			}
		}
		
		//导入工具类
		import('util.CategoryShow');
		$category_show = CategoryShow::category_show($category_list);
		//获取所选择的分类名称
		$category_info = $obj_category->get_category_info($category_id);
		
		
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
		
		if($order_role_id)
		{
			//获取role_name
			$role_info = $obj_role->get_role($order_role_id);
			$user_title	= !empty($role_info['role_name']) ? $role_info['role_name'] : '所有客户';
		}
		elseif($order_user_id)
		{
			$user_info = $obj_user->get_user_detail($order_user_id);
			$user_title	= !empty($user_info['realname']) ? $user_info['realname'] : '所有客户';
		}
		else
		{
			$user_title = '所有客户';
		}
		//print_r($user_title);
		//print_r($data_role);
		//重新排版打印样式
		$print_array = array();
		if($data_role)
		{
			foreach($data_role as $key => $val)
			{
				if($val['list'])
				{
					foreach($val['list'] as $k => $v)
					{
						$print_array[] = $v;
					}
				}
			}
		}
		import('util.ArrayUtil');
		$print = ArrayUtil::join_array($print_array, 16, 50, 3);
		//print_r($result_s);die;
		
		//$result_s = ArrayUtil::ptint_web($print_array, 16, 50, 3);
		//$print = ArrayUtil::transposition($result_s);
		
		$page = $this->app->page();
		$page->value('title',"宇尧实业（上海）有限公司");
		$page->value('confirm_time',$confirm_time);
		$page->value('goods',$data_role);
		$page->value('role_category',$customer_arr);
		$page->value('order_role_id',$order_role_id);
		$page->value('order_role_name',!empty($customer_arr[$order_role_id]) ? $customer_arr[$order_role_id] : '所有客户');
		$page->value('category',$category_show);
		$page->value('category_id',$category_id);
		$page->value('total_amount',$total_amount);
		$page->value('user_id',$order_user_id);
		$page->value('role_show',$role_show);
		$page->value('user_title',$user_title);
		$page->value('print',$print);
		$page->value('category_name',$category_info['cname'] ? $category_info['cname'] : '所有分类');
		$page->params['template'] = 'goods_statistics.html';
		$page->output();
	}
	
	
	//月统计
	public function doMonth()
	{
		//获取日期
		$start_time = !empty($_REQUEST['start_time']) ? trim($_REQUEST['start_time']) : date("Y-m-d", time());//当前时间
		
		importModule("StatisticsInfo","class");
		$obj_statistics = new StatisticsInfo;
		//$month = date('m', strtotime($start_time));
		$title = date("Y年m月", strtotime($start_time));
		$this->days = date('t', strtotime($start_time));

		$data = $obj_statistics->get_month_order($start_time);
		$result = $this->tongji_by_operator($data);
		
		$tongji = array();
		if($result)
		{
			for($i=2; $i<=33; $i++)
			{
				$total = 0;
				foreach($result as $key => $val)
				{
					
					foreach($val as $k => $v)
					{
						if($k == $i)
						{
							$total += $v;
						}
					}
					
				}
				$tongji[$i] = !empty($total) ? sprintf("%01.2f",$total) : 0;
				
			}
		}	
		
		array_unshift($tongji, '计');
		array_unshift($tongji, '合');

		$header = array();
		for($i=-2; $i<=$this->days; $i++)
		{
			if($i == -2)
			{
				array_push($header, '序号');
			}
			elseif($i == -1)
			{
				array_push($header, '客户名称');
			}
			elseif($i == 0)
			{
				array_push($header, '总计金额');
			}
			else
			{
				array_push($header, $i.'日');
			}
		}

		$page = $this->app->page();
		$page->value('title',$title);
		$page->value('start_time',$start_time);
		$page->value('list',$result);
		$page->value('head',$header);
		$page->value('foot',$tongji);
		$page->params['template'] = 'month.html';
		$page->output();
	}
	
	//分用户统计
	public function tongji_by_operator($data)
	{
		$result = array();
		if($data)
		{
			foreach($data as $key => $val)
			{
				$data[$key]['day'] = intval(date("d", strtotime($val['order_time'])));
			}
			
			//先按下单用户分类
			$user_order = array();
			foreach($data as $key => $val)
			{
				$user_order[$val['order_user_id']][] = $val;
			}

			$user_day_order = array();
			for($i=1; $i<=31; $i++)
			{
				
				foreach($user_order as $ku => $vu)
				{
					if(!empty($vu))
					{
						$day_amount = 0;
						$month_amount = 0;
						foreach($vu as $kk => $vv)
						{
							if($vv['day'] == $i)
							{
								$day_amount += $vv['total_amount'];
							}
							$month_amount += $vv['total_amount'];
						}
						$user_day_order[$ku][$i]['total_amount'] = $day_amount;
						$user_day_order[$ku][$i]['order_user_name'] = $vu[0]['order_user_name'];
						$user_day_order[$ku][$i]['order_user_id'] = $vu[0]['order_user_id'];
						$user_day_order[$ku][$i]['day'] = $i;
						$user_day_order[$ku][$i]['month_amount'] = $month_amount;
					}
					
				}
				
			}
			//print_r($user_day_order);die;
			$order_item  = array();
			$i = 0;
			foreach($user_day_order as $keys => $vals)
			{
				$order_item[$i][0] = $i+1;
				$order_item[$i][1] = $vals[1]['order_user_name'];
				$order_item[$i][2] = !empty($vals[1]['month_amount']) ? sprintf("%01.2f",$vals[1]['month_amount']) : 0;
				foreach($vals as $ka => $va)
				{
					$order_item[$i][$ka+2] =  !empty($va['total_amount']) ? sprintf("%01.2f",$va['total_amount']) : 0;
					
				}
				$i++;
			}
			//print_r($order_item);die;
			return $order_item;
		}		
	}
	
	//分车单统计
	public function doTruckStatistic()
	{
		//获取日期
		$confirm_time 		= !empty($_REQUEST['confirm_time']) ? trim($_REQUEST['confirm_time']) : date("Y-m-d", strtotime("+1 days"));//当前时间
		$category_id  		= !empty($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : 0;
		$order_role_id		= !empty($_REQUEST['order_role_id']) ? intval($_REQUEST['order_role_id']) : 0;
		$order_truck_id		= !empty($_REQUEST['order_truck_id']) ? intval($_REQUEST['order_truck_id']) : 0;
		$order_user_id		= !empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;

		if(empty($order_truck_id))
		{
			echo "<script>alert('请选择分车');</script>";
		}

		//客户分类
		if(!empty($order_user_id))
		{
			$prefix = substr($order_user_id,0,3);
			if($prefix == 'rid')
			{
				$rid = substr($order_user_id, 3);
			}
		}
		
		if($rid)
		{
			//客户分类
			$order_role_id = $rid;
			$order_user_id = 0;
		}
		
		importModule("StatisticsInfo","class");
		$obj_statistics = new StatisticsInfo;
		//var_dump('user_id:'.$order_user_id);
		//var_dump('rile_id:'.$order_role_id);
		$data = $obj_statistics->get_day_unsend_goods($confirm_time, $category_id, 0,  $order_truck_id,  $order_user_id, $order_role_id);
		
//		$data = $obj_statistics->get_day_goods($confirm_time, $category_id, $order_user_id, $order_truck_id, $user_id);
		//print_r($data);die;
		//分客户统计
		$data_role = array();
		$total_amount = 0;
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;

		if(!empty($data))
		{
			foreach($data as $key => $val)
			{
				$truck_detail = $obj_truck->get_truck($val['order_truck_id']);
				$val['order_truck_name'] = !empty($truck_detail['truck_name']) ? $truck_detail['truck_name'] : '';
				$role_detail = $obj_role->get_role($val['order_role_id']);
				$val['order_role_name'] = !empty($role_detail['role_name']) ? $role_detail['role_name'] : '';
				
				//[$val['operator_id']]['operator'] = $val['operator'];
				//$data_role[$val['operator_id']]['list'][] = $val;
				
				$data_role[$val['order_user_id']]['order_user_name'] = $val['order_user_name'];
				$data_role[$val['order_user_id']]['list'][] = $val;
				//$data_role[$val['role_id']]['role_name'] = $val['order_role_name'];
				//$data_role[$val['role_id']]['list'][] = $val;
				$total_amount += ($val['goods_num']*$val['good_price']);
			}
		}
		
		//print_r($data_role);//die;
		//导入工具类
		//import('util.Tongji');
		//$return = Tongji::goods_statistics($data);
		//$goods_detail = $return;
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		$category_list = $obj_category->get_categoty_list();
		//获取客户
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		$customer = $obj_order->get_order_users($confirm_time);
		//print_r($customer);die;
		$customer_arr = array();
		$customer_user_arr = array();
		if($customer)
		{
			foreach($customer as $key => $val)
			{
				$customer_arr[$val['order_role_id']] = $val['role_name'];
				
				//获取所有下单的客户
				$customer_user_arr[$val['operator_id']] = $val['operator'];
			}
		}

		//导入工具类
		import('util.CategoryShow');
		$category_show = CategoryShow::category_show($category_list);
		//获取所选择的分类名称
		$category_info = $obj_category->get_category_info($category_id);
		//获取分车、
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		$truck = $obj_truck->get_all_trucks();
		if($truck)
		{
			foreach($truck as $key => $val)
			{
				if($val['truck_id'] == $order_truck_id)
				{
					$truck_name = $val['truck_name'];
				}
			}
		}
		
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
		
		if($order_role_id)
		{
			//获取role_name
			$role_info = $obj_role->get_role($order_role_id);
			$user_title	= !empty($role_info['role_name']) ? $role_info['role_name'] : '所有客户';
		}
		elseif($order_user_id)
		{
			$user_info = $obj_user->get_user_detail($order_user_id);
			$user_title	= !empty($user_info['realname']) ? $user_info['realname'] : '所有客户';
		}
		else
		{
			$user_title = '所有客户';
		}

		$page = $this->app->page();
		$page->value('title',"宇尧实业（上海）有限公司");
		$page->value('confirm_time',$confirm_time);
		$page->value('goods',$data_role);
		$page->value('role_category',$customer_arr);
		$page->value('customer',$customer_user_arr);
		$page->value('order_user_id',$order_user_id);
		$page->value('order_role_id',$order_role_id);
		$page->value('order_truck_id',$order_truck_id);
		$page->value('truck',$truck);
		//$page->value('order_role_name',!empty($customer_arr[$order_role_id]) ? $customer_arr[$order_role_id] : '所有客户');
		$page->value('order_user_name',!empty($customer_user_arr[$order_user_id]) ? $customer_user_arr[$order_user_id] : '所有客户');
		$page->value('category',$category_show);
		$page->value('role_show',$role_show);
		$page->value('category_id',$category_id);
		$page->value('user_id',$order_user_id);
		$page->value('total_amount',$total_amount);
		$page->value('user_title',$user_title);
		$page->value('category_name',$category_info['cname'] ? $category_info['cname'] : '所有分类');
		$page->value('truck_name',!empty($truck_name) ? $truck_name : '所有分车');
		$page->params['template'] = 'truck_statistics.html';
		$page->output();
	}
}
$app->run();
	
?>
