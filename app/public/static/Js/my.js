//js
$(function(){
	//省市联动
	$("#province").change(function(){
		var pid = $(this).val();
		$.ajax({
			url:'http://dev220.duapp.com/app/public/index.php/index/region/get_region',
			type:'get',
			data:'id='+pid,
			dataType:'json',
			success:function(msg){
				var txt='<option value="">请选择</option>';
				$.each(msg, function(i, n){
					txt += '<option value="'+n.id+'">'+n.name+'</option>';
				});
				$("#city").html(txt);
			}
		});
	});
	
	//市县联动
	$("#city").change(function(){
		var pid = $(this).val();
		$.ajax({
			url:'http://dev220.duapp.com/app/public/index.php/index/region/get_region',
			type:'get',
			data:'id='+pid,
			dataType:'json',
			success:function(msg){
				var txt='';
				$.each(msg, function(i, n){
					txt += '<option value="'+n.id+'">'+n.name+'</option>';
				});
				$("#district").html(txt);
			}
		});
	});
	
	
});
