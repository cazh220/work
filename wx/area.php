<?php 
/**
 * 区域处理
 * 
 */
require_once('./common.inc.php');

class area extends Action {
	
	/**
	 * 默认执行的方法(用户登录页面)
	 */
	public function doDefault(){	
		
		$page = $this->app->page();
		$page->value('user_list',$_SESSION);
		$page->params['template'] = 'user.php';
		$page->output();
	}
	
	//获取城市
	public function doGetCity()
	{
		$province_id = $_GET['province_id'];
		
		importModule("AreaInfo","class");
		$obj_area = new AreaInfo;
		$city = $obj_area->get_city($province_id);
		
		if (!empty($city))
		{
			exit(json_encode(array('status'=>1, 'list'=>$city)));
		}
		else
		{
			exit(json_encode(array('status'=>0, 'list'=>array())));
		}
		
	}
	
	//获取区域
	public function doGetDistrict()
	{
		$city_id = $_GET['city_id'];
		
		importModule("AreaInfo","class");
		$obj_area = new AreaInfo;
		$district = $obj_area->get_city($city_id);
		
		if (!empty($district))
		{
			exit(json_encode(array('status'=>1, 'list'=>$district)));
		}
		else
		{
			exit(json_encode(array('status'=>0, 'list'=>array())));
		}
		
	}
	
	//删除地址
	public function doDeleteAddr()
	{
		$address_id 	= $_GET['address'];
		importModule("AddressInfo","class");
		$obj_addr = new AddressInfo;
		$res = $obj_addr->delete_addr($address_id);
		if($res)
		{
			exit(json_encode(array('status'=>true, 'message'=>'删除成功')));
		}
		else
		{
			exit(json_encode(array('status'=>false, 'message'=>'删除失败')));
		}
	}
	
	//地址列表
	public function doAddressList()
	{
		$user_id	 = !empty($_GET['user_id']) ? intval($_GET['user_id']) : 0;
		$act	 = !empty($_GET['act']) ? trim($_GET['act']) : '';
		$order_id	 = !empty($_GET['order_id']) ? trim($_GET['order_id']) : '';
		//获取地址列表
		importModule("AddressInfo","class");
		$obj_address = new AddressInfo;
		
		$address = $obj_address->address_list($user_id);
		//print_r($address);die;
		$page = $this->app->page();
		$page->value('address',$address);
		$page->value('user_id',$user_id);
		$page->value('order_id',$order_id);
		$page->value('act',$act);
		$page->params['template'] = 'address_list.php';
		$page->output();
	}
	
	//选择地址
	public function doSelectAddress()
	{
		$address = !empty($_GET['address']) ? intval($_GET['address']) : 0;
		$user_id = !empty($_GET['user_id']) ? intval($_GET['user_id']) : 0;
		
		//获取地址列表
		importModule("AddressInfo","class");
		$obj_address = new AddressInfo;
		
		$res = $obj_address->update_address_id($user_id, $address);
		if($res)
		{
			exit(json_encode(array('status'=>true, 'message'=>'success')));
		}
		else
		{
			exit(json_encode(array('status'=>false, 'message'=>'failed')));
		}
	}
	
	
}
$app->run();
	
?>
