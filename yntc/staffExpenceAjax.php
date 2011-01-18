<?php
require_once ('../common/db_connect.php');
declare(encoding='UTF-8');
if(isset($_GET["kayitAra"]))
{
    $kodAra = temizSayi($_GET["kodAra"]);
    $tanimAra = temizYazi($_GET["tanimAra"]);
    $miktarAra = temizSayi($_GET["miktarAra"]);
    $birimAra = temizYazi($_GET["birimAra"]);
    $tarihAra = temizYazi($_GET["tarihAra"]);
	foreach ($db->query("SELECT g.ID, p.tanim, g.miktar, g.birim, g.tarih, g.personelID  FROM personelGider g, personel p WHERE g.personelID=p.ID AND g.ID LIKE '$kodAra%' AND p.tanim LIKE '$tanimAra%' AND g.miktar LIKE '$miktarAra%' AND g.birim LIKE '$birimAra%' AND g.tarih LIKE '$tarihAra%' ORDER BY g.ID ") as $satir)
    {
		$sth = $db->prepare("SELECT birimKisaltma FROM birim WHERE ID='$satir[birim]'");
		$sth->execute();
		$satirKisaltma = $sth->fetchColumn();
        echo $satir['ID']."|".$satir['tanim']."|".$satir['miktar']."|".$satirKisaltma."|".$satir['tarih']."|".$satir['personelID']."|";
    }
}

if(isset($_GET["kaydet"]))
{
    $personelID = temizSayi($_GET["personelID"]);
    $miktar = temizSayi($_GET["miktar"]);
    $birim = temizYazi($_GET["birim"]);
    $tarih = temizYazi($_GET["tarih"]);
    $db->exec("INSERT INTO personelGider(personelID,miktar,birim,tarih) VALUES('$personelID','$miktar','$birim','$tarih')");
    echo "true";
}

if(isset($_GET["guncelle"]))
{
    $personelID = temizSayi($_GET["personelID"]);
    $miktar = temizSayi($_GET["miktar"]);
    $birim = temizYazi($_GET["birim"]);
    $tarih = temizYazi($_GET["tarih"]);
    $id = temizSayi($_GET["id"]);
    $db->exec("UPDATE personelGider SET personelID='$personelID', miktar='$miktar', birim='$birim', tarih='$tarih' WHERE ID='$id'");
    echo "true";
}

if(isset($_GET["sil"]))
{
    $id = temizSayi($_GET["id"]);
    $db->exec("DELETE FROM personelGider WHERE ID='$id'");
    echo "true";
}