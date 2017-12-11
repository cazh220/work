<?php

/**
 *  分页工具类
 *
 * @package lib
 * @subpackage util
 * @author  
 */
class PageUtil {
	
	/**
	 * 起始页码数
	 */
	private static $init = 1;
	
	/**
	 * 结束页码数
	 */
	private static $max;
	
	/**
	 * 分页程序(分页数大于页码个数时可以偏移)
	 * 1.如果当前页小于等于左偏移;2.如果当前页大于左偏移;
	 * 3.如果当前页码右偏移超出最大分页数;4.左右偏移都存在时的计算;
	 * 
	 * @param int $total_num 总的记录数
	 * @param int $current_page 当前页
	 * @param int $page_size 每页显示数
	 * @param int $page_offset 左右偏移量
	 * @param int $page_num  页码个数
	 * @return array|bool  $page 
	 */
	public static function pagination($total_num, $current_page = 1, $page_size = 20, $page_offset = 2, $page_num = 4) {
		if($total_num <= 0){return false;}		
	
		$page_count = (int)(($total_num-1)/$page_size)+1;
		
		$current_page = (int)$current_page;
		
		if(!isset($current_page) || $current_page == 0){
			$current_page = 1;
		}else if($current_page > $page_count){
			$current_page = $page_count;
		}
	
		$prev_page = ($current_page > 1 && $total_num > 1) ? $current_page - 1 : $current_page;
		
		$next_page = ($current_page >= 1 && $current_page < $page_count) ?  $current_page + 1 : $current_page;

		if($page_count > $page_num){
			if($current_page <= $page_offset){
				$init = 1;
				$max = $page_num;
			}else{
				if($current_page + $page_offset > $page_count){
					$init = $page_count-$page_num+1;
					$max = $page_count;
				}else{
					$init = $current_page - $page_offset+1;
					$max = $current_page + $page_offset;
				}
			}
		}else{
			$init = self::$init;
			$max = $page_count;
		}
		$page = array(
					'init'        => $init,
					'max'         => $max,
					'currentPage' => $current_page,
					'prevPage'    => $prev_page,
					'nextPage'    => $next_page,
					'lastPage'    => $page_count,
					'pageCount'   => $page_count,
					'pageSize'    => $page_size,
					'totalNum'    => $total_num
				);	
		
		return $page;
	}
}
?>