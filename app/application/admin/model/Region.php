<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Region extends Model
{
	public function get_area($region_id=0)
	{
		$res = array();
		if (!empty($region_id))
		{
			$res = Db::table('hg_region')->where('id',$region_id)->find();

		}

		return isset($res['name']) ? $res['name'] : '';
	}
	
	//获取区域
	public function get_region($region_id)
	{
		$res = array();
		if (!empty($region_id))
		{
			$res = Db::table('hg_region')->where('parent_id',$region_id)->select();

		}
		
		return $res ? $res : array();
	}
}