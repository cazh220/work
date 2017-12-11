<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Paginator;

class Message extends Model
{
	//获取消息列表
	public function getMessageList($param = array())
	{
		$obj_data = $obj_data = Db::name('hg_message');
		if (!empty($param['sender']))
		{
			$obj_data = $obj_data->where('sender', 'like', '%'.$param['sender'].'%');
		}
		
		if (!empty($param['receiver']))
		{
			$obj_data = $obj_data->where('receiver', 'like', '%'.$param['receiver'].'%');
		}
		
		if (!empty($param['start_time']) && !empty($param['end_time']))
		{
			$obj_data = $obj_data->where('create_time','between',[$param['start_time'],$param['end_time']]);
		}
		
		$obj_data = $obj_data->order('id asc')->paginate();
		
		return $obj_data;
	}
	
	
	//发消息
	public function insert_message($users=array(), $content='')
	{
		if($users && $content)
		{
			foreach($users as $ket => $val)
			{
				$user_info = $this->get_user_id($val);
				$user_id = $user_info[0]['user_id'];
				
				$sql = "INSERT INTO hg_message(type,from_user_id,sender,to_user_id,receiver,message,create_time)VALUES(0,0,'系统',$user_id,'".$val."','".$content."',NOW())";
				
				$res = Db::execute($sql);
			}
		}
		
		return true;
	}
	
	//全局发消息
	public function send_all_message($content='')
	{
		if(!empty($content))
		{
			$sql = "SELECT user_id,username FROM hg_user";
			$res = Db::query($sql);
			if($res)
			{
				foreach($res as $key => $val)
				{
					$user_id = $val['user_id'];
					if($key%10000 == 0)
					{
						$sql = "INSERT INTO hg_message(type,from_user_id,sender,to_user_id,receiver,message,create_time)VALUES(0,0,'系统',$user_id,'".$val['username']."','".$content."',NOW())";
						$res = Db::execute($sql);
						sleep(1);
					}
				}
			}
		}
		
		return true;
	}
	
	//获取user_id
	public function get_user_id($mobile='')
	{
		if($mobile)
		{
			$sql = "SELECT user_id FROM hg_user WHERE mobile ='".$mobile."'";
			$res = Db::query($sql);
		}
		
		return $res ? $res : array();
	}

}