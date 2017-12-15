<?php
/**
 * 用户处理
 */
 
class UserInfo
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
    
    //获取所有普通用户
    public function get_user_list($param=array())
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$where = " a.is_delete = 0 AND a.type = 0 ";
    	
    	if(!empty($param['username']))
    	{
    		$where .= " AND a.username LIKE '%".$param['username']."%'";
    	}
    	
    	if($param['role_id'] &&  $param['role_id'] != ROLE_TOP)
    	{
    		$where .= " AND a.role_id = ".$param['role_id'];
    	}
    	
    	$sql = "SELECT * FROM yy_users a left join yy_role b on a.role_id = b.role_id left join yy_truck c on a.truck = c.truck_id WHERE {$where}";//echo $sql;
    	$sql_count = "SELECT count(*) as cnt FROM yy_users a WHERE {$where}";
    	$res_count = $this->db->getValue($sql_count);
    	/*
    	if($param['page'] && $param['page_size'])
    	{
    		$start = ($param['page']-1)*$param['page_size'];
    		$page_size = $param['page_size'];
    		$sql .= " ORDER BY user_id DESC LIMIT $start, $page_size";
    	}*/
    	//echo $sql;die;
    	$res = $this->db->getArray($sql);
    	
    	$return = array(
    		'list'	=> $res ? $res : array(),
    		'count'	=> $res_count ? $res_count : 0
    	);
    	return $return;
    }
    
    //添加新用户
    public function add_new_user($param=array())
    {
    	if($this->db == null || empty($param))
		{
    		return false;
    	}
    	
    	$sql = "INSERT INTO yy_users SET ";
    	foreach($param as $key => $val)
    	{
    		$sql .= $key." = '".$val."',";
    	}
    	$sql .= "create_time = NOW(), update_time = NOW()";
    	//echo $sql;die;
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    //获取用户详情
    public function get_user_detail($user_id = 0)
    {
    	if($this->db == null || empty($user_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_users WHERE user_id = {$user_id}";
    	
    	$res = $this->db->getRow($sql);

    	return !empty($res) ? $res : array();
    }
    
    //获取用户详情
    public function get_user($username = '')
    {
    	if($this->db == null || empty($username))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_users WHERE username = '".$username."'";
    	
    	$res = $this->db->getRow($sql);

    	return !empty($res) ? $res : array();
    }
    
    //更新用户信息
    public function edit_user($param=array(), $user_id=0)
    {
    	if($this->db == null || empty($user_id) || empty($param))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_users SET ";
    	foreach($param as $key => $val)
    	{
    		$sql .= $key." = '".$val."',";
    	}
    	
    	$sql .= ' update_time = NOW() WHERE user_id = '.$user_id;
    	//echo $sql;die;
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    //更新用户信息
    public function user_edit($param=array(), $username='')
    {
    	if($this->db == null || empty($username) || empty($param))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_users SET ";
    	foreach($param as $key => $val)
    	{
    		$sql .= $key." = '".$val."',";
    	}
    	
    	$sql .= " update_time = NOW() WHERE username = '".$username."'";
    	//echo $sql;die;
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    //移除用户
    public function remove_user($user_id=0)
    {
    	if($this->db == null || empty($user_id))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_users SET is_delete = 1 WHERE user_id = ".$user_id;
    	
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    //获取管理员
    public function get_admin_list()
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_users a left join yy_role b on a.role_id = b.role_id left join yy_truck c on a.truck = c.truck_id WHERE a.is_delete = 0 AND a.type = 1";
    	$res = $this->db->getArray($sql);
    	return $res;
    }
    
    //验证登录
    public function check_login($username='', $password='')
    {
    	if($this->db == null ||  empty($username) || empty($password))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_users WHERE username = '".$username."' AND password = '".md5($password)."'";
    	
    	$res = $this->db->getArray($sql);
    	return $res;
    }
    
    //获取登录信息
    public function get_user_info($username='', $password='')
    {
    	if($this->db == null ||  empty($username) || empty($password))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_users WHERE username = '".$username."' AND password = '".md5($password)."'";
    	$res = $this->db->getRow($sql);
    	
    	return $res ? $res : array();
    }
    
    //检查是否存在
    public function check_user_id($user_id=0, $password='')
    {
    	if($this->db == null ||  empty($user_id) || empty($password))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_users WHERE user_id = {$user_id} AND password = '".md5($password)."'";
    	
    	$res = $this->db->getArray($sql);
    	return $res ? $res : array();
    }
    
    //修改密码
    public function doUpdatePwd($user_id=0, $password='')
    {
    	if($this->db == null ||  empty($user_id) || empty($password))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_users SET password = '".md5($password)."' WHERE user_id = ".$user_id;
    	
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    //获取角色的用户
    public function get_role_users($role_id=0)
    {
    	$res = array();
    	if($this->db == null ||  empty($role_id))
		{
    		return false;
    	}
    	$sql = "SELECT * FROM yy_users WHERE role_id = ".$role_id;
    	$res = $this->db->getArray($sql);
    	
    	return $res;
    }
    
    //更新登录ip
    public function update_client_ip($user_id=0, $ip='')
    {
    	$res = array();
    	if($this->db == null ||  empty($user_id))
		{
    		return false;
    	}
    	$sql = "UPADTE yy_users SET login_ip = '".$ip."' WHERE user_id = ".$user_id;
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