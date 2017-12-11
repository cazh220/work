<?php
/**
 * 病人处理类
 * 
 * @package     modules
 */
 
class PatientInfo
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

    //插入病人
    public function insert_patient($data)
    {
    	if($this->db == null)
		{
    		return false;
    	}
		
		$this->db->exec("START TRANSACTION");
    	$sql  = "INSERT INTO hg_patient(name, sex, birthday, hospital, doctor, tooth_position, production_unit, create_time, operator, operate_user_id, tech_id, false_tooth, repairosome_pic, security_code, mobile, email, update_time, product_detail_id)VALUES('".$data['name']."', '".$data['sex']."', '".$data['birthday']."', '".$data['hospital']."', '".$data['doctor']."','".$data['tooth_position']."', '".$data['production_unit']."','".$data['create_time']."', '".$data['operator']."', '".$data['user_id']."', '".$data['user_id']."', '".$data['false_tooth']."', '".$data['repaire_pic']."','".$data['security_code']."','".$data['mobile']."','".$data['email']."','".$data['update_time']."','".$data['product_detail_id']."')";
		$res = $this->db->exec($sql);
    	if($res === false) {
    		$this->db->exec("ROLLBACK");
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
    	
    	//增加积分
    	$sql = "UPDATE hg_user SET total_credits = total_credits + ".$data['credits'].", left_credits = left_credits + ".$data['credits']." WHERE user_id = ".$data['user_id'];
    	
    	$res = $this->db->exec($sql);
    	
    	if($res === false) {
    		$this->db->exec("ROLLBACK");
    		$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    		return false;
    	}
    	
    	//写入增加积分日志
    	$admin_id = $data['user_id'];
    	$user_id = $data['user_id'];
    	$username = $data['name'];
    	$content = '增加积分：'.$data['credits'];
    	$ip = $_SERVER['REMOTE_ADDR'];
    	
    	$sql = "INSERT INTO hg_user_actions(admin_id,user_id,username,content,action_create_time,ip,channel)VAlUES($admin_id,$user_id,'".$username."','".$content."',NOW(),'".$ip."',1)";
    	
    	try{
    		$this->db->exec($sql);
    	}catch(exception $e){
    		$this->db->exec("ROLLBACK");
    		return false;
    	}

    	$this->db->exec("COMMIT");
    	return true;
    }
    
    //更新病人
    public function update_patient($data)
    {
    	if($this->db == null)
		{
    		return false;
    	}
    	
    	$sql = "UPDATE hg_patient SET name='".$data['patient_name']."', sex='".$data['sex']."',birthday='".$data['patient_age']."',hospital='".$data['hospital']."',doctor='".$data['doctor']."',tooth_position='".$data['tooth_position']."', false_tooth='".$data['false_tooth']."', product_detail_id='".$data['product_detail_id']."'";
    	if($data['repaire_pic'])
    	{
    		$sql .= ", repairosome_pic='".$data['repaire_pic']."'";
    	}
    	$sql .= " WHERE security_code = '".$data['security_code']."'";
    	$res = $this->db->exec($sql);

    	//$sql  = "UPDATE hg_patient SET name='".$data['patient_name']."', sex='".$data['sex']."',birthday='".$data['patient_age']."',hospital='".$data['hospital']."',doctor='".$data['doctor']."',tooth_position='".$data['tooth_position']."', false_tooth='".$data['false_tooth']."', repairosome_pic='".$data['repaire_pic']."' WHERE security_code = '".$data['security_code']."'";
    	//echo $sql;die;
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
    
    
    //医生确认
    public function confirm_patient($data, $where)
    {
    	if($this->db == null || empty($data) || empty($where))
		{
    		return false;
    	}
    	$sql  = "UPDATE hg_patient SET ";
    	foreach($data as $key => $val)
    	{
    		$sql .= $key."='".$val."',";
    	}
    	$sql = rtrim($sql, ',');
    	$sql .= " WHERE patient_id = ".$where['patient_id'];

		/*
    	$sql  = "UPDATE hg_patient SET  repairosome_pic ='".$data['case_pic']."', confirm_time = '".$data['confirm_time']."',doctor_id = '".$data['doctor_id']."' WHERE patient_id = '".$data['patient_id']."'";
    	*/
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
    
    //更新积分
    public function update_credits($user_id, $credist=0)
    {
    	if($this->db == null)
		{
    		return false;
    	}

    	$sql  = "UPDATE hg_user SET  total_credits = total_credits + {$credist}, left_credits = left_credits + {$credist} WHERE user_id = {$user_id}";
    	//echo $sql;die;
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
    
    //获取患者信息通过patient_id
    public function get_patient_by_id($patient_id=0)
    {
    	$res = array();
    	if(!empty($patient_id))
    	{
    		if($this->db == null){
	    		return false;
	    	}
	    	
	    	$sql = "SELECT * FROM hg_patient a LEFT JOIN hg_false_tooth b ON a.false_tooth = b.false_tooth_id WHERE patient_id = ".$patient_id;
	    	$res = $this->db->getRow($sql);
	    	if($res === false){
				return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
			}
    	}
    	return $res;
    }    
    
    //获取患者信息
    public function get_patient($qrcode='')
    {
    	if ($qrcode)
    	{
    		if($this->db == null){
	    		return false;
	    	}
	
	    	$sql = "SELECT * FROM hg_patient a LEFT JOIN hg_false_tooth b ON a.false_tooth = b.false_tooth_id WHERE security_code = '".$qrcode."'";
	    	$res = $this->db->getArray($sql);
	    	if($res === false){
				return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
			}
			
			return $res;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    //获取患者列表
    public function patient_list($data)
    {
    	if($this->db == null || empty($data)){
    		return false;
    	}
    	$sql = "SELECT user_type FROM hg_user WHERE user_id = ".$data['operate_user_id'];
    	$user_type = $this->db->getValue($sql);
    	$sql = "SELECT * FROM hg_patient WHERE is_delete = 0";
    	if($user_type ==1)
    	{
    		$sql .= " AND operate_user_id = ".$data['operate_user_id'];
    	}
    	else
    	{
    		$sql .= " AND doctor_id = ".$data['operate_user_id'];
    	}
		if($data['hospital'])
		{
			$sql .= " AND hospital LIKE '%".$data['hospital']."%'";
		}
		$sql .= " ORDER BY patient_id DESC ";
		$start = ($data['page']-1)*$data['page_size'];
		$page_size = $data['page_size'];
		$sql .= " LIMIT $start,$page_size";
		
    	$res = $this->db->getArray($sql);

    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
    }
    
    
    //获取患者列表
    public function patient_list_search($data)
    {
    	if($this->db == null || empty($data)){
    		return false;
    	}
    	$sql = "SELECT user_type FROM hg_user WHERE user_id = ".$data['operate_user_id'];
    	$user_type = $this->db->getValue($sql);
    	$sql = "SELECT * FROM hg_patient ";
    	$sql_count = "SELECT count(*) as 'cnt' FROM hg_patient ";
    	
    	$where = " WHERE is_delete = 0 ";
    	if($user_type ==1)
    	{
    		$where .= " AND operate_user_id = ".$data['operate_user_id'];
    	}
    	else
    	{
    		$where .= " AND doctor_id = ".$data['operate_user_id'];
    	}
		if($data['hospital'])
		{
			$where .= " AND hospital LIKE '%".$data['hospital']."%'";
		}
		if(!empty($data['start']) && !empty($data['end']))
		{
			$where .= " AND create_time >= '".$data['start']."' AND create_time <= '".$data['end']."'";
		}
		
		$sql_count = $sql_count.$where;
		$sql = $sql.$where;
		$sql .= " ORDER BY patient_id DESC ";
		$start = ($data['page']-1)*$data['page_size'];
		$page_size = $data['page_size'];
		$sql .= " LIMIT $start,$page_size";
		
    	$res = $this->db->getArray($sql);
    	$res_count = $this->db->getValue($sql_count);
    	

    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return array('list'=>$res, 'count'=>$res_count ? $res_count : 0);
    }
    
    
    //获取患者数量
    public function patient_count($data)
    {
    	if($this->db == null || empty($data)){
    		return false;
    	}
    	
    	$sql = "SELECT user_type FROM hg_user WHERE user_id = ".$data['operate_user_id'];
    	$user_type = $this->db->getValue($sql);
    	if($user_type == 1)
    	{
    		$sql = "SELECT count(*) as cnt FROM hg_patient WHERE is_delete = 0 AND operate_user_id = ".$data['operate_user_id'];
    	}
    	else
    	{
    		$sql = "SELECT count(*) as cnt FROM hg_patient WHERE is_delete = 0 AND doctor_id = ".$data['operate_user_id'];
    	}
		
    	$res = $this->db->getValue($sql);

    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res ? $res : 0;
    }
	
	//获取修复体
	public function get_repaire_tooth($id)
	{
		if($this->db == null){
    		return false;
    	}
		
    	$sql = "SELECT * FROM hg_false_tooth WHERE is_delete = 0 AND false_tooth_id = ".$id;
		
    	$res = $this->db->getArray($sql);

    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
	}
	
	//获取出库单位
	public function get_out_company($qrcode)
	{
		if($this->db == null || empty($qrcode)){
    		return false;
    	}
		
		$sql = "SELECT b.company_name FROM hg_security_code a LEFT JOIN hg_stock b ON a.stock_no = b.stock_no WHERE a.security_code = '{$qrcode}'";
		$res = $this->db->getValue($sql);

    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
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