<?php
/**
 *反馈
 */
 
class FeedbackInfo
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
    
    //写入反馈
    public function insert_feedback($data=array())
    {
    	if($this->db == null || empty($data))
		{
    		return false;
    	}
    	
    	$sql = "INSERT INTO yy_feedback SET ";
    	foreach($data as $key => $val)
    	{
    		$sql .= $key." = '".$val."',";
    	}
    	$sql .= "create_time = NOW()";
    	//$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
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
	    $log->reset()->setPath("modules/Feddback")->setData($data)->write();
	    
	    return false;
	}
}
?>