<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;
use think\Session;

class Member
{	
	public function __construct()
	{
		$this->host_url = Config::get('host_url');
	}
	
	public function index()
	{
		//获取我的信息
		$user_id = Session::get('user.user_id');

		
		$Member = model('Member');
		$user = $Member->get_my_detail($user_id);
		if(empty($user))
		{
			//转到登录页
			header("Location:".$this->host_url."/public/index.php/index/index");
			exit();
		}
		
		if($user[0]['user_type'] == 1)
		{
			$user[0]['user_type_name'] = '技工';
		}
		else
		{
			$user[0]['user_type_name'] = '医生';
		}
		//print_r($user);die;
		//获取区域名称
		$Region = model('Region');
		$province_name = $Region->get_area($user[0]['province']);
		$city_name = $Region->get_area($user[0]['city']);
		$district_name = $Region->get_area($user[0]['district']);
		
		$user[0]['province_name'] = $province_name ? $province_name : '';
		$user[0]['city_name'] = $city_name ? $city_name : '';
		$user[0]['district_name'] = $district_name ? $district_name : '';
		
		$view = new View();
		$view->assign('user', $user[0]);
		$view->assign('users', Session::get('user.mobile'));
		return $view->fetch('index');
	}

	public function edit_member()
	{
		$user_id = Session::get('user.user_id');
		$Member = model('Member');
		$user = $Member->get_my_detail($user_id);
		//年
		$year = range(1950, 2040);
		$month = range(1, 12);
		$day = range(1, 31);
		
		//获取年月日
		//print_r($user[0]['birthday']);die;
		$temp = $user[0]['birthday'] ? explode('-', $user[0]['birthday']) : array();
		$year_s = !empty($temp[0]) ? intval($temp[0]) : '';
		$month_s = !empty($temp[1]) ? intval($temp[1]) : '';
		$day_s = !empty($temp[2]) ? intval($temp[2]) : '';
		
		//省市区
		$Region = model('Region');
		$province_list = $Region->get_province();
		//获取市列表
		$city_list = $Region->get_region($user[0]['province']);
		//获取区域
		$district_list = $Region->get_region($user[0]['city']);
		
		$view = new View();
		$view->assign('user', $user[0]);
		$view->assign('year', $year);
		$view->assign('month', $month);
		$view->assign('day', $day);
		$view->assign('year_s', $year_s);
		$view->assign('month_s', $month_s);
		$view->assign('day_s', $day_s);
		$view->assign('province_list', $province_list);
		$view->assign('city_list', $city_list);
		$view->assign('district_list', $district_list);
		$view->assign('province_s', $user[0]['province']);
		$view->assign('city_s', $user[0]['city']);
		$view->assign('district_s', $user[0]['district']);
		
		$view->assign('users', Session::get('user.mobile'));
		return $view->fetch('edit_member');
	}
	
	public function do_edit_member()
	{
		if(!empty($_FILES['head']['name']))
		{
			$upload = $this->upload();
			if($upload['status']==0)
			{
				//错误
				echo "<script>alert('更新失败');history.go(-1);</script>";
			}
			else
			{
				$head_img = $upload['image'];
			}
		}
		print_r($_POST);die;
		/*
		$year = $_POST['year'];
		$month = $_POST['month'] < 10 ? '0'.$_POST['month'] : $_POST['month'];
		$day = $_POST['day'] < 10 ? '0'.$_POST['day'] : $_POST['day'];
		$birthday = $year.'-'.$month.'-'.$day." 00:00:00";
		*/
		$data = array(
			'realname'	=> $_POST['realname'],
			//'user_type'	=> $_POST['user_type'],
			//'birthday'	=> $birthday,
			'company_name'	=> $_POST['company_name'],
			'company_addr'	=> $_POST['company_addr'],
			'company_phone'	=> $_POST['company_phone'],
			'department' 	=> $_POST['department'],
			'position'		=> $_POST['position'],
			'company_info'	=> $_POST['company_info'],
			'email'			=> $_POST['email'],
			'province'		=> $_POST['province'],
			'city'			=> $_POST['city'],
			'district'		=> $_POST['district']
		);
		
		$where['user_id'] = $_POST['user_id'];
		
		if(isset($head_img))
		{
			$data['head_img'] = addslashes($head_img);
		}
		
		$Member = model('Member');
		$res = $Member->update_user($data, $where);
		//var_dump($res);die;
		if(empty($res))
		{
			echo "<script>alert('更新失败');history.go(-1);</script>";
		}
		else
		{
			header("Location:http://www.yrsyc.cn/app/public/index.php/index/member/edit_member");
			exit();
		}
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
	
	
	//修改密码
	public function edit_password()
	{
		$user_id = Session::get('user.user_id');
		$Member = model('Member');
		$user = $Member->get_my_detail($user_id);
		//print_r($user);die;
		
		$view = new View();
		$view->assign('user', Session::get('user.mobile'));
		$view->assign('user_detail', $user[0]);
		return $view->fetch('edit_password');
	}
	
	//修改密码操作
	public function do_update_password()
	{
		$user_id = intval($_POST['user_id']);
		$old_pwd = trim($_POST['oldpwd']);
		$new_pwd = trim($_POST['newpwd']);
		//确认老密码是否正确
		$Member = model('Member');
		$user = $Member->get_my_detail($user_id);
		if($user[0]['password'] != $old_pwd)
		{
			echo "<script>alert('老密码不正确');history.go(-1);</script>";
			exit();
		}
		
		$res = $Member->update_pwd_byuserid($user_id, $new_pwd);
		if(empty($res))
		{
			echo "<script>alert('修改失败');history.go(-1);</script>";
		}
		else
		{
			echo "<script>alert('修改成功');</script>";
		}
		header("Location:http://www.yrsyc.cn/app/public/index.php/index/member/");
		exit();
	}

}
