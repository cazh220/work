<?php
/**
 *  HTTPSQS日志 
 *
 * @package modules
 * @subpackage LogSqs
 * @author  jerry<feng.jiang@lyceem.com>
 *
 * $Id$
 */
class LogSqs{
	
	/**
     * 应用程序对像
     *
     * @var Application
     */
    private $app = null;
	
	
	function __construct($data=null,$type='')
	{
		global $app;
		// $this->app=$app;
		// $this->sendLogData($data,$type);
	}
	
	/**
	 * 发送日志信息到HTTPSQS
	 */
	function sendLogData($data,$type){
		global $cfg;
		$host=$cfg['LogSqs']['host'];
		$port=$cfg['LogSqs']['port'];
		$charset=$cfg['LogSqs']['charset'];
		$name=$cfg['LogSqs']['name'];
		$sys=$cfg['LogSqs']['sys'];
		$mo=($_GET['mo']) ? '&mo='.ucfirst($_GET['mo']) : '';
		$act=$_SERVER['SCRIPT_NAME'].(($_GET['do']) ? '?do='.ucfirst($_GET['do']) : 'Default').$mo;
		
		$ip=$this->get_client_ip();
		
		if( $type=="string")
		{
			$request=serialize($data);
		}else{
			$request=serialize($_REQUEST);
		}

		$order_id=!empty($_REQUEST['order_sn'])?$_REQUEST['order_sn']:0;
		switch($sys){
			case 2:$user_id=!empty($_SESSION['admin_user']['user_id'])?$_SESSION['admin_user']['user_id']:0;break;
			case 97:;
			case 53:;
			case 97:;
			case 3:$user_id=!empty($_SESSION['user_id'])?$_SESSION['user_id']:0;break;
			default:$user_id=0;
		}
		

		$log_time=$_SERVER['REQUEST_TIME'];	
		$send=array(
			'user_id'=>$user_id,
			
			'sys'=>$sys,
			'act'=>$act,
			'request'=>$request,
			'ip'=>$ip,
			'log_time'=>$log_time,
		);
		$send=urlencode(json_encode($send));
		$result = $this->put($host, $port, $charset, $name, $send);
		if (!$result)$this->_log(array(''.__CLASS__.'.class.php line '.__LINE__,'function '.__FUNCTION__.' HttpSQS_PUT_ERR '.'Host:'.$host.' Port:'.$port.' name:'.$name, date("Y-m-d H:i:s")));
		if (!$result)$this->_log(array(''.__CLASS__.'.class.php line '.__LINE__,'function '.__FUNCTION__.' HttpSQS_PUT_ERR '.$send, date("Y-m-d H:i:s")));
		return true;
	}

	/**
	 * 返回客户端IP
	 * @return string
	 */
	function get_client_ip() {
		if ($_SERVER['HTTP_CLIENT_IP'] && $_SERVER['HTTP_CLIENT_IP']!='unknown') {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['HTTP_X_FORWARDED_FOR']!='unknown') {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	
	function http_get($host, $port, $query)
    {
        $httpsqs_socket = @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$httpsqs_socket)
        {
            return false;
        }
        $out = "GET ${query} HTTP/1.1\r\n";
        $out .= "Host: ${host}\r\n";
        $out .= "Connection: close\r\n";
        $out .= "\r\n";
        fwrite($httpsqs_socket, $out);
        $line = trim(fgets($httpsqs_socket));
        $header = $line;
        list($proto, $rcode, $result) = explode(" ", $line);
        $len = -1;
        while (($line = trim(fgets($httpsqs_socket))) != "")
        {
            $header .= $line;
            if (strstr($line, "Content-Length:"))
            {
                list($cl, $len) = explode(" ", $line);
            }
            if (strstr($line, "Pos:"))
            {
                list($pos_key, $pos_value) = explode(" ", $line);
            }
        }
        if ($len < 0)
        {
            return false;
        }
        $body = @fread($httpsqs_socket, $len);
        fclose($httpsqs_socket);
                $result_array["pos"] = (int)$pos_value;
                $result_array["data"] = $body;
        return $result_array;
    }

    
    
    function get($host, $port, $charset='utf-8', $name)
    {
        $result = $this->http_get($host, $port, "/?charset=".$charset."&name=".$name."&opt=get");
                if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false) {
                        return false;
                }
        return $result["data"];
    }
	
	
        
   
	
    function http_post($host, $port, $query, $body)
    {
        $httpsqs_socket = @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$httpsqs_socket)
        {
            return false;
        }
        $out = "POST ${query} HTTP/1.1\r\n";
        $out .= "Host: ${host}\r\n";
        $out .= "Content-Length: " . strlen($body) . "\r\n";
        $out .= "Connection: close\r\n";
        $out .= "\r\n";
        $out .= $body;
        fwrite($httpsqs_socket, $out);
        $line = trim(fgets($httpsqs_socket));
        $header = $line;
        list($proto, $rcode, $result) = explode(" ", $line);
        $len = -1;
        while (($line = trim(fgets($httpsqs_socket))) != "")
        {
            $header .= $line;
            if (strstr($line, "Content-Length:"))
            {
                list($cl, $len) = explode(" ", $line);
            }
            if (strstr($line, "Pos:"))
            {
                list($pos_key, $pos_value) = explode(" ", $line);
            }
        }
        if ($len < 0)
        {
            return false;
        }
        $body = @fread($httpsqs_socket, $len);
        fclose($httpsqs_socket);
                $result_array["pos"] = (int)$pos_value;
                $result_array["data"] = $body;
        return $result_array;
    }
    
	function put($host, $port, $charset='utf-8', $name, $data)
    {
        $result = $this->http_post($host, $port, "/?charset=".$charset."&name=".$name."&opt=put", $data);
                if ($result["data"] == "HTTPSQS_PUT_OK") {
                        return true;
                } else if ($result["data"] == "HTTPSQS_PUT_END") {
                        return $result["data"];
                }
                return false;
    }
    
	/**
	 * 数据更新失败记录日志，并标识操作失败
	 * 
	 * 此类中只需要一个日志记录的地方 ，APP在此引用
	 *
	 * @param Array $data
	 * @return false
	 */
	private function _log($data)
	{
	    $log = $this->app->log(); 
	    $log->reset()->setPath("LogSqs")->setData($data)->write();	
	    return false;
	}
	

}


?>
