<?php
/**
 * 商城处理类
 * 
 * @package     modules
 */
 
class ShopInfo
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


    //获取商品列表
    public function get_products()
    {
    	if($this->db == null)
		{
    		return false;
    	}

    	$sql = "SELECT * FROM hg_gift WHERE is_delete = 0 ORDER BY gift_id DESC LIMIT 0, 10";

    	$res = $this->db->getArray($sql);

    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
    }
    
    //获取商品列表详情
    public function get_product_list($id='')
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$res = array();
    	if($id)
    	{
    		$sql = "SELECT * FROM hg_gift WHERE gift_id IN ($id)";
    		//echo $sql;die;
    		$res = $this->db->getArray($sql);

	    	if($res === false){
				return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
			}
    	}
    	
    	return $res;
    }
    
    //获取单个商品详情
    public function get_gift_detail($id=0)
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$res = array();
    	if($id)
    	{
    		$sql = "SELECT * FROM hg_gift WHERE gift_id = {$id}";
    		
    		$res = $this->db->getRow($sql);

	    	if($res === false){
				return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
			}
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
	    $log->reset()->setPath("modules/UserInfo")->setData($data)->write();
	    
	    return false;
	}
}
?>