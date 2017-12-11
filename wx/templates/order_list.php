<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>我的订单</title>
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
  <h1>我的订单</h1>
  <!--<a href="#pagetwo" data-role="button" data-icon="alert">12</a>-->
  </div>

  <div data-role="content">
   
    <div style="height:20px"></div>
	<ul data-role="listview">
	  {foreach from=$list item=item key=key}
      <li><a href="order.php?do=myorder&order_no={$item.order_no}" data-ajax="false">
      		<table style="font-size: xx-small; line-height: 25px;">
      			<tr>
      				<td width="60%">订单编号：{$item.order_no}</td>
      				<td width="40%">下单时间：{$item.create_time}</td>
      			</tr>
      			<tr>
      				<td width="60%">订单总积分：{$item.total_credits}</td>
      				<td width="40%">订单状态：{$item.order_status_name}</td>
      			</tr>
      		</table>
      </a></li>
      {/foreach}
    </ul>
  </div>
  
</div>


</body>
</html>