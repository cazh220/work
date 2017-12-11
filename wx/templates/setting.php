<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>设置</title>
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
  <h1>设置</h1>
  </div>

  <div data-role="content">
	<ul data-role="listview">
      <li><a href="index.php?do=feedback&user_id={$user_id}" data-ajax="false">意见反馈</a></li>
      <li><a href="index.php?do=about" data-ajax="false">关于沪鸽</a></li>
      <li><a href="#" onclick="logout()" data-ajax="false">退出</a></li>
    </ul>
  </div>
</div>
<script type="text/javascript">
{literal}
function logout()
{
	if(confirm('确定退出？'))
	{
		window.location.href="user.php?do=logout";
	}
}
{/literal}
</script>

</body>
</html>