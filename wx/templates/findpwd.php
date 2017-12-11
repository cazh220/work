<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>找回密码</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>
</head>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>找回密码</h1>
  </div>

  <div data-role="content">

    <form method="post" action="user.php?do=updatePwd" data-ajax="false"  onsubmit="return check()">
		
		<table width="100%">
			<tr>
				<td width="100%" colspan="2"><input type="text" name="mobile" id="mobile" placeholder="输入手机号"></td>
			</tr>
			<tr>
				<td width="60%"><input type="text" name="vcode"  placeholder="输入验证码"></td>
				<td width="40%" style="text-align:center"><input type="button" data-role="none" value="免费获取验证码" class="vcode_button" id="send_message" onclick="send_sms()"></td>
			</tr>
			<tr>
				<td width="100%" colspan="2"><input type="password" name="password1" id="password1" placeholder="输入新密码" autocomplete="off"></td>
			</tr>
			<tr>
				<td width="100%" colspan="2"><input type="password" name="password2" id="password2" placeholder="确认密码" autocomplete="off"></td>
			</tr>
		</table>
        
		
		<input type="hidden" id="send_time_f" value="0" />
		
		<input id="sub" type="submit" value="提交">
    </form>
  </div>
</div>
<script type="text/javascript">
{literal}
function show(note)
{
	//提示
  layer.open({
    content: note
    ,skin: 'msg'
    ,time: 2 //2秒后自动关闭
  });
}


$(function(){
	$("#mobile").blur(function(){
		var mobile = $("#mobile").val();
		if(mobile)
		{
			$.ajax({
				url:"user.php?do=CheckMobile",
				data:'mobile='+mobile,
				method:'get',
				dataType:'json',
				success:function(msg){
					if(msg.status)
					{
						//$("#sub").attr("disabled","false"); 
					}
					else
					{
						//$("#sub").attr("disabled",false);
						show("您还没有注册，请先注册");
					}
					
				}
			});
			
		}
		
		
	});
});


function check()
{
	var mobile = $("#mobile").val();
	if (mobile == '') {
		alert('请填写手机号');
		return false;
	}

	var code = $("#vcode").val();
	if (code == '') {
		alert('请填写验证码');
		return false;
	}

	var password1 = $("#password1").val();
	if (password1 == '') {
		alert('请填写密码');
		return false;
	}

	var password2 = $("#password2").val();
	if (password2 == '') {
		alert('请填写确认密码');
		return false;
	}

	if (password2 != password1) {
		alert('密码不一致');
		return false;
	}

	return true;
}

function send_sms()
{
	var mobile = $("#mobile").val();
	if (mobile == '')
	{
		alert("请输入手机号");
		return false;
	}
	$.ajax({
		url:"user.php?do=SendSms",
		data:'mobile='+mobile,
		method:'get',
		dataType:'json',
		success:function(msg){
			show(msg.message);
			if(msg.status)
			{
				$("#send_time_f").val(1);
			}
			else
			{
				$("#send_time_f").val(0);
				return false;
			}
			
		}
	});
}

var wait=60;
	function time(o) {
		var f = $("#send_time_f").val();
		if(f==0)
		{
			return false;
		}
	  if (wait == 0) {
	   o.removeAttribute("disabled");   
	   o.value="免费获取验证码";
	   wait = 60;
	  } else { 
	 
	   o.setAttribute("disabled", true);
	   o.value="重新发送(" + wait + ")";
	   wait--;
	   setTimeout(function() {
	    time(o)
	   },
	   1000)
	  }
	 }


 
 $(function(){
 	$("#send_message").click(function(){
	 	time(this);
	 });
 });
{/literal}
</script>
</body>
</html>