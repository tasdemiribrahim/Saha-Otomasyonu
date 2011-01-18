<?php
require_once ('../common/db_connect.php');
declare(encoding='UTF-8');

class tupcuAjax
{
    public function personel_goster($depo)
    {
		$sth = $GLOBALS["db"]->prepare("SELECT personel FROM depo WHERE ID='$depo'");
		$sth->execute();
		echo $sth->fetchColumn();
    }

    public function birim_goster($stok)
    {
		$sth = $GLOBALS["db"]->prepare("SELECT birim.birimKisaltma FROM stok,birim WHERE stok.tanim1='$stok' AND birim.ID=stok.temelOlcuBirim");
		$sth->execute();
		echo $sth->fetchColumn();
    }

    public function siparis_ekle($isim,$depo,$personel,$stok,$miktar)
    {
		$sth = $GLOBALS["db"]->prepare("SELECT ID FROM depo WHERE tanim='$depo'");
		$sth->execute();
		$depo=$sth->fetchColumn();
		$sth = $GLOBALS["db"]->prepare("SELECT ID FROM konum WHERE tanim='$isim'");
		$sth->execute();
		$konumID=$sth->fetchColumn();
		$sth = $GLOBALS["db"]->prepare("SELECT ID FROM stok WHERE tanim1='$stok'");
		$sth->execute();
		$stok=$sth->fetchColumn();
		$sth = $GLOBALS["db"]->prepare("SELECT ID FROM personel WHERE tanim='$personel'");
		$sth->execute();
		$personel=$sth->fetchColumn();
        $GLOBALS["db"]->exec ("INSERT INTO siparis (konumID,depoID,personelID,stokID,miktar,durum) VALUES ('$konumID','$depo','$personel','$stok','$miktar','0')");
    }
}

$tupcuSecim=new tupcuAjax();
if(isset($_POST['isim'])){   $tupcuSecim->siparis_ekle(temizYazi($_POST['isim']),temizYazi($_POST['depoKaydet']),temizYazi($_POST['personel']),temizYazi($_POST['stokKaydet']),temizSayi($_POST['miktar']));}
if(isset($_POST['depo'])){   $tupcuSecim->personel_goster(temizSayi($_POST['depo']));}
if(isset($_POST['stok'])){   $tupcuSecim->birim_goster(temizYazi($_POST['stok']));} 