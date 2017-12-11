<?php

/**
 * 生成四位随机数
 */
class Vcode {

	
	//生成四位数
	static public function generate_num()
	{   
		$code = "";
		$num = rand(1, 9999);
		if ($num >= 100 && $num < 1000) {
			$code = '0'.$num;
		}elseif ($num >= 10 && $num < 100) {
			$code = '00'.$num;
		}elseif ($num < 10) {
			$code = '000'.$num;
		}else{
			$code = $num;
		}
		return $code;
	}
	
	
}

?>