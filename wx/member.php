<?php 
/**
 * 会员信息处理
 * 
 */
require_once('./common.inc.php');

class member extends Action {
	
	/**
	 * 默认执行的方法(用户登录页面)
	 */
	public function doDefault(){
		$act = $_POST['act'];
		$user_id	= !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$page 		= !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$page_size  = 30;
		
		$date		= !empty($_REQUEST['date']) ? trim($_REQUEST['date']) : '';
		$hospital   = !empty($_REQUEST['hospital']) ? trim($_REQUEST['hospital']) : '';
		$name	    = !empty($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
		
		$data = array(
			'operate_user_id'	=> $user_id,
			'page'				=> $page,
			'page_size'			=> $page_size
		);
		//确定用户类型
		importModule("userInfo","class");
		$obj_user = new userInfo;
		$user_detail = $obj_user->get_user_detail($user_id);
		if($act == 1)
		{
			if($user_detail[0]['user_type'] == 1)
			{
				//技工
				$data['name'] = $name;
				$data['date'] = $date;
			}
			else
			{
				$data['date'] = $date;
				$data['hospital'] = $hospital;
			}
			
			
		}
	
		importModule("PatientInfo","class");
		$obj_patient = new PatientInfo;
		$list = $obj_patient->patient_list($data);
		if(!empty($list))
		{
			foreach($list as $key => $val)
			{
				$list[$key]['create_time'] = date("Y/m/d", strtotime($val['create_time']));
			}
		}
		$page = $this->app->page();
		$page->value('user_list',$_SESSION);
		$page->value('list',$list);
		$page->value('serach',$serach);
		$page->value('user_type',$user_detail[0]['user_type']);
		$page->value('user_id',$user_id);
		$page->value('date',$date);
		$page->value('name',$name);
		$page->value('hospital',$hospital);
		$page->params['template'] = 'search.php';
		$page->output();
	}
	
	//搜索
	public function doSearch()
	{
		print_r($_POST);
	}
	
}
$app->run();
	
?>
