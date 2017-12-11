<?php

/**
 *  邮件 类
 *
 * @package lib
 * @subpackage util
 * @author  鲍<chenglin.bao@lyceem.cn>
 */
class MailUtil {

	/**
	 * @var string $to 收件人
	 * @access private
	 */
	private $to = '';
	
	/**
	 * @var string $subject 邮件主题
	 * @access private
	 */
	private $subject = '';
	
	/**
	 * @var string $content 邮件内容
	 * @access private
	 */
	private $content = '';

	/**
	 * @var string 	$from 收件人
	 * @access private
	 */ 
	private $from = '';
	
	
	/**
	 * 发送邮件
	 * @param $to 收件人
	 * @param $subject 邮件主题
	 * @param $content 邮件内容
	 * @param $from    发件人
	 */ 
	public function sendMail($to='',$subject='',$content='',$from=''){
		if($to == '' || $subject == '' || $content == '')
			return false;
		
		if(!is_string($to) || !is_string($subject) ||!is_string($content) ||!is_string($from))
			return false;
		
		//导入check类，检测email的合法性
		require_once("Check.class.php");
		
		if(Check::isemail($to) == false)
			return false;
			
		if(mail($to,$subject,$content,$from))
			return true;
		
		return false;	
	}
}	
?>