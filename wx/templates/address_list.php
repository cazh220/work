<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>地址列表</title>
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



$(function(){
	$("#delete_addr").click(function(){
		var address = $("input[name=address]:checked").val();
		if(address == '')
		{
			show("请选择收货地址");
			return false;
		}
		
		$.ajax({
			type:"get",
			url:"area.php?do=deleteaddr",
			data:"address="+address,
			async:true,
			dataType:"json",
			success:function(msg){
				show(msg.message);
				//alert(msg.message);
				if(msg.status)
				{
					window.location.reload();
				}
				else
				{
					//$("#address"+address).parent(".ui-radio").css("display", "none");
				}
			}
		});
	});
	
	$("#select_address").click(function(){
		var address = $("input[name=address]:checked").val();
		if(address == '')
		{
			show("请选择收货地址");
			return false;
		}
		var user_id = $("#user_id").val();
		var act = $("#act").val();
		var order_id = $("#order_id").val();
		$.ajax({
			type:"get",
			url:"area.php?do=selectaddress",
			data:"address="+address+"&user_id="+user_id,
			async:true,
			dataType:"json",
			success:function(msg){
				
				//alert(msg.message);
				if(msg.status)
				{
					if(act == 'member')
					{
						window.location.href = 'user.php?do=member&user_id='+user_id;
					}
					else
					{
						//url = 'order.php?do=orderconfirm&id='+order_id+'&user_id='+user_id;
						window.location.href = 'order.php?do=orderconfirm&id='+order_id+'&user_id='+user_id;
					}
					
				}
				else
				{
					show(msg.message);
					//$("#address"+address).parent(".ui-radio").css("display", "none");
				}
			}
		});
		
	});
});
{/literal}
</script>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>地址列表</h1>
  <a href="{$url_application}/user.php?do=ucenter&user_id={$user_id}" class="ui-btn ui-corner-all ui-icon-home ui-btn-icon-notext" data-ajax="false">首页</a>
  </div>
  

  <div data-role="content">
    <div class="blank"></div>
    <form id="order_form" method="post" action="order.php?do=createorder" data-ajax="false">
		<ul data-role="listview">
			<li><table width="100%">
				<tr>
					<td>
						<fieldset data-role="controlgroup" >
							{if $address}
								{foreach from=$address item=item key=key}
									<label for="address{$item.address_id}">地址：{$item.province_name}{$item.city_name}{$item.district_name}{$item.address}<br>收货人：{$item.receiver}<br>电话：{$item.mobile}</label>
	        				<input type="radio" name="address" id="address{$item.address_id}" value="{$item.address_id}" {if $mine.address_id == $item.address_id || $key == 0}checked="checked"{/if}>
								{/foreach}
							{/if}
					    </fieldset>
					</td>
				</tr>
				</table>
			</li>
			
			<label class="right_show"><a data-ajax="false" href="order.php?do=addnewaddress&user_id={$user_id}&order_id={$order_id}&act={$act}" style="text-decoration:none;">新增地址</a>&nbsp;&nbsp;<a data-ajax="false" id="delete_addr" href="#" style="text-decoration:none;">删除地址</a></label>
		</ul>
       <input type="hidden" id="user_id" name="user_id" value="{$user_id}"/>
       <input type="hidden" id="act" name="act" value="{$act}"/>
       <input type="hidden" id="order_id" name="order_id" value="{$order_id}"/>
    </form>
    
  </div>
  
  
  <div data-role="footer" data-position="fixed">
    <div id="select_address" style="line-height:40px; float:left; width:100%; text-align:center;">选择</div>
  </div>
  
</div>

</body>

</html>
