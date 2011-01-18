$(document).ready(function()
{	
	$("#ekle").click(function (){
		$("#ayarlar").append($("#deger").val()+"<br>");
		$("#ana").val($("#ana").val()+"-"+$("#deger").val());
		$("#deger").val("");
	});
});