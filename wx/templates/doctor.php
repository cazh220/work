<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>医生录入</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>
<script src="public/js/megapix-image.js"></script>
<script src="public/js/megapix-image.test.js"></script>
</head>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>医生录入</h1>
    <a href="http://www.yrsyc.cn/wx/user.php?do=ucenter&user_id={$user_id}" class="ui-btn ui-corner-all ui-icon-home ui-btn-icon-notext" data-ajax="false">首页</a>
  </div>

  <div data-role="content">
    <form method="post" id="doctor_form" action="doctor.php?do=confirm" enctype="multipart/form-data">
      <div data-role="fieldcontain" style="border-bottom-style: none;">
      	<div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">医疗机构：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="hospital" id="hospital" placeholder="" value="{$patient.hospital}" disabled="disabled"></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">医疗专家：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="doctor" id="doctor" placeholder="" value="{$patient.doctor}" disabled="disabled"></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">患者姓名：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="name" id="name" placeholder="" value="{$patient.name}" disabled="disabled"></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">患者性别：</div><div style="text-align: center; width: 70%; float: left;">
        	<fieldset data-role="controlgroup">
        			<label for="male">男性</label>
        		    <input type="radio" name="gender" id="male" value="male" {if $patient.sex == 0 || !$patient.sex}checked{/if} disabled="disabled">
        		    <label for="female">女性</label>
        		    <input type="radio" name="gender" id="female" value="female" {if $patient.sex == 1}checked{/if} disabled="disabled">	
            </fieldset>
        </div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">患者年龄：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="birthday" id="birthday" placeholder="" value="{$patient.birthday}" disabled="disabled"></div> 
        
        <!--<div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">选择牙位：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="tooth_position" id="tooth_position" placeholder="" value="{$patient.tooth_position}"></div>-->
        <!--<div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">选择牙位：</div><div style="height: 50px; text-align: center; width: 30%; float: left; line-height: 50px; margin-right:10px"><input type="text" name="tooth_position1" id="tooth_position1" placeholder="输入左上牙位" value="{$tooth_position1}" disabled="disabled"></div><div style="height: 50px; text-align: center; width: 30%; float: left; line-height: 50px; margin-left:10px"><input type="text" name="tooth_position2" id="tooth_position2" placeholder="输入右上牙位" value="{$tooth_position2}" disabled="disabled"></div>
        <div style="height: 50px; text-align: center; width: 30%; float: left; line-height: 50px; margin-left:100px; margin-right:10px"><input type="text" name="tooth_position3" id="tooth_position3" placeholder="输入左下牙位" value="{$tooth_position3}" disabled="disabled"></div><div style="height: 50px; text-align: center; width: 30%; float: left; line-height: 50px; margin-left:10px"><input type="text" name="tooth_position4" id="tooth_position4" placeholder="输入右下牙位" value="{$tooth_position4}" disabled="disabled"></div>
        -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        	<tr>
        		<td width="30%" align="right">选择牙位：</td>
        		<td style="padding-right: 5px; border-bottom:1px solid #000; border-right:1px solid #000;"><input type="text" name="tooth_position1" id="tooth_position1" placeholder="输入左上牙位" value="{$tooth_position1}" disabled="disabled"></td>
        		<td style="padding-left: 5px; border-bottom:1px solid #000; border-left:1px solid #000;"><input type="text" name="tooth_position2" id="tooth_position2" placeholder="输入右上牙位" value="{$tooth_position2}" disabled="disabled"></td>
        	</tr>
        	<tr>
        		<td></td>
        		<td style="padding-right: 5px; border-top:1px solid #000; border-right:1px solid #000;"><input type="text" name="tooth_position3" id="tooth_position3" placeholder="输入左下牙位" value="{$tooth_position3}" disabled="disabled"></td>
        		<td style="padding-left: 5px; border-top:1px solid #000; border-left:1px solid #000;"><input type="text" name="tooth_position4" id="tooth_position4" placeholder="输入右下牙位" value="{$tooth_position4}" disabled="disabled"></td>
        	</tr>
        </table>
        
        
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">客户单位：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="company_name" id="company_name" placeholder="关联客户单位" value="{if $company_name}{$company_name}{else}未关联客户{/if}" disabled="disabled"></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">制作单位：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="production_unit" id="production_unit" placeholder="" value="{$patient.production_unit}" disabled="disabled"></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">录入时间：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="create_time" id="create_time" placeholder="" value="{$patient.create_time}" disabled="disabled"></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">录入人员：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="operator" id="operator" value="{$patient.operator}" disabled="disabled"></div>
        
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 90px">修复体类别：</div><div style="text-align: center; width: 70%; float: left;">
        	<fieldset data-role="fieldcontain" style="min-width: 180px;">
		        <select name="type" id="type" disabled="disabled">
		         {foreach from=$repaire_type key=key item=item}
		         <option id="p1" value="{$item.false_tooth_id}" {if $false_tooth == $item.false_tooth_id}selected="selected"{/if}>{$item.false_tooth_name}</option>
		         {/foreach}	
		        </select>
	      </fieldset>
        </div>
        
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 90px">
        	修复体明细：</div>
        <div style="text-align: center; width: 7570; float: left;">
        	<fieldset data-role="fieldcontain" style="min-width: 180px;">
		        <select name="product_detail" id="product_detail" disabled="disabled">
		         {foreach from=$product_detail key=key item=item}
		         <option id="p1" value="{$item.id}" {if $product_detail_id == $item.id}selected="selected"{/if}>{$item.product_detail}</option>
		         {/foreach}
		        </select>
	      </fieldset>
        </div>
        <br/>
        
        <div style="height: 120px; text-align: right; width: 30%; float: left; line-height: 120px">修复体图片：</div><div style="height: 120px; text-align: center; width: 70%; float: left; line-height: 120px"><img src="http://www.yrsyc.cn/app/public/uploads/{$patient.repairosome_pic}" width="100px" height="100px"></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">案例图片：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px">
        	<input type="file" name="case_pic" id="fileInput" value="" disabled="disabled"></div>
		
      </div>
      
      
      
      <div id="yulan" {if $patient.case_pic ==''}style="display: none;"{/if}>
      <div style="text-align: right; width: 40%; float: left; line-height: 120px">案例图片预览：</div> 
      <div style="text-align: center; width: 60%; float: left; line-height: 120px">
				
				<span id="id_canvas" style="display: none;">
				<img id="resultImage" style="display:none">
				<canvas id="resultCanvas1" style="width: 100px; height: 100px;">
					
				</canvas>
				<input type="hidden"  id="company_pic" name="h_repaire_pic" value="{$patient.case_pic}"/><a href="javascript:remove()" style="color: red;">撤销</a>
				</span>
				
				<img src="http://www.yrsyc.cn/app/public/uploads/{$patient.case_pic}" width="120px" height="100px" style="vertical-align: middle;" id="yu_picture">
				
      </div>
      </div>
      
      
      
      <!--<div style="height: 120px; text-align: right; width: 40%; float: left; line-height: 120px">案例图片预览：</div>
      <div style="height: 120px; text-align: center; width: 60%; float: left; line-height: 120px">
      	{if $patient.case_pic}
      	<img src="http://www.yrsyc.cn/app/public/uploads/{$patient.case_pic}" width="100px" height="100px" style="vertical-align: middle;">
      	{/if}
      </div>-->
      
      
      <div style="text-align: right; width: 50%; float: left;"><a href="#" onclick="docommit()" style="display: none;" id="docommit" data-role="button">提交</a><a href="#" onclick="update_s()" id="update_s" data-role="button">修改</a></div><div style="text-align: center; width: 50%; float: left;"><a href="doctor.php?do=correction&qrcode={$qrcode}&user_id={$user_id}" data-role="button" data-ajax="false">纠错</a></div>
      <input type="hidden" id="repairosome_pic" name="repairosome_pic" value="{$patient.repairosome_pic}" />
      <input type="hidden" id="case_pic_yl" name="case_pic_yl" value="{$patient.case_pic}" />
      <input type="hidden" id="patient_id" name="patient_id" value="{$patient.patient_id}" />
      <input type="hidden" id="user_id" name="user_id" value="{$user_id}" />
    </form>
  </div>
</div>
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

function update_s()
{
	$("#fileInput").removeAttr("disabled");
	$("#fileInput").parent().removeClass("ui-state-disabled");
	$("#docommit").css("display","");
	$("#update_s").css("display","none");
}

function docommit()
{
	var repairosome_pic = $("#company_pic").val();//案例图谱
	var patient_id = $("#patient_id").val();//病患id
	var user_id	= $("#user_id").val();//医生id
	
	var param = repairosome_pic+':'+patient_id+':'+user_id;
	var url = 'doctor.php?do=confirm';
	$.ajax({
		url:url,
		method:'post',
		data:'param='+param,
		dataType:'json',
		success:function(msg){
			if(msg.status == 1)
			{
				show(msg.message);
				window.location.href=msg.url;
				//window.location.href='user.php?register=1';
			}
			else
			{
				show(msg.message);
				return false;
			}
		}
	});
	//document.getElementById("doctor_form").submit();
}

{/literal}
</script>
</body>
</html>