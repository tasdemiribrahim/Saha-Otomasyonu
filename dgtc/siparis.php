<?php
$kontol=0;
include ('../common/db_connect.php');
if (isset($yeniParti))
    {
      $sip=$_POST['siparisid'];
      $parca=split("[/]", $sip);
      $update = "UPDATE siparis SET durum='1' WHERE ID=$parca[0] ";
      mysql_query($update);
    }

    $siparisSql = mysql_query("SELECT *FROM siparis WHERE durum='0' AND personelID=" . $_COOKIE["fso_kullanici"] );
      while ($okuSiparis = mysql_fetch_assoc($siparisSql)) {
          $kontrol=1;
      }

      $personelSql = mysql_query("SELECT tanim FROM personel WHERE ID=" . $_COOKIE["fso_kullanici"]);
      while ($okuPersonel = mysql_fetch_assoc($personelSql)) {
        $personelName = $okuPersonel[tanim];
      }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<head>
  <title>KUYAS Saha Otomasyonu</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <style>
    table {
      font-size:12px;
    }
  </style>
  <meta http-equiv="refresh" content="30; url=siparis.php">
</head>

<body>
<table cellpadding="1" cellspacing="0">
  <tr><td>Personel: <b><?php echo $personelName; ?></b> </td>
  <td><b><?php if($kontrol==1){echo " <img src=\"gif.gif\"/> ";}?></b> </td>
  </tr>
    <?
    $siparisSql = mysql_query("SELECT * FROM siparis WHERE durum='0' AND personelID=" . $_COOKIE["fso_kullanici"]);
      while ($okuSiparis = mysql_fetch_assoc($siparisSql)) 
      {
        $urunID = $okuSiparis[stokID];
        $ID = $okuSiparis[ID];
        $miktar = $okuSiparis[miktar];
        $konum=$okuSiparis[konumID];
        $okuUrun = mysql_fetch_assoc(mysql_query("SELECT *FROM stok WHERE ID=$urunID"));
        $urunName=$okuUrun[tanim1];
        $birim = $okuUrun[temelOlcuBirim];
        $konumSql = mysql_query("SELECT *FROM konum WHERE ID=$konum");
        while ($okuKonum = mysql_fetch_assoc($konumSql)) 
        {
              $tel = $okuKonum[telefon];
              $adres = $okuKonum[adres];
              $isim = $okuKonum[tanim];

        }


        echo " <form method=\"post\">
    <input type=\"hidden\" name=\"siparisid\" size=\"5\" value=";echo$ID;echo"/>
    <tr>
    <td>
    <table border=\"0\" width=\"250\" cellpadding=\"1\" cellspacing=\"1\" style=\"color:white\">
         <tr bgcolor=\"#5f4f4f\">
          <td>Miktar</td>
          <td>";echo $miktar; echo "&nbsp;"; echo $birim ;echo "</td>
         </tr>

        <tr bgcolor=\"#5f4f4f\">
          <td>Ürün</td>
          <td>";echo $urunName;echo"</td></tr>
          
        </tr>
          <tr bgcolor=\"#5f4f4f\">
          <td>Alıcı</td>
          <td>";echo $isim;echo"</td></tr>

        </tr>
         <tr bgcolor=\"#5f4f4f\">
          <td>Tel</td>
          <td>";echo $tel;echo "</td>
        </tr>
        <tr bgcolor=\"#5f4f4f\">
          <td>Adres</td>
          <td>";echo $adres;echo "</td>
        </tr>
        <tr>
          <td colspan=\"3\" align=right>
            <input type=\"submit\" name=\"yeniParti\" value=\"Tamam\" />
          </td>
        </tr> </table> <tr></tr>
        <tr></tr></td>
  </tr>
  </form>" ;

      }

      ?>

</table>

</body>
</html>
