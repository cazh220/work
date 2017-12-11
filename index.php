<?php
session_start();
define("TOKEN", "myweixin");

$checkObj = new Index();
if (isset($_GET['echostr']))
{
    $checkObj->valid();
}
else 
{
    $checkObj->responseMsg();
}


class Index
{
    public function valid()
    {
        $echoStr = $_GET['echostr'];
        if ($this->checkSignature()) {
            echo $echoStr;
            exit();
        }
    }
    
    private function checkSignature() {
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce     = $_GET['nonce'];
        
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        
        if ($tmpStr == $signature) {
            return true;
        }else{
            return false;
        }
    }
    
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        
        if (!empty($postStr)) {
            $this->logger("S \n".$postStr);
            
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			
            $RX_TYPE = trim($postObj->MsgType);
            switch($RX_TYPE)
            {
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
					$result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj); 
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    break;
                case "link":
                    break;
                case "event":
                	$result = $this->DealEvent($postObj);
                default:
                    $result = "unknown msg type: ".$RX_TYPE;
            }
            
            $this->logger("E \n".$result);
            
            echo $result;
        }
        else
        {
            echo "";
            exit();
        }
    }
    
    //事件处理
    public function DealEvent($object)
    {
    	$key = $object->Event;
    	
    	switch($key)
    	{
    		case 'scancode_push':
    			$type = $object->ScanCodeInfo->ScanType;
	    		$result = $object->ScanCodeInfo->ScanResult;
	    		$open_id = $object->FromUserName;
	    		
	    		$_SESSION['open_id'] = $open_id;
	    		//$this->logger("E res：\n".$type.':'.$result.':'.$open_id);
	    		if($type=='qrcode')
	    		{
	    			$this->writeOpenid($result, $open_id);
	    			//header("Location:index.php?do=share&security_code=".$result);
	    			exit();
	    		}
	    		
	    		break;
	    	default:
	    		$result = $object->ScanResult;
    	}
    	
    }
    
    public function writeOpenid($security_code, $open_id)
    {
    	//import('util.Jssdk');
    	require "wx/lib/util/Jssdk.class.php";
		$jssdk = new JSSDK();
		$access_token = $jssdk->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$open_id}";
		$data = $this->curl_get($url);
		$user = json_decode($data, true);
		//$this->logger("C \n".$data);
    	//print_r($data);die;
    	$con = mysql_connect("udm3289897.my3w.com", "udm3289897", "0610640326");
		if (!$con)
		{
			die('Could not connect: ' . mysql_error());
		}
		//解析URL
		$arr = parse_url($security_code);
		$arr_query = $this->convertUrlQuery($arr['query']);
		$this->logger("D Arr\n".json_encode($arr_query));
		mysql_select_db("udm3289897_db", $con);
		$this->logger("G SESSION\n".json_encode(json_encode($_SESSION)));
		mysql_query("INSERT INTO hg_share (open_id, security_code, headimgurl, create_time) 
		VALUES ('".$user['openid']."', '".$arr_query['security_code']."', '".$user['headimgurl']."', NOW())");
		
		mysql_close($con);
    }
    
    private function convertUrlQuery($query)
	{
	  $queryParts = explode('&', $query);
	  $params = array();
	  foreach ($queryParts as $param) {
	    $item = explode('=', $param);
	    $params[$item[0]] = $item[1];
	  }
	  return $params;
	}
    
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
    
    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        if (strstr($keyword, "文本")) {
            $content = "简单的文本消息";
        }else if (strstr($keyword, "单图文")) {
            $content[0] = array(
                    'Title'=>'第一条新闻',
                    'Description'=>'这是条重要消息',
                    'PicUrl'=>'https://cdn.bdstatic.com/portal/img/logo/logo.2x_5d8bc201.png',
                    'Url'=>'http://blog.csdn.net/xuewufeifang/article/details/49756099' 
                );
        }else if (strstr($keyword, "多图文")) {
            $content[0] = array(
                    'Title'=>'第二条新闻',
                    'Description'=>'这是条重要消息',
                    'PicUrl'=>'https://cdn.bdstatic.com/portal/img/logo/logo.2x_5d8bc201.png',
                    'Url'=>'http://blog.csdn.net/xuewufeifang/article/details/49756099' 
                );
            $content[1] = array(
                    'Title'=>'第三条新闻',
                    'Description'=>'这是条重要消息',
                    'PicUrl'=>'https://cdn.bdstatic.com/portal/img/logo/logo.2x_5d8bc201.png',
                    'Url'=>'http://blog.csdn.net/xuewufeifang/article/details/49756099' 
                );
            $content[2] = array(
                    'Title'=>'第四条新闻',
                    'Description'=>'这是条重要消息',
                    'PicUrl'=>'https://cdn.bdstatic.com/portal/img/logo/logo.2x_5d8bc201.png',
                    'Url'=>'http://blog.csdn.net/xuewufeifang/article/details/49756099' 
                );
            $content[3] = array(
                    'Title'=>'第五条新闻',
                    'Description'=>'这是条重要消息',
                    'PicUrl'=>'https://cdn.bdstatic.com/portal/img/logo/logo.2x_5d8bc201.png',
                    'Url'=>'http://blog.csdn.net/xuewufeifang/article/details/49756099' 
                );
        }else{
            $content  = date("Y-m-d H:i:s", time()."\n 没有要返回的");
        }
        
        if (is_array($content)) {
            if (isset($content[0]['PicUrl'])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
                $result = $this->transmitText($object, $content);
        }
        
        return $result;
    }

    public function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    public function receiveVoice($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitVoice($object, $content);
        return $result;
    }

    public function receiveLocation($object)
    {
        $result = $this->transmitLocation($object);
        return $result;
    }

    public function transmitLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }
    
    private function transmitText($object, $content)
    {   
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    private function transmitImage($object, $imageArray)
    {   
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    public function transmitVoice($object, $content)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
<Voice>
<MediaId><![CDATA[%s]]></MediaId>
</Voice>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content['MediaId']);
        return $result;
    }

    //图文
    public function transmitNews($object, $content)
    {
        $itemTpl = '<item>
<Title><![CDATA[%s]]></Title> 
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>';
        foreach ($content as $key => $value) {
            $item_str .= sprintf($itemTpl, $value['Title'], $value['Description'], $value['PicUrl'],$value['Url']);
        }
        
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>{$item_str}
</Articles>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($content));
        return $result;
    }
    
    
    //log
    private function logger($log_content)
    {
        /*
        $max_size = 100000;
        $log_filename = "log.xml";
        
        if (file_exists($log_filename) && (abs(filesize($log_filename)) > $max_size)) {
            unlink($log_filename);
        }
        */
        $log_filename = "log.xml";
        
        file_put_contents($log_filename, date("Y-m-d H:i:s", time())." ".$log_content."\n", FILE_APPEND);
    }
}