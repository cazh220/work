<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>验证手机号</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<link href="//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>
<script type="text/javascript">
{literal}

$(function(){
	$("#vcode").keyup(function(){
		var mobile = $("#mobile").val();
		var code = $("#vcode").val();
		$.ajax({
			type:"GET",
			url:"user.php?do=ValidateMobile",
			data:"mobile="+mobile+"&vcode="+code,
			dataType:'json',
			success:function(msg){
				if(msg.status)
				{
					$("#i_mobile").css("display", "");
					$("#i_vcode").css("display", "");
				}
				else
				{
					$("#i_mobile").css("display", "none");
					$("#i_vcode").css("display", "none");
				}
				
			}
		});
		
	});
	
});


// 提示
function show(note)
{
	//提示
  layer.open({
    content: note
    ,skin: 'msg'
    ,time: 2 //2秒后自动关闭
  });
}

function send_sms(obj)
{
	var mobile = $("#mobile").val();
	if (mobile == '')
	{
		//alert("请输入手机号");
		show("请输入手机号");
		return false;
	}
	//检查手机号是否已注册
	$.ajax({
		url:"user.php?do=checkmobile",
		data:'mobile='+mobile,
		method:'get',
		dataType:'json',
		success:function(msg){
			//console.log(msg);return false;
			if(msg.status==true)
			{
				show(msg.message);
				return false;
			}
			else
			{
				time(obj);
				//手机号可用
				$.ajax({
					url:"user.php?do=SendSms",
					data:'send=1&mobile='+mobile,
					method:'get',
					dataType:'json',
					success:function(msg){
						show(msg.message);
					}
				});
			}
			
		}
	});
	
	
	
}

//验证
function check()
{
	var mobile = $("#mobile").val();
	var code = $("#vcode").val();
	$.ajax({
		type:"GET",
		url:"user.php?do=ValidateMobile",
		data:"mobile="+mobile+"&vcode="+code,
		dataType:"json",
		success:function(msg)
		{
			if(msg.status)
			{
				window.location.href="user.php?do=showregister&mobile="+mobile;
			}
			else
			{
				show("验证码不一致");
				return false;
			}
		}
	});
}

var wait=60;
function time(o) {
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
 
 /*
 $(function(){
 	$("#send_message").click(function(){
	 	time(this);
	 });
 });
 */
{/literal}
</script>
</head>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>会员注册</h1>
  </div>

  <div data-role="content">

    <form method="post" action="user.php?do=ValidateMobile" data-ajax="false">
		
		<table width="100%">
			<tr>
				<td width="100%" colspan="2"><input type="text" name="mobile" id="mobile" placeholder="输入手机号"><i id="i_mobile" class="fa fa-check-circle-o fa-lg" style="color: green;position: absolute;margin-top: -33px;left: 90%; display: none;"></i></td>
			</tr>
			<tr>
				<td width="60%"><input type="text" name="vcode" id="vcode" placeholder="输入验证码"><i id="i_vcode" class="fa fa-check-circle-o fa-lg" style="color: green;position: absolute;margin-top: -33px;left: 53%; display:none"></i></td>
				<td width="40%" style="text-align:center"><input type="button" data-role="none" value="免费获取验证码" class="vcode_button" id="send_message" onclick="send_sms(this)"></td>
			</tr>
		</table>

		<input type="button" value="下一步" onclick="check()">
    </form>
  </div>
</div>

</body>
</html>