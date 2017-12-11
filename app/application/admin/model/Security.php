<?php
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Paginator;

class Security extends Model
{
	public function last_code()
	{
		$obj_data = Db::name('hg_security_code');
		$res = $obj_data->order('code_id desc')->limit(1)->value('security_code');
		return $res;
	}
	
	public function get_order($param=array())
	{
		$obj_data = Db::name('hg_order');
		$res = $obj_data->order('code_id desc')->toArray();
		/*
		if (!empty($param['keyword']))
		{
			$obj_data = $obj_data->where('username', 'like', '%'.$param['keyword'])->whereOr('mobile', 'like', '%'.$param['keyword'])->whereOr('company_name', 'like', '%'.$param['keyword']);
		}
		
		if (!empty($param['dental']))
		{
			$obj_data = $obj_data->where('company_name', 'like', '%'.$param['dental']);
		}
		
		if (!empty($param['hospital']))
		{
			$obj_data = $obj_data->where('hospital', 'like', '%'.$param['hospital']);
		}*/
		
		return $res;
	}
	
	public function insert_code($param=array(), $batch=array())
	{
		//事务操作
		if(!empty($param) && is_array($param) && !empty($batch))
		{
			Db::startTrans();
			//插入批次信息			
			try{
				//Db::table('hg_security_code_batch')->insert($batch);
				$batch_id = Db::table('hg_security_code_batch')->insert($batch); 
			} catch (\Exception $e) {
				// 回滚事务
				Db::rollback();
				return false;
			}		
			$batch_id = Db::table('hg_security_code_batch')->getLastInsID();
			
			try{
				foreach($param as $key => $value)
				{
					$value['batch_id'] = $batch_id;
					Db::table('hg_security_code')->insert($value);
				}
				
			} catch (\Exception $e) {
				// 回滚事务
				Db::rollback();
				return false;
			}
			// 提交事务
			Db::commit();    
		}
		
		return true;
	}
	
	public function code_list($param=array())
	{
		$obj_data = $obj_data = Db::name('hg_security_code')->alias('a')->join('hg_stock b', 'a.stock_no = b.stock_no', 'LEFT');
		if (!empty($param['_code']))
		{
			$obj_data = $obj_data->where('security_code', 'like', '%'.$param['_code'].'%');
		}
		
		if(!empty($param['type']))
		{
			$obj_data = $obj_data->where('production_title', $param['type']);
		}
		
		if (!empty($param['start_time']) && !empty($param['end_time']))
		{
			$start_time = $param['start_time']." 00:00:00";
			$end_time = $param['end_time']." 23:59:59";
			$obj_data = $obj_data->where('create_time', 'between', [$start_time, $end_time]);
		}
		$obj_data = $obj_data->order('code_id desc')->paginate();
		
		return $obj_data;
	}
	
	public function code_all()
	{
		$obj_data = $obj_data = Db::name('hg_security_code');
		$obj_data = $obj_data->order('code_id desc')->paginate();
		
		return $obj_data;
	}
	
	//防伪码已发放数量
	public function used_num()
	{
		$count = $obj_data = Db::name('hg_security_code')->alias('a')->join('hg_stock b', 'a.stock_no = b.stock_no', 'LEFT')->where('a.status', '>', 0)->count();
		return $count ? $count : 0;
	}
	
	//更新防伪码状态
	public function stock_out_security_code($code='', $stock_no='')
	{
		if(!empty($code) &&!empty($stock_no))
		{
			//判断是否存在
			$sql ="select * from hg_security_code where security_code LIKE '".$code."%'";
			$res = Db::query($sql);
			if($res)
			{
				//更新
				$data = array('status'=>1, 'stock_no'=>$stock_no);
				$res = Db::table('hg_security_code')->where('security_code', 'LIKE',$code.'%')->update($data);
				if($res)
				{
					return true;
				}
			}
		}
		return false;
	}
	
	//更新防伪码状态
	public function update_security_code_status($status=0,$code_arr=array())
	{
		$in = '';
		if($code_arr)
		{
			foreach($code_arr as $key => $val)
			{
				$in .= "'".$val."',";
			}
			$in = rtrim($in, ',');
		}
		$sql = "UPDATE hg_security_code SET status = ".$status." WHERE security_code IN ($in)";
		
		$res = Db::execute($sql);
		
		return $res ? $res : 0;
	}
	
	
	//获取最后执行的num
	public function get_last_num()
	{
		$obj_data = Db::name('hg_security_code');
		$res = $obj_data->order('code_id desc')->limit(1)->value('num');
		return $res;
	}
	
	//获取防伪码生成列表
	public function create_index($param)
	{
		$obj_data = $obj_data = Db::name('hg_security_code_batch');
		if (!empty($param['operater']))
		{
			$obj_data = $obj_data->where('operater', 'like', '%'.$param['operater'].'%');
		}
		
		if (!empty($param['start_time']) && !empty($param['end_time']))
		{
			$start_time = $param['start_time']." 00:00:00";
			$end_time = $param['end_time']." 23:59:59";
			$obj_data = $obj_data->where('create_time', 'between', [$start_time, $end_time]);
		}
		$obj_data = $obj_data->order('batch_id desc')->paginate();
		
		return $obj_data;
	}
	
	//获取批次详情
	public function batch_code($batch_id, $page_size=10, $path='')
	{
		if(empty($batch_id))
		{
			return array();
		}
		$obj_data = $obj_data = Db::name('hg_security_code');
		$obj_data = $obj_data->where('batch_id', $batch_id);
		
		$obj_data = $obj_data->order('code_id desc')->paginate($page_size,false,['query' => ['batch_id'=>$batch_id, 'path'=>$path] ]);
		
		return $obj_data;
	}
	
	//获取批次详情
	public function batch_code_all($batch_id)
	{
		if(empty($batch_id))
		{
			return array();
		}
		$res = Db::table('hg_security_code')->where('batch_id',$batch_id)->order('code_id desc')->select();
		
		return $res;
	}
	
	
}