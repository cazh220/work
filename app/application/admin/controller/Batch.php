<?php
namespace app\admin\controller;
use think\Controller;
use think\View;
use think\Session;

set_time_limit(0);
ini_set('memory_limit', '256M');
/**
 * 批量操作
 */
class Batch
{
	//批量导入防伪码
	public function batch_import_security_code()
	{
		$file_type = $_GET['file_type'];
		$file_type = 'txt';
		$file_path = ROOT_PATH.'data/code.txt';
		//读取防伪码
		//echo $file_path;die;
		if($file_type == 'txt')
		{
			$contents = file_get_contents($file_path);
		}
		
		$code = explode("\n", $contents);
		
		//入库
		foreach($code as $key => $val)
		{
			if($val)
			{
				$data[$key] = array(
					'security_code'	=> $val,
					'create_time'	=> date("Y-m-d H:i:s", time()),
					'update_time'	=> date("Y-m-d H:i:s", time()),
				);
			}
			
		}
		
		$Batch = Model("Batch");
		$res = $Batch->insert_code($data);
		if($res)
		{
			echo "导入成功";
		}
		else
		{
			echo "导入失败";
		}
		//print_r($data);die;
	}
	
	
	//读取excel
	public function  readexcel()
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
	
	//批量导入防伪码
	/*
	public function batch_import_security_code()
	{
		$file_type = $_GET['file_type'];
		$file_type = 'txt';
		$file_path = ROOT_PATH.'data/code.txt';
		//读取防伪码
		//echo $file_path;die;
		if($file_type == 'txt')
		{
			$contents = file_get_contents($file_path);
		}
		
		$code = explode("\n", $contents);
		
		//入库
		foreach($code as $key => $val)
		{
			if($val)
			{
				$data[$key] = array(
					'security_code'	=> $val,
					'create_time'	=> date("Y-m-d H:i:s", time()),
					'update_time'	=> date("Y-m-d H:i:s", time()),
				);
			}
			
		}
		
		$Batch = Model("Batch");
		$res = $Batch->insert_code($data);
		if($res)
		{
			echo "导入成功";
		}
		else
		{
			echo "导入失败";
		}
		//print_r($data);die;
	}
	*/
	
	//读取防伪码excel
	public function  readexcel_fwm()
	{
		require_once VENDOR_PATH.'PHPExcel.php';
		//$PHPReader = new \PHPExcel();
		$file_path = ROOT_PATH.'data/fangweima4.xlsx';
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
    		/*
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
			*/
			$code = $currentSheet->getCellByColumnAndRow(ord('B') - 65,$currentRow)->getValue();
			$security_code[$i] = $code;
			$i++;
		}
		
		
		$batch_insert = array(
			'operater_id'		=> 1,
			'operater'			=> 'admin',
			'create_time'		=> date("Y-m-d H:i:s", time()),
			'is_delete'			=> 0,
			'num'				=> 5000,
			'path'				=> 'qrcode/20171208054534',
			'them'				=> '20171209/刘加其/诺必灵/5000/绘卡',
			'production_title'	=> 4
		);
		
		
		$Batch = Model("Batch");
		$res = $Batch->insert_security_code($batch_insert, $security_code);
		
		if($res)
		{
			echo "success";
		}
		else
		{
			echo "failed";
		}
		//print_r($security_code);die;
		
		//先插入批量的信息
		
		//$Batch = Model("Batch");
		//$res = $Batch->insert_user($data);
		//print_r($data);die;
	}
	
	
}
	
	
?>