<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>录入查询</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<style>
{literal}
.ui-btn{
	padding-top: 7px;
	padding-bottom: 7px;
}
{/literal}
</style>
</head>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>录入查询</h1>
  </div>

  <div data-role="content">
    <form method="post" action="member.php?user_id={$user_id}" data-ajax="false">
		<!--<input type="search" name="search" id="search" placeholder="诊所姓名/模糊查询" value="{$serach}">-->
		<table>
			<tr>
				<td width="12%">时间:</td>
				<td>
					<input  type="date" name="date" id="date" value="{$date}" placeholder="" width="30%">
				</td>
				{if $user_type==1}
				<td width="12%">患者:</td>
				<td><input type="text" name="name" value="{$name}" width="30%"/>	</td>
				{else}
				<td width="12%">医院:</td>
				<td><input type="text" name="hospital" value="{$hospital}" width="30%"/>	</td>
				{/if}
				
				<td><input type="submit" name="subm"  value="搜索" width=""/></td>
			</tr>
		</table>
		<input type="hidden" id="act" name="act" value="1" />
		<!--<input type="hidden" id="user_id" name="user_id" value="{$user_id}" />-->
    </form>
  </div>
  
  <table width="100%">
	<tr class="member_table">
		<th width="15%" style="text-align:center; font-size: 12px;">防伪码</th>
		<th width="35%" style="text-align:center; font-size: 12px;">医院</th>
		<th width="15%" style="text-align:center; font-size: 12px;">医生</th>
		<th width="15%" style="text-align:center; font-size: 12px;">患者</th>
		<th width="20%" style="text-align:center; font-size: 12px;">录入日期</th>
	</tr>
	{if $list}
	{foreach from=$list item=item key=key}
	<tr class="member_content_table" style="font-size: 11px;">
		<td>{$item.security_code_hidden}</td>
		<td>{$item.hospital}</td>
		<td>{$item.doctor}</td>
		<td>{$item.name}</td>
		<td>{$item.create_time}</td>
	</tr>
	{/foreach}
	{/if}
  </table>

  	<div style="height:35px; border-top: solid 1px;">
	
		   <div class='page' style="text-align: center; margin-top:20px; font-size: small;">
		   	 总{$page_info.totalNum}个记录 分 {$page_info.pageCount}页  &nbsp;&nbsp;  当前第 {$page_info.currentPage}页
		        <a href="./member.php?{$query_link}&page=1">
		        	<i id="left_stop" class="fa fa-step-backward" style=""></i>
		        </a>&nbsp;&nbsp;
		        <a href="./member.php?{$query_link}&page={$page_info.prevPage}">
		        	<i id="left_stop" class="fa fa-backward" style=""></i>
		        </a>&nbsp;&nbsp;
		        <a href="./member.php?{$query_link}&page={$page_info.nextPage}">
		        	<i id="left_stop" class="fa fa-forward" style=""></i>
		        </a>&nbsp;&nbsp;
		        <a href="./member.php?{$query_link}&page={$page_info.lastPage}">
		        	<i id="left_stop" class="fa fa-step-forward" style=""></i>
		        </a>
		        
		        
		        <input type="hidden" name="query_string" value="{$query_link}" />
		   </div>
		</div>
	
</div>

</body>
</html>