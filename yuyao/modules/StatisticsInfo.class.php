<?php
/**
 * 订单信息统计
 */
 
class StatisticsInfo
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
    
    //获取日订单产品统计
    public function get_day_goods($date='', $category_id=0, $order_user_id=0, $order_truck_id=0, $user_id = 0)
    {
    	if($this->db == null || empty($date))
		{
    		return false;
    	}
    	
    	$start 	= $date.' 00:00:00';
    	$end	= $date.' 23:59:00';

    	$sql = "SELECT e.role_id, e.role_name, b.goods_id,b.goods_num,b.good_price,c.goods_name,c.unit,b.good_note,a.order_role_id,a.order_truck_id,a.operator_id, a.operator,a.order_user_id,a.order_user_name,c.stock FROM yy_order a LEFT JOIN yy_order_goods b ON a.order_id = b.order_id LEFT JOIN yy_goods c ON b.goods_id = c.goods_id LEFT JOIN yy_users d ON a.operator_id = d.user_id LEFT JOIN yy_role e ON d.role_id = e.role_id WHERE a.is_delete = 0 AND a.order_time >='".$start."' AND a.order_time <= '".$end."'";
    	
    	if(!empty($category_id) && $category_id != 1)
    	{
    		//获取分类的所有子类
    		$sql_cat = "SELECT cid FROM yy_category WHERE (parent_id = {$category_id} OR cid = {$category_id})";
    		$res_cat = $this->db->getArray($sql_cat);
    		
    		$cat_ids = array();
    		$cat_set = '';
    		if($res_cat)
    		{
    			foreach($res_cat as $k => $v)
    			{
    				array_push($cat_ids, $v['cid']);
    			}
    			$cat_set = implode(',', $cat_ids);
    		}
    		
    		if(!empty($cat_set))
    		{
    			$sql .= " AND c.category_id IN ({$cat_set})";
    		}
    		
    	}
    	/*
    	if(!empty($order_role_id))
    	{
    		$sql .= " AND a.order_role_id = ".$order_role_id;
    	}*/
    	if(!empty($order_user_id))
    	{
    		$sql .= " AND a.operator_id = ".$order_user_id;
    	}		
    	
    	if(!empty($user_id))
    	{
    		$sql .= " AND a.order_user_id = ".$user_id;
    	}
    	
    	if(!empty($order_truck_id))
    	{
    		$sql .= " AND a.order_truck_id = ".$order_truck_id;
    	}//echo $sql;
    	$res = $this->db->getArray($sql);
    	return $res ? $res : array();
    }
    
    //分车筛选   已发送不统计
    public function get_day_unsend_goods($date='', $category_id=0, $order_user_id=0, $order_truck_id=0, $user_id = 0, $order_role_id=0)
    {
    	if($this->db == null || empty($date))
		{
    		return false;
    	}
    	
    	$start 	= $date.' 00:00:00';
    	$end	= $date.' 23:59:00';

    	$sql = "SELECT e.role_id, e.role_name, b.goods_id,b.goods_num,b.good_price,c.goods_name,c.unit,b.good_note,a.order_role_id,a.order_truck_id,a.operator_id, a.operator,a.order_user_id,a.order_user_name FROM yy_order a LEFT JOIN yy_order_goods b ON a.order_id = b.order_id LEFT JOIN yy_goods c ON b.goods_id = c.goods_id LEFT JOIN yy_users d ON a.operator_id = d.user_id LEFT JOIN yy_role e ON d.role_id = e.role_id WHERE a.is_delete = 0 AND b.send_status = 0 AND a.order_time >='".$start."' AND a.order_time <= '".$end."'";
    	
    	if(!empty($category_id) && $category_id != 1)
    	{
    		//获取分类的所有子类
    		$sql_cat = "SELECT cid FROM yy_category WHERE (parent_id = {$category_id} OR cid = {$category_id})";
    		$res_cat = $this->db->getArray($sql_cat);
    		
    		$cat_ids = array();
    		$cat_set = '';
    		if($res_cat)
    		{
    			foreach($res_cat as $k => $v)
    			{
    				array_push($cat_ids, $v['cid']);
    			}
    			$cat_set = implode(',', $cat_ids);
    		}
    		
    		if(!empty($cat_set))
    		{
    			$sql .= " AND c.category_id IN ({$cat_set})";
    		}
    		
    	}
    	
    	if(!empty($order_role_id))
    	{
    		$sql .= " AND a.order_role_id = ".$order_role_id;
    	}
    	if(!empty($order_user_id))
    	{
    		$sql .= " AND a.operator_id = ".$order_user_id;
    	}		
    	
    	if(!empty($user_id))
    	{
    		$sql .= " AND a.order_user_id = ".$user_id;
    	}
    	
    	if(!empty($order_truck_id))
    	{
    		$sql .= " AND a.order_truck_id = ".$order_truck_id;
    	}//echo $sql;die;
    	$res = $this->db->getArray($sql);
    	return $res ? $res : array();
    }
    
    //月统计
    public function get_month_order($month='')
    {
    	if($this->db == null || empty($month))
		{
    		return false;
    	}
    	
    	//获取统计信息
    	$start = date("Y-m-01 00:00:00", strtotime($month));
    	$end = date('Y-m-d 23:59:59', strtotime("$start +1 month -1 day"));
    	
    	$sql = "SELECT total_amount, total_num, order_user_id, order_user_name, order_time FROM yy_order WHERE is_delete = 0 AND order_time >= '".$start."' AND order_time < '".$end."'";//echo $sql;die;
    	$res = $this->db->getArray($sql);
    	
    	return $res ? $res : array();
    }
    
   
		
}
?>