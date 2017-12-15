<?php
namespace app\admin\controller;
use app\admin\model;
use think\Controller;
use think\View;
use think\Config;
use think\Session;
use think\Log;

class Index extends Controller
{
	static $verify = 1;
	public function test()
	{
		
		$Test = model('Test');
		$a = $Test->insert();
		
		//echo mb_detect_encoding($a);
		print_r($a);
		/*
		header('Content-Type:text/html; charset=utf-8');
		$Admin = model('Admin');
		//print_r($Admin);die;
		$admin_detail = $Admin->getAdmin(array('username'=>'admin', 'password'=>md5(123456)));
		$Menu = model('Menu');
		$menus = $Menu->getMenu(array('status'=>0, 'action_list'=>$admin_detail[0]['action_list']));
		print_r($menus);die;
		*/
	}
	
    public function index()
    {
    	if(empty(Session::get('admin_id')))
    	{
    		//跳转到登录页面
    		header("Content-type:text/html;charset=utf-8");
            echo "<script>window.location.href='index/login';</script>";
    	}
    	else
    	{
    		//print_r(Config::get('host'));die;
	        $view = new View();
			$view->assign('menu', Session::get('menu'));
			$view->assign('username', Session::get('username'));
			return $view->fetch('index');
    	}
		
    }
	
	public function login()
	{
		$view = new View();
		return $view->fetch('login');
	}
	
	public function doLogin()
	{
		$username	= input('username');
		$password	= input('password');
		$code		= input('verify_code');
		
		$username 	= !empty($username) ? addslashes(trim($username)) : '';
		$password 	= !empty($password) ? addslashes(trim($password)) : '';
		$code 		= !empty($code) ? addslashes(trim($code)) : '';
		
		if(!captcha_check($code) && self::$verify == 1)
		{
			header("Content-type:text/html;charset=utf-8");
            exit("<script>alert('验证码错误！');window.location.href='login?".time()."';</script>");
		}
		
		$Admin = model('Admin');
		$admin_detail = $Admin->getAdmin(array('username'=>$username, 'password'=>md5($password)));
		
		//密码校验
		if (empty($admin_detail))
		{
			Log::error($username."用户名或密码错误！");
			header("Content-type:text/html;charset=utf-8");
            exit("<script>alert('用户名或密码错误！');window.location.href='login?".time()."';</script>");
		}
		Log::info($username."登录成功;");
		//写session
		Session::set('admin_id',$admin_detail[0]['admin_id']);
		Session::set('username',$username);
		Session::set('action_list',$admin_detail[0]['action_list']);
		//获取menu和权限列表
		$Menu = model('Menu');
		$menus = $Menu->getMenu(array('status'=>0, 'action_list'=>$admin_detail[0]['action_list']));

		$menu_list = get_menu_list($menus);
		Session::set('menu',$menu_list);
		Session::set('action_list',$admin_detail[0]['action_list']);
		
		$data = array(
			'last_login_time'	=> date('Y-m-d H:i:s', time()),
			'last_ip'			=> $_SERVER['REMOTE_ADDR']
		);
		$where = array(
			'admin_id'			=> $admin_detail[0]['admin_id']
		);
		//更新登录信息
		$Admin->updateAdmin($data, $where);
		
		$target_url = Config::get('host_url')."/public/admin.php/admin/";
		header("Location:".$target_url);
		exit();

	}
	
	public function tz()
	{
		$a = '{"id":"1","menu":[{"text":"管理员管理","items":[{"id":"21","text":"管理员列表","href":"admin/index"}]}]}';
		
		//$b = json_decode($c);
		
		$x = array(
			'id' => '21',
			'text'=> '管理员列表',
			'href'=> 'admin/index'
		);
		
		var_dump(json_encode($x));
		print_r(json_decode($a, true));
	}
	
	//注销登录
	public function logout()
	{
		unset($_SESSION);
		header("Location:".Config::get('host_url')."/public/admin.php/admin/index/login");
		exit();
	}
	
	
	
}
