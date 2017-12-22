<?php

/**
 * 发短信
 */
class SendSms {
	private static $user = 'huge';
	private static $password = '123456';
	private static $id = 15454;
	private static $api_url = 'http://www.qf106.com/sms.aspx?action=send';
	
	//发送验证码
	static public function send_vcode($mobile, $code)
	{
		import('util.RequestCurl');
		$content = "您的验证码为：".$code.",请在30分钟内完成注册，否则验证码将失效。";
		$param = '&userid='.self::$id.'&account='.self::$user.'&password='.self::$password.'&mobile='.$mobile.'&content='.$content.'&mobilenumber=1';
		$url = self::$api_url.$param;//echo $url;
		$xmlfile = RequestCurl::curl_get($url);
		$ob= simplexml_load_string($xmlfile);
		return $ob;
	}
	
	//发送短信
	public function send_sms_template($mobile, $content)
	{
		import('util.RequestCurl');
		$content = "【HUGE】恭喜您成为沪鸽口腔防伪认证会员！您的登陆账号为".$mobile."！";
		$param = '&userid='.self::$id.'&account='.self::$user.'&password='.self::$password.'&mobile='.$mobile.'&content='.$content.'&mobilenumber=1';
		$url = self::$api_url.$param;//echo $url;
		$xmlfile = RequestCurl::curl_get($url);
		$ob= simplexml_load_string($xmlfile);
		return $ob;
	}
	
	
}

?>