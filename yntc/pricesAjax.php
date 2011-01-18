<?php
    require_once ('../common/db_connect.php');
	declare(encoding='UTF-8');
    if(isset($_GET["tabloDoldur"]))
    {
        $konum = temizSayi($_GET["konum"]);
        $tarih = temizYazi($_GET["tarih"]);
        $urunler=array();
		foreach ($db->query("SELECT tanim1 FROM stok WHERE stokTur='2'") as $satir)
             $urunler[] = $satir['tanim1'];
		$size=sizeof($urunler);
        for($i=0;$i<$size;$i++)
        {
			$sth = $db->prepare("SELECT DISTINCT f.fiyat,s.tanim1,datediff('$tarih',tarih) AS fark FROM fiyat f, stok s WHERE f.stokID=s.ID AND f.konumID='$konum' AND s.tanim1='$urunler[$i]' AND tarih <='$tarih' ORDER BY fark ASC LIMIT 1");
			$sth->execute();
			$satir = $sth->fetch();
            if($sth->rowCount()>0)
                echo $satir['tanim1']."|".$satir['fiyat']."|";
            else
                echo $urunler[$i]."|0|";
        }
    }

    if(isset($_GET["fiyatGuncelle"]))
    {
       $konum = temizSayi($_GET['konum']);
       $tarih = temizYazi($_GET['tarih']);
       $urun = explode(",", $_GET['urun']);
       $fiyat = explode(",", $_GET['fiyat']);
	   $size=sizeof($urun);
       for($i=0;$i<$size;$i++)
       {
			$sth = $db->prepare("SELECT ID FROM stok WHERE tanim1='$urun[$i]'");
			$sth->execute();
			$urunID = $sth->fetchColumn();
			$sth = $db->prepare("SELECT * FROM fiyat WHERE konumID='$konum' AND stokID='$urunID' AND tarih='$tarih'");
			$sth->execute();
			$sonuc = $sth->fetch();
			if($sth->rowCount()>0)
               $db->exec("UPDATE fiyat SET fiyat='$fiyat[$i]' WHERE konumID='$konum' AND stokID='$urunID' AND tarih='$tarih'");
            else
				$db->exec("INSERT INTO fiyat(konumID,stokID,fiyat,tarih) VALUES('$konum','$urunID','$fiyat[$i]','$tarih')");
       }
       echo "true";
    }