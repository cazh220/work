<?php
/**
 * 配置文件－－主配置文件
 * @package main
 * @subpackage configure
 * @author  崇<lonce@live.cn>
 * @version 0.1 20090707
 *
 * $Id$
 */

//调试开关
define('DEBUG', true);

//是否需要进行SESSION处理
define('SESSION', true);

//设置时区
date_default_timezone_set('Asia/Shanghai');

//在线活动时间长度,单位：秒
define('ONLINE_TIME_GAP', 600);

//统一分页长度
define('PAGE_NAV_SIZE', 6);

define('SOCKET_REQUEST_URL','http://stock.test.com');

//域名
if($_SERVER['HTTP_HOST'] == 'work.com')
{
	$host = 'http://work.com/wx';//本地开发
}
else
{
	$host = 'http://www.yrsyc.cn/wx';
}
define('HOST', $host);
define('UPLOAD_HOST', "http://www.yrsyc.cn/app");

//初始化配置变量
$cfg = array();

$cfg['path']['conf'] = dirname(__FILE__) . '/';
$cfg['path']['root'] = dirname($cfg['path']['conf']) . '/';

//统一商品接口配置信息
$cfg['url']['socket_goods'] = 'http://stock.test.com/api/goods.php';
$cfg['url']['socket_goods_w'] = 'http://stock.test.com/api/goods_w.php';
$cfg['url']['socket_stock'] = 'http://stock.test.com/api/stock.php';
$cfg['api']['id'] = 1;
$cfg['api']['key'] = '123456';

//首页
$cfg['url']['root'] = 'http://union.test.com/';
$cfg['url']['p_images'] = 'http://image.lyceem.com/';

//avatar的URL数组

$cfg['site']['title'] = '蓝橙,让旅途更舒适!';

$cfg['site']['name'] = '蓝橙,让旅途更舒适!';

$cfg['site']['close_comment'] = '系统升级...请稍候...';

$cfg['site']['icp_number'] = '沪ICP备08020902号';

$cfg['site']['service_email'] = 'service@lyceem.com';

$cfg['site']['service_phone'] = '400-888-2301';

$cfg['site']['shop_address'] = 'lyceem shopping Ltd.,co';

$cfg['site']['shop_desc'] = '蓝橙-中国消费者在选择旅游休闲用品时的首选品牌：主营各类男士旅游休闲服装、旅行装备、旅行配件等。蓝橙呼吸性面料国际一线品质，精细做工，安全环保，给予旅行者一路健康的旅行户外享受！';

$cfg['site']['shop_keywords'] = 'Lyceem, breathable, 蓝橙,蓝橙服饰，蓝橙设计，旅行服装，户外服装，旅行配件，旅行裤子，外套，旅游鞋，专业户外用品，快干系列，旅游休闲，透气系列，控温服装，舒适内衣，Polo 衫，圆领，圆领T恤，沙滩裤，旅行装备，休闲用品，太阳镜，袜子，旅行箱包，防水，防水外套，衬衫,牛津纺,牛津纺衬衫,衬衣,长袖衬衫,短袖衬衫,全棉,纯棉,百分百棉,100%棉,全棉衬衫,纯棉衬衫,全棉衬衣,纯棉衬衣,免烫,免熨,免烫衬衫,免熨衬衫,免烫衬衣,免熨衬衣,牛津纺衬衣,领尖扣,直领,小方领,意式领,T恤,POLO,棉线衫,卫衣,外套,休闲西服,毛衣,背心,毛背心,裤子,休闲裤,牛仔裤,直筒休闲裤,免烫休闲裤,纹休闲裤,短裤,沙滩裤,内衣,内裤,三角裤,平角裤,棉袜,断码,打折在线购物,在线购物网,在线购物网站,网络购物';

$cfg['site']['shop_title'] = 'LYCEEM 蓝橙 - 我身边的平价旅游用品专家';


//加载数据库配置文件
include($cfg['path']['conf'] .'database.cfg.php');

//默认存储数量
$cfg['default_storage'] = 1;

//页面信息
$cfg['page'] = array(
'charset'			=> 'UTF-8',
'contentType'		=> 'text/html',
'title'				=> '',
'cached'			=> false,
'engine'			=> 'smarty',
'css'				=> array(),
'js'				=> array(),
);


//风格
$cfg['theme'] = array(
'root'				=> '',
'current'			=> '',
);


//其他路径
$cfg['path'] = array_merge($cfg['path'], array(
'lib'				=> $cfg['path']['root'] . 'lib/',
'class'				=> $cfg['path']['root'] . 'lib/',
'common'			=> $cfg['path']['root'] . 'lib/',
'cache'				=> $cfg['path']['root'] . 'cache/',
'upload'			=> $cfg['path']['root'] . 'public/upload/',
'fonts'				=> $cfg['path']['root'] . 'public/fonts/',
'temp'				=> $cfg['path']['root'] . 'public/temp/',
'module'			=> $cfg['path']['root'] . 'modules/',
));


//URL设置
$cfg['url'] = array_merge($cfg['url'], array(
'js'				=> '/public/js/',
'css'				=> '/public/css/',
'images'			=> '/public/images/',
'theme'			    => '/public/theme/',
));


//Smarty
$cfg['smarty'] = array(
'template_dir'		=> $cfg['path']['root'] . $cfg['theme']['current'] . 'templates/',
'compile_dir'		=> $cfg['path']['cache'] . 'smarty/',
'caching'			=> false,
'left_delimiter'	=> '{',
'right_delimiter'	=> '}',
);


//cache
$cfg['cache'] = array(
'root'			=> $cfg['path']['cache'],  // engine=memcached 时为服务器地址
'engine'			=> '', //file|memcached
'port'			=> 11211, //engine=memcached 时才有意义
'timeout'			=> 60, //engine=memcached 时才有意义
);

//货号前缀
$cfg['sn_prefix'] = array(
'M'  => 'M',
'P'  => 'P',);

//成本设置
$cfg['cost'] = array(
'standard'  => 'ON',
);

// 在线时间配置
$cfg['online'] = array(
'min' => 60,	// 在线时长,最短计算时间,单位秒
'max' => 300    // 在线时长,最长计算时间,单位秒
);

//邮件配置
$cfg['mail'] = array(
'host' => 'mail.lyceem.com',    	// 邮件服务主机域名或ip
'port' => 25,               	// 邮件服务器端口号
'user' => 'chenglin.bao@lyceem.com',   // 邮件服务器帐号
'pass' => '870926',          // 邮件服务器密码
'from' => 'chenglin.bao@lyceem.com'    // 发件人邮箱
);

//图片相关
$cfg['auto_generate_gallery']  = 1;
$cfg['image_width']            = 230;
$cfg['image_height']           = 230;
$cfg['watermark_place']        = 0;
$cfg['watermark']              = '';
$cfg['watermark_alpha']        = 65;
$cfg['thumb_width']            = 100;
$cfg['thumb_height']           = 100;



// Rsa的公私钥
$cfg['rsa']['pubkey'] =
'-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDHbnmlsI7lgWOBhONJeBATmyV2
+oXG7VIKMsDWEN13VkWYHqrZnzqWePnTtakv0ckmYTM51Fb+K0W+uZsxBKwynIBj
vhZVCIP9/8LOIAIdmiGQFtIRd8SLR/jS5AOx0H2iqFhOSYIP596BmxNYR43BJ/Jg
PMuXaxPEHYheuzxKuQIDAQAB
-----END PUBLIC KEY-----
';
$cfg['rsa']['prikey'] =
'-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDHbnmlsI7lgWOBhONJeBATmyV2+oXG7VIKMsDWEN13VkWYHqrZ
nzqWePnTtakv0ckmYTM51Fb+K0W+uZsxBKwynIBjvhZVCIP9/8LOIAIdmiGQFtIR
d8SLR/jS5AOx0H2iqFhOSYIP596BmxNYR43BJ/JgPMuXaxPEHYheuzxKuQIDAQAB
AoGBALSy18wWFtPCkduIAbzO6ZoqKB8OzWm6HGybIfiUHWaEp9g2aU13pckzYgG+
hsaKSbzZs2WBjTUNFkvCtugKOM6idFCQe2zUBlahWxAIZD+cUEcyzh82pshwJgxf
8mI3nAQoCsb09Mhq86G6wo8zf0d7RsdsiOPcFXSc1g8DGswFAkEA/qaRL2IGNDL3
v1SS/M97C1+KeNiowUhyu+YgXOgNyMeACkHTrmw7nmE9NMn/vwbT85Ws7xdBQykE
xXp4XFm8RwJBAMh9AOT80wdUXNwMBGWeTS1mWzBI0fmEy56R7yfH8G6m5Xa1yeJy
4R0mJQjH52eDulFWEAJONUrNKDT68cb4QP8CQQCior8XBAPyUproF5vI2ro7CUnm
5Hji+OJOHyuMKqijEsczxdbsDzQEcxYkIN61oia761wHV1LXEdt6RD2avbUBAkBo
yRTHmfB92zTxeYJuzi8ONHoioVzFage2aBW0GAbs/mPeCKNsrJhF0OL4VOr4Klwe
GLojSlcGMnX6QtJNKQFnAkBh8buEQtaizkoyE7zsoyirxBWuoI/D25mcwrAVz/yd
Na7A+5tb5SnmQtNyNFn9ebU5L/4/d7NTz6XHmcJTbnSX
-----END RSA PRIVATE KEY-----
';
?>
