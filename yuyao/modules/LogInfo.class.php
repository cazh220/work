<?php 
/**
 * 日志处理类
 * 
 * @package     modules
 * @author      鲍(chenglin.bao@lyceem.com)
 * @copyright   2010-11-01 
 */

class LogInfo {
	/**
	 * 应用程序对象
	 * @var Application
	 */
	private $app = null;
	
	/**
	 * 数据库操作对象
	 * @var OrmQuery
	 */
	private $store_db = null;
	
	
 	/**
     * 构造函数，获取数据库连接对象
     *
     */
    public function __construct(){
        global $app;
        
        $this->app = $app;
        
        $this->store_db = $app->orm($app->cfg['store_db'])->query();
		
        mysql_query("set names utf8");
    }
    
    /**
     * 记录用户操作日志
     * 
     * @param string $log_info 日志信息
     * @return bool
     */
    public function AddLog($log_info) {
   		if($this->store_db == null) return false;
   		
   		$i_adminid = $_SESSION['user_id'] ? (int)$_SESSION['user_id'] : 3;
		
		//获取客户端ip
		import('util.Ip');
		$obj_ip = new Ip;
		
		$s_ip = $obj_ip->get();
		
		$s_ip = $s_ip ? $s_ip : $_SERVER['REMOTE_ADDR'];
   		
   		$sql = 'INSERT INTO admin_log (log_time, user_id, log_info, ip_address)' .
            " VALUES ('".time()."', ".$i_adminid.", '".stripslashes($log_info)."','".$s_ip."')";
   		
   		$res = $this->store_db->exec($sql);
   		
   		if($res === false || $res == 0){
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . 'execute select faile.'.$sql, date("Y-m-d H:i:s")));
    	}
		
    	return true;
    }
    
    /**
     * 获取日志总记录数
     * 
     * @param  string  $s_where
     * @return int|bool
     */
    public function getLogNum($s_where) {
    	if($this->store_db == null) return false;
    	
    	if(empty($s_where)) return false;
    	
    	$sql = "SELECT COUNT(l.log_id) FROM admin_log l $s_where";
    	$res = $this->store_db->getValue($sql);
    	
   		if($res === false || $res == 0){
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . 'execute select faile.'.$sql, date("Y-m-d H:i:s")));
    	}
    	return $res;
    }
    
    /**
     * 分页查询日志记录
     * 
     * @param string $s_where
     * @return array|bool
     */
    public function getLogInfo($s_where) {
    	if($this->store_db == null) return false;
    	
    	if(empty($s_where)) return false;
    	
    	$sql = "SELECT l.log_id,l.log_time,l.log_info,l.ip_address,u.user_name FROM admin_log l,admin_user u $s_where";
    	$res = $this->store_db->getArray($sql);
    	
   		if($res === false){
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . 'execute select faile.'.$sql, date("Y-m-d H:i:s")));
    	}
    	
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
	    $log->reset()->setPath("modules/LogInfo")->setData($data)->write();
	    
	    return false;
	}
}
?>