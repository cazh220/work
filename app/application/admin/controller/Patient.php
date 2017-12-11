<?php
namespace app\admin\controller;
use think\Controller;
use think\View;
use think\Session;

class Patient
{
	//患者列表
    public function index()
    {
		//筛选参数
		$keyword = input("keyword");
		$dental = input("dental");
		$hospital = input("hospital");
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
		
		$Patient = Model("Patient");
		$res = $Patient->get_patient($param);
		$page = $res->render();
		
		$data = $res->toArray();
		$view = new View();
		$view->assign('patient', $data);
		$view->assign('page', $page);
		return $view->fetch('patient/index');
    }
	
	//添加
	public function patient_detail()
	{
		$patient_id = input("patient_id");
		$patient_id = !empty($patient_id) ? intval($patient_id) : 0;
		
		$Patient = Model("Patient");
		$res = $Patient->patient_detail($patient_id);
		$view = new View();
		$view->assign('detail', $res['data'][0]);
		
		return $view->fetch('patient/detail');
	}
	
	public function edit_patient()
	{
		$patient_id = input("patient_id");
		
		//获取详情
		$Patient = Model("Patient");
		$res = $Patient->patient_detail($patient_id);
		

		if ($res['data'][0]['birthday'])
		{
			$year = intval(substr($res['data'][0]['birthday'], 0, 4));
			$month = intval(substr($res['data'][0]['birthday'], 4, 2));
			$day = intval(substr($res['data'][0]['birthday'], 6, 2));
		}
		$res['data'][0]['year'] = $year ? $year : 0;
		$res['data'][0]['month'] = $month ? $month : 0;
		$res['data'][0]['day'] = $day ? $day : 0;
		
		$year_ary = range(1920,2050);
		$month_ary = range(1,12);
		$day_ary = range(1,31);
		
		// 获取省
		$Region = Model("Region");
		$province = $Region->get_region(1);
		
		$view = new View();
		$view->assign('year', $year_ary);
		$view->assign('month', $month_ary);
		$view->assign('day', $day_ary);
		$view->assign('province', $province);
		$view->assign('province_id', 1);
		$view->assign('patient', $res['data'][0]);
		return $view->fetch('patient/edit_patient');
	}
	
	
	public function edit()
	{
		$name = input("patient_name");
		$mobile = input("mobile");
		$sex = input("sex");
		$email = input("email");
		$hospital = input("hospital");
		$tooth_position = input("tooth_position");
		$doctor = input("doctor");
		$false_tooth = input("false_tooth");
		$security_code = input("security_code");
		$year = input("year");
		$month = input("month");
		$day = input("day");
		$production_unit = input("production_unit");
		$patient_id = intval(input("id"));
		
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
		
		$params = array(
			'name'			=> $name,
			'mobile'		=> $mobile,
			'sex'			=> $sex,
			'email'			=> $email,
			'hospital'		=> $hospital,
			'doctor'		=> $doctor,
			'tooth_position'=> $tooth_position,
			'false_tooth'	=> $false_tooth,
			'security_code'	=> $security_code,
			'birthday'		=> $year.$month.$day,
			'production_unit'=> $production_unit,
			'update_time'	=> date("Y-m-d H:i:s", time()),
			'patient_id'	=> $patient_id
		);

		$Patient = Model("Patient");
		$res = $Patient->update_patient($params);
		
		if ($res == 1)
		{
			$data = array(
				'admin_id'		=> Session::get('admin_id'),
				'user_id'		=> $patient_id,
				'username'		=> Session::get('username'),
				'content'		=> '编辑患者:'.$name,
				'create_time'	=> date("Y-m-d H:i:s", time()),
				'ip'			=> $_SERVER['REMOTE_ADDR']	
			);
			$User = Model("User");
			$User->insert_user_action($data);
			echo "<script>window.location.href='index';</script>";
		}
		else
		{
			echo "<script>alert('编辑患者失败');history.back();</script>";
		}
	}
	
	public function export()
	{
		require_once VENDOR_PATH.'PHPExcel.php';
		$objPHPExcel = new \PHPExcel();
        $name = '患者名单';
        $name = iconv('UTF-8', 'GBK', $name);
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
        
		//获取列表
		$Patient = Model("Patient");
		$list = $Patient->get_patient()->toArray();

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
        $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
        ->setCellValue('A1', '编号')
        ->setCellValue('B1', '患者名')
        ->setCellValue('C1', '性别')
        ->setCellValue('D1', '出生年月')
        ->setCellValue('E1', '联系电话')
        ->setCellValue('F1', '修复体类别')
		->setCellValue('G1', '牙位')
		->setCellValue('H1', '制作单位')
		->setCellValue('I1', '医疗机构')
		->setCellValue('J1', '录入人');
		
        $num = 0;
        if (!empty($list['data']) && is_array($list['data']))
        {
            foreach($list['data'] as $k => $v){
                $num=$k+2;
				$birthday = !empty($v['birthday']) ? date("Y-m-d", strtotime($v['birthday'])) : '';
				
				if ($v['sex'] == 0)
				{
					$sex = '男';
				}
				else if ($v['sex'] == 1)
				{
					$sex = '女';
				}
				else
				{
					$sex = '未知';
				}
                $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, $v['patient_id'])
                ->setCellValue('B'.$num, $v['name'])
                ->setCellValue('C'.$num, $sex)
				->setCellValue('D'.$num, $birthday)
                ->setCellValue('E'.$num, $v['mobile'])
                ->setCellValue('F'.$num, $v['false_tooth_name'])
                ->setCellValue('G'.$num, $v['tooth_position'])
                ->setCellValue('H'.$num, $v['production_unit'])
				->setCellValue('I'.$num, $v['hospital'])
				->setCellValue('J'.$num, $v['operator']);
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle('患者名单');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: applicationnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
        exit;
	}

	
}