<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Sms extends Model
{
	public function write_code($param)
	{
		$res = Db::execute("INSERT INTO hg_sms_code SET mobile = :mobile, code = :code, update_time = :update_time", $param);
		return $res;
	}
	
	//校验短信验证码
	public function check_sms($mobile, $code)
	{
		$res = Db::query("SELECT count(*) as count FROM hg_sms_code WHERE mobile = :mobile AND code = :code", ['mobile'=>$mobile, 'code'=>$code]);
		
		return !empty($res) ? $res : array();
	}
	
}

	