$(document).ready(function()
{
    $.ajaxSetup({
        type: 'POST',
        timeout: 5000,
        url: 'tupcuAjax.php',
        error: function(xhr) {
    		$("#anaUyari").html('Hata: ' + xhr.status + ' ' + xhr.statusText + '<br>' + 'Teknik bir hata oluştu.Sunucuları kontrol edin!!!');
        }
    });
    
    var satirno = 0;
    var no = 0;
    
    $("#kaydet").click(function ()
    {
      seciliIsim=$("#isim").val();
      for(;no>0;no--)
      {
    	seciliDepo=$("#araTablo tbody tr:nth-child("+no+") td:nth-child(1)").html();
    	seciliPersonel=$("#araTablo tbody tr:nth-child("+no+") td:nth-child(2)").html();
    	seciliStok=$("#araTablo tbody tr:nth-child("+no+") td:nth-child(3)").html();
    	seciliMiktar=$("#araTablo tbody tr:nth-child("+no+") td:nth-child(4)").html();
    	$.ajax({
          data: '&isim='+seciliIsim+'&depoKaydet='+seciliDepo+'&personel='+seciliPersonel+'&stokKaydet='+seciliStok+'&miktar='+seciliMiktar
        });
      }
      alert("Kayıt işlemi başarıyla gerçekleşti.");
      window.close();
    });

    $(".sil").live("click" ,function ()
    {
      $(this).parent().parent().remove();
      no--;     
      
      $('tbody tr:odd', $("#araTablo")).removeClass('acikSatir').addClass('koyuSatir');
      $('tbody tr:even', $("#araTablo")).removeClass('koyuSatir').addClass('acikSatir');
    });

    $("#ekle").click(function ()
    {
      seciliDepo=$("#depo :selected").html();
      seciliPersonel=$("#personel :selected").html();
      seciliStok=$("#stok").val();
      seciliMiktar=$("#miktar").val();
      birim=$("#birimLabel").html();
	  $(".uyari").html("");
      if(seciliDepo=="" || seciliPersonel=="" || seciliStok=="" || !seciliMiktar.match(/^[0-9]+$/) ||  seciliMiktar=="")
	  {
		  if(seciliDepo=="")
		  	$("#depoUyari").html("*");
		  if(seciliPersonel=="")
		  	$("#personelUyari").html("*");
		  if(seciliStok=="")
		  	$("#stokUyari").html("*");
		  if(!seciliMiktar.match(/^[0-9]+$/) ||  seciliMiktar=="")
		  	$("#miktarUyari").html("*");
		  $("#anaUyari").html("*Lütfen Gerekli Yerleri Doldurunuz!!!");
	  }
      else
      {
        if(satirno % 2 == 0)
        sinif = "acikSatir";
        else
        sinif = "koyuSatir";

        var satirID = "satir"+satirno;
        satirno++;
        no++;
        $('#tabloGovde').append('<tr id="'+satirID+'"></tr>');
        $('#'+satirID).append('<td width=20%>'+seciliDepo+'</td>');
        $('#'+satirID).append('<td width=20%>'+seciliPersonel+'</td>'); 
        $('#'+satirID).append('<td width=20%>'+seciliStok+'</td>');
        $('#'+satirID).append('<td width=10%>'+seciliMiktar+'</td>');
        $('#'+satirID).append('<td width=20%>'+birim+'</td>');
        $('#'+satirID).append('<td width=10%><input type="button" class="button sil" name="sil" id="sil" value="Sil" style="width: 50%"></td>');
        $('tbody tr:odd', $("#araTablo")).removeClass('acikSatir').addClass('koyuSatir');
        $('tbody tr:even', $("#araTablo")).removeClass('koyuSatir').addClass('acikSatir');
      }
    });

	$("#depo").change(function (){ 
	  if($("#depo").val()!="")
		$.ajax({
		  data: 'depo='+$("#depo").val(),
		  success: function(cevap) {
				$("#personel").val(cevap);
		  }
		});
		else $("#personel").val("");
	});
	
	$("#stok").change(function (){
	  if($("#stok").val()!="")
		$.ajax({
		  data: 'stok='+$("#stok").val(),
		  success: function(cevap) {
			  if(cevap!="")
			  	$("#birimLabel").html(cevap);
			  else
			  	$("#birimLabel").html("Birim Yok");
		  }
		});
	  else $("#birimLabel").html("Birim Yok");
	});
});
