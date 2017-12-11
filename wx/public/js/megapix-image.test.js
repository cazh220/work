window.onload = function() {
  var fileInput = document.getElementById('fileInput');
  fileInput.onchange = function() {
  	$("#id_canvas").css("display","");
  	$("#yu_picture").css("display","none");
    var file = fileInput.files[0];
    
    // MegaPixImage constructor accepts File/Blob object.
    var mpImg = new MegaPixImage(file);

    // Render resized image into image element using quality option.
    // Quality option is valid when rendering into image element.
    var resImg = document.getElementById('resultImage');
    //mpImg.render(resImg, { maxWidth: 300, maxHeight: 300, quality: 0.5 });
		console.log(resImg.src);
	mpImg.render(resImg, { maxWidth: 500, maxHeight: 500, quality: 0.5 }, function() {
		var base64 = getBase64Image(resImg);
		var base641 = resImg.src;
		
		//console.log("base64", base64.length, simpleSize(base64.length), base641.length, simpleSize(base641.length));
		//if (para.auto) core.uploadBase64str(base64);
		var formdata = new FormData();
		formdata.append("base64str", base64);
		var xhr = new XMLHttpRequest();

		xhr.open("post", 'picture.php?do=upload', true);
		xhr.send(formdata);
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4){
				if(xhr.status ==200){
					alert(xhr.responseText);
					//console.log(xhr.responseText);
					$("#company_pic").val(xhr.responseText);
					$("#yulan").css("display", "");
				}
		   }
		}
	});
	

    // Render resized image into canvas element.
    var resCanvas1 = document.getElementById('resultCanvas1');
    mpImg.render(resCanvas1, { maxWidth: 300, maxHeight: 300 });

    // Render resized image into canvas element, rotating orientation = 6 (90 deg rotate right)
    // Types of orientation is defined in EXIF specification.
    // To detect orientation of JPEG file in JS, you can use exif.js from https://github.com/jseidelin/exif-js
    //var resCanvas2 = document.getElementById('resultCanvas2');
    //mpImg.render(resCanvas2, { maxWidth: 300, maxHeight: 300, orientation: 6 });
  };
  
  
  function getBase64Image(img) {
		var canvas = document.createElement("canvas");
		canvas.width = img.width;
		canvas.height = img.height;

		var ctx = canvas.getContext("2d");
		ctx.drawImage(img, 0, 0, img.width, img.height);
		var dataURL = canvas.toDataURL("image/jpeg");
		alert(dataURL);
		return dataURL;

		// return dataURL.replace("data:image/png;base64,", "");
	}
  
  
  
};
