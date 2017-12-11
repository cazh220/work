<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Fixtype extends Model
{
	//获取修复体
	public function get_fix_list()
	{
		$sql = "SELECT a.false_tooth_id,a.false_tooth_name,a.Institute,b.id,b.product_detail FROM hg_false_tooth a LEFT JOIN hg_false_tooth_detail b ON a.false_tooth_id = b.false_tooth_id WHERE a.is_delete = 0 AND b.id <> ''";
		$res = Db::query($sql);
		return $res ? $res : array();
	}
	
	
	//获取修复体类别
	public function get_fix_type_list()
	{
		$res = Db::table('hg_false_tooth')->where('is_delete', 0)->select();
		return $res ? $res : array();
	}
	
	//获取详情
	public function get_fix_type_detail($id)
	{
		$res = array();
		if(!empty($id))
		{
			$res = Db::table('hg_false_tooth')->where('false_tooth_id', $id)->find();
		}
		return $res;
	}
	
	//获取详情明细
	public function get_fix_type_detail_new($id)
	{
		$res = array();
		if(!empty($id))
		{
			$res = Db::table('hg_false_tooth_detail')->where('id', $id)->find();
		}
		return $res;
	}
	
	
	//更新积分
	public function add_fix_type($fix_type_desc, $false_tooth_name)
	{
		if ($institute && $false_tooth_name)
		{
			$sql = "INSERT INTO hg_false_tooth(false_tooth_name, description)VALUES('".$false_tooth_name."', '".$fix_type_desc."')";
			try{
				$res = Db::execute($sql);
			}catch(exception $e){
				//echo $e->getMessage();
				return false;
			}
			return true;
		}
		return false;
	}
	
	//添加修复体明细
	public function add_fix_detail_type($product_detail, $false_tooth_id)
	{
		if ($product_detail && $false_tooth_id)
		{
			$sql = "INSERT INTO hg_false_tooth_detail(false_tooth_id, product_detail)VALUES($false_tooth_id,'".$product_detail."')";
			try{
				$res = Db::execute($sql);
			}catch(exception $e){
				//echo $e->getMessage();
				return false;
			}
			return true;
		}
		return false;
	}
	
	//
	public function edit_fix_type($description, $false_tooth_name, $false_tooth_id)
	{
		if ($description && $false_tooth_name && $false_tooth_id)
		{
			$sql = "UPDATE hg_false_tooth SET description = '{$description}', false_tooth_name = '{$false_tooth_name}' WHERE false_tooth_id = ".$false_tooth_id;
			try{
				$res = Db::execute($sql);
			}catch(exception $e){
				//echo $e->getMessage();
				return false;
			}
			return true;
		}
		return false;
	}
	
	public function edit_fix_type_detail($product_detail, $false_tooth_id, $id)
	{
		if ($product_detail && $false_tooth_id && $id)
		{
			$sql = "UPDATE hg_false_tooth_detail SET product_detail = '{$product_detail}', false_tooth_id = '{$false_tooth_id}' WHERE id = ".$id;
			try{
				$res = Db::execute($sql);
			}catch(exception $e){
				//echo $e->getMessage();
				return false;
			}
			return true;
		}
		return false;
	}
	
	
	
	//更新积分
	public function delete_fix_type($id)
	{
		if ($id)
		{
			$sql = "UPDATE hg_false_tooth SET is_delete = 1 WHERE false_tooth_id IN (".$id.")";
			try{
				$res = Db::execute($sql);
			}catch(exception $e){
				//echo $e->getMessage();
				return false;
			}
			return true;
		}
		return false;
	}
	
	//删除修复体明细
	public function delete_fix_type_detail($id)
	{
		if ($id)
		{
			$sql = "UPDATE hg_false_tooth_detail SET is_delete = 1 WHERE id IN (".$id.")";
			try{
				$res = Db::execute($sql);
			}catch(exception $e){
				//echo $e->getMessage();
				return false;
			}
			return true;
		}
		return false;
	}

	
}