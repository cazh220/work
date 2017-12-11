<?php 
/**
 * 分车分类
 */
require_once('./common.inc.php');

class truck extends Action {
	
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
	
	//添加卡车
	public function doAddTruck()
	{
		$page = $this->app->page();
		$page->params['template'] = 'truck_add.html';
		$page->output();
	}
	
	//添加jiaose
	public function doAddTruckAct()
	{
		$truck_name	= !empty($_POST['truck_name']) ? trim($_POST['truck_name']) : '';
		$mobile		= !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
		
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		
		$res = $obj_truck->add_new_truck($truck_name, $mobile);
		
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '添加成功',
				'navTabId'		=> 'truck_list',
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
				'navTabId'		=> 'truck_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
	}
	
	//编辑Truck
	public function doEditTruck()
	{
		$truck_id = !empty($_GET['truck_id']) ? intval($_GET['truck_id']) : 0;
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		
		$truck = $obj_truck->get_truck($truck_id);
		
		$page = $this->app->page();
		$page->value('truck',$truck);
		$page->params['template'] = 'truck_edit.html';
		$page->output();
	}
	
	//编辑
	public function doEditTruckAct()
	{
		$truck_id = !empty($_POST['truck_id']) ? intval($_POST['truck_id']) : 0;
		$truck_name	= !empty($_POST['truck_name']) ? trim($_POST['truck_name']) : '';
		$mobile		= !empty($_POST['mobile']) ? trim($_POST['mobile']) : '';
		
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;
		
		$res = $obj_truck->edit_truck($truck_name, $mobile, $truck_id);
		
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '编辑成功',
				'navTabId'		=> 'truck_list',
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
				'navTabId'		=> 'truck_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
	}
	
	//移除角色
	public function doRemoveTruck()
	{
		$truck_id = !empty($_GET['truck_id']) ? intval($_GET['truck_id']) : 0;
		
		importModule("TruckInfo","class");
		$obj_truck = new TruckInfo;

		$res = $obj_truck->remove_truck($truck_id);
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '删除成功',
				'navTabId'		=> 'truck_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> HOST.'/truck.php',
				'confirmMsg'	=> ''
			);
		}
		else
		{
			//失败
			$return = array(
				'statusCode'	=> 0,
				'message'		=> '删除失败',
				'navTabId'		=> 'truck_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> HOST.'/truck.php',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
		/*
		if(!$res)
		{
			//成功
			echo "<script>alert('删除失败');window.go(-1);</script>";
		}*/
	}
	
	

	
	
	

}
$app->run();
	
?>
