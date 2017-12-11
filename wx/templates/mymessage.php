<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>我的信息</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>我的消息</h1>
  </div>

    <div data-role="content">	
			<ul data-role="listview" data-inset="true"> 
				{if $list}
					{foreach from=$list item=item key=key}
		      <li onclick="read({$item.id})"><a href="#"> 
		      	<p class="ui-li-aside" style="width:80%;">
		        	<table style="font-size: 12px; font-weight: lighter;">
		        		<tr>
		        			<td colspan="3"><b>Title：</b><span {if $item.is_read ==0}style="font-weight:bolder;"{/if}>医疗机构纠错提示</span>&nbsp;&nbsp;</td>
		        			<!--<td><b>From：</b>{$item.hospital} </td>-->
		        		</tr>
		        		<tr>
		        			<td width="50%"><b>Time：</b>{$item.send_time}&nbsp;&nbsp;</td>
		        			<td width="45%"><b>From：</b>{$item.hospital}&nbsp;&nbsp;</td>
		        			<td>{if $item.is_read ==1}<i class="fa fa-envelope-open"></i>{else}<i class="fa fa-envelope"></i>{/if}</td>
		        		</tr>
		        	</table>
		        	<!--<b>Title:</b>医疗机构纠错提示&nbsp;&nbsp;
		        	<b>From:</b>{$item.hospital} &nbsp;&nbsp;<br/>
		        	<b>Time：</b>{$item.send_time}  &nbsp;&nbsp;
		        	{if $item.is_read ==1}<i class="fa fa-envelope-open"></i>{else}<i class="fa fa-envelope"></i>{/if}-->
		        	</p> 
		        {if $item.type == 1}
		        <p>{$item.hospital}，{$item.realname}纠正了您于{$item.record_time}录入的{$item.name}的信息，<span id="link" class="link_style" onclick="link_u({$item.qrcode})">点击这里</span>更正此信息，即就可获得30积分。</p>
		        {/if}
		        
		       </a>
		      </li>
		      {/foreach}
		    {/if}
		    </ul>
  	</div>

</body>
<script type="text/javascript">
{literal}
function link_u(qrcode)
{
	window.location.href="user.php?do=patientin&user_id=25&qrcode="+qrcode;
}

function read(id)
{
	window.location.href="message.php?do=messagedetail&msg_id="+id;
}
{/literal}
</script>
</html>