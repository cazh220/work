<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;
use think\Session;

class Credits
{	
	public function search_credits()
	{
		$Credits = model('Credits');
		$mine_credits = $Credits->query_credists(Session::get('user.user_id'));
		$view = new View();
		$view->assign('credits', $mine_credits[0]);
		$view->assign('user', Session::get('user.mobile'));
		return $view->fetch('index');
	}
	
	//积分明细
	public function detail()
	{
		$user_id = Session::get('user.user_id');
		$Credits = model('Credits');
		$res  = $Credits->get_credits_detil($user_id);
		if($res)
		{
			foreach($res as $key => $val)
			{
				if($val['type'] == 0)
				{
					$res[$key]['type_name'] = '注册';
				}
				elseif($val['type'] == 1)
				{
					$res[$key]['type_name'] = '录入信息';
				}
				elseif($val['type'] == 2)
				{
					$res[$key]['type_name'] = '兑换';
				}
				else
				{
					$res[$key]['type_name'] = '其他';
				}
			}
		}
		//print_r($res);die;
		
		$view = new View();
		$view->assign('list', $res);
		$view->assign('users', Session::get('user.mobile'));
		return $view->fetch('detail');
	}
	
	//输入条码
	public function write_security_code()
	{
		$view = new View();
		$view->assign('users', Session::get('user.mobile'));
		return $view->fetch('code');
	}
	
	//技工录入
	public function add_patient_techer()
	{
		$security_code = !empty($_POST['code']) ? $_POST['code'] : '';
		$user_id = Session::get('user.user_id');
		//获取会员信息
		$Member = model('Member');
		$user = $Member->get_my_detail($user_id);
		$Patient = model('Patient');
		$patient_detail = array();
		$patient_detail = $Patient->get_patient_detail($security_code);
		
		$tooth_position = !empty($patient_detail['tooth_position']) ? explode('|', $patient_detail['tooth_position']) : array();
		
		if(empty($patient_detail))
		{
			$patient_detail['hospital'] = '';
			$patient_detail['doctor'] = '';
			$patient_detail['name'] = '';
			$patient_detail['sex'] = 1;
			$patient_detail['year'] = '';
			$patient_detail['month'] = '';
			$patient_detail['day'] = '';
			$patient_detail['tooth_position1'] = '';
			$patient_detail['tooth_position2'] = '';
			$patient_detail['tooth_position3'] = '';
			$patient_detail['tooth_position4'] = '';
			$patient_detail['false_tooth'] = '';
			$patient_detail['repairosome_pic'] = '';
			$patient_detail['patient_id'] = '';
			$patient_detail['birthday'] = '';
		}
		else
		{
			$patient_detail['tooth_position1'] = !empty($tooth_position[0]) ? $tooth_position[0] : '';
			$patient_detail['tooth_position2'] = !empty($tooth_position[1]) ? $tooth_position[1] : '';
			$patient_detail['tooth_position3'] = !empty($tooth_position[2]) ? $tooth_position[2] : '';
			$patient_detail['tooth_position4'] = !empty($tooth_position[3]) ? $tooth_position[3] : '';
		}
		
	
		$year = range(1950, 2040);
		$month = range(1, 12);
		$day = range(1, 31);
		
		$temp = $user[0]['birthday'] ? explode('-', $user[0]['birthday']) : array();

		$year_s = !empty($temp[0]) ? intval($temp[0]) : '';
		$month_s = !empty($temp[1]) ? intval($temp[1]) : '';
		$day_s = !empty($temp[2]) ? intval(substr($temp[2], 0, 2)) : '';
		$view = new View();
		$view->assign('patient', $patient_detail);
		$view->assign('users', Session::get('user.mobile'));
		$view->assign('user_id', $user_id);
		$view->assign('security_code', $security_code);
		$view->assign('user_detail', $user[0]);
		$view->assign('year', $year);
		$view->assign('month', $month);
		$view->assign('day', $day);
		$view->assign('year_s', $year_s);
		$view->assign('month_s', $month_s);
		$view->assign('day_s', $day_s);
		return $view->fetch('add_patient_techer');
	}
	
	//技工录入
	public function do_edit_member()
	{
		$hospital = !empty($_POST['hospital']) ? $_POST['hospital'] : '';
		$doctor = !empty($_POST['doctor']) ? $_POST['doctor'] : '';
		$name = !empty($_POST['name']) ? $_POST['name'] : '';
		$tooth_position1 = !empty($_POST['tooth_position1']) ? $_POST['tooth_position1'] : '';
		$tooth_position2= !empty($_POST['tooth_position2']) ? $_POST['tooth_position2'] : '';
		$tooth_position3= !empty($_POST['tooth_position3']) ? $_POST['tooth_position3'] : '';
		$tooth_position4 = !empty($_POST['tooth_position4']) ? $_POST['tooth_position4'] : '';
		$birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : '';
		if(empty($hospital))
		{
			echo "<script>alert('医疗机构不为空');history.go(-1);</script>";
			exit();
		}
		elseif(empty($doctor))
		{
			echo "<script>alert('医生不为空');history.go(-1);</script>";
			exit();
		}
		elseif(empty($name))
		{
			echo "<script>alert('患者不为空');history.go(-1);</script>";
			exit();
		}
		elseif(empty($birthday))
		{
			echo "<script>alert('生日不为空');history.go(-1);</script>";
			exit();
		}
		elseif(empty($tooth_position1) && empty($tooth_position2) && empty($tooth_position3) && empty($tooth_position4))
		{
			echo "<script>alert('至少填一个牙位');history.go(-1);</script>";
			exit();
		}

		if(!empty($_FILES['repairosome_pic']['name']))
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
		
		//$birthday = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'].' 00:00:00';
		$tooth_position = $_POST['tooth_position1'].'|'.$_POST['tooth_position2'].'|'.$_POST['tooth_position3'].'|'.$_POST['tooth_position4'];
			
		//获取录入人信息
		$user_id = $_POST['user_id'];
		$Member = model('Member');
		$user = $Member->get_my_detail($user_id);
		
		$data = array(
			'hospital'	=> $hospital,
			'doctor'	=> $doctor,
			'name'		=> $name,
			'sex'		=> $_POST['sex'],
			'operator'	=> $user[0]['realname'],
			//'birthday'	=> date("Y-m-d H:i:s", strtotime($birthday)),
			'birthday'	=> intval($_POST['birthday']),
			'tooth_position'=> $tooth_position,
			'false_tooth'	=> $_POST['false_tooth'],
			//'repairosome_pic'	=> $head_img,
			'operate_user_id'	=> $user_id,
			'tech_id'	=> $user_id,
			'production_unit'	=> $user[0]['company_name'],
			'security_code'	=> $_POST['security_code']
		);

		//$where['user_id'] = $user_id;
		if(isset($head_img))
		{
			$data['repairosome_pic'] = addslashes($head_img);
		}
		$Patient = model('Patient');
		//print_r($data);die;
		$patient_id = $_POST['patient_id'];
		if($patient_id)
		{
			//更新
			$res  = $Patient->update_patient($data, $patient_id);
		}
		else
		{
			$res  = $Patient->insert_patient($data);
		}
		
		//var_dump($res);die;
		if(empty($res))
		{
			echo "<script>alert('添加失败');history.go(-1);</script>";
		}
		else
		{
			echo "<script>alert('录入成功');window.location.href='http://www.yrsyc.cn/app/public/index.php/index/credits/write_security_code';</script>";
			//header("Location:http://www.yrsyc.cn/app/public/index.php/index/credits/write_security_code");
			exit();
		}
		
	}
	
	private function upload()
	{
		$files = request()->file('repairosome_pic');
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

	

}
