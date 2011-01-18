$(document).ready(function()
{	
    $.ajaxSetup({
        type: 'POST',
        timeout: 5000,
        url: 'irsaliyeAjax.php',
        error: function(xhr) {
    		$("#anaUyari").html('Hata: ' + xhr.status + ' ' + xhr.statusText + '<br>' + 'Teknik bir hata oluştu.Sunucuları kontrol edin!!!');
        }
    });  
	
    var tarihi = new Date();
    var temp = tarihi.getDate();
    var gun = (temp < 10) ? '0' + temp : temp;
    temp = tarihi.getMonth() + 1;
    var ay = (temp < 10) ? '0' + temp : temp;
    temp = tarihi.getYear();
    var yil = (temp < 1000) ? temp + 1900 : temp;	
  $("#tarih").val(yil+"-"+ay+"-"+gun);
  $("#teslimTarih").val(yil+"-"+ay+"-"+gun);
    
  if($("#ID").val()==0)
    yeniID(0);
  else IDGetir($("#ID").val());
  $('#guncelle').hide();
  $('#ekle').show();
  $('#iptal').hide();
  $('.donusum').hide();
   
  $("#musteri").focus(); 
  
// Sayfa linklerinden birine t�klan�nca
  $(".sayfaLink").live("click" ,sayfaClick);
  
// İkinci birim textbox veya combobox değişirse uygulanır   
  $(".donusum").change(donusum);
    
// Stok textbox de�i�ince
  $("#stok").change(function (){birimGetir($("#stok").val());});
  
// Tarih se�icileri
  $("#tarih").click(function(){displayDatePicker('tarih');});
  $("#teslimTarih").click(function(){displayDatePicker('teslimTarih');});
  
// M��teri tahminleri
 $("#musteri").keyup(function(){auto('musteri','irsaliyeAjax.php?part=');});
  
// ID textbox yazılınca   
  $("#ID").change(function (){IDGetir($("#ID").val());});
  
// Yeni irsaliye ID   
  $(".irTur").live("change" ,function (){yeniID(1);});
  
  $("#iptal").click(function (){IDGetir($("#ID").val());});
  
// Ekle butonuna basılırsa uygulanır 
  $("#ekle").click(ekle); 
  
// Guncelle butonuna basılırsa uygulanır 
  $("#guncelle").click(guncelle);
  
  $("#irsaliyeSil").click(sil);
  
});

///////////////////////////////////////////////
function auto(id,url){setAutoComplete(id, "results", url);}
//////////////////////////////////////////////
function radioSec(gelen){$("h2 input:radio:"+gelen).attr("checked","checked");}
/////////////////////////////////////////////
function donusum()
{   
    donID=$("#ikinciBirim").val();
    donMiktar=$("#ikinciMiktar").val();
    var re = /^[0-9]+$/;
    if (!donMiktar.match(re)) 
      $("#detayUyari").html("*Lütfen miktara sayısal bir değer giriniz!");
    else
    {
	  $("#detayUyari").html("");
      $.ajax({
        data: 'donID='+donID+'&donMiktar='+donMiktar,
        success: function(cevap) 
        { 
          $("#miktar").val(cevap);
        }
      });
    }
}
/////////////////////////////////////////////
function sil()
{
	iptalID=$("#ID").val();
	var silinsinMi = confirm(iptalID + " Nolu İrsaliyeyi Silmek İstediğinizden Emin Misiniz?");
    if(silinsinMi)
		$.ajax({ 
		  data: 'iptalID='+iptalID,
		  success: function() 
		  {
			IDGetir(iptalID);
		  }
		}); 
}
///////////////////////////////////////////
function detaySil(gelen)
{
  $.ajax({
    data: 'detaySil='+gelen,
    success: function() 
    {
      IDGetir($("#ID").val());
    }
  });
}
///////////////////////////////////////////
function detayGuncelle(gelen)
{
  $('#guncelle').show();
  $('#ekle').hide();
  $('#iptal').show();
  $(".uyari").html("");
  $('#irDetay').val(gelen);
  $.ajax({
    data: 'guncelleGetir='+gelen,
    success: function(cevap) 
    { 
      var parcalar= cevap.split("|");
      birimGetir(parcalar[0]);
      $("#stok").disable().val(parcalar[0]);
      $("#depo").disable().val(parcalar[1]);
      $("#miktar").val(parcalar[2]);
      $("#miktar").focus(); 
    }
  });
}
///////////////////////////////////////////
function ekle()
{  
  stokEkle=$("#stok").val();
  miktarEkle=$("#miktar").val();
  depoEkle=$("#depo").val();
  IDEkle=$("#ID").val();
  musteriEkle=$("#musteri").val();
  tarihEkle=$("#tarih").val();
  teslimTarihEkle=$("#teslimTarih").val();
  turEkle = $("h2 input:radio:checked").val();
  var re = /^[0-9]+$/;
  $(".uyari").html("");
  if(IDEkle=="" || musteriEkle=="" || tarihEkle=="" || teslimTarihEkle=="")
  {
	if(IDEkle=="")
		$("#IDUyari").html("*");
	if(musteriEkle=="")
		$("#musteriUyari").html("*");
	if(tarihEkle=="")
		$("#tarihUyari").html("*");
	if(teslimTarihEkle=="")
		$("#teslimUyari").html("*");
    $("#anaUyari").html("*Lütfen önce ana bilgileri giriniz!!!");
  }
  else if(!miktarEkle.match(re) || stokEkle=="" || depoEkle=="" || miktarEkle=="")
  {
	if(stokEkle=="")
		$("#stokUyari").html("*");
	if(depoEkle=="")
		$("#depoUyari").html("*");
	if(!miktarEkle.match(re) || miktarEkle=="")
		$("#miktarUyari").html("*");
	$("#detayUyari").html("*Lütfen Gerekli Yerleri Doldurunuz!!!");
  }
  else
  {
  $("#ekle").disable();
  var adres="ekleID="+IDEkle+"&musteri="+musteriEkle+"&tarih="+tarihEkle+"&teslimTarih="+teslimTarihEkle+"&ekleStok="+stokEkle+"&depo="+depoEkle+"&miktar="+miktarEkle+"&irsaliyeTur="+turEkle+"&ekstra=";
  $(".ekstra").each(function (fr) {
    adres+=$(this).val()+"|";
  });
  $.ajax({
	data: adres,
	success: function(cevap) 
	{
	  if(cevap=="uyari")
			$("#IDUyari").html("*Bu irsaliye numarası diğer tür için ayrılmıştır!");
	  else if(cevap=="uyari1")
	  {
		alert("Depodaki ürün sınırını aştınız!Depo izin verdiğinden işleme devam ediliyor!");
		IDGetir($("#ID").val());
	  }
	  else if(cevap=="uyari2")
		alert("Depodaki ürün sınırını aştığından işlem iptal edildi!");
	  else
		IDGetir($("#ID").val());
  		$("#ekle").enable();
	}
  });
}
}
///////////////////////////////////////////
function yeniID(gelen)
{ 
  $.ajax({
    data: 'yeniTur=1',
    success: function(cevap) 
    {
	  cevap++;
      $("#ID").val(cevap);
	  if(gelen)
		IDGetir(cevap);
    }
  });
}
///////////////////////////////////////////
function birimGetir(gelen) 
{
  $('.donusum').hide();
  $.ajax({
    data: 'stok='+gelen,
    async: false,
    success: function(cevap) 
    {
      if(cevap!="")
      {
        var parcalar= cevap.split("|");
        $("#birimLabel").html(parcalar[0]);
        $("#miktar").val("");
        if(parcalar[1]!=0)
        {
          $('.donusum').show();
          $("#ikinciMiktar").val("");
          $("#ikinciBirim").html(parcalar[1]);
        }
      }
      $("#depo").val(parcalar[2]);
    }
  });
}

///////////////////////////////////////////////
function IDGetir(gelen) 
{ 
  $("#stok").enable();
  $("#depo").enable();
  $('#guncelle').hide();
  $('#ekle').show();
  $('#iptal').hide();
  $('.donusum').hide();
  $("#sayfaNo").html("");
  $(".uyari").html("");
  $.ajax({
    data: 'ID='+gelen,
    success: function(cevap) 
    {
		var satirno = 0;
		$i=0;
		var parcalar= cevap.split("|");
		$("#musteri").val(parcalar[$i++]);
		$("#tarih").val(parcalar[$i++]);
		$("#teslimTarih").val(parcalar[$i++]);
		if(parcalar[$i]==1){radioSec("last");}
		if(parcalar[$i++]==0){radioSec("first");}
		$(".ekstra").each(function (fr) {
		  $(this).val(parcalar[$i++]);
		 });
		dongu=parcalar[$i];
		$("#tabloGovde").html('');
		for($l=1;$l<=dongu;$l++)
		{ 
		  if(satirno % 2 == 0)
			sinif = "acikSatir";
		  else
			sinif = "koyuSatir";
		
		  var satirID = "satir"+satirno;
		  satirno++;
		  $('#tabloGovde').append('<tr id="'+satirID+'" class="'+sinif+'" ></tr>');
		  $('#'+satirID).append('<td><input type="button" class="button" title="Güncellemek için tıklayın" onClick="detayGuncelle('+parcalar[++$i]+')" value="'+satirno+'"></td>')
		  .append('<td>'+parcalar[++$i]+'</td>')
		  .append('<td>'+parcalar[++$i]+'</td>')
		  .append('<td>'+parcalar[++$i]+'</td>')
		  .append('<td>'+parcalar[++$i]+'</td>')
		  .append('<td><input type="button" class="button" onClick="detaySil('+parcalar[++$i-5]+')" value="Sil"></td>');
		  $("#sil").show();
		}
		sayfaNoEkle();
		$("#miktar").val("");
		$("#stok").val("");
		$("#depo").val("0");
		$("#birimLabel").html("");
    }
  });
}

//////////////////////////////////////////
function guncelle()
{
    detay = $("#irDetay").val();
    stokDetay = $("#stok").val();
    miktarDetay = $("#miktar").val();
    depoDetay = $("#depo").val();
    turDetay = $("h2 input:radio:checked").val();
    var re = /^[0-9]+$/;
    if (!miktarDetay.match(re) || miktarDetay=="") 
	  $("#detayUyari").html("*Lütfen miktara sayısal bir değer giriniz!");
    else
    {
		$.ajax({
		  data: 'guncelle='+detay+'&stokG='+stokDetay+'&miktarG='+miktarDetay+'&depoG='+depoDetay+'&turG='+turDetay,
		  success: function(cevap) 
		  {
			if(cevap=="uyari1")
			{
			  alert("Depodaki ürün sınırını aştınız!Depo izin verdiğinden işleme devam ediliyor!");
			  IDGetir($("#ID").val());
			}
			else if(cevap=="uyari2")
			  alert("Depodaki ürün sınırını aşan işlemler iptal edildi!");
			else
			  IDGetir($("#ID").val());
		  }
		});
    }
}