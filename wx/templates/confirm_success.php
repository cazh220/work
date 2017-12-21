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
<body>

<div data-role="page" data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1> 质保卡录入</h1>
    <a href="{$url_application}/user.php?do=ucenter&user_id={$user_id}" class="ui-btn ui-corner-all ui-icon-home ui-btn-icon-notext"  data-ajax="false">首页</a>
  </div>
  

  <div data-role="content">
    <div class="blank"></div>
    <form method="post" action="demoform.asp">
		<ul data-role="listview">
			<li>
				<div class="list_order_success">
					<div class="title"><img src="templates/images/fill_finish.png" width="20px" height="20px"><span class="text_title">患者信息已提交，沪鸽正品保障，让患者更放心！</span></div>
					<div class="order_content"></div>
				</div>
			</li>
			
			
		</ul>
		      
    </form>
    <div class="blank"></div>
    <a href="#" id="scanQRCode" data-id="{$url_application}/user.php?do=doctorin&user_id={$user_id}&qrcode="  data-ajax="false" data-role="button">继续录入</a>
  </div>
  
</div>

</body>

</html>
