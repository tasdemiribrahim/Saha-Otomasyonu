<?php
require_once ('../common/db_connect.php');
declare(encoding='UTF-8');

if(isset($_GET["tabloDoldur"]))
{
    $kodAra = temizSayi($_GET["kodAra"]);
    $tanimAra = temizYazi($_GET["tanimAra"]);
    $personelAra = temizYazi($_GET["personelAra"]);

	foreach ($db->query("SELECT * FROM depo WHERE kod LIKE '$kodAra%' AND tanim LIKE '$tanimAra%'") as $satir)
	{
        if($satir['personel'] != 0)
        {
			$sth = $db->prepare("SELECT tanim FROM personel WHERE ID='$satir[personel]' AND tanim LIKE '$personelAra%'");
			if ($sth->execute())
			{
				$record = $sth->fetch(PDO::FETCH_ASSOC);
				if($record['tanim'])
					echo $satir['ID']."|".$satir['kod']."|".$satir['tanim']."|".$satir['durum']."|".$satir['eksiBakiyeUyari']."|".$satir['eksiBakiyeIzin']."|".$record['tanim']."|";
			}
        }
		else
			echo $satir['ID']."|".$satir['kod']."|".$satir['tanim']."|".$satir['durum']."|".$satir['eksiBakiyeUyari']."|".$satir['eksiBakiyeIzin']."||"; 
    }
}

if(isset($_GET["guncelle"]))
{
    $id = temizSayi($_GET["id"]);
    $kod = temizSayi($_GET["kod"]);
    $tanim = temizYazi($_GET["tanim"]);
    $personel = temizYazi($_GET["personel"]);
    $durum = temizSayi($_GET["durum"]);
    $eksiBakiyeUyari = temizSayi($_GET["eksiBakiyeUyari"]);
    $eksiBakiyeIzin = temizSayi($_GET["eksiBakiyeIzin"]);

    $sonuc = & $db->getOne("SELECT kod FROM depo WHERE kod='$kod' AND ID!='$id'");
    if($sonuc)
        echo "Aynı kod numarasına sahip başka bir kayıt Mevcut.Lütfen başka bir kod numarası giriniz.";
    else
	{
        $db->query("UPDATE depo SET kod='$kod', tanim='$tanim', personel='$personel', durum='$durum',eksiBakiyeUyari = '$eksiBakiyeUyari', eksiBakiyeIzin='$eksiBakiyeIzin' WHERE ID='$id'");
		echo "Güncelleme Başarılı!";
	}
}

if(isset($_GET["kaydet"]))
{
    $kod = temizSayi($_GET["kod"]);
    $tanim = temizYazi($_GET["tanim"]);
    $personel = temizYazi($_GET["personel"]);
    $durum = temizSayi($_GET["durum"]);
    $eksiBakiyeUyari = temizSayi($_GET["eksiBakiyeUyari"]);
    $eksiBakiyeIzin = temizSayi($_GET["eksiBakiyeIzin"]);

    $sonuc = $db->getOne("SELECT kod FROM depo WHERE kod='$kod'");
    if($sonuc)
        echo "Aynı kod numarasına sahip başka bir kayıt Mevcut.Lütfen başka bir kod numarası giriniz.";
    else
	{
        $db->query("INSERT INTO depo(kod, tanim, personel, durum, eksiBakiyeUyari, eksiBakiyeIzin) values('$kod','$tanim','$personel','$durum','$eksiBakiyeUyari','$eksiBakiyeIzin')");
		echo "Kayıt Başarılı!";
	}
}

if(isset($_GET["sil"]))
{
    $id = temizSayi($_GET["id"]);
    $db->exec("DELETE FROM depo WHERE ID='$id'");
}