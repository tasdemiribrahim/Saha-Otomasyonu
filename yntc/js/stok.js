var yeniEklenenResimler = new Array();
var yeniEklenenDosyalar = new Array();
var kayitliSilResimler = new Array();
var kayitliSilDosyalar = new Array();
var temelOlcuBirimDeger = new Array();
var ikinciOlcuBirim = new Array();
var ikinciOlcuBirimDeger = new Array();
var kayitliDonusumSil = new Array();
var yeniResimIndeks = 0, yeniDosyaIndeks=0,kayitliSilResimIndeks=0, kayitliSilDosyaIndeks=0,donusumIndeks = 0, kayitliDonusumSilIndeks=0;

$(function(){
    var resimBtnUpload=$('#resimUpload');
    var resimStatus=$('#resimStatus');
    new AjaxUpload(resimBtnUpload, {
        action: 'stokAjax.php',
        name: 'uploadResim',
        onSubmit: function(file, ext){
            if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
                resimStatus.text('Sadece JPG, PNG veya GIF resimleri yuklenebilir!!!');
                return false;
            }
            resimStatus.text('Uploading...');
        },
        onComplete: function(file, uniqueFile){
            resimStatus.text('');
			if(uniqueFile=="hata")
                resimStatus.text('Resim yuklenirken bir hata oluştu!!!');
			else if(uniqueFile=="uyari")
                resimStatus.text('Sadece JPG, PNG veya GIF resimleri yuklenebilir!!!');
			else
			{
                $('<li></li>').appendTo('#resimler').html('<a class="yeniResimSil" href="#">X</a><img class="'+uniqueFile+'" width="100" height="100" src="'+uniqueFile+'"/><input type="hidden" name="index" value="'+yeniResimIndeks+'">').addClass('success');
				yeniEklenenResimler[yeniResimIndeks] = uniqueFile;
				yeniResimIndeks++;
			}
        }
    });

    var dosyaBtnUpload=$('#dosyaUpload');
    var dosyaStatus=$('#dosyaStatus');
    new AjaxUpload(dosyaBtnUpload, {
        action: 'stokAjax.php',
        name: 'uploadDosya',
        onSubmit: function(file, ext){
            dosyaStatus.text('Uploading...');
        },
        onComplete: function(file, uniqueFile){
			dosyaStatus.text('');
			if(uniqueFile=="hata")
                dosyaStatus.text('Dosya yuklenirken bir hata oluştu!!!');
			else if(uniqueFile=="uyari")
                dosyaStatus.text('Zararli dosya yuklemeye calistiniz!!!');
			else
			{ 
				$('<li></li>').appendTo('#dosyalar').html('<a class="yeniDosyaSil" href="#">X</a>&nbsp;&nbsp;&nbsp;<input type="hidden" value="'+uniqueFile+'"/><a id="silinecekDosyaIsim" href="'+uniqueFile+'">'+file+'</a><input type="hidden" name="dosyalar[]" id="silinecekDosyaIndex" value="'+yeniDosyaIndeks+'">').addClass('success');
				yeniEklenenDosyalar[yeniDosyaIndeks] = uniqueFile;
				yeniDosyaIndeks++;
			}
        }
    });

});

$(document).ready(function(){
    $('#guncelle').hide();
    $('#buttonBul').append('<input type="hidden" id="mod" name="mod" value="kaydet">');
	
    if($("#kod").val() != "")
        getir($("#kod").val(),"stokGetirKod");
		
    $(".yeniResimSil").live("click" ,function (){
        var src = $(this).parent().children("img").attr("src");
        var indeks = $(this).parent().children("input").attr("value");
        yeniEklenenResimler[indeks] = 0;
        $(this).parent().remove();
        $.ajax({
            type: 'GET',
            url: 'stokAjax.php',
            data: 'resimAdi='+src
        });
    });

    $(".kayitliResimSil").live("click" ,function (){
        var src = $(this).parent().children("img").attr("src");
        $(this).parent().remove();
        kayitliSilResimler[kayitliSilResimIndeks] = src;
        kayitliSilResimIndeks++;
    });

    $(".yeniDosyaSil").live("click" ,function (){
        var src = $(this).parent().children("#silinecekDosyaIsim").attr("href");
        var indeks = $(this).parent().children("#silinecekDosyaIndex").val();
        yeniEklenenDosyalar[indeks] = 0;
        $(this).parent().remove();
         $.ajax({
            type: 'GET',
            url: 'stokAjax.php',
            data: 'dosyaAdi='+src
        });
    });

    $(".kayitliDosyaSil").live("click" ,function (){
        var src = $(this).parent().children("#silinecekDosyaIsim").attr("href");
        $(this).parent().remove();
        kayitliSilDosyalar[kayitliSilDosyaIndeks] = src;
        kayitliSilDosyaIndeks++;
    });

    $(".donusumSilButon").live("click" ,function (){
        var satir = $(this).parent().parent();
        var ekle = $(this).parent().prev().html();
        var indeks = $(this).next().val();
        satir.remove();

        temelOlcuBirimDeger[indeks] = 0;
        ikinciOlcuBirim[indeks] = 0;
        ikinciOlcuBirimDeger[indeks] = 0;

        $('#yeniIkinciOlcuBirim').append('<option value="'+ekle+'">'+ekle+'</option>');
        if($('#degerEkle').is(':hidden'))
            $('#degerEkle').show();
        return false;
    });

    $(".kayitliDonusumSil").live("click" ,function (){
        var satir = $(this).parent().parent();
        var ekle = $(this).parent().prev().html();
        satir.remove();

        var ekleme = false;
        for(var i=0; i<kayitliDonusumSilIndeks;i++)
        {
            if(kayitliDonusumSil[i] == ekle)
            {
                ekleme = true;
                break;
            }
        }
        if(ekleme == false)
        {
            kayitliDonusumSil[kayitliDonusumSilIndeks] = ekle;
            kayitliDonusumSilIndeks++;
        }
        
        $('#yeniIkinciOlcuBirim').append('<option value="'+ekle+'">'+ekle+'</option>');
        if($('#degerEkle').is(':hidden'))
            $('#degerEkle').show();
        return false;
    });

    $("#donusumEkle").live("click" ,function (){
        var yeniTemelOlcuBirimDegeri = $("#yeniTemelOlcuBirimDeger").val();
        var yeniIkinciOlcuBirimDegeri = $("#yeniIkinciOlcuBirimDeger").val();
        var yeniIkinciOlcuBirimi = $("#yeniIkinciOlcuBirim").val();
        $("#yeniIkinciOlcuBirim :selected").remove();

        if($("#yeniIkinciOlcuBirim :selected").html()==null)
            $('#degerEkle').hide();
        yeniBirimDonusumEkle(yeniTemelOlcuBirimDegeri, yeniIkinciOlcuBirimi, yeniIkinciOlcuBirimDegeri);
    });

    $("#temelOlcuBirim").change(function (){
        $("#donusumTablo").html('');
        if($("#temelOlcuBirim :selected").val()!="" && $("#temelOlcuBirim :selected").val()!=0)
            donusumTabloDoldur($("#temelOlcuBirim :selected").text());
    });

    $("#kod").blur(function (){
        getir($("#kod").val(),"stokGetirKod");
    });
    
    $("#tanim1").blur(function (){
        getir($("#tanim1").val(),"stokGetirTanim");
    });

    $(".kodOneriListe").live("click", function (){
        getir($("#kod").val(),"stokGetirKod");
    });

    $(".tanimOneriListe").live("click", function (){
       getir($("#tanim1").val(),"stokGetirTanim");
    });

    $("#iptal").click(function (){
        temizle();
    });

    $("#kaydet").click(function (){
        kaydet($("#kod").val(), $("#stokTur :selected").val(), $("#depoID :selected").val(), $("#tanim1").val(), $("#tanim2").val(), $("#fiyatBirim").val(), $("#alisFiyat").val(), $("#alisKDV").val(), $("#satisFiyat").val(),$("#satisKDV").val(), $("#temelOlcuBirim :selected").val(), $("#agirlik").val(), $("#agirlikBirim :selected").val(), $("#en").val(), $("#enBirim :selected").val(), $("#boy").val(), $("#boyBirim :selected").val(), $("#yukseklik").val(),$("#yukseklikBirim :selected").val(), $("#barkod").val());
    });

    $("#guncelle").click(function (){
        guncelle($("#kod").val(), $("#eskiKod").val(), $("#stokTur :selected").val(), $("#depoID :selected").val(), $("#tanim1").val(), $("#tanim2").val(), $("#fiyatBirim").val(), $("#alisFiyat").val(), $("#alisKDV").val(), $("#satisFiyat").val(),$("#satisKDV").val(), $("#temelOlcuBirim :selected").val(), $("#eskiTemelOlcuBirim").val(), $("#agirlik").val(), $("#agirlikBirim :selected").val(), $("#en").val(), $("#enBirim :selected").val(), $("#boy").val(), $("#boyBirim :selected").val(), $("#yukseklik").val(),$("#yukseklikBirim :selected").val(), $("#barkod").val());

    });

    $("#kod").jSuggest({
        url: "stokAjax.php",
        type: "POST",
        data: "kodOneri",
        autoChange: false
    });
    $("#tanim1").jSuggest({
        url: "stokAjax.php",
        type: "POST",
        data: "tanim1Oneri",
        autoChange: false
    });
    $("#tanim2").jSuggest({
        url: "stokAjax.php",
        type: "POST",
        data: "tanim2Oneri",
        autoChange: false
    });

    $("#stokAra").click(function (){
        window.open('stokAra.php','','status=1,width=600,height=550,resizable = 1,resize=no');
    });
});

function getir(gelen,id) {
    $.ajax({
        type: 'GET',
        url: 'stokAjax.php',
        data: id+'='+gelen,
        success: function(cevap) {
            if(cevap!="")
            {
                temizle();
                var parcalar= cevap.split("|");
                $('#id').val(parcalar[0]);
                document.form.kod.value=parcalar[1];
                $('#eskiKod').val(parcalar[1]);
                goster("stokTur",parcalar[2]);
                goster("depoID",parcalar[3]);
                document.form.tanim1.value=parcalar[4];
                document.form.tanim2.value=parcalar[5];
                goster("fiyatBirim",parcalar[6]);
                document.form.alisFiyat.value=parcalar[7];
                document.form.alisKDV.value=parcalar[8];
                document.form.satisFiyat.value=parcalar[9];
                document.form.satisKDV.value=parcalar[10];
                document.form.agirlik.value=parcalar[11];
                goster("temelOlcuBirim",parcalar[12]);
                $('#eskiTemelOlcuBirim').val(parcalar[12]);
                document.form.en.value=parcalar[13];
                document.form.boy.value=parcalar[14];
                document.form.yukseklik.value=parcalar[15];
                goster("agirlikBirim",parcalar[16]);
                goster("enBirim",parcalar[17]);
                goster("boyBirim",parcalar[18]);
                goster("yukseklikBirim",parcalar[19]);
                document.form.barkod.value=parcalar[20];
				if(parcalar[20]!=0)
                	$("#barkodImage").html('&nbsp;&nbsp;<img id="barkodImageValue" style=" width:100px; height:50px; " src="images/'+parcalar[20]+'.jpeg" alt="'+parcalar[20]+'Barkod">');
				else 
                	$("#barkodImage").html('');
                resimGetir(parcalar[0]);
                dosyaGetir(parcalar[0]);
                donusumGetir(parcalar[0]);

                $('#kaydet').hide();
                $('#guncelle').show();
                $('#buttonBul').append('<input type="hidden" id="mod" name="mod" value="guncelle">');
            }
        }

    });
}
function resimGetir(gelen)
{
	$.ajax({
		type: 'GET',
		url: 'stokAjax.php',
		data: 'resimGetirId='+gelen,
		success: function(cevap) 
		{
			if(cevap)
			{
				var parcalar = cevap.split("|");
				var uzunluk = parcalar.length-1;
				for (i=0;i<uzunluk;i++)
					$('<li></li>').appendTo('#resimler').html('<a class="kayitliResimSil" href="#">X</a><img width="100" height="100" id="'+parcalar[i]+'" src="'+parcalar[i]+'"/><br />').addClass('success');
			}
		}
	});
}

function dosyaGetir(gelen)
{
    $.ajax({
        type: 'GET',
        url: 'stokAjax.php',
        data: 'dosyaGetirId='+gelen,
        success: function(cevap) {
            if(cevap)
            {
                var parcalar = cevap.split("|");
                var uzunluk = parcalar.length-1;
                for (i=0;i<uzunluk;i++)
                {
                    var bas = parcalar[i].indexOf('-')+1;
                    var son = parcalar[i].length;
                    $('<li></li>').appendTo('#dosyalar').html('<a class="kayitliDosyaSil" href="#">X</a>&nbsp;&nbsp;&nbsp;<a href="'+parcalar[i]+'" id="silinecekDosyaIsim">'+parcalar[i].substring(bas, son)+'</a>').addClass('success');
                }
            }
        }

    });
}

function donusumGetir(gelen)
{
    if($("#temelOlcuBirim").val() != 0)
    {
        donusumTabloDoldur($("#temelOlcuBirim :selected").text());
        $.ajax({
            type: 'GET',
            url: 'stokAjax.php',
            data: 'donusumGetirId='+gelen,
            success: function(cevap) {
                if(cevap!="")
                {
                    $('#yeniIkinciOlcuBirim').find('option[value='+$("#temelOlcuBirim :selected").text()+']').remove();
                    var parcalar = cevap.split("|");
                    var uzunluk = parcalar.length-1;
                    for (i=0;i<uzunluk;i=i+3)
                    {
                        var satirID="donusum"+satirno;
                        $('#donusumTablo').append('<tr id="'+satirID+'" class="acikSatir" ></tr>');
                        $('#'+satirID).append('<td>'+parcalar[i]+'</td>');
                        $('#'+satirID).append('<td>'+ $("#temelOlcuBirim :selected").text()+' = </td>');
                        $('#'+satirID).append('<td>'+parcalar[i+1]+'</td>');
                        $('#'+satirID).append('<td>'+parcalar[i+2]+'</td>');
                        $('#'+satirID).append('<td><a class="kayitliDonusumSil" href="#">SİL</a></td>');
                        satirno++;

                        $('#yeniIkinciOlcuBirim').find('option[value='+parcalar[i+2]+']').remove();
                    }

                    if($("#yeniIkinciOlcuBirim :selected").html()==null)
                        $('#degerEkle').hide();
                }
            }
        });
    }
}
function goster(id,optionDeger)
{
    var x=document.getElementById(id);
	var uz=x.length;
    for (i=0;i<uz;i++)
        if(x.options[i].value==optionDeger)
                x.selectedIndex=i;
}

function donusumTabloDoldur(deger){
    $('#donusumTablo').append('<tr><td class="tbl_baslik" colspan="5">Dönüşüm Ekle</td></tr><tr id="degerEkle" class="tbl_baslik"></tr>');
    $('#degerEkle').append('<td><input type="text" name="yeniTemelOlcuBirimDeger" id="yeniTemelOlcuBirimDeger" size="6" maxlength="11"/></td><td>'+deger+' = </td><td><input type="text" name="yeniIkinciOlcuBirimDeger" id=yeniIkinciOlcuBirimDeger size="6" maxlength="11"/></td><td><select name="yeniIkinciOlcuBirim" id="yeniIkinciOlcuBirim"></select></td>');

    $.ajax({
        type: 'GET',
        url: 'stokAjax.php',
        data: 'donusumTur=1',
        success: function(cevap) {
            if(cevap!="")
            {
                var parcalar= cevap.split("|");
				var uz=parcalar.length-1;
                for(i=0;i<uz;i++)
                    if(parcalar[i]!=deger)
                        $('#yeniIkinciOlcuBirim').append('<option value="'+parcalar[i]+'">'+parcalar[i]+'</option>');
            }
        }

    });
    $('#degerEkle').append('<td><input type="button" class="button" name="donusumEkle" id="donusumEkle" value="Ekle"/></td>');
}

var satirno=0;
function yeniBirimDonusumEkle(temelBirimDeger, ikinciBirim, ikinciBirimDeger){
    var satirID="donusum"+satirno;
    $('#donusumTablo').append('<tr id="'+satirID+'" class="acikSatir" ></tr>');
    $('#'+satirID).append('<td>'+temelBirimDeger+'</td>');
    $('#'+satirID).append('<td>'+ $("#temelOlcuBirim :selected").text()+' = </td>');
    $('#'+satirID).append('<td>'+ikinciBirimDeger+'</td>');
    $('#'+satirID).append('<td>'+ikinciBirim+'</td>');
    $('#'+satirID).append('<td><a class="donusumSilButon" href="#">SİL</a><input type="hidden" value="'+donusumIndeks+'"></td>');
   
   temelOlcuBirimDeger[donusumIndeks] = temelBirimDeger;
   ikinciOlcuBirim[donusumIndeks] = ikinciBirim;
   ikinciOlcuBirimDeger[donusumIndeks] = ikinciBirimDeger;
   
   satirno++;
   donusumIndeks++;
}
function kaydet(kod, stokTur, depoID, tanim1, tanim2, fiyatBirim, alisFiyat, alisKDV, satisFiyat, satisKDV, temelOlcuBirim, agirlik, agirlikBirim, en, enBirim, boy, boyBirim, yukseklik, yukseklikBirim, barkod)
{
    var kontrol = kontrolEt();
    if(kontrol == true)
        $.ajax({
            type: 'GET',
            url: 'stokAjax.php',
            data: 'kaydet=1&kod='+kod+'&stokTur='+stokTur+'&depoID='+depoID+'&tanim1='+tanim1+'&tanim2='+tanim2+'&fiyatBirim='+fiyatBirim+'&alisFiyat='+alisFiyat+'&alisKDV='+alisKDV+'&satisFiyat='+satisFiyat+'&satisKDV='+satisKDV+'&temelOlcuBirim='+temelOlcuBirim+'&agirlik='+agirlik+'&agirlikBirim='+agirlikBirim+'&en='+en+'&enBirim='+enBirim+'&boy='+boy+'&boyBirim='+boyBirim+'&yukseklik='+yukseklik+'&yukseklikBirim='+yukseklikBirim+'&barkod='+barkod+'&yeniEklenenResimler='+yeniEklenenResimler+'&yeniEklenenDosyalar='+yeniEklenenDosyalar+'&temelOlcuBirimDeger='+temelOlcuBirimDeger+'&ikinciOlcuBirim='+ikinciOlcuBirim+'&ikinciOlcuBirimDeger='+ikinciOlcuBirimDeger,
            success: function(cevap) {
				if(cevap==true)
					alert("Kayit İşlemi Gerçekleştirildi!!!");
				else
					alert("Kayit İşlemi Başarısız!!!");
				temizle();
            }
        });
}

function guncelle(kod, eskiKod, stokTur, depoID, tanim1, tanim2, fiyatBirim, alisFiyat, alisKDV, satisFiyat,satisKDV, temelOlcuBirim, eskiTemelOlcuBirim, agirlik, agirlikBirim, en, enBirim, boy, boyBirim, yukseklik,yukseklikBirim, barkod)
{
	var kontrol = kontrolEt();
	if(kontrol == true)
		$.ajax({
			type: 'GET',
			url: 'stokAjax.php',
			data: 'guncelle=1&kod='+kod+'&stokTur='+stokTur+'&depoID='+depoID+'&tanim1='+tanim1+'&tanim2='+tanim2+'&fiyatBirim='+fiyatBirim+'&alisFiyat='+alisFiyat+'&alisKDV='+alisKDV+'&satisFiyat='+satisFiyat+'&satisKDV='+satisKDV+'&temelOlcuBirim='+temelOlcuBirim+'&eskiTemelOlcuBirim='+eskiTemelOlcuBirim+'&agirlik='+agirlik+'&agirlikBirim='+agirlikBirim+'&en='+en+'&enBirim='+enBirim+'&boy='+boy+'&boyBirim='+boyBirim+'&yukseklik='+yukseklik+'&yukseklikBirim='+yukseklikBirim+'&barkod='+barkod+'&yeniEklenenResimler='+yeniEklenenResimler+'&yeniEklenenDosyalar='+yeniEklenenDosyalar+'&kayitliSilResimler='+kayitliSilResimler+'&kayitliSilDosyalar='+kayitliSilDosyalar +'&temelOlcuBirimDeger='+temelOlcuBirimDeger+'&ikinciOlcuBirim='+ikinciOlcuBirim +'&ikinciOlcuBirimDeger='+ikinciOlcuBirimDeger+'&kayitliDonusumSil='+kayitliDonusumSil+'&eskiKod='+eskiKod,
			success: function(cevap) {
				if(cevap==true)
					alert("Güncelleme İşlemi Gerçekleştirildi!!!");
				else
					alert("Güncelleme İşlemi Başarısız!!!");
				temizle();
			}
		});
}

function temizle()
{
    $("#guncelle").hide();
    $("#kaydet").show();
    $("#resimler").html('');
    $("#dosyalar").html('');
    $("#donusumTablo").html('');
    $("#eskiTemelOlcuBirim").val('');
    $("#id").val('');
    $("#eskiKod").val('');
	$("#barkodImage").html('');
    $(".uyari").html("");
    yeniEklenenResimler = new Array();
    yeniEklenenDosyalar = new Array();
    kayitliSilResimler = new Array();
    kayitliSilDosyalar = new Array();
    temelOlcuBirimDeger = new Array();
    ikinciOlcuBirim = new Array();
    ikinciOlcuBirimDeger = new Array();
    kayitliDonusumSil = new Array();
    yeniResimIndeks = 0, yeniDosyaIndeks=0,kayitliSilResimIndeks=0, kayitliSilDosyaIndeks=0,
    donusumIndeks = 0, kayitliDonusumSilIndeks=0;
    
    $('#form').each (function(){
        this.reset();
    });
    $("#kod").val("");
}
function kontrolEt()
{
    $(".uyari").html("");
	var pass=true;
    if($("#kod").val()=="")
    {
    	$("#kodUyari").html("*");
        pass=false;
    }
	if($("#tanim1").val() =="")
    {
    	$("#tanimUyari").html("*");
        pass=false;
    }
	if($("#stokTur :selected").val() =="")
    {
    	$("#turUyari").html("*");
        pass=false;
    }
	if(!pass)
    	$("#anaUyari").html("*Lütfen gerekli alanları doldurunuz!!!");
    return pass;
}

