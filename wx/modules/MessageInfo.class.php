<?php
/**
 * 消息处理类
 * 
 * @package     modules
 */
 
class MessageInfo
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
    
    //消息列表
    public function get_message_list($user_id=0)
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT *,a.create_time as send_time,c.create_time as record_time FROM hg_message a LEFT JOIN hg_user b ON a.from_user_id = b.user_id  LEFT JOIN hg_patient c ON a.qrcode = c.security_code WHERE a.to_user_id = {$user_id} AND a.is_delete = 0 ORDER BY a.id DESC";
    	//echo $sql;die;
    	$res = $this->db->getArray($sql);
	
    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
    }
    
    //未读消息数量
    public function get_unread_count($user_id=0)
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$res = 0;
    	$sql = "SELECT count(*) as num FROM hg_message WHERE to_user_id = {$user_id} AND is_delete = 0 AND is_read = 0";

    	$res = $this->db->getValue($sql);

    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
    }
    
    //写消息
    public function write_message($data=array())
    {
    	if($this->db == null)
		{
    		return false;
    	}

    	$sql  = "INSERT INTO hg_message(type, from_user_id, to_user_id, message, error_info, correct_info, create_time, qrcode)VALUES('".$data['type']."', '".$data['from']."', '".$data['to']."', '".$data['message']."','".$data['error_info']."','".$data['correct_info']."','".$data['create_time']."','".$data['qrcode']."')";
	
		$res = $this->db->exec($sql);
    	
    	if($res === false) {
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	if($res > 0 ){
    		importModule('LogSqs');
			
			$logsqs=new LogSqs;
    		return true;
    		//return $this->db->getLastId();
		}
    	
    	return false;
    }
    
    //获取消息详情
    public function get_message_detail($id)
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM hg_message WHERE id = {$id}";
    	//echo $sql;die;
    	$res = $this->db->getArray($sql);
	
    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
    }
    
    //已读
    public function read_message($id=0)
    {
    	if($this->db == null)
		{
    		return false;
    	}

    	$sql  = "UPDATE hg_message SET is_read = 1 WHERE id = ".$id;
	
		$res = $this->db->exec($sql);
    	
    	if($res === false) {
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	if($res > 0 ){
    		importModule('LogSqs');
			
			$logsqs=new LogSqs;
    		return true;
    		//return $this->db->getLastId();
		}
    	
    	return false;
    }

	
	/**
	 * 数据更新失败记录日志，并标识操作失败
	 *
	 * @param 	Array 	$data
	 * @return 	bool	false
	 */
	private function _log($data){
	    $log = $this->app->log();
	    $log->reset()->setPath("modules/MessageInfo")->setData($data)->write();
	    
	    return false;
	}
}
?>