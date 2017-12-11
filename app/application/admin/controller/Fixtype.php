<?php
namespace app\admin\controller;
use think\Controller;
use think\View;

class Fixtype 
{
	//获取修复体类别
	public function index()
	{
		$Fixtype = model('Fixtype');
		$data = $Fixtype->get_fix_type_list();
        $view = new View();
		$view->assign('list', $data);
		return $view->fetch('Fixtype/index');
	}
	
	//获取修复体列表明细
	public function fix_list()
	{
		$Fixtype = model('Fixtype');
		$data = $Fixtype->get_fix_list();
        $view = new View();
		$view->assign('list', $data);
		return $view->fetch('Fixtype/fix_list');
	}
	
	//添加修复体
	public function add_fix_type()
	{
        $view = new View();
		return $view->fetch('Fixtype/add');
	}
	
	
	//添加修复体
	public function add_fix_type_action()
	{
        $fix_type_desc = !empty($_POST['fix_type_desc']) ? trim($_POST['fix_type_desc']) : '';
        //$product_detail = !empty($_POST['product_detail']) ? trim($_POST['product_detail']) : '';
        $false_tooth_name = !empty($_POST['false_tooth_name']) ? trim($_POST['false_tooth_name']) : '';
        $Fixtype = model('Fixtype');
        $res = $Fixtype->add_fix_type($fix_type_desc, $false_tooth_name);
        if($res)
        {
        	echo "<script>alert('添加成功');window.location.href='index';</script>";
        }
        else
        {
        	echo "<script>alert('添加失败');history.go(-1);</script>";
        }
	}
	
	//修复体明细添加
	public function add_fix_type_detail_action()
	{
        $product_detail = !empty($_POST['product_detail']) ? trim($_POST['product_detail']) : '';
        $false_tooth_id = !empty($_POST['false_tooth_name']) ? trim($_POST['false_tooth_name']) : '';
        $Fixtype = model('Fixtype');
        $res = $Fixtype->add_fix_detail_type($product_detail, $false_tooth_id);
        if($res)
        {
        	echo "<script>alert('添加成功');window.location.href='fix_list';</script>";
        }
        else
        {
        	echo "<script>alert('添加失败');history.go(-1);</script>";
        }
	}
	
	
	//添加修复体明细
	public function add_fix_type_detail()
	{
		$Fixtype = model('Fixtype');
		$data = $Fixtype->get_fix_type_list();

		$view = new View();
		$view->assign('list', $data);
		return $view->fetch('Fixtype/add_fix');
	}
	
	//删除修复体
	public function delete_fix_type()
	{
		$false_tooth_id = !empty($_GET['c_tooth_id']) ? ($_GET['c_tooth_id']) : array();
		if($false_tooth_id)
		{
			$Fixtype = model('Fixtype');
			$res = $Fixtype->delete_fix_type($false_tooth_id);
			if($res)
			{
				echo "<script>alert('删除成功');window.location.href='index';</script>";
			}
			else
			{
				echo "<script>alert('删除失败');history.go(-1);</script>";
			}
		}
		else
		{
			echo "<script>alert('没有删除的数据');history.go(-1);</script>";
		}
		
	}
	
	
	public function delete_fix_type_detail()
	{
		$false_tooth_id = !empty($_GET['id']) ? ($_GET['id']) : array();
		if($false_tooth_id)
		{
			$Fixtype = model('Fixtype');
			$res = $Fixtype->delete_fix_type_detail($false_tooth_id);
			if($res)
			{
				echo "<script>alert('删除成功');window.location.href='fix_list';</script>";
			}
			else
			{
				echo "<script>alert('删除失败');history.go(-1);</script>";
			}
		}
		else
		{
			echo "<script>alert('没有删除的数据');history.go(-1);</script>";
		}
		
	}
	
	//修改
	public function edit_fix_type()
	{
		$false_tooth_id = !empty($_GET['false_tooth_id']) ? intval($_GET['false_tooth_id']) : 0;
		$Fixtype = model('Fixtype');
		$data = $Fixtype->get_fix_type_detail($false_tooth_id);
		$view = new View();
		$view->assign('data', $data);
		return $view->fetch('Fixtype/edit');
	}
	
	//修改明细
	public function edit_fix_type_detail()
	{
		$id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
		$Fixtype = model('Fixtype');
		$data = $Fixtype->get_fix_type_detail_new($id);
		$Fixtype = model('Fixtype');
		$list = $Fixtype->get_fix_type_list();
		$view = new View();
		$view->assign('data', $data);
		$view->assign('list', $list);
		return $view->fetch('Fixtype/edit_fix');
	}
	
	//编辑
	public function edit_fix_type_action()
	{
        $fix_type_desc = !empty($_POST['fix_type_desc']) ? trim($_POST['fix_type_desc']) : '';
        //$product_detail = !empty($_POST['product_detail']) ? trim($_POST['product_detail']) : '';
        $false_tooth_name = !empty($_POST['false_tooth_name']) ? trim($_POST['false_tooth_name']) : '';
        $false_tooth_id = !empty($_POST['false_tooth_id']) ? intval($_POST['false_tooth_id']) : 0;
        $Fixtype = model('Fixtype');
        $res = $Fixtype->edit_fix_type($fix_type_desc, $false_tooth_name, $false_tooth_id);
        if($res)
        {
        	echo "<script>alert('编辑成功');window.location.href='index';</script>";
        }
        else
        {
        	echo "<script>alert('编辑失败');history.go(-1);</script>";
        }
	}
	
	//编辑详细
	public function edit_fix_type_detail_action()
	{
        $product_detail 	= !empty($_POST['product_detail']) ? trim($_POST['product_detail']) : '';
        $false_tooth_id 	= !empty($_POST['false_tooth_id']) ? trim($_POST['false_tooth_id']) : '';
        $id 				= !empty($_POST['id']) ? intval($_POST['id']) : 0;
        $Fixtype = model('Fixtype');
        $res = $Fixtype->edit_fix_type_detail($product_detail, $false_tooth_id, $id);
        if($res)
        {
        	echo "<script>alert('编辑成功');window.location.href='fix_list';</script>";
        }
        else
        {
        	echo "<script>alert('编辑失败');history.go(-1);</script>";
        }
	}
    
		
	
}