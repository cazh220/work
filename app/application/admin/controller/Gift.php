<?php
namespace app\admin\controller;
use think\Controller;
use think\View;

class Gift 
{
	public function index()
    {
		$gift_name = trim(input("gift_name"));
		
		$Gift = model('Gift');
		$where = array(
			'gift_name'		=> $gift_name ? $gift_name : '',
			'status'		=> 0
		);

		$obj_data = $Gift->gift_list($where);
		$data = $obj_data->toArray();

		$page = $obj_data->render();
        $view = new View();
        if(!empty($data['data']))
        {
        	foreach($data['data'] as $key => $val)
        	{
        		if($val['num'] == 0 || time() > strtotime($val['validity_time']))
        		{
        			$data['data'][$key]['status_name'] = '下架';
        		}
        		else
        		{
        			$data['data'][$key]['status_name'] = '正常';
        		}
        	}
        }
		$view->assign('data', $data['data']);
		$view->assign('page', $page);
		return $view->fetch('gift/index');
    }
	
	public function add()
	{
		$view = new View();
		return $view->fetch('gift/add');
	}
	
	public function doAdd()
	{
		$gift_name = input('gift_name');
		$gift_intro = input('gift_intro');
		$credit = input('credit');
		$gift_pic = input('gift_pic');
		$attribute = input('attribute');

		$param = array(
			'gift_name'		=> $gift_name,
			'gift_photo'	=> date("Ymd").'/'.$gift_pic,
			'credits'		=> $credit,
			'num'			=> 30,
			'sales_num'		=> 123,
			'gift_intro'	=> $gift_intro,
			'create_time'	=> date("Y-m-d H:i:s", time()),
			'status'		=> 0,
			'update_time'	=> date("Y-m-d H:i:s", time()),
			'validity_time'	=> date("Y-m-d H:i:s", time()),
			'attribute'		=> $attribute,
			'is_delete'		=> 0
		);
		$Gift = model('Gift');
		$ressult = $Gift->addGift($param);
		$url = $_SERVER['HTTP_ORIGIN']."/app/public/admin.php/admin/gift/index";
		if ($ressult == 0)
		{
			echo "<script>alert('新建礼品失败');history.back();</script>";
		}
		else
		{
			echo "<script>window.location.href='".$url."';</script>";
		}
	}
	
	public function edit()
	{
		$gift_id = input("gift_id");
		
		$Gift = model('Gift');
		
		$detail = $Gift->gift_detail($gift_id);

        $view = new View();
		$view->assign('data', $detail);
		return $view->fetch('gift/edit');
	}
	
	public function doEdit()
	{
		$gift_name = input('gift_name');
		$gift_intro = input('gift_intro');
		$credit = input('credit');
		$gift_pic = input('gift_pic');
		$gift_id = input('gift_id');
		$attribute = input('attribute');
		
		$param = array(
			'gift_name'		=> $gift_name,
			'gift_photo'	=> $gift_pic,
			'credits'		=> $credit,
			'num'			=> 30,
			'sales_num'		=> 123,
			'gift_intro'	=> $gift_intro,
			'create_time'	=> date("Y-m-d H:i:s", time()),
			'status'		=> 0,
			'update_time'	=> date("Y-m-d H:i:s", time()),
			'validity_time'	=> date("Y-m-d H:i:s", time()),
			'is_delete'		=> 0,
			'gift_id'		=> $gift_id,
			'attribute'		=> $attribute,
		);

		$Gift = model('Gift');
		$ressult = $Gift->update_gift($param);
		$url = $_SERVER['HTTP_ORIGIN']."/app/public/admin.php/admin/gift/index";
		if ($ressult == 0)
		{
			echo "<script>alert('编辑礼品失败');history.back();</script>";
		}
		else
		{
			echo "<script>window.location.href='".$url."';</script>";
		}
	}
	
	
	public function delete_gift()
	{
		$gift_id = input("gift_id");
		if (!empty($gift_id))
		{
			$Gift = Model("Gift");
			$gift_arr = explode(",", $gift_id);
			foreach($gift_arr as $key => $val)
			{
				$res = $Gift->delete_gift($val);
			}
		}
		
		if ($res == 1)
		{
			echo "<script>window.location.href='index';</script>";
		}
		else
		{
			echo "<script>alert('删除礼品失败');history.back();</script>";
		}
	}
	
	public function offline()
	{
		$gift_id = input("gift_id");
		if (!empty($gift_id))
		{
			$Gift = Model("Gift");
			$res = $Gift->off_shelf($gift_id);
		}
		
		if ($res)
		{
			echo "<script>window.location.href='index';</script>";
		}
		else
		{
			echo "<script>alert('下架礼品失败');history.back();</script>";
		}
	}
	
	public function upload()
	{
		$files = request()->file('image');
		// 移动到框架应用根目录/public/uploads/ 目录下
		$info = $files->move(ROOT_PATH . 'public' . DS . 'uploads');
		if($info){
			// 成功上传后 获取上传信息
			return array('name'=>$info->getFilename(), 'status'=>1);
		}else{
			// 上传失败获取错误信息
			return array('error'=>$files->getError(), 'status'=>0);
		} 
	}
	
	public function delete_pic()
	{
		$pic_name = input("gift_pic");
		$gift_id = input("gift_id");
		$where = array(
			'pic_name'	=> $pic_name,
			'gift_id'	=> $gift_id
		);
		if ($pic_name)
		{
			$path_name  = ROOT_PATH . 'public' . DS . 'uploads/20170606/'.$pic_name;
			if(!file_exists($path_name))
			{
				return array('status'=>0);
			}
			else
			{
				unlink($path_name);
				//更新图片空
				$Gift = Model("Gift");
				$Gift->update_gift_photo($where);
				return array('status'=>1);
			}
		}
	}
	
}