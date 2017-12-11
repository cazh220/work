<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;

class Sms
{	
	private static $user = 'huge';//'dd1678';
	private static $password = '123456';
	private static $id = 15454;//15450;
	private static $api_url = 'http://www.qf106.com/sms.aspx?action=send';
	
	public function sendsms()
	{
		$mobile = $_GET['mobile'];
		$code = $this->create_code();
		$content = "您的验证码为：".$code.",请在30分钟内完成注册，否则验证码将失效。";
		$param = '&userid='.self::$id.'&account='.self::$user.'&password='.self::$password.'&mobile='.$mobile.'&content='.$content.'&mobilenumber=1';
		$url = self::$api_url.$param;//echo $url;
		$xmlfile = $this->curl_get($url);
		$ob= simplexml_load_string($xmlfile);
		$jsonStr = json_encode($ob);
		$result = json_decode($jsonStr, true);
		
		if($ob->returnstatus == 'Faild')
		{
			$data = array('status'=>0, 'message'=>$result['message']);
		}
		else
		{
			//写入到数据库
			$param = array(
				'mobile'		=> $mobile,
				'code'			=> $code,
				'update_time'	=> date("Y-m-d H:i:s", time())
			);
			$Sms = model('Sms');
			$sms_res = $Sms->write_code($param);
			if($sms_res)
			{
				$data = array('status'=>1, 'message'=>'验证码已发送');
			}
			else
			{
				$data = array('status'=>0, 'message'=>'验证码发送失败');
			}
			
		}
		
		exit(json_encode($data));
		
	}
	
	//验证码校验
	public function check_sms()
	{
		$mobile = $_GET['mobile'];
		$code = $_GET['code'];
		
		$Sms = model('Sms');
		$res = $Sms->check_sms($mobile, $code);
		//var_dump($res[0]['count']);die;
		if($res[0]['count'] == 0)
		{
			$data = array('status'=>1, 'message'=>'验证码校验失败');
		}
		else
		{
			$data = array('status'=>0, 'message'=>'ok');
		}
		
		exit(json_encode($data));
	}
	
	//生成验证码
	private function create_code()
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
	
	//curl
	public function curl_get($url)
	{
		
		//初始化
		$curl = curl_init();
		//设置抓取的url
		curl_setopt($curl, CURLOPT_URL, $url);
		//设置头文件的信息作为数据流输出
		curl_setopt($curl, CURLOPT_HEADER, 0);
		//设置获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		//执行命令
		$data = curl_exec($curl);
		//关闭URL请求
		curl_close($curl);
		//显示获得的数据
		
		return $data;
	}
	
	
	//校验验证码
	

}
