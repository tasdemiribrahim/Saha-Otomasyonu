$(document).ready(function(){
    $('#guncelle').hide();
    tabloDoldur("","");
    $(".ara").live("keyup" ,function (){
        tabloDoldur($("#kodAra").val(),$("#tanimAra").val());
    });

    var kod;
    $(".sec").live("click" ,function (){
        $("#kaydet").hide();
        $("#guncelle").show();

        kod = $(this).parent().prev().prev().prev().html();

        $("#tanim").val($(this).parent().prev().prev().html());
        $("#sifre").val($(this).parent().prev().val());
    });

    $("#guncelle").click(function (){
        if(kontrolEt($("#tanim").val(),$("#sifre").val()))
            guncelle(kod,$("#tanim").val(),$("#sifre").val());
    });

    $("#kaydet").click(function (){
        if(kontrolEt($("#tanim").val(),$("#sifre").val()))
            kaydet($("#tanim").val(),$("#sifre").val());
    });
    $("#iptal").click(temizle);
    $(".sayfaLink").live("click" ,sayfaClick);
});

var satirno;
function tabloDoldur(kodGir,tanimGir)
{
    satirno=0;
    $.ajax({
        type: 'GET',
        url: 'staffAjax.php',
        data: 'tabloDoldur=1&kodGir='+kodGir+'&tanimGir='+tanimGir,
        success: function(cevap) {

            $("#sayfaNo").html('');
            $('#tabloGovde').html('');

            if(cevap != "")
            {
                var parcalar = cevap.split("|");
                var uzunluk = parcalar.length-1;
                for (i=0;i<uzunluk;i=i+3)
                {
                    var satirID = "satir"+satirno;
                    $('#tabloGovde').append('<tr id="'+satirID+'"></tr>');
                    $('#'+satirID).append('<td>'+parcalar[i]+'</td>');
                    $('#'+satirID).append('<td>'+parcalar[i+1]+'</td>');
                    $('#'+satirID).append('<input type="hidden" value="'+parcalar[i+2]+'">');
                    $('#'+satirID).append('<td><input type="button" class="button sec" name="sec" id="sec" value="Güncelle" style="width: 50%"></td>');
                    satirno++;
                }
                $('tbody tr:odd', $("#tablo")).addClass('acikSatir');
                $('tbody tr:even', $("#tablo")).addClass('koyuSatir');
                sayfaNoEkle();
            }else
                $('#tabloGovde').append('<tr class="koyuSatir"><td colspan="5" align="center">Kayıt Bulunamadı.</td></tr>');
        }
    });
}

function guncelle(kod,tanim,sifre)
{
    alert('guncelle=1&tanim='+tanim+'&sifre='+sifre+'&kod='+kod);
    $.ajax({
        type: 'GET',
        url: 'staffAjax.php',
        data: 'guncelle=1&tanim='+tanim+'&sifre='+sifre+'&kod='+kod,
        success: function(cevap) {

            if(cevap != "")
            {
                if(cevap == "false")
                    alert("Güncelleme işlemi gerçekleştirilemedi.");
                else
                    alert("Güncelleme işlemi başarıyla gerçekleştirildi.");
                temizle();
            }
        }
    });
}

function kaydet(tanim,sifre)
{
    $.ajax({
        type: 'GET',
        url: 'staffAjax.php',
        data: 'kaydet=1&tanim='+tanim+'&sifre='+sifre,
        success: function(cevap) {
            if(cevap != "")
            {
                if(cevap == "false")
                    alert("Kaydetme işlemi gerçekleştirilemedi.");
                else
                    alert("Kaydetme işlemi başarıyla gerçekleştirildi.");
                temizle();
            }
        }
    });
}

function temizle()
{
    $("#tanim").val("");
    $("#sifre").val("");
    $("#kodAra").val("");
    $("#tanimAra").val("");
    $("#kaydet").show();
    $("#guncelle").hide();
    $(".uyari").html("");
    tabloDoldur("","");
}

function kontrolEt(deger1, deger2)
{
	var pass=true;
    $(".uyari").html("");
    if(deger1=="")
    {
    	$("#tanimUyari").html("*");
        pass=false;
    }
    if(deger2 =="")
    {
    	$("#sifreUyari").html("*");
        pass=false;
    }
	if(!pass)
    	$("#anaUyari").html("*Lütfen gerekli alanları doldurunuz!!!");
    return pass;
}