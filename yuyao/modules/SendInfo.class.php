<?php
/**
 *配送处理
 */
 
class SendInfo
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
    
    //获取分车配送信息
    public function get_truck_send_detail($truck_id=0, $send_status=0)
    {
    	if($this->db == null || empty($truck_id))
		{
    		return false;
    	}
    	
    	$where = " is_delete = 0 AND truck = {$truck_id} ";
    	if($send_status)
    	{
    		$where .= " AND send_status = {$send_status}";
    	}
    	
    	$sql = "SELECT * FROM yy_truck_send WHERE {$where}";
    	
    	$res = $this->db->getArray($sql);
    	
    	return $res[0] ? $res[0] : array();
    }
    
    //插入新配货订单
    public function insert_send_order($param=array(), $send_no='', $order_id='')
    {
    	if($this->db == null || empty($param) || empty($send_no) || empty($order_id))
		{
    		return false;
    	}
    	
    	$sql = "INSERT INTO yy_truck_send SET ";
    	foreach($param as $key => $val)
    	{
    		$sql .= $key."='".$val."',";
    	}
    	$sql .= "create_time = NOW(), update_time = NOW()";
		$this->db->exec("START TRANSACTION");
		
		try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	
    	//更新订单的配送号
    	$sql = "UPDATE yy_order SET send_no = '".$send_no."' WHERE order_id = {$order_id}";
    	
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
    
    //更新配送订单
    public function update_send_order($param=array(), $send_no='', $order_id='')
    {
    	if($this->db == null || empty($param) || empty($send_no) || empty($order_id))
		{
    		return false;
    	}
    	
    	//更新订单的配送号
    	$sql = "UPDATE yy_order SET send_no = '".$send_no."' WHERE order_id = {$order_id}";
    	$this->db->exec("START TRANSACTION");
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	
    	//更新配送单
    	$sql = "UPDATE yy_truck_send SET send_num = send_num + ".$param['send_num'].", send_amount = send_amount + ".$param['send_amount'].", operator = '".$param['operator']."', operator_id = ".$param['operator_id']." WHERE send_no = '".$send_no."'";//echo $sql;die;
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
    
    //获取配送详情
    public function get_send_by_send_no($send_no='')
    {
    	if($this->db == null || empty($send_no))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_truck_send WHERE send_no = '".$send_no."' LIMIT 1";//echo $sql;die;
    	$res = $this->db->getArray($sql);
    	
    	return $res[0] ? $res[0] : array();
    }
    
    //获取待发送的配送单
    public function get_assign_order($send_status=0, $page = array())
    {
		{
			if($this->db == null)
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_truck_send WHERE is_delete = 0 ";
    	$sql_count = "SELECT count(*) as count FROM yy_truck_send WHERE is_delete = 0 ";
    	
    	if($status)
    	{
    		$sql .= " AND send_status = {$send_status}";
    		$sql_count .= " AND send_status = {$send_status}";
    	}
    	
    	$count = $this->db->getValue($sql_count);
    	
    	$count = $count ? $count : 0;
    	
    	
    	if($page['current_page'] && $page['page_size'])
    	{
    		$start = ($page['current_page']-1)*$page['page_size'];
    		$page_size = $page['page_size'];
    		$sql .= " ORDER BY send_id DESC LIMIT $start, $page_size";
    	}

    	$res = $this->db->getArray($sql);
    	
    	$return = array('list'=>$res ? $res : array(), 'count'=>$count);
    	return $return ? $return : array();
    	
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
}
?>