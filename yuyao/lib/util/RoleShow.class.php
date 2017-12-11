<?php
/**
 * 客户分类处理
 */
class RoleShow{
	
	//分类数组处理
	public static function role_show($category=array())
	{
		if(empty($category))
		{
			return array();
		}
		//print_r($category);die;
		//获取一级分类
		$f_category = array();
		if(!empty($category['list']) && is_array($category['list']))
		{
			foreach($category['list'] as $key => $val)
			{
				if($val['parent_id'] == 0)
				{
					$f_category[] = $val;
				}
			}
		}
		
		//二级分类
		$s_category = array();
		if(!empty($f_category) && is_array($f_category))
		{
			foreach($f_category as $k => $v)
			{
				foreach($category['list'] as $key => $val)
				{
					if($v['role_id'] == $val['parent_id'])
					{
						$f_category[$k]['child'][] = $val;
					}
				}
			}
		}
		
		//三级分类
		if(!empty($f_category) && is_array($f_category))
		{
			foreach($f_category as $k => $v)
			{
				if(!empty($v['child']) && is_array($v['child']))
				{
					foreach($v['child'] as $key => $val)
					{
						foreach($category['list'] as $kk => $vv)
						{
							if($val['role_id'] == $vv['parent_id'])
							{
								$f_category[$k]['child'][$key]['child'][] = $vv;
							}
						}
						
					}
				}
				
			}
		}

		return $f_category;
	}
	
	//获取角色的叶子节点
	public static function get_leaf_role($role_show=array())
	{
		if(empty($role_show))
		{
			return array();
		}
		
		$role_leaf = array();
		foreach($role_show as $key => $val)
		{
			if(empty($val['child']))
			{
				array_push($role_leaf, array('role_id'=>$val['role_id'], 'role_name'=>$val['role_name'], 'type'=>$val['type'], 'parent_id'=>$val['parent_id']));
			}
			else
			{
				foreach($val['child'] as $ka => $va)
				{
					if(empty($va['child']))
					{
						array_push($role_leaf, array('role_id'=>$va['role_id'], 'role_name'=>$va['role_name'], 'type'=>$val['type'], 'parent_id'=>$val['parent_id']));
					}
					else
					{
						foreach($va['child'] as $kb => $vb)
						{
							if(empty($vb['child']))
							{
								array_push($role_leaf, array('role_id'=>$vb['role_id'], 'role_name'=>$vb['role_name'], 'type'=>$val['type'], 'parent_id'=>$val['parent_id']));
							}
						}
					}
				}
			}
		}
		return $role_leaf;
		
	}
}

?>