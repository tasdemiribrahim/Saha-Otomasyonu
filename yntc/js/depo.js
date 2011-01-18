$(document).ready(function(){

    $('#guncelle').hide();
    tabloDoldur("","","");

    $(".ara").live("keyup" ,function (){
        tabloDoldur($("#kodAra").val(),$("#tanimAra").val(),$("#personelAra").val());
    });

    var idi;
    $(".sec").live("click" ,function (){

        $("#kaydet").hide();
        $("#guncelle").show();
        $("#durum").attr('checked', false);
        $("#eksiBakiyeUyari").attr('checked', false);
        $("#eksiBakiyeIzin").attr('checked', false);

        idi = $(this).parent().prev().prev().prev().prev().prev().prev().prev().val();
        var personeli = $(this).parent().prev().html();

        $("#kod").val($(this).parent().prev().prev().prev().prev().prev().prev().html());
        $("#tanim").val($(this).parent().prev().prev().prev().prev().prev().html());

        if(personeli == "")
            $("#personel").val("");
        else
        {
            $("#personel :option").each(function () {
                if($(this).text() == personeli)
                    $("#personel").val($(this).val());
            });
        }

        if($(this).parent().prev().prev().prev().prev().val() == 1)
            $("#durum").attr('checked', true);

        if($(this).parent().prev().prev().prev().val() == 1)
            $("#eksiBakiyeUyari").attr('checked', true);

        if($(this).parent().prev().prev().val() == 1)
            $("#eksiBakiyeIzin").attr('checked', true);
    });

    $(".sil").live("click" ,function (){
        idi = $(this).parent().prev().prev().prev().prev().prev().prev().prev().val();
        sil(idi);
    });

    $("#guncelle").click(function (){
        var durumi = 0;
        var eksiBakiyeUyarii = 0;
        var eksiBakiyeIzini = 0;

        if($("#durum").attr('checked'))
            durumi = 1;
        if($("#eksiBakiyeUyari").attr('checked'))
            eksiBakiyeUyarii = 1;
        if($("#eksiBakiyeIzin").attr('checked'))
            eksiBakiyeIzini = 1;
        if(kontrolEt($("#kod").val(), $("#tanim").val()))
            guncelle(idi, $("#kod").val(), $("#tanim").val(), $("#personel :selected").val(), durumi, eksiBakiyeUyarii, eksiBakiyeIzini);
    });

    $("#kaydet").click(function (){
        var durumi = 0;
        var eksiBakiyeUyarii = 0;
        var eksiBakiyeIzini = 0;

        if($("#durum").attr('checked'))
            durumi = 1;

        if($("#eksiBakiyeUyari").attr('checked'))
            eksiBakiyeUyarii = 1;

        if($("#eksiBakiyeIzin").attr('checked'))
            eksiBakiyeIzini = 1;

        if(kontrolEt($("#kod").val(), $("#tanim").val()))
            kaydet($("#kod").val(), $("#tanim").val(), $("#personel :selected").val(), durum, eksiBakiyeUyari, eksiBakiyeIzin);
    });

    $("#iptal").click(function (){
        $("#kaydet").show();
        $("#guncelle").hide();
    });
    $(".sayfaLink").live("click" ,sayfaClick);

});


var satirno;
function tabloDoldur(kodAra,tanimAra,personelAra)
{
    satirno = 0;

    $.ajax({
        type: 'GET',
        url: 'depoAjax.php',
        data: 'tabloDoldur=1&kodAra='+kodAra+'&tanimAra='+tanimAra+'&personelAra='+personelAra,
        success: function(cevap) {

            $("#sayfaNo").html('');
            $('#tabloGovde').html('');
            if(cevap != "")
            {
                var parcalar = cevap.split("|");
                var uzunluk = parcalar.length-1;
                for (var i=0;i<uzunluk;i=i+7)
                {
                    var satirID = "satir"+satirno;
                    $('#tabloGovde').append('<tr id="'+satirID+'"></tr>');
                    $('#'+satirID).append('<input type="hidden" id="ID" value="'+parcalar[i]+'">');
                    $('#'+satirID).append('<td>'+parcalar[i+1]+'</td>');
                    $('#'+satirID).append('<td>'+parcalar[i+2]+'</td>');
                    $('#'+satirID).append('<input type="hidden" value="'+parcalar[i+3]+'">');
                    $('#'+satirID).append('<input type="hidden" value="'+parcalar[i+4]+'">');
                    $('#'+satirID).append('<input type="hidden" value="'+parcalar[i+5]+'">');
                    $('#'+satirID).append('<td>'+parcalar[i+6]+'</td>');
                    $('#'+satirID).append('<td><input type="button" class="button sec" name="sec" id="sec" value="Güncelle" style="width: 50%">\n\
                        <input type="button" class="button sil" name="sil" id="sil" value="Sil" style="width: 40%"></td>');
                    satirno++;
                }
                $('tbody tr:odd', $("#tablo")).addClass('acikSatir');
                $('tbody tr:even', $("#tablo")).addClass('koyuSatir');
                sayfaNoEkle();
            }else
            {
                $('#tabloGovde').append('<tr class="koyuSatir"><td colspan="5" align="center">Kayıt Bulunamadı.</td></tr>');
            }
        }

    });
}

function kontrolEt(deger1, deger2)
{
    $(".uyari").html("");
    if(deger1=="" || deger2 =="")
    {
		if(deger1=="")
			$('#kodUyari').html('*');
		if(deger2=="")
			$('#tanimUyari').html('*');
        $('#anaUyari').html("*Lutfen gerekli alanlari doldurunuz!!!");
        return false;
    }
    else
        return true;
}

function guncelle(idi, kod, tanim, personel, durum, eksiBakiyeUyari, eksiBakiyeIzin)
{
    $.ajax({
        type: 'GET',
        url: 'depoAjax.php',
        data: 'guncelle=1&id='+idi+'&kod='+kod+'&tanim='+tanim+'&personel='+personel+'&durum='+durum+'&eksiBakiyeUyari='+eksiBakiyeUyari+'&eksiBakiyeIzin='+eksiBakiyeIzin,
        success: function(cevap) {
            if(cevap != "")
            {
                alert(cevap);
                temizle();
            }
        }
    });
}

function kaydet(kod, tanim, personel, durum, eksiBakiyeUyari, eksiBakiyeIzin)
{
    $.ajax({
        type: 'GET',
        url: 'depoAjax.php',
        data: 'kaydet=1&kod='+kod+'&tanim='+tanim+'&personel='+personel+'&durum='+durum
             +'&eksiBakiyeUyari='+eksiBakiyeUyari+'&eksiBakiyeIzin='+eksiBakiyeIzin,
        success: function(cevap) {
            if(cevap != "")
            {
                alert(cevap);
                temizle();
            }
        }
    });
}

function sil(idi)
{
    $.ajax({
        type: 'GET',
        url: 'depoAjax.php',
        data: 'sil=1&id='+idi,
        success: function(cevap) {
            if(cevap != "")
            {
                alert(cevap);
                temizle();
            }
        }
    });
}

function temizle()
{
    $(".uyari").html("");
    $("#kodAra").val("");
    $("#tanimAra").val("");
    $("#personelAra").val("");
    $("#kaydet").show();
    $("#guncelle").hide();
    $('#form').each (function(){
        this.reset();
    });
    tabloDoldur("","","");
}