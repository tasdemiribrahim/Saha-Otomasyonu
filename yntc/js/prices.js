$(document).ready(function(){
    
    $("#konum").change(function (){
        if($("#konum :selected").val() != "")
            tabloDoldur($("#konum :selected").val(), $("#tarih").val());
    });
	$("#guncelle").hide();
    $("#tarih").focus(function (){
        if($("#konum :selected").val() != "")
            tabloDoldur($("#konum :selected").val(), $("#tarih").val());
    });
    $("#guncelle").live("click",guncelle);
});

var satirno;
function tabloDoldur(konum, tarih)
{
    satirno=0;
    var sinif;
    
    $("#tablo").html('');
    $('#tablo').append('<tr class="tbl_baslik"><td>Ürünler</td><td id="fiyatTarih">Ürün Fiyatları ('+tarih+')</td></tr>');

    $.ajax({
        type: 'GET',
        url: 'pricesAjax.php',
        data: 'konum='+konum+'&tarih='+tarih+'&tabloDoldur=1',
        success: function(cevap) {

            if(cevap != "")
            {
                var parcalar = cevap.split("|");
                var uzunluk = parcalar.length-1;
                for (i=0;i<uzunluk;i=i+2)
                {
                    if(satirno % 2 == 0)
                        sinif = "acikSatir";
                    else
                        sinif = "koyuSatir";

                    var satirID = "satir"+satirno;
                    var urunSutunID = "urun"+satirno;
                    var fiyatSutunID = "fiyat"+satirno;
                    $('#tablo').append('<tr id="'+satirID+'" class="'+sinif+'" ></tr>');
                    $('#'+satirID).append('<td><p style="text-align:left;" id="'+urunSutunID+'">'+parcalar[i]+'</p></td>');
                    $('#'+satirID).append('<td><input type="text" name="'+fiyatSutunID+'" id="'+fiyatSutunID+'" value="'+parcalar[i+1]+'" style="width:100px"/></td>')
                    satirno++;
                }
                $('#guncelle').show();
            }
        }
    });
}

function guncelle()
{
    var urun = new Array(satirno);
    var fiyat = new Array(satirno);
    var kontrol = true;
    for(var i=0;i<satirno;i++)
    {
        urun[i]= $("#urun"+i).html();
        fiyat[i]= $("#fiyat"+i).val();
        
        if(fiyat[i]=="")
        {
			$("#uyari"+i).html("*");
            kontrol = false;
        }
		else
			$("#uyari"+i).html("");
    }
    if(kontrol == true)
    {
        $.ajax({
            type: 'GET',
            url: 'pricesAjax.php',
            data: 'konum='+$("#konum :selected").val()+'&tarih='+$("#tarih").val()+'&urun='+urun+'&fiyat='+fiyat+'&fiyatGuncelle=1',
            success: function(cevap) {
                if(cevap != "")
                {
                    if(cevap =="true")
                        alert("Güncelleme işlemi sırasında bir hata oluştu");
                    else
                        alert("Güncelleme işlemi başarıyla gerçekleştirildi");
                    temizle();
                }
            }
        });
    }
	else	
	    $("#anaUyari").html("*Lütfen bütün fiyat alanlarını doldurunuz!!!");
}

function temizle()
{
    $("#konum option[value='0']").attr('selected', 'selected');
    $("#guncelle").hide();
    $("#tablo").html('');
    $(".uyari").html("");
    var tarih = new Date();
    var temp = tarih.getDate();
    var gun = (temp < 10) ? '0' + temp : temp;
    temp = tarih.getMonth() + 1;
    var ay = (temp < 10) ? '0' + temp : temp;
    temp = tarih.getYear();
    var yil = (temp < 1000) ? temp + 1900 : temp;
    $("#tarih").val(yil+"-"+ay+"-"+gun);
}
