<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Paginator;

class Admin extends Model
{
	public function getAdminList($param = array())
	{
		$obj_data = $obj_data = Db::name('hg_admin_users');
		if (!empty($param['username']))
		{
			$obj_data = $obj_data->where('username', 'like', '%'.$param['username'].'%');
		}
		$obj_data = $obj_data->order('admin_id asc')->paginate();
		
		return $obj_data;
	}
	
	
	public function getAdmin($condition=array(), $order=0, $page=array())
	{
		$where_ary = array();
		//拼接参数
		foreach($condition as $key => $val)
		{
			array_push($where_ary, $key."= :".$key);
		}
		$where_condition = implode(' AND ', $where_ary);
		$order = " ORDER BY admin_id DESC";
		if ($order=='1')
		{
			$order = " ORDER BY admin_id ASC";
		}
		$where_condition .= $order;
		
		if (!empty($page))
		{
			$start = $page['current_page'] - 1;
			$page_size = $page['page_size'];
			$start = ($start > 0) ? intval($start) : 0;
			$limit = " LIMIT $start, $page_size";
			$where_condition .= $limit;
		}
	
		$res = Db::query("select *  from hg_admin_users where $where_condition", $condition);
		return $res;
	}
	
	public function updateAdmin($param=array(), $where=array())
	{
		if (!empty($param) && !empty($where))
		{
			$set = $this->join2param($param, ',');
			$where_condition = $this->join2param($where, 'AND');
			$params = array_merge($param, $where);
			//echo "update hg_admin_users set $set where $where_condition";print_r($params);die;
			$res = Db::execute("update hg_admin_users set $set where $where_condition", $params);
		}
		
		return $res==1 ? 1 : 0;
	}
	
	public function getActionList()
	{
		$res = Db::query("SELECT * FROM hg_admin_action WHERE status = 0");
		return $res;
	}
	
	public function changePrev($prev_list=array())
	{
		$output = "";
		if (!empty($prev_list))
		{
			$in_str = implode(',', $prev_list);
			$res = Db::query("select action_code from hg_admin_action where action_id IN (".$in_str.")");
			if ($res && is_array($res))
			{
				foreach($res as $key => $val)
				{
					$output_arr[] = $val['action_code'];
				}
			}
			$output = implode(',', $output_arr);
		}
		return $output;
	}
	
	//获取权限列表
	public function getPermissionList($admin_id='')
	{
		if (!empty($admin_id))
		{
			$res = Db::query("select action_list from hg_admin_users where admin_id = :admin_id", ['admin_id'=>$admin_id]);
		}
		return !empty($res[0]['action_list']) ? $res[0]['action_list'] : '';
	}
	

}