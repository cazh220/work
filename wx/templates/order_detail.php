<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>填写订单</title>
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

function plus(id, price)
{
	var num = $("#buy_num_"+id).html();
	num = parseInt(num);
	num++;
	$("#buy_num_"+id).html(num);
	$("#gift_num_"+id).val(num);

	credits = parseInt($("#need_credits").val());
	credits += parseFloat(price);
	
	$("#need_credits").val(credits);
}

function subplus(id, price)
{
	var num = $("#buy_num_"+id).html();
	num = parseInt(num);
	num--;
	if (num < 0)
	{
		num = 0;
	}
	$("#buy_num_"+id).html(num);
	$("#gift_num_"+id).val(num);
	var credits =parseInt( $("#need_credits").val());
	credits -= parseFloat(price);
	$("#need_credits").val(credits);
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

	$(".create_order").click(function(){
		//检查积分
		var left_credits = $("#left_credits").val();
		var need_credits = $("#need_credits").val();
		left_credits = parseInt(left_credits);
		need_credits = parseInt(need_credits);
		//console.log(left_credits);console.log(need_credits);return false;
		if(need_credits==0)
		{
			show("请选择数量");
			return false;
		}
		else if(left_credits < need_credits)
		{
			show("积分余额不足");
			return false;
		}

		//检查表单
		//var address = $("input[name=address]:checked").val();
		var address = $("#address_select").val();
		if(address == '')
		{
			show("请选择收货地址");
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
  <h1>填写订单</h1>
  <a href="{$url_application}/user.php?do=ucenter&user_id={$user.user_id}" class="ui-btn ui-corner-all ui-icon-home ui-btn-icon-notext" data-ajax="false">首页</a>
  </div>
  

  <div data-role="content">
    <div class="blank"></div>
    <form id="order_form" method="post" action="order.php?do=createorder" data-ajax="false">
		<ul data-role="listview">
			{if $list}
			{foreach from=$list item=item key=key}
			<li>
				<div class="list_product">
					<div class="list_pic"><img src="{$url_upload}/public/uploads/{$item.gift_photo}" class="pic_size"></div>
					<div class="attr">产品名称：{$item.gift_name}<br> 产品规格：{$item.gift_intro}<br> 兑换积分：{$item.credits}<br> 数量：<span class="buy_num_css" onclick="subplus({$item.gift_id}, {$item.credits})"><img src="templates/images/subplus.png" width="20px" height="20px" style="vertical-align: middle;"></span><span id="buy_num_{$item.gift_id}" class="buy_num" data-id="{$item.gift_id}">1</span><span class="buy_num_css" onclick="plus({$item.gift_id}, {$item.credits})"><img src="templates/images/plus.png" width="20px" height="20px" style="vertical-align: middle;"></span></div>
					<input type="hidden" id="gift_id_{$item.gift_id}" name="gift_id[]" class="h_gift_id" value="{$item.gift_id}"/>
					<input type="hidden" id="gift_num_{$item.gift_id}" name="gift_num[]" class="h_gift_num" value="1"/>
				</div>
			</li>
			{/foreach}
			{/if}
			
		</ul>
		
		<div class="blank"></div>
		<div class="blank"></div>
		<div class="blank"></div>
		<div class="blank"></div>
		<ul data-role="listview">
			<li><table width="100%">
				<tr><td width="15%" align="right">收货人:</td>
					<td width="80%">
					    <ul data-role="listview" data-inset="true" style="width: 100%;">
					      <li><a href="area.php?do=addresslist&user_id={$user.user_id}&act=order&order_id={$order_id}" style="font-size: 12px; line-height: 20px;">
					      	地址：{$address_select.province_name}{$address_select.city_name}{$address_select.district_name}{$address_select.address}
					      	<br/>
					      	收货人：{$address_select.receiver}
					      	<br/>
					      	电话：{$address_select.mobile}
					      </a></li>
				    	</ul>
					</td>
				</tr>
				</table>
		</li>
		</ul>
       <input type="hidden" id="left_credits" name="left_credits" value="{$left_credits}"/>
       <input type="hidden" id="need_credits" name="need_credits" value="{$need_credits}"/>
       <input type="hidden" id="user_id" name="user_id" value="{$user.user_id}"/>
       <input type="hidden" id="username" name="username" value="{$user.username}"/>
       <input type="hidden" id="address_select" name="address_select" value="{$address_select.address_id}"/>
    </form>
    
  </div>
  
  
  <div data-role="footer" data-position="fixed">
    <div class="create_order" style="line-height:40px; float:left; width:100%; text-align:center; background-color:#FF7F00">立即兑换</div>
  </div>
  
</div>

</body>

</html>
