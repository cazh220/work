<?php
namespace app\admin\controller;
use think\Controller;
use think\View;

class System 
{
	public function auto_audit()
    {
		$path = APP_PATH.'auto_audit';
		$config = file_get_contents($path);
		$config = $config ? $config : 0;
		
        $view = new View();
		$view->assign('config', intval($config));
		return $view->fetch('auto_audit');
    }
    
    public function audit_on()
    {
    	$audit = intval($_POST['audit']);
    	
    	$path = APP_PATH.'auto_audit';
    	file_put_contents($path, $audit);
    	
    	echo "<script>alert('设置成功');window.location.href='auto_audit';</script>";
    }
	
	
}