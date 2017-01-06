(function(){
    var d = art.dialog.defaults;
    d.skin = ['aero'];
    d.drag = true;
    d.showTemp = 100000;
})();



function loading(){
	$('#Form').append('<input type="hidden" name="ajax" id="ajax" value="1">');
	$('#tips').html("<div class='notification information png_bg'><div>正在提交您的请求，请稍等……</div></div>");
}
function Aform(form){
    $(function(){
        $(form).ajaxForm({
            beforeSubmit:  checkForm,  // pre-submit callback
            success:       complete,  // post-submit callback
            dataType: 'json'
        });
    function checkForm(){
	loading();
    }
    function complete(data){
	tip_warn(data);
    }
});
}

function Bform(form){
    $(function(){
        $(form).ajaxForm({
            beforeSubmit:  checkForm,  // pre-submit callback
            success:       complete,  // post-submit callback
            dataType: 'json'
        });
    function checkForm(){
		art.dialog.tips("正在通信，请稍后……",3);
    }
    function complete(data){
		art.dialog.alert(data.info);
    }
});
}


function tip_warn(data){
	$('#tips').html("<div class='notification information png_bg'><div>"+data.info+"</div></div>");
}

function tip_warn2(data){
	art.dialog.tips(data.info,3);
}


function setCookie (name,value) {
document.cookie = name + "=" + value + "; path=/;expires=Thursday,01-Jan-2099 00:00:00 GMT";
}
function getCookie(name) {
var search;
search = name + "=";
offset = document.cookie.indexOf(search);
if (offset != -1) {
offset += search.length ;
end = document.cookie.indexOf(";", offset) ;
if (end == -1)
end = document.cookie.length;
return unescape(document.cookie.substring(offset, end));
}
else
return "";
}

function del(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}
	if (window.confirm('确实要删除选择项吗？'))
	{
		self.location=URL+"?act=del&id="+keyValue;
	}
}
function edit_state(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要智能审核的项！');
		return false;
	}
	if (window.confirm('确实要智能审核选择项吗？'))
	{
		self.location=URL+"?act=state&id="+keyValue;
	}
}
function type_edit(id,cid){
	art.dialog.load('Type_edit?id='+id+'&cid='+cid,{id:'type_edit',title:false,lock:true,skin:'default'});
}

function admin_edit(id){
	art.dialog.load('admin_edit?id='+id+'&cid=',{id:'admin_edit',title:false,lock:true,skin:'default'});
}

function classad_edit(id){
	art.dialog.load('classad_edit?id='+id+'&cid=',{id:'pop',title:false,lock:true,skin:'default'});
}
function page_edit(id){
	art.dialog.load('page_edit?id='+id+'&cid=',{id:'pop',title:false,lock:true,skin:'default'});
}
function ad_edit(id){
	art.dialog.load('ad_edit?id='+id+'&cid=',{id:'pop',title:false,lock:true,skin:'default'});
}
function open_pop(url){
	art.dialog.load(url,{id:'pop',title:false,lock:true,skin:'default'});
}
function edit_pop(action,id){
	art.dialog.load(action+'_edit?id='+id,{id:'pop',title:false,lock:true,skin:'default'});
}
function getSelectCheckboxValue(){
	var obj = document.getElementsByName('id');
	var result ='';
	for (var i=0;i<obj.length;i++)
	{
		if (obj[i].checked==true)
				return obj[i].value;

	}
	return false;
}

function getSelectCheckboxValues(){
	var obj = document.getElementsByName('id');
	var result ='';
	var j= 0;
	for (var i=0;i<obj.length;i++)
	{
		if (obj[i].checked==true){
				//selectRowIndex[j] = i+1;
				result += obj[i].value+",";
				j++;
		}
	}
	return result.substring(0, result.length-1);
}


