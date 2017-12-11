<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Company extends Model
{
	
	//获取单位列表
	public function get_company_list()
	{
		$res = Db::table('hg_company')->where('is_delete',0)->select();
		
		return $res ? $res : array();
	}
	
	//判断单位是否亦存在
	public function is_exist_company($name='')
	{
		$res = Db::table('hg_company')->where('company_name',$name)->find();
		
		if($res)
		{
			return true;//存在
		}
		else
		{
			return false;//不存在
		}
	}
	
	//插入新单位
	public function insert_company($company_name='')
	{
		$company_id = 0;
		if($company_name)
		{
			$data = array(
				'company_name'	=> $company_name
			);
			
			$res = Db::table('hg_company')->insert($data);
			$company_id = Db::name('hg_company')->getLastInsID();
		}	
		
		return $company_id;	
	}
	
	//获取单位名
	public function get_company_name($company_id=0)
	{
		$res = Db::table('hg_company')->where('cid',$company_id)->find();
		
		return $res['company_name'] ? $res['company_name'] : '';
	}
	
}