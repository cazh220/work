<?php

/**
 * 验证类（EMAIL）
 *
 * @package lib
 * @subpackage util
 * @author  崇<lonce@live.cn>
 */
class Check {

	/**
	 * 检查mail是否有效
	 *
	 * @param string $email
	 * @access public
	 * @return boolean
	 */
	static public function isemail($email) {
		return strlen($email) > 6 && preg_match("/^([\w{1,}])([\w-]*(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/", $email);
	}

	/**
	 * 检查QQ是否符合标准
	 *
	 * @param int $qq
	 * @return boolean
	 */
	static public function isqq($qq)
	{
		return ereg("[1-9][0-9]{4,}",$qq);
	}
	
	/**
	 * 检查是否符合手机标准
	 *
	 * @param int $mobile
	 * @return boolean
	 */
	static public function ismobile($mobile)
	{
		return ereg("[1-9][0-9]{10,}",$mobile);
	}
	
	/**
	 * 验证是否为数字
	 *
	 * @param int $num
	 * @return boolean
	 */
	static public function isnum($num)
	{
		return is_numeric($num);
	}
	/**
	 * 验证是否合格为DOMAIN
	 *
	 * @param string $domain
	 * @return boolean
	 */
	static public function isdomain($domain)
	{
		return ereg("^[a-zA-Z0-9]{3,20}$",$domain);
	}
}

?>