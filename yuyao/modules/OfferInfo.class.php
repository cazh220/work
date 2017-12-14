<?php
/**
 *报价
 */
 
class OfferInfo
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
    
    //更新用户商品报价
    public function update_user_good_price($param)
    {
    	if($this->db == null || empty($param))
		{
    		return false;
    	}
    	
    	//判断是否存在如存在则更新否则插入
    	$sql = "SELECT * FROM yy_offer WHERE goods_id = ".$param['goods_id']." AND user_id = ".$param['user_id']." AND role_id = ".$param['role_id']." AND is_delete = 0";
    	$res = $this->db->getArray($sql);
    	
    	if($res)
    	{
    		//更新
    		$sql = "UPDATE yy_offer SET price = ".$param['price'].", update_time = NOW() WHERE goods_id = ".$param['goods_id']." AND user_id = ".$param['user_id']." AND role_id = ".$param['role_id']." AND is_delete = 0";
    	}	
    	else
    	{
    		//插入
    		$sql = "INSERT INTO yy_offer SET ";
			foreach($param as $key => $val)
			{
				$sql .= $key." = '".$val."',";
			}
			$sql .= " create_time = NOW(), update_time = NOW()";
    		
    	}
    	//开启事务
    	$this->db->exec("START TRANSACTION");
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	
    	$this->db->exec("COMMIT");
		return true;
    }
    
    //更新用户分类的报价
    public function update_role_good_price($param)
    {
    	if($this->db == null || empty($param))
		{
    		return false;
    	}
    	
    	//先清掉所有user和role的报价
    	$sql = "DELETE FROM yy_offer WHERE role_id = ".$param['role_id']. " AND is_delete = 0";
    	//开启事务
    	$this->db->exec("START TRANSACTION");
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	//插入分类的报价
    	//插入
		$sql = "INSERT INTO yy_offer SET ";
		foreach($param as $key => $val)
		{
			$sql .= $key." = '".$val."',";
		}
		$sql .= " create_time = NOW(), update_time = NOW()";

		try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		$this->db->exec("ROLLBACK");
			return false;
    	}
    	
    	$this->db->exec("COMMIT");
		return true;
    }
    
    //获取商品报价
    public function get_good_offer_price($goods_id, $user_id=0, $role_id=0)
    {
    	if($this->db == null || empty($goods_id))
		{
    		return false;
    	}
    	
    	//优先获取用户价格
    	if($user_id)
    	{
    		$sql = "SELECT * FROM yy_offer WHERE goods_id = ".$goods_id." AND user_id = ".$user_id." AND is_delete = 0";
	    	$user_price = $this->db->getRow($sql);
	    	if($user_price)
	    	{
	    		return !empty($user_price['price']) ? $user_price['price'] : 0;
	    	}
    	}
    	
    	
    	//获取分类的价格
    	$sql = "SELECT * FROM yy_offer WHERE goods_id = ".$goods_id." AND role_id = ".$role_id." AND is_delete = 0";
    	$role_price = $this->db->getRow($sql);
    	if($role_price)
    	{
    		return !empty($role_price['price']) ? $role_price['price'] : 0;
    	}
    	
    	//获取商品基础报价
    	$sql = "SELECT * FROM yy_goods WHERE is_delete = 0 AND goods_id = ".$goods_id;
    	$good_price = $this->db->getRow($sql);
    	if($good_price)
    	{
    		return !empty($good_price['price']) ? $good_price['price'] : 0;
    	}
    } 
    
    

	/**
	 * 数据更新失败记录日志，并标识操作失败
	 *
	 * @param 	Array 	$data
	 * @return 	bool	false
	 */
	private function _log($data){
	    $log = $this->app->log();
	    $log->reset()->setPath("modules/OfferInfo")->setData($data)->write();
	    
	    return false;
	}
}
?>