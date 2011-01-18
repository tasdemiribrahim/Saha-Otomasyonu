<?php
require_once ('../common/db_connect.php');
declare(encoding='UTF-8');
if(isset($_GET["tabloDoldur"]))
{
    $kodGir = temizSayi($_GET["kodGir"]);
    $tanimGir = temizYazi($_GET["tanimGir"]);
    $turGir = temizSayi($_GET["turGir"]);

    if($kodGir!="" || $tanimGir!="" || $turGir!="")
    {
        $sorgu = "SELECT * FROM konum WHERE kod LIKE '$kodGir%' AND tanim LIKE '$tanimGir%'";
        if($turGir!="")
            $sorgu .= " AND tur='$turGir'";
		foreach ($db->query($sorgu) as $satir)
            echo $satir['ID']."|".$satir['kod']."|".$satir['tanim']."|".$satir['adres']."|".$satir['telefon']."|".$satir['vergiNo']."|".$satir['vergiDaire']."|".$satir['tur']."|";
    }
    else
    {
		foreach ($db->query("SELECT * FROM konum") as $satir)
            echo $satir['ID']."|".$satir['kod']."|".$satir['tanim']."|".$satir['adres']."|".$satir['telefon']."|".$satir['vergiNo']."|".$satir['vergiDaire']."|".$satir['tur']."|";
    }
}

if(isset($_GET["guncelle"]))
{
    $id = temizSayi($_GET["id"]);
    $kod = temizSayi($_GET["kod"]);
    $tanim = temizYazi($_GET["tanim"]);
    $adres = temizYazi($_GET["adres"]);
    $telefon = temizSayi($_GET["telefon"]);
    $vergiNo = temizSayi($_GET["vergiNo"]);
    $vergiDaire = temizYazi($_GET["vergiDaire"]);
    $tur = temizSayi($_GET["tur"]);

	$db->exec("UPDATE konum SET kod='$kod', tanim='$tanim', adres='$adres', telefon='$telefon', vergiNo='$vergiNo', vergiDaire='$vergiDaire', tur='$tur' WHERE ID='$id' ");
}

if(isset($_GET["kaydet"]))
{
    $kod = temizSayi($_GET["kod"]);
    $tanim = temizYazi($_GET["tanim"]);
    $adres = temizYazi($_GET["adres"]);
    $telefon = temizSayi($_GET["telefon"]);
    $vergiNo = temizYazi($_GET["vergiNo"]);
    $vergiDaire = temizYazi($_GET["vergiDaire"]);
    $tur = temizSayi($_GET["tur"]);
	
	$sth = $db->prepare("SELECT COUNT(ID) AS say FROM konum WHERE kod = '$kod'");
	if ($sth->execute())
	{
		$record = $sth->fetch(PDO::FETCH_ASSOC);
		if($record['say'] > 0)
       		echo "Bu kodda bir kayıt var. Lütfen başka bir kod giriniz.";
		else
		{
			$db->exec("INSERT INTO konum(kod,tanim,adres,telefon,vergiNo,vergiDaire,tur) VALUES('$kod','$tanim','$adres','$telefon','$vergiNo','$vergiDaire','$tur')");
			$sth1 = $db->prepare("SELECT ID FROM konum WHERE kod='$kod'");
			$sth1->execute();
			$konumRow = $sth->fetch(PDO::FETCH_ASSOC);
			$konumID = $konumRow['ID'];
			$tarih = date('Y-m-d');		
			foreach ($db->query("SELECT satisFiyat, ID from stok WHERE stokTur=$MAMUL") as $satir)	
			{
				$urunSatisFiyat = $satir['satisFiyat'];
				if($urunSatisFiyat!=0 && $urunSatisFiyat!="")
					$db->exec("INSERT INTO fiyat(stokID,konumID,fiyat,tarih) VALUES('$satir[ID]','$konumID','$urunSatisFiyat','$tarih')");
			}
		}
	}
}

if(isset($_GET["sil"]))
{
    $id = temizSayi($_GET["id"]);
	$db->exec("DELETE FROM konum WHERE ID='$id'");
	$db->exec("DELETE FROM fiyat WHERE konumID='$id'");
}