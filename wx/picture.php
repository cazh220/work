<?php 
/**
 * 图片处理
 * 
 */
require_once('./common.inc.php');

class picture extends Action {
	
	/**
	 * 默认执行的方法(用户登录页面)
	 */
	public function doDefault(){
		
	}
	
	
	//上传图片
	public function doUpload()
	{
		file_put_contents("pic.txt", date("Y-m-d H:i:s")."上传图片3：".json_encode($_POST)."\n", FILE_APPEND); 
		$smeta = $_POST['base64str'];
		//file_put_contents("pic.txt", date("Y-m-d H:i:s")."上传图片2：".$smeta."\n", FILE_APPEND); 
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $smeta, $result)) {//base64上传
			$data = base64_decode(str_replace($result[1], '', $smeta));
			$dataname = uniqid().'.jpg';
			file_put_contents("pic.txt", date("Y-m-d H:i:s")."上传图片1：".$dataname."\n", FILE_APPEND);
			$picture_path = $this->get_picture_dir();
			$res_name = $this->path.'/'.$dataname;
			$dataname = $picture_path.'/'.$dataname;
			
			if (file_put_contents($dataname, $data)) {
				file_put_contents("pic.txt", date("Y-m-d H:i:s")."上传图片：".$dataname."\n", FILE_APPEND);
				echo $res_name;
			}else{
				echo '';
			}
		}
	}
	
	private function get_picture_dir()
	{
		$dirname = date("Ymd", time());
		$pic_path = '../app/public/uploads/'.$dirname;
		$this->path = $dirname;
		if(!file_exists($pic_path)) {
			if(!mkdir($pic_path, 0777, true)) {
				echo "<script>alert('创建新图片目录失败');history.go(-1);</script>";
				exit();
				//exit(json_encode(array('status'=>0, 'info'=>'创建目录失败')));
			}
			chmod($pic_path.'/',0777);
		}
		return $pic_path;
	}
	
	
	private function _upload_pic()
	{
		if (!empty($_FILES['cfile']['name']))
		{
			$ar_type  = explode('.',$_FILES['cfile']['name']); 
			$s_type   = strtolower($ar_type[1]);
			
			if(!in_array($s_type,array('jpg','png','bmp','gif','jpeg'))){
				echo "<script>alert('上传图片格式不正确(".$s_type.")');history.go(-1);</script>";
				
				exit();
				//exit(json_encode(array('status'=>0, 'info'=>'文件类型不正确')));
			}
			
			if($_FILES["cfile"]["size"] > 2000000)
			{
				echo "<script>alert('图片不大于1M');history.go(-1);</script>";
				exit();
				//echo "<script>alert('上传图片格式不正确');history.go(-1);</script>";
			}
			
			$dirname = date("Ymd", time());
			$pic_path = '../app/public/uploads/'.$dirname;
			if(!file_exists($pic_path)) {
				if(!mkdir($pic_path, 0777, true)) {
					echo "<script>alert('创建新图片目录失败');history.go(-1);</script>";
					exit();
					//exit(json_encode(array('status'=>0, 'info'=>'创建目录失败')));
				}
				chmod($pic_path.'/',0777);
			}
			
			import('util.UploadFile');
			$obj_upload = new UploadFile;
			//$destFolder = '../app/public/uploads/20171020/';
			$res = $obj_upload->upload($_FILES['cfile'], $pic_path.'/', 1);

			if($res === false) {
				echo "<script>alert('图片上传失败失败');history.go(-1);</script>";
				exit();
				//exit(json_encode(array('status'=>0, 'info'=>'文件上传失败')));
			}
			
			return $res;
		}
		else
		{
			return '';
		}
		
	}
	
	
	
}
$app->run();
	
?>
