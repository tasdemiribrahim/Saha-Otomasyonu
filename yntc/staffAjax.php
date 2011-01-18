<?php
require_once ('../common/db_connect.php');
declare(encoding='UTF-8');

$root = new SimpleXMLElement('../common/sabitler.xml', NULL, true);
$gizliAnahtar=$root->gizliAnahtar;

if(isset($_GET["sifre"]))
	$sifre = hash_hmac('ripemd160',$_GET["sifre"], $gizliAnahtar);

if(isset($_GET["tabloDoldur"]))
{
    $kodGir = temizSayi($_GET["kodGir"]);
    $tanimGir = temizYazi($_GET["tanimGir"]);
	$sorgu="SELECT * FROM personel";
    if($kodGir!="" || $tanimGir!="")
		$sorgu.=" WHERE ID LIKE '$kodGir%' AND tanim LIKE '$tanimGir%'";
	foreach ($db->query($sorgu) as $satir)
		echo $satir['ID']."|".$satir['tanim']."|".$satir['sifre']."|";
}

if(isset($_GET["guncelle"]))
{
    $kod = temizSayi($_GET["kod"]);
    $tanim = temizYazi($_GET["tanim"]);
    if($db->exec("UPDATE personel SET tanim='$tanim', sifre='$sifre' WHERE ID='$kod'") ===false)
        echo "false";
    else
        echo "true";
}

if(isset($_GET["kaydet"]))
{
    $tanim = temizYazi($_GET["tanim"]);
    if($db->exec("INSERT INTO personel(tanim,sifre) VALUES('$tanim','$sifre')") ===false)
        echo "false";
    else
        echo "true";
}