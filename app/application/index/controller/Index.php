<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;
use think\Session;
use think\Config;

class Index
{
	public function __construct()
	{
		$this->host_url = Config::get('host_url');
	}
	
	public function index()
	{
		//var_dump(Session::get('user.mobile'));die;
		$view = new View();
		$view->assign('user', Session::get('user.mobile'));
		return $view->fetch('index');
	}
	
	public function login()
	{
		$view = new View();
		$view->assign('user', Session::get('user.mobile'));
		return $view->fetch('login');
	}
	
	public function register()
	{
		//获取区域省
		$Region = model('Region');
		$province = $Region->get_province();

		$view = new View();
		$view->assign('user', Session::get('user.mobile'));
		$view->assign('province', $province);
		return $view->fetch('register');
	}
	
	//注册
	public function do_register()
	{
		$mobile = !empty($_POST['mobile']) ?  trim($_POST['mobile']) : '';
		$code = !empty($_POST['code']) ? trim($_POST['code']) : '';
		$realname = !empty($_POST['username']) ? trim($_POST['username']) : '';
		$password = !empty($_POST['password']) ? trim($_POST['password']) : '';
		$user_type = !empty($_POST['user_type']) ?  intval($_POST['user_type']) : 0;
		$company_name = !empty($_POST['company_name']) ? trim($_POST['company_name']) : '';
		$company_addr = !empty($_POST['company_addr']) ? trim($_POST['company_addr']) : '';
		$position = !empty($_POST['position']) ?  trim($_POST['position']) : '';
		$province = !empty($_POST['province']) ? intval($_POST['province']) : 0;
		$city = !empty($_POST['city']) ? intval($_POST['city']) : 0;
		$district = !empty($_POST['district']) ? intval($_POST['district']) : 0;
		
		//上传图片

		$param = array(
			'mobile'	=> $mobile,
			'username'	=> $mobile,
			'realname'	=> $realname,
			'password'	=> $password,
			'user_type'	=> $user_type,
			'company_name'	=> $company_name,
			'company_addr'	=> $company_addr,
			'position'		=> $position,
			'province'		=> $province,
			'city'			=> $city,
			'district'		=> $district,
			'last_login'	=> date("Y-m-d H:i:s", time()),
			'last_ip'		=> $_SERVER["REMOTE_ADDR"]
		);
		
		if($user_type == 2)
		{
			$param['status'] = 1;
		}
		//echo ROOT_PATH;die;
		$upload = $this->upload();
		if($upload['status']==0)
		{
			//错误
			echo "<script>alert('注册失败');history.go(-1);</script>";
		}
		else
		{
			$param['head_img'] = $upload['image'];
		}
		
		$User = model('User');
		
		$res = $User->register($param);
		if(empty($res))
		{
			echo "<script>alert('注册失败');history.go(-1);</script>";
			exit();
		}
		
		header("Location:".$this->host_url."/public/index.php/index/index");
		exit();
		
	}
	
	
	private function upload()
	{
		$files = request()->file('head');
		// 移动到框架应用根目录/public/uploads/ 目录下
		$info = $files->move(ROOT_PATH . 'public' . DS . 'uploads');
		if($info){
			// 成功上传后 获取上传信息
			//return array('name'=>$info->getFilename(), 'status'=>1);
			return array('status'=>1, 'image'=>$info->getSavename());
		}else{
			// 上传失败获取错误信息
			//return array('error'=>$files->getError(), 'status'=>0);
			return array('status'=>0, 'image'=>'');
		} 
		
	}
	
	//登录
	public function do_login()
	{
		$mobile = $_GET['mobile'];
		$password = $_GET['password'];
		
		$User = model('User');
		$res = $User->check_user($mobile, $password);
		if(empty($res))
		{
			//检查是否老用户
			$old = $User->check_old_user($mobile);
			if($old)
			{
				$default_pwd = @file_get_contents("pwd");
				if($password == $default_pwd)
				{
					$user_id = $old[0]['user_id'] ? $old[0]['user_id'] : 0;
					$mobile = $old[0]['mobile'] ? $old[0]['mobile'] : '';
					//写入session
					Session::set('user.user_id',$user_id);
					Session::set('user.mobile',$mobile);
					exit(json_encode(array('status'=>2, 'message'=>'系统升级，请尽快完善会员资料和修改密码')));
				}
			}
			exit(json_encode(array('status'=>1, 'message'=>'账号或密码不正确')));
		}
		else
		{
			$user_id = $res[0]['user_id'] ? $res[0]['user_id'] : 0;
			$mobile = $res[0]['mobile'] ? $res[0]['mobile'] : '';
		}
		
		//写入操作记录
		$param = array(
			'user_id'	=> $user_id,
			'username'	=> $mobile,
			'content'	=> $mobile.'注册成功',
		);
		$User->action_log($param);
		
		//写入session
		Session::set('user.user_id',$user_id);
		Session::set('user.mobile',$mobile);
		exit(json_encode(array('status'=>0, 'message'=>'登录成功')));
	}
	
	//校验用户是否亦存在
	public function is_exist()
	{
		$mobile = $_GET['mobile'];
		
		$User = model('User');
		$res = $User->is_exist_mobile($mobile);
		
		if($res)
		{
			$return = array('status'=>1, 'message'=>'手机号已注册');
		}
		else
		{
			$return = array('status'=>0, 'message'=>'可用');
		}
		exit(json_encode($return));
	}
	
	public function logout()
	{
		//Session::flush();
		Session::delete('user.user_id');
		Session::delete('user.mobile');
		header("Location:".$this->host_url."/public/index.php/index/index");
		exit();
	}
	
	//重置密码
	public function resetpwd()
	{

		$view = new View();
		$view->assign('user', Session::get('user.mobile'));
		$view->assign('mobile', Session::get('user.mobile'));
		return $view->fetch('resetpwd');
	}
	
	//密码重置
	public function update_pwd()
	{
		$mobile = trim($_POST['mobile']);
		$password = trim($_POST['password']);
		
		$User = model('User');
		$res = $User->update_pwd($mobile, $password);
		
		if(empty($res))
		{
			echo "<script>alert('修改失败');history.go(-1);</script>";
			exit();
		}
		else
		{
			echo "<script>alert('修改成功');window.location.href='".$this->host_url."/public/index.php/index/index/login';</script>";
			exit();
		}
	}
	
	
	
	public function rule()
	{
		$view = new View();
		$view->assign('user', Session::get('user.mobile'));
		return $view->fetch('rule');
	}

}
