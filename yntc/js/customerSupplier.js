$(document).ready(function(){
    
    $('#guncelle').hide();
	
    tabloDoldur("","","");
    
    $("#kodAra").live("keyup" ,function (){
        tabloDoldur($("#kodAra").val(),$("#tanimAra").val(),$('#turAra :selected').val());
    });
	
    $("#turAra").live("change" ,function (){
        tabloDoldur($("#kodAra").val(),$("#tanimAra").val(),$('#turAra :selected').val());
    });

    var idi;
    $(".sec").live("click" ,function (){
									  
        $("#kaydet").hide();
        $("#guncelle").show();

        idi = $(this).parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().val();
        var vergiNosu = $(this).parent().prev().prev().prev().prev().val();
        if(vergiNosu == 0)
            vergiNosu ="";
        var tur = $(this).parent().prev().val();

        $("#kod").val($(this).parent().prev().prev().prev().prev().prev().prev().prev().prev().html());
        $("#tanim").val($(this).parent().prev().prev().prev().prev().prev().prev().prev().children("input").val());
        $("#adres").val($(this).parent().prev().prev().prev().prev().prev().prev().val());
        $("#telefon").val($(this).parent().prev().prev().prev().prev().prev().val());
        $("#vergiNo").val(vergiNosu);
        $("#vergiDaire").val($(this).parent().prev().prev().prev().val());

        if(tur == $("#musteriDeger").val())
        {
            $("#turMusteri").attr('checked', true);
            $("#turTedarikci").attr('checked', false);
        }
        else if(tur == $("#tedarikciDeger").val())
        {
            $("#turTedarikci").attr('checked', true);
            $("#turMusteri").attr('checked', false);
        }
        else if(tur == $("#musteriTedarikciDeger").val())
        {
            $("#turMusteri").attr('checked', true);
            $("#turTedarikci").attr('checked', true);
        }
    });

    $("#guncelle").click(function (){
        var tur;
        if($("#turMusteri").attr('checked') && $("#turTedarikci").attr('checked'))
            tur = $("#musteriTedarikciDeger").val();
        else if($("#turMusteri").attr('checked'))
            tur = $("#musteriDeger").val();
        else if($("#turTedarikci").attr('checked'))
            tur = $("#tedarikciDeger").val();
        
        guncelle(idi,$("#kod").val(),$("#tanim").val(),$("#adres").val(),$("#telefon").val(),$("#vergiNo").val(),$("#vergiDaire").val(),tur);
    });

    $("#kaydet").click(function (){
        var tur;
        if($("#turMusteri").attr('checked') && $("#turTedarikci").attr('checked'))
            tur = $("#musteriTedarikciDeger").val();
        else if($("#turMusteri").attr('checked'))
            tur = $("#musteriDeger").val();
        else if($("#turTedarikci").attr('checked'))
            tur = $("#tedarikciDeger").val();

        kaydet($("#kod").val(),$("#tanim").val(),$("#adres").val(),$("#telefon").val(),$("#vergiNo").val(),$("#vergiDaire").val(),tur);
    });

    $("#iptal").click(function (){
        $("#guncelle").hide();
        $("#kaydet").show();
    });

    $(".sayfaLink").live("click" ,sayfaClick);

    $(".sil").live("click" ,function (){
        idi = $(this).parent().prev().prev().prev().prev().prev().prev().prev().prev().prev().val();
        sil(idi);
    });
});

function tabloDoldur(kodGir,tanimGir,turGir)
{
    var satirno = 0;
   
    $.ajax({
        type: 'GET',
        url: 'customerSupplierAjax.php',
        data: 'tabloDoldur=1&kodGir='+kodGir+'&tanimGir='+tanimGir+'&turGir='+turGir,
        success: function(cevap) {

            $("#sayfaNo").html('');
            $('#tabloGovde').html('');
            if(cevap != "")
            {
                var parcalar = cevap.split("|");
                var uzunluk = parcalar.length-1;
                for (i=0;i<uzunluk;i=i+8)
                {
                    var mesaj="window.open('tupcu.php?kisi="+parcalar[i+2]+"','','status=1,width=720,height=650,resizable = 1')";
                    var satirID = "satir"+satirno;
                    $('#tabloGovde').append('<tr id="'+satirID+'"></tr>');
                    $('#'+satirID).append('<input type="hidden" name="id" value="'+parcalar[i]+'">');
                    $('#'+satirID).append('<td>'+parcalar[i+1]+'</td>');
                    $('#'+satirID).append('<td><input type=button class="button" title="Musteriye Siparis Gir" STYLE="width : 150px" onclick="'+mesaj+'" value="'+parcalar[i+2]+'"></td>');
                    $('#'+satirID).append('<input type="hidden" name="adres" value="'+parcalar[i+3]+'">');
                    $('#'+satirID).append('<input type="hidden" name="telefon" value="'+parcalar[i+4]+'">');
                    $('#'+satirID).append('<input type="hidden" name="vergiNo" value="'+parcalar[i+5]+'">');
                    $('#'+satirID).append('<input type="hidden" name="vergiDaire" value="'+parcalar[i+6]+'">');
                    var tur;
                    if(parcalar[i+7] == $("#musteriTedarikciDeger").val())
                        tur = "Müşteri&Tedarikci";
                    else if(parcalar[i+7] == $("#musteriDeger").val())
                        tur = "Müşteri";
                    else if(parcalar[i+7] == $("#tedarikciDeger").val())
                        tur = "Tedarikçi"
                    $('#'+satirID).append('<td>'+tur+'</td>');
                    $('#'+satirID).append('<input type="hidden" name="tur" value="'+parcalar[i+7]+'">');
                    $('#'+satirID).append('<td><input type="button" class="button sec" name="sec" id="sec" value="Güncelle" class="sec" style="width: 50%">\n\
                        <input type="button" class="button sil" name="sil" id="sil" value="Sil" class="sil" style="width: 40%"></td>');
                    satirno++;
                }
                $('tbody tr:odd', $("#tablo")).addClass('acikSatir');
                $('tbody tr:even', $("#tablo")).addClass('koyuSatir');
                sayfaNoEkle();
            }
			else
                $('#tabloGovde').append('<tr class="koyuSatir" ><td colspan="5" align="center">Kayıt Bulunamadı.</td></tr>');
        }
    });
}

function guncelle(id,kod,tanim,adres,telefon,vergiNo,vergiDaire,tur)
{
    var kontrol = kontrolEt();
    if(kontrol)
    {
        $.ajax({
            type: 'GET',
            url: 'customerSupplierAjax.php',
            data: 'guncelle=1&id='+id+'&kod='+kod+'&tanim='+tanim+'&adres='+adres+'&telefon='+telefon+'&vergiNo='+vergiNo+'&vergiDaire='+vergiDaire+'&tur='+tur,
            success: function(cevap) {
                    temizle();
            }
        });
    }
}
function kaydet(kod,tanim,adres,telefon,vergiNo,vergiDaire,tur)
{
    var kontrol = kontrolEt();
    if(kontrol)
    {
        $.ajax({
            type: 'GET',
            url: 'customerSupplierAjax.php',
            data: 'kaydet=1&kod='+kod+'&tanim='+tanim+'&adres='+adres+'&telefon='+telefon+'&vergiNo='+vergiNo+'&vergiDaire='+vergiDaire+'&tur='+tur,
            success: function(cevap) {
                if(cevap != "")
                {
                    alert(cevap);
                }
                temizle();
            }
        });
    }
}

function sil(id)
{
	var silinsinMi = confirm(id + " Kodlu Konumu Silmek İstediğinizden Emin Misiniz?");
    if(silinsinMi)
		$.ajax({
			type: 'GET',
			url: 'customerSupplierAjax.php',
			data: 'sil=1&id='+id,
			success: function(cevap){
					temizle();
			}
		});
}
function temizle()
{
    $(".uyari").html("");
    $("#kod").val("");
    $("#tanim").val("");
    $("#adres").val("");
    $("#telefon").val("");
    $("#vergiNo").val("");
    $("#vergiDaire").val("");
    $("#turMusteri").attr('checked', false);
    $("#turTedarikci").attr('checked', false);
    $("#kodAra").val("");
    $("#tanimAra").val("");
    $("#turAra").val("");
    $("#kaydet").show();
    $("#guncelle").hide();

    tabloDoldur("","","");
}

function kontrolEt()
{
    $(".uyari").html("");
    var tur = $("#turMusteri").attr('checked') || $("#turTedarikci").attr('checked') ? true : false;
	var pass=true;
    if($("#kod").val()=="")
    {
    	$("#kodUyari").html('*');
        pass=false;
    }
	if($("#tanim").val() =="")
    {
    	$("#tanimUyari").html('*');
        pass=false;
    }
	if(tur == false)
    {
    	$("#turUyari").html('*');
        pass=false;
    }
	if(!pass);
    	$("#anaUyari").html('*Lütfen Gerekli Alanlari Doldurun!!!');
    return pass;
}