<?php
namespace app\admin\controller;
use think\Controller;
use think\View;
use think\Session;
use think\Config;

class Security 
{
	public function index()
    {	
		//获取参数
		$_code = input("code");
		$start_time = input("start_time");
		$end_time = input("end_time");
		$type = input("type");
		
		$Security = model('Security');
		$where = array(
			'_code'		=> $_code,
			'start_time'=> $start_time,
			'end_time'	=> $end_time,
			'type'		=> $type
		);

		$data = $Security->code_list($where);
		$page = $data->render();
		$list = $data->toArray();
		if($list['data'])
		{
			foreach($list['data'] as $key => $val)
			{
				$list['data'][$key]['qrcode'] = urlencode("http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code=".$val['security_code']);
			}
		}

		//已使用数量
		$count = $Security->used_num();
        $view = new View();
		$view->assign('data', $list);
		$view->assign('page', $page);
		$view->assign('count', $count);
		return $view->fetch('security/index');
    }
    
    //防伪码批次生成列表
    public function create_list()
    {
    	//获取参数
		$operater = input("operater");
		$start_time = input("start_time");
		$end_time = input("end_time");
		
		$Security = model('Security');
		$where = array(
			'operater'		=> $operater,
			'start_time'	=> $start_time,
			'end_time'		=> $end_time
		);

		$data = $Security->create_index($where);
		$page = $data->render();
		$list = $data->toArray();
		
    	$view = new View();
		$view->assign('data', $list);
		$view->assign('page', $page);
		
		if(!empty($list['data']))
		{
			foreach($list['data'] as $key =>$val)
			{
				$list['data'][$key]['path'] = urlencode($val['path']);
			}
		}

		return $view->fetch('security/create_index');
    }
    
    //导出防伪码
    public function export_security_code()
    {
    	$batch_id = input("batch_id");
		$batch_id = !empty($batch_id) ? intval($batch_id) : 0;
		$Security = model('Security');
		$list = $Security->batch_code_all($batch_id);		
		
		require_once VENDOR_PATH.'PHPExcel.php';
		$objPHPExcel = new \PHPExcel();
        $name = '防伪码列表'.date("YmdHis", time());
        $name = iconv('UTF-8', 'GBK', $name);
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");


        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
        ->setCellValue('A1', '序号')
        ->setCellValue('B1', '防伪码序列号')
        ->setCellValue('C1', '防伪条形码链接')
        ->setCellValue('D1', '防伪二维码链接');

        $num = 0;
        if (!empty($list) && is_array($list))
        {
            foreach($list as $k => $v){
            	$url_encode = urlencode("http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code=".$v["security_code"]);
            	//$url = "http://qr.liantu.com/api.php?text=".$url_encode;
            	$url = "http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code=".$v["security_code"];
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, $v['code_id'])
                ->setCellValue('B'.$num, $v['security_code'].' ')
                ->setCellValue('C'.$num, $v['security_code'].' ')
				->setCellValue('D'.$num, $url);
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle('防伪码列表'.date('YmdHis', time()));
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: applicationnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }
    
    //批次详情
    public function batch_detail()
    {
    	//获取参数
    	$path = input("path");
		$batch_id = input("batch_id");
		$batch_id = $batch_id ? $batch_id : 0;
		$page_size = 10;

		$Security = model('Security');
		$data = $Security->batch_code($batch_id, $page_size, $path);
		$page = $data->render();
		$list = $data->toArray();
		
    	$view = new View();
		$view->assign('data', $list);
		$view->assign('page', $page);
		$view->assign('path', $path);
		return $view->fetch('security/batch_index');
    }
	
	public function create()
	{
	    //获取修复体
	    $Fixtype = model('Fixtype');
		$data = $Fixtype->get_fix_type_list();	
		
		$view = new View();
		$view->assign('list', $data);
		return $view->fetch('security/create');
	}
	
	public function doCreate()
	{
		set_time_limit(0);
		ini_set('memory_limit', '4096M');
		$this->num = input('num');
		$this->size = input('size');
		$this->prefix = input('prefix');
		$this->export = input('export');
		$this->them = input('them');
		$this->production_title = input('production_title');

		if($this->num > 5000)
		{
			echo "<script>alert('一次性生成最多5000个');history.back();</script>";
		}
		
		if($this->size < 16)
		{
			echo "<script>alert('位数不少于18位');history.back();</script>";
		}
		
		if(!preg_match("/^[a-zA-Z|\d]*$/i",$this->prefix))
		{
			echo "<script>alert('前缀必须是字母或数字');history.back();</script>";
		}
				
		$code = generate_code(0,9999,$this->num);
		
		//防伪码存储目录
		$dir = 'qrcode/'.date("Ymdhis", time());
		if(!is_dir($dir))
		{
			//chmod("runtime",7);
			mkdir($dir, 0777, true);
			//chmod($dir, 777);
		}
		$this->dir = $dir;
		
		$res = $this->security_code($code);
		if (!$res)
		{
			echo "<script>alert('生成防伪码失败');history.back();</script>";
		}
		else
		{
			if ($this->export == 1)
			{
				//导出
				if (!empty($this->list))
				{
					$this->export_code($this->list);
				}

			}
			//$this->save();
			echo "<script>window.location.href='index';</script>";
		}
	}
	
	public function save_qrcode($data)
	{
		$data = $data ? trim($data) : '';
		$qrdata = urlencode("http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code=".$data);
		//$data = isset($_GET['data']) ? $_GET['data'] : '1234'; 
		$url = "http://www.kuitao8.com/qr/view?qr=".$qrdata; 
		/*
		function GetCurl($url){ 
			$curl = curl_init(); 
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,1); 
			curl_setopt($curl,CURLOPT_URL, $url); 
			curl_setopt($curl,CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); 
			$resp = curl_exec($curl); 
			curl_close($curl); 
			return $resp; 
		}*/ 
		$content = GetCurl($url); 
		preg_match('/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png|jpeg))\"?.+>/i',$content,$arr); 
		
		$imgFile = "http://www.kuitao8.com/".$arr[1];
		$size = isset($_GET['size']) ? $_GET['size'] : '400x400'; 
		$logo = isset($_GET['logo']) ? $_GET['logo'] : ''; 
		header('Content-type: image/png'); 
		$QR = imagecreatefrompng($imgFile);
		
		if($logo){ 
			$logo = imagecreatefromstring(file_get_contents($logo)); 
			$QR_width = imagesx($QR); 
			$QR_height = imagesy($QR); 
			$logo_width = imagesx($logo); 
			$logo_height = imagesy($logo); 
			$logo_qr_width = $QR_width/3; 
			$scale = $logo_width/$logo_qr_width; 
			$logo_qr_height = $logo_height/$scale; 
			imagecopyresampled($QR, $logo, $QR_width/3, $QR_height/3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height); 
		}
		imagepng($QR);
		$save = $this->dir.'/'.$data.'.png';
		//$save = "qrq.png"; 
		imagepng($QR, $save); 
		imagedestroy($QR); 
		//exit();
	}
	
	//批量生成二维码图片
	public function save()
	{
		if(!empty($this->list))
		{
			foreach($this->list as $key => $val)
			{
				@$this->save_qrcode($val);
			}
		}
	}
	
	
	//删除防伪码
	public function delete_code()
	{
		$code_id = input("code_id");
		if (!empty($code_id))
		{
			$Security = model('Security');
			$code_arr = explode(",", $code_id);
			foreach($code_arr as $key => $val)
			{
				$res = $Security->delete_code($val);
			}
		}
		
		if ($res == 1)
		{
			echo "<script>window.location.href='index';</script>";
		}
		else
		{
			echo "<script>alert('删除防伪码失败');history.back();</script>";
		}
	}
	
	public function export()
	{
		//获取列表
		$Security = model('Security');
		$list = $Security->code_list()->toArray();
		$this->export_excel($list);
	}
	
	private function export_excel($list)
	{
		require_once VENDOR_PATH.'PHPExcel.php';
		$objPHPExcel = new \PHPExcel();
        $name = '防伪码列表';
        $name = iconv('UTF-8', 'GBK', $name);
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");


        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
        ->setCellValue('A1', 'ID')
        ->setCellValue('B1', '防伪码')
        ->setCellValue('C1', '最近查询时间')
        ->setCellValue('D1', '出库单号')
        ->setCellValue('E1', '客户单位')
        ->setCellValue('F1', '出库时间')
        ->setCellValue('G1', '生码主题')
        ->setCellValue('H1', '对应产品');

        $num = 0;
        if (!empty($list['data']) && is_array($list['data']))
        {
            foreach($list['data'] as $k => $v){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, $v['code_id'])
                ->setCellValue('B'.$num, $v['security_code'])
                ->setCellValue('C'.$num, $v['query_time'])
				->setCellValue('D'.$num, $v['stock_no'])
                ->setCellValue('E'.$num, $v['company_name'])
                ->setCellValue('F'.$num, $v['stock_time'])
                ->setCellValue('G'.$num, $v['them'])
                ->setCellValue('H'.$num, $v['production_title']);
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
	
	private function export_code($list)
	{
		require_once VENDOR_PATH.'PHPExcel.php';
		$objPHPExcel = new \PHPExcel();
        $name = '防伪码';
        $name = iconv('UTF-8', 'GBK', $name);
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");


        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
        ->setCellValue('A1', '防伪码');
		
        $num = 0;
        if (!empty($list) && is_array($list))
        {
            foreach($list as $k => $v){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, ' '.$v);
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle('防伪码');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: applicationnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
        exit;
	}
	
	private function security_code($code)
	{
		$Security = model('Security');
		if(empty($code))
		{
			return false;
		}

		foreach($code as $key => $val)
		{
			if($val < 10)
			{
				$code[$key] = '000'.$val;
			}
			elseif($val < 100)
			{
				$code[$key] = '00'.$val;
			}
			elseif($val < 1000)
			{
				$code[$key] = '0'.$val;
			}
		}
		
		//获取最后一次的数据
		$last = $Security->get_last_num();
		$last = $last ? intval($last) : 0;
		$current = $last + 1;
		if($current < 10)
		{
			$current = '00'.$current;
		}
		elseif($current < 100)
		{
			$current = '0'.$current;
		}
		elseif($current < 1000)
		{
			$current = $current;
		}
//		elseif($current < 10000)
//		{
//			$current = '0'.$current;
//		}
		//var_dump($current);die;
		//插入db
		$data = array();
		foreach($code as $key => $val)
		{
			$data[$key] = array(
				'security_code'	=> $this->prefix.'1'.$current.date("Ymd", time()).$val,
				'create_time'	=> date("Y-m-d H:i:s", time()),
				'update_time'	=> date("Y-m-d H:i:s", time()),
				'qrcode'		=> '',
				'num'			=> intval($current),
				'prefix'		=> $this->prefix,
				'production_title'=> $this->production_title,
				'them'			=> $this->them
			);
			$this->list[$key] = $this->prefix.'1'.$current.date("Ymd", time()).$val.' ';
			
		}
		
		//获取要插入的信息
		$batch = array(
			'operater_id'	=> Session::get('admin_id'),
			'operater'		=> Session::get('username'),
			'num'			=> count($data),
			'path'			=> $this->dir,
			'production_title'=> $this->production_title,
			'them'	    	=> $this->them,
			'is_delete'		=> 0,
			'create_time'	=> date("Y-m-d H:i:s", time())
		);
		
		$result = $Security->insert_code($data, $batch);
		
		return $result;
	}
	
	//下载
	function down()
	{
		$path = input("path");
		$path = !empty($path) ? trim($path) : '';
		$file = input("file");
		$file = !empty($file) ? trim($file) : '';
		
		$file_url = Config::get('host_url')."/public/".$path."/".$file.".png";
		$save_to = $file.".png";
		
		$this->download($file_url, $save_to);
	}
	
	public function download($url, $filename)
	{
	  	$ch = curl_init();
	  	curl_setopt($ch, CURLOPT_URL, $url);
	  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	  	$file = curl_exec($ch);	  	
	  	curl_close($ch);
	  	$resource = fopen($filename, 'a');
	  	fwrite($resource, $file);
	  	fclose($resource);
	}
	
	//导出二维码
	public function export_pdf()
	{
		//获取列表
		$Security = model('Security');
		$list = $Security->code_all()->toArray();//print_r($list);die;
		$this->export_qrcode($list);
	}
	
	public function export_qrcode($list)
	{
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		//$path = ROOT_PATH.'data\\qrcode\\';
		require_once VENDOR_PATH.'tcpdf/tcpdf_config.php';//加载配置
        require_once VENDOR_PATH.'tcpdf/tcpdf.php';//引入PDF类
        

        $this->pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);//实例化PDF类
        //空就不要生成了
        if (empty($list))
        {
            return true;
        }
        
      
        $this->pdf->setCreator(PDF_CREATOR);
        $this->pdf->setAuthor('huge');
        $this->pdf->setTitle('防伪码二维码');
        $this->pdf->setSubject('防伪码二维码');
        $this->pdf->setKeywords('防伪码二维码');
        
        //设置页眉和页脚
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '上海沪鸽', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $this->pdf->setFooterData(array(0,64,255), array(0,64,128));
         
        //设置页眉和页脚字体
        $this->pdf->setHeaderFont(Array('stsongstdlight', '', '10'));
        $this->pdf->setFooterFont(Array('helvetica', '', '8'));
         
        //设置默认等宽字体
        $this->pdf->SetDefaultMonospacedFont('courier');
         
        // 设置间距
        $this->pdf->setMargins(15, 20, 15);
        $this->pdf->setHeaderMargin(5);
        $this->pdf->setFooterMargin(10);
         
        //设置分页
        $this->pdf->SetAutoPageBreak(TRUE, 25);
        //set default font subsetting mode
        $this->pdf->setFontSubsetting(true);
         
        //设置字体
        $this->pdf->SetFont('cid0cs', 'B', 12);
        //$pdf->SetFont('stsongstdlight', 'B', 24);
        $this->pdf->AddPage();
         
        $subject = "防伪码二维码";
        $this->pdf->Write(0, $subject, '', 0, 'C', true, 0, false, false, 0);
         
        $this->pdf->MultiCell(0, 10, '', $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
         
        $this->pdf->SetFont('stsongstdlight', '', 12);

		$table = "";
		foreach($list['data'] as $key => $val)
		{
			if($key < 50)
			{
				$url_encode = urlencode("http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code=".$val["security_code"]);
				$table .= '<div style="float:left; width:200px"><div><img src="http://qr.liantu.com/api.php?text='.$url_encode.'" width="100px" height="100px"><br>'.$val["security_code"].'</div></div>';
			}
		}	
        //$table = mb_convert_encoding($table, 'utf8', 'latin1');
		
        $this->pdf->writeHTML($table, true, false, false, false, '');
        ob_end_clean();//清除缓冲区的内容

        $file_path = date("Y-m-d H:i:s", time()).'_security_code.pdf';//echo $file_path;die;
        //下载PDF
        $this->pdf->Output($file_path, 'D');
		
	}
	
	
	//导出防伪码pdf
    public function export_security_code_pdf()
    {
    	set_time_limit(0);
		ini_set('memory_limit', '4096M');
    	$batch_id = input("batch_id");
		$batch_id = !empty($batch_id) ? intval($batch_id) : 0;
		$Security = model('Security');
		$list = $Security->batch_code_all($batch_id);		
		
		require_once VENDOR_PATH.'tcpdf/tcpdf_config.php';//加载配置
        require_once VENDOR_PATH.'tcpdf/tcpdf.php';//引入PDF类
        
        $this->pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);//实例化PDF类
        //空就不要生成了
        if (empty($list))
        {
            return true;
        }
        
        $this->pdf->setCreator(PDF_CREATOR);
        $this->pdf->setAuthor('沪鸽');
        $this->pdf->setTitle('防伪码二维码');
        $this->pdf->setSubject('防伪码二维码');
        $this->pdf->setKeywords('防伪码二维码');
        
        //设置页眉和页脚
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '上海沪鸽', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $this->pdf->setFooterData(array(0,64,255), array(0,64,128));
         
        //设置页眉和页脚字体
        $this->pdf->setHeaderFont(Array('stsongstdlight', '', '10'));
        $this->pdf->setFooterFont(Array('helvetica', '', '8'));
         
        //设置默认等宽字体
        $this->pdf->SetDefaultMonospacedFont('courier');
         
        // 设置间距
        $this->pdf->setMargins(15, 20, 15);
        $this->pdf->setHeaderMargin(5);
        $this->pdf->setFooterMargin(10);
         
        //设置分页
        $this->pdf->SetAutoPageBreak(TRUE, 25);
        //set default font subsetting mode
        $this->pdf->setFontSubsetting(true);
         
        //设置字体
        $this->pdf->SetFont('cid0cs', 'B', 12);
        //$pdf->SetFont('stsongstdlight', 'B', 24);
        $this->pdf->AddPage();
         
        $subject = "防伪码二维码";
        $this->pdf->Write(0, $subject, '', 0, 'C', true, 0, false, false, 0);
         
        $this->pdf->MultiCell(0, 10, '', $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
         
        $this->pdf->SetFont('stsongstdlight', '', 12);

		$table = "";
		foreach($list as $key => $val)
		{
			if($key < 50)
			{
				$url_encode = urlencode("http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code=".$val["security_code"]);
				$table .= '<div style="float:left; width:200px"><div><img src="http://qr.liantu.com/api.php?text='.$url_encode.'" width="100px" height="100px"><br>'.$val["security_code"].'</div></div>';
			}
		}	
        //$table = mb_convert_encoding($table, 'utf8', 'latin1');
		
        $this->pdf->writeHTML($table, true, false, false, false, '');
        ob_end_clean();//清除缓冲区的内容

        $file_path = date("Y-m-d H:i:s", time()).'_security_code.pdf';//echo $file_path;die;
        //下载PDF
        $this->pdf->Output($file_path, 'D');
    }
    
    //导入页面
    public function import_qrcode()
    {
    	//获取修复体
	    $Fixtype = model('Fixtype');
		$data = $Fixtype->get_fix_type_list();	
		
		$view = new View();
		$view->assign('list', $data);
		return $view->fetch('security/import');
    }
    
    
    //导入防伪码
    public function import_act()
    {
    	$type = $_POST['type'] ? intval($_POST['type']) : 1;
    	$them = $_POST['them'] ? trim($_POST['them']) : '';
    	$production_title = $_POST['production_title'] ? trim($_POST['production_title']) : '';
    	//上传文件
    	// 获取表单上传文件 例如上传了001.jpg
	    $file = request()->file('qrcode_file');
	    
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    if($file){
	        $info = $file->move(ROOT_PATH . 'public' . DS . 'files');
	        if($info){
	        	$file_save = $info->getSaveName();
	            // 成功上传后 获取上传信息
	            if($type == 1)
	            {
	            	//txt
	            	$res = $this->import_txt($file_save, $them, $production_title);
	            }
	            else
	            {
	            	//excel
	            }
	            
	            if($res)
	            {
	            	echo "<script>alert('导入成功');</script>";
	            }
	            else
	            {
	            	echo "<script>alert('导入失败');history.go(-1);</script>";
	            }
	        }else{
	            // 上传失败获取错误信息
	            $error =  $file->getError();
	        }
	    }
	    
	    if(!empty($error))
	    {
	    	echo "<script>alert('".$error."');history.go(-1);</script>";
	    	exit();
	    }
	    
    }
    
    //txt导入
    public function import_txt($file, $them, $production_title)
    {
    	$file_path = ROOT_PATH . 'public' . DS . 'files'. DS .$file;
    	//echo $file_path;die;
		//读取防伪码
		$contents = file_get_contents($file_path);
		
		$code = explode("\n", $contents);
		
		if(!empty($code))
		{
			$batch = array(
				'operater_id'	=> Session::get('admin_id'),
				'operater'		=> Session::get('username'),
				'num'			=> count($code),
				'path'			=> '',
				'production_title'=> $production_title,
				'them'	    	=> $them,
				'is_delete'		=> 0,
				'create_time'	=> date("Y-m-d H:i:s", time())
			);
			foreach($code as $key => $val)
			{
				$data[$key] = array(
					'security_code'	=> $val,
					'create_time'	=> date("Y-m-d H:i:s", time()),
					'update_time'	=> date("Y-m-d H:i:s", time()),
					'qrcode'		=> '',
					'num'			=> count($code),
					'prefix'		=> '',
					'production_title'=> $production_title,
					'them'			=> $them
				);
			}
		}
		
		$Security = model('Security');
		$result = $Security->insert_code($data, $batch);
		
		
		return $result;

    }
    
    
    //导入
    public function import_excel()
    {
    	require_once VENDOR_PATH.'PHPExcel.php';
		//$PHPReader = new \PHPExcel();
		$file_path = ROOT_PATH.'data/jigong.xlsx';
		//echo $file_path;die;
		/*
		$PHPReader = new \PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($file_path)){
		    $PHPReader = new \PHPExcel_Reader_Excel5();
		    
		    if(!$PHPReader->canRead($file_path)){//print_r('xxx');die;
		        echo 'no Excel';
		        return ;
		  }
	}*/
		
		$extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION) );

		if ($extension =='xlsx') {
		    $PHPReader = new \PHPExcel_Reader_Excel2007();
		    $PHPExcel = $PHPReader->load($file_path);
		} else if ($extension =='xls') {
		    $PHPReader = new \PHPExcel_Reader_Excel5();
		    $PHPExcel = $PHPReader->load($file_path);
		} else if ($extension=='csv') {
		    $PHPReader = new \PHPExcel_Reader_CSV();
		
		    //默认输入字符集
		    $PHPReader->setInputEncoding('GBK');
		
		    //默认的分隔符
		    $PHPReader->setDelimiter(',');
		
		    //载入文件
		    $PHPExcel = $PHPReader->load($file_path);
		}
		
		
		//$PHPExcel = $PHPReader->load($file_path);
		/**读取excel文件中的第一个工作表*/
		$currentSheet = $PHPExcel->getSheet(0);
		/**取得最大的列号*/
		$allColumn = $currentSheet->getHighestColumn();
		/**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow();
		//echo $allColumn;die;
		$data = array();
		$i = 0;
		for($currentRow =2;$currentRow <= $allRow;$currentRow++){
		/**从第A列开始输出*/
			/*
			for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
    				$val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();
    				var_dump($currentColumn);var_dump($currentRow);echo $val;echo "<br>";
    				$username = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();;
					$mobile = '';
					$password = '';
					$user_type = '';
					$sex = '';
					$email = '';
					$birthday = '';
					$company_name ='';
					$company_addr = '';
					$company_phone = '',
					$department = '';
					$position = '';
					$total_credits = '';
					$exchanged_credits = '';
					$left_credits = '';
					$persons_num = '';
					$zipcode = '';
					$create_time = '';
					$status = '';
					$head_img = '';
					$province = '';
					$city = '';
					$district = '';
					$company_info = '';
					$year_tooth_num = '';
					$year_sales = '';
					$cf_band = '';
					$yhg_band = '';
					$factory = '';
					$plan = '';
					$seats = '';
					$is_grow = '';
					$is_seats = '';
					$is_correct = '';
    			}
    		}*/
			$username = $currentSheet->getCellByColumnAndRow(ord('E') - 65,$currentRow)->getValue();
			$username = mb_convert_encoding($username, 'UTF-8', mb_detect_encoding($username));
			//var_dump($username);var_dump(mb_detect_encoding($username));
			$mobile = $currentSheet->getCellByColumnAndRow(ord('I') - 65,$currentRow)->getValue();
			$password = 'huge123456';
			$user_type = 1;
			$sex_name = $currentSheet->getCellByColumnAndRow(ord('D') - 65,$currentRow)->getValue();
			$sex = ($sex_name=='男') ? 0 : 1;
			$realname = $currentSheet->getCellByColumnAndRow(ord('B') - 65,$currentRow)->getValue();
			//var_dump($realname);var_dump(mb_detect_encoding($realname));die;
			$email = $currentSheet->getCellByColumnAndRow(ord('K') - 65,$currentRow)->getValue();
			$birthday = '';
			$company_name =$currentSheet->getCellByColumnAndRow(ord('G') - 65,$currentRow)->getValue();
			$company_addr = $currentSheet->getCellByColumnAndRow(ord('H') - 65,$currentRow)->getValue();
			$company_phone = $currentSheet->getCellByColumnAndRow(ord('J') - 65,$currentRow)->getValue();
			$department = '';
			$position = $currentSheet->getCellByColumnAndRow(ord('L') - 65,$currentRow)->getValue();
			$total_credits = '';
			$exchanged_credits = '';
			$left_credits = '';
			$persons_num = $currentSheet->getCellByColumnAndRow(ord('N') - 65,$currentRow)->getValue();
			$zipcode = '';
			$create_time = '';
			$status = '';
			$head_img = '';
			$province = '';
			$city = '';
			$district = '';
			$company_info = '';
			$year_tooth_num = $currentSheet->getCellByColumnAndRow(ord('O') - 65,$currentRow)->getValue();
			$year_sales = $currentSheet->getCellByColumnAndRow(ord('P') - 65,$currentRow)->getValue();
			$cf_band = $currentSheet->getCellByColumnAndRow(ord('R') - 65,$currentRow)->getValue();
			$yhg_band = $currentSheet->getCellByColumnAndRow(ord('Q') - 65,$currentRow)->getValue();
			$factory = $currentSheet->getCellByColumnAndRow(ord('S') - 65,$currentRow)->getValue();
			$plan = $currentSheet->getCellByColumnAndRow(ord('T') - 65,$currentRow)->getValue();
			$seats = '';
			$is_grow = '';
			$is_seats = '';
			$is_correct = '';
				
			$data[$i]['username']	= $username;
			$data[$i]['mobile']	= $mobile;
			$data[$i]['password']	= $password;
			$data[$i]['user_type']	= $user_type;
			$data[$i]['sex']	= $sex;
			
			$data[$i]['realname']	= $realname;
			$data[$i]['email']	= $email;
			$data[$i]['company_name']	= $company_name;
			$data[$i]['company_addr']	= $company_addr;
			$data[$i]['company_phone']	= $company_phone;
			$data[$i]['position']	= $position;
			$data[$i]['persons_num']	= $persons_num ? $persons_num : 0;
		
			$data[$i]['year_tooth_num']	= $year_tooth_num ? $year_tooth_num : 0;
			$data[$i]['year_sales']	= $year_sales ? $year_sales : 0;
			$data[$i]['cf_band']	= $cf_band;
			$data[$i]['yhg_band']	= $yhg_band;
			$data[$i]['factory']	= $factory ? $factory : 0;
			$data[$i]['plan']	= $plan ?  $plan  : 0;
			
			$i++;
		}
		
		$Batch = Model("Batch");
		$res = $Batch->insert_user($data);
		//print_r($data);die;
    }

}