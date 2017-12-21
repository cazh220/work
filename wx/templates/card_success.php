<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>质保卡录入</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="public/qrcode/js/cookie.js"></script>
<script src="public/qrcode/js/wxJSSDK.js"></script>
<script src="public/qrcode/js/scanQRCode.js"></script>
</head>
<style>
{literal}
.to{
	width: 100px;
	height: 100px;
	background-image: url('templates/images/3.png');
	background-size: cover;
	display: block;
	border-radius: 50px;
	-webkit-border-radius:50px;
	-moz-border-radius:50px;
	margin:0 auto;
}
.record_header{
	float: left;
	line-height: 30px;
	text-align: center;
	font-weight: bold;
}
.record_content{
	float: left;
	line-height: 30px;
	text-align: left;
	font-size: 11px;
}
{/literal}
</style>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back" data-ajax="false">后退</a>
  <h1>质保卡录入</h1>
    <a href="{$url_application}/user.php?do=ucenter&user_id={$user_id}" class="ui-btn ui-corner-all ui-icon-home ui-btn-icon-notext" data-ajax="false">首页</a>
  </div>

  <div data-role="content">
    <div style="text-align:center; padding:10px 0px 20px 0px">恭喜！{if $action==1}更新成功{else}录入完成{/if}</div>
	<label style="font-size:28px; font-weight: bold; position:absolute; left: 46%; margin-top: 20px">{if $action==1}0{else}{$credits}{/if}</label>
	<div class="to"></div>
	<div style="text-align:center; padding:20px 0px 20px 0px">当前总积分：{$left_credits}</div>
	<a href="patient.php?do=techrecord&user_id={$user_id}&security_code={$security_code}" data-role="button" data-ajax="false">完善患者信息</a>
	{if $user_type == 1}
	<a href="#" id="scanQRCode" data-id="{$url_application}/user.php?do=patientin&user_id={$user_id}&qrcode="  data-ajax="false" data-role="button">继续录入</a>
	{else}
	<a href="#" id="scanQRCode" data-id="{$url_application}/user.php?do=doctorin&user_id={$user_id}&qrcode="  data-ajax="false" data-role="button">继续录入</a>
	{/if}
	
	
	<div style="height: 200px;"></div>
	<div>
		<div class="record_header" style="width: 20%;">日期</div>
		<div class="record_header" style="width: 30%;">卡号</div>
		<div class="record_header" style="width: 30%;">医院</div>
		<div class="record_header" style="width: 20%;">医生</div>
	</div>
	<hr>
	<div>
		<marquee direction="up" behavior="scroll" scrollamount="1" height="120px" loop="-1">
		{if $patient}
		{foreach from=$patient item=item key=key}
			<div>
			<div class="record_content" style="width: 20%;">{$item.create_time}</div>
			<div class="record_content" style="width: 30%;">{$item.security_code}</div>
			<div class="record_content" style="text-align: center; width: 30%;">{$item.hospital}</div>
			<div class="record_content" style="text-align: center; width: 20%;">{$item.doctor}</div>
			</div>
		{/foreach}
		{/if}
		</marquee>
	</div>
	
	
  </div>
</div>

</body>
</html>