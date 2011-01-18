var id;
$(document).ready(function(){

   $('#guncelle').hide();
   
   tabloDoldur("","","","","");
   
   $(".ara").live("keyup" ,function (){        
        tabloDoldur($("#kodAra").val(),$("#tanimAra").val(),$('#miktarAra').val(),$('#birimAra').val(),$('#tarihAra').val());
   });

   $("#birimAra").live("change" ,function (){     
		tabloDoldur($("#kodAra").val(),$("#tanimAra").val(),$('#miktarAra').val(),$('#birimAra').val(),$('#tarihAra').val());
   });

   $(".sec").live("click" ,function (){

        $("#kaydet").hide();
        $("#guncelle").show();
		
        id = $(this).parent().prev().prev().prev().prev().prev().prev().html();

        $("#personel").val($(this).parent().prev().val());
        $("#miktar").val($(this).parent().prev().prev().prev().prev().html());
        $("#paraBirim").val($(this).parent().prev().prev().prev().html());
        $("#tarih").val($(this).parent().prev().prev().html());
        
    });

     $(".sil").live("click" ,function (){
        sil($(this).parent().prev().prev().prev().prev().prev().prev().html());
     });

    $("#kaydet").click(function (){
        kaydet($("#personel :selected").val(), $("#miktar").val(), $("#paraBirim").val(), $("#tarih").val());
    });

    $("#guncelle").click(function (){
        guncelle($("#personel :selected").val(), $("#miktar").val(), $("#paraBirim").val(), $("#tarih").val());
    });

    $(".sayfaLink").live("click" ,sayfaClick);
});

var satirno;
function tabloDoldur(kodAra,tanimAra,miktarAra,birimAra,tarihAra)
{
    satirno = 0;
    $.ajax({
        type: 'GET',
        url: 'staffExpenceAjax.php',
        data: 'kayitAra=1&kodAra='+kodAra+'&tanimAra='+tanimAra+'&miktarAra='+miktarAra+'&birimAra='+birimAra+'&tarihAra='+tarihAra,
        success: function(cevap) {
            $("#sayfaNo").html('');
            $('#tabloGovde').html('');
            
            if(cevap != "")
            {
                var parcalar = cevap.split("|");
                var uzunluk = parcalar.length-1;
                for (var i=0;i<uzunluk;i=i+6)
                {
                    var satirID = "satir"+satirno;
                    $('#tabloGovde').append('<tr id="'+satirID+'"></tr>');
                    $('#'+satirID).append('<td>'+parcalar[i]+'</td>');
                    $('#'+satirID).append('<td>'+parcalar[i+1]+'</td>');
                    $('#'+satirID).append('<td>'+parcalar[i+2]+'</td>');
                    $('#'+satirID).append('<td>'+parcalar[i+3]+'</td>');
                    $('#'+satirID).append('<td>'+parcalar[i+4]+'</td>');
                    $('#'+satirID).append('<input type="hidden" name="personelID" value="'+parcalar[i+5]+'">');
                    $('#'+satirID).append('<td><input type="button" class="button sec" name="sec" id="sec" value="Güncelle" style="width: 50%">\n\<input type="button" class="button sil" name="sil" id="sil" value="Sil" style="width: 40%"></td>');
                    satirno++;
                }
                $('tbody tr:odd', $("#tablo")).removeClass('koyuSatir').addClass('acikSatir');
                $('tbody tr:even', $("#tablo")).removeClass('acikSatir').addClass('koyuSatir');
                sayfaNoEkle();
            }else
                $('#tabloGovde').append('<tr class="koyuSatir"><td colspan="6" align="center">Kayıt Bulunamadı.</td></tr>');
        }
    });
}

function kaydet(personelID, miktar, birim, tarih)
{
    var kontrol = kontrolEt();
    if(kontrol)
    {
        $.ajax({
            type: 'GET',
            url: 'staffExpenceAjax.php',
            data: 'kaydet=1&personelID='+personelID+'&miktar='+miktar+'&birim='+birim+'&tarih='+tarih,
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
}

function guncelle(personelID, miktar, birim, tarih)
{
    var kontrol = kontrolEt();
    if(kontrol)
    {
        $.ajax({
            type: 'GET',
            url: 'staffExpenceAjax.php',
            data: 'guncelle=1&personelID='+personelID+'&miktar='+miktar+'&birim='+birim+'&tarih='+tarih+'&id='+id,
            success: function(cevap) {
                if(cevap != "")
                {
                    if(cevap == "false")
                        alert("Guncelleme işlemi gerçekleştirilemedi.");
                    else
                        alert("Guncelleme işlemi başarıyla gerçekleştirildi.");
                    temizle();
                }
            }

        });
    }
    
}

function sil(id)
{
    $.ajax({
        type: 'GET',
        url: 'staffExpenceAjax.php',
        data: 'sil=1&id='+id,
        success: function(cevap) {
			if(cevap != "")
			{
				if(cevap == "false")
					alert("Silme işlemi gerçekleştirilemedi.");
				else
					alert("Silme işlemi başarıyla gerçekleştirildi.");
				temizle();
			}
        }
    });
}

function temizle()
{
    $("#personel").val("");
    $("#miktar").val("");
    $("#paraBirim").val("");
    $("#kodAra").val("");
    $("#tanimAra").val("");
    $("#miktarAra").val("");
    $("#tarihAra").val("");
    $(".uyari").html("");

    var tarih = new Date();
    var temp = tarih.getDate();
    var gun = (temp < 10) ? '0' + temp : temp;
    temp = tarih.getMonth() + 1;
    var ay = (temp < 10) ? '0' + temp : temp;
    temp = tarih.getYear();
    var yil = (temp < 1000) ? temp + 1900 : temp;
    $("#tarih").val(yil+"-"+ay+"-"+gun);
    
    $("#kaydet").show();
    $("#guncelle").hide();
    tabloDoldur("", "", "", "", "");
}

function kontrolEt()
{
	var pass=true;
    $(".uyari").html("");
    if($("#personel :selected").val()=="")
    {
    	$("#personelUyari").html("*");
        pass=false;
    }
	if($("#miktar").val() =="")
    {
    	$("#miktarUyari").html("*");
        pass=false;
    }
	if(!pass)
    	$("#anaUyari").html("*Lütfen gerekli alanları doldurunuz!!!");
    return pass;
}
