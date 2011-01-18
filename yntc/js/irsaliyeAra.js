$(document).ready(function(){

    $.ajaxSetup({
        type: 'GET',
        timeout: 5000,
        url: 'irsaliyeAjax.php',
        error: function(xhr) {
    		$("#anaUyari").html('Hata: ' + xhr.status + ' ' + xhr.statusText + '<br>' + 'Teknik bir hata oluştu.Sunucuları kontrol edin!!!');
        }
    });
	
   $("#araTarih").click(function(){displayDatePicker('araTarih');});
   $("#araTeslimTarih").click(function(){displayDatePicker('araTeslimTarih');});
   ara();
   $(".arama").keyup(function (){ara();});
   $(".tarih").blur(function (){ara();});
   $("#araTur").change(function (){ara();});

   $(".sayfaLink").live("click" ,sayfaClick);
});


function ara()
{
  $.ajax({
      data: 'araID='+$("#araID").val()+'&araMusteri='+$("#araMusteri").val()+'&araTarih='+$("#araTarih").val()+'&araTeslimTarih='+$("#araTeslimTarih").val()+'&araTur='+$("#araTur").val(),
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
          $('#'+satirID).append('<td><input type=button class="button" onclick="cikis('+parcalar[$i]+')" value='+parcalar[$i]+' style="width:50px"></td>');
          $('#'+satirID).append('<td>'+parcalar[$i+1]+'</td>');
          $('#'+satirID).append('<td>'+parcalar[$i+2]+'</td>');
          $('#'+satirID).append('<td>'+parcalar[$i+3]+'</td>');
          if(parcalar[$i+4]==0)
            $('#'+satirID).append('<td>Satış</td>');
          else
            $('#'+satirID).append('<td>Alış</td>');
          $i=$i+5;
          satirno++;
        }
        sayfaNoEkle();
      } 
  });
}

function cikis(ID)
{
   window.opener.document.location='irsaliye.php?ID='+ID;
   window.close()
}