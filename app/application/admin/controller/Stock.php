<?php
namespace app\admin\controller;
use think\Controller;
use think\View;
use think\Session;

class Stock
{
	//出库信息
    public function index()
    {
		//筛选参数
		$keyword = trim(input("keyword"));
		$start_time = input("start_time");
		$end_time = input("end_time");
		
		$param = array(
			'keyword'		=> $keyword,
			'start_time'	=> $start_time ? $start_time." 00:00:00" : '',
			'end_time'		=> $end_time ? $end_time." 23:59:59" : ''
		);

		$Stock = Model("Stock");
		$res = $Stock->stock_list($param);
		//print_r($res);die;
		$data = $res->toArray();
		$page = $res->render();
		
		

		$view = new View();
		$view->assign('stock', $data);
		$view->assign('page', $page);
		return $view->fetch('stock/index');
    }
	
	//添加出库
	public function add()
	{
		//获取单位名称
		$Company = Model("Company");
		$company_list = $Company->get_company_list();
		//print_r($company_list);die;
		
		$view = new View();
		//print_r($_SESSION);die;
		$view->assign('record_time', date("Y-m-d H:i:s", time()));
		$view->assign('company', $company_list);
		$view->assign('username', Session::get('username'));
		return $view->fetch('stock/create');
	}
	
	//出库操作
	public function doStockOut()
	{
		$company = input("company");
		$other_company = input("other_company");
		$code_list = input("code_list");
		$mobile = input("mobile");
		$company = !empty($company) ? trim($company) : '';
		$other_company = !empty($other_company) ? trim($other_company) : '';
		$code_list = !empty($code_list) ? trim($code_list) : '';
		$mobile = !empty($mobile) ? trim($mobile) : '';		
		
		$Company = Model("Company");
		if($company == -1 && !empty($other_company))
		{
			//插入新客户单位
			$res = $Company->is_exist_company($other_company);
			if($res)
			{
				echo "<script>alert('此单位已存在');history.go(-1);</script>";
				exit();
			}
			//插入新单位
			$company_id = $Company->insert_company($other_company);
			
			if(empty($company_id))
			{
				echo "<script>alert('此单位写入失败');history.go(-1);</script>";
				exit();
			}
			
			$company = $other_company;
		}
		//获取单位名
		$company_name = $Company->get_company_name($company);
		
		//出库单号
		$Stock = Model("Stock");
		$stock_no = $Stock->create_stock_no();
		$security_code = array();
		//出库
		if(!empty($code_list))
		{
			//分割
			$Security = Model("Security");
			$code_tmp = urlencode($code_list);
			$code_arr = explode("%0D%0A", $code_tmp);	
			foreach($code_arr as $key => $val)
			{
				$val = trim($val);
				//判断是否已出库
				$stock_no_s = $Stock->view_stock_no($val);
				if($stock_no_s)
				{
					//提示已出库
					echo "<script>alert('".$val."已出库，不能重复出库');history.go(-1);</script>";
					exit();
				}
				
				//判断防伪码是否存在，如果存在则处理，如果不存在则跳过
				$res = $Security->stock_out_security_code($val, $stock_no);
				if($res)
				{
					array_push($security_code, $val);
				}
			}
			
			$data = array(
					'stock_no'		=> $stock_no,
					'stock_num'		=> count($security_code),
					'code_list' 	=> implode(',', $security_code),
					'company_name'	=> $company_name,
					'user_name'		=> Session::get('username'),
					'mobile'		=> $mobile,
					'stock_time'	=> date("Y-m-d H:i:s", time()),
				);
			//插入
			$res = $Stock->insert_stock($data);
			if(empty($res))
			{
				echo "<script>alert('出库失败');history.go(-1);</script>";
				exit();
			}
		}
		
		header("Location:http://www.yrsyc.cn/app/public/admin.php/admin/stock/index");
		exit();
	}
	
	//确认出库
	public function stockout_confirm()
	{
		$stock_id = $_GET['stock_id'];
		
		//获取防伪码
		$Stock = Model("Stock");
		$stock_info = $Stock->stock_out_detail($stock_id);
		$Security = Model("Security");
		/*
		if(isset($stock_info['code_list']))
		{
			$code_arr = explode(',', $stock_info['code_list']);
			//更新防伪码状态
			$res= $Security->update_security_code_status(2,$code_arr);
			
			if(empty($res))
			{
				echo "<script>alert('确认出库失败');history.go(-1);</script>";
			    exit();
			}
		}*/
		
		//更新出库单
		$res = $Stock->update_stock_out($stock_id);
		
		if(empty($res))
		{
			echo "<script>alert('确认出库失败');history.go(-1);</script>";
		    exit();
		}
		else
		{
			echo "<script>window.location.href='index';</script>";
			exit();
		}
	}
	
	//导出
	public function export()
	{
		//获取列表
		$Stock = model('Stock');
		$list = $Stock->stock_list()->toArray();

		$this->export_excel($list);
	}
	
	private function export_excel($list)
	{
		require_once VENDOR_PATH.'PHPExcel.php';
		$objPHPExcel = new \PHPExcel();
        $name = '出库列表';
        $name = iconv('UTF-8', 'GBK', $name);
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");


        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
        ->setCellValue('A1', '序号')
        ->setCellValue('B1', '出库单号')
        ->setCellValue('C1', '出库数量')
        ->setCellValue('D1', '出单人')
        ->setCellValue('E1', '客户单位')
        ->setCellValue('F1', '出库状态')
        ->setCellValue('G1', '出库时间')
		->setCellValue('H1', '出库的防伪码');
		
        $num = 0;
        if (!empty($list['data']) && is_array($list['data']))
        {
            foreach($list['data'] as $k => $v){
            	 if($v['status'] ==1)
            	 {
            	 	$status_name = '已出库';
            	 }
            	 else
            	 {
            	 	$status_name = '待出库';
            	 }
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, $v['stock_id'])
                ->setCellValue('B'.$num, $v['stock_no']." ")
                ->setCellValue('C'.$num, $v['stock_num'])
				->setCellValue('D'.$num, $v['user_name'])
                ->setCellValue('E'.$num, $v['company_name'])
                ->setCellValue('F'.$num, $status_name)
                ->setCellValue('G'.$num, ($v['stock_time']=='0000-00-00 00:00:00') ? '' : $v['stock_time'])
                ->setCellValue('H'.$num, $v['code_list'].' ');
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle('防伪码列表');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: applicationnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
        exit;
	}
	
	
	//判断手机号是否存在
	public function check_mobile()
	{
		$mobile = !empty($_POST['mobile']) ? $_POST['mobile'] : '';
		$User = Model("User");
		$res = $User->check_mobile($mobile);
		
		if($res)
		{
			$return = array('status'=>1, 'message'=>'已存在');
		}
		else
		{
			$return = array('status'=>0, 'message'=>'不存在');
		}
		exit(json_encode($return));
	}
	

	
}