<?php
/**
 * 区域
 */
 
class AreaInfo
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
	
	//获取省
	public function get_province()
	{
		if($this->db == null)
		{
    		return false;
    	}

		$sql = "SELECT * FROM hg_region WHERE parent_id = 1";

		$r = $this->db->getArray($sql);
		
		if($r === false){
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
		
		if(!is_array($r) || count($r) == 0)
		{
			return false;
		}
		
		return $r;
	}
	
	//获取城市
	public function get_city($province_id)
	{
		$r = array();
		if($province_id)
		{
			if($this->db == null)
			{
				return false;
			}

			$sql = "SELECT * FROM hg_region WHERE parent_id = ".$province_id;

			$r = $this->db->getArray($sql);
			
			if($r === false){
				return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
			}
			
			if(!is_array($r) || count($r) == 0)
			{
				return false;
			}
		}
		return $r;
	}
	//获取区县
	public function get_district($city_id)
	{
		$r = array();
		if(city_id)
		{
			if($this->db == null)
			{
				return false;
			}

			$sql = "SELECT * FROM hg_region WHERE parent_id = ".$city_id;

			$r = $this->db->getArray($sql);
			
			if($r === false){
				return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
			}
			
			if(!is_array($r) || count($r) == 0)
			{
				return false;
			}
		}
		return $r;
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