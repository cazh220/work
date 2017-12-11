<?php
/**
 * 防伪码
 * 
 * @package     modules
 */
 
class ScodeInfo
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


    //获取防伪码详情
    public function get_security_code_detail($code='')
    {
    	if($this->db == null || empty($code))
		{
    		return false;
    	}

    	$sql = "SELECT * FROM hg_security_code WHERE security_code = '".$code."'";
    	$res = $this->db->getRow($sql);

    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
    }
    
    
    //获取关联客户单位
    public function get_company_name($qrcode='')
    {
    	if(empty($qrcode) || $this->db == null)
    	{
    		return false;
    	}
    	
    	$sql = "SELECT company_name FROM hg_stock WHERE code_list = '".$qrcode."'";

    	$res = $this->db->getValue($sql);
    	
    	return $res ? $res : '';
    }
    
    //获取修复体
    public function get_repaire_type($code)
    {
    	if(empty($code) || $this->db == null)
    	{
    		return false;
    	}
    	
    	$sql = "SELECT production_title FROM hg_security_code WHERE security_code = '".$code."'";
    	$res = $this->db->getValue($sql);
    	
    	return $res ? $res : '';
    }
    
	
	/**
	 * 数据更新失败记录日志，并标识操作失败
	 *
	 * @param 	Array 	$data
	 * @return 	bool	false
	 */
	private function _log($data){
	    $log = $this->app->log();
	    $log->reset()->setPath("modules/ScodeInfo")->setData($data)->write();
	    
	    return false;
	}
}
?>