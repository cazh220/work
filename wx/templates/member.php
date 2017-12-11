<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>个人资料</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>
<script src="public/js/megapix-image.js"></script>
<script src="public/js/megapix-image.test.js"></script>
<style>
{literal}
.ui-btn {
    font-size: 12px;
}
{/literal}
</style>
</head>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>个人资料</h1>
  </div>

  <div data-role="content">

		<table width="100%">
			<tr>
				<td width="35%" align="right"><span style="color: red;">*</span>真实姓名:</td>
				<td width="65%" colspan="2"><input type="text" name="realname" id="realname" placeholder="真实姓名" value="{$mine.realname}"></td>
			</tr>
			<tr>
				<td width="35%" align="right"><span style="color: red;">*</span>用户类型:</td>
				<td width="65%" colspan="2">
					<fieldset data-role="controlgroup">
						<label for="techer">技工</label>
						<input type="radio" name="typer" id="techer" value="1" {if $mine.user_type == 1 || !$mine.user_type}checked="checked" style="color:#000000"{/if} disabled>
						<label for="doctor">医生</label>
						<input type="radio" name="typer" id="doctor" value="2" {if $mine.user_type == 2}checked="checked" style="color:#000000"{/if} disabled>	
				    </fieldset>
				</td>
			</tr>
			<tr>
				<td width="35%" align="right"><span style="color: red;">*</span>电子邮箱:</td>
				<td width="65%" colspan="2"><input type="text" name="email" id="email" placeholder="邮箱" value="{$mine.email}"></td>
			</tr>
			<tr>
				<td width="35%" align="right"><span style="color: red;">*</span>单位全称:</td>
				<td width="65%" colspan="2"><input type="text" name="company_name" id="company_name" placeholder="单位全称" value="{$mine.company_name}"></td>
			</tr>
			<tr>
				<td width="35%" align="right">职位:</td>
				<td width="65%" colspan="2"><input type="text" name="job" id="job" placeholder="部门职位" value="{$mine.position}"></td>
			</tr>
			<tr>
				<td width="35%" align="right"><span style="color: red;">*</span>出生年月:</td>
				<td width="65%" colspan="2"><input type="date" name="create_time" id="create_time" value="{$mine.birthday}" {if $mine.birthday}disabled="disabled" style="color:#000000"{/if}></td>
			</tr>
			<tr>
				<td width="35%" align="right">员工数量:</td>
				<td width="65%" colspan="2"><input type="text" name="employee_num" id="employee_num" placeholder="椅位数/员工数" value="{if $mine.persons_num}{$mine.persons_num}{/if}"></td>
			</tr>
			<tr>
				<td width="35%" align="right"><span style="color: red;">*</span>区域:</td>
				<td width="65%" colspan="2">
					<fieldset data-role="controlgroup" data-type="horizontal">
						<label for="province">选择省：</label>
						<select name="province" id="province">
						{if $province}
							{foreach from=$province item=item key=key}
							<option value="{$item.id}" {if $item.id == $mine.province}selected{/if}>{$item.name}</option>
						    {/foreach}
						{/if}
						</select>

						<label for="city">选择市：</label>
						<select name="city" id="city">
						{if $city}
							{foreach from=$city item=item key=key}
							<option value="{$item.id}" {if $item.id == $mine.city}selected{/if}>{$item.name}</option>
						  {/foreach}
						{/if}
						</select>
						<label for="district">选择区：</label>
						<select name="district" id="district">
						{if $district}
							{foreach from=$district item=item key=key}
							<option value="{$item.id}" {if $item.id == $mine.district}selected{/if}>{$item.name}</option>
						  {/foreach}
						{/if}
						</select>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td width="35%" align="right"><span style="color: red;">*</span>详细地址:</td>
				<td width="65%" colspan="2"><input type="text" name="address" id="address" placeholder="详细地址" value="{$mine.company_addr}"></td>
			</tr>
			<tr>
				<td width="35%" align="right">单位图片:</td>
				<td width="65%" colspan="2">
				<form id="myform" data-ajax="false" enctype="multipart/form-data">
					<input type="file" name="cfile" id="fileInput" value="单位图片">
				</form>		
				</td>
			</tr>
			<!--<tr>
				<td width="30%" align="right">单位图片预览：</td>
				<td width="70%"><img src="http://www.yrsyc.cn/app/public/uploads/{$mine.head_img}" width="150px" height="150px"></td>
			</tr>-->
			
			<tr id="yulan" {if $mine.head_img == ''}style="display: none;"{/if}>
				<td width="35%" align="right">图片预览:</td>
				<td width="65%" colspan="2">
					
					<span id="id_canvas" style="display: none;">
						<img id="resultImage" style="display:none">
						<canvas id="resultCanvas1"></canvas>
						<input type="hidden"  id="company_pic" name="company_pic" value="{$mine.head_img}"/><a href="javascript:remove()" style="color: red;">撤销</a>
					</span>
					<img id="yu_picture" src="http://www.yrsyc.cn/app/public/uploads/{$mine.head_img}" width="150px" height="150px">
				</td>
			</tr>
			
			<tr>
				<td width="35%" align="right">单位介绍:</td>
				<td width="65%" colspan="2"><textarea name="addinfo" id="info" placeholder="单位介绍">{$mine.company_info}</textarea></td>
			</tr>
			{if $address}
			<tr>
				<td width="35%" align="right">收货地址:</td>
				<td width="65%" colspan="2">
					
				    <ul data-role="listview" data-inset="true">
				      <li><a href="area.php?do=addresslist&user_id={$mine.user_id}&act=member">
				      	地址：{$address_select.province_name}{$address_select.city_name}{$address_select.district_name}{$address_select.address}
				      	<br/>
				      	收货人：{$address_select.receiver}
				      	<br/>
				      	电话：{$address_select.mobile}
				      </a></li>
				    </ul>
				</td>
			</tr>
			{/if}
			
		</table>

		<input type="button" id="perfect" value="提交" >
		<input type="hidden" id="mobile" name="mobile" value="{$mine.mobile}" >
		<input type="hidden" id="province" name="province" value="{$mine.province}" >
		<input type="hidden" id="city" name="city" value="{$mine.city}" >
		<input type="hidden" id="district" name="district" value="{$mine.district}" >
		<input type="hidden" id="username" name="username" value="{$mine.username}" >
		<input type="hidden" id="company_pic" name="company_pic" value="{$mine.head_img}" >
		<input type="hidden" id="user_id" name="user_id" value="{$mine.user_id}" >
    <!--</form>-->
  </div>
</div>
<script type="text/javascript">
{literal}
//上传
/*
$("#cfile").change(function(){
	var data = new FormData();
	data.append('cfile', $('input[name=cfile]')[0].files[0]);
	$.ajax({
		url:'user.php?do=uploadfile',
		method:'post',
		data:data,
		dataType:'json',
		contentType: false,    //不可缺
		processData: false,    //不可缺
		success:function(msg){
			if(msg.status)
			{
				$("#company_pic").val(msg.path);
			}
			else
			{
				show('图片上传失败');
				return false;
			}
		}
	});
});
*/

$("#province").change(function(){
	var id = $(this).val();
	$.ajax({
		url:'area.php?do=getcity',
		method:'get',
		data:'province_id='+id,
		dataType:'json',
		success:function(msg){
			var str = '<option value="0">请选择</option>';
			if (msg.status==1)
			{
				$.each(msg.list, function(i, n){
					str += "<option value='"+n.id+"'>"+n.name+"</option>";
				});
			}
			$("#city").html(str);
		}
	});
});

$("#city").change(function(){
	var id = $(this).val();
	$.ajax({
		url:'area.php?do=getdistrict',
		method:'get',
		data:'city_id='+id,
		dataType:'json',
		success:function(msg){
			var str = '<option value="0">请选择</option>';
			if (msg.status==1)
			{
				$.each(msg.list, function(i, n){
					str += "<option value='"+n.id+"'>"+n.name+"</option>";
				});
			}
			$("#district").html(str);
		}
	});
})

function show(note)
{
	var user_id = $("#user_id").val();
	//提示
  layer.open({
    content: note
    ,btn: '我知道了'
    ,yes:function(){
    	window.location.href="user.php?do=ucenter&user_id="+user_id;
    }
  });
}

//提交
$("#perfect").click(function(){
	var mobile		= $("#mobile").val();
	var province 	= $("#province").val();	
	var city 		= $("#city").val();
	var user_id 	= $("#user_id").val();
	var address_id 	= $("input[name=address]:checked").val();//收货地址
	var district	= $("#district").val();
	var username	= $("#username").val();

	var realname 	= $("#realname").val();
	var email 		= $("#email").val();
	var company_name= $("#company_name").val();
	var job 		= $("#job").val();
	var create_time = $("#create_time").val();//出生时间
	var employee_num= $("#employee_num").val();
	var address 	= $("#address").val();
	var file_pic	= $("#company_pic").val();
	var info 		= $("#info").val();

	if (realname == '')
	{
		show('请填写真实姓名');
		return false;
	}

	if (email == '')
	{
		show("请填写邮箱");
		return false;
	}

	if (company_name == '')
	{
		show("请填写单位名称");
		return false;
	}

	if (create_time == '')
	{
		show("请填写成立时间");
		return false;
	}

	if (district == '')
	{
		show("请选择省市区");
		return false;
	}

	if (address == '')
	{
		show("请填写地址");
		return false;
	}
	
	var user_type;
	if($("#techer").attr("checked") == 'checked')
	{
		user_type = 1;
	}
	else
	{
		user_type = 2;
	}
	
	var data = "mobile="+mobile+"&province="+province+"&city="+city+"&district="+district+"&user_id="+user_id+"&address_id="+address_id+"&username="+username+"&realname="+realname+"&email="+email+"&company_name="+company_name+"&job="+job+"&create_time="+create_time+"&employee_num="+employee_num+"&address="+address+"&company_pic="+file_pic+"&info="+info;
	//console.log(data);//return false;
	$.ajax({
		type:"POST",
		url:"user.php?do=updateuser",
		data:data,
		dataType:"json",
		success:function(msg){
			show(msg.message);
			if(msg.status)
			{
				window.location.href = msg.url;
			}
			else
			{
				return false;
			}
		}
	});
	
});

{/literal}
</script>
</body>
</html>