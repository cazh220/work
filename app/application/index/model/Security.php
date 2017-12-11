<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Security extends Model
{
	public function serach_security_code($param)
	{
		$res = Db::query("SELECT * FROM hg_security_code a LEFT JOIN hg_patient b ON a.security_code = b.security_code WHERE a.security_code = :security_code", ['security_code'=>$param['security_code']]);
		
		return !empty($res) ? $res : array();
	}

	
}

	