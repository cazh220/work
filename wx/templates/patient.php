<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>技工录入</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>
<script src="public/js/megapix-image.js"></script>
<script src="public/js/megapix-image.test.js"></script>
<style>
{literal}
.ui-input-text{ color:#000000;}
{/literal}
</style>
</head>
<body>
<div data-role="page" data-theme="p">
  <div data-role="header"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>技工录入</h1>
  </div>
  
  <div data-role="content">
    <form id="patient_form" method="post" action="patient.php?do=addpatient" data-ajax="false" enctype="multipart/form-data">
    	<input type="hidden" name="action" id="action" value="{$action}" />
    	<input type="hidden" id="sex_s" name="sex_s" value="{$sex}" />
    	<input type="hidden" id="false_tooth" name="false_tooth" value="{$false_tooth}" />
      <div data-role="fieldcontain" style="border-bottom-style: none;">
      	<div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">医疗机构：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="hospital" id="hospital" class="txt_s" placeholder="医疗机构" value="{$hospital}" {if $hospital}disabled="disabled"{/if}></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">医疗专家：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="doctor" id="doctor" class="txt_s" placeholder="医疗专家" value="{$doctor}" {if $doctor}disabled="disabled"{/if}></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">患者姓名：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="patient_name" id="patient_name" class="txt_s" placeholder="患者姓名" value="{$name}" {if $name}disabled="disabled"{/if}></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">患者性别：</div><div style="text-align: center; width: 70%; float: left;">
        	<fieldset data-role="controlgroup">
        			<label for="male">男性</label>
        				<input type="radio" name="gender" id="male" value="0" {if $sex == 0}checked="checked"{/if}/>
        		    <label for="female">女性</label>
        		    <input type="radio" name="gender" id="female" value="1" {if $sex == 1}checked="checked"{/if}/>
            </fieldset>
        </div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">患者年龄：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="patient_age" id="patient_age" placeholder="患者年龄" value="{$birthday}" {if $birthday}disabled="disabled"{/if}></div> 
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        	<tr>
        		<td width="30%" align="right">选择牙位：</td>
        		<td style="padding-right: 5px; border-bottom:1px solid #000; border-right:1px solid #000;"><input type="text" name="tooth_position1" id="tooth_position1" class="txt_s" placeholder="输入左上牙位" value="{$tooth_position1}" {if $tooth_position1 || $hospital}disabled="disabled"{/if}></td>
        		<td style="padding-left: 5px; border-bottom:1px solid #000; border-left:1px solid #000;"><input type="text" name="tooth_position2" id="tooth_position2" class="txt_s" placeholder="输入右上牙位" value="{$tooth_position2}" {if $tooth_position2 || $hospital}disabled="disabled"{/if}></td>
        	</tr>
        	<tr>
        		<td></td>
        		<td style="padding-right: 5px; border-top:1px solid #000; border-right:1px solid #000;"><input type="text" name="tooth_position3" id="tooth_position3" class="txt_s" placeholder="输入左下牙位" value="{$tooth_position3}" {if $tooth_position3 || $hospital}disabled="disabled"{/if}></td>
        		<td style="padding-left: 5px; border-top:1px solid #000; border-left:1px solid #000;"><input type="text" name="tooth_position4" id="tooth_position4" placeholder="输入右下牙位" class="txt_s" value="{$tooth_position4}" {if $tooth_position4 || $hospital}disabled="disabled"{/if}></td>
        	</tr>
        </table>
        
        <!--<div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">选择牙位：</div>
        <div style="background-image: url(templates/images/huge_logo.png);">
        <div style="height: 50px; text-align: center; width: 30%; float: left; line-height: 50px; margin-right:10px"><input type="text" name="tooth_position1" id="tooth_position1" placeholder="输入左上牙位" value="{$tooth_position1}"></div><div style="height: 50px; text-align: center; width: 30%; float: left; line-height: 50px; margin-left:10px"><input type="text" name="tooth_position2" id="tooth_position2" placeholder="输入右上牙位" value="{$tooth_position2}"></div>
        <div style="height: 50px; text-align: center; width: 30%; float: left; line-height: 50px; margin-left:100px; margin-right:10px"><input type="text" name="tooth_position3" id="tooth_position3" placeholder="输入左下牙位" value="{$tooth_position3}"></div><div style="height: 50px; text-align: center; width: 30%; float: left; line-height: 50px; margin-left:10px"><input type="text" name="tooth_position4" id="tooth_position4" placeholder="输入右下牙位" value="{$tooth_position4}"></div>
        </div>-->
        
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">制作单位：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="production_unit" class="txt_s" id="production_unit" placeholder="" value="{$user.company_name}" disabled="disabled"></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">录入人员：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="recorder" id="recorder" class="txt_s" value="{$user.realname}" disabled="disabled"></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">出库单位：</div><div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px"><input type="text" name="out_company" id="out_company" value="{$user.out_company}" class="txt_s" disabled="disabled"></div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 90px">修复体类别：</div><div style="text-align: center; width: 7570; float: left;">
        	<fieldset data-role="fieldcontain" style="min-width: 180px;">
		        <select name="repaire_types" id="repaire_type" disabled="disabled">
		         {foreach from=$repaire_type key=key item=item}
		         <option id="p1" value="{$item.false_tooth_id}" {if $false_tooth == $item.false_tooth_id}selected="selected"{/if}>{$item.false_tooth_name}</option>
		         {/foreach}
		        </select>
	      </fieldset>
        </div>
        
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 90px">
        	修复体明细：</div>
        <div style="text-align: center; float: left;">
        	<fieldset data-role="fieldcontain" style="min-width: 180px;">
		        <select name="product_detail" id="product_detail" {if $hospital}disabled="disabled"{/if}>
		         {foreach from=$product_detail key=key item=item}
		         <option id="p1" value="{$item.id}" {if $product_detail_id == $item.id}selected="selected"{/if}>{$item.product_detail}</option>
		         {/foreach}
		        </select>
	      </fieldset>
        </div>

				<div>
        <div style="height: 50px; text-align: right; width: 30%; float: left; line-height: 50px">修复体图片：</div>
        <div style="height: 50px; text-align: center; width: 70%; float: left; line-height: 50px">
        	<input type="file" name="repaire_pic" id="fileInput" value="{$url_upload}/public/uploads/{$repairosome_pic}" {if $repairosome_pic}disabled="disabled"{/if}>
        </div>
        </div>
        
      <div id="yulan" {if $repairosome_pic ==''}style="display: none;"{/if}>
      <div style="text-align: right; width: 40%; float: left; line-height: 150px">修复体图片预览：</div> 
      <div style="text-align: center; width: 60%; float: left; line-height: 150px">
				
				<span id="id_canvas" style="display: none;">
				<img id="resultImage" style="display:none">
				<canvas id="resultCanvas1" style="width: 100px; height: 100px;">
					
				</canvas>
				<input type="hidden"  id="company_pic" name="h_repaire_pic" value=""/><a href="javascript:remove()" style="color: red;">撤销</a>
				</span>
				
				<img src="{$url_upload}/public/uploads/{$repairosome_pic}" width="120px" height="100px" style="vertical-align: middle;" id="yu_picture">
				
      </div>
      </div> 
      
      {if $repairosome_pic !=''}
			<!--<div style="height: 150px; text-align: right; width: 40%; float: left; line-height: 150px">修复体图片预览：</div>
			<div style="height: 150px; text-align: center; width: 60%; float: left; line-height: 150px">
				<img src="http://www.yrsyc.cn/app/public/uploads/{$repairosome_pic}" width="120px" height="100px" style="vertical-align: middle;">
				<img id="resultImage" style="display:none">
				<canvas id="resultCanvas1"></canvas>
				<input type="hidden"  id="company_pic" name="h_repaire_pic" value=""/><a href="javascript:remove()" style="color: red;">撤销</a>
      </div>-->
			{/if}      
			{if $hospital}
      {if previlege != 1}<div style="text-align: right; width: 100%; float: left;"><a href="#" id="reset_s" data-role="button" onclick="doreset()">更改</a></div>
      <div style="text-align: center; width: 100%; float: left;"><a href="#" id="submit_s" style="display: none;" data-role="button" onclick="dodubmit()">提交</a></div>{/if}
      {else}
      <div style="text-align: center; width: 100%; float: left;"><a href="#" id="submit_s" data-role="button" onclick="dodubmit()">提交</a></div>
      {/if}
      <input type="hidden" id="user_id" name="user_id" value="{$user_id}" />
      <input type="hidden" id="qrcode" name="qrcode" value="{$qrcode}" />
      <input type="hidden" id="repaire_type" name="repaire_type" value="{$false_tooth}" />
    </form>
  </div>
</div>
<script type="text/javascript">
{literal}
$(function(){
	var sex = $("#sex_s").val();
	if(sex == 0)
	{
		$("#male").attr("checked", true);
		$("#female").attr("checked", false);
	}
	else
	{
		$("#male").attr("checked", false);
		$("#female").attr("checked", true);
	}
	
	var false_tooth = $("#false_tooth").val();
	$("#p"+false_tooth).attr("selected", true);
});

function remove()
{
	$("#company_pic").val("");
	$("#yulan").css("display", "none");
}

function show(note)
{
	//提示
  layer.open({
    content: note
    ,skin: 'msg'
    ,time: 2 //2秒后自动关闭
  });
}

function dodubmit()
{
	var hospital = $("#hospital").val();
	var doctor = $("#doctor").val();
	var patient_name = $("#patient_name").val();
	var patient_age = $("#patient_age").val();
	var tooth_position1 = $("#tooth_position1").val();
	var tooth_position2 = $("#tooth_position2").val();
	var tooth_position3 = $("#tooth_position3").val();
	var tooth_position4 = $("#tooth_position4").val();
	var sex = $("input[name=gender]:checked").val();
	var product_detail = $("#product_detail").val();
	var user_id = $("#user_id").val();
	var qrcode = $("#qrcode").val();
	var repaire_type = $("#repaire_type").val();
	var company_pic = $("#company_pic").val();//修复体图片
	
	var action = $("#action").val();
	var sex_s = $("#sex_s").val();
	var false_tooth = $("#false_tooth").val();
	
	//var param = user_id+':'+qrcode+':'+doctor+':'+patient_name+':'+patient_age+':'+tooth_position1+':'+tooth_position2+':'+tooth_position3+':'+tooth_position4+':'+sex+':'+product_detail+':'+repaire_type+':'+company_pic;
	//console.log(param);return false;
	if (hospital=="")
	{
	  show('请填写医疗机构');
		return false;
	}

	if (doctor == '')
	{
		show('请填写医疗专家');
		return false;
	}
	
	if (patient_name == '')
	{
		show('请填写患者姓名');
		return false;
	}
	
	if (patient_age == '')
	{
		show('请填写患者年龄');
		return false;
	}
	
	if (tooth_position1 == '' && tooth_position2 == '' && tooth_position3 == '' && tooth_position4 == '')
	{
		show('请填写牙位');
		return false;
	}
	else
	{
		var exp = /^[1-8]{1,8}$/;
		if(tooth_position1 && !exp.exec(tooth_position1))
		{
			show('左上牙位填写不符合要求');
			return false;
		}
		else if(tooth_position2 && !exp.exec(tooth_position2))
		{
			show('右上牙位填写不符合要求');
			return false;
		}
		else if(tooth_position3 && !exp.exec(tooth_position3))
		{
			show('左下牙位填写不符合要求');
			return false;
		}
		else if(tooth_position4 && !exp.exec(tooth_position4))
		{
			show('右下牙位填写不符合要求');
			return false;
		}
	}

	if(typeof(action) == 'undefined')
	{
		action = 0;
	}

	if(action==0)
	{
		var sex_s = sex;
		var req_url = 'patient.php?do=addnewpatient';
	}
	else
	{
		//var req_url = 'patient.php?do=updatepatient';
		var req_url = 'patient.php?do=addnewpatient';
		var sex_s = sex_s;
	}
	
	var out_company = $("#out_company").val();
	
	var param = user_id+':'+qrcode+':'+hospital+':'+doctor+':'+patient_name+':'+patient_age+':'+tooth_position1+':'+tooth_position2+':'+tooth_position3+':'+tooth_position4+':'+sex+':'+product_detail+':'+repaire_type+':'+company_pic+':'+action+':'+sex_s+':'+false_tooth+':'+out_company;
	
	$.ajax({
		url:req_url,
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

	
	/*
	var out_company = $("#out_company").val();
	if (out_company == '')
	{
		show('请填写出库单位');
		return false;
	}
	*/
	/*
	var tooth_position2 = $("#tooth_position2").val();
	if (tooth_position2 == '')
	{
		show('请填写右上牙位');
		return false;
	}
	
	var tooth_position3 = $("#tooth_position3").val();
	if (tooth_position3 == '')
	{
		show('请填写左下牙位');
		return false;
	}
	
	var tooth_position4 = $("#tooth_position4").val();
	if (tooth_position4 == '')
	{
		show('请填写右下牙位');
		return false;
	}*/
	
	/*
	var action = $("#action").val();
	var file = $("#repaire_pic").val();
	if (file == '' && action == 0)
	{
		show("请选择图片");
		return false;
	}
	*/
	//document.getElementById("patient_form").submit();
}

function doreset()
{
	$("#submit_s").css("display", "block");
	$("#reset_s").css("display", "none");
	$("#hospital").removeAttr("disabled");
	$("#hospital").parent().removeClass("ui-state-disabled");
	$("#doctor").removeAttr("disabled");
	$("#doctor").parent().removeClass("ui-state-disabled");
	$("#patient_name").removeAttr("disabled");
	$("#patient_name").parent().removeClass("ui-state-disabled");
	$("#patient_age").removeAttr("disabled");
	$("#patient_age").parent().removeClass("ui-state-disabled");
	$("#tooth_position1").removeAttr("disabled");
	$("#tooth_position1").parent().removeClass("ui-state-disabled");
	$("#tooth_position2").removeAttr("disabled");
	$("#tooth_position2").parent().removeClass("ui-state-disabled");
	$("#tooth_position3").removeAttr("disabled");
	$("#tooth_position3").parent().removeClass("ui-state-disabled");
	$("#tooth_position4").removeAttr("disabled");
	$("#tooth_position4").parent().removeClass("ui-state-disabled");
	$("#product_detail").removeAttr("disabled");
	$("#product_detail").parent().removeClass("ui-state-disabled");
	$("#repaire_pic").removeAttr("disabled");
	$("#repaire_pic").parent().removeClass("ui-state-disabled");
	$("#fileInput").removeAttr("disabled");
	$("#fileInput").parent().removeClass("ui-state-disabled");
	//document.getElementById("patient_form").reset();
}
{/literal}
</script>
</body>
</html>