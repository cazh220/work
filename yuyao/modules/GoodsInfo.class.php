<?php
/**
 * 商品处理
 */
 
class GoodsInfo
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
    public function get_goods_list($param)
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$where = ' a.is_delete = 0 AND a.is_show = 0';
    	if($param['category_id'])
    	{
    		$where .= " AND a.category_id = ".$param['category_id'];
    	}
    	
    	if($param['goods_name'])
    	{
    		$where .= " AND goods_name LIKE '%".$param['goods_name']."%'";
    	}
    	
    	if(!empty($param['ids']))
    	{
    		$where .= " AND goods_id NOT IN (".implode(',', $param['ids']).")";
    	}
    	
    	
    	$sql = "SELECT * FROM yy_goods a LEFT JOIN yy_category b ON a.category_id = b.cid WHERE {$where}  ORDER BY goods_id DESC LIMIT 10";//echo $sql;die;
    	
    	$sql_count = "SELECT COUNT(*) as cnt FROM yy_goods a LEFT JOIN yy_category b ON a.category_id = b.cid WHERE {$where}";
    	
    	$res_count = $this->db->getValue($sql_count);
    	/*
    	if($param['page'] && $param['page_size'])
    	{
    		$start = ($param['page']-1)*$param['page_size'];
    		$page_size = $param['page_size'];
    		$sql .= " LIMIT {$start}, {$page_size}";
    	}*/
    	//echo $sql;die;
    	$res = $this->db->getArray($sql);
    	
    	$data = array(
    		'list'=> $res ? $res : array(),
    		'count'	=> $res_count ? $res_count : 0
    	);
    	return $data;
    }
    
    //获取商品详情
    public function get_good_detail($goods_id=0)
    {
    	if($this->db == null || empty($goods_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT * FROM yy_goods WHERE goods_id = {$goods_id}";
    	
    	$res = $this->db->getRow($sql);
    	return $res;
    }
    
    //获取价格
    public function get_good_price($goods_id=0)
    {
    	if($this->db == null || empty($goods_id))
		{
    		return false;
    	}
    	
    	$sql = "SELECT price FROM yy_goods WHERE goods_id = {$goods_id}";
    	
    	$res = $this->db->getValue($sql);
    	return $res;
    }
    
    
    //添加商品
    public function add_new_good($param)
    {
    	if($this->db == null || empty($param))
		{
    		return false;
    	}
    	
    	$sql = "INSERT INTO yy_goods SET ";
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
    
    
    //编辑商品
    public function update_good($param=array(), $goods_id=0)
    {
    	if($this->db == null || empty($param) || empty($goods_id))
		{
    		return false;
    	}
    
    	$sql = "UPDATE yy_goods SET ";
    	foreach($param as $key => $value)
    	{
    		$sql .= $key." = '".$value."',";
    	}
    	$sql .= "update_time = NOW() WHERE goods_id = ".$goods_id;
    	try{
    		$res = $this->db->exec($sql);
    		return true;
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
    }
    
    //删除商品
   public function delete_good($goods_id=0)
   {
		if($this->db == null || empty($goods_id))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_goods SET is_delete = 1 WHERE cid = ".$goods_id;
    
    	try{
    		$res = $this->db->exec($sql);
    		return true;
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
   }
   
    //查询报价
    public function get_offer_info($goods_id=0)
    {
    	if($this->db == null || empty($goods_id))
		{
    		return false;
    	}
    	
    	//$sql = "SELECT * FROM yy_offer WHERE is_delete = 0 AND start_time <= NOW() AND end_time >= NOW() AND goods_id = {$goods_id} ORDER BY offer_id DESC LIMIT 1";
    	$sql = "SELECT * FROM yy_offer WHERE is_delete = 0 AND goods_id = {$goods_id} ORDER BY offer_id DESC LIMIT 1";
    	try{
    		$res = $this->db->getArray($sql);
    	}
    	catch(exception $e)
    	{
    		echo $e->getMessage();
    	}
    	
    	return !empty($res[0]) ? $res[0] : array();
    }
    
    //更新报价
    public function update_role_price($param)
    {
    	if($this->db == null || empty($param))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_offer SET is_delete = 1 WHERE goods_id = ".$param['goods_id']." AND user_id = ".$param['user_id'];
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
    	
    	$sql = "INSERT INTO yy_offer(goods_id, role_id, price, user_id, create_time, update_time, operator_id, operator)VALUES(".$param['goods_id'].",".$param['role_id'].", ".$param['price'].", ".$param['user_id'].",NOW(), NOW(), ".$param['operator_id'].",'".$param['operator']."')";//echo $sql;die;
    	try{
    		$res = $this->db->exec($sql);
    		return true;
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
    }
    
    //更新用户用户
    public function update_role_good_price($param)
    {
    	if($this->db == null || empty($param))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_offer SET is_delete = 1 WHERE goods_id = ".$param['goods_id']." AND role_id = ".$param['role_id'];
    	try{
    		$res = $this->db->exec($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
    	
    	//获取用户
    	$sql = "SELECT * FROM yy_users WHERE role_id = ".$param['role_id'];
    	$res = $this->db->getArray($sql);
    	
    	if ($res)
    	{
    		foreach($res as $key => $val)
    		{
    			$sql = "INSERT INTO yy_offer(goods_id, role_id, price, user_id, create_time, update_time, operator_id, operator)VALUES(".$param['goods_id'].",".$param['role_id'].", ".$param['price'].", ".$val['user_id'].",NOW(), NOW(), ".$param['operator_id'].",'".$param['operator']."')";//echo $sql;die;
		    	try{
		    		$res = $this->db->exec($sql);
		    		return true;
		    	}catch(exception $e){
		    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		    		return false;
		    	}
		    	
    		}
    	}


    }
    
    //获取报价商品
    public function get_offer_list($param)
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$where = ' a.is_delete = 0 AND a.is_show = 0';
    	if($param['category_id'])
    	{
    		$where .= " AND a.category_id = ".$param['category_id'];
    	}
    	
    	if($param['goods_name'])
    	{
    		$where .= " AND goods_name LIKE '%".$param['goods_name']."%'";
    	}
    	
    	$sql = "SELECT * FROM yy_goods a LEFT JOIN yy_category b ON a.category_id = b.cid WHERE {$where} ORDER BY goods_id DESC";
    	
    	$sql_count = "SELECT count(*) as count FROM yy_goods a LEFT JOIN yy_category b ON a.category_id = b.cid WHERE {$where}";
    	$res_count = $this->db->getValue($sql_count);
    	/*
    	if($param['page'] && $param['page_size'])
    	{
    		$start = ($param['page']-1)*$param['page_size'];
    		$page_size = $param['page_size'];
    		$sql .= " LIMIT {$start}, {$page_size}";
    	}*/
    	//echo $sql;die;
    	$res = $this->db->getArray($sql);
    	
    	$data = array();
    	
    	$data['list'] = !empty($res) ? $res : array();
    	$data['count'] = !empty($res_count)  ? $res_count : 0;
    	
    	return $data;
    }
   
   	//删除商品
   	public function delete_goods($goods_ids='')
   	{
   		if($this->db == null || empty($goods_ids))
		{
    		return false;
    	}
    	
    	$sql = "UPDATE yy_goods SET is_delete = 1 WHERE goods_id IN (".$goods_ids.")";

    	try{
    		$res = $this->db->exec($sql);
    		return true;
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
   	}
   	
   	//获取用户特定的报价
   	public function get_role_good_price($role_id=0, $goods_id=0)
   	{
   		if($this->db == null || empty($role_id) || empty($goods_id))
		{
    		return false;
    	}
    	$sql = "SELECT price FROM yy_offer WHERE role_id = ".$role_id." AND goods_id = ".$goods_id." ORDER BY offer_id DESC";
    	
    	try{
    		$res = $this->db->getValue($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	return $res ? $res : 0;

   	}
   	
   	//获取指定用户商品报价详情
   	public function get_role_price_detail($role_id=0, $goods_id=0, $user_id =0)
   	{
   		if($this->db == null || empty($role_id) || empty($goods_id))
		{
    		return false;
    	}
    	
    	if(!empty($user_id))
    	{
    		$sql = "SELECT * FROM yy_offer WHERE user_id = ".$user_id." AND goods_id = ".$goods_id." ORDER BY offer_id DESC";
    	}
    	else
    	{
    		$sql = "SELECT * FROM yy_offer WHERE role_id = ".$role_id." AND goods_id = ".$goods_id." ORDER BY offer_id DESC";
    	}
    	
    	try{
    		$res = $this->db->getRow($sql);
    	}catch(exception $e){
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' err:'.$e->getMessage().'  sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	return $res ? $res : array();
   	}
   	
   	//获取分类下所有商品
   	public function get_cat_goods($cid='')
   	{
   		if($this->db == null)
		{
    		return false;
    	}
    	
    	if(empty($cid))
    	{
    		return array();
    	}
    	
    	$sql = "SELECT * FROM yy_goods";
    	if($cid)
    	{
    		$sql .= " WHERE category_id IN ($cid)";
    	}
    	$res = $this->db->getArray($sql);
    	return $res ? $res : array();
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