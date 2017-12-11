<?php
namespace app\admin\controller;
use think\Controller;
use think\View;
use think\Db;
use think\Session;
use think\Log;


class Credit extends Controller
{
	//获取积分规则
    public function index()
    {
    	$Credit = model('Credit');
		$credit_list = $Credit->get_credit_list();

        $view = new View();
		$view->assign('list', $credit_list);
		return $view->fetch('credit/index');
    }
    
    //更新
    public function update()
    {
    	$data = $_POST;
    	$Credit = model('Credit');
    	if($data)
    	{
    		foreach($data as $key => $val)
    		{
    			$temp = explode("_", $key);
    			$id = $temp[1];
    			$res = $Credit->update_credits($val, $id);
    			if(!$res)
    			{
    				echo "<script>alert('保存失败');history.back();</script>";
    				exit();
    			}
    		}
    	}
    	echo "<script>alert('保存成功');window.location.href='index';</script>";
    	exit();
    }
	
	
}