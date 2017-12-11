<?php
/**
 * 收货地址
 */
 
class AddressInfo
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
	
	//地址列表
	public function address_list($user_id=0)
	{
		if($this->db == null || empty($user_id))
		{
    		return false;
    	}
		$sql = "SELECT a.address_id,b.name as province_name, c.name as city_name, d.`name` as district_name, a.address, a.is_checked, a.receiver, a.mobile  FROM hg_address a LEFT JOIN hg_region b ON a.province = b.id LEFT JOIN hg_region c ON a.city = c.id LEFT JOIN hg_region d ON a.district = d.id  WHERE user_id = ".$user_id." ORDER BY address_id DESC";
		$result = $this->db->getArray($sql);
		return $result;
	}
	
	
	//插入地址
	public function insert_address($data)
	{
		if($this->db == null || empty($data))
		{
    		return false;
    	}	
    	
    	$sql = "INSERT INTO hg_address SET ";
    	foreach($data as $key => $val)
    	{
    		$sql .= $key." = '".$val."',";
    	}
    	$sql .= "create_time = NOW()";
    	
    	try{
    		$this->db->exec($sql);
    		$last_id = $this->db->getLastId();
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return 0;
    	}
    	
    	return $last_id ? $last_id : 0;
	}
	
	
	
	//生成短信验证码
	public function generate_code($mobile, $code)
	{
		if($this->db == null)
		{
    		return false;
    	}

		$sql = "INSERT INTO hg_sms_code(mobile, code, update_time)VALUES('".$mobile."', $code, NOW())";

		$r = $this->db->exec($sql);
		
		if($r === false) {
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
		
		return $r;
	}
	
	//验证验证码是否正确
	public function validate_code($mobile)
	{
		if($this->db == null)
		{
    		return false;
    	}
		$sql = "SELECT code FROM hg_sms_code WHERE mobile = '".$mobile."' ORDER BY mid DESC LIMIT 1";
		$result = $this->db->getArray($sql);
		return $result;
	}
	
	//更新地址
	public function update_address($param, $address_id)
	{
		if($this->db == null || empty($param) || empty($address_id))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE hg_address SET ";
    	foreach($param as $key => $val)
    	{
    		$sql .= $key." = '".$val."',";
    	}
    	$sql .= " create_time = NOW() WHERE address_id = ".$address_id;
    	//echo $sql;die;
    	try{
    		$this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
    	
    	return true;
	}
	
	//删除地址
	public function delete_addr($address_id)
	{
		if($this->db == null || empty($address_id))
		{
    		return false;
    	}
    	
    	$sql = "delete from hg_address where address_id = ".$address_id;
    	try{
    		$this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
    	
    	return true;
	}
	
	//更新address_id
	public function update_address_id($user_id, $address_id)
	{
		if($this->db == null || empty($address_id) || empty($user_id))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE hg_user SET address_id = {$address_id} WHERE user_id = {$user_id}";
    	try{
    		$this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
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
	    $log->reset()->setPath("modules/Vcode")->setData($data)->write();
	    
	    return false;
	}
}
?>