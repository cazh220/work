<?php 
/**
 * 报价
 */
require_once('./common.inc.php');

class offer extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		//判断切换是role_id有值
		
		$this->role_id = !empty($_GET['role_id']) ? intval($_GET['role_id']) : 0;
		if(!empty($this->role_id))
		{
			if($this->role_id == -1)
			{
				//管理员报价
				$_SESSION['offer_role'] = -1;
			}
			else
			{
				$_SESSION['offer_role'] = $this->role_id;
			}
			
		}
		
		//获取分类列表
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		
		$category_list = $obj_category->get_categoty_list();
		
		
		//导入工具类
		import('util.CategoryShow');
		$category_show = CategoryShow::category_show($category_list);

		$page = $this->app->page();
		$page->value('category',$category_show);
		$page->params['template'] = 'offer_menu.html';
		$page->output();
	}
	
	//商品列表
	public function doOfferList()
	{
		$role_id 		= $_SESSION['role_id'];
		$user_id		= !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$category_id 	= !empty($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : 0;
		$current_page 	= !empty($_REQUEST['pageNum']) ? intval($_REQUEST['pageNum']) : 1;
		$page_size		= !empty($_REQUEST['numPerPage']) ? intval($_REQUEST['numPerPage']) : 10;
		$goods_name		= !empty($_REQUEST['goods_name']) ? trim($_REQUEST['goods_name']) : '';
		
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		$param = array(
			'category_id'	=> $category_id,
			'page'			=> $current_page,
			'page_size'		=> $page_size,
			'goods_name'	=> $goods_name
		);	
		
		$list = $obj_good->get_offer_list($param);
		//print_r($list);die;
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		
		$category_list = $obj_category->get_categoty_list();
		
		//导入工具类
		import('util.CategoryShow');
		$category_show = CategoryShow::category_show($category_list);
		//print_r($list);die;
		//获取总数
//		$count = $list['count'];
//		$page_num = ceil($count/$page_size);
//		
//		$page_info = array(
//			'total'		=> $count,
//			'page_num'	=> $page_num,
//			'page_size'	=> $page_size,
//			'current_page'=>$current_page
//		);
		//用户报价输出
		foreach($list['list'] as $key => $val)
		{
			$role_id = !empty($_SESSION['offer_role']) ? $_SESSION['offer_role'] : $role_id;
			//获取商品报价
			importModule("OfferInfo","class");
			$obj_offer = new OfferInfo;
			$role_price = $obj_offer->get_good_offer_price($val['goods_id'], $user_id, $role_id);
			//$role_price = $obj_good->get_role_good_price($role_id, $val['goods_id']);
			//var_dump($role_price);die;
			if(empty($role_price) || $role_id == -1 )
			{
				//取基本价
				$role_price = $obj_good->get_good_price($val['goods_id']);
			}
			
			$list['list'][$key]['price'] = number_format($role_price, 2, ".", "");//sprintf("%01.2f",$role_price);
		}
		
		//print_r($page_info);die;
		$page = $this->app->page();
		$page->value('goods',$list['list']);
		$page->value('category',$category_show);
		$page->value('goods_name',$goods_name);
		$page->value('category_id',$category_id);
		$page->params['template'] = 'offer_list.html';
		$page->output();
	}
	
	//更新具体客户的商品定价
	public function doUpdateUserGoodPrice()
	{
		$goods_id		= !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
		$user_id		= !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$role_id		= !empty($_REQUEST['role_id']) ? intval($_REQUEST['role_id']) : 0;
		$price			= !empty($_REQUEST['price']) ? floatval($_REQUEST['price']) : 0;
		$start_time		= !empty($_REQUEST['start_time']) ? trim($_REQUEST['start_time']) : '';
		$end_time		= !empty($_REQUEST['end_time']) ? trim($_REQUEST['end_time']) : '';
		$operator_id	= !empty($_REQUEST['operator_id']) ? trim($_REQUEST['operator_id']) : $_SESSION['user_id'];
		$operator		= !empty($_REQUEST['operator']) ? trim($_REQUEST['operator']) : $_SESSION['username'];
		
		//价格格式化
		$price			= number_format($price, 2, '.', '');
		
		$offer = array(
			'goods_id'		=> $goods_id,
			'price'			=> $price,
			'user_id'		=> $user_id,
			'role_id'		=> $role_id,
			'start_time'	=> $start_time,
			'end_time'		=> $end_time,
			'operator_id'	=> $operator_id,
			'operator'		=> $operator,
		);
		importModule("OfferInfo","class");
		$obj_offer = new OfferInfo;
		$res = $obj_offer->update_user_good_price($offer);
		
		if($res)
		{
			$return = array('status'=>1, 'msg'=>'更新用户报价成功');
		}
		else
		{
			$return = array('status'=>0, 'msg'=>'更新用户报价失败');
		}
		
		exit(json_encode($return));
		
	}
	
	//更新客户分类的商品定价
	public function doUpdateRoleGoodPrice()
	{
		$goods_id		= !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
		$role_id		= !empty($_REQUEST['role_id']) ? intval($_REQUEST['role_id']) : 0;
		$price			= !empty($_REQUEST['price']) ? floatval($_REQUEST['price']) : 0;
		$start_time		= !empty($_REQUEST['start_time']) ? trim($_REQUEST['start_time']) : '';
		$end_time		= !empty($_REQUEST['end_time']) ? trim($_REQUEST['end_time']) : '';
		$operator_id	= !empty($_REQUEST['operator_id']) ? trim($_REQUEST['operator_id']) : $_SESSION['user_id'];
		$operator		= !empty($_REQUEST['operator']) ? trim($_REQUEST['operator']) : $_SESSION['username'];
		
		$offer = array(
			'goods_id'		=> $goods_id,
			'price'			=> $price,
			'user_id'		=> 0,
			'role_id'		=> $role_id,
			'start_time'	=> $start_time,
			'end_time'		=> $end_time,
			'operator_id'	=> $operator_id,
			'operator'		=> $operator,
		);
		importModule("OfferInfo","class");
		$obj_offer = new OfferInfo;
		$res = $obj_offer->update_role_good_price($offer);
		
		if($res)
		{
			$return = array('status'=>1, 'msg'=>'更新用户分类报价成功');
		}
		else
		{
			$return = array('status'=>0, 'msg'=>'更新用户分类报价失败');
		}
		
		exit(json_encode($return));
	}
		

}
$app->run();
	
?>
