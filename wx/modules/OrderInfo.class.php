<?php
/**
 * 订单处理类
 * 
 * @package     modules
 */
 
class OrderInfo
{
	/**
	 * 应用程序对象
	 * @var Application
	 */
	private $app = null;
	
	/**
	 * 数据库操作对象
	 * @var OrmQuery
	 */
	private $db = null;
	
	/**
     * 构造函数，获取数据库连接对象
     *
     */
    public function __construct(){
        global $app;
        
        $this->app = $app;
        
        $this->db = $app->orm($app->cfg['db'])->query();
		
        mysql_query("set names utf8");
    }
    
    //生成订单
    public function create_order($order=array(), $order_goods=array())
    {
    	if($this->db == null)
		{
    		return false;
    	}	
    	
    	$this->db->exec("START TRANSACTION");
    	$sql = "INSERT INTO hg_order(order_no, user_id, username, address, consignee, mobile, total_credits, create_time,address_id)VALUES('".$order['order_no']."','".$order['user_id']."','".$order['username']."','".$order['address']."','".$order['consignee']."','".$order['mobile']."','".$order['total_credits']."','".$order['create_time']."',".$order['address_id'].")";
    	$res = $this->db->exec($sql);
    	$order_id = $this->db->getLastId();
    	    	
    	if(empty($order_id))
    	{
    		$this->db->exec("ROLLBACK");
    		return false;
    	}
    	
    	foreach($order_goods as $key => $val)
    	{
    		$sql = "INSERT INTO hg_order_gift(order_id, gift_id, gift_name, amount, price, gift_pic)VALUES('".$order_id."', '".$val['gift_id']."','".$val['gift_name']."','".$val['amount']."','".$val['price']."', '".$val['gift_pic']."')";
    		
    		$result = $this->db->exec($sql);
    		if(empty($result))
    		{
    			$this->db->exec("ROLLBACK");
    			return false;
    		}
    	}
    	
    	//扣积分
    	$sql = "UPDATE hg_user SET exchanged_credits = exchanged_credits + {$order['total_credits']}, left_credits = left_credits - {$order['total_credits']} WHERE user_id = {$order['user_id']}";
    	
    	$up_res = $this->db->exec($sql);
		if(empty($up_res))
		{
			$this->db->exec("ROLLBACK");
			return false;
		}
		
		//插入日志
		$admin_id = $user_id = !empty($order['user_id']) ? $order['user_id'] : 0;
		$username	= !empty($order['username']) ? $order['username'] : '';
		$content = '消耗积分'.$order['total_credits'];
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$sql = "INSERT INTO hg_user_actions(admin_id,user_id,username,content,action_create_time,ip,channel)VAlUES($admin_id,$user_id,'".$username."','".$content."',NOW(),'".$ip."',1)";
		
		try{
			$this->db->exec($sql);
		}catch(exception $e){
			$this->db->exec("ROLLBACK");
			return false;
		}

    	$this->db->exec("COMMIT");
    	return true;
    }
    
	//获取订单信息
	public function get_order_info($order_no)
	{
		if($this->db == null)
		{
    		return false;
    	}
		$sql = "SELECT *,a.order_no FROM hg_order a LEFT JOIN hg_order_gift b ON a.order_id = b.order_id WHERE a.order_no ='".$order_no."'";
		$res = $this->db->getArray($sql);

		if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		return $res;
	}
	
	//我的订单
	public function get_my_order($user_id)
	{
		if($this->db == null)
		{
    		return false;
    	}
		//$sql = "SELECT a.order_id,a.order_no,a.order_status,a.user_id,a.username,a.province,a.city,a.district,a.address,a.zipcode,a.consignee,a.mobile,a.ship_no,a.ship_company,a.update_ship_time,a.send_time,a.total_credits,a.create_time,a.confirm_time,a.update_time,a.address_id,b.gift_id,b.gift_name,b.amount,b.price,b.gift_pic,b.num FROM hg_order a LEFT JOIN hg_order_gift b ON a.order_id = b.order_id WHERE a.user_id = {$user_id}";
		$sql = "SELECT * FROM hg_order  WHERE user_id = {$user_id} ORDER BY order_id DESC";
		$res = $this->db->getArray($sql);
		
		if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		return $res;
	}


    //订单号生成
    public function get_orderno()
    {
    	return '10'.date("YmdHis",time()).rand(1000,9999);
    }
	
	//获取收货地址
	public function get_order_address($address_id=0)
	{
		if($this->db == null || empty($address_id))
		{
			return false;
		}
		$sql = "SELECT a.*,b.`name` as province_name, c.`name` as city_name, d.`name` as district_name  FROM hg_address a LEFT JOIN hg_region b on a.province = b.id LEFT JOIN hg_region c ON a.city = c.id LEFT JOIN hg_region d ON a.district = d.id WHERE address_id = {$address_id}";
			
		$res = $this->db->getRow($sql);
		
		return $res ? $res : array();
	}
	
	
	/**
	 * 数据更新失败记录日志，并标识操作失败
	 *
	 * @param 	Array 	$data
	 * @return 	bool	false
	 */
	private function _log($data){
	    $log = $this->app->log();
	    $log->reset()->setPath("modules/UserInfo")->setData($data)->write();
	    
	    return false;
	}
}
?>