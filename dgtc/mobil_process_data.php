<?php

include ('../common/db_connect.php');

$xmlStr = $HTTP_RAW_POST_DATA;
$xml = simplexml_load_string($xmlStr);

$attrs = $xml->attributes();
$dagiticiNo = $attrs['dagiticiNo'];
$hatas;
$tarih = date("Y-m-d H:i:s");
$i = 0.0;
$alinanPara = $attrs['alinanPara'];
$eklenenBorc = 0.0;
$iade_miktar = 0.0;
$teslimat_miktar = 0.0;

foreach($xml->root as $root) {

  // urunun fiyati aliniyor
  $fiyatSql = mysql_query("SELECT fiyat FROM fiyat WHERE konum_kod =$root->konum AND urun_kod = $root->urun");
  while($oku = mysql_fetch_assoc($fiyatSql)){

    $fiyat = $oku[fiyat];
  }  

  if(!empty($root->iade)) {

     $ekleSql = "INSERT INTO hareket VALUES (NULL,$root->konum,$dagiticiNo,$root->urun,$root->iade,'$tarih',1)";
     $retVal = mysql_query($ekleSql);
     $iade = $root->iade;
  }

  if(!empty($root->miktar)) {

     $ekleSql = "INSERT INTO hareket VALUES (NULL,$root->konum,$dagiticiNo,$root->urun,$root->miktar,'$tarih',0)";
     $retVal = mysql_query($ekleSql);
     $miktar = $root->miktar;
  }

  $borcEkleSql = "INSERT INTO borc VALUES($tarih,$root->konum,$alinanMiktar ,($teslimat_miktar-$iade_miktar)*$fiyat)";
    
 // birakilan urun borcunu ve alınan ücreti ekle
  $borcEkle = mysql_query("INSERT INTO borc VALUES('$datetime',$konum, $alinanPara, $eklenenBorc)");
}
?>