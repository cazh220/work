<?php
/**
 * 订单处理
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
    
    //添加订单
    public function add_order($order=array(), $order_goods=array())
    {
    	if($this->db == null || empty($order) || empty($order_goods))
		{
    		return false;
    	}

		$sql = "INSERT INTO yy_order SET ";
		foreach($order as $key => $val)
		{
			$sql .= $key." = '".$val."',";
		}
		$sql .= " create_time = NOW(), update_time = NOW()";
		
		$this->db->exec("START TRANSACTION");
		
		try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
		
		if(!$res)
		{
			$this->db->exec("ROLLBACK");
			return false;
		}
		
		$order_id = $this->db->getLastId();
		
		foreach($order_goods as $key => $val)
		{
			$sql = "INSERT INTO yy_order_goods SET order_id = ".$order_id.", goods_id = ".$val['goods_id'].", goods_num = ".$val['goods_num'].", good_price = ".$val['good_price'];
			
			try{
	    		$res = $this->db->exec($sql);
	    	}catch(exception $e){
	    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
	    		$this->db->exec("ROLLBACK");
				return false;
	    	}
			
			if(!$res)
			{
				$this->db->exec("ROLLBACK");
				return false;
			}
		}
		
		$this->db->exec("COMMIT");
		return true;

    }
    
    //获取待确认的订单商品
    public function get_unconfirm_order_goods($param=array())
    {
    	if($this->db == null || empty($param['user_id']))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_order a LEFT JOIN yy_order_goods b ON a.order_id = b.order_id LEFT JOIN yy_goods c ON b.goods_id = c.goods_id WHERE a.is_delete = 0 AND a.order_status = 0 AND a.operator_id = ".$param['user_id'];
    	
    	if($param['goods_id'])
    	{
    		$sql .= " AND b.goods_id = ".$param['goods_id'];
    	}

    	$res = $this->db->getArray($sql);
    	return $res ? $res : array();  	
    }
    
    //获取的订单
    public function get_order($param=array())
    {
    	if($this->db == null || empty($param['user_id']))
		{
    		return false;
    	}
    	
    	$where = " is_delete = 0 AND order_status = ".$param['order_status']." AND operator_id = ".$param['user_id'];
    	if($param['order_no'])
    	{
    		$where .= " AND order_no = '".$param['order_no']."'";
    	}
    	
    	$sql = "SELECT * FROM yy_order WHERE {$where}";//echo $sql;die;
    	
    	$sql_count = "SELECT count(*) as cnt FROM yy_order WHERE {$where}";
    	
    	$res_count = $this->db->getValue($sql_count);
    	/*
    	if($param['page'] && $param['page_size'])
    	{
    		$start = ($param['page']-1)*$param['page_size'];
    		$page_size = $param['page_size'];
    	}
    	
    	$sql .= " ORDER BY order_id DESC LIMIT {$start}, {$page_size}";
    	*/
    	$res = $this->db->getArray($sql);
    	
    	$return = array(
    		'list' => $res ? $res : array(),
    		'count'	=> $res_count ? $res_count : 0
    	);
    	return $return;  	
    }
    
    //移除订单内商品
    public function remove_goods($goods_id=0, $order_id=0)
    {
    	if($this->db == null || empty($goods_id) || empty($order_id))
		{
    		return false;
    	}
    	$this->db->exec("START TRANSACTION");
    	
    	$sql = "DELETE FROM yy_order_goods WHERE goods_id = {$goods_id} AND order_id = {$order_id}";
    	
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	
    	//如没有订单商品了，要删除订单
    	$sql = "SELECT COUNT(*) as cnt FROM yy_order_goods WHERE order_id = {$order_id}";
    	
    	try{
    		$res = $this->db->getValue($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	
    	if($res == 0)
    	{
    		//删除订单
    		$sql = "DELETE FROM yy_order WHERE order_id = {$order_id}";
    		
    		try{
	    		$res = $this->db->exec($sql);
	    	}catch(exception $e){
	    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
	    		$this->db->exec("ROLLBACK");
				return false;
	    	}
    	}
    	
    	$this->db->exec("COMMIT");
    	return true;
    }
    
    //更新订单信息
    public function confirm_order($order=array(), $order_goods=array())
    {
    	if($this->db == null || empty($order) || empty($order_goods))
		{
    		return false;
    	}
    	
    	$this->db->exec("START TRANSACTION");
    	foreach($order_goods as $key => $val)
    	{
    		$sql = "UPDATE yy_order_goods SET goods_num = ".$val['goods_num'].",good_note = '".$val['good_note']."' WHERE goods_id = ".$val['goods_id']." AND order_id = ".$val['order_id'];
    		try{
	    		$res = $this->db->exec($sql);
	    	}catch(exception $e){
	    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
	    		$this->db->exec("ROLLBACK");
				return false;
	    	}
    		
    	}
    	
    	//更改订单状态
    	$sql  = "UPDATE yy_order SET order_status = 1, confirm_time = NOW() WHERE order_id = ".$order['order_id'];
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	
    	
    	$this->db->exec("COMMIT");
		return true;
    }
    
    //检查订单信息
    public function get_unconfirm_order($user_id = 0)
    {
    	if($this->db == null || empty($user_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_order WHERE operator_id = {$user_id} AND order_status = 0 LIMIT 1";
    	
    	try{
    		$order = $this->db->getArray($sql);
    	}catch(exception $e){
    		echo $e->getMessage();die;
    	}
    	
    	return isset($order[0]) ? $order[0] : array();
    }
    
    //添加新商品
    public function add_new_order_goods($order=array(), $order_goods=array())
    {
    	if($this->db == null || empty($order) || empty($order_goods))
		{
    		return false;
    	}
    	
    	$this->db->exec("START TRANSACTION");
    	foreach($order_goods as $key => $val)
    	{
    		$sql = "INSERT INTO yy_order_goods SET order_id = ".$val['order_id'].", goods_id = ".$val['goods_id'].", goods_num = 1, good_price = ".$val['good_price'];
    		try{
	    		$res = $this->db->exec($sql);
	    	}catch(exception $e){
	    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
	    		$this->db->exec("ROLLBACK");
				return false;
	    	}
    	}	

    	//更新订单
    	$sql = "UPDATE yy_order SET total_amount = ".$order['total_amount'].", total_num = ".$order['total_num']." WHERE order_id = ".$order['order_id'];
    	
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	
    	$this->db->exec("COMMIT");
		return true;
    }
    
    //获取订单商品明显
    public function get_order_goods($order_id=0)
    {
    	if($this->db == null || empty($order_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_order a LEFT JOIN yy_order_goods b ON a.order_id = b.order_id LEFT JOIN yy_goods c ON b.goods_id = c.goods_id WHERE a.order_id = ".$order_id;
    	$res = $this->db->getArray($sql);
    	
    	return $res ? $res : array();
    }
    
    //获取订单商品信息
    public function get_order_goods_detail($order_id=0, $goods_id=0)
    {
    	if($this->db == null || empty($order_id) || empty($goods_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_order_goods WHERE order_id = ".$order_id." AND goods_id = ".$goods_id;
    	
    	$res = $this->db->getRow($sql);
    	
    	return $res ? $res : array();
    }
    
    
    //API更新订单信息
    public function new_confirm_order($order=array(), $order_goods=array())
    {
    	if($this->db == null || empty($order) || empty($order_goods))
		{
    		return false;
    	}
    	
    	$this->db->exec("START TRANSACTION");
    	foreach($order_goods as $key => $val)
    	{
    		$sql = "UPDATE yy_order_goods SET goods_num = ".$val['goods_num']." WHERE goods_id = ".$val['goods_id']." AND order_id = ".$val['order_id'];
    		
    		try{
	    		$res = $this->db->exec($sql);
	    	}catch(exception $e){
	    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
	    		$this->db->exec("ROLLBACK");
				return false;
	    	}
    		
    	}
    	
    	//更改订单状态
    	$sql  = "UPDATE yy_order SET order_status = 1, total_num = ".$order['total_num'].", total_amount = ".$order['total_amount'].", update_time = NOW() WHERE order_id = ".$order['order_id'];
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	
    	
    	$this->db->exec("COMMIT");
		return true;
    }
    
    //获取有效订货单
    //获取的订单
    public function get_effect_order($user_id=0)
    {
    	if($this->db == null || empty($user_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT order_id,order_no,total_amount,total_num,order_status,confirm_time FROM yy_order WHERE is_delete = 0 AND order_status IN (1,2,3) AND operator_id = ".$user_id;//echo $sql;die;
    	
    	$res = $this->db->getArray($sql);
    	return $res ? $res : array();  	
    }
    
    
    //获取订单商品sample
    public function get_simple_order_goods($order_id=0)
    {
    	if($this->db == null || empty($order_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT c.goods_name,b.good_price,b.goods_num,(b.good_price*b.goods_num) as amount FROM yy_order_goods b LEFT JOIN yy_goods c ON b.goods_id = c.goods_id WHERE b.order_id = ".$order_id;
    	//echo $sql;die;
    	$res = $this->db->getArray($sql);
    	
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
	    $log->reset()->setPath("modules/Category")->setData($data)->write();
	    
	    return false;
	}
	
	//获取订单列表
	public function get_order_list($param=array())
	{
		if($this->db == null)
		{
    		return false;
    	}
    	
    	$where = " a.is_delete = 0 ";
    	if($param['order_status'] = -1)
    	{
    		//全部
    		$where .= " AND a.order_status IN (1,2,3,4) ";
    	}
    	else
    	{
    		$where .= " AND a.order_status = ".$param['order_status'];
    	}
    	
    	if(!empty($param['operator']))
    	{
    		$where .= " AND a.operator LIKE '".$param['operator']."%'";
    	}
    	
    	if(!empty($param['role_id']))
    	{
    		//获取子分类
    		$category_set = array($param['role_id']);
    		$sql = "SELECT role_id FROM yy_role WHERE parent_id = ".$param['role_id'];
    		$res_cat = $this->db->getArray($sql);
    		if($res_cat)
    		{
    			foreach($res_cat as $key => $val)
    			{
    				array_push($category_set, $val['role_id']);
    			}
    			
    			$cat_list = implode(',', $category_set);
    			$sql = "SELECT role_id FROM yy_role WHERE parent_id IN (".$cat_list.")";
    			$res_cat_child = $this->db->getArray($sql);
    			if ($res_cat_child)
    			{
    				foreach($res_cat_child as $k => $v)
	    			{
	    				array_push($category_set, $v['role_id']);
	    			}
    			}
    		}
    		$cat_list = implode(',', $category_set);
    		
    		$where .= " AND a.order_role_id IN (".$cat_list.") ";
    		//$where .= " AND a.order_role_id = ".$param['role_id'];
    	}
    	
    	if(!empty($param['start_time']))
    	{
    		$where .= " AND a.order_time >= '".$param['start_time']."'";
    	}
    	
    	if(!empty($param['end_time']))
    	{
    		$where .= " AND a.order_time <= '".$param['end_time']."'";
    	}
    	
    	$sql = "SELECT *,a.create_time as create_time FROM yy_order a LEFT JOIN yy_users b ON a.operator_id = b.user_id LEFT JOIN yy_role c ON b.role_id = c.role_id WHERE {$where}";//echo $sql;die;
    	//echo $sql;//die;
    	$sql_count = "SELECT count(*) as cnt FROM yy_order a WHERE {$where}";
    	$res_count = $this->db->getValue($sql_count);
    	/*
    	if($param['page'] && $param['page_size'])
    	{
    		$start = ($param['page']-1)*$param['page_size'];
    		$page_size = $param['page_size'];
    		$sql .= " ORDER BY a.order_id DESC LIMIT {$start}, {$page_size}";
    	}*/
    	//echo $sql;//die;
    	$res = $this->db->getArray($sql);
    	
    	$return = array(
    		'list'	=> $res ? $res : array(),
    		'count'	=> $res_count ? $res_count : 0
    	);
    	return $return;  	
	}
	
	//获取订单的总览
	public function get_general_info($order_id = 0)
	{
		if($this->db == null || empty($order_id))
		{
    		return false;
    	}

		
		$sql = "SELECT * FROM yy_order WHERE order_id = ".$order_id;
		
		$res = $this->db->getRow($sql);
		
		return $res ? $res : array();
	}
	
	//更新订单状态
	public function update_order_status($order_id=0, $order_status = 0)
    {
    	if($this->db == null || empty($order_id))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_order SET order_status = {$order_status} WHERE order_id = {$order_id}";
    	
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	
    	return true;
    }
    
    //获取分车订单
    public function get_truck_order_list($param=array())
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$where = " a.is_delete = 0 ";
    	if($param['truck_id'])
    	{

    		$where .= " AND a.truck = ".$param['truck_id'];
    	}
		
    	$sql = "SELECT * FROM yy_truck_send a LEFT JOIN yy_truck b ON a.truck = b.truck_id WHERE {$where}";
    	//echo $sql;die;
    	$sql_count = "SELECT count(*) as cnt FROM yy_truck_send a LEFT JOIN yy_truck b ON a.truck = b.truck_id WHERE {$where}";
    	$res_count = $this->db->getValue($sql_count);
    	/*
    	if($param['page'] && $param['page_size'])
    	{
    		$start = ($param['page']-1)*$param['page_size'];
    		$page_size = $param['page_size'];
    		$sql .= " ORDER BY a.send_id DESC LIMIT {$start}, {$page_size}";
    	}*/
    	//echo $sql;die;
    	$res = $this->db->getArray($sql);
    	
    	$return = array(
    		'list'	=> $res ? $res : array(),
    		'count'	=> $res_count ? $res_count : 0
    	);
    	return $return; 
    }
    
    //获取货车订单
    public function get_truck_orders($send_no='')
    {
    	if($this->db == null || empty($send_no))
		{
    		return false;
    	}
    	
    	$sql = "SELECT distinct(order_no) FROM yy_order WHERE send_no = '".$send_no."'";
    	
    	$res = $this->db->getArray($sql);
    	
    	return $res ? $res : array();
    }
    
    //获取分车订单商品
    public function get_truck_goods($send_no = '')
    {
    	if($this->db == null || empty($send_no))
		{
    		return false;
    	}
    	
    	$sql = "SELECT e.truck_name,a.create_time,c.good_price,c.goods_num,d.goods_name,d.goods_id,d.unit,b.order_no,b.create_time as order_time FROM yy_truck_send a LEFT JOIN yy_order b ON a.send_no = b.send_no LEFT JOIN yy_order_goods c ON b.order_id = c.order_id LEFT JOIN yy_goods d ON c.goods_id = d.goods_id LEFT JOIN yy_truck e ON a.truck = e.truck_id WHERE a.send_no = '".$send_no."'";

    	$res = $this->db->getArray($sql);
    	
    	return $res ? $res : array();
    }
    
    
    //更新订单信息，修改订单
    public function replace_order($order_title=array(), $order_goods=array())
    {
    	if($this->db == null || empty($order_title))
		{
    		return false;
    	}
    	//订单ID
    	$order_id = $order_title['order_id'];
    	
    	$this->db->exec("START TRANSACTION");//事务开始
    	if(!empty($order_goods))
    	{
    		//先删除、再新增
	    	$sql = "delete from yy_order_goods where order_id = {$order_id}";
	    	try{
	    		$this->db->exec($sql);
	    	}catch(exception $e){
	    		$this->db->exec("ROLLBACK");//返回
	    		return false;
	    	}
	
			//重新增加订单商品
			$sql = "INSERT INTO yy_order_goods(order_id, goods_id, goods_num, good_price, good_note, send_num, received_num, n_good_note, send_status)VALUES";
			
			foreach($order_goods as $key => $val)
			{
				$sql .= "(".$order_id.", ".$val['goods_id'].", ".$val['goods_num'].", ".$val['good_price'].", '".$val['good_note']."', '".$val['send_num']."', '".$val['received_num']."', '".$val['n_good_note']."', '".$val['send_status']."'),";
			}
			
			$sql = rtrim($sql, ',');
			//echo $sql;die;
			try{
	    		$this->db->exec($sql);
	    	}catch(exception $e){
	    		$this->db->exec("ROLLBACK");//返回
	    		return false;
	    	}
	    	
    	}
    	
    	//更新订单主表信息
    	$sql = "SELECT sum(goods_num*good_price) as amt, count(*) as cnt FROM `yy_order_goods` where order_id = ".$order_id;
		$res = $this->db->getRow($sql);
		$amount = $res['amt'] ? intval($res['amt']) : 0;
		$count  = $res['cnt'] ? intval($res['cnt']) : 0;
    	
		$sql = "UPDATE yy_order SET total_amount = ".$amount.", total_num =  ".$count.", update_time = '".$order_title['update_time']."', send_no = '".$order_title['send_no']."', order_time = '".$order_title['order_time']."', jg_date = '".$order_title['order_time']."', send_time = '".$order_title['send_time']."', pay_type = ".$order_title['pay_type'].", processing_time = '".$order_title['processing_time']."' WHERE order_id = ".$order_id;
		//echo $sql;die;
		try{
			$res = $this->db->exec($sql);
		}catch(exception $e){
			$this->db->exec("ROLLBACK");//返回
			return false;
		}
		
		$this->db->exec("COMMIT");
    	return true;

    }
    
    //修改发送状态
    public function update_send_status($order_id=0, $goods_id=0, $status=0)
    {
    	if($this->db == null || empty($order_id) || empty($goods_id))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_order_goods SET send_status = {$status} WHERE order_id = {$order_id} AND goods_id = {$goods_id}";
    	
    	try{
			$res = $this->db->exec($sql);
		}catch(exception $e){
			return false;
		}

    	return true;
    } 
    
    //获取某天的所有下单 客户
    public function get_order_users($date='')
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	$result = array();
    	$sql = "SELECT DISTINCT(order_role_id) as order_role_id FROM yy_order WHERE is_delete = 0 ";
    	if($date)
    	{
    		$start = $date.' 00:00:00';
    		$end = $date.' 23:59:59';
    		$sql .= " AND confirm_time  > '".$start."' AND confirm_time < '".$end."'";
    	}
    	$res = $this->db->getArray($sql);
    	$ids = array();
    	if($res)
    	{
    		foreach($res as $key => $val)
    		{
    			array_push($ids, $val['order_role_id']);
    		}
    		$str_id = implode(',', $ids);
    		$sql = "SELECT * FROM yy_order a left join yy_role b ON a.order_role_id = b.role_id WHERE a.order_role_id IN ({$str_id}) AND a.confirm_time  > '".$start."' AND a.confirm_time < '".$end."'";
    		$result = $this->db->getArray($sql);
    	}
    	return $result ? $result : array();
    }
    
    
    //添加订单
    public function create_user_order($order=array(), $order_goods=array())
    {
    	if($this->db == null || empty($order) || empty($order_goods))
		{
    		return false;
    	}

		$sql = "INSERT INTO yy_order SET ";
		foreach($order as $key => $val)
		{
			$sql .= $key." = '".$val."',";
		}
		$sql .= " create_time = NOW(), update_time = NOW()";
		//echo $sql;die;
		$this->db->exec("START TRANSACTION");
		
		try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
		
		if(!$res)
		{
			$this->db->exec("ROLLBACK");
			return false;
		}
		
		$order_id = $this->db->getLastId();
		
		foreach($order_goods as $key => $val)
		{
			$sql = "INSERT INTO yy_order_goods SET order_id = ".$order_id.", goods_id = ".$val['goods_id'].", goods_num = '".$val['goods_num']."', send_num = '".$val['send_num']."', received_num = '".$val['received_num']."', good_price = ".$val['good_price'].", good_note = '".$val['good_note']."', n_good_note = '".$val['n_good_note']."', send_status = '".$val['send_status']."'";
			
			try{
	    		$res = $this->db->exec($sql);
	    	}catch(exception $e){
	    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
	    		$this->db->exec("ROLLBACK");
				return false;
	    	}
			
			if(!$res)
			{
				$this->db->exec("ROLLBACK");
				return false;
			}
		}
		
		$this->db->exec("COMMIT");
		return true;
    }
    
    //获取销售清单
    public function get_sales_order($param=array())
	{
		if($this->db == null)
		{
    		return false;
    	}
    	
    	$where = " a.is_delete = 0 ";
    	
    	if(!empty($param['operator_id']))
    	{
    		$where .= " AND a.operator_id = ".$param['operator_id'];
    	}
    	
    	if(!empty($param['customer_id']))
    	{
    		$where .= " AND a.order_user_id = ".$param['customer_id'];
    	}
    	
    	if(!empty($param['confirm_time']))
    	{
    		$where .= " AND a.confirm_time >= '".$param['confirm_time']." 00:00:00' AND a.confirm_time <= '".$param['confirm_time']." 23:59:59'";
    	}
    	
    	if(!empty($param['order_time']))
    	{
    		$where .= " AND a.order_time >= '".$param['order_time']." 00:00:00' AND a.order_time <= '".$param['order_time']." 23:59:59'";
    	}
    	
    	if(!empty($param['order_no']))
    	{
    		$where .= " AND a.order_no = '".$param['order_no']."'";
    	}
    	
    	if(!empty($param['order_day_id']))
    	{
    		$where .= " AND a.order_day_id = '".$param['order_day_id']."'";
    	}
    	
    	$sql = "SELECT * FROM yy_order a LEFT JOIN yy_users b ON a.operator_id = b.user_id LEFT JOIN yy_role c ON b.role_id = c.role_id LEFT JOIN yy_order_goods d ON a.order_id = d.order_id LEFT JOIN yy_goods e ON d.goods_id = e.goods_id WHERE {$where} ORDER BY a.order_id DESC";//echo $sql;

    	$res = $this->db->getArray($sql);
    	return $res ? $res : array();	
	}
	
	//更新销售清单
	public function update_sales_order($order_title=array(), $order_goods=array())
	{
		if($this->db == null || empty($order_title) || empty($order_goods))
		{
    		return false;
    	}
    	//订单ID
    	$order_id = $order_title['order_id'];
    	
    	$this->db->exec("START TRANSACTION");//事务开始
    	if(!empty($order_goods))
    	{
    		//先删除、再新增
	    	$sql = "delete from yy_order_goods where order_id = {$order_id}";
	    	try{
	    		$this->db->exec($sql);
	    	}catch(exception $e){
	    		$this->db->exec("ROLLBACK");//返回
	    		return false;
	    	}
	
			//重新增加订单商品
			$sql = "INSERT INTO yy_order_goods(order_id, goods_id, goods_num, good_price, good_note, send_num, received_num, send_status, n_good_note)VALUES";
			
			foreach($order_goods as $key => $val)
			{
				$sql .= "('".$order_id."', '".$val['goods_id']."', '".$val['goods_num']."', '".$val['good_price']."', '".$val['good_note']."', '".$val['send_num']."', '".$val['received_num']."', '".$val['send_status']."', '".$val['n_good_note']."'),";
			}
			
			$sql = rtrim($sql, ',');
			//echo $sql;die;
			try{
	    		$this->db->exec($sql);
	    	}catch(exception $e){
	    		$this->db->exec("ROLLBACK");//返回
	    		return false;
	    	}
	    	
    	}
    	
    	//更新订单主表信息
    	/*
    	$sql = "SELECT sum(goods_num*good_price) as amt, count(*) as cnt FROM `yy_order_goods` where order_id = ".$order_id;
		$res = $this->db->getRow($sql);
		$amount = $res['amt'] ? intval($res['amt']) : 0;
		$count  = $res['cnt'] ? intval($res['cnt']) : 0;
		*/
		$amount = !empty($order_title['total_amount']) ? $order_title['total_amount'] : 0;
		$count = !empty($order_title['total_num']) ? $order_title['total_num'] : 0;
    	
		$sql = "UPDATE yy_order SET total_amount = '".$amount."', total_num =  ".$count.", jg_date = '".$order_title['jg_date']."', send_no = '".$order_title['send_no']."', send_time = '".$order_title['send_time']."', pay_type = ".$order_title['pay_type'].", processing_time = '".$order_title['processing_time']."', order_time = '".$order_title['processing_time']."', update_time = NOW() WHERE order_id = ".$order_id;
		//echo $sql;die;
		try{
			$res = $this->db->exec($sql);
		}catch(exception $e){
			$this->db->exec("ROLLBACK");//返回
			return false;
		}
		
		$this->db->exec("COMMIT");
    	return true;
	}
	
	// 获取订单号
	public function get_all_order_no()
	{
		if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT distinct(order_no) FROM yy_order WHERE is_delete = 0 ORDER BY order_id DESC";
    	
    	$res = $this->db->getArray($sql);
    	
    	return $res;
	}
	
	//获取订单序号
	public function get_all_order_id()
	{
		if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT order_id FROM yy_order WHERE is_delete = 0 ORDER BY order_id DESC";
    	
    	$res = $this->db->getArray($sql);
    	
    	return $res;
	}
	
	//判断order_id是否存在
	public function check_order_id($id, $start, $end, $customer_id)
	{
		if($this->db == null || empty($id) || empty($start) || empty($end) || empty($customer_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT count(*) as 'cnt' FROM yy_order WHERE is_delete = 0 AND order_day_id = ".$id." AND order_user_id = ".$customer_id." AND order_time >= '".$start."' AND  order_time <= '".$end."'";
    	//echo $sql;die;
    	$res = $this->db->getValue($sql);
    	
    	return $res;
	}
	
	//判断订单是否存在
	public function get_day_order_detail($order_user_id=0, $order_day_id=0, $order_time='')
	{
		if($this->db == null || empty($order_user_id) || empty($order_day_id) || empty($order_time))
		{
			return false;
		}
		
		$sql = "SELECT count(*) as cnt FROM yy_order WHERE order_user_id = '".$order_user_id."' AND order_day_id = '".$order_day_id."' AND order_time = '".$order_time."'";
		$res = $this->db->getValue($sql);
		
		return $res ? $res : 0;
	}
	
    
}
?>