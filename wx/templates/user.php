<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>个人中心</title>
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
	/*background-image: url("/public/upload/");*/
	background-size: cover;
	display: block;
	border-radius: 50px;
	-webkit-border-radius:50px;
	-moz-border-radius:50px;
	margin:0 auto;
	border: solid 2px;
	border-color: #ff1f3d;
}
.user_account{
	text-align:center;
	padding:10px 0px;
}
.base_info{
	padding:20px 0px;
}
{/literal}
</style>
<body>
<div data-role="page" id="pageone" data-theme="p">
  <div data-role="header"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>会员中心</h1>
  <!--<a href="#pagetwo" data-role="button" data-icon="alert">12</a>-->
  </div>

  <div data-role="content">
    <!--<div class="to" style="background-image: url('http://www.yrsyc.cn/app/public/uploads/{$user.head_img}');"></div>-->
    <div class="to" style="background-image: url('{$user.wx_headimgurl}');"></div>
	<div class="user_account">{$user.mobile}</div>
	<div class="base_info"><label class="left_show">已录入：{$patient_count}</label><label   class="right_show">积分余额：{$user.left_credits}</label></div>
	
	<div style="height:40px"></div>
	<ul data-role="listview">
      <li><a href="member.php?user_id={$user.user_id}" data-ajax="false">录入查询</a></li>
      {if $user.user_type==1}
      <li><a href="#" id="scanQRCode" data-id="{$url_application}/user.php?do=patientin&user_id={$user.user_id}&qrcode="  data-ajax="false">质保卡积分录入</a></li>
      {else}
      <li><a href="#" id="scanQRCode" data-id="{$url_application}/user.php?do=doctorin&user_id={$user.user_id}&qrcode=" data-ajax="false">质保卡积分录入</a></li>
      {/if}
      
    </ul>
	<div style="height:60px"></div>
	<ul data-role="listview">
	  <li><a href="message.php?user={$user.user_id}" data-ajax="false">我的消息{if $message_count > 0}<span class="ui-li-count" style="background-color: #E10F05;">{$message_count}</span>{/if}</a></li>
      <li><a href="order.php?do=orderlist&&user_id={$user.user_id}" data-ajax="false">我的订单</a></li>
      <li><a href="shop.php?&user_id={$user.user_id}" data-ajax="false">积分兑换</a></li>
    </ul>
	
	<div style="height:60px"></div>
	<ul data-role="listview">
      <li><a href="user.php?do=member&user_id={$user.user_id}" data-ajax="false">个人资料</a></li>
      <li><a href="user.php?do=setting&user_id={$user.user_id}" data-ajax="false">设置</a></li>
      <!--<li><a href="user.php?do=logout" data-ajax="false">退出</a></li>-->
    </ul>
  </div>
</div>


</body>
</html>