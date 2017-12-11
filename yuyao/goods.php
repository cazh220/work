<?php 
/**
 * 商品
 */
require_once('./common.inc.php');

class goods extends Action {
	
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

		$page = $this->app->page();
		$page->value('category',$category_show);
		$page->params['template'] = 'goods_menu.html';
		$page->output();
	}
	
	public function doGoodsIndex()
	{
		$category_id 	= !empty($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : 0;
		$current_page 	= !empty($_REQUEST['pageNum']) ? intval($_REQUEST['pageNum']) : 1;
		$page_size		= !empty($_REQUEST['numPerPage']) ? intval($_REQUEST['numPerPage']) : 10;
		$goods_name		= !empty($_REQUEST['goods_name']) ? trim($_REQUEST['goods_name']) : '';
		$order_id		= !empty($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;
		
		
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		importModule("OrderInfo","class");
		$obj_order = new OrderInfo;

		$param = array(
			'category_id'	=> $category_id,
			'page'			=> $current_page,
			'page_size'		=> $page_size,
			'goods_name'	=> $goods_name
		);	
		
		//获取订单商品id
		$ids = array();
		if(!empty($order_id))
		{
			$info = $obj_order->get_order_goods($order_id);
			if($info)
			{
				foreach($info as $key => $val)
				{
					array_push($ids, $val['goods_id']);
				}
			}
			$param['ids'] = $ids;
		}
		
		$list = $obj_good->get_goods_list($param);
		$page_num = ceil($list['count']/$page_size);
		$page_info = array(
			'total'		=> $list['count'],
			'page_num'	=> $page_num,
			'page_size'	=> $page_size,
			'current_page'=>$current_page
		);
		
		//print_r($page_info);die;
		$page = $this->app->page();
		$page->value('goods',$list['list']);
		$page->value('total',$list['count']);
		$page->value('category_id',$category_id);
		$page->value('page',$page_info);
		$page->params['template'] = 'goods_index.html';
		$page->output();
	}
	
	//商品列表
	public function doGoodsList()
	{
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
		$list = $obj_good->get_goods_list($param);
		
//		$page_num = ceil($list['count']/$page_size);
//		$page_info = array(
//			'total'		=> $list['count'],
//			'page_num'	=> $page_num,
//			'page_size'	=> $page_size,
//			'current_page'=>$current_page
//		);
		if($list['list'])
		{
			foreach($list['list'] as $key =>$val)
			{
				$list['list'][$key]['price'] = sprintf("%01.2f",$val['price']);
			}
		}
		
		
		$page = $this->app->page();
		$page->value('goods',$list['list']);
		$page->value('total',$list['count']);
		$page->value('goods_name',$goods_name);
		$page->value('category_id',$category_id);
		$page->value('page',$page_info);
		$page->params['template'] = 'goods_list.html';
		$page->output();
	}
	
	//添加商品
	public function doAddGood()
	{
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		
		$category_list = $obj_category->get_categoty_list();
		
		//导入工具类
		import('util.CategoryShow');
		$category_show = CategoryShow::category_show($category_list);
		
		$page = $this->app->page();
		$page->value('category',$category_show);
		$page->params['template'] = 'add_good.html';
		$page->output();
	}
	
	//添加商品
	public function doAddGoodAct()
	{
		//模拟用户
		$operator_id = $_SESSION['user_id'];
		$operator = $_SESSION['username'];
		$goods_name	= $_POST['goods_name'] ? trim($_POST['goods_name']) : '';
		$goods_code = $_POST['goods_code'] ? trim($_POST['goods_code']) : '';
		$category_id= $_POST['category_id'] ? intval($_POST['category_id']) : 0;
		$unit		= $_POST['unit'] ? trim($_POST['unit']) : '';
		$price 		= $_POST['price'] ? floatval($_POST['price']) : 0;
		$tax 		= $_POST['tax'] ? intval($_POST['tax']) : 0;
		$stock		= $_POST['stock'] ? intval($_POST['stock']) : 0;
		$is_show	= $_POST['is_show'] ? intval($_POST['is_show']) : 0;
		$descrition	= $_POST['description'] ? trim($_POST['description']) : '';
		
		
		$param = array(
			'goods_name'	=> $goods_name,
			'goods_code'	=> $goods_code,
			'category_id'	=> $category_id,
			'unit'			=> $unit,
			'price'			=> $price,
			'tax'			=> $tax,
			'stock'			=> $stock,
			'is_show'		=> $is_show,
			'description'	=> $descrition,
			'operator_id'	=> $operator_id,
			'operator'		=> $operator
		);
		
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		$res = $obj_good->add_new_good($param);
		
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '添加成功',
				'navTabId'		=> 'goods_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		else
		{
			//失败
			$return = array(
				'statusCode'	=> 0,
				'message'		=> '添加失败',
				'navTabId'		=> 'goods_list',
				'rel'			=> '',
				'callbackType'	=> '',
				'forwardUrl'	=> 'closeCurrent',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));

	}
	
	//编辑商品页面
	public function doEditGood()
	{
		$goods_id = !empty($_GET['goods_id']) ? intval($_GET['goods_id']) : 0;
		
		//获取分类
		importModule("CategoryInfo","class");
		$obj_category = new CategoryInfo;
		
		$category_list = $obj_category->get_categoty_list();
		//导入工具类
		import('util.CategoryShow');
		$category_show = CategoryShow::category_show($category_list);
		
		//获取商品
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		$good_detail = $obj_good->get_good_detail($goods_id );
		if($good_detail)
		{
			$good_detail['price'] = sprintf("%01.2f",$good_detail['price']);
		}

		$page = $this->app->page();
		$page->value('category',$category_show);
		$page->value('good',$good_detail);
		$page->params['template'] = 'edit_good.html';
		$page->output();
	}
	
	//编辑商品操作
	public function doEditGoodAct()
	{
		$goods_id 		= !empty($_POST['goods_id']) ? intval($_POST['goods_id']) : 0;
		$goods_name		= !empty($_POST['goods_name']) ? trim($_POST['goods_name']) : '';
		$category_id	= !empty($_POST['category_id']) ? intval($_POST['category_id']) : 0;
		$unit			= !empty($_POST['unit']) ? trim($_POST['unit']) : '';
		$price			= !empty($_POST['price']) ? floatval($_POST['price']) : 0;
		$tax			= !empty($_POST['tax']) ? intval($_POST['tax']) : 0;
		$stock			= !empty($_POST['stock']) ? intval($_POST['stock']) : 0;
		$is_show		= !empty($_POST['is_show']) ? intval($_POST['is_show']) : 0;
		$description	= !empty($_POST['description']) ? trim($_POST['description']) : '';
		
		$param = array(
			'goods_name'	=> $goods_name,
			'category_id'	=> $category_id,
			'unit'			=> $unit,
			'price'			=> $price,
			'tax'			=> $tax,
			'stock'			=> $stock,
			'is_show'		=> $is_show,
			'description'	=> $description
		);		
		
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		$res = $obj_good->update_good($param, $goods_id);
		
		if($res)
		{
			//成功
			$return = array(
				'statusCode'	=> 200,
				'message'		=> '编辑成功',
				'navTabId'		=> 'goods_list',
				'rel'			=> '',
				'callbackType'	=> 'closeCurrent',
				'forwardUrl'	=> '',
				'confirmMsg'	=> ''
			);
		}
		else
		{
			//失败
			$return = array(
				'statusCode'	=> 0,
				'message'		=> '编辑失败',
				'navTabId'		=> 'goods_list',
				'rel'			=> '',
				'callbackType'	=> '',
				'forwardUrl'	=> 'closeCurrent',
				'confirmMsg'	=> ''
			);
		}
		exit(json_encode($return));
	}
	
	//商品报价页
	public function doOfferPrice()
	{
		$goods_id = !empty($_GET['goods_id']) ? intval($_GET['goods_id']) : 0;
		//获取所有users
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$users = $obj_user->get_user_list();
		//print_r($users);
		
		//获取所有用户的商品报价
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		//获取所有客户用户角色
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		
		//获取商品报价
		importModule("OfferInfo","class");
		$obj_offer = new OfferInfo;

		$roles = $obj_role->get_all_roles(array(), 0);
		import('util.RoleShow');
		$role_cate = RoleShow::role_show($roles);
		$leaf_role = RoleShow::get_leaf_role($role_cate);

		$good_info = $obj_good->get_good_detail($goods_id);
		$base_price = !empty($good_info['price']) ? $good_info['price'] : 0;
		$goods_name = !empty($good_info['goods_name']) ? $good_info['goods_name'] : '';
		$offer_price = array();
		//基础报价
		$base = array(
			'goods_id'	=> $goods_id,
			'role_id'	=> 0,//$roles['list'][0]['role_id'],
			'role_name'	=> '管理员',//$roles['list'][0]['role_name'],\
			'username'	=> '管理员',
			'user_id'	=> 0,
			'goods_name'=> $goods_name,
			'price'		=> $base_price ? $base_price : 0.00,
			'start_time'=> '',
			'end_time'	=> '',
			'type'		=> 'base'
		);
		//print_r($users);
		if($users)
		{
			foreach($users['list'] as $key => $val)
			{
				if($val['role_id'] != 1)//1管理员
				{
					//查询用户报价
					//$offer = $obj_good->get_role_price_detail($val['role_id'], $goods_id, $val['user_id']);
					$offer = $obj_offer->get_good_offer_price($goods_id, $val['user_id'], $val['role_id']);
					
					if(!empty($offer))
					{
						$price  = $offer ? $offer : 0;
						$start_time = '';//$offer['start_time'] ? $offer['start_time'] : '';
						$end_time = '';//$offer['end_time'] ? $offer['end_time'] : '';
					}
					else
					{
						//基础报价
						$price  = $base_price ? $base_price : 0;
						$start_time = '';
						$end_time = '';
					}
					
					$offer_price[$key] = array(
						'goods_id'	=> $goods_id,
						'role_id'	=> $val['role_id'],
						'role_name'	=> $val['role_name'],
						'goods_name'=> $goods_name,
						'price'		=> sprintf("%01.2f",$price),
						'start_time'=> $start_time,
						'end_time'	=> $end_time,
						'type'		=> 'client',
						'user_id'	=> $val['user_id'],
						'username'	=> $val['username']
					);
					
				}
			}
		}

		/*
	
		//遍历整合报价
		if($leaf_role)
		{
			foreach($leaf_role as $key => $val)
			{
				if($val['role_id'] != 1)//1管理员
				{
					//查询报价
					$offer = $obj_good->get_role_price_detail($val['role_id'], $goods_id);

					if(!empty($offer))
					{
						$price  = $offer['price'] ? $offer['price'] : 0;
						$start_time = $offer['start_time'] ? $offer['start_time'] : '';
						$end_time = $offer['end_time'] ? $offer['end_time'] : '';
					}
					else
					{
						//基础报价
						$price  = $base_price ? $base_price : 0;
						$start_time = '';
						$end_time = '';
					}

					$offer_price[$key] = array(
						'goods_id'	=> $goods_id,
						'role_id'	=> $val['role_id'],
						'role_name'	=> $val['role_name'],
						'goods_name'=> $goods_name,
						'price'		=> $price,
						'start_time'=> $start_time,
						'end_time'	=> $end_time,
						'type'		=> 'client'
					);
					
				}
			}
		}
		*/
		array_unshift($offer_price, $base);
		$page = $this->app->page();
		$page->value('offer',$offer_price);
		$page->params['template'] = 'offer_price.html';
		$page->output();
		
	}
	
	//分类-商品报价页
	public function doRoleOfferPrice()
	{
		$goods_id = !empty($_GET['goods_id']) ? intval($_GET['goods_id']) : 0;
		//获取所有users
		importModule("UserInfo","class");
		$obj_user = new UserInfo;
		$users = $obj_user->get_user_list();
		//print_r($users);
		
		//获取所有用户的商品报价
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		//获取所有客户用户角色
		importModule("RoleInfo","class");
		$obj_role = new RoleInfo;
		
		//获取商品报价
		importModule("OfferInfo","class");
		$obj_offer = new OfferInfo;

		$roles = $obj_role->get_all_roles(array(), 0);
		import('util.RoleShow');
		$role_cate = RoleShow::role_show($roles);
		$leaf_role = RoleShow::get_leaf_role($role_cate);

		$good_info = $obj_good->get_good_detail($goods_id);
		$base_price = !empty($good_info['price']) ? $good_info['price'] : 0;
		$goods_name = !empty($good_info['goods_name']) ? $good_info['goods_name'] : '';
		$offer_price = array();
		//基础报价
		$base = array(
			'goods_id'	=> $goods_id,
			'role_id'	=> 0,//$roles['list'][0]['role_id'],
			'role_name'	=> '管理员',//$roles['list'][0]['role_name'],\
			'user_name'	=> '管理员',
			'user_id'	=> 0,
			'goods_name'=> $goods_name,
			'price'		=> $base_price ? $base_price : 0,
			'start_time'=> '',
			'end_time'	=> '',
			'type'		=> 'base'
		);

		//遍历整合报价
		if($leaf_role)
		{
			foreach($leaf_role as $key => $val)
			{
				if($val['role_id'] != 1)//1管理员
				{
					//查询报价
					//$offer = $obj_good->get_role_price_detail($val['role_id'], $goods_id);
					$offer = $obj_offer->get_good_offer_price($goods_id, 0, $val['role_id']);

					if(!empty($offer))
					{
						$price  = $offer ? $offer : 0;
						$start_time = '';//$offer['start_time'] ? $offer['start_time'] : '';
						$end_time = '';//$offer['end_time'] ? $offer['end_time'] : '';
					}
					else
					{
						//基础报价
						$price  = $base_price ? $base_price : 0;
						$start_time = '';
						$end_time = '';
					}

					$offer_price[$key] = array(
						'goods_id'	=> $goods_id,
						'role_id'	=> $val['role_id'],
						'role_name'	=> $val['role_name'],
						'goods_name'=> $goods_name,
						'price'		=> sprintf("%01.2f",$price),
						'start_time'=> $start_time,
						'end_time'	=> $end_time,
						'type'		=> 'client'
					);
					
				}
			}
		}

		array_unshift($offer_price, $base);
		
		if ($offer_price)
		{
			foreach($offer_price as $kk => $vv)
			{
				//获取父级
				$parent_id = $obj_role->get_parent_role($vv['role_id']);
				if($parent_id)
				{
					$parent_role_info = $obj_role->get_role($parent_id);
					$offer_price[$kk]['parent_role_name'] = !empty($parent_role_info['role_name']) ? $parent_role_info['role_name'] : '';
				}
				else
				{
					$offer_price[$kk]['parent_role_name']	= '管理员';
				}
			}
		}
		$page = $this->app->page();
		$page->value('offer',$offer_price);
		$page->params['template'] = 'role_offer_price.html';
		$page->output();
		
	}
	
	//修改角色报价
	public function doUpdateOfferPrice()
	{
		$goods_id = !empty($_GET['goods_id']) ? intval($_GET['goods_id']) : 0;
		
		$page = $this->app->page();
		//$page->value('offer',$offer_price);
		$page->params['template'] = 'update_offer.html';
		$page->output();
	}
	
	//修改报价
	public function doUpdateRolePrice()
	{
		$goods_id = !empty($_POST['goods_id']) ? intval($_POST['goods_id']) : 0;
		$user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
		$price = !empty($_POST['price']) ? ($_POST['price']) : 0;
		$role_id = !empty($_POST['role_id']) ? intval($_POST['role_id']) : 0;
		
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		
		$param = array(
			'goods_id'		=> $goods_id,
			'role_id'		=> $role_id,
			'user_id'		=> $user_id,
			'price'			=> $price,
			'operator_id'	=> $_SESSION['user_id'],
			'operator'		=> $_SESSION['username']
		);

		$res = $obj_good->update_role_price($param);
		
		if($res)
		{
			$return = array('status'=>1, 'msg'=>'更新成功');
		}
		else
		{
			$return = array('status'=>0, 'msg'=>'更新失败');
		}
		
		exit(json_encode($return));
	}
	
	//修改用户分类报价
	public function doUpdateUserRolePrice()
	{
		$goods_id = !empty($_POST['goods_id']) ? intval($_POST['goods_id']) : 0;
		$user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;
		$price = !empty($_POST['price']) ? ($_POST['price']) : 0;
		$role_id = !empty($_POST['role_id']) ? intval($_POST['role_id']) : 0;
		
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		
		$param = array(
			'goods_id'		=> $goods_id,
			'role_id'		=> $role_id,
			'user_id'		=> $user_id,
			'price'			=> $price,
			'operator_id'	=> $_SESSION['user_id'],
			'operator'		=> $_SESSION['username']
		);

		$res = $obj_good->update_role_good_price($param);
		
		if($res)
		{
			$return = array('status'=>1, 'msg'=>'更新成功');
		}
		else
		{
			$return = array('status'=>0, 'msg'=>'更新失败');
		}
		
		exit(json_encode($return));
	}
	
	
	//删除商品
	public function doDeleteGoods()
	{
		$goods_ids = $_REQUEST['ids'] ? trim($_REQUEST['ids']) : '';

		if($goods_ids)
		{
			importModule("GoodsInfo","class");
			$obj_good = new GoodsInfo;
			$res = $obj_good->delete_goods($goods_ids);

			if($res)
			{
				//成功
				$return = array(
					'statusCode'	=> 200,
					'message'		=> '删除成功',
					'navTabId'		=> 'role_list',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/goods.php?do=goodslist',
					'confirmMsg'	=> ''
				);
			}
			else
			{
				//失败
				$return = array(
					'statusCode'	=> 0,
					'message'		=> '删除失败',
					'navTabId'		=> 'role_list',
					'rel'			=> '',
					'callbackType'	=> 'closeCurrent',
					'forwardUrl'	=> HOST.'/role.php',
					'confirmMsg'	=> ''
				);
			}
			exit(json_encode($return));
		}
		
		exit(json_encode($return));	
		
		
	}
	
	public function doExportGoods()
	{
		require_once dirname(__FILE__).'/public/PHPExcel.php';
		$objPHPExcel = new \PHPExcel();
		
        $name = '商品';
        $name = iconv('UTF-8', 'GBK', $name);
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
        $category_id = $_GET['category_id'] ? $_GET['category_id'] : 0;
		//获取列表
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		$param = array(
			'category_id'	=> $category_id,
		);	
		$list = $obj_good->get_goods_list($param);

        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
        ->setCellValue('A1', '商品编号')
        ->setCellValue('B1', '商品名称')
        ->setCellValue('C1', '所属分类')
        ->setCellValue('D1', '商品规格')
        ->setCellValue('E1', '价格')
        ->setCellValue('F1', '税率')
		->setCellValue('G1', '库存')
		->setCellValue('H1', '状态')
		->setCellValue('I1', '添加人')
		->setCellValue('J1', '添加时间');
		
        $num = 0;
        if (!empty($list['list']) && is_array($list['list']))
        {
            foreach($list['list'] as $k => $v){
                $num=$k+2;

				if ($v['is_show'] == 0)
				{
					$show = '正常';
				}
				else
				{
					$show = '下架';
				}
                $objPHPExcel->setActiveSheetIndex(0)//Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, $v['goods_id'])
                ->setCellValue('B'.$num, $v['goods_name'])
                ->setCellValue('C'.$num, $v['cname'])
				->setCellValue('D'.$num, $v['unit'])
                ->setCellValue('E'.$num, $v['price'])
                ->setCellValue('F'.$num, $v['tax'])
                ->setCellValue('G'.$num, $v['stock'])
                ->setCellValue('H'.$num, $show)
				->setCellValue('I'.$num, $v['operator'])
				->setCellValue('J'.$num, $v['create_time']);
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle('商品名单');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: applicationnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
        exit;
        
	}
	
	//获取商品信息
	public function doGetGoods()
	{
		$goods_id = !empty($_GET['goods_id']) ? intval($_GET['goods_id']) : 0;
		
		importModule("GoodsInfo","class");
		$obj_good = new GoodsInfo;
		
		$good_info = $obj_good->get_good_detail($goods_id);
		$offer = $obj_good->get_role_price_detail($_SESSION['role_id'], $goods_id);
		
		exit(json_encode(array('offer'=>$offer, 'goods'=>$good_info)));
	}
	

}
$app->run();
	
?>
