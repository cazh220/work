/*
 函数名称：wxJSSDK.scanQRCode
 函数功能：为wxJSSDK增加智能操作服务
 参数：
 scanQRCodeApi 扫一扫API Object 配置
 */

wxJSSDK.scanQRCode = function(codeApi){
    if(wxJSSDK.isReady){//wxJSSDK.isReady 查看微信JSSDK是否初始化完毕
        if(codeApi){
            codeApi.scanQRCode && wx.scanQRCode({
                needResult: codeApi.scanQRCode.needResult, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: codeApi.scanQRCode.scanType || ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                    codeApi.scanQRCode.success && codeApi.scanQRCode.success(res);
                }
            });
        }else{
            console.log("缺少配置参数");
        }
    }else{
        console.log("抱歉，wx没有初始化完毕，请等待wx初始化完毕，再调用扫一扫接口服务。");
    }

}

window.onload = function(){
    /*音频*/
    $("#scanQRCode").click(function(){//开始录音
    	var url = $("#scanQRCode").data("id");
        wxJSSDK.scanQRCode({
            scanQRCode:{
                needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                    var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
                    var param;
                    param = parseQueryString(result);
                    if (res.errMsg === "scanQRCode:ok"){
			            //{"resultStr":"", "errMsg":"scanQRCode:ok"}
			            /*
			            if(/^[https?:\/\/|tel:|mailto:]/i.test(result)) {
			                location.href = result;
			                return;
			            }*/
			           location.href = url+param;
			            // 二维码ajax验票 
			            // checkPost(data);
			        }
                }
            }
        });
    });
}

//解析URL  获得防伪码
function parseQueryString(url){
    var obj = {};
    var start = url.indexOf("?")+1;
    var str = url.substr(start);
    var arr = str.split("&");
    for(var i = 0 ;i < arr.length;i++){
          var arr2 = arr[i].split("=");
           obj[arr2[0]] = arr2[1];
    }
	return obj.security_code;
}


