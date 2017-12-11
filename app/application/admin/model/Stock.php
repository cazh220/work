<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Stock extends Model
{
	public function stock_list($param=array())
	{
		$res = array();
		$obj = Db::name('hg_stock');
		if (!empty($param['keyword']))
		{
			$obj = $obj->where('company_name|user_name|stock_no','like',$param['keyword'].'%');
		}
		if (!empty($param['start_time']) && !empty($param['end_time']))
		{
			$obj = $obj->where('stock_time',['>=',$param['start_time']],['<=',$param['end_time']],'and');
		}
		
		$res = $obj->where('is_delete', 0)->order('stock_id desc')->paginate(10);
		return $res;
	}
	
	//生成出库单号
	public function create_stock_no()
	{
		$no = '20'.date("YmdHis", time()).rand(1000,9999);
		return $no;
	}
	
    //插入出库
    public function insert_stock($data=array())
    {
    	if($data)
    	{
			$res = Db::table('hg_stock')->insert($data);
    	}
    	
    	return $res ? $res : 0;
    }
    
    //获取出库详情
    public function stock_out_detail($stock_id=0)
    {
    	$res = Db::table('hg_stock')->where('stock_id',$stock_id)->find();
    	return $res;
    }
    
    //更新出库单状态
    public  function update_stock_out($stock_id=0)
    {
    	$sql = "UPDATE hg_stock SET status = 1, stock_time = NOW() WHERE stock_id = ".$stock_id;
    	try{
    		Db::execute($sql);
    	}catch(exception $e){
    		return false;
    	}
    	return true;
    	
    }
    
    //出库数量不过统计
    public function stock_out_num($mobile)
    {
    	if($mobile)
    	{
    		$res = Db::query("select count(*) as 'cnt' from hg_stock where mobile = '".$mobile."'");
    		
    	}
    	
    	return !empty($res[0]['cnt']) ? $res[0]['cnt'] : 0;
    }
    
    //根据码查出库单号
    public function view_stock_no($code='')
    {
    	if(empty($code))
    	{
    		return '';
    	}
    	$res = Db::table('hg_security_code')->where('security_code',$code)->find();
    	
    	return !empty($res['stock_no']) ? $res['stock_no'] : '';
    }
}