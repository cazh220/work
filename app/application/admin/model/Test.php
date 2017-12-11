<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Test extends Model
{
	public function get_area($region_id=0)
	{
		$res = array();
		if (!empty($region_id))
		{
			$res = Db::table('hg_region')->where('id',$region_id)->find();

		}

		return isset($res['name']) ? $res['name'] : '';
	}
	
	//获取区域
	public function gets()
	{
		$res = array();
		$res = Db::table('hg_admin_action')->where('action_id',73)->find();

		return isset($res['action_name']) ? $res['action_name'] : '';
	}
	
	public function test()
	{
		$sql = "SELECT * FROM hg_admin_action";
		$res = Db::query($sql);
		
		return $res;
	}
	
	public function insert()
	{
		
		$sql = "INSERT INTO `yy_role` (`role_id`, `role_name`, `is_delete`, `create_time`, `update_time`) VALUES
(1, '宇尧实业', 0, '2017-09-07 00:00:00', '2017-09-07 00:00:00'),
(4, '一支队', 0, '2017-09-07 00:00:00', '2017-09-07 00:00:00'),
(5, '二支队', 0, '2017-09-08 00:00:00', '2017-09-08 00:00:00'),
(6, '三支队', 0, '2017-09-08 00:00:00', '2017-09-08 00:00:00');";

		//$sql  = "INSERT INTO hg_admin_action(parent_id, action_code, action_name, action_url)VALUES(74, 'default_pwd', '老会员初始密码', 'user/default_pwd')";
		//echo $sql;die;
		$res = Db::execute($sql);
		
		return $res;
	}
}