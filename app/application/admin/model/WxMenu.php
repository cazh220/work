<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class WxMenu extends Model
{
	public function getMenu()
	{
		$sql = "SELECT * FROM wx_menu";
		$res = Db::query($sql);
		return $res;
	}
	
	public function check_menu($parent_id)
	{
		$sql = "SELECT count(*) as count FROM wx_menu WHERE parent_id = {$parent_id}";
		$res = Db::query($sql);
		return $res;
	}
	
	
	public function edit_menu($param)
	{
		if($param['type'] == 'view')
		{
			$sql = "UPDATE wx_menu SET menu_name = '".$param['menu_name']."', type='".$param['type']."',url='".$param['url']."' WHERE id = ".$param['id'];
		}
		else
		{
			$sql = "UPDATE wx_menu SET menu_name = '".$param['menu_name']."', type='".$param['type']."',menu_key='".$param['key']."' WHERE id = ".$param['id'];
		}
		$res = Db::execute($sql);
		return $res;
	}
	
	//增加新菜单名字
	public function add_menu_name($param)
	{
		$sql = "INSERT INTO wx_menu(menu_name, type, parent_id)VALUES('".$param['text']."','view', '".$param['parent_id']."')";
		$res = Db::execute($sql);
		$userId = Db::name('wx_menu')->getLastInsID();
		return $userId;
	}
	
	//删除菜单
	public function delete_menu($id)
	{
		$sql = "DELETE FROM wx_menu WHERE id = {$id}";
		$res = Db::execute($sql);
		return $res;
	}
	
	//增加新菜单名字
	public function update_menu_name($param)
	{
		$sql = "UPDATE wx_menu SET menu_name = '".$param['text']."' WHERE id=".$param['id'];
		$res = Db::execute($sql);
		return $res;
	}
	
}