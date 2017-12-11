<?php
/**
 * 角色用户处理
 */
 
class RoleInfo
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
    
    //获取所有角色用户
    public function get_all_roles($param=array(), $flag=1)
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$where = "is_delete = 0 ";
    	if($flag == 1)
    	{
    		$where .= " AND role_id <> ".ROLE_TOP;
    	}
    	if($param['role_name'])
    	{
    		$where .= " AND role_name LIKE '".$param['role_name']."%'";
    	}
    	
    	if($param['role_id'] &&  $param['role_id'] != ROLE_TOP)
    	{
    		$where .= " AND role_id = ".$param['role_id'];
    	}
    	
    	$sql = "SELECT * FROM yy_role WHERE {$where}";

    	$sql_count = "SELECT count(*) as cnt FROM yy_role WHERE {$where}";
    	$res_count = $this->db->getValue($sql_count);
	
    	if($param['page'] && $param['page_size'])
    	{
    		$start = ($param['page']-1)*$param['page_size'];
    		$page_size = $param['page_size'];
    		$sql .= " ORDER BY role_id DESC LIMIT $start, $page_size";
    	}

    	$res = $this->db->getArray($sql);
    	
    	$return = array(
    		'list'	=> $res ? $res : array(),
    		'count'	=> $res_count ? $res_count : 0
    	);
    	return $return;
    }
    
    //添加新客户分类
    public function add_role_category($param=array())
    {
    	if($this->db == null || empty($param))
		{
    		return false;
    	}
    	
    	$sql = "INSERT INTO yy_role(role_name, create_time, update_time, parent_id, deepth)VALUES('".$param['role_name']."', NOW(), NOW(), ".$param['parent_id'].", ".$param['deepth'].")";
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    
    //添加新角色
    public function add_new_role($role_name='', $parent_id=0)
    {
    	if($this->db == null || empty($role_name))
		{
    		return false;
    	}
    	
    	$sql = "INSERT INTO yy_role(role_name, create_time, update_time, parent_id)VALUES('".$role_name."', NOW(), NOW(), ".$parent_id.")";
    	
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    //获取角色
    public function get_role($role_id=0)
    {
    	if($this->db == null || empty($role_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_role WHERE role_id = ".$role_id;
    	
    	$res = $this->db->getRow($sql);
    	
    	return $res ? $res : array();
    }
    
    
    //编辑
    public function edit_role($role_name='', $role_id=0, $parent_id=0)
    {
    	if($this->db == null || empty($role_id) || empty($role_name) || $role_id == $parent_id)
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_role SET role_name = '".$role_name."', parent_id = ".$parent_id." WHERE role_id = ".$role_id;
    	
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    //删除角色
    public function remove_role($role_id='')
    {
    	if($this->db == null || empty($role_id))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_role SET is_delete = 1 WHERE role_id = ".$role_id;
    	
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	} 
    	
    	return true;
    }
    
    //获取用户的角色详情
    public function get_user_role($user_id=0)
    {
    	if($this->db == null || empty($user_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_users a LEFT JOIN yy_role b ON a.role_id = b.role_id WHERE a.user_id = {$user_id}";
    	$res = $this->db->getRow($sql);
    	return $res ? $res :array();
    }
    
    //获取父级role
    public function get_parent_role($role_id)
    {
    	if($this->db == null || empty($role_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT parent_id FROM yy_role WHERE role_id = {$role_id}";
    	$res = $this->db->getValue($sql);
    	return $res ? $res : 0;
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