<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//生成菜单
function get_menu_list($menus)
{
	if(!empty($menus) && is_array($menus))
	{
		$top_menu = array();
		foreach($menus as $key => $val)
		{
			if ($val['parent_id'] == 0)
			{
				$top_menu[] = $val;
			}
		}
		//整合二级目录
		foreach($top_menu as $k => $v)
		{
			foreach($menus as $km => $vm)
			{
				if ($vm['parent_id'] == $v['action_id'])
				{
					$top_menu[$k]['child'][] = $vm;
				}
			}
		}
		//菜单array
		$menu_arr = array();
		foreach ($top_menu as $keys => $value)
		{
			$menu_arr[$keys]['text'] = $value['action_name'];
			if (!empty($value['child']) && is_array($value['child']))
			{
				foreach ($value['child'] as $key => $val)
				{
					$menu_arr[$keys]['items'][$key]['id'] = $val['action_id'];
					$menu_arr[$keys]['items'][$key]['text'] = $val['action_name'];;
					$menu_arr[$keys]['items'][$key]['href'] = $val['action_url'];;
				}
			}
		}
		
		$menu_list = array('id'=>1, 'menu'=>$menu_arr);
		return $menu_list ? json_encode($menu_list) : '';
	}
}

//获取权限列表
function get_prev_list($privilege, $session_list='')
{
	$out_privilege_list = array();
	if (!empty($privilege) && is_array($privilege))
	{
		//获取顶级目录
		$out_privilege_list = array();
		foreach ($privilege as $key => $val)
		{
			if ($val['parent_id'] == 0)
			{
				//判断是否已有权限
				$priv = check_admin_priv($val['action_code'], $session_list);
				$val['checked'] = !empty($priv) ? 1 : 0;
				$out_privilege_list[] = $val;
			}
		}
		//二级目录
		foreach ($out_privilege_list as $k => $v)
		{
			foreach ($privilege as $key => $value)
			{
				if ($value['parent_id'] == $v['action_id'])
				{
					$priv_nex = check_admin_priv($value['action_code'], $session_list);
					$value['checked'] = !empty($priv_nex) ? 1 : 0;
					$out_privilege_list[$k]['child'][] = $value;
				}
			}
		}
	}
	return $out_privilege_list;
}

function check_admin_priv($priv_action, $session_list) {
	if ($session_list == 'all') {
		return true;
	}

	$r = strpos(',' . $session_list . ',', ',' . $priv_action . ',');
	
	if($r !== false) return true;
}

//检查权限
/*
function check_admin_priv($priv_action) 
{
	if ($_SESSION['action_list'] == 'all') {
		return true;
	}

	$r = strpos(',' . $_SESSION['action_list'] . ',', ',' . $priv_action . ',');
	
	if($r !== false) return true;

	echo "<script>alert("对不起您没有权限");history.back();</script>";
}
*/
//生成不重合的防伪码
function generate_code($min, $max, $num=1)
{
	$count = 0;
	$return = array();
	while($count < $num)
	{
		$return[] = mt_rand($min, $max);
		$return = array_flip(array_flip($return));
		$count = count($return);
	}
	shuffle($return);
	return $return;
}


function GetCurl($url){ 
	$curl = curl_init(); 
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($curl,CURLOPT_URL, $url); 
	curl_setopt($curl,CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); 
	$resp = curl_exec($curl); 
	curl_close($curl); 
	return $resp; 
}
