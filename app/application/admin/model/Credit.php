<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Credit extends Model
{
	
	//获取单位列表
	public function get_credit_list()
	{
		$res = Db::table('hg_credit')->select();
		
		return $res ? $res : array();
	}
	
	
	//更新积分
	public function update_credits($data, $id)
	{
		if ($data && $id)
		{
			try{
				$res = Db::execute("UPDATE hg_credit SET credits = ".$data." WHERE id = ".$id);
			}catch(exception $e){
				//echo $e->getMessage();
				return false;
			}
			return true;
		}
		return false;
	}

	
}