<?php
namespace app\admin\controller;
use app\admin\model;
use think\Controller;
use think\View;
use think\Config;
use think\Session;
use think\Log;

class Weixin extends Controller
{
    public function index()
    {
		//print_r(Config::get('host'));die;
        $view = new View();
		//$view->assign('menu', Session::get('menu'));
		return $view->fetch('weixin/index');
    }
	
	//获取微信菜单
	public function get_menu()
	{
		$WxMenu = Model("WxMenu");
		$res = $WxMenu->getMenu();
		
		$json_menu = $this->create_json($res);
		
		echo $json_menu;
		exit();
	}
	
	//编辑菜单
	public function edit_menu()
	{
		$menu_id = $_POST['menu_id'];
		$menu_id_a = $_POST['menu_id_a'];
		$menu_id = $menu_id ? $menu_id : $menu_id_a;
		$menu_type = $_POST['menu_type'];
		$flag = $_POST['flag'];
		$url = $_POST['url'];
		$menu_name = $_POST['menu_name'];
		
		$param = array('id'=>$menu_id, 'menu_name'=>$menu_name, 'url'=>$url, 'type'=>$menu_type,'key'=>$flag);//print_r($param);die;
		$WxMenu = Model("WxMenu");
		$res = $WxMenu->edit_menu($param);
		if($res)
		{
			echo "<script>alert('编辑成功');window.location.href='http://www.yrsyc.cn/app/public/admin.php/admin/Weixin/index'</script>";
		}
		else
		{
			echo "<script>alert('编辑失败');history.go(-1);</script>";
		}
	}
	
	//添加菜单
	public function add_menu()
	{
		$parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : 0;
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$text = $_POST['text'];
		
		$param = array(
			'id'	=> $id,
			'text'	=> $text,
			'parent_id'	=> $parent_id
		);
		
		$WxMenu = Model("WxMenu");
		if(empty($id))
		{
			//添加菜单
			$id = $WxMenu->add_menu_name($param);
		}
		else
		{
			//编辑菜单
			$WxMenu->update_menu_name($param);
		}
		$data = array('status'=>true, 'id'=>$id);
		exit(json_encode($data));
	}
	
	//删除menu
	public function delete_menu()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : 0;
		
		$WxMenu = Model("WxMenu");
		$WxMenu->delete_menu($id);
		$data = array('status'=>true, 'message'=>'删除成功');
		exit(json_encode($data));
	}
	
	//生成微信菜单
	public function create_menu()
	{
		//获取菜单信息
		$WxMenu = Model("WxMenu");
		$res = $WxMenu->getMenu();
		
		$json_menu = $this->create_json_string($res);
		$json_menu = urlencode($json_menu);
		//print_r($json_menu);die;
		//$body = $_GET['body'];
		$url="http://www.yrsyc.cn/wx/index.php?do=wxmenu&body=$json_menu";
		$curl = curl_init();
		//设置抓取的url
		curl_setopt($curl, CURLOPT_URL, $url);
		//设置头文件的信息作为数据流输出
		curl_setopt($curl, CURLOPT_HEADER, 0);
		//设置获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		//执行命令
		$data = curl_exec($curl);
		//关闭URL请求
		curl_close($curl);
		//显示获得的数据
		//return $data;
		exit($data);
	}
	
	//检查子菜单数
	public function check_item()
	{
		$parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : 0;
		
		$WxMenu = Model("WxMenu");
		$res = $WxMenu->check_menu($parent_id);
		$data = array('status'=>false, 'message'=>'success');
		if($parent_id==0)
		{
			if($res[0]['count'] >=3)
			{
				$data = array('status'=>true, 'message'=>'子菜单数量已达上限');
			}
		}
		else
		{
			if($res[0]['count'] >=5)
			{
				$data = array('status'=>true, 'message'=>'子菜单数量已达上限');
			}
		}

		exit(json_encode($data));
	}
	
	
	private function create_json($res)
	{
		$menu_arr =array();
		//获取一级菜单
		foreach($res as $key => $val)
		{
			if($val['parent_id'] == 0)
			{
				$menu_arr[] = array(
					'id'	=> $val['id'],
					'text'	=> $val['menu_name'],
					'type'	=> $val['type'],
					'url'	=> $val['url']
				);
			}
		}
		
		if($menu_arr)
		{
			foreach($menu_arr as $key => $val)
			{
				foreach($res as $k => $v)
				{
					if($v['parent_id'] == $val['id'])
					{
						if($v['type']=='view')
						{
							$menu_arr[$key]['children'][] = array(
								'id'	=> $v['id'],
								'text'	=> $v['menu_name'],
								'type'	=> $v['type'],
								'url'	=> $v['url']
							);
						}
						else
						{
							$menu_arr[$key]['children'][] = array(
								'id'	=> $v['id'],
								'text'	=> $v['menu_name'],
								'type'	=> $v['type'],
								'key'	=> $v['menu_key']
							);
						}
						
					}
				}
			}
		}
		
		$menu[0] = array(
			'id'=>0,
			'text'=>'所有菜单',
			'children' => $menu_arr
		);
		
		//print_r($menu);die;
		return json_encode($menu);
	}
	
	
	private function create_json_string($res)
	{
		$menu_arr =array();
		//获取一级菜单
		foreach($res as $key => $val)
		{
			if($val['parent_id'] == 0)
			{
				$menu_arr[] = array(
					'id'	=> $val['id'],
					'text'	=> $val['menu_name'],
					'type'	=> $val['type'],
					'url'	=> $val['url']
				);
			}
		}
		
		if($menu_arr)
		{
			foreach($menu_arr as $key => $val)
			{
				foreach($res as $k => $v)
				{
					if($v['parent_id'] == $val['id'])
					{
						/*
						$menu_arr[$key]['sub_button'][] = array(
							'name'	=> $v['menu_name'],
							'type'	=> $v['type'],
							'url'	=> $v['url']
						);*/
						
						if($v['type']=='view')
						{
							$menu_arr[$key]['sub_button'][] = array(
								'type'	=> $v['type'],
								'name'	=> $v['menu_name'],
								'url'	=> $v['url']
							);
						}
						else
						{
							$menu_arr[$key]['sub_button'][] = array(
								'type'	=> $v['type'],
								'name'	=> $v['menu_name'],
								'key'	=> $v['menu_key']
							);
						}
					}
				}
			}
		}
		
		$menu_wx = array();
		foreach($menu_arr as $key => $val)
		{
			$menu_wx[$key]['name'] = $val['text'];
			$menu_wx[$key]['sub_button'] = $val['sub_button'];
		}
		
		$menu['button'] = $menu_wx;
		//print_r(json_encode($menu));die;
		//print_r($menu);die;
		return json_encode($menu, JSON_UNESCAPED_UNICODE);
	}
	


	
	
	
}
