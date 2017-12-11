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

//Memcache分组相关配置信息
$cfg['memcache'] = array(
array('host' => '127.0.0.1','port' => 11211),
); 

//Memcacheq分组队列相关配置信息
$cfg['memcacheq'] = array(
array('host' => '192.168.1.102','port' => 22201)
);

$cfg['memcached'] = array(
array('host' => '127.0.0.1','port' => 11211,'expire' => 86400)
);
//表前缀
//$cfg['prefix'] = 'ly_';
$cfg['prefix'] = 'ecs_';
//库前缀
//$cfg['table'] = '_t_';

//主数据库，默认连接该数据库
//*
$cfg['db'] = array('driver'=> 'mysql', 'host'=> 'localhost', 'name'=> 'work', 'user'=> 'root','password'=> '');
/*
$cfg['union'] = array('driver'=> 'mysql', 'host'=> 'localhost', 'name'=> 'lyceem_distribution', 'user'=> 'root', 'password'=> '');
$cfg['store_db'] = array('driver'=> 'mysql', 'host'=> 'localhost', 'name'=> 'market', 'user'=> 'root','password'=> '');
$cfg['goods_db'] = array('driver'=> 'mysql', 'host'=> 'localhost', 'name'=> 'lyceem_goods', 'user'=> 'root','password'=> '');
*/

//$cfg['db'] = array('driver'=> 'mysql', 'host'=> '114.141.181.165', 'name'=> '_t_lyceem', 'user'=> 'developer','password'=> 'LyceemDev203');
//$cfg['union'] = array('driver'=> 'mysql', 'host'=> '114.141.181.165', 'name'=> '_t_lyceem_distribution', 'user'=> 'developer', 'password'=> 'LyceemDev203');
//$cfg['store_db'] = array('driver'=> 'mysql', 'host'=> '114.141.181.165', 'name'=> '_t_stock', 'user'=> 'developer','password'=> 'LyceemDev203');
//$cfg['goods_db'] = array('driver'=> 'mysql', 'host'=> '114.141.181.165', 'name'=> 'lyceem_goods', 'user'=> 'developer','password'=> 'LyceemDev203');
?>
