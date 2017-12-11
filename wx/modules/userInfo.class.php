<?php
/**
 * 用户处理类
 * 
 * @package     modules
 * @author      sam(sam.ma@lyceem.com)
 * @copyright   2010-4-12
 */
 
class userInfo
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
	
	
	/**
     * 获取所有管理员信息
     * 
     * @return array|bool  成功返回批次数组,失败返回false
     */
    public function getUserInfo()
	{
		if($this->db == null)
		{
    		return false;
    	}
		
		$sql = "SELECT user_id , user_name FROM admin_user";
		$r   = $this->db->getArray($sql);
		
		if($r === false){
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
		
		if(!is_array($r) || count($r) == 0)
		{
			return false;
		}
		
		$ar_admin = array();
		foreach($r as $v)
		{
			$ar_admin[$v['user_id']] = $v['user_name'];	
		}
		
		return $ar_admin;
	}
	
	//注册信息插入表中
	public function insert_user($data)
	{
		if($this->db == null || empty($data)){
    		return false;
    	}
    	
    	$sql = "INSERT INTO hg_user SET ";
    	foreach($data as $key => $val)
    	{
			$sql .= $key ." = '".$val."',";
    	}
    	
    	$sql = rtrim($sql, ',');
    	
    	$this->db->exec("START TRANSACTION");//开启事务
		try{
			$this->db->exec($sql);
		}catch(exception $e){
			$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
			$this->db->exec("ROLLBACK");//回滚
			return false;
		}
		
		$user_id = $this->db->getLastId();
		$username = !empty($data['username']) ? $data['username'] : '';
		$content = $username.'注册成功;';
		$channel = 1;
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$sql = "INSERT INTO hg_user_actions(admin_id,user_id,username,content,action_create_time,ip, channel)VALUES($user_id, $user_id,'".$username."','".$content."', NOW(), '".$ip."', $channel)";
		
		try{
			$this->db->exec($sql);
		}catch(exception $e){
			$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
			$this->db->exec("ROLLBACK");//回滚
			return false;
		}
		
		$this->db->exec("COMMIT");//提交
		return $user_id;
	}
	
	//更新用户信息
	public function update_user($data)
	{
		if($this->db == null){
    		return false;
    	}

		$sql  = "UPDATE hg_user SET realname = '".$data['realname']."', email = '".$data['email']."', company_name = '".$data['company_name']."', company_addr = '".$data['address']."', head_img = '".$data['company_pic']."', company_info = '".$data['info']."', province = {$data['province']}, city = {$data['city']}, district = {$data['district']}, position='".$data['position']."', persons_num = '".$data['persons_num']."',address_id={$data['address_id']} WHERE user_id = {$data['user_id']}";
		$res = $this->db->exec($sql);
    	if($res === false) {
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	if($res > 0 ){
    		importModule('LogSqs');
			
			$logsqs=new LogSqs;
    		//return true;
    		return true;
		}
    	
    	return false;
	}
	
	//更新用户的address_id
	public function update_user_address_id($user_id, $address_id)
	{
		if($this->db == null || empty($user_id) || empty($address_id)){
    		return false;
    	}

		$sql  = "UPDATE hg_user SET address_id = ".$address_id." WHERE user_id = ".$user_id;
		$res = $this->db->exec($sql);
    	
    	if($res === false) {
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	return $res;
	}
	
	//更新微信基本信息
	public function update_wx_user($data)
	{
		if($this->db == null){
    		return false;
    	}

		$sql  = "UPDATE hg_user SET wx_nick_name = '".$data['wx_nick_name']."', wx_open_id = '".$data['wx_open_id']."', wx_headimgurl = '".$data['wx_headimgurl']."' WHERE user_id = {$data['user_id']}";
		
		//$this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		$res = $this->db->exec($sql);
    	
    	if($res === false) {
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	if($res > 0 ){
    		importModule('LogSqs');
			
			$logsqs=new LogSqs;
    		//return true;
    		return true;
		}
    	
    	return false;
	}

	//获取会员信息
	public function get_user_detail($user_id)
	{
		if($this->db == null){
    		return false;
    	}

    	$sql = "SELECT * FROM hg_user WHERE user_id = ".$user_id;

    	$res = $this->db->getArray($sql);

    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
	}
	
	//获取会员信息是否异常
	public function check_user_status($user_id)
	{
		if($this->db == null || empty($user_id)){
    		return false;
    	}

    	$sql = "SELECT count(*) as cnt FROM hg_user WHERE status = 1 AND user_id = ".$user_id;

    	$res = $this->db->getValue($sql);
    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return !empty($res) ? true : false;
	}
	
	
	//获取会员积分
	public function get_user_credits($user_id)
	{
		if($this->db == null){
    		return false;
    	}
    	$sql = "SELECT left_credits FROM hg_user WHERE user_id = {$user_id}";
    	$res = $this->db->getValue($sql);
    	
    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
	}
	
	
	/**
	 * 登录查询
	 * 
	 * @param string $s_username 用户名
	 * @param string $s_password 密码
	 * @return array|bool
	 */
	public function findLogin($s_username,$s_password){
		if($this->db == null){
    		return false;
    	}
    		
    	$s_username = (string)trim($s_username);
		$s_password = ((string)trim($s_password));
		
		if(empty($s_username)){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . 'username is empty. ' . $s_username, date("Y-m-d H:i:s")));
		}
		
		if(empty($s_password)){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . 'password is empty. ' . $s_password, date("Y-m-d H:i:s")));
		}
		
		$sql = "SELECT * FROM hg_user WHERE (username = '$s_username' OR email = '$s_username') AND password = '$s_password' AND status = 1";

		$res = $this->db->getRow($sql);
		
		if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
	}
	
	//判断注册用户是否存在
	public function get_reg_user($s_username){
		if($this->db == null){
    		return false;
    	}
    		
    	$s_username = (string)trim($s_username);

		if(empty($s_username)){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . 'username is empty. ' . $s_username, date("Y-m-d H:i:s")));
		}
		
		
		$sql = "SELECT * FROM hg_user WHERE (username = '$s_username' OR email = '$s_username')";

		$res = $this->db->getRow($sql);
		
		if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
	}
	
	//注册了但是状态未通过
	public function get_unpass_user($s_username)
	{
		if($this->db == null || empty($s_username)){
    		return false;
    	}
    	
    	$sql = "SELECT COUNT(*) as 'cnt' FROM hg_user WHERE (username = '$s_username' OR email = '$s_username') AND status = 2";
    	$res = $this->db->getValue($sql);
    	
    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
	}
	
	
	//判断审核状态
	public function check_status($s_username){
		if($this->db == null || empty($s_username)){
    		return false;
    	}
    		
    	$s_username = (string)trim($s_username);
		
		if(empty($s_username)){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . 'username is empty. ' . $s_username, date("Y-m-d H:i:s")));
		}

		$sql = "SELECT status FROM hg_user WHERE (username = '$s_username' OR email = '$s_username')";

		$res = $this->db->getValue($sql);
		
		if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res ? $res : 0;
	}
	
	
	/**
	 * 更新登录时间或ip
	 * 
	 * @param string $i_userid 用户名
	 * @param string $s_ip 	        客户端IP
	 * 
	 * @return bool
	 */
	public function updateLoginInfo($i_userid, $s_ip) {
		if($this->db == null){
    		return false;
    	}
    	
    	$sql = "UPDATE hg_user SET last_login = ".time()." , last_ip = '".$s_ip."' WHERE user_id = $i_userid";
    	$res = $this->db->exec($sql);
    	
    	if($res === false) {
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	if($res > 0 ){
    		importModule('LogSqs');
			
			$logsqs=new LogSqs;
    		return true;
		}
    	
    	return false;
	}
	

	//修改密码
	public function update_pwd($mobile, $pwd)
	{
		if($this->db == null){
    		return false;
    	}
    	
    	$sql = "UPDATE hg_user SET password = '".$pwd."' WHERE mobile = '".$mobile."'";
    	$res = $this->db->exec($sql);
    	
    	if($res === false) {
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	if($res)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    	
	} 
	
	//意见反馈
	public function feedback($user_id=0, $feedback='', $pic='')
	{
		if($this->db == null){
    		return false;
    	}
    	
    	$sql = "INSERT INTO hg_feedback(user_id, content, pic, create_time)VALUES($user_id, '".$feedback."', '".$pic."',NOW()) ";
    	$res = $this->db->exec($sql);
    	
    	if($res === false) {
    		return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
    	}
    	
    	if($res > 0 ){
    		importModule('LogSqs');
			
			$logsqs=new LogSqs;
    		return true;
		}
    	
    	return false;
	}
	
	//获取微信信息
	public function get_wx_user($security_code)
	{
		if($this->db == null){
    		return false;
    	}
    	//$sql = "SELECT * FROM hg_share WHERE security_code = {$security_code} ORDER BY id desc LIMIT 1";
    	$sql = "SELECT * FROM hg_user a LEFT JOIN hg_patient b ON a.user_id = b.operate_user_id WHERE b.security_code = '".$security_code."'";
    	$res = $this->db->getArray($sql);
    	
    	if($res === false){
			return $this->_log(array( __CLASS__ . '.class.php line ' . __LINE__ , 'function '. __FUNCTION__ . ' sql execute false. sql = ' . $sql, date("Y-m-d H:i:s")));
		}
		
		return $res;
	}
	
	//获取分享人
	public function get_share_user($security_code=0)
	{
		if($this->db == null || empty($security_code)){
    		return false;
    	}
    	
    	$sql = "SELECT * FROM hg_share WHERE  security_code = '".$security_code."' ORDER BY id desc limit 1";
    	try
    	{
    		$res = $this->db->getArray($sql);
    	}
    	catch(exception $e)
    	{
    		echo $e->getMessage();exit();
    	}
    	
    	return $res[0] ? $res[0] : array();
    	
	}
	
	//更新分享
	public function update_share($security_code=0, $session_id = 0)
	{
		if($this->db == null || empty($session_id) || empty($security_code)){
    		return false;
    	}
    	$sql = "SELECT id FROM hg_share WHERE security_code = '".$security_code."' ORDER BY id LIMIT 1";
    	$id = $this->db->getValue($sql);
    	
    	$sql = "UPDATE hg_share SET session_id = '".$session_id."' WHERE id = {$id}";
    	
    	try
    	{
    		$this->db->exec($sql);
    	}catch(exception $e){
    		return fasle;
    	}
    	
    	return true;
	}
	
	
	
	
	//检查手机是否已存在
	public function check_mobile($mobile='')
	{
		if($this->db == null || empty($mobile)){
    		return false;
    	}
    	
    	$sql = "SELECT count(*) as cnt FROM hg_user WHERE mobile = '".$mobile."' AND status IN (0, 1)";
    	
    	$res = $this->db->getValue($sql);
    	
    	return $res ? $res : 0;
	}
	
	//插入操作日志
	public function action_log($param=array())
	{
		if($this->db == null || empty($param)){
    		return false;
    	}
		
		$admin_id = $user_id = !empty($param['user_id']) ? $param['user_id'] : 0;
		$username	= !empty($param['username']) ? $param['username'] : '';
		$content = !empty($param['content']) ? $param['content'] : '';
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$sql = "INSERT INTO hg_user_actions(admin_id,user_id,username,content,action_create_time,ip,channel)VAlUES($admin_id,$user_id,'".$username."','".$content."',NOW(),'".$ip."',1)";
		
		try{
			$this->db->exec($sql);
		}catch(\Exception $e){
			return false;
		}
		
		return true;	
	}
	
	//写入新地址
	public function insert_address($data)
	{
		if($this->db == null || empty($data)){
    		return false;
    	}
    	
    	$sql = "INSERT INTO hg_address SET ";
    	foreach($data as $key => $val)
    	{
			$sql .= $key ." = '".$val."',";
    	}
    	$sql .= "create_time = NOW()";
    	
    	//$sql = rtrim($sql, ',');
    	try{
			$this->db->exec($sql);
		}catch(\Exception $e){
			return false;
		}
		
		$res_id = $this->db->getLastId();
		
		//更新主表
		$sql = "UPDATE hg_user SET address_id = ".$res_id." WHERE user_id = ".$data['user_id'];
		try{
			$this->db->exec($sql);
		}catch(\Exception $e){
			return false;
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
	    $log->reset()->setPath("modules/UserInfo")->setData($data)->write();
	    
	    return false;
	}
}
?>