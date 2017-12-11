<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>会员注册</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/js/megapix-image.js"></script>
<script src="public/js/megapix-image.test.js"></script>
<script src="public/layer_mobile/layer.js"></script>
</head>
<style type="text/css">
{literal}
.ui-btn{
	font-size:12px
}
{/literal}
</style>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>会员注册</h1>
  </div>

  <div data-role="content">

    <form method="post" action="user.php?do=register" data-ajax="false" enctype="multipart/form-data">
		
		<table width="100%">
			<tr>
				<td width="30%" align="right"><span style="color: red;">*</span>真实姓名：</td>
				<td width="70%" colspan="2"><input type="text" name="realname" id="realname" placeholder="注册人真实姓名"></td>
			</tr>
			<tr>
				<td width="30%" align="right"><span style="color: red;">*</span>密码：</td>
				<td width="70%" colspan="2"><input type="password" name="password1" id="password1" placeholder="请输入密码(6-20个字符，由字母和数字或符号组成)" autocomplete="off"></td>
			</tr>
			<tr>
				<td width="30%" align="right"></td>
				<td width="70%" colspan="2"><span style="font-size:xx-small; color: #aaa;">6-20个字符，由字母和数字或符号组成<br><span id="note" style="font-size:xx-small; color: red;"></span></span>
					<br><img id="img1" src='public/themes/images/6.png' width="70px" height="24px"/><img id="img2" src='public/themes/images/5.png' width="70px" height="24px"/><img id="img3" src='public/themes/images/4.png' width="70px" height="24px"/>
				</td>
			</tr>
			<tr>
				<td width="30%" align="right"><span style="color: red;">*</span>确认密码：</td>
				<td width="70%" colspan="2"><input type="password" name="password2" id="password2" placeholder="确认密码" autocomplete="off"></td>
			</tr>
			<tr>
				<td width="30%" align="right"><span style="color: red;">*</span>用户类型：</td>
				<td width="70%" colspan="2">
					<fieldset data-role="controlgroup">
						<label for="techer">技工</label>
						<input type="radio" name="typer" id="techer" value="1" checked="checked">
						<label for="doctor">医生</label>
						<input type="radio" name="typer" id="doctor" value="2">	
				    </fieldset>
				</td>
			</tr>
			<tr>
				<td width="30%" align="right"><span style="color: red;">*</span>电子邮箱：</td>
				<td width="70%" colspan="2"><input type="text" name="email" id="email" placeholder="邮箱"></td>
			</tr>
			<tr>
				<td width="30%" align="right"><span style="color: red;">*</span>单位全称：</td>
				<td width="70%" colspan="2"><input type="text" name="company_name" id="company_name" placeholder="单位全称"></td>
			</tr>
			<tr>
				<td width="30%" align="right">部门：</td>
				<td width="70%" colspan="2"><input type="text" name="department" id="department" placeholder="请输入部门"></td>
			</tr>
			<tr>
				<td width="30%" align="right">职位：</td>
				<td width="70%" colspan="2"><input type="text" name="position" id="position" placeholder="请输入职位"></td>
			</tr>
			<tr>
				<td width="30%" align="right"><span style="color: red;">*</span>出生年月：</td>
				<td width="70%" colspan="2"><input  type="date" name="birthday" id="birthday" value="" placeholder="出生年月" onfocus="this.removeAttribute('placeholder')"></td>
			</tr>
			<tr>
				<td width="30%" align="right">椅位数：</td>
				<td width="70%" colspan="2"><input type="text" name="seats" id="seats" placeholder="椅位数"></td>
			</tr>
			<tr>
				<td width="30%" align="right">员工数：</td>
				<td width="70%" colspan="2"><input type="text" name="persons_num" id="persons_num" placeholder="员工数"></td>
			</tr>
			<tr>
				<td width="30%" align="right"><span style="color: red;">*</span>区域：</td>
				<td width="70%" colspan="2">
					<fieldset data-role="controlgroup" data-type="horizontal">
						<label for="province">选择省：</label>
						<select name="province" id="province">
						<option value="0">省</option>
						{if $province}
							{foreach from=$province item=item key=key}
							<option value="{$item.id}">{$item.name}</option>
						    {/foreach}
						{/if}
						</select>

						<label for="city">选择市：</label>
						<select name="city" id="city">
						  <option value="0">市</option>
						</select>
						<label for="district">选择区：</label>
						<select name="district" id="district">
						  <option value="0">区</option>
						</select>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td width="30%" align="right"><span style="color: red;">*</span>详细地址：</td>
				<td width="70%" colspan="2"><input type="text" name="address" id="address" placeholder="详细地址"></td>
			</tr>
			<tr>
				<td width="30%" align="right">单位图片：</td>
				<td width="70%" colspan="2"><input type="file" name="cfile" id="fileInput" value="单位图片" style="color: darkgray;"></td>
			</tr>
			<tr id="yulan" style="display: none;">
				<td width="30%" align="right">预览图片：</td>
				<td width="70%" colspan="2">
					<img id="resultImage" style="display:none">
					<canvas id="resultCanvas1"></canvas>
					<input type="hidden"  id="company_pic" name="company_pic" value=""/><a href="javascript:remove()" style="color: red;">撤销</a>
				</td>
			</tr>
			<tr>
				<td width="30%" align="right">单位介绍：</td>
				<td width="70%" colspan="2"><textarea name="addinfo" id="info" placeholder="单位介绍"></textarea></td>
			</tr>
			<tr>
				<td width="15%" style="text-align:right"></td>
				<td width="85%" style="text-align:left"><input type="checkbox" name="agree" id="agree" value="agree"  style="margin: 0 auto; left: -30px;">
					我已阅读并接受<a href="user.php?do=ViewXy" style="text-decoration:none">《用户注册协议》</a>
					
				</td>
			</tr>
			
			
		</table>
		<input type="button" id="register" value="注册">
		<!--<input type="submit" id="register" value="注册">-->
		<input type="hidden" id="mobile" name="mobile" value="{$mobile}" >
		<input type="hidden" id="username" name="username" value="{$username}" >
    </form>
  </div>
</div>
<script type="text/javascript">
{literal}
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
});

function remove()
{
	$("#company_pic").val("");
	$("#yulan").css("display", "none");
}

function show(note)
{
	//提示
  layer.open({
    content: note
    ,skin: 'msg'
    ,time: 2 //2秒后自动关闭
  });
}

$("#register").click(function(){
	var realname = $("#realname").val();
	var password1 = $("#password1").val();
	var password2 = $("#password2").val();
	var user_type = $("input[name=typer]:checked").val();
	var email = $("#email").val();
	var company_name = $("#company_name").val();
	var department = $("#department").val();
	var job = $("#position").val();
	var birthday = $("#birthday").val();
	var seats = $("#seats").val();
	var persons_num = $("#persons_num").val();
	var province = $("#province").val();
	var city = $("#city").val();
	var district = $("#district").val();
	var address = $("#address").val();
	var company_pic = $("#company_pic").val();
	var info = $("#info").val();
	
	/*
	console.log(realname+":"+password1+":"+password2+":"+user_type+":"+email+":"+company_name+":"+department+":"+job+":"+birthday+":"+seats+":"+persons_num+":"+province+":"+city+":"+district+":"+address+":"+company_pic+":"+info);
	return false;
	*/
	
	if (realname == '')
	{
		show('请填写真实姓名');
		return false;
	}

	if (password1 == '')
	{
		show("请填写密码");
		return false;
	}

	if (password2 == '')
	{
		show("请填写确认密码");
		return false;
	}
	
	if (password2 != password1)
	{
		show("密码不一致");
		return false;
	}

	if (email == '')
	{
		show("请填写邮箱");
		return false;
	}
	else
	{
			var exp = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
			if(!exp.test(email))
			{
				show("电子邮箱不正确");
				return false;
			}
	}

	if (company_name == '')
	{
		show("请填写单位名称");
		return false;
	}

	if (birthday == '')
	{
		show("请填写出生年月");
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

	var agree = $("#agree").attr("data-cacheval");
	if (typeof(agree)=="undefined" || agree == "true")
	{
	  show('请接受用户注册协议');
		return false;
	}	
	
	var mobile = $("#mobile").val();
	var username = $("#username").val();
	
	var param = realname+":"+password1+":"+password2+":"+user_type+":"+email+":"+company_name+":"+department+":"+job+":"+birthday+":"+seats+":"+persons_num+":"+province+":"+city+":"+district+":"+address+":"+company_pic+":"+info+":"+mobile+":"+username;
	
	$.ajax({
		url:'user.php?do=registeruser',
		method:'post',
		data:'param='+param,
		dataType:'json',
		success:function(msg){
			if(msg.status == 1)
			{
				show(msg.message);
				window.location.href='user.php?register=1';
			}
			else
			{
				show(msg.message);
				return false;
			}
		}
	});
	
	
	
	
	
});

$(function(){
	$("#password1").keyup(function(){

		var pwd = $(this).val();
		var l = pwd.length;
		//console.log(l);
		var exp = /^(?![A-Z]+$)(?![a-z]+$)(?!\d+$)(?![\W_]+$)\S{6,20}$/;
		if(!exp.test(pwd))
		{
			$("#note").html('密码不符合要求');
			$("#img2").attr("src", "public/themes/images/6.png");
			$("#img1").attr("src", "public/themes/images/5.png");
			$("#img3").attr("src", "public/themes/images/4.png");
			//alert("密码不符合要求");
			return false;
		}
		else if(l < 10)
		{
			$("#note").html('');
			$("#img2").attr("src", "public/themes/images/5.png");
			$("#img1").attr("src", "public/themes/images/3.png");
			$("#img3").attr("src", "public/themes/images/4.png");
		}
		else if(l < 15)
		{
			$("#note").html('');
			$("#img2").attr("src", "public/themes/images/2.png");
			$("#img1").attr("src", "public/themes/images/6.png");
			$("#img3").attr("src", "public/themes/images/4.png");
		}
		else
		{
			$("#note").html('');
			$("#img2").attr("src", "public/themes/images/5.png");
			$("#img1").attr("src", "public/themes/images/6.png");
			$("#img3").attr("src", "public/themes/images/1.png");
		}
	});
	
//	//图片上传
//	var fileInput = document.getElementById('fileInput');
//	fileInput.onchange = function(){
//		var file = fileInput.files[0];
//		var mpImg = new MegaPixImage(file);
//		console.log(mpImg);
//	}
});

{/literal}
</script>
</body>
</html>