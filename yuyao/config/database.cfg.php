<?php
/**
 * 配置文件－－主配置文件
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
?>
