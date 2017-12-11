<?php 
/**
 * 文章类
 * 
 */
require_once('./common.inc.php');

class article extends Action {
	
	/**
	 * 默认执行的方法消息中心
	 */
	public function doDefault(){	
		$user_id = $_SESSION['user_id'];
		//获取
		importModule("MessageInfo","class");
		$obj_message = new MessageInfo;
		$list = $obj_message->get_message_list($user_id);
		//print_r($list);die;
		$page = $this->app->page();
		$page->value('user',$_SESSION);
		$page->value('list', $list);
		$page->params['template'] = 'mymessage.php';
		$page->output();
	}
	
	
	public function doArticleDetail()
	{
		$page = $this->app->page();
		$page->value('user',$_SESSION);
		$page->value('list', $list);
		$page->params['template'] = 'article.php';
		$page->output();
	}

	
}
$app->run();
	
?>
