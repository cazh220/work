<?php
/**
 * 短信验证码
 */
 
class SmsCode
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