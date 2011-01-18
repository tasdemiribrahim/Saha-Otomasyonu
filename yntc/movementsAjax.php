<?php
require_once ('../common/db_connect.php');
declare(encoding='UTF-8');
$root = new SimpleXMLElement('../common/sabitler.xml', NULL, true);
$HAMMADDE=$root->stokTuru->hammadde;
$YARIMAMUL=$root->stokTuru->yarimamul;
$MAMUL=$root->stokTuru->mamul;

 if(isset($_POST["hareketGetir"]))
 {
	$personel = temizSayi($_POST["personel"]);
	$konum = temizSayi($_POST["konum"]);
	$tarih = temizYazi($_POST["tarih"]);

	foreach ($db->query("SELECT DISTINCT tarih, konumID, personelID FROM hareket WHERE personelID='$personel' AND konumID ='$konum' AND tarih LIKE '$tarih%'") as $satir)
		echo $satir['personelID']."|".$satir['konumID']."|".$satir['tarih']."|";
 }
 
 if(isset($_GET["guncelle"]))
 {
	$hareketID = explode(",",$_GET['hareketID']);
	$stokID = explode(",", $_GET['stokID']);
	$fiyat = explode(",", $_GET['fiyat']);
	$alinanMiktar = explode(",", $_GET['alinanMiktar']);
	$iadeMiktar = explode(",", $_GET['iadeMiktar']);
	$personelID = temizSayi($_GET['personelID']);
	$konumID = temizSayi($_GET['konumID']);
	$tarih = temizYazi($_GET["tarih"]);
	$alinan= temizSayi($_GET["alinan"]);
	$toplamEklenen = 0;
   for($i=0;$i<sizeof($hareketID);$i++)
   {
		if(empty($alinanMiktar[$i]))
			$alinanMiktar[$i] = 0;
		if(empty($iadeMiktar[$i]))
			$iadeMiktar[$i] = 0;
			
		$sth = $db->prepare("SELECT COUNT(stokDepo.ID) FROM stokDepo,stok WHERE stokDepo.stokID='$stokID[$i]' AND stokDepo.depoID=stok.depoID AND stok.ID='$stokID[$i]'");
		$sth->execute();
		$oku = $sth->fetchColumn();
		if($oku == 0)
			$db->exec("INSERT INTO stokDepo (stokID,depoID,miktar) VALUES ('$stokID[$i]','$depoID','0')");
		if(empty($hareketID[$i]) && ($alinanMiktar[$i]!=0 || $iadeMiktar[$i]!=0))
		{
			if($db->exec("INSERT INTO hareket(konumID,personelID,stokID,alinanMiktar,iadeMiktar,tarih) VALUES('$konumID','$personelID','$stokID[$i]','$alinanMiktar[$i]','$iadeMiktar[$i]','$tarih')") === false)
			{
				echo "false";
				break;
			}
			else
			{
				$toplamEklenen += ($alinanMiktar[$i] - $iadeMiktar[$i]) * $fiyat[$i];
				if($alinanMiktar[$i] != 0)
				{
					if($db->exec("UPDATE stokDepo SET miktar=miktar-$alinanMiktar[$i] WHERE stokID='$stokID[$i]' AND depoID='$depoID'") === false)
					{
						echo "false";
						break;
					}
				}
			}
		}
		else
		{
			$sth = $db->prepare("SELECT iadeMiktar,alinanMiktar FROM hareket WHERE ID='$hareketID[$i]'");
			$sth->execute();
			$sonuc = $sth->fetch();
			$eskiAlinan= $sonuc['alinanMiktar'];
			$eskiIade = $sonuc['iadeMiktar'];
			if($eskiAlinan != $alinanMiktar[$i] || $eskiIade != $iadeMiktar[$i])
			{
				if($alinanMiktar[$i] == 0 && $iadeMiktar[$i] == 0)
				{
					if($db->exec("DELETE FROM hareket WHERE ID='$hareketID[$i]'") === false)
					{
						echo "false";
						break;
					}
				}
				else
				{
					if($db->exec("UPDATE hareket SET alinanMiktar='$alinanMiktar[$i]',iadeMiktar='$iadeMiktar[$i]' WHERE ID='$hareketID[$i]'") === false)
					{
						echo "false";
						break;
					}
				}
				
				$farkAlinan = $alinanMiktar[$i] - $eskiAlinan;
				if($farkAlinan != 0)
				{  
					if($db->exec("UPDATE stokDepo SET miktar=miktar-$farkAlinan WHERE stokID='$stokID[$i]' AND depoID='$depoID'") === false)
					{
						echo "false";
						break;
					}
				}
			}
			$toplamEklenen += ($alinanMiktar[$i] - $iadeMiktar[$i]) * $fiyat[$i];
		}
	}

	if($alinan == 0 && $toplamEklenen == 0 )
		$db->exec("DELETE FROM borc WHERE konumID='$konumID' AND hareketID='$tarih'");
	else
		$db->exec("UPDATE borc SET alinan='$alinan', eklenen='$toplamEklenen' WHERE konumID='$konumID' AND hareketID='$tarih'");	
 }

 if(isset($_GET["kaydet"]))
 {
        $stokID = explode(",", $_GET['stokID']);
        $fiyat = explode(",", $_GET['fiyat']);
        $alinanMiktar = explode(",", $_GET['alinanMiktar']);
        $iadeMiktar = explode(",", $_GET['iadeMiktar']);
        $personelID = temizSayi($_GET['personelID']);
        $konumID = temizSayi($_GET['konumID']);
        $alinan= temizSayi($_GET["alinan"]);
        $tarih= temizYazi($_GET["tarih"]);

        if(empty($alinan))
            $alinan = 0;

        $saat = date("H:i:s");
        $tarihSaat = $tarih." ".$saat;
        
        $toplamEklenen = 0;

       for($i=0;$i<sizeof($stokID);$i++)
       {
            if(empty($alinanMiktar[$i]))
                $alinanMiktar[$i] = 0;
            if(empty($iadeMiktar[$i]))
                $iadeMiktar[$i] = 0;

			$sth = $db->prepare("SELECT COUNT(stokDepo.ID) FROM stokDepo,stok WHERE stokDepo.stokID='$stokID[$i]' AND stokDepo.depoID=stok.depoID AND stok.ID='$stokID[$i]'");
			$sth->execute();
			$oku = $sth->fetchColumn();
            if($oku == 0)
                $db->exec("INSERT INTO stokDepo (stokID,depoID,miktar) VALUES ('$stokID[$i]','$depoID','0')");
            if($alinanMiktar[$i]!=0 || $iadeMiktar[$i]!=0)
            {
                if($db->exec("INSERT INTO hareket(konumID,personelID,stokID,alinanMiktar,iadeMiktar,tarih) VALUES('$konumID','$personelID','$stokID[$i]','$alinanMiktar[$i]','$iadeMiktar[$i]','$tarihSaat')") === false)
                {
                    echo "false";
                    break;
                }
                else
                {
                    $toplamEklenen += ($alinanMiktar[$i] - $iadeMiktar[$i]) * $fiyat[$i];
                    if($alinanMiktar[$i] != 0)
                    {
                        if($db->exec("UPDATE stokDepo SET miktar=miktar-$alinanMiktar[$i] WHERE stokID='$stokID[$i]' AND depoID='$depoID'") === false)
                        {
                            echo "false";
                            break;
                        }
                    }
                }
            }
        }

        if($alinan !=0 || $toplamEklenen != 0)
            $db->exec("INSERT INTO borc(hareketID,konumID,alinan,eklenen) VALUES('$tarihSaat','$konumID','$alinan','$toplamEklenen')");
 }

 if(isset($_GET["sil"]))
 {
        $konumID = temizSayi($_GET['konumID']);
        $tarih= temizYazi($_GET["tarih"]);
		foreach ($db->query("SELECT * FROM hareket WHERE konumID='$konumID' AND tarih='$tarih'") as $satir)
        {
            $hareketID = $satir['ID'];
            $stokID = $satir['stokID'];
            $alinanMiktar = $satir['alinanMiktar'];
            if($alinanMiktar != 0)
            {
				$sth = $db->prepare("SELECT COUNT(stokDepo.ID) FROM stokDepo,stok WHERE stokDepo.stokID='$stokID' AND stokDepo.depoID=stok.depoID AND stok.ID='$stokID'");
				$sth->execute();
				$adet = $sth->fetchColumn();
                if($adet == 0)
                    $db->exec("INSERT INTO stokDepo (stokID,depoID,miktar) VALUES ('$stokID','$depoID','0')");
				$db->exec("UPDATE stokDepo SET miktar=miktar+$alinanMiktar WHERE stokID='$stokID' AND depoID='$depoID'");
			}
			$db->exec("DELETE FROM hareket WHERE ID='$hareketID'");
        }
        $db->exec("DELETE FROM borc WHERE hareketID='$tarih' AND konumID='$konumID'");
 }

if(isset($_GET["hareketAyrintiGetir"]))
{
    $personel = temizSayi($_GET["personel"]);
    $konum = temizSayi($_GET["konum"]);
    $tarih = temizYazi($_GET["tarih"]);
	foreach ($db->query("SELECT ID,tanim1 from stok WHERE stokTur=$MAMUL") as $satir)
	{
        $stokID = $satir['id'];
        $tanim = $satir['tanim1'];

		$sth = $db->prepare("SELECT DISTINCT f.fiyat FROM fiyat f,stok s WHERE s.ID=f.stokID AND f.stokID='$stokID' AND f.konumID ='$konum' AND f.fiyat>0 AND f.tarih<='$tarih' ORDER BY f.tarih DESC LIMIT 1");
		$sth->execute();
		$SatirFiyat = $sth->fetchColumn();
        echo $stokID."|".$tanim."|";
		if($SatirFiyat)
            echo $SatirFiyat;
        else
            echo "0";
		echo "|";

		$sth = $db->prepare("SELECT * FROM hareket WHERE konumID='$konum' AND personelID='$personel' AND stokID='$stokID' AND tarih='$tarih'");
		$sth->execute();
		$satir3 = $sth->fetch();
        if($sth->columnCount() == 0)
            echo "|0|0|";
        else
            echo $satir3['ID']."|".$satir3['alinanMiktar']."|".$satir3['iadeMiktar']."|";
        $bulundu = true;
    }

    if($bulundu == true)
    {
		$sth = $db->prepare("SELECT SUM(eklenen)-SUM(alinan) AS toplam FROM borc WHERE konumID='$konum'");
		$sth->execute();
		$ToplamBorc = $sth->fetchColumn();
        if($ToplamBorc)
            echo $ToplamBorc."|";
        else
            echo "0|";
		$sth = $db->prepare("SELECT alinan FROM borc WHERE konumID='$konum' AND hareketID='$tarih'");
		$sth->execute();
		$AlinanBorc = $sth->fetchColumn();
        echo $AlinanBorc ? $AlinanBorc : 0;
    }
}

if(isset($_GET["stokGetir"]))
{
    $konumID = $_GET["konumID"];
    $tarih = $_GET["tarih"];
	foreach ($db->query("SELECT ID,tanim1 from stok WHERE stokTur=$MAMUL") as $satir)
	{
        $stokID = $satir['ID'];
        $tanim = $satir['tanim1'];
		$sth = $db->prepare("SELECT DISTINCT f.fiyat FROM fiyat f,stok s WHERE s.ID=f.stokID AND f.stokID='$stokID' AND f.konumID ='$konumID' AND f.fiyat>0 AND f.tarih<='$tarih' ORDER BY f.tarih DESC LIMIT 1");
		$sth->execute();
		$sonucFiyat = $sth->fetchColumn();
		echo $stokID."|".$tanim."|";
        if($sonucFiyat)
            echo $sonucFiyat;
        else
            echo "0";
		echo "|";
        $bulundu = true;
    }

    if($bulundu == true)
    {
		$sth = $db->prepare("SELECT SUM(eklenen)-SUM(alinan) AS toplam from borc WHERE konumID='$konumID'");
		$sth->execute();
		$sonucToplam = $sth->fetchColumn();
        if($sonucToplam)
            echo $sonucToplam;
        else
            echo "0";
    }
}