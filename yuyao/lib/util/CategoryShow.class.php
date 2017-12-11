<?php
/**
 * 分类处理
 */
class CategoryShow{
	
	//分类数组处理
	public static function category_show($category=array())
	{
		if(empty($category))
		{
			return array();
		}
		//print_r($category);die;
		//获取一级分类
		$f_category = array();
		if(!empty($category) && is_array($category))
		{
			foreach($category as $key => $val)
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
				foreach($category as $key => $val)
				{
					if($v['cid'] == $val['parent_id'])
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
						foreach($category as $kk => $vv)
						{
							if($val['cid'] == $vv['parent_id'])
							{
								$f_category[$k]['child'][$key]['child'][] = $vv;
							}
						}
						
					}
				}
				
			}
		}

		//四级分类
		if(!empty($f_category) && is_array($f_category))
		{
			foreach($f_category as $k => $v)
			{
				if(!empty($v['child']) && is_array($v['child']))
				{
					foreach($v['child'] as $key => $val)
					{
						if(!empty($val['child']) && is_array($val['child']))
						{
							foreach($val['child'] as $keya => $vala)
							{
								foreach($category as $kk => $vv)
								{
									if($vala['cid'] == $vv['parent_id'])
									{
										$f_category[$k]['child'][$key]['child'][$keya]['child'][] = $vv;
									}
								}
								
							}
						}
						
					}
				}
				
			}
		}

		return $f_category;
	}
}

?>