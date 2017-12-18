$(function(){
	
	//省市联动
	$("#province").change(function(){
		var pid = $(this).val();
		$.ajax({
			url:'http://www.yrsyc.cn/app/public/index.php/index/region/get_region',
			type:'get',
			data:'id='+pid,
			dataType:'json',
			success:function(msg){
				var txt='<option value="">请选择</option>';
				$.each(msg, function(i, n){
					txt += '<option value="'+n.id+'">'+n.name+'</option>';
				});
				$("#city").html(txt);
			}
		});
	});
	
	//市县联动
	$("#city").change(function(){
		var pid = $(this).val();
		$.ajax({
			url:'http://www.yrsyc.cn/app/public/index.php/index/region/get_region',
			type:'get',
			data:'id='+pid,
			dataType:'json',
			success:function(msg){
				var txt='';
				$.each(msg, function(i, n){
					txt += '<option value="'+n.id+'">'+n.name+'</option>';
				});
				$("#district").html(txt);
			}
		});
	});
	
	
	$("#show").click(function(){
			var _class = $(this).attr("class");
			if(_class =='fa fa-eye')
			{
				$(this).removeClass();
				$(this).addClass("fa fa-eye-slash");
				$("#password").attr("type", "password");
			}
			else
			{
				$(this).removeClass();
				$(this).addClass("fa fa-eye");
				$("#password").attr("type", "text");
			}
		});
	
});

//发短信校验
function send_sms(obj)
{
	//判断手机是否填写
	var mobile = $("#mobile").val();
	//console.log(mobile);
	if(mobile=='')
	{
		alert('请填写手机号');
		return false;
	}
	
	//校验手机号是否存在
	$.ajax({
		url:'http://www.yrsyc.cn/app/public/index.php/index/index/is_exist',
			type:'get',
			data:'mobile='+mobile,
			dataType:'json',
			success:function(msg){
				if(msg.status == 1)
				{   
					//$("#sendsms").attr("disabled", true);
					alert(msg.message);
					return false;
				}else{
					//$("#sendsms").attr("disabled", false);
					time(obj);
					//发短息ajax
					$.ajax({
						url:'http://www.yrsyc.cn/app/public/index.php/index/sms/sendsms',
							type:'get',
							data:'mobile='+mobile,
							dataType:'json',
							success:function(msg){
								//console.log(msg);
								alert(msg.message);
								return false;
							}
					});
				}
			}
	});
	
	
}


//忘记密码发送验证码
function send_code(obj)
{
	//判断手机是否填写
	var mobile = $("#mobile").val();
	if(mobile=='')
	{
		alert('请填写手机号');
		return false;
	}
	
	reg = /^1\d{10}$/;
	if(!reg.test(mobile))
	{
		alert('手机号格式不正确');
		return false;
	}
	
	time(obj);//倒计时
	//发短息ajax
	$.ajax({
		url:'http://www.yrsyc.cn/app/public/index.php/index/sms/sendsms',
			type:'get',
			data:'mobile='+mobile,
			dataType:'json',
			success:function(msg){
				//console.log(msg);
				alert(msg.message);
				return false;
			}
	});
	
	
}

//检查注册用户是否亦存在
/*
function is_user()
{
	//校验手机
	var mobile = $("#mobile").val();
	if(mobile=='')
	{
		alert('请填写手机号');
		return false;
	}
	//校验手机号是否已存在
	$.ajax({
		url:'http://www.yrsyc.cn/app/public/index.php/index/index/is_exist',
			type:'get',
			data:'mobile='+mobile,
			dataType:'json',
			success:function(msg){
				if(msg.status == 1)
				{
					$("#sendsms").attr("disabled", true);
					alert(msg.message);
					return false;
				}else{
					$("#sendsms").attr("disabled", false);
				}
			}
	});
}
*/

//注册校验
function check_user()
{
	//校验手机
	var mobile = $("#mobile").val();
	if(mobile=='')
	{
		alert('请填写手机号');
		return false;
	}
	//校验手机号是否已存在
	/*
	$.ajax({
		url:'http://www.yrsyc.cn/app/public/index.php/index/index/is_exist',
			type:'get',
			data:'mobile='+mobile,
			dataType:'json',
			success:function(msg){
				if(msg.status == 1)
				{
					alert(msg.message);
					return false;
				}
			}
	});*/
	
	//校验短信验证码ajax
	var code = $("#code").val();
	if(code == '')
	{
		alert('请填写短信验证码');
		return false;
	}
	else
	{
		$.ajax({
			url:'http://www.yrsyc.cn/app/public/index.php/index/sms/check_sms',
				type:'get',
				data:'mobile='+mobile+'&code='+code,
				dataType:'json',
				success:function(msg){
					if(msg.status==1)
					{
						alert(msg.message);
						return false;
					}
					else
					{
						//$("$_mobile").val(mobile);
						//校验密码
						var fpassword = $("#password").val();
						var repassword = $("#repassword").val();
						if(fpassword == '')
						{
							alert('请填写密码');
							return false;
						}
						
						if(repassword == '')
						{
							alert('请填写确认密码');
							return false;
						}
						
						//判断密码是否符合要求
						var exp = /^\w{6,20}/;
						if(!exp.test(fpassword))
						{
							alert("密码不符合要求");
							return false;
						}

						if(fpassword != repassword)
						{
							alert('密码不一致');
							return false;
						}
						
						//请填写单位名
						var company_name = $("#company_name").val();
						if(company_name == '')
						{
							alert('请填写姓名');
							return false;
						}
						
						//单位地址
						var company_addr = $("#company_addr").val();
						if(company_addr == '')
						{
							alert('请填写单位地址');
							return false;
						}
						
						
						$("#userinfoform").submit();
					}
					//console.log(msg);
				}
		});
	}
	
}

//登录
function login_s()
{
	var mobile = $("#user").val();
	if(mobile=='')
	{
		alert('请填写手机号');
		return false;
	}
	var password = $("#password").val();
	//console.log(password);
	if(password == '')
	{
		alert('请填写密码');
		return false;
	}
	
	$.ajax({
		url:'http://www.yrsyc.cn/app/public/index.php/index/index/do_login',
			type:'get',
			data:'mobile='+mobile+'&password='+password,
			dataType:'json',
			success:function(msg){
				//console.log(msg);
				//return false;
				if(msg.status==1)
				{
					alert(msg.message);
					return false;
				}
				else
				{
					if(msg.status ==2)
					{
						alert(msg.message);
					}
					window.location.href='http://www.yrsyc.cn/app/public/index.php/index/member/index';
				}
			}
	});
	
	
}

function update_pwd()
{
	//校验手机
	var mobile = $("#mobile").val();
	if(mobile=='')
	{
		alert('请填写手机号');
		return false;
	}
	//校验短信验证码ajax
	var code = $("#code").val();
	if(code == '')
	{
		alert('请填写短信验证码');
		return false;
	}
	else
	{
		$.ajax({
			url:'http://www.yrsyc.cn/app/public/index.php/index/sms/check_sms',
				type:'get',
				data:'mobile='+mobile+'&code='+code,
				dataType:'json',
				success:function(msg){
					if(msg.status==1)
					{
						alert(msg.message);
						return false;
					}
					else
					{
						//校验密码
						var fpassword = $("#password").val();
						var repassword = $("#repassword").val();
						if(fpassword == '')
						{
							alert('请填写密码');
							return false;
						}
						
						if(repassword == '')
						{
							alert('请填写确认密码');
							return false;
						}
						
						if(fpassword != repassword)
						{
							alert('密码不一致');
							return false;
						}
						
						$("#userinfoform").submit();
					}
					//console.log(msg);
				}
		});
	}
}

//防伪码查询
function checksecuritycode()
{
	var security_code = $("#security_code").val();
	if(security_code == '')
	{
		alert('请填写防伪码');
		return false;
	}
	
	$("#userinfoform").submit();
	
}

//快捷查询
function fast_checksecuritycode()
{
	var security_code = $("#security_code").val();
	if(security_code == '')
	{
		alert('请填写防伪码');
		return false;
	}
	
	$.ajax({
		url:'http://www.yrsyc.cn/app/public/index.php/index/security/query_security_code',
			type:'POST',
			data:'security_code='+security_code,
			dataType:'json',
			success:function(msg){
				if(msg.status==1)
				{
					var txt = '';
					txt += "<b>查询结果：</b>你查询的防伪码是正品！使用者："+msg.patient;
					$("#search_result").html(txt);
					return false;
				}
				else
				{
					var txt = '';
					txt += "<b>查询结果：</b>你的防伪码可能还未录入系统;";
					$("#search_result").html(txt);
				}
				//console.log(msg);
			}
	});
}

function add_to_cart(id)
{
	$.ajax({
		url:'http://www.yrsyc.cn/app/public/index.php/index/goods/add_cart',
			type:'get',
			data:'gift_id='+id,
			dataType:'json',
			success:function(msg){
				alert(msg.message);
				if(msg.status==0)
				{
					return false;
				}
			}
	});
}

function register()
{
	window.location.href="http://www.yrsyc.cn/app/public/index.php/index/index/register";
}

//验证码
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