<?php 
/**
 * app商品分类
 */
require_once('./../common.inc.php');

class min_category extends Action {
	
	/**
	 * 默认执行的方法
	 */
	public function doDefault(){	
		//获取分类
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		$list = array();
		$category_list = $obj_category->get_deep_category(2);
		//获取所有分类商品		
		if($category_list)
		{
			foreach($category_list as $key => $val)
			{  
				$cat_arr = array();
				$list[$key] = $val;
				array_push($cat_arr, "'".$val['cid']."'");
				$sub_category = $obj_category->get_sub_category($val['cid']);
				if($sub_category)
				{
					foreach($sub_category as $k => $v)
					{
						array_push($cat_arr, "'".$v['cid']."'");
					}
				}
				unset($sub_category);
				//获取商品信息
				//var_dump($cat_arr);
				$cids = $cat_arr ? implode(',', $cat_arr) : '';
				importModule("GoodsInfo","class");
				$obj_good = new GoodsInfo;
				$goods = array();
				$goods = $obj_good->get_cat_goods($cids);
				$list[$key]['children'] = $goods;
				unset($cat_arr,$goods);
			}
		}
		
		exit(json_encode($list));
	}
	

}
$app->run();
	
?>
