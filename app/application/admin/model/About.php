<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Paginator;

class About extends Model
{
	//获取消息列表
	public function getAbout($id)
	{
		$obj_data = $obj_data = Db::name('hg_about');
		$obj_data = $obj_data->where('id', $id);
		return $obj_data;
	}
	
	
	//插入
	public function insert_about($content='', $id)
	{
		if($id && $content)
		{
			foreach($users as $ket => $val)
			{
				$sql = "INSERT INTO hg_about(id, content, create_time, update_time)VALUES({$id}, '".$content."', NOW(), NOW())";
				$res = Db::execute($sql);
			}
		}
		
		return true;
	}
	
	//更新
	public function insert_about($content='', $id)
	{
		if($id && $content)
		{
			$sql = "UPDATE hg_about SET content='".$content."', update_time = NOW() WHERE id= ".$id;
			$res = Db::execute($sql);
		}
		
		return true;
	}

}