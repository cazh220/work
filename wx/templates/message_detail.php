<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>消息详情</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>
</head>
<body>
<div data-role="page" id="pageone"  data-theme="p">
  <div data-role="header"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>消息详情</h1>
    <a href="{$url_application}/user.php?do=ucenter&user_id={$message.to_user_id}" class="ui-btn ui-corner-all ui-icon-home ui-btn-icon-notext" data-ajax="false">首页</a>
  </div>

  <div data-role="content">
  {if $message.type == 1}
	{$doctor.realname}纠正了您于{$message.create_time}录入的{$patient.name}的信息，<span id="link" class="link_style" onclick="link_u({$message.to_user_id},'{$message.qrcode}')">点击这里</span>更正此信息，即就可获得30积分。
	{else}
	{$message.message}
	{/if}
  </div>
</div>
</body>
<script type="text/javascript">
{literal}
function link_u(user_id, qrcode)
{
	window.location.href="user.php?do=patientin&user_id="+user_id+"&qrcode="+qrcode;
}

{/literal}
</script>
</html>