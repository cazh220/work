<?php
namespace app\admin\controller;
use think\Controller;
use think\View;
use think\Session;

//set_time_limit(0);
//ini_set('memory_limit', '128M');
/**
 * 定时任务
 */
class Crontab
{
	public function index()
	{
		//定时任务
	}
	
	
	//自动审核
	public function auto_check()
	{
		$path = APP_PATH.'auto_audit';
		$config = file_get_contents($path);
		$config = $config ? $config : 0;
		
		if($config)
		{
			//查询超过8小时未审核的
			$User = Model('User');
			$list = $User->un_audited_user_list();
			foreach($list as $key => $val)
			{
				$User->pass($val['user_id']);
			}
		}
	}
	
}
	
	
?>