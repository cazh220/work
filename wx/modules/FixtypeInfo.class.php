<?php
/**
 * 修复体
 * 
 * @package     modules
 * @author      sam(sam.ma@lyceem.com)
 * @copyright   2010-4-12
 */
 
class FixtypeInfo
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
    
    
    //获取修复体
    public function get_repaire_type()
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM hg_false_tooth WHERE is_delete = 0";
		$r   = $this->db->getArray($sql);
		
		if($r === false){
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	return $r;
    }
    
    //获取修复体详情
    public function get_repaire_type_detail($false_tooth_id)
    {
    	if($this->db == null || empty($false_tooth_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM hg_false_tooth WHERE is_delete = 0 AND false_tooth_id = {$false_tooth_id}";
		$r   = $this->db->getRow($sql);
		
		if($r === false){
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	return $r;
    }
    
    // 获取关连产品明细
    public function get_product_detail($id)
    {
    	if($this->db == null || empty($id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM hg_false_tooth_detail WHERE false_tooth_id = ".$id;
    	$r   = $this->db->getArray($sql);
		
		if($r === false){
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	return $r;
    }
    
    //获取修复体详情
    public function get_fix_type_product_detail($id)
    {
    	if($this->db == null || empty($id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT product_detail FROM hg_false_tooth_detail WHERE id = ".$id;
    	$r   = $this->db->getValue($sql);
		
		if($r === false){
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	return $r ? $r : '';
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