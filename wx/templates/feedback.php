<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>沪鸽商城</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>
</head>
<script type="text/javascript">
{literal}
function show(note)
{
	//提示
  layer.open({
    content: note
    ,skin: 'msg'
    ,time: 2 //2秒后自动关闭
  });
}

function feed_back()
{
	var feedback = $("#feedback_content").val();

	if (feedback == '')
	{
		show('请填写');
		return false;
	}

	$("#feedback").submit();
	
}

{/literal}	
</script>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>意见反馈</h1>
    <a href="http://www.yrsyc.cn/wx/user.php?do=ucenter&user_id={$user_id}" class="ui-btn ui-corner-all ui-icon-home ui-btn-icon-notext" data-ajax="false">首页</a>
  </div>

  <div data-role="content">
    <form id="feedback" method="post" action="index.php?do=feedback" data-ajax="false" enctype="multipart/form-data">
      <label for="fname">您的意见反馈：</label>
      <textarea id="feedback_content" name="feedback" placeholder="请输入反馈内容"></textarea>
      <br>
      <label for="fname">上传截图：</label><input type="file" name="cfile" id="cfile" value="单位图片" style="color: darkgray;">
      <br><br>
	  <input type="hidden" id="action" name="action" value="1"/>
	  <input type="hidden" id="user_id" name="user_id" value="{$user_id}"/>
      <div style="text-align: right; width: 100%; float: left;"><a href="#" onclick="feed_back()" data-role="button" data-ajax="false">提交</a></div>  
    </form>
  </div>
</div>

</body>
</html>