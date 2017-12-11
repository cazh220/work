<?php

/**
 * 应用初始化程序
 * 
 * @package core
 * @subpackage common
 * @author  崇<lonce@live.cn>
 * 
 * $Id$
 */

require_once(dirname(__FILE__) . '/' . 'config/application.cfg.php');
require_once($cfg['path']['lib'] . 'base.inc.php');

$cfg['path']['current'] = dirname($_SERVER['SCRIPT_FILENAME']) . '/';

header('Content-type: ' . $cfg['page']['contentType'] . '; charset=' . $cfg['page']['charset']);

if (DEBUG)
	import('core.FirePHP');

if (SESSION)
	session_start();
	
// 初始化application
$app = new Application();

?>