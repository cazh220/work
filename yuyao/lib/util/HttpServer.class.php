<?php
/**
 * HTTP操作类
 * 
 * @package modules
 * @subpackage mail
 * @author 苏宁 
 * 
 * $Id: HttpServer.class.php 5 2008-08-21 02:47:17Z suning $
 */

class HttpServer
{
	public function __construct() {}

	/**
	 * 打开一个socket连接，并返回处理后的数据
	 *
	 * @param string $host 主机名
	 * @param integer $port 端口,一般为80
	 * @param string $data 写入数据
	 * @return string
	 */
	public static function getSocketData($host, $port, $data)
	{
		$errno = $error = null;
		$fp = fsockopen($host, $port, $errno, $error, 20);
		if (!$fp)
		{
			return false;
		}

		fwrite($fp, $data);
		$res = '';
		$t = '';
		while (!feof($fp))
		{
			$t = fread($fp, 128);
			if ($t === false) break; // to fix some problem
			$res .= $t;
		}
		fclose($fp);
		return $res;
	}

	/**
	 * 从HTTP头中获取COOKIE的信息
	 *
	 * @param string $data HTTP数据流
	 * @return array
	 */
	public static function getCookies($data)
	{
		preg_match_all('/Set-Cookie: ([^=]+)=([^;]*)/s', $data, $cookie_mc);

		$cookie_cnt = count($cookie_mc[1]);
		if (!empty($cookie_mc[1]) && !empty($cookie_mc[2]) && count($cookie_mc[2]) == $cookie_cnt)
		{
			$cookie_keys = $cookie_mc[1];
			$cookie_values = $cookie_mc[2];
			for ($i = 0; $i < $cookie_cnt; $i++)
			{
				$cookies[$cookie_keys[$i]] = $cookie_values[$i];
			}

			return $cookies;
		}
		else
		{
			return false;
		}
	}



	/**
	 * socket发送
	 *
	 * @para  String    $url          需连接的url地址
	 * @para  Int       $limit		  从缓冲区中读取长度$limit的数据
	 * @para  String    $post         需发送的内容 ， 为空时采用 GET 方式发送  ，  不为空采用 POST 方式   
	 * @para  String    $cookie       需要设置的cookie内容 ， 默认为空
	 * @para  Bool      $bysocket     
	 * @para  String    $ip           需连接的ip地址
	 * @para  Int       $timeout      过期时间
	 * @para  Bool      $block        设置socket连接时阻塞模式或非阻塞模式 ， 默认 true ，设置成阻塞模式
     *
	 * return  null | String
	 */
	public function dfopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE)
	{
		$return = '';
		$matches = parse_url($url);
		$host = $matches['host'];
		$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
		$port = !empty($matches['port']) ? $matches['port'] : 80;

		if($post) {
			$out = "POST $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			//$out .= "Referer: $boardurl\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host\r\n";
			$out .= 'Content-Length: '.strlen($post)."\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cache-Control: no-cache\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
			$out .= $post;
		} else {
			$out = "GET $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			//$out .= "Referer: $boardurl\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
		}
		
		$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
		if(!$fp) {
			return 'false';
			return '';
		} else {
			stream_set_blocking($fp, $block);
			stream_set_timeout($fp, $timeout);
			@fwrite($fp, $out);
			$status = stream_get_meta_data($fp);

			if(!$status['timed_out']) {
				while (!feof($fp)) {
					if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
						break;
					}
				}

				$stop = false;
				while(!feof($fp) && !$stop) {
					
					$data = fread($fp, ($limit == 0 || $limit > 1024 ? 1024 : $limit));
					$return .= $data;
					
					if($limit) {
						$limit -= strlen($data);
						$stop = $limit <= 0;
					}
					
				}
			}
			@fclose($fp);
			//echo "<pre>";print_r($return);die;
			return $return;
		}
	}

}
?>