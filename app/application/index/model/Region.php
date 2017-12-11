<?php
namespace app\index\model;

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
		return !empty($res['name']) ? $res['name'] : '';
	}
	
	//获取省
	public function get_province()
	{
		$res = array();
		$res = Db::table('hg_region')->where('parent_id',1)->select();
		return $res ? $res : '';
	}
	
	//获取区域
	public function get_region($pid)
	{
		$res = array();
		$res = Db::table('hg_region')->where('parent_id',$pid)->select();
		return $res ? $res : '';
	}
}

	