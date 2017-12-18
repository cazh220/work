<?php 
/**
 * 分类模块
 */
require_once('./common.inc.php');

class category extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){
		//获取分类列表
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		
		$category_list = $obj_category->get_categoty_list();
		
		//导入工具类
		import('util.CategoryShow');
		$category_show = CategoryShow::category_show($category_list);
	
		//$sess_id = $_SESSION['sess_id'];
		if(isset($_COOKIE['last_sess_id']))
		{
			$sess_id = $_COOKIE['last_sess_id'];
		}
		else
		{
			$sess_id = $_SESSION['sess_id'];
		}
		//print_r($category_show);die;
		$page = $this->app->page();
		$page->value('category',$category_show);
		$page->value('main','category_main');
		$page->value('user',$_SESSION[$sess_id]);
		$page->params['template'] = 'index_menu.html';
		$page->output();
	}
	
	//分类
	public function doCategory(){	
		//获取分类列表
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		
		$category_list = $obj_category->get_categoty_list(0);
		
		//私有分类
		$my_category_list = $obj_category->get_categoty_list(1);
		
		//导入工具类
		import('util.CategoryShow');
		$category_show = CategoryShow::category_show($category_list);
		$my_category_show = CategoryShow::category_show($my_category_list);
		//print_r($category_show);
		$page = $this->app->page();
		$page->value('category',$category_show);
		$page->value('mycategory',$my_category_show);
		$page->params['template'] = 'category_menu.html';
		$page->output();
	}
	
	//分类细目展示
	public function doShowCategory()
	{
		
	}
	
	//添加节点
	public function doAddCategory()
	{
		$last_cat_id = $_GET['last_cat_id'];
		$current_cat_id = $_GET['current_cat_id'];
		
		//获取上级分类名称
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		$last_detail = $obj_category->get_category_info($last_cat_id);
		//获取当前分类信息
		$current_detail = $obj_category->get_category_info($current_cat_id);
		//print_r($current_detail);die;
		$page = $this->app->page();
		$page->value('last_category',$last_detail);
		$page->value('current_category',$current_detail);
		$page->params['template'] = 'add_category.html';
		$page->output();
	}
	
	//更新分类
	public function doCategoryAct()
	{	
		$type 			= intval($_POST['type']);
		$cname			= $_POST['category_name'] ? trim($_POST['category_name']) : '';
		$url			= '';//$_POST['category_link'] ? trim($_POST['category_link']) : '';
		$description 	= $_POST['category_desc'] ? trim($_POST['category_desc']) : '';
		$ctype			= 0;//$_POST['ctype'] ? intval($_POST['ctype']) : 0;//供应商
		$parent_id		= $_POST['last_category_id'] ? intval($_POST['last_category_id']) : 0;
		$deepth			= $_POST['deepth'] ? intval($_POST['deepth']) : 0;
		$cid			= $_POST['cid'] ? intval($_POST['cid']) : 0;
		
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		if($type == 1)
		{
			//添加同类
			$deepth = $deepth + 1;
			$param = array(
				'cname'			=> $cname,
				'url'			=> $url,
				'description'	=> $description,
				'ctype'			=> $ctype,
				'parent_id'		=> $parent_id,
				'deepth'		=> $deepth
			);
			
			$res = $obj_category->add_new_category($param);
			
			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '添加成功',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/category.php',
					'confirmMsg'	=> ''
				);
			}
			else
			{
				//失败
				$return = array(
					'statusCode'	=> 0,
					'message'		=> '添加失败',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/category.php',
					'confirmMsg'	=> ''
				);
			}
		}
		elseif($type == 2)
		{
			//添加子类
			$deepth = $deepth + 2;
			$param = array(
				'cname'			=> $cname,
				'url'			=> $url,
				'description'	=> $description,
				'ctype'			=> $ctype,
				'parent_id'		=> $cid,
				'deepth'		=> $deepth
			);
			$res = $obj_category->add_new_category($param);
			
			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '添加成功',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/category.php',
					'confirmMsg'	=> ''
				);
			}
			else
			{
				//失败
				$return = array(
					'statusCode'	=> 0,
					'message'		=> '添加失败',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/category.php',
					'confirmMsg'	=> ''
				);
			}
		}
		elseif($type == 3)
		{
			//编辑
			$param = array(
				'cname'			=> $cname,
				'url'			=> $url,
				'description'	=> $description,
				'ctype'			=> $ctype
			);
			
			$res = $obj_category->edit_category($param, $cid);

			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '编辑成功',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/category.php',
					'confirmMsg'	=> ''
				);
			}
			else
			{
				//失败
				$return = array(
					'statusCode'	=> 0,
					'message'		=> '编辑失败',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/category.php',
					'confirmMsg'	=> ''
				);
			}
		}
		else
		{
			//删除
			$param = array(
				'cid'	=> $cid
			);
			//判断分类下是否有产品，有则不能删
			importModule("GoodsInfo","class");
			$obj_good = new GoodsInfo;	
			$goods = $obj_good->get_goods_list(array('category_id'=>$cid));
			
			if(!empty($goods['list']))
			{
				//echo "<script>alert('该分类下有产品，禁止删除');</script>";
				//失败
				$return = array(
					'statusCode'	=> 300,
					'message'		=> '该分类下有产品，禁止删除',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/category.php',
					'confirmMsg'	=> ''
				);
				exit(json_encode($return));
			}
			
			$res = $obj_category->delete_category($param);
			
			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '删除成功',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/category.php',
					'confirmMsg'	=> ''
				);
			}
			else
			{
				//失败
				$return = array(
					'statusCode'	=> 0,
					'message'		=> '删除失败',
					'navTabId'		=> 'catgory_show',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/category.php',
					'confirmMsg'	=> ''
				);
			}
		}
		exit(json_encode($return));
	}

}
$app->run();
	
?>
