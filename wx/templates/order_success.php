<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>订单完成</title>
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
  <h1>订单详情</h1>
  <a href="http://www.yrsyc.cn/wx/user.php?do=ucenter&user_id={$user_id}" class="ui-btn ui-corner-all ui-icon-home ui-btn-icon-notext" data-ajax="false">首页</a>
  </div>
  

  <div data-role="content">
    <div class="blank"></div>
    <form method="post" action="demoform.asp">
		<ul data-role="listview">
			<li>
				<div class="list_order_success">
					<div class="title"><img src="templates/images/fill_finish.png" width="20px" height="20px"><span class="text_title">{$info.create_time}您的订单（订单号：{$info.order_no}）</span></div>
					<div class="order_content" style="word-wrap:break-word; width:200px">{$info.info}已成功提交；扣除积分{$used_credits}，当前可用积分{$left_credits}；积分信息请前往会员中心查看</div>
				</div>
			</li>
			
		</ul>

      
    </form>
  </div>
  
</div>

</body>

</html>
