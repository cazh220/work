//自定义js
$(function(){

});

function show(val)
{
	if(val == 1)
	{
		$("#role_price").css("display","");
	}
	else
	{
		$("#role_price").css("display","none");
	}
}

//客户
function add_role_same(name)
{
	//先保存数据
	$("div").data("role_name",name);
	$("input[name=role_category_name]").val('');
}

function add_role_child(name)
{
	//先保存数据
	$("div").data("role_name",name);
	$("input[name=role_category_name]").val('');
}

function edit_role_cat()
{
	//先保存数据
	var name = $("div").data("role_name");
	$("input[name=role_category_name]").val(name);
}

//客户分类删除
function delete_role_cat()
{
	//先保存数据
	var name = $("div").data("role_name");
	$("input[name=role_category_name]").val(name);
}


//产品分类
function add_same(name,description)
{
	//先保存数据
	$("div").data("name",name);
	//$("div").data("url","http://www.me.com");
	$("div").data("desc",description);
	$("input[name=category_name]").val('');
	$("input[name=category_link]").val('');
	$("input[name=category_desc]").val('');
}

function add_child(name,description)
{
	//先保存数据
	$("div").data("name",name);
	//$("div").data("url","http://www.me.com");
	$("div").data("desc",description);
	$("input[name=category_name]").val('');
	$("input[name=category_link]").val('');
	$("input[name=category_desc]").val('');
}

function edit_cat()
{
	//先保存数据
	var name = $("div").data("name");
	//var urls = $("div").data("url");
	var desc = $("div").data("desc");

	$("input[name=category_name]").val(name);
	//$("input[name=category_link]").val(urls);
	$("input[name=category_desc]").val(desc);
}


function delete_cat()
{
	//先保存数据
	var name = $("div").data("name");
	//var urls = $("div").data("url");
	var desc = $("div").data("desc");

	$("input[name=category_name]").val(name);
	//$("input[name=category_link]").val(urls);
	$("input[name=category_desc]").val(desc);
}