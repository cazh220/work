<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
<title>商品详情</title>
<link rel="stylesheet" href="public/mobile_themes/themes/skyd.min.css" />
<link rel="stylesheet" href="public/mobile_themes/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
<link rel="stylesheet" href="templates/css/style.css">
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="public/layer_mobile/layer.js"></script>
<script src="plugins/js/imagesloaded.pkgd.min.js"></script>
<script src="plugins/js/jquery.hslider.js"></script>
<script type="text/javascript">       
	$( document ).ready(function() { 
		$( ".hsldr-container" ).hslider({
		  navBar: true,
		  auto: true,
		  delay: 2000
		});
		
		$("#hear li").click(function(){
			$(this).css({
				borderBottom: "2px solid red",
				height:"43px"
			}).siblings().css({
				borderBottom: "none",
				height:"45px"
			});
		});					
			
		$("#hear li").click(function(){
			$(this).addClass("action").siblings().removeClass("action");
			var index = $(this).index();
			$("#contentop li").eq(index).css("display","block").siblings().css("display","none");
		});
		
	});	
</script> 
</head>
<body>

<div data-role="page" data-theme="p">
  <div data-role="header" data-position="fixed"><a href="#" class="ui-btn ui-corner-all ui-icon-carat-l ui-btn-icon-notext" data-rel="back">返回</a>
  <h1>商品详情</h1>
  </div>
  
	<div class="hsldr-container">
		<figure>
			<img src="plugins/images/wider.jpg" />
			<figcaption>Car in the snow</figcaption>
		</figure>
		<figure>
			<img src="plugins/images/f5bd8360.jpg" />
			<figcaption>People surfing</figcaption>
		</figure>
		<figure>
			<img src="plugins/images/photo-1415769663272-8504c6cc02b3.jpg" />
			<figcaption>Girl with the balloon</figcaption>
		</figure>
		<figure>
			<img src="plugins/images/photo-1418662589339-364ad47f98a2.jpg" />
			<figcaption>Ice surfing</figcaption>
		</figure>
		<figure>
			<img src="plugins/images/photo-1423483786645-576de98dcbed.jpg" />
			<figcaption>Golden hair</figcaption>
		</figure>
		<figure>
			<img src="plugins/images/photo-1424470535838-79a00dc41aa5.jpg" />
			<figcaption>Antartica</figcaption>
		</figure>
		<figure>
			<img src="plugins/images/photo-1428069940893-209d71f133cf.jpg" />
			<figcaption>Mofler</figcaption>
		</figure>
		<figure>
			<img src="plugins/images/photo-1430834447668-d44a17fc36fe.jpg" />
			<figcaption>The hard worker</figcaption>
		</figure>
		<figure>
			<img src="plugins/images/photo-1446902236611-65b30daefc2a.jpg" />
			<figcaption>Winter lamps</figcaption>
		</figure>
		<figure>
			<img src="plugins/images/photo-1428471226620-c2698eadf413.jpg" />
			<figcaption>Winter lamps</figcaption>
		</figure>				
		<figure>
			<img src="plugins/images/wider2.jpg" />
			<figcaption>Winter lamps</figcaption>
		</figure>

	</div>
	
	<div data-role="content">
		<form method="post" action="demoform.asp">
			<hr>
			<div class="attribute">产品名称：考拉</div>
			<hr>
			<div class="attribute">参数规格：100cm</div>
			<hr>
			<div class="attribute">积分：10000</div>
			
			
			<div data-role = "content-floud">			
			<div style="font-family: '微软雅黑';">
				<ul id="hear">
					<li class="action" style="border-bottom: 2px solid red;height: 43px;"><a href="#">未使用<span style="color: red;">(0)</span></a></li>
					<li><a href="#" >已使用<span style="color: red;">(8)</span></a></li>
					<li><a href="#" >已过期<span style="color: red;">(2)</span></a></li>
				</ul>
				<ul id="contentop">
					<li class="action">
                        <div class="alo">
                        	<div class="ui-grid-a sty3" >
                        		<div class="ui-block-a">优惠券</div>
                        		<div class="ui-block-b">仅可购买部分商品</div>
                        	</div>
                        	<div class="ui-grid-a sty2" >
                        		<div class="ui-block-a "><span>￥50.00</span></div>
                        		<div class="ui-block-b"></div>
                        	</div>
                        	<div class="cllio"></div>
                        	<div class="ui-grid-a Buy" >
                        		<div class="ui-block-a">兑换码：0001</div>
                        		<div class="ui-block-b">2016.02.01-2016.08.25</div>
                        	</div>
                        </div>
                        <div class="alo">
                        	<div class="ui-grid-a sty3" >
                        		<div class="ui-block-a">优惠券</div>
                        		<div class="ui-block-b">仅可购买部分商品</div>
                        	</div>
                        	<div class="ui-grid-a sty2" >
                        		<div class="ui-block-a "><span>￥30.00</span></div>
                        		<div class="ui-block-b">满99元可用</div>
                        	</div>
                        	<div class="cllio"></div>
                        	<p class="sty5">2016.02.01-2016.08.25&nbsp;&nbsp;&nbsp;&nbsp;</p>
                        </div>
					</li>
					<li>
                       <div class="alo">
                        	<div class="ui-grid-a sty3" >
                        		<div class="ui-block-a">优惠券</div>
                        		<div class="ui-block-b">仅可购买部分商品</div>
                        	</div>
                        	<div class="ui-grid-a sty2" >
                        		<div class="ui-block-a "><span>￥50.00</span></div>
                        		<div class="ui-block-b"></div>
                        	</div>
                        	<div class="cllio"></div>
                        	<p class="sty5">2016.02.01-2016.08.25&nbsp;&nbsp;&nbsp;&nbsp;</p>
                        </div>
					</li>
					<li>
						<div class="usl">
                        	<div class="ui-grid-a sty3" >
                        		<div class="ui-block-a">优惠券</div>
                        		<div class="ui-block-b">仅可购买部分商品</div>
                        	</div>
                        	<div class="ui-grid-a sty2" >
                        		<div class="ui-block-a "><span>￥50.00</span></div>
                        		<div class="ui-block-b"></div>
                        	</div>
                        	<div class="cllio"><!--<img src="../images/2001.png"/>--></div>
                        	<p class="sty5">2016.02.01-2016.08.25&nbsp;&nbsp;&nbsp;&nbsp;</p>
                       </div>
					</li>
				</ul>
			</div>			
			</div>
			
		</form>
	</div>
	
	<div data-role="footer" data-position="fixed" style="overflow:hidden;">
		<div style="line-height:40px; font-size:12px; width:60%; float:left;">可用积分：9999  兑换所需积分：1000</div><div style="line-height:40px; float:left; width:40%; text-align:center; background-color:#FF7F00">立即兑换</div>
	</div>

</div>

</body>
</html>