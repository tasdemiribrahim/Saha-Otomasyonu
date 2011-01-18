<?php
require_once ('../common/db_connect.php');
@include_once 'Image/Barcode.php';
declare(encoding='UTF-8');
$root = new SimpleXMLElement('../common/sabitler.xml', NULL, true);
$HAMMADDE=$root->stokTuru->hammadde;
$YARIMAMUL=$root->stokTuru->yarimamul;
$MAMUL=$root->stokTuru->mamul;
$resimGecDizin="stok_resimleri/temp/";
$resimDizin="stok_resimleri/";
$dosyaGecDizin="stok_dosyalari/temp/";
$dosyaDizin="stok_dosyalari/";
$barkodDizin="images/";

//resim veya dosya yükle
if(isset($_FILES['uploadResim']['name']) || isset($_FILES['uploadDosya']['name']))
{
	$unique = time();
	if(isset($_FILES['uploadResim']['name']) && $_FILES['uploadResim']['error'] == UPLOAD_ERR_OK)
	{
		$file = $resimGecDizin.$unique."-".basename($_FILES['uploadResim']['name']);
		if (!move_uploaded_file($_FILES['uploadResim']['tmp_name'], $file)) 
		{
			echo "hata";
			exit;
		}
		if(!guvenliResim($file) || !($_FILES['uploadResim']['type'] == "image/gif" || $_FILES['uploadResim']['type'] == "image/jpeg" || $_FILES['uploadResim']['type'] == "image/pjpeg" || $_FILES['uploadResim']['type'] == "image/png"))
		{
			echo "uyari";
			exit;
		}
		echo $file;
		exit;
	}

	if(isset($_FILES['uploadDosya']['name']) && $_FILES['uploadDosya']['error'] == UPLOAD_ERR_OK)
	{
		$file = $dosyaGecDizin.$unique."-".basename($_FILES['uploadDosya']['name']);
		if (!move_uploaded_file($_FILES['uploadDosya']['tmp_name'], $file)) 
		{
			echo "hata";
			exit;
		}
		if(!guvenliDosya($file))
		{
			echo "uyari";
			exit;
		}
		echo $file;
		exit;
	 }
}
//resim veya dosya sil
if(isset($_GET["dosyaAdi"]))
	unlink($dosyaGecDizin.basename($_GET["dosyaAdi"]));
//resim veya dosya sil
if(isset($_GET["resimAdi"]))
	unlink($resimGecDizin.basename($_GET["resimAdi"]));

//öneri göster
if(isset($_POST['kodOneri']) || isset($_POST['tanim1Oneri']) || isset($_POST['tanim2Oneri']))
{
	if(isset($_POST['kodOneri'])) {
		$searchQuery = $_POST['kodOneri'];
		$alan="kod";
		$yap=1;
	}
	if(isset($_POST['tanim1Oneri'])) {
		$searchQuery = $_POST['tanim1Oneri'];
		$alan="tanim1";
		$yap=1;
	}
	if(isset($_POST['tanim2Oneri'])) {
		$searchQuery = $_POST['tanim2Oneri'];
		$alan="tanim2";
		$yap=1;
	}

	if(!empty($yap))
	{
		$i = strrpos($searchQuery,"=");
		if ($i)
		{
			$l = strlen($searchQuery) - $i;
			$searchQuery = substr($searchQuery,$i+1,$l);
		}
		if(strlen($searchQuery) >0) 
		{
			echo"<ul>";
			foreach ($db->query("SELECT * FROM stok WHERE $alan LIKE '$searchQuery%'") as $row)
				if($alan=="kod")
					echo '<li class="kodOneriListe">'.$row[$alan].'</li>';
				else
					echo '<li class="tanimOneriListe">'.$row[$alan].' - Stok Kodu:'.$row["kod"].'</li>';
			echo"</ul>";
		}
	}
}

if(isset($_GET['stokGetirKod']) || isset($_GET['stokGetirTanim']))
{
	if(isset($_GET['stokGetirKod']))
	{
		$alan = temizSayi($_GET['stokGetirKod']);
		$sorgu="SELECT * FROM stok WHERE kod='$alan'";
	}
	else if(isset($_GET['stokGetirTanim']))
	{
		$alan = temizYazi($_GET['stokGetirTanim']);
		$sorgu="SELECT * FROM stok WHERE tanim1='$alan'";
	}

	if(!empty($sorgu))
	{
		$sth = $db->prepare($sorgu);
		$sth->execute();
		$row = $sth->fetch();
		echo $row['ID']."|".$row['kod']."|".$row['stokTur']."|".$row['depoID']."|".$row['tanim1']."|".$row['tanim2']."|".$row['fiyatBirim']."|".$row['alisFiyat']."|".$row['alisKDV']."|".$row['satisFiyat']."|".$row['satisKDV']."|".$row['agirlik']."|".$row['temelOlcuBirim']."|".$row['en']."|".$row['boy']."|".$row['yukseklik']."|".$row['agirlikBirim']."|".$row['enBirim']."|".$row['boyBirim']."|".$row['yukseklikBirim']."|".$row['barkod'];
		if($row['barkod']!=0 && !file_exists($barkodDizin.$row['barkod'].'.jpeg'))
		{
			@$img=Image_Barcode::draw($row['barkod'], 'int25', 'jpeg', false);
			imagejpeg($img, $barkodDizin.$row['barkod'].'.jpeg',50);
			imagedestroy($img);
		}
	}
}

if(isset($_GET['resimGetirId']))
{
	$id = temizSayi($_GET['resimGetirId']);
	foreach ($db->query("SELECT adres FROM stokResim WHERE stokID='$id'") as $row)
		echo $row['adres']."|";
}

if(isset($_GET['dosyaGetirId']))
{
	$id = temizSayi($_GET['dosyaGetirId']);
	foreach ($db->query("SELECT adres FROM stokDosya WHERE stokID='$id'") as $row)
		echo $row['adres']."|";
}

if(isset($_GET['donusumGetirId']))
{
	$id = temizSayi($_GET['donusumGetirId']);
	foreach ($db->query("SELECT * FROM stokBirimDonusum WHERE stokID='$id'") as $row)
	{
		echo $row['temelBirimDeger']."|".$row['ikinciBirimDeger']."|";
		$sth = $db->prepare("SELECT birimKisaltma FROM birim WHERE ID='$row[ikinciBirim]'");
		$sth->execute();
		$ikinciBirim = $sth->fetchColumn();
		echo $ikinciBirim."|";
	}
}

if(isset($_GET['donusumTur']))
{
	foreach ($db->query("SELECT birimKisaltma FROM birim WHERE birimTur!='Para'") as $row)
		echo $row['birimKisaltma']."|";
}

if(isset($_GET['kaydet']) || isset($_GET['guncelle']))
{
	$kod=temizSayi($_GET['kod']);
	$yeniEklenenResimler = explode(",", $_GET['yeniEklenenResimler']);
	$yeniEklenenDosyalar = explode(",", $_GET['yeniEklenenDosyalar']);
	$stokTur=temizSayi($_GET['stokTur']);
	$tanim1=temizYazi($_GET['tanim1']);
	$tanim2=temizYazi($_GET['tanim2']);
	$depoID=temizSayi($_GET['depoID']);
	$fiyatBirim=temizYazi($_GET['fiyatBirim']);
	$alisFiyat=temizSayi($_GET['alisFiyat']);
	$alisKDV=temizSayi($_GET['alisKDV']);
	$satisFiyat=temizSayi($_GET['satisFiyat']);
	$satisKDV=temizSayi($_GET['satisKDV']);
	$temelOlcuBirim=temizYazi($_GET['temelOlcuBirim']);
	$temelOlcuBirimDeger = explode(",", $_GET['temelOlcuBirimDeger']);
	$ikinciOlcuBirim = explode(",", $_GET['ikinciOlcuBirim']);
	$ikinciOlcuBirimDeger = explode(",", $_GET['ikinciOlcuBirimDeger']);
	$agirlik=temizSayi($_GET['agirlik']);
	$agirlikBirim=temizYazi($_GET['agirlikBirim']);
	$en=temizSayi($_GET['en']);
	$enBirim=temizYazi($_GET['enBirim']);
	$yukseklik=temizSayi($_GET['yukseklik']);
	$yukseklikBirim=temizYazi($_GET['yukseklikBirim']);
	$boy=temizSayi($_GET['boy']);
	$boyBirim=temizYazi($_GET['boyBirim']);
	$barkod=$_GET['barkod'];
	@$eskiKod=temizSayi($_GET['eskiKod']);
	
	$sth = $db->prepare("SELECT COUNT(ID) FROM stok WHERE kod = '$kod'");
	$sth->execute();
	$say = $sth->fetchColumn();
	 if((empty($eskiKod) || $eskiKod != $kod) && $say > 0)
		 echo "Bu kodda bir kayıt var. Lütfen başka bir kod giriniz.";
	 else
	 {
		if(isset($_GET['kaydet']))
		{
			if($db->exec("INSERT INTO stok(kod,stokTur,tanim1,tanim2,depoID,fiyatBirim,alisFiyat,alisKDV,satisFiyat,satisKDV,temelOlcuBirim,en,boy,yukseklik,agirlik,enBirim,boyBirim,yukseklikBirim,agirlikBirim,barkod) VALUES ('$kod','$stokTur','$tanim1','$tanim2','$depoID','$fiyatBirim' ,'$alisFiyat','$alisKDV','$satisFiyat','$satisKDV','$temelOlcuBirim','$en','$boy', '$yukseklik','$agirlik','$enBirim','$boyBirim','$yukseklikBirim','$agirlikBirim','$barkod')")!==false)
			{
				if(!empty($satisFiyat))
				{
					$sth = $db->prepare("SELECT ID FROM stok WHERE kod='$kod'");
					$sth->execute();
					$id = $sth->fetchColumn();
					if($stokTur == $MAMUL)
					{
						foreach ($db->query("SELECT ID FROM konum") as $row)
							$db->exec("INSERT INTO fiyat(stokID, konumID, fiyat, tarih) VALUES('$id', '$row[ID]', '$satisFiyat',date('Y-m-d'))");
					}
				}
				echo true;
			}
			else
				echo false;
		}

		if(isset($_GET['guncelle']))
		{
			$kayitliSilResimler = explode(",", $_GET['kayitliSilResimler']);
			$kayitliSilDosyalar = explode(",", $_GET['kayitliSilDosyalar']);
			$eskiTemelOlcuBirim=temizYazi($_GET['eskiTemelOlcuBirim']);
			$kayitliDonusumSil = explode(",", $_GET['kayitliDonusumSil']);
			
			$sth = $db->prepare("SELECT ID FROM stok WHERE kod=$eskiKod");
			$sth->execute();
			$id = $sth->fetchColumn();
			if($db->exec("UPDATE stok SET kod='$kod', stokTur='$stokTur', tanim1='$tanim1', tanim2='$tanim2', depoID='$depoID',fiyatBirim='$fiyatBirim',alisFiyat='$alisFiyat', alisKDV='$alisKDV', satisFiyat='$satisFiyat', satisKDV='$satisKDV', temelOlcuBirim='$temelOlcuBirim', en='$en', boy='$boy', yukseklik='$yukseklik', agirlik='$agirlik', enBirim='$enBirim', boyBirim='$boyBirim', yukseklikBirim='$yukseklikBirim', agirlikBirim='$agirlikBirim',barkod='$barkod' WHERE ID=$id") ===false)
				echo false;
			else
			{
				dosyaSil($kayitliSilResimler, $id, "stokResim");
				dosyaSil($kayitliSilDosyalar, $id, "stokDosya");
				if($temelOlcuBirim!=$eskiTemelOlcuBirim)
					$db->exec("DELETE FROM stokBirimDonusum WHERE stokID='$id'");
				elseif(isset($silDonusum))
						for($i=0;$i<sizeof($silDonusum);$i++)
							if($silDonusum[$i]!=0 && $silDonusum[$i]!="")
								$db->exec("DELETE FROM stokBirimDonusum WHERE ikinciBirim= '$silDonusum[$i]' AND stokID='$id'");
				echo true;
			}
		}
		if(isset($id))
		{
			$dizin = $resimDizin.$id;
			dosyaYukle($dizin,$yeniEklenenResimler,$id,"stokResim");
			$dizin = $dosyaDizin.$id;
			dosyaYukle($dizin,$yeniEklenenDosyalar,$id,"stokDosya");
			$donusumSayi=sizeof($temelOlcuBirimDeger);
			for($i=0;$i<$donusumSayi;$i++)
				if($temelOlcuBirimDeger[$i]!=0 && $temelOlcuBirimDeger[$i]!="")
				{
					$sth = $db->prepare("SELECT ID FROM birim WHERE birimKisaltma='$ikinciOlcuBirim[$i]'");
					$sth->execute();
					$YeniIkinciOlcuBirim = $sth->fetchColumn();
					$db->exec("INSERT INTO stokBirimDonusum(stokID,temelBirimDeger,ikinciBirim,ikinciBirimDeger) VALUES('$id','$temelOlcuBirimDeger[$i]','$YeniIkinciOlcuBirim','$ikinciOlcuBirimDeger[$i]')");
				}
		}
	 }
}

function dosyaAdiBul($str)
{
	$i = strrpos($str,"/");
	if (!$i)
		return "";
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
	return $str;
}

function dosyaYukle($dizin,$dosyaDizi,$id,$tablo)
{
	if(!is_dir($dizin))
		mkdir($dizin, 0777);
	$dosyaSayi = sizeof($dosyaDizi);
	for($i=0; $i<$dosyaSayi;$i++)
		if($dosyaDizi[$i]!="0" && $dosyaDizi[$i]!="")
		{
			$dosyaAdi = dosyaAdiBul($dosyaDizi[$i]);
			$dosyaAdres=$dizin."/".$dosyaAdi;
			rename($dosyaDizi[$i],$dosyaAdres);
			$GLOBALS["db"]->exec("INSERT INTO $tablo (stokID,adres) values('$id','$dosyaAdres')");
		}
}

function dosyaSil($silDosya, $id, $tablo)
{
	for($i=0;$i<sizeof($silDosya);$i++)
	{
		if($silDosya[$i]!="")
			unlink($silDosya[$i]);
		$GLOBALS["db"]->exec("DELETE FROM $tablo WHERE adres = '$silDosya[$i]' AND stokID= '$id'");
	}
}

if(isset($_GET['stokAraTur']))
 {
	$stokAraKod=temizSayi($_GET['stokAraKod']);
	$stokAraTur=temizSayi($_GET['stokAraTur']);
	$stokAraTanim=temizYazi($_GET['stokAraTanim']);
	$stokAraDepo=temizSayi($_GET['stokAraDepo']);
	$sorgu="SELECT * FROM stok WHERE (kod like '".$stokAraKod."%') AND (tanim1 like '".$stokAraTanim."%' OR tanim1 like '".(strtoupper($stokAraTanim))."%')";		
	if($stokAraTur!="3")
		$sorgu.=" AND stokTur='$stokAraTur'";
	if($stokAraDepo!="")
		$sorgu.=" AND depoID='$stokAraDepo'";
	$i=0;
	$mesaj="";
	foreach ($db->query($sorgu) as $row)
	{	
		$i++;
		$sth = $db->prepare("SELECT tanim FROM depo WHERE ID='$row[depoID]'");
		$sth->execute();
		$depo = $sth->fetchColumn();
		if($row['stokTur']==$HAMMADDE)
			$stokTuru="Hammadde";
		else if($row['stokTur']==$YARIMAMUL)
			$stokTuru="Yarı Mamül";
		else if($row['stokTur']==$MAMUL)
			$stokTuru="Mamül";
		$mesaj.= "|".$row['kod']."|".$stokTuru."|".$row['tanim1']."|".$row['tanim2']."|".$depo;
	}
	echo $i.$mesaj;
 }