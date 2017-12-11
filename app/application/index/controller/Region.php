<?php
namespace app\index\controller;

use think\Controller;
use think\Model;
use think\Db;
use think\View;

class Region
{	
	public function get_region()
	{
		$id = $_GET['id'];
		//获取区域
		$Region = model('Region');
		$area = $Region->get_region($id);

		exit(json_encode($area));
	}

	

}
