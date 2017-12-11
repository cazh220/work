<?php
/**
 * 积分处理
 * 
 * @package     modules
 * @author      sam(sam.ma@lyceem.com)
 * @copyright   2010-4-12
 */
 
class CreditInfo
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
    
    
    //获取积分设置
    public function get_credits_list()
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM hg_credit";
		$r   = $this->db->getArray($sql);
		
		if($r === false){
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	return $r;
    }
    
    
    //更新积分
    public function update_credits($user_id=0, $score=0)
    {
    	if($this->db == null || empty($user_id))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE hg_user SET total_credits = total_credits + ".$score.", left_credits = left_credits + ".$score." WHERE user_id = ".$user_id;
    	
    	try
    	{
    		$res = $this->db->exec($sql);
    	}
    	catch(exception $e)
    	{
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
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
	    $log->reset()->setPath("modules/CreditInfo")->setData($data)->write();
	    
	    return false;
	}
}
?>