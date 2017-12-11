<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Paginator;

class User extends Model
{
	public function insert_user($data=array())
	{
		$res = 0;
		if (!empty($data))
		{
			$sql = "INSERT INTO hg_user SET ";
			foreach($data as $key => $val)
			{
				$sql .= $key ." = '".$val."',";
			}
			$sql = rtrim($sql, ',');
			$res = Db::execute($sql);
			//$res = Db::execute("INSERT INTO hg_user SET username = :username, mobile = :mobile, realname = :realname, password = :password, user_type = :user_type, sex = :sex,email = :email, birthday = :birthday, company_name = :company_name, company_addr = :company_addr, company_phone = :company_phone, department = :department, position = :position, persons_num = :persons_num, zipcode = :zipcode, create_time = :create_time, company_info = :company_info, head_img = :head_img", $data);
		}
		return $res;
	}
	
	public function user_list($param=array())
	{
		$obj_data = Db::name('hg_user');
		
		if (!empty($param['keyword']))
		{
			$obj_data = $obj_data->where('username', 'like', '%'.$param['keyword'])->whereOr('mobile', 'like', '%'.$param['keyword'])->whereOr('company_name', 'like', '%'.$param['keyword']);
		}
		
		if (!empty($param['dental']))
		{
			if($param['dental'] == '技工所')
			{
				$obj_data = $obj_data->where('user_type', 1);
			}
			else
			{
				$obj_data = $obj_data->where('user_type', 2);
			}
		}
//		if (!empty($param['dental']))
//		{
//			$obj_data = $obj_data->where('company_name', 'like', '%'.$param['dental']);
//		}
		
		if (!empty($param['hospital']))
		{
			$obj_data = $obj_data->where('hospital', 'like', '%'.$param['hospital']);
		}
		
		if (!empty($param['start_time']) && !empty($param['end_time']))
		{
			$obj_data = $obj_data->where('create_time',['>=',$param['start_time']],['<=',$param['end_time']],'and');
		}
		
		$obj_data = $obj_data->order('user_id desc');
		
		return $obj_data->paginate(10);
	}
	
	public function user_detail($user_id=0)
	{
		$res = Db::query("SELECT * FROM hg_user WHERE user_id = :user_id", ['user_id'=>$user_id]);
		
		return !empty($res) ? $res : array();
	}
	
	//判断用户是否存在
	public function is_exist_user($user)
	{
		if($user)
		{
			$sql = "SELECT * FROM hg_user WHERE username = '".$user."' OR mobile = '".$user."'";
			$res = Db::query($sql);
		}
		return !empty($res) ? $res : array();
	}
	
	public function update_user($param = array())
	{
		$user_id = isset($param['user_id']) ? $param['user_id'] : 0;
		unset($param['user_id']);
		$res = 0;
		if (!empty($param) && $user_id)
		{
			$sql = "UPDATE hg_user SET ";
			foreach($param as $key => $val)
			{
				$sql .= $key ." = '".$val."',";
			}
			$sql = rtrim($sql, ',');
			$sql .= " WHERE user_id = ".$user_id;//echo $sql;die;
			$res = Db::execute($sql);
			
		}
		return $res;
		/*
		$res = 0;
		if (!empty($param))
		{
			$res = Db::execute("UPDATE hg_user SET username = :username, mobile = :mobile, realname = :realname, user_type = :user_type, sex = :sex,email = :email, birthday = :birthday, company_name = :company_name, company_addr = :company_addr, company_phone = :company_phone, department = :department, position = :position, persons_num = :persons_num, zipcode = :zipcode, create_time = :create_time WHERE user_id = :user_id", $param);
		}
		return $res;
		*/
	}
	
	public function delete_user($user_id=0)
	{
		$res = 0;
		if (!empty($user_id))
		{
			$res = Db::execute("DELETE FROM hg_user WHERE user_id = :user_id", ['user_id'=>$user_id]);
		}
		return $res;
	}
	
	//审核
	public function audit_user($user_id=0, $status=0)
	{
		if (!empty($user_id))
		{
			$res = Db::execute("UPDATE hg_user SET status = :status  WHERE user_id = :user_id", ['status'=>$status,'user_id'=>$user_id]);
		}
		return $res;
	}
	
	//插入一条操作记录
	public function insert_user_action($param=array())
	{
		$res = 0;
		if (!empty($param))
		{
			try
			{
				$res = Db::execute("INSERT INTO hg_user_actions SET admin_id = :admin_id, user_id = :user_id, username = :username, content = :content, create_time = :create_time, ip = :ip", $param);
			}
			catch(exception $e)
			{
				echo $e->getMessage();//die;
			}
			
		}
		return $res;
	}
	
	public function getHistory($user_id = 0)
	{
		$obj_data = Db::name('hg_user_actions')->alias('a')->join('hg_user b', 'a.user_id = b.user_id', 'LEFT');
		if (!empty($user_id))
		{
			$obj_data = $obj_data->where('a.user_id', $user_id);
		}
		$res = $obj_data->order('id desc')->paginate();
		return $res;
	}
	
	//获取超过8小时未审核的用户
	public function un_audited_user_list()
	{
		//8小时
		$time = date('Y-m-d H:i:s', time()-3600*8);
		$res = Db::name('hg_user')->where('create_time', '<', $time)->select();
		//print_r($res);die;
		return $res;
	}
	
	//更新通过审核状态
	public function pass($user_id=0)
	{
		$res = 0;
		if($user_id)
		{
			$res = Db::execute("UPDATE hg_user SET status = :status, auto_check = :auto_check  WHERE user_id = :user_id", ['status'=>1,'auto_check'=>1,'user_id'=>$user_id]);
		}
		
		return $res;
	}
	
	public function reset_pwd($user_id)
	{
		$pwd = @file_get_contents('pwd');
		//初始密码
		try
		{
			$res = Db::execute("UPDATE hg_user SET password = '".$pwd."' WHERE user_id = :user_id", ['user_id'=>$user_id]);
		}
		catch(exception $e)
		{
			return false;
		}
		
		return true;
	}
	
	//校验手机号是否存在
	public function check_mobile($mobile)
	{
		$res = false;
		if($mobile)
		{
			$sql = "SELECT * FROM hg_user WHERE mobile = '".$mobile."'";
			$res = Db::query($sql);
		}
		return !empty($res) ? true : false;
	}
	
	//获取地址
	public function get_address($address_id)
	{
		$res = array();
		if($address_id)
		{
			$sql = "SELECT * FROM hg_address WHERE address_id = ".$address_id;
			$res = Db::query($sql);
		}
		
		return $res;
	}
	
	//获取地区
	public function get_area($id)
	{
		$res = array();
		if($id)
		{
			$sql = "SELECT name FROM hg_region WHERE id = ".$id;
			$res = Db::query($sql);
		}
		
		return $res;
	}
}