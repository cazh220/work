<?php

import('smarty.Smarty');

/**
 * 应用Smarty的Page类
 *
 * @package lib
 * @subpackage core.page
 * @author  崇<lonce@live.cn>
 */
class PageSmarty extends APageFactory {
	/**
	 * Smarty 对象
	 *
	 * @var object Smarty
	 * @access private
	 */
	var $smarty;
	
	/**
	 * 构造函数
	 *
	 * @param Application $app
	 */
	function __construct(& $app) {
		parent::__construct($app);
		$this->smarty = new Smarty;
		(isset($app->cfg['smarty']['template_dir'])) && $this->smarty->template_dir = $app->cfg['smarty']['template_dir'];
		(isset($app->cfg['smarty']['compile_dir'])) && $this->smarty->compile_dir = $app->cfg['smarty']['compile_dir'];
		(isset($app->cfg['smarty']['config_dir'])) && $this->smarty->config_dir = $app->cfg['smarty']['config_dir'];
		(isset($app->cfg['smarty']['cache_dir'])) && $this->smarty->cache_dir = $app->cfg['smarty']['cache_dir'];
		(isset($app->cfg['smarty']['debugging'])) && $this->smarty->debugging = $app->cfg['smarty']['debugging'];
		(isset($app->cfg['smarty']['caching'])) && $this->smarty->caching = $app->cfg['smarty']['caching'];
		(isset($app->cfg['smarty']['cache_lifetime'])) && $this->smarty->cache_lifetime = $app->cfg['smarty']['cache_lifetime'];
		(isset($app->cfg['smarty']['left_delimiter'])) && $this->smarty->left_delimiter = $app->cfg['smarty']['left_delimiter'];
		(isset($app->cfg['smarty']['right_delimiter'])) && $this->smarty->right_delimiter = $app->cfg['smarty']['right_delimiter'];
		$this->smarty->plugins_dir = SMARTY_DIR . 'plugins/';
		
		$this->smarty->assign_by_ref('cfg', $app->cfg);
		$this->smarty->register_modifier('head', 'head'); 
		$this->smarty->register_modifier('html', '_htmlspecialchars'); 
		
		//设置默认的图片、样式、JS的模版路径
		$this->value('url_js', $app->cfg['url']['js']);
		$this->value('url_images', $app->cfg['url']['images']);
		$this->value('url_p_images', $app->cfg['url']['p_images']);
		$this->value('url_css', $app->cfg['url']['css']);
		$this->value('site_title', $app->cfg['site']['title']);
		$this->value('site_name', $app->cfg['site']['name']);
		$this->value('url_theme', $app->cfg['url']['theme']);
		$this->value('url_application', HOST);
		$this->value('url_upload', UPLOAD_HOST);
		
		$ar_payensure = $ar_shipensure = $ar_lawensure = $ar_cooperation = array();    //支付保障 ， 配送保障 ， 法律保障 ， 合作伙伴
		$cooperation = isset($app->cfg['cooperation']) ? $app->cfg['cooperation'] : array();
	
		if(is_array($cooperation) && count($cooperation) > 0)
		{
			foreach($cooperation as $v)
			{
				if($v['type'] == 1)
				{	
					//支付保障
					$ar_payensure[] = $v;
				}
				elseif($v['type'] == 2)
				{	
					//配送保障
					$ar_shipensure[] = $v;
				}
				elseif($v['type'] == 3)
				{	
					//法律保障
					$ar_lawensure[] = $v;
				}
				else
				{	
					//合作伙伴
					$ar_cooperation[] = $v;
				}
			}
		}
		$this->value('payensure'   , $ar_payensure);
		$this->value('shipensure'  , $ar_shipensure);
		$this->value('lawensure'   , $ar_lawensure);
		$this->value('cooperation' , $ar_cooperation);
		$this->value('marquee'     , count($ar_cooperation));

		$this->value('top_menu'       , isset($app->cfg['top_menu']) ? $app->cfg['top_menu'] : array());
		$this->value('menu'       , isset($app->cfg['menu']) ? $app->cfg['menu'] : array());
		if (isset($this->app->pool['message_cnt_info']) && is_array($this->app->pool['message_cnt_info']))
		{
			$message_cnt = $this->app->pool['message_cnt_info'];
			$this->value('all_message_cnt', array_sum(array_values($message_cnt)));
			
			/* 消息中心的页面 */
			$message_page = 'index';
			
			if ($message_cnt['comment'] > 0)
				$message_page = 'comments';
			else if ($message_cnt['guestbook'] > 0)
				$message_page = 'guestbook';
			else if ($message_cnt['system'] > 0)
				$message_page = 'system';
			
			$this->value('message_page', $message_page);
			
			$this->value('message_cnt_info', '[' . $message_cnt['system'] . ',' . $message_cnt['message'] . ',' . $message_cnt['guestbook'] . ',' . $message_cnt['comment'] . ']');
		}
			
	}
	
	/**
	 * 给页面变量赋值
	 *
	 * @param string $name 变量名,如果参数类型为数组,则为变量赋值,此时$value参数无效
	 * @param mixed $value 变量值,如果该参数未指定,则返回变量值,否则设置变量值
	 * @return APage 如果参数为NULL则返回Page对象本身,否则返回变量值
	 */
	function value($name, $value = NULL) {
		if ($value === NULL && !is_array($name)) { //取值
			return $this->smarty->get_template_vars($name);
		} else { //赋值
			if (is_array($name)) { //如果是数组则批量变量赋值
				foreach ($name as $k => $v) {
					$this->smarty->assign($k, $v);
				}
			} else {
				$this->smarty->assign($name, $value);
			}
			return $this;
		}
	}
	
	/**
	 * 页面内容输出
	 * @param boolean $fetch 是否提取输出结果
	 * @param string 如果$fetch值为true,则返回页面HTML文本内容,否则直接输出
	 */
	function output($fetch=false) {
		if (!isset($this->params['template'])) {
			$offsetPath = substr($this->app->cfg['path']['current'],
								 strlen($this->app->cfg['path']['root']));
			$this->params['template'] = $offsetPath . $this->app->name 
					. $this->app->module . '.tpl';
		}
		if ($fetch) {
			return $this->smarty->fetch($this->params['template']);
		} else {
			$this->smarty->display($this->params['template']);
		}
	}
}
?>