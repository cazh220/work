<?php 
/**
 * 配送
 */
require_once('./common.inc.php');

class send extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		//获取客户分类
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		$trucks = $obj_truck->get_all_trucks();
		//print_r($trucks);die;
		$page = $this->app->page();
		$page->value('trucks',$trucks);
		$page->params['template'] = 'truck_list.html';
		$page->output();
	}
	
	//配送分车
	public function doAssignTruck()
	{
		$order_id = !empty($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		
		//获取货车
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		$trucks = $obj_truck->get_all_trucks();
		
		$truck_id = 0;
		//获取配送的车，先send_no, 在获取truck_id
	    importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		//获取订单详情
		$order_info = $obj_order->get_order_detail($order_id);
		if($order_info['send_no'])
		{
			importModule("SendInfo","class");
			$obj_send = new SendInfo;
			$send_info = $obj_send->get_send_by_send_no($order_info['send_no']);
			if(!empty($send_info['truck']))
			{
				$truck_id = $send_info['truck'];
			}
		}
		
		$page = $this->app->page();
		$page->value('trucks',$trucks);
		$page->value('order_id',$order_id);
		$page->value('truck_id',$truck_id);
		$page->params['template'] = 'send_assign.html';
		$page->output();
	}
	
	//配送分车Act
	public function doAssignTruckAct()
	{
		$order_id	= !empty($_POST['order_id']) ? intval($_POST['order_id']) : 0;
		$truck_id	= !empty($_POST['truck_id']) ? intval($_POST['truck_id']) : 0;
		$operator_id = $_SESSION['user_id'];
		$operator = $_SESSION['username'];
		//生成一个配送号
		$send_no = 'S'.date("YmdHis").rand(1000, 9999);
		//检查是否有配送
		importModule("SendInfo","class");
		$obj_send = new SendInfo;
		
		//获取订单详情
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;
		
		$order_detail = $obj_order->get_order_detail($order_id);
		$order_num = !empty($order_detail['total_num']) ? intval($order_detail['total_num']) : 0;
		$order_amount = !empty($order_detail['total_amount']) ? intval($order_detail['total_amount']) : 0;
		$send_info = $obj_send->get_truck_send_detail($truck_id, 1);//配送中
		//如果是第一辆车换车
		/*
		if(!empty($send_info))
		{
			$num = $send_info['send_num'] - $order_num;
			if($num <= 0)
			{
				//删除换车
				
			}
		}*/
		
		if($send_info)
		{
			//已有其他配送，更新
			$param = array(
				'send_num'		=>  $order_num,
				'send_amount'	=>  $order_amount,
				'operator_id'	=>$operator_id,
				'operator'		=> $operator,
			);

			$res = $obj_send->update_send_order($param, $send_info['send_no'], $order_id);
			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '编辑成功',
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
					'message'		=> '编辑失败',
					'navTabId'		=> 'pagination1',
					'rel'			=> '',
					'callbackType'	=> '',
					'forwardUrl'	=> 'closeCurrent',
					'confirmMsg'	=> ''
				);
			}
			exit(json_encode($return));
		}
		else
		{
			//空车
			$param = array(
				'send_no'	=> $send_no,
				'send_num'	=> $send_num,
				'truck'		=> $truck_id,
				'operator_id'=>$operator_id,
				'operator'	=> $operator,
				'send_num'	=> $order_num,
				'send_amount'=>$order_amount,
				'send_status'=> 1,
			);
			//print_r($param);print_r($send_no);var_dump($order_id);die;
			$res = $obj_send->insert_send_order($param, $send_no, $order_id);
			
			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '添加成功',
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
					'message'		=> '添加失败',
					'navTabId'		=> 'pagination1',
					'rel'			=> '',
					'callbackType'	=> '',
					'forwardUrl'	=> 'closeCurrent',
					'confirmMsg'	=> ''
				);
			}
			exit(json_encode($return));
		}
		
		
		
	}

}
$app->run();
	
?>
