<?php
namespace app\admin\model;

use think\Model;
use think\Db;

/**
 * 批量操作
 */
class Batch extends Model
{
	
	//防伪码入库
	public function insert_code($code)
	{
		//$a = Db::name('hg_security_code');
		//var_dump($a);die;
		try
		{
			Db::name('hg_security_code')->insertAll($code);
		}
		catch(\Exception $e)
		{
			echo $e->getMessage();
			return false;
		}
		
		return true;
	}	
	public function stock_list($param=array())
	{
		$res = array();
		$obj = Db::name('hg_stock');
		if (!empty($param['keyword']))
		{
			$obj = $obj->where('user_id|user_name|stock_no','like',$param['keyword'].'%');
		}
		if (!empty($param['start_time']) && !empty($param['end_time']))
		{
			$obj = $obj->where('stock_time',['>=',$param['start_time']],['<=',$param['end_time']],'and');
		}
		
		$res = $obj->where('is_delete', 0)->paginate(10);
		return $res;
	}
	
	//插入会员
	public function insert_user($data)
	{
		if($data)
		{
			/*
			$sql = "INSERT INTO hg_user SET ";
			
			foreach($data as $key => $val)
			{
				$sql .= $key."='".$val."'";
			}
			
			$sql = rtrim($sql, ',');
			*/
			
			try
			{
				Db::name('hg_user')->insertAll($data);
			}
			catch(\Exception $e)
			{
				echo $e->getMessage();
				return false;
			}
			
			return true;
		}
	}
		
		
	//插入批量防伪码
	public function insert_security_code($batch, $code)
	{
		if($code && $batch)
		{
			// 启动事务
			Db::startTrans();
			try{
				//插入批量的头信息
				Db::name('hg_security_code_batch')->insert($batch);
				$batch_id = Db::name('hg_security_code_batch')->getLastInsID();  //var_dump($batch_id);die; 
			} catch (\Exception $e) {
				//echo $e->getMessage();
			    // 回滚事务
			    Db::rollback();
			    return false;
			}
			
			$items = array();
			foreach($code as $key => $value)
			{
				$items[$key] = array(
					'security_code'			=> $value,
					'create_time'			=> date("Y-m-d H:i:s", time()),
					'update_time'			=> date("Y-m-d H:i:s", time()),
					'prefix'				=> 'N',
					'num'					=> 5000,
					'them'					=> '20171209/刘加其/诺必灵/5000/绘卡',
					'production_title'		=> 4,
					'batch_id'				=> $batch_id
				);
			}


			try
			{
				Db::name('hg_security_code')->insertAll($items);
				// 提交事务
				Db::commit(); 
			}
			catch(\Exception $e)
			{
				echo $e->getMessage();
				return false;
			}
			
			return true;
		}
		
		
	}
}