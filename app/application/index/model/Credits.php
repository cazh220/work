<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Credits extends Model
{
	public function query_credists($user_id)
	{
		$sql = "SELECT total_credits,exchanged_credits,left_credits FROM hg_user WHERE user_id = $user_id";
		$res = Db::query($sql);
		return $res;
	}
	
	//查询积分明细
	public function get_credits_detil($user_id)
	{
		$sql = "SELECT * FROM hg_credits_flow WHERE user_id = ".$user_id;
		$res = Db::query($sql);
		return $res;
	}
	
	
}

	