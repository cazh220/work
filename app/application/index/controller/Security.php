<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;
use think\Session;

class Security
{	

	public function security()
	{
		$view = new View();
		$view->assign('user', Session::get('user.mobile'));
		return $view->fetch('security_code');
	}
	
	public function search_security_code()
	{
		$security_code = $_POST['security_code'];
		
		//查询
		$param['security_code'] = $security_code;
		$Security = model('Security');
		$result = $Security->serach_security_code($param);
		$data = !empty($result) ? $result[0] : array();
		$patient = !empty($data['name']) ? $data['name'] : '';
		
		$view = new View();
		$view->assign('data', $data);
		$view->assign('patient', $patient);
		$view->assign('user', Session::get('user.mobile'));
		return $view->fetch('security_code');
	}
	
	//查询质保卡信息
	public function query_security_code()
	{
		$security_code = $_POST['security_code'];
		
		//查询
		$param['security_code'] = $security_code;
		$Security = model('Security');
		$result = $Security->serach_security_code($param);
		$data = !empty($result) ? $result[0] : array();
		$patient = !empty($data['name']) ? $data['name'] : '';
		
		if(!empty($data))
		{
			$return_data = array('status'=>1, 'data'=>$data, 'patient'=>$patient);
		}
		else
		{
			$return_data = array('status'=>0, 'data'=>array(), 'patient'=>'');
		}
		exit(json_encode($return_data));
	}
	

}
