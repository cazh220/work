<?php
namespace app\index\model;

use think\Model;
use think\Db;

class User extends Model
{
	public function register($param)
	{
		if(empty($param) || !is_array($param))
		{
			return false;
		}
		
		$sql = "INSERT INTO hg_user SET ";
		if(is_array($param) && !empty($param))
		{
			foreach($param as $key => $val)
			{
				$sql .= $key ." = :".$key.",";
			}
		}
		$sql = rtrim($sql, ',');
		
		Db::startTrans();//启动事务
		try{
			$res = Db::execute($sql, $param);
		}catch(\Exception $e){
			//回滚
			Db::rollback();
			return false;
		}
		print_r($param);
		//写入操作记录
		$user_id = Db::name('hg_user')->getLastInsID();
		$admin_id = !empty($user_id) ? $user_id : 0;
		$username = !empty($param['username']) ? $param['username'] : '';
		$content = $username."注册成功";
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$data = ['admin_id'=>$admin_id, 'user_id'=>$user_id, 'username'=>$username, 'content'=>$content, 'action_create_time'=>date("Y-m-d H:i:s", time()), 'ip'=>$ip];
		try{
			Db::name('hg_user_actions')->insert($data);
		}catch(\Exception $e){
			//回滚
			Db::rollback();
			return false;
		}
		
		// 提交事务
    	Db::commit(); 
    	return true;
		
	}
	
	//校验短信验证码
	public function check_sms($mobile, $code)
	{
		$res = Db::query("SELECT count(*) as count FROM hg_sms_code WHERE mobile = :mobile AND code = :code", ['mobile'=>$mobile, 'code'=>$code]);
		
		return !empty($res) ? $res : array();
	}
	
	//验证登录
	public function check_user($mobile, $password)
	{
		$res = Db::query("SELECT * FROM hg_user WHERE (mobile = :mobile OR username = :username) AND password = :password AND status = 1", ['mobile'=>$mobile, 'username'=>$mobile, 'password'=>$password]);
		
		return !empty($res) ? $res : array();
	}
	
	//检查老用户
	public function check_old_user($account)
	{
		$res = Db::query("SELECT * FROM hg_user WHERE username = :username AND password = :password", ['username'=>$account, 'password'=>'huge123456']);
		
		return !empty($res) ? $res : array();
	} 
	
	//判断手机号是否亦存在
	public function is_exist_mobile($mobile)
	{
		if($mobile)
		{
			$res = Db::query("SELECT * FROM hg_user WHERE mobile = :mobile", ['mobile'=>$mobile]);
		
			return !empty($res) ? $res : array();
		}
	}
	
	//判断用户名是否存在
	public function is_exist_user($user)
	{
		if($user)
		{
			$res = Db::query("SELECT * FROM hg_user WHERE username = :username", ['username'=>$user]);
		
			return !empty($res) ? $res : array();
		}
	}
	
	//更新密码
	public function update_pwd($mobile, $password)
	{
		//var_dump($mobile);var_dump($password);die;
		try{
			$res = Db::execute("UPDATE hg_user SET password = :password WHERE mobile = :mobile", ['mobile'=>$mobile, 'password'=>$password]);
		}
		catch(exception $e)
		{
			echo $e->getMessage();
			return false;
		}
		
		return true;
	}
	
	//插入日志
	public function action_log($param=array())
	{
		$admin_id = $user_id = !empty($param['user_id']) ? $param['user_id'] : 0;
		$username	= !empty($param['username']) ? $param['username'] : '';
		$content = !empty($param['content']) ? $param['content'] : '';
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$data = ['admin_id'=>$admin_id, 'user_id'=>$user_id, 'username'=>$username,'content'=>$content,'action_create_time'=>date("Y-m-d H:i:s", time()), 'ip'=>$ip, 'channel'=>0];
		
		try{
			Db::name('hg_user_actions')->insert($data);
		}catch(\Exception $e){
			return false;
		}
		
		return true;		
	}
	
}

	