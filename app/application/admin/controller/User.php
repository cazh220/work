<?php
namespace app\admin\controller;
use think\Controller;
use think\View;
use think\Session;

class User
{
    public function index()
    {
		//筛选参数
		$keyword = input("keyword");
		$dental = input("dental");
		$hospital = input("hospital");
		$start_time = input("start_time");
		$end_time = input("end_time");
		$keyword = !empty($keyword) ? addslashes(trim($keyword)) : '';
		$dental = !empty($dental) ? addslashes(trim($dental)) : '';
		$hospital = !empty($hospital) ? addslashes(trim($hospital)) : '';
		$start_time = !empty($start_time) ? addslashes(trim($start_time)) : '';
		$end_time = !empty($end_time) ? addslashes(trim($end_time)) : '';
		$param = array(
			'keyword'	=> $keyword,
			'dental'	=> $dental,
			'hospital'	=> $hospital,
			'start_time'=> $start_time,
			'end_time'	=> $end_time
		);
		
		$User = Model("User");
		$res = $User->user_list($param);
		$page = $res->render();
		
		$data = $res->toArray();

		if($data['data'])
		{
			$Patient = Model("Patient");
			$Stock = model('Stock');
			foreach($data['data'] as $key => $val)
			{
				
				$data['data'][$key]['record_count'] = $Patient->my_record_count($val['user_id']);
				$data['data'][$key]['stock_count'] = $Stock->stock_out_num($val['mobile']);
			}
		}
		
		$view = new View();
		$view->assign('user', $data);
		$view->assign('page', $page);
		return $view->fetch('user/index');
    }
	
	public function add()
	{
		$year = range(1920,2050);
		$month = range(1,12);
		$day = range(1,31);
		
		$year_now = date("Y", time());
		
		$view = new View();
		$view->assign('year', $year);
		$view->assign('month', $month);
		$view->assign('day', $day);
		$view->assign('year_now', $year_now);
		return $view->fetch('user/add');
	}
	
	public function add_user()
	{
		$username = input("username");
		$password = input("password");
		$mobile = input("mobile");
		$realname = input("realname");
		$user_type = input("user_type");
		$sex = input("sex");
		$email = input("email");
		$company_name = input("company_name");
		$company_addr = input("company_addr");
		$company_phone = input("company_phone");
		$department = input("department");
		$position = input("position");
		$year = input("year");
		$month = input("month");
		$day = input("day");
		$person_num = input("person_num");
		$zipcode = input("zipcode");
		$company_info = input('company_info');
		
		$year_tooth_num = input('year_tooth_num');
		$year_sales = input('year_sales');
		$yhg_band = input('yhg_band');
		$cf_band = input('cf_band');
		$factory = input('factory');
		$plan = input('plan');
		$seats = input('seats');
		$is_grow = input('is_grow');
		$is_seats = input('is_seats');
		$is_correct = input('is_correct');
		
		//生日验证
		if ($month < 10)
		{
			$month = "0".$month;
		}
		if ($day < 10)
		{
			$day = "0".$day;
		}
		
		$time = $year."-".$month."-".$day." 00:00:00";
		if (strtotime($time) == false)
		{
			echo "<script>alert('生日日期选择错误');history.back();</script>";
		}
		
		$pic = $this->upload();
		$pic = date("Ymd", time())."/".$pic;
		
		$params = array(
			'username'		=> $username,
			'password'		=> $password,//md5($password),
			'mobile'		=> $mobile,
			'realname'		=> $realname,
			'user_type'		=> $user_type,
			'sex'			=> $sex,
			'email'			=> $email,
			'company_name'	=> $company_name,
			'company_phone'	=> $company_phone,
			'company_addr'	=> $company_addr,
			'department'	=> $department,
			'position'		=> $position,
			'birthday'		=> $year.$month.$day,
			'persons_num'	=> $person_num,
			'zipcode'		=> $zipcode,
			'create_time'	=> date("Y-m-d H:i:s", time()),
			'company_info'	=> $company_info,
			'head_img'		=> $pic
		);
		
		if($user_type==1)
		{
			$params['year_tooth_num'] = intval($year_tooth_num);
			$params['year_sales'] = intval($year_sales);
			$params['yhg_band'] = ($yhg_band);
			$params['cf_band'] = ($cf_band);
			$params['factory'] = ($factory);
			$params['plan'] = ($plan);
		}
		else
		{
			$params['seats'] = intval($seats);
			$params['is_grow'] = intval($is_grow);
			$params['is_seats'] = intval($is_seats);
			$params['is_correct'] = intval($is_correct);
		}
		

		$User = Model("User");
		$res = $User->insert_user($params);
		
		if ($res == 1)
		{
			$data = array(
				'admin_id'		=> Session::get('admin_id'),
				'user_id'		=> 0,
				'username'		=> Session::get('username'),
				'content'		=> '新增会员:'.$username,
				'create_time'	=> date("Y-m-d H:i:s", time()),
				'ip'			=> $_SERVER['REMOTE_ADDR']	
			);
			$User->insert_user_action($data);
			echo "<script>window.location.href='index';</script>";
		}
		else
		{
			echo "<script>alert('新增会员失败');history.back();</script>";
		}
		
		
	}
	
	public function upload()
	{
		$files = request()->file('logo');
		if($files)
		{
			// 移动到框架应用根目录/public/uploads/ 目录下
			$info = $files->move(ROOT_PATH . 'public' . DS . 'uploads');
			if($info){
				return $info->getFilename();
				// 成功上传后 获取上传信息
				//return array('name'=>$info->getFilename(), 'status'=>1);
			}else{
				echo $files->getError();
				// 上传失败获取错误信息
				//return array('error'=>$files->getError(), 'status'=>0);
			} 
		}
		else
		{
			return '';
		}
		
	}
	
	//判断用户名是否存在
	public function is_exist_user()
	{
		$username = $_GET['username'];
		$User = Model("User");
		$res = $User->is_exist_user($username);
		
		if($res)
		{
			$return = array('status'=>1, 'message'=>'已存在');
		}
		else
		{
			$return = array('status'=>0, 'message'=>'ok');
		}
		exit(json_encode($return));
	}
	
	public function user_detail()
	{
		$user_id = input("user_id");
		$user_id = !empty($user_id) ? intval($user_id) : 0;
		
		$User = Model("User");
		$res = $User->user_detail($user_id);
		if(isset($res[0]['birthday']))
		{
			$res[0]['birthday'] = substr($res[0]['birthday'],0,4).'年'.intval(substr($res[0]['birthday'],4,2)).'月'.intval(substr($res[0]['birthday'],6,2)).'日';
		}
		
		//扫描次数
		$Patient = Model("Patient");
		$count = $Patient->my_record_count($user_id);

		//获取地址
		$address = $User->get_address($res[0]['address_id']);
		$province = $User->get_area($res[0]['province']);
		$city = $User->get_area($res[0]['city']);
		$district = $User->get_area($res[0]['district']);
		$column = '';
		if($res[0]['user_type'] == 1)
		{
			$column = 'tech';
		}
		else
		{
			$column = 'doctor';
		}
		
		//获取最后扫描事件
		$last = $Patient->get_last_patient($user_id, $column);
		$last_time = !empty($last[0]['create_time']) ? $last[0]['create_time'] : '';

		$view = new View();
		$view->assign('detail', $res[0]);
		$view->assign('address', $address[0]);
		$view->assign('province', $province[0]['name']);
		$view->assign('city', $city[0]['name']);
		$view->assign('district', $district[0]['name']);
		$view->assign('count', $count);
		$view->assign('last_time', $last_time);
		return $view->fetch('user/detail');
	}
	
	public function edit_user()
	{
		$user_id = input("user_id");
		//var_dump($user_id);
		
		//获取详情
		$User = Model("User");
		$res = $User->user_detail($user_id);
		
		if ($res[0]['birthday'])
		{
			$year = intval(substr($res[0]['birthday'], 0, 4));
			$month = intval(substr($res[0]['birthday'], 4, 2));
			$day = intval(substr($res[0]['birthday'], 6, 2));
		}
		$res[0]['year'] = $year ? $year : 0;
		$res[0]['month'] = $month ? $month : 0;
		$res[0]['day'] = $day ? $day : 0;
		
		$year_ary = range(1920,2050);
		$month_ary = range(1,12);
		$day_ary = range(1,31);

		$view = new View();
		$view->assign('year', $year_ary);
		$view->assign('month', $month_ary);
		$view->assign('day', $day_ary);
		$view->assign('user', $res[0]);
		$view->assign('user_type', $res[0]['user_type']);
		return $view->fetch('user/edit_user');
	}
	
	public function edit()
	{
		$username = input("username");
		$mobile = input("mobile");
		$realname = input("realname");
		$user_type = input("user_type");
		$sex = input("sex");
		$email = input("email");
		$company_name = input("company_name");
		$company_addr = input("company_addr");
		$company_phone = input("company_phone");
		$department = input("department");
		$position = input("position");
		$year = input("year");
		$month = input("month");
		$day = input("day");
		$person_num = input("person_num");
		$zipcode = input("zipcode");
		$user_id = intval(input("id"));
		
		$company_info = input('company_info');
		$year_tooth_num = input('year_tooth_num');
		$year_sales = input('year_sales');
		$yhg_band = input('yhg_band');
		$cf_band = input('cf_band');
		$factory = input('factory');
		$plan = input('plan');
		$seats = input('seats');
		$is_grow = input('is_grow');
		$is_seats = input('is_seats');
		$is_correct = input('is_correct');
		
		//生日验证
		if ($month < 10)
		{
			$month = "0".$month;
		}
		if ($day < 10)
		{
			$day = "0".$day;
		}
		
		$time = $year."-".$month."-".$day." 00:00:00";
		if (strtotime($time) == false)
		{
			echo "<script>alert('生日日期选择错误');history.back();</script>";
		}
		
		$pic = $this->upload();

		$params = array(
			'username'		=> $username,
			'mobile'		=> $mobile,
			'realname'		=> $realname,
			'user_type'		=> $user_type,
			'sex'			=> $sex,
			'email'			=> $email,
			'company_name'	=> $company_name,
			'company_phone'	=> $company_phone,
			'company_addr'	=> $company_addr,
			'department'	=> $department,
			'position'		=> $position,
			'birthday'		=> $year.$month.$day,
			'persons_num'	=> $person_num,
			'zipcode'		=> $zipcode,
			'create_time'	=> date("Y-m-d H:i:s", time()),
			'company_info'	=> $company_info,
			'user_id'		=> $user_id
		);
		
		//有图片上传时
		if(!empty($pic))
		{
			$pic = date("Ymd", time())."/".$pic;
			$params['head_img'] = $pic;
		}
		
		if($user_type==1)
		{
			$params['year_tooth_num'] = intval($year_tooth_num);
			$params['year_sales'] = intval($year_sales);
			$params['yhg_band'] = trim($yhg_band);
			$params['cf_band'] = trim($cf_band);
			$params['factory'] = trim($factory);
			$params['plan'] = trim($plan);
		}
		else
		{
			$params['seats'] = intval($seats);
			$params['is_grow'] = intval($is_grow);
			$params['is_seats'] = intval($is_seats);
			$params['is_correct'] = intval($is_correct);
		}

		$User = Model("User");
		$res = $User->update_user($params);
		
		if ($res == 1)
		{
			$data = array(
				'admin_id'		=> Session::get('admin_id'),
				'user_id'		=> $user_id,
				'username'		=> Session::get('username'),
				'content'		=> '编辑会员:'.$username,
				'create_time'	=> date("Y-m-d H:i:s", time()),
				'ip'			=> $_SERVER['REMOTE_ADDR']	
			);
			$User->insert_user_action($data);
			echo "<script>window.location.href='index';</script>";
		}
		else
		{
			echo "<script>alert('编辑会员失败');history.back();</script>";
		}
	}
	
	public function delete_user()
	{
		$user_id = input("user_id");
		if (!empty($user_id))
		{
			$User = Model("User");
			$user_arr = explode(",", $user_id);
			foreach($user_arr as $key => $val)
			{
				$res = $User->delete_user($val);
			}
		}
		
		if ($res == 1)
		{
			echo "<script>window.location.href='index';</script>";
		}
		else
		{
			echo "<script>alert('删除会员失败');history.back();</script>";
		}
	}
	
	//审核状态
	public function audit()
	{
		$user_id = input("user_id");
		$type = input("type");
		
		$status = ($type=='pass') ? 1 : 2;
		if (!empty($user_id))
		{
			$User = Model("User");
			$res = $User->audit_user($user_id, $status);
		}
		
		if ($res == 1)
		{
			echo "<script>window.location.href='index';</script>";
		}
		else
		{
			echo "<script>alert('审核失败');history.back();</script>";
		}
		
	}
	
	
	public function export()
	{
		require_once VENDOR_PATH.'PHPExcel.php';
		$objPHPExcel = new \PHPExcel();
        $name = '会员名单';
        $name = iconv('UTF-8', 'GBK', $name);
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
        
		//获取列表
		$User = Model("User");
		$list = $User->user_list()->toArray();
		
		if($list['data'])
		{
			$Patient = Model("Patient");
			foreach($list['data'] as $key => $val)
			{
				
				$list['data'][$key]['record_count'] = $Patient->my_record_count($val['user_id']);
			}
		}

        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('K1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('L1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('M1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('N1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('O1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('P1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('Q1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('R1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('S1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('T1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('U1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('V1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
        ->setCellValue('A1', '账户名')
        ->setCellValue('B1', '手机号')
        ->setCellValue('C1', '真实姓名')
        ->setCellValue('D1', '密码')
        ->setCellValue('E1', '会员类型')
        ->setCellValue('F1', '性别')
		->setCellValue('G1', '电子邮箱')
		->setCellValue('H1', '出生年月')
		->setCellValue('I1', '单位名称')
		->setCellValue('J1', '单位地址')
		->setCellValue('K1', '单位电话')
		->setCellValue('L1', '部门')
		->setCellValue('M1', '职位')
		->setCellValue('N1', '累计积分')
		->setCellValue('O1', '已兑换积分')
		->setCellValue('P1', '积分余额')
		->setCellValue('Q1', '人员数')
		->setCellValue('R1', '邮编')
		->setCellValue('S1', '审核状态')
        ->setCellValue('T1', '创建时间')
        ->setCellValue('U1', '录入数量')
        ->setCellValue('V1', '出库数量');
		
        $num = 0;
        if (!empty($list['data']) && is_array($list['data']))
        {
            foreach($list['data'] as $k => $v){
                $num=$k+2;
				if($v['user_type'] == 1)
				{
					$user_type = '技工所';
				}
				else if($v['user_type'] == 1)
				{
					$user_type = '医生';
				}
				else
				{
					$user_type = '其他';
				}
				
				if ($v['sex'] == 1)
				{
					$sex = '男';
				}
				else if ($v['sex'] == 2)
				{
					$sex = '女';
				}
				else
				{
					$sex = '未知';
				}
                $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, $v['username'])
                ->setCellValue('B'.$num, $v['mobile'])
                ->setCellValue('C'.$num, $v['realname'])
				->setCellValue('D'.$num, $v['password'])
                ->setCellValue('E'.$num, $user_type)
                ->setCellValue('F'.$num, $v['sex'])
                ->setCellValue('G'.$num, $v['email'])
                ->setCellValue('H'.$num, $v['birthday'])
				->setCellValue('I'.$num, $v['company_name'])
				->setCellValue('J'.$num, $v['company_addr'])
				->setCellValue('K'.$num, $v['company_phone'])
				->setCellValue('L'.$num, $v['department'])
				->setCellValue('M'.$num, $v['position'])
				->setCellValue('N'.$num, $v['total_credits'])
				->setCellValue('O'.$num, $v['exchanged_credits'])
				->setCellValue('P'.$num, $v['left_credits'])
				->setCellValue('Q'.$num, $v['persons_num'])
				->setCellValue('R'.$num, $v['zipcode'])
				->setCellValue('S'.$num, $v['status'])
				->setCellValue('T'.$num, $v['create_time'])
				->setCellValue('U'.$num, $v['record_count'])
				->setCellValue('V'.$num, 0);
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle('会员名单');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: applicationnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
        exit;
	}
	
	public function history()
	{
		$user_id = input("user_id");
		$user_id = !empty($user_id) ? intval($user_id) : 0;
		
		$User = Model("User");
		$list = $User->getHistory($user_id);
		$view = new View();
		$view->assign('list', $list->toArray());
		$view->assign('page', $list->render());
		return $view->fetch('user/user_action');
	}
	
	//老会员密码
	public function default_pwd()
	{
		$pwd = @file_get_contents('pwd');
		$view = new View();
		$view->assign('pwd', $pwd);
		return $view->fetch('user/default_pwd');
	}
	
	public function pwd_default()
	{
		$pwd = $_POST['pwd'];
		
		@file_put_contents('pwd',$pwd);
		
		echo "<script>alert('保存成功');window.location.href='../user/default_pwd';</script>";
	}
	
	
	public function reset_pwd()
	{
		$user_id = !empty($_GET['user_id']) ? intval($_GET['user_id']) : 0;
		
		$User = Model("User");
		$res = $User->reset_pwd($user_id);
		$username = Session::get('username');
		$data = array(
			'admin_id'		=> Session::get('admin_id'),
			'user_id'		=> $user_id,
			'username'		=> $username,
			'content'		=> '恢复初始密码:'.$user_id,
			'create_time'	=> date("Y-m-d H:i:s", time()),
			'ip'			=> $_SERVER['REMOTE_ADDR']	
		);//print_r($data);die;
		$User->insert_user_action($data);
		//插入操作记录
		if($res)
		{
			exit(json_encode(array('status'=>0, 'message'=>'重置成功')));
		}
		else
		{
			exit(json_encode(array('status'=>1, 'message'=>'重置失败')));
		}
	}
	
	
	
	
}