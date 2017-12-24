<?php
namespace app\admin\controller;
use think\Controller;
use think\View;
use think\Db;
use think\Session;
use think\Log;


class About extends Controller
{
	//添加内容
	public function insert_content()
	{
		$id 		= !empty($_POST['id']) ? trim($_POST['id']) : '';
		$content 	= !empty($_POST['content']) ? trim($_POST['content']) : '';
		
		$About = model('About');
		
		
	}
	
	//防伪码验证版本说明
	public function get_content()
	{
		$id	= !empty($_GET['id']) ? intval($_GET['id']) : 0;
		
		//获取内容
		importModule("AboutInfo","class");
		$obj_patient = new AboutInfo;
		
		//获取内容
		$list = $obj_patient->getAbout($id);
		
		
		$page = $this->app->page();
		$page->value('list',$list);
		$page->params['template'] = 'fangweima.php';
		$page->output();
	}
	
		
	
}