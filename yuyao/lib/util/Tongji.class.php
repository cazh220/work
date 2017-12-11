<?php
/**
 * 订单商品统计
 */
class Tongji{
	
	//分类数组处理
	public static function goods_statistics($goods=array())
	{
		if(empty($goods))
		{
			return array();
		}
		
		//获取商品种类
		$goods_ids = array();
		foreach($goods as $key => $val)
		{
			array_push($goods_ids, $val['goods_id']);
		}
		
		//去重
		array_unique($goods_ids);
		$total_num = 0;
		$total_amount = 0;
		$result = array();
		foreach($goods_ids as $k => $v)
		{
			$goods_name = '';
			$goods_note ='';
			$unit ='';
			$goods_total_num = 0;
			$goods_total_amount = 0;
			foreach($goods as $kk => $vv)
			{
				if($v == $vv['goods_id'])
				{
					$goods_total_num += $vv['goods_num'];
					$goods_total_amount += ($vv['goods_num']*$vv['good_price']);
					$goods_name = $vv['goods_name'];
					$goods_note = $vv['good_note'];
					$unit		= $vv['unit'];
				}
				
			}
			$result[$v]['goods_name'] = $goods_name;
			$result[$v]['unit'] = $unit;
			$result[$v]['good_note'] = $goods_note;
			$result[$v]['total_num'] = $goods_total_num;
			$result[$v]['total_amount'] = $goods_total_amount;
		}

		return $result;
		
	}
}

?>