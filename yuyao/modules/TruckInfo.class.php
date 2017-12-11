<?php
/**
 *货车处理
 */
 
class TruckInfo
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
    
    //获取所有货车
    public function get_all_trucks()
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_truck WHERE is_delete = 0";
    	
    	$res = $this->db->getArray($sql);
    	return $res;
    }
    
    //添加货车
    public function add_new_truck($truck_name='', $mobile='')
    {
    	if($this->db == null || empty($truck_name) || empty($mobile))
		{
    		return false;
    	}
    	
    	$sql = "INSERT INTO yy_truck(truck_name, mobile, create_time, update_time)VALUES('".$truck_name."', '".$mobile."', NOW(), NOW())";
    	
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    //获取货车
    public function get_truck($truck_id=0)
    {
    	if($this->db == null || empty($truck_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_truck WHERE truck_id = ".$truck_id;
    	
    	$res = $this->db->getRow($sql);
    	return $res ? $res : array();
    }
    
    //编辑货车
    public function edit_truck($truck_name='', $mobile='', $truck_id='')
    {
    	if($this->db == null || empty($truck_name) || empty($mobile) || empty($truck_id))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_truck SET truck_name = '".$truck_name."', mobile = '".$mobile."' WHERE truck_id = ".$truck_id;
    	
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    //移除货车
    public function remove_truck($truck_id=0)
    {
    	if($this->db == null || empty($truck_id))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_truck SET is_delete = 1 WHERE truck_id = ".$truck_id;
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
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