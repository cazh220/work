<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>分享</title>
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
.to{
	width: 100px;
	height: 100px;
	/*background-image: url('images/kl.jpg');*/
	background-size: cover;
	display: block;
	border-radius: 50px;
	-webkit-border-radius:50px;
	-moz-border-radius:50px;
	margin:0 auto;
}
.user_account{
	text-align:left;
	/*padding:10px 0px;*/
	line-height: 30px;
	margin-top: 10px;
	font-size: 14px;
}
.base_info{
	padding:30px 0px;
}
{/literal}
</style>
<body>

<div data-role="page"  data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">后退</a>
  <h1>沪鸽口腔防伪验证中心</h1>
  <!--<a href="#popupMenu" data-rel="popup" data-transition="slideup" data-icon="gear">选项</a>
  <div data-role="popup" id="popupMenu" data-theme="b">
	<ul data-role="listview" data-inset="true" style="min-width:20px">
		<li data-role="list-divider">请选择</li>
		<li><a href="#" onclick="share()">微信</a></li>
		<li><a href="#">微博</a></li>
	</ul>
  </div>-->
  </div>
  

  <div data-role="content">
    <ul data-role="listview">
		<li>
			{if $wx_user.wx_headimgurl}<div class="to" style="background-image: url({$wx_user.wx_headimgurl})"></div>{else}<div class="to" style="background-image: url('images/default_head.png')"></div>{/if}
			<!--<div class="user_account">使用人：{$patient.name}</div>-->
			<div class="user_account" style="padding-bottom: 10px;">
				<table>
					<tr>
						<td width="20%">使用人：</td>
						<td>{$patient.name}</td>
					</tr>
					<tr>
						<td width="20%">产&nbsp;&nbsp;&nbsp;品：</td>
						<td>{$patient.false_tooth_name}</td>
					</tr>
					<tr>
						<td width="20%"></td>
						<td>{$product_detail}</td>
					</tr>
				</table>
				<!--产品：{$patient.false_tooth_name}&nbsp;&nbsp;{$product_detail}-->
			</div>
		</li>
		
	</ul>
	<ul data-role="listview">
		<li style="height: auto;white-space: normal;">
			{if $res_repaire.false_tooth_id == 1}
			<div style="font-size: 16px; line-height: 22px;">经验证：该产品符合：<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$patient.false_tooth_name}正品验证流程！</div>   
			<div style="font-size: 14px; line-height: 22px; padding-bottom: 10px;">二氧化锆全瓷牙是国际流行的、目前世界最好的牙齿美容修复技术，拥有一口整洁漂亮的牙齿是高品质生活的表现之一。<br>美晶瓷氧化锆抗弯强度高达1200MPA、含有一定量的钇稳定剂，使得全瓷牙更坚固耐用；<br>美晶瓷氧化锆采用全球高端锆粉，提纯度更高、不含铅等，消除重金属对人体的危害、安全性更高；<br>美晶瓷全瓷牙做医疗影像时无需拆除，一劳永逸。</div>
			{else}
			<div style="font-size: 16px; line-height: 22px;">经验证，该产品符合：<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;美国诺必灵优质合金正品验证流程</div>   
			<div style="font-size: 14px; line-height: 22px; padding-bottom: 10px;">诺必灵生产企业CMP创始于1889年，第一任总裁是世界闻名的发明家乔治.西屋，美国第32任总统法兰克福.罗斯福的父亲是股东之一。诺必灵面向美国牙医和牙科技工所发行了近600期TIC杂志，记录了牙科行业的技术发展历程。<br>诺必灵优质合金因其纯度高、佩戴轻盈舒适、无异物感的特性，连续荣获美国牙科协会的五星评价，是美国牙医和牙科技工所的主要选择！</div>
			{/if}
		</li>
		
	</ul>
	
	{if $patient.confirm == 1}
	<ul data-role="listview">
		<li>
			<div><h4>医疗机构：{$patient.hospital}</h4></div>
			<div class="content" style="font-size: 14px; white-space: normal; line-height: 20px;">{$patient.doc.company_info}</div>
			<div style="text-align: center; padding-bottom: 30px;"><img src="http://www.yrsyc.cn/app/public/uploads/{$patient_pic}" width="100%" height="100%"></div>
		</li>
		
	</ul>
	{/if}
	<ul data-role="listview">
		<li>
			<div><h4>加工单位：{$patient.tech.company_name}</h4></div>
			<div class="content" style="font-size: 14px; white-space: normal; line-height: 20px;">{$patient.tech.company_info}</div>
			<div style="text-align: center; padding-bottom: 30px;"><img src="http://www.yrsyc.cn/app/public/uploads/{$patient.tech.head_img}" width="100%" height="100%"></div>
		</li>
		
	</ul>
	
  </div>
  <input type="hidden" id="appId" value="{$sign.appId}"/>
  <input type="hidden" id="timestamp" value="{$sign.timestamp}"/>
  <input type="hidden" id="nonceStr" value="{$sign.nonceStr}"/>
  <input type="hidden" id="signature" value="{$sign.signature}"/>
  <input type="hidden" id="security_code" value="{$security_code}"/>
  <input type="hidden" id="head_img" value="{$head_img}"/>
  <input type="hidden" id="hospital" value="{$patient.hospital}"/>
</div>
</body>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
{literal}
  // 注意：所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。 
  // 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
  // 完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
  var a = $("#appId").val();
  var b = $("#timestamp").val();
  var c = $("#nonceStr").val();
  var d = $("#signature").val();
  var e = $("#head_img").val();
  var f = $("#hospital").val();
  var security_code = $("#security_code").val();
  var urlen = encodeURIComponent("http://www.yrsyc.cn/wx/index.php?do=share&security_code="+security_code);
  var links = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa02a6a965b89a9c0&redirect_uri="+urlen+"&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
  var headimgurl_encode = encodeURIComponent(e);
  var data;
  data = {debug:false, appId:a, timestamp:b, nonceStr:c, signature:d, scode:security_code, linka:links, head_img:e, hospital:f, headimgurl:headimgurl_encode, jsApiList:[
      // 所有要调用的 API 都要加到这个列表中
      'checkJsApi',
      'onMenuShareTimeline',
      'onMenuShareAppMessage',
      'onMenuShareQQ',
      'onMenuShareWeibo',
      'onMenuShareQZone',
      'chooseImage'
    ]};
  wx.config(data);
  //console.log(data);
  /*
  wx.config({
    debug: false,
    appId: $("#appId").val(),
    timestamp: b,
    nonceStr: c,
    signature: d,
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      'checkJsApi',
      'onMenuShareTimeline',
      'onMenuShareAppMessage',
      'onMenuShareQQ',
      'onMenuShareWeibo',
      'onMenuShareQZone',
      'chooseImage'
    ]
  });*/
  wx.ready(function () {
    // 在这里调用 API
    wx.checkJsApi({
		    jsApiList: ['chooseImage'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
		    success: function(res) {
						//console.log(res);
		        // 以键值对的形式返回，可用的api值true，不可用为false
		
		        // 如：{"checkResult":{"chooseImage":true},"errMsg":"checkJsApi:ok"}
		
		    }
		});
		
		//分享到朋友圈
		wx.onMenuShareTimeline({
		    title: '沪鸽口腔高品质产品防伪验证中心', // 分享标题
		    desc:'我在'+data.hospital+'制作的产品经验证为正品，使用更放心啦，推荐给您…..',
		    link:'http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code='+data.scode+'&headimgurl='+data.headimgurl, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
		    imgUrl: data.head_img, // 分享图标
		    success: function () { 
		        // 用户确认分享后执行的回调函数
		        //alert('分享成功');
		        $.ajax({
		        	type:"get",
		        	url:"index.php?do=updatecredits",
		        	data:"",
		        	datatType:"json",
		        	success:function(msg){
		        		
		        	}
		        });
		    },
		    cancel: function () { 
		        // 用户取消分享后执行的回调函数
		        //alert('分享取消');
		    }
		});
		//分享微信好友
		wx.onMenuShareAppMessage({
		    title: '沪鸽口腔高品质产品防伪验证中心', // 分享标题
		    desc: '我在'+data.hospital+'制作的产品经验证为正品，使用更放心啦，推荐给您…..', // 分享描述
		    link: 'http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code='+data.scode+'&headimgurl='+data.headimgurl, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
		    imgUrl: data.head_img, // 分享图标
		    type: '', // 分享类型,music、video或link，不填默认为link
		    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
		    success: function () { 
		        // 用户确认分享后执行的回调函数
		    },
		    cancel: function () { 
		        // 用户取消分享后执行的回调函数
		    }
		});
		//分享到qq
		wx.onMenuShareQQ({
		    title: '沪鸽口腔高品质产品防伪验证中心', // 分享标题
		    desc: '我在'+data.hospital+'制作的产品经验证为正品，使用更放心啦，推荐给您…..', // 分享描述
		    link: 'http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code='+data.scode+'&headimgurl='+data.headimgurl,// 分享链接
		    imgUrl: data.head_img, // 分享图标
		    success: function () { 
		       // 用户确认分享后执行的回调函数
		    },
		    cancel: function () { 
		       // 用户取消分享后执行的回调函数
		    }
		});
		//分享到腾讯微博
		wx.onMenuShareWeibo({
		    title: '沪鸽口腔高品质产品防伪验证中心', // 分享标题	
		    desc: '我在'+data.hospital+'制作的产品经验证为正品，使用更放心啦，推荐给您…..', // 分享描述
		    link: 'http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code='+data.scode+'&headimgurl='+data.headimgurl,// 分享链接
		    imgUrl: data.head_img, // 分享图标
		    success: function () { 
		       // 用户确认分享后执行的回调函数
		
		    },
		    cancel: function () { 
		        // 用户取消分享后执行的回调函数
		    }
		});
		//分享到QQ空间
		wx.onMenuShareQZone({
		    title: '沪鸽口腔高品质产品防伪验证中心', // 分享标题
		    desc: '我在'+data.hospital+'制作的产品经验证为正品，使用更放心啦，推荐给您…..', // 分享描述
		    link: 'http://www.yrsyc.cn/wx/index.php?do=middlepage&security_code='+data.scode+'&headimgurl='+data.headimgurl,// 分享链接
		    imgUrl: data.head_img, // 分享图标
		    success: function () { 
		       // 用户确认分享后执行的回调函数
		    },
		    cancel: function () { 
		        // 用户取消分享后执行的回调函数
		    }
		});
		
		
		
  });
  
  wx.error(function (res) {
	 	alert(res.errMsg);  //打印错误消息。及把 debug:false,设置为debug:ture就可以直接在网页上看到弹出的错误提示
	});
	
	function select_photo()
	{
		//选图
		wx.chooseImage({
		    count: 1, // 默认9
		    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
		    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
		    success: function (res) {
		        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
		        alert(localIds);
		    }
		});
	}
{/literal}
</script>
</html>
