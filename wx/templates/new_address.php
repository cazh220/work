<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>新增收货地址</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>
</head>
<style>
{literal}
div.ui-select{
	font-size: 13px;
}
{/literal}
</style>
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

$(function(){
	$("#province").change(function(){
		var id = $(this).val();
		$.ajax({
			url:'area.php?do=getcity',
			method:'get',
			data:'province_id='+id,
			dataType:'json',
			success:function(msg){
				var str = '<option value="0">请选择</option>';
				if (msg.status==1)
				{
					$.each(msg.list, function(i, n){
						str += "<option value='"+n.id+"'>"+n.name+"</option>";
					});
				}
				$("#city").html(str);
			}
		});
	});
	$("#city").change(function(){
		var id = $(this).val();
		$.ajax({
			url:'area.php?do=getdistrict',
			method:'get',
			data:'city_id='+id,
			dataType:'json',
			success:function(msg){
				var str = '<option value="0">请选择</option>';
				if (msg.status==1)
				{
					$.each(msg.list, function(i, n){
						str += "<option value='"+n.id+"'>"+n.name+"</option>";
					});
				}
				$("#district").html(str);
			}
		});
	});


	$(".save_address").click(function(){

		//检查表单
		var receiver = $("#receiver").val();
		var mobile = $("#mobile").val();
		var address = $("#address").val();
		var province = $("#province").val();
		var city = $("#city").val();
		var district = $("#district").val();

		if(receiver == '')
		{
			show("请填写收货人");
			return false;
		}
		if(mobile == '')
		{
			show("请填写收货电话");
			return false;
		}
		
		var district = $("#district").val();

		if (district == '' || district == 0)
		{
			show("请选择省市区");
			return false;
		}
		
		if(address == '')
		{
			show("请填写收货地址");
			return false;
		}
		else if(address.length > 20)
		{
			show("字数不大于20个");
			return false;
		}
		
		$("#order_form").submit();
	});
});
{/literal}
</script>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>填写收货地址</h1>
  <a href="{$url_application}/user.php?do=ucenter&user_id={$user.user_id}" class="ui-btn ui-corner-all ui-icon-home ui-btn-icon-notext" data-ajax="false">首页</a>
  </div>
  

  <div data-role="content">
    <div class="blank"></div>
    <form id="order_form" method="post" action="order.php?do=saveaddress" data-ajax="false">
		
		<div class="blank"></div>
		<ul data-role="listview">
			<li><table width="100%">
				<tr><td>收货人信息：</td>
				<td>
					<input type="text" name="receiver" id="receiver" value="{$user.realname}" data-min="true" placeholder="默认会员信息">
						
					</td>
				</tr>
			<tr><td>收货电话：</td><td><input type="text" name="mobile" id="mobile" value="{$user.mobile}" data-min="true" placeholder="默认会员手机号"></td></tr>
			<tr><td>收货地址：</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" style="font-size: 13px;">
						<label for="province">选择省：</label>
						<select name="province" id="province">
						<option value="0">省</option>
						{if $province}
							{foreach from=$province item=item key=key}
							<option value="{$item.id}">{$item.name}</option>
						    {/foreach}
						{/if}
						</select>

						<label for="city">选择市：</label>
						<select name="city" id="city">
						  <option value="0">市</option>
						</select>
						<label for="district">选择区：</label>
						<select name="district" id="district">
						  <option value="0">区</option>
						</select>
					</fieldset>
				<input type="text" name="address" id="address" value="" data-min="true" placeholder="详细地址"></td></tr></table></li>
		</ul>
       <input type="hidden" id="user_id" name="user_id" value="{$user.user_id}"/>
       <input type="hidden" id="username" name="username" value="{$user.username}"/>
       <input type="hidden" id="order_id" name="order_id" value="{$order_id}"/>
       <input type="hidden" id="act" name="act" value="{$act}"/>
    </form>
  </div>
  
  <div data-role="footer" data-position="fixed">
    <div class="save_address" style="line-height:40px; float:left; width:100%; text-align:center; background-color:#FF7F00">保存</div>
  </div>
  
</div>

</body>

</html>
