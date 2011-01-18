$(document).ready(function(){
    $.ajaxSetup({
        type: 'GET',
        timeout: 5000,
        url: 'stokAjax.php',
        error: function(xhr) {
    		$("#anaUyari").html('Hata: ' + xhr.status + ' ' + xhr.statusText + '<br>' + 'Teknik bir hata oluştu.Sunucuları kontrol edin!!!');
        }
    });
	
   stokAra();
   $(".arama").keyup(stokAra);
   $(".aramaSelect").change(stokAra);
   $(".sayfaLink").live("click" ,sayfaClick);
});

function stokAra()
{
    stokAraKodu=$("#stokAraKod").val();
    stokAraTuru=$("#stokAraTur").val();
    stokAraDeposu=$("#stokAraDepo").val();
    stokAraTanimi=$("#stokAraTanim").val();
    $.ajax({
        data: 'stokAraKod='+stokAraKodu+'&stokAraTur='+stokAraTuru+'&stokAraDepo='+stokAraDeposu+'&stokAraTanim='+stokAraTanimi,
        success: function(cevap)
        {
          $("#sayfaNo").html('');
          $('#tabloGovde').html('');

          var satirno = 0;
          var parcalar= cevap.split("|");
          $dongu=parcalar[0];
		  
          $i=1;
          for($l=1;$l<=$dongu;$l++)
          {
            if(satirno % 2 == 0)
              sinif = "acikSatir";
            else
              sinif = "koyuSatir";

            var satirID = "satir"+satirno;
            $('#tabloGovde').append('<tr id="'+satirID+'" class="'+sinif+'" ></tr>');
            $('#'+satirID).append('<td><input type=button class="button" onclick="cikis('+parcalar[$i]+')" value='+parcalar[$i]+'></td>');
            $('#'+satirID).append('<td>'+parcalar[$i+1]+'</td>');
            $('#'+satirID).append('<td>'+parcalar[$i+2]+'</td>');
            $('#'+satirID).append('<td>'+parcalar[$i+3]+'</td>');
            $('#'+satirID).append('<td>'+parcalar[$i+4]+'</td>');
            $i=$i+5;
            satirno++;
          }
          sayfaNoEkle();
         }
    });

}

function cikis(kod)
{
   window.opener.document.location='stok.php?araKod='+kod;
   window.close()
}