<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Member extends Model
{
	//获取我的详情
	public function get_my_detail($user_id)
	{
		$res = Db::query("SELECT * FROM hg_user WHERE user_id = :user_id", ['user_id'=>$user_id]);
		
		return !empty($res) ? $res : array();
	}
	
	
	public function register($param)
	{
		$sql = "INSERT INTO hg_user SET ";
		if(is_array($param) && !empty($param))
		{
			foreach($param as $key => $val)
			{
				$sql .= $key ." = :".$key.",";
			}
		}
		$sql = rtrim($sql, ',');
		$res = Db::execute($sql, $param);
		return $res;
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
		$res = Db::query("SELECT * FROM hg_user WHERE mobile = :mobile AND password = :password", ['mobile'=>$mobile, 'password'=>$password]);
		
		return !empty($res) ? $res : array();
	}
	
	//更新密码
	public function update_pwd($mobile, $password)
	{
		//var_dump($mobile);var_dump($password);die;
		$res = Db::execute("UPDATE hg_user SET password = :password WHERE mobile = :mobile", ['mobile'=>$mobile, 'password'=>$password]);
		return $res;
	}
	
	//更新密码
	public function update_pwd_byuserid($user_id, $password)
	{
		$res = Db::execute("UPDATE hg_user SET password = :password WHERE user_id = :user_id", ['user_id'=>$user_id, 'password'=>$password]);
		return $res;
	}
	
	//更新用户信息
	public function update_user($data, $where)
	{
		$sql = "UPDATE hg_user SET ";
		if(!empty($data))
		{
			foreach($data as $key => $val)
			{
				$sql .= $key ." = '".$val."',";
			}
		}
		$sql = rtrim($sql,',');
		$sql .= " WHERE user_id = ".$where['user_id'];//echo $sql;die;
		try
		{
			$res = Db::execute($sql);
			return true;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}
	
}

	