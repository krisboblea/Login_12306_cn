(function(){
    var d = art.dialog.defaults;
    d.skin = ['chrome'];
    d.drag = true;
    d.showTemp = 100000;
})();



function loading(){
	$('#Form').append('<input type="hidden" name="ajax" id="ajax" value="1">');
	tip_warn("正在通信，请稍后……");
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
	tip_warn(data.info);
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
	$('#tips').html("<div class='notification information png_bg'><a href='#' class='close'><img src='"+PUBLIC+"/images/Manage/icons/cross_grey_small.png' title='关闭这则消息'/></a><div>"+data+"</div></div>");
	$('#tips').slideDown('normal');
}

function tip_warn2(data){
	art.dialog.tips(data.info,3);
}
function redirect(url) {
	location.href = url;
}
function to_domain(domain) {
	location.href=APP+'/Domain/'+domain.toLowerCase();
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
	return false;
}


function DomainState(state,id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择操作项！');
		return false;
	}
	if (window.confirm('确实要进行此操作吗？'))
	{
		self.location=APP+"/Console/DomainState?state="+state+"&id="+keyValue;
	}
}
function DelDomain(id){
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
		self.location=APP+"/Console/DelDomain?id="+keyValue;
	}
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


$(document).ready(function(){
//Close button:
	$(".close").click(
		function () {
			$(this).parent().fadeTo(400, 0, function () { // Links with the class "close" will close parent
				$(this).slideUp(400);
			});
			return false;
		}
	);
	$('.check-all').click(
		function(){
			$(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', $(this).is(':checked'));   
		}
	);
});