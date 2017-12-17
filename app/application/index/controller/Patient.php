<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;
use think\Session;

class Patient
{	
	public function index()
	{
		//获取我的信息
		$user_id = Session::get('user.user_id');
		
		$param = array('page'=>1, 'page_size'=>10, 'tech_id'=>$user_id);
		$Patient = model('Patient');
		$res = $Patient->patient_list($param);
		
		if($res)
		{
			foreach($res as $key => $val)
			{
				$tooth_false_arr = explode('|', $val['tooth_position']);
				$res[$key]['tooth_position1'] = $tooth_false_arr[0];
				$res[$key]['tooth_position2'] = $tooth_false_arr[1];
				$res[$key]['tooth_position3'] = $tooth_false_arr[2];
				$res[$key]['tooth_position4'] = $tooth_false_arr[3];
			}
		}
		//print_r($res);die;
		$view = new View();
		//$view->assign('user', $user[0]);
		$view->assign('list', $res);
		$view->assign('user', Session::get('user.mobile'));
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
		
		$view = new View();
		$view->assign('user', $user[0]);
		$view->assign('year', $year);
		$view->assign('month', $month);
		$view->assign('day', $day);
		$view->assign('year_s', $year_s);
		$view->assign('month_s', $month_s);
		$view->assign('day_s', $day_s);
		$view->assign('user', Session::get('user.mobile'));
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
		
		
		$year = $_POST['year'];
		$month = $_POST['month'] < 10 ? '0'.$_POST['month'] : $_POST['month'];
		$day = $_POST['day'] < 10 ? '0'.$_POST['day'] : $_POST['day'];
		$birthday = $year.'-'.$month.'-'.$day." 00:00:00";
		
		$data = array(
			'realname'	=> $_POST['realname'],
			'user_type'	=> $_POST['user_type'],
			'birthday'	=> $birthday,
			'company_name'	=> $_POST['company_name'],
			'company_addr'	=> $_POST['company_addr'],
			'company_phone'	=> $_POST['company_phone'],
			'department' 	=> $_POST['department'],
			'position'		=> $_POST['position'],
			'company_info'	=> $_POST['company_info'],
			'email'			=> $_POST['email'],
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
			header("Location:http://huge.com/public/index.php/index/member/edit_member");
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
	
	//删除
	public function delete_patient()
	{
		$list_id	= !empty($_GET['list']) ? trim($_GET['list']) : '';

		if(empty($list_id))
		{
			$return = array('status'=>false, 'message'=>'未选择患者');
			exit(json_encode($return));
		}
		$res = false;
		$Patient = model('Patient');
		$res = $Patient->delete_patient($list_id);
		if($res)
		{
			$return = array('status'=>true, 'message'=>'删除成功');
		}
		else
		{
			$return = array('status'=>false, 'message'=>'删除失败');
			
		}
		exit(json_encode($return));
	}
	

}
