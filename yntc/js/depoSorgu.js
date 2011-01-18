var kayitIstegiSayisi=0;	
var slaytDizini;
var current=0;
$(document).ready(function(){
    no=0;	
    $.ajaxSetup({
        type: 'GET',
        timeout: 5000,
        url: 'depoSorguAjax.php',
        error: function(xhr) {
    		$("#anaUyari").html('Hata: ' + xhr.status + ' ' + xhr.statusText + '<br>' + 'Teknik bir hata olustu.Sunuculari kontrol edin!!!');
			$("#kaydet").enable();
        }
    });
	
   depoSorgulama();
   arsivGetir();
   
    var tarihi = new Date();
    var temp = tarihi.getDate();
    var gun = (temp < 10) ? '0' + temp : temp;
    temp = tarihi.getMonth() + 1;
    var ay = (temp < 10) ? '0' + temp : temp;
    temp = tarihi.getYear();
    var yil = (temp < 1000) ? temp + 1900 : temp;
   $("h2").html("Depo Sorgu: "+yil+"-"+ay+"-"+gun);
   
   $("#grafikGetirLink").live("click",grafikGetir);
   
   $("#slayKapatButton").live("click",function (){
		slaytKapat(450,'depoSorguSlayt');					   
	});
   
   $("#sonrakiSlayt").live("click",function (){
		current=(current+1)%slaytDizini.length;
		grafikGuvenliGetir(slaytDizini[current]);
	});
   
   $("#oncekiSlayt").live("click",function (){
		current=(slaytDizini.length+current-1)%slaytDizini.length;
		grafikGuvenliGetir(slaytDizini[current]);
	});
    
   $(".sorgu").change(depoSorgulama); 
   $("#kaydet").click(function (){
	   kayitIstegiSayisi=0;				
       metin="";
	   MetinBaslik=$(".tbl_baslik th:nth-child(1)").html()+"="+
	   $(".tbl_baslik th:nth-child(2)").html()+"="+
	   $(".tbl_baslik th:nth-child(3)").html()+"="+
	   $(".tbl_baslik th:nth-child(4)").html()+"="+
       $(".tbl_baslik th:nth-child(5)").html();
      for(i=1;i<no;i++)
      {
        depo=$("#tablo tbody tr:nth-child("+i+") td:nth-child(1)").html();
        personel=$("#tablo tbody tr:nth-child("+i+") td:nth-child(2)").html();
        stok=$("#tablo tbody tr:nth-child("+i+") td:nth-child(3)").html();
        miktar=$("#tablo tbody tr:nth-child("+i+") td:nth-child(4)").html();
        birim=$("#tablo tbody tr:nth-child("+i+") td:nth-child(5)").html();
        metin+=depo+"="+personel+"="+stok+"="+miktar+"="+birim+"|";
      }
      depo=$("#tablo tbody tr:nth-child("+no+") td:nth-child(1)").html();
      personel=$("#tablo tbody tr:nth-child("+no+") td:nth-child(2)").html();
      stok=$("#tablo tbody tr:nth-child("+no+") td:nth-child(3)").html();
      miktar=$("#tablo tbody tr:nth-child("+no+") td:nth-child(4)").html();
      birim=$("#tablo tbody tr:nth-child("+no+") td:nth-child(5)").html();
      metin+=depo+"="+personel+"="+stok+"="+miktar+"="+birim;
	  $(".arsivUyari").html("");
      if($("#pdf").attr('checked'))
		  $.ajax({
			type: 'POST',
			url: 'depoSorguKayit.php',
			data: 'pdf='+metin+'&baslik='+MetinBaslik,
			beforeSend: kayitOncesi,
			success: function(cevap)
			{
	  			$(".arsivUyari").append("Sorgu PDF Olarak Masaustunuze Kaydedildi!<br>");
				kayitSonrasi();
			}
			});
      if($("#excel").attr('checked'))
		  $.ajax({
			type: 'POST',
			url: 'depoSorguKayit.php',
			data: 'excel='+metin+'&baslik='+MetinBaslik,
			beforeSend: kayitOncesi,
			success: function(cevap)
			{
	  			$(".arsivUyari").append("Sorgu XLS Olarak Masaustunuze Kaydedildi!<br>");
				kayitSonrasi();
			}
			});
      if($("#xml").attr('checked'))
		  $.ajax({
			type: 'POST',
			url: 'depoSorguKayit.php',
			data: 'xml='+metin+'&baslik='+MetinBaslik,
			beforeSend: kayitOncesi,
			success: function(cevap)
			{
	  			$(".arsivUyari").append("Sorgu XML Olarak Masaustunuze Kaydedildi!<br>");
				kayitSonrasi();
			}
			});
      if($("#archive").attr('checked'))
		  $.ajax({
			type: 'POST',
			url: 'depoSorguKayit.php',
			data: 'archive='+metin+'&baslik='+MetinBaslik,
			beforeSend: kayitOncesi,
			success: function(cevap)
			{	
				if(cevap=="Hata1")
	  				$(".arsivUyari").append("ZIP Arsiv Kaydedilemedi!<br>");
				else
				{
					arsivGetir();
					$(".arsivUyari").append("Sorgu ZIP Olarak Sunucu Arsivine Kaydedildi!<br>");
					kayitSonrasi();
				}
			} 
			});
    });

   $(".sayfaLink").live("click" ,sayfaClick);
});

function kayitOncesi()
{
	kayitIstegiSayisi++;
	$("#kaydet").disable();
	$("#kaydet").val("     X     ");
}

function kayitSonrasi()
{
	kayitIstegiSayisi--;
	if(kayitIstegiSayisi==0)
	{
		$("#kaydet").enable();
		$("#kaydet").val("KAYDET");
	}
}

function depoSorgulama()
{ 
  no=0;
  depoSorDeg=$("#depoSor").val();
  stokSorDeg=$("#stokSor").val();
  turSorDeg=$("#turSor").val();
  $.ajax({
    data: 'depoSor='+depoSorDeg+'&stokSor='+stokSorDeg+'&turSor='+turSorDeg,
    success: function(cevap)
    { 
      $("#sayfaNo").html('');
      $('#tabloGovde').html("");
      var parcalar= cevap.split("|");
      $suankiKonum=$yeniDepoKonumu=0;
      $uzunluk=parcalar.length;
      while(($suankiKonum+1)<$uzunluk && parcalar[0]!=0)
      {   
          if($suankiKonum==$yeniDepoKonumu)
          { 
            $('#tabloGovde').append('<tr id="'+$suankiKonum+'" class="koyuSatir" ></tr>');
            $('#'+$suankiKonum).append('<td >'+parcalar[$suankiKonum+1]+'</td>');
            $('#'+$suankiKonum).append('<td >'+parcalar[$suankiKonum+2]+'</td>');
            $('#'+$suankiKonum).append('<td >'+parcalar[$suankiKonum+3]+'</td>');
            $('#'+$suankiKonum).append('<td >'+parcalar[$suankiKonum+4]+'</td>');
            $('#'+$suankiKonum).append('<td />');
            $yeniDepoKonumu=$yeniDepoKonumu+5+parcalar[$suankiKonum]*3;
            $suankiKonum=$suankiKonum+5;
            no++;
          }
          $('#tabloGovde').append('<tr id="'+$suankiKonum+'" class="acikSatir" ></tr>');
          $('#'+$suankiKonum).append('<td></td><td></td><td>'+parcalar[$suankiKonum]+'</td>');
          $('#'+$suankiKonum).append('<td>'+parcalar[$suankiKonum+1]+'</td>');
          $('#'+$suankiKonum).append('<td>'+parcalar[$suankiKonum+2]+'</td>');
          $suankiKonum=$suankiKonum+3;
          no++;
      }
      sayfaNoEkle();
    }
   });
}

function arsivGetir()
{
    $.ajax({
    data: 'ls=1',
    success: function(cevap)
    {
          $("#arsiv").html('');
          var parcalar= cevap.split("|");
          $("#arsiv").append('<tr id="tbl_baslik" class="koyuSatir" ><td colspan=5>ARSIV</td></tr>');
          j=0;
		  var uz=parcalar.length;
          for(i=0;i<uz;i++)
          { 
            if(i%5==0)
              {  
			  	j++;
                $("#arsiv").append('<tr id="satir'+j+'" ></tr>');
              }
            $('#satir'+j).append('<td ><a class="arsivIndir" href="./depo_sorgu_arsiv/'+parcalar[i]+'.zip">'+parcalar[i]+'</a></td>');
          }
    }
    });
}

grafikGetirHolderJS="";
function grafikGetir()
{
	grafikGetirHolderJS=$("#grafikGetirHolder").html();
	$("#grafikGetirHolder").html("Yukleniyor...");
	$(".arsivUyari").html("");
	metin="";
	$yeniDepo=null;
	for(i=1;i<=no;i++)
	{
		depo=$("#tablo tbody tr:nth-child("+i+") td:nth-child(1)").html();
		if(depo!="")
			yeniDepo=depo;
		else
		{
			stok=$("#tablo tbody tr:nth-child("+i+") td:nth-child(3)").html();
			miktar=$("#tablo tbody tr:nth-child("+i+") td:nth-child(4)").html();
			if(miktar>0)
				metin+=stok+"="+yeniDepo+"="+miktar+"|";
		}
	}
	$.ajax({
		type: 'POST',
		url: 'depoSorguKayit.php',
		data: 'grafik='+metin,
		success: function(cevap)
		{	
       		slaytDizini= cevap.split("|");
			  $('<div></div>').css({
				position: 'absolute',
				left: $("#grafikGetirHolder").offset().left,
				top: $("#grafikGetirHolder").offset().top,
				display: 'none'
			  })
			  .addClass('depoSorguSlayt')
			  .html('<img id="slaytResimi" src="" />'+
					'<div id="buttonBar" style="clear: both; text-align: center;">'+
					'<button type="button" id="oncekiSlayt" class="button" style="width: 45%;"><<</button>'+
      				'<button type="button" id="slayKapatButton" class="button" style="width: 10%;">X</button>'+
      				'<button type="button" id="sonrakiSlayt" class="button" style="width: 45%;">>></button>'+
    				'</div>'
					)
			  .appendTo('body')
			  .fadeIn();
			  grafikGuvenliGetir(slaytDizini[current]);
		} 
		});
}

function grafikGuvenliGetir(file)
{
	$.ajax({
		type: 'GET',
		url: 'depoSorguAjax.php',
		data: 'png='+file,
		success: function(cevap)
		{
			if(cevap)
				$("#slaytResimi").attr("src",cevap);
		}
	});
}

function slaytKapat(sure,sinif)
{
    current=0;
	$("."+sinif).fadeOut(sure,function(){$(this).remove();});
	$("#grafikGetirHolder").html(grafikGetirHolderJS);
}