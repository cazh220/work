<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>登录</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<script src="public/layer_mobile/layer.js"></script>
</head>
<body>
<script type="text/javascript">
{literal}
	function login(host){
		//帐号
		var username = $('#username').val();
		if(username == ''){
			//alert('请输入您的帐号！');
			show('请输入您的帐号！');
			$('#username').focus();
			return false;
		}
		
		//密码
		var pwd =  $('#password').val();
		if(pwd == ''){
			//alert('请输入密码！');
			show('请输入密码！');
			$('#password').focus();
			return false;
		}

		$.ajax({
		   type: "POST",
		   dataType: "json",
		   url: host+"/user.php?do=login",
		   data: "username="+username+'&password='+pwd,
		   success: function(msg){
			   if(msg.status){
			      window.location.href = host+'/user.php?do=ucenter&user_id='+msg.user_id;	  
			   }else{
			   	  show(msg.info);
				  $('#password').val('');
			   }
		   }
		});
	}
	
	$(function(){
		$("#show").click(function(){
			var _class = $(this).attr("class");
			if(_class =='fa fa-eye-slash')
			{
				$(this).removeClass();
				$(this).addClass("fa fa-eye");
				$("#password").attr("type", "text");
			}
			else
			{
				$(this).removeClass();
				$(this).addClass("fa fa-eye-slash");
				$("#password").attr("type", "password");
			}
		});
	});
	
	// 提示
	function show(note)
	{
		//提示
	  layer.open({
	    content: note
	    ,skin: 'msg'
	    ,time: 5 //5秒后自动关闭
	  });
	}
{/literal}
</script>
<div data-role="page" data-theme="p" >
  <div data-role="header">
  <h1>防伪认证</h1>
  </div>

  <div data-role="content">
    <div style="text-align:center"><img src="templates/images/huge_logo2.png"></div>
    <form data-ajax="false">

        <input type="text" name="username" id="username" placeholder=" 手机号/邮箱">       
        <input type="password" name="password" id="password" placeholder="密码" autocomplete="off"><i id="show" class="fa fa-eye-slash" style="position: absolute; top: 229px; left: 90%;"></i>
      
		<a href="javascript:login('{$host}')" data-role="button">登录</a>
		<label class="left_show"><a data-ajax="false" href="user.php?do=findPwd" style="text-decoration:none;">忘记密码?</a></label><label class="right_show"><a data-ajax="false" href="user.php?do=bind_mobile" style="text-decoration:none;">马上注册</a></label>
		
		<div style="margin-top: 80px; font-size: 14px; line-height: 25px; text-align: left">
			沪鸽防伪认证系统说明：<br>
			1、 沪鸽美晶瓷、美国诺必灵等产品将陆续实现防伪认证；<br>
			2、 沪鸽防伪认证，将提高沪鸽、技工所、医疗机构的知名度和美誉度；<br>
			3、 技师和医生，都可以免费注册会员，并录入或查询信息； <br>
			4、 患者凭防伪认证卡，扫一扫二维码，可以查看防伪认证、技工所和医生的信息； <br>
			5、 技工所手机端和电脑端录入，其中电脑端可以批量录入（电脑端地址： http://www.yrsyc.cn/app/public/index.php/index#）； <br>
			6、 医生使用本系统，主要是查询和纠错，高效方便； <br>
			7、 技师和医生会员，录入和查询会获得积分，有礼品可兑换； <br>
			8、会员注册或登陆，将视为您已理解和同意会员协议；<br>
			9、任何疑问，可以通过沪鸽微信平台沟通咨询。<br>
		</div>
    </form>
  </div>
</div>

</body>
</html>