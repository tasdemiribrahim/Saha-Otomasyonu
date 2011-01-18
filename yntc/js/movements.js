var personelID, konumID, tarihi;
$(document).ready(function(){

    $("#kaydet").hide();
    $("#guncelle").hide();
    $("#iptal").hide();
    $("#ekle").hide();

    $("#listele").click(function (){

        $("#kaydet").hide();
        $("#guncelle").hide();
        $("#iptal").hide();
        $("#ekle").hide();
        $('#hareketListe').html('');
        $('#satisListe').html('');
    	$(".uyari").html("");

        personelID = $("#personel :selected").val();
        konumID = $("#konum :selected").val();
        tarihi = $("#tarih").val();

        if(personelID =="" || konumID == "0")
		{
			if(personelID =="")
        		$('#personelUyari').html('*');
			if(konumID =="0")
        		$('#konumUyari').html('*');
        	$('#anaUyari').html("*Lutfen gerekli alanlari doldurunuz!!!");
		}
		else
            hareketGetir();
    });

    $("#iptal").click(temizle);

    $(".degisim").change(function (){
        $("#hareketListe").html('');
        $("#satisListe").html('');
        
        $("#kaydet").hide();
        $("#guncelle").hide();
        $("#iptal").hide();
        $("#ekle").hide();

    });

    $("#ekle").click(function (){
        $('#hareketListe').html('');
        $("#ekle").hide();
        stokGetir();
    });

    $("#kaydet").click(function (){
        
        var stokID = new Array(satirno);
        var fiyat = new Array(satirno);
        var alinanMiktar = new Array(satirno);
        var iadeMiktar = new Array(satirno);
        var alinan = $("#alinan").val();

        for(var i=0;i<satirno;i++)
        {
            stokID[i]= $("#stok"+i).val();
            fiyat[i] = $("#fiyat"+i).val();
            alinanMiktar[i] = $("#alinan"+i).val();
            iadeMiktar[i] = $("#iade"+i).val();
        }
        
        kaydet(stokID, fiyat, alinanMiktar, iadeMiktar, alinan);
    });

    $(".sec").live("click" ,function (){
        $('#hareketListe').html('');
        $("#ekle").hide();
        tarihi = $(this).parent().prev().html();
        hareketAyrintiGetir();
    });

    $(".sil").live("click" ,function (){
        tarihi = $(this).parent().prev().prev().html();
        hareketSil();
    });

    $("#guncelle").click(function (){
        var stokID = new Array(satirno);
        var hareketID = new Array(satirno);
        var fiyat = new Array(satirno);
        var alinanMiktar = new Array(satirno);
        var iadeMiktar = new Array(satirno);
        var alinani = $("#alinan").val();

        for(var i=0;i<satirno;i++)
        {
            stokID[i]= $("#stok"+i).val();
            hareketID[i]= $("#hareket"+i).val();
            fiyat[i] = $("#fiyat"+i).val();
            alinanMiktar[i] = $("#alinan"+i).val();
            iadeMiktar[i] = $("#iade"+i).val();
        }
        guncelle(stokID, hareketID, fiyat, alinanMiktar, iadeMiktar, alinani);
    });


});

function guncelle(stokID, hareketID, fiyat, alinanMiktar, iadeMiktar, alinan)
{
     $.ajax({
        type: 'GET',
        url: 'movementsAjax.php',
        data: 'guncelle=1&hareketID='+hareketID+'&stokID='+stokID+'&fiyat='+fiyat+'&alinanMiktar='+alinanMiktar
                +'&iadeMiktar='+iadeMiktar+'&personelID='+personelID+'&konumID='+konumID+'&tarih='+tarihi+'&alinan='+alinan,
        success: function(cevap) {
            if(cevap != "" || cevap!="false")
                alert("Güncelleme işlemi başarıyla gerçekleştirildi");
            else
                alert("Güncelleme işlemi sırasında bir hata oluştu");
            temizle();
        } 
    });
}

function kaydet(stokID, fiyat, alinanMiktar, iadeMiktar, alinan)
{
     $.ajax({
        type: 'GET',
        url: 'movementsAjax.php',
        data: 'kaydet=1&stokID='+stokID+'&fiyat='+fiyat+'&alinanMiktar='+alinanMiktar+'&iadeMiktar='+iadeMiktar
            +'&personelID='+personelID+'&konumID='+konumID+'&alinan='+alinan+"&tarih="+tarihi,
        success: function(cevap) {
            if(cevap != "" || cevap!="false")
                alert("Kaydetme işlemi başarıyla gerçekleştirildi");
            else
                alert("Kaydetme işlemi sırasında bir hata oluştu");
            temizle();
        }
    });
}


var satirno;
function hareketGetir()
{
    satirno = 0;
    var sinif;
    $('#hareketListe').append('<tr class="tbl_baslik"><td>Parti No</td><td>Parti Tarihi</td><td colspan ="2">İşlemler</td></tr>');
     $.ajax({
        type: 'POST',
        url: 'movementsAjax.php',
        data: 'hareketGetir=1&personel='+personelID+'&konum='+konumID+'&tarih='+tarihi,
        success: function(cevap) {
            if(cevap != "")
            {
                var parcalar = cevap.split("|");
                var uzunluk = parcalar.length-1;
                for (var i=0;i<uzunluk;i=i+2)
                {
                    if(satirno % 2 == 0)
                        sinif = "acikSatir";
                    else
                        sinif = "koyuSatir";
                    var satirID = "satir"+satirno;
                    $('#hareketListe').append('<tr id="'+satirID+'" class="'+sinif+'" ></tr>');
                    $('#'+satirID).append('<input type="hidden" id="personelID" value="'+parcalar[i]+'">');
                    $('#'+satirID).append('<input type="hidden" id="konumID" value="'+parcalar[i+1]+'">');
                    $('#'+satirID).append('<td style="width: 15%">'+(i+1)+'</td>');
                    $('#'+satirID).append('<td style="width: 40%">'+parcalar[i+2]+'</td>');
                    $('#'+satirID).append('<td style="width: 35%"><input type="button" name="sec" id="sec" value="Güncelle" class="sec button" style="width: 50%"></td>');
                    $('#'+satirID).append('<td style="width: 10%"><input type="button" name="sil" id="sil" value="Sil" class="sil button" style="width: 50%"></td>');
                    satirno++;
                    i++;
                }
            }
            else
                $('#hareketListe').append('<tr><td align="center" colspan="4">Kayıt Bulunamadı.</td></tr>');
            $("#ekle").show();
        }
    });
}

function hareketAyrintiGetir()
{
    satirno = 0;
    var sinif;
    $('#satisListe').append('<tr class="tbl_baslik"><td>Ürün</td><td>Miktar</td><td>İade</td></tr>');
	
     $.ajax({
        type: 'GET',
        url: 'movementsAjax.php',
        data: 'hareketAyrintiGetir=1&personel='+personelID+'&konum='+konumID+'&tarih='+tarihi,
        success: function(cevap) {
            if(cevap != "")
            {

                var parcalar = cevap.split("|");
                var uzunluk = parcalar.length-2;
                for (var i=0;i<uzunluk;i=i+6)
                {
                    if(satirno % 2 == 0)
                        sinif = "acikSatir";
                    else
                        sinif = "koyuSatir";

                    var satirID = "urun"+satirno;
                    var hareketID = "hareket"+satirno;
                    var stokID = "stok"+satirno;
                    var stokFiyat = "fiyat"+satirno;
                    var alinanMiktar = "alinan"+satirno;
                    var iadeMiktar = "iade"+satirno;

                    $('#satisListe').append('<tr id="'+satirID+'" class="'+sinif+'" ></tr>');
                     $('#'+satirID).append('<input type="hidden" id="'+stokID+'" value="'+parcalar[i]+'">');
                    $('#'+satirID).append('<td style="width: 40%">'+parcalar[i+1]+'</td>');
                    $('#'+satirID).append('<input type="hidden" id="'+stokFiyat+'" value="'+parcalar[i+2]+'">');
                    $('#'+satirID).append('<input type="hidden" id="'+hareketID+'" value="'+parcalar[i+3]+'">');
                    $('#'+satirID).append('<td style="width: 10%"><input type="text" id="'+alinanMiktar+'" value="'+parcalar[i+4]+'"></td>');
                    $('#'+satirID).append('<td style="width: 10%"><input type="text" id="'+iadeMiktar+'" value="'+parcalar[i+5]+'"></td>');
                    satirno++;
                }
                $('#satisListe').append('<tr class="tbl_baslik">\n\<td>Toplam Borc/ Alinan</td>\n\<td>'+parcalar[i]+'</td>\n\
<td><input type="text" id="alinan" value="'+parcalar[i+1]+'"></td></tr>');
                $("#guncelle").show();
                $("#iptal").show();
            }
            else
                $('#hareketListe').append('<tr><td align="center" colspan="4">Kayıt Bulunamadı.</td></tr>');
        }
    });
}

function stokGetir()
{
    satirno = 0;
    var sinif;
    $('#satisListe').append('<tr class="tbl_baslik"><td>Ürün</td><td>Miktar</td><td>İade</td></tr>');
     $.ajax({
        type: 'GET',
        url: 'movementsAjax.php',
        data: 'stokGetir=1&konumID='+konumID+'&tarih='+tarihi,
        success: function(cevap) {
            if(cevap != "")
            {
                var parcalar = cevap.split("|");
                var uzunluk = parcalar.length-1;
                for (var i=0;i<uzunluk;i=i+3)
                {
                    if(satirno % 2 == 0)
                        sinif = "acikSatir";
                    else
                        sinif = "koyuSatir";
                    var satirID = "urun"+satirno;
                    var stokID = "stok"+satirno;
                    var stokFiyat = "fiyat"+satirno;
                    var alinanMiktar = "alinan"+satirno;
                    var iadeMiktar = "iade"+satirno;
                    $('#satisListe').append('<tr id="'+satirID+'" class="'+sinif+'" ></tr>');
                     $('#'+satirID).append('<input type="hidden" id="'+stokID+'" value="'+parcalar[i]+'">');
                    $('#'+satirID).append('<td style="width: 40%">'+parcalar[i+1]+'</td>');
                    $('#'+satirID).append('<input type="hidden" id="'+stokFiyat+'" value="'+parcalar[i+2]+'">');
                    $('#'+satirID).append('<td style="width: 10%"><input type="text" id="'+alinanMiktar+'"></td>');
                    $('#'+satirID).append('<td style="width: 10%"><input type="text" id="'+iadeMiktar+'"></td>');
                    satirno++;
                }
                $('#satisListe').append('<tr class="tbl_baslik">\n\<td>Toplam Borç / Alınan</td>\n\<td>'+parcalar[i]+'</td>\n\<td><input type="text" id="alinan"></td></tr>');
                $("#kaydet").show();
                $("#iptal").show();
            }
            else
                $('#satisListe').append('<tr><td align="center" colspan="4">Uygun Stok Bulunamadı.</td></tr>');
        }
    });
}

function hareketSil()
{
    $.ajax({
        type: 'GET',
        url: 'movementsAjax.php',
        data: 'sil=1&konumID='+konumID+'&tarih='+tarihi,
        success: function(cevap) {
            if(cevap != "" || cevap!="false")
                alert("Silme işlemi başarıyla gerçekleştirildi");
            else
                alert("Silme işlemi sırasında bir hata oluştu");
            temizle();
        }
    });
}

function temizle()
{
    $("#personel").val("");
    $("#konum").val("");
    $('#hareketListe').html('');
    $('#satisListe').html('');
    $(".uyari").html("");
    var tarihi = new Date();
    var temp = tarihi.getDate();
    var gun = (temp < 10) ? '0' + temp : temp;
    temp = tarihi.getMonth() + 1;
    var ay = (temp < 10) ? '0' + temp : temp;
    temp = tarihi.getYear();
    var yil = (temp < 1000) ? temp + 1900 : temp;
    $("#tarih").val(yil+"-"+ay+"-"+gun);

    $("#kaydet").hide();
    $("#guncelle").hide();
    $("#iptal").hide();
    $("#ekle").hide();
}