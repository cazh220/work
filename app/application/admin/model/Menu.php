<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Menu extends Model
{
	public function getMenu($data)
	{
		$in_str = "";
		if (!empty($data))
		{
			$data_tmp = explode(",", $data['action_list']);
			foreach ($data_tmp as $key => $val)
			{
				$in_str .= "'".$val."',";
			}
			$in_str = rtrim($in_str, ',');
		}
		$sql = "SELECT * FROM hg_admin_action WHERE status = ".$data['status']." AND action_code IN ($in_str)";
		$res = Db::query($sql);
		return $res;
	}
}