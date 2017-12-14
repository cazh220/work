<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Patient extends Model
{
	public function patient_list($param)
	{
		$start = ($param['page']-1)*$param['page_size'];
		$page_size = $param['page_size'];
		$sql = "SELECT * FROM hg_patient a left join hg_false_tooth b on a.false_tooth = b.false_tooth_id";
		if(!empty($param['tech_id']))
		{
			$sql .= " WHERE a.tech_id = ".$param['tech_id'];
		}
		$sql .= " ORDER BY a.patient_id DESC LIMIT $start, $page_size";
		
		$res = Db::query($sql);
		
		return !empty($res) ? $res : array();
	}
	
	//获取患者详情
	public function get_patient_detail($security_code='')
	{
		if(!empty($security_code))
		{
			$res = Db::query("SELECT * FROM hg_patient WHERE security_code = '".$security_code."'");
		
			return !empty($res) ? $res[0] : array();
		}
		return array();
	}
	
	//删除
	public function delete_patient($list)
	{
		if(!empty($list))
		{
			$sql = "DELETE FROM hg_patient WHERE patient_id IN (".$list.")";
			try{
				$res = Db::execute($sql);
			}catch(exception $e){
				echo $e->getMessage();
				return false;
			}
		}
		return true;
	}
	
	
	//插入患者
	public function insert_patient($data)
	{
		if(empty($data))
		{
			return false;
		}
		
		$sql = "INSERT INTO hg_patient SET ";
		
		foreach($data as $key => $val)
		{
			$sql .= $key."='".$val."',";
		}
		
		$sql .= " create_time = NOW()";

		try{
			$res = Db::execute($sql);
		}catch(exception $e){
			echo $e->getMessage();
			return false;
		}
		
		return true;
	}
	
	//更新患者
	public function update_patient($data, $patient_id)
	{
		if(empty($data) || empty($patient_id))
		{
			return false;
		}
		
		$sql = "UPDATE hg_patient SET ";
		foreach($data as $key => $val)
		{
			$sql .= $key."='".$val."',";
		}
		
		$sql .= " update_time = NOW()";
		
		$sql .= " WHERE patient_id = ".$patient_id;
		try{
			$res = Db::execute($sql);
		}catch(exception $e){
			echo $e->getMessage();
			return false;
		}
		
		return true;
		
	}

	
}

	