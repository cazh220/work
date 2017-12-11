<?php
/**
 * 分类处理
 */
 
class CategoryInfo
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
    
    //获取分类列表
    public function get_categoty_list($ctype=0)
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_category WHERE is_delete = 0 AND ctype = {$ctype} ORDER BY sort ASC";
    	
    	$r = $this->db->getArray($sql);
    	
    	return $r;
    }
    
    //获取分类详情
    public function get_category_info($cid=0)
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_category WHERE cid = {$cid}";
    	
    	$r = $this->db->getRow($sql);
    	
    	return $r;
    }
    
    //添加分类
    public function add_new_category($param=array())
    {
    	if($this->db == null || empty($param))
		{
    		return false;
    	}
    
    	$sql = "INSERT INTO yy_category SET ";
    	foreach($param as $key => $value)
    	{
    		$sql .= $key." = '".$value."',";
    	}
    	$sql .= "create_time = NOW(), update_time = NOW()";
    	
    	try{
    		$res = $this->db->exec($sql);
    		return true;
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}

    }
    
    //编辑分类
    public function edit_category($param=array(),$cid=0)
    {
    	if($this->db == null || empty($param))
		{
    		return false;
    	}
    
    	$sql = "UPDATE yy_category SET ";
    	foreach($param as $key => $value)
    	{
    		$sql .= $key." = '".$value."',";
    	}
    	$sql .= "update_time = NOW() WHERE cid = ".$cid;
    	try{
    		$res = $this->db->exec($sql);
    		return true;
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
    }
    
    //删除
   public function delete_category($param)
   {
		if($this->db == null || empty($param))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_category SET is_delete = 1 WHERE cid = ".$param['cid'];
    
    	try{
    		$res = $this->db->exec($sql);
    		return true;
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
   }
   
   //分区二级分类
   public function get_deep_category($deepth=0)
   {
   		if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_category WHERE deepth = {$deepth}";
    	
    	$res = $this->db->getArray($sql);
    	return $res;
   }
   
   //获取下属分类
   public function get_sub_category($pid=0)
   {
   		if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT cid FROM yy_category WHERE parent_id = {$pid}";
    	
    	$res = $this->db->getArray($sql);
    	
    	return $res;
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