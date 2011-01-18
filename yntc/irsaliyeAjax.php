<?php
require_once "../common/db_connect.php";
declare(encoding='UTF-8');

class irsaliyeAjax
{ 
    public function id_getir($ID) 
    {
		$sth = $GLOBALS["db"]->prepare("SELECT * FROM irsaliye WHERE ID='$ID'");
		$sth->execute();
		$row = $sth->fetch();
        if($row['tur']!="")
        {
            $kayit="";
            $k=0;
            $ana="";
			foreach ($GLOBALS["db"]->query("SELECT value FROM dinamikBilgi WHERE ID='$ID'") as $row4)
                $ana.=$row4['value']."|";
			foreach ($GLOBALS["db"]->query("SELECT * FROM irsaliyeDetay WHERE ID='$ID'") as $row4)
            {
				$sth = $GLOBALS["db"]->prepare("SELECT tanim1,tanim FROM stok,depo WHERE stok.ID='$row4[stokID]' AND depo.ID='$row4[depoID]'");
				$sth->execute();
				$sonuc = $sth->fetch();
				$sth = $GLOBALS["db"]->prepare("SELECT birimKisaltma FROM stok,birim WHERE stok.ID='$row4[stokID]' AND birim.ID=stok.temelOlcuBirim");
				$sth->execute();
				$temelolcubirim = $sth->fetchColumn();
                $k++;
                $kayit.=$row4['detayID']."|".$sonuc['tanim1']."|".$sonuc['tanim']."|".$row4['miktar']."|".$temelolcubirim."|".$row4['depoID']."|";
            }
			$sth = $GLOBALS["db"]->prepare("SELECT tanim FROM konum WHERE ID='$row[musteri]'");
			$sth->execute();
			$tanim = $sth->fetchColumn();
            echo $tanim."|".$row['tarih']."|".$row['teslimTarih']."|".$row['tur']."|".$ana.$k."|".$kayit; 
        }
        else
        {
            echo "|".date("Y-m-d")."|".date("Y-m-d")."|3"."|";
			$sth = $GLOBALS["db"]->prepare("SELECT COUNT(ID) FROM irsaliyeayar");
			$sth->execute();
			$i = $sth->fetchColumn();
            while($i) { echo "|"; $i--; }
        }
    }

    public function musteri_tahmin($ara)
    { 	
        $results = array();
		foreach ($GLOBALS["db"]->query("SELECT tanim FROM konum WHERE tanim LIKE '".$ara."%' OR tanim LIKE '".(strtoupper($ara))."%' LIMIT 10") as $row)
            if($row['tanim']!="")
                $results[] = $row['tanim'];
        echo json_encode($results);
    }
    
    public function stok_getir($stok)
    {
		$sth = $GLOBALS["db"]->prepare("SELECT birimKisaltma,stok.* FROM birim,stok WHERE birim.ID=stok.temelOlcuBirim AND tanim1='$stok'");
		$sth->execute();
		$row = $sth->fetch();
        $mesaj=$row['birimKisaltma']."|";
        if($mesaj!="|") 
			foreach ($GLOBALS["db"]->query("SELECT * FROM stokBirimDonusum WHERE stokID='$row[ID]'") as $row2)
            {
				$sth = $GLOBALS["db"]->prepare("SELECT birimKisaltma FROM birim WHERE ID='$row2[ikinciBirim]'");
				$sth->execute();
				$ikiBir = $sth->fetchColumn();
                $mesaj.="<option value='$row2[ID]'>".$ikiBir."</option>";
            }
        $mesaj.="|".$row['depoID'];
        echo $mesaj; 
    }

    public function detay_ekle($ID,$musteri,$tarih,$teslimTarih,$stokTanim,$depo,$miktar,$tur)
    {
		$gec=explode("|",$_POST['ekstra']);
		$sth = $GLOBALS["db"]->prepare("SELECT tur FROM irsaliye WHERE ID='$ID'");
		$sth->execute();
		$rowTur = $sth->fetchColumn();
        if($tur==$rowTur || $rowTur=="")
        {
			$sth = $GLOBALS["db"]->prepare("SELECT ID FROM stok WHERE tanim1='$stokTanim'");
			$sth->execute();
			$stok = $sth->fetchColumn();
            $GLOBALS["db"]->exec("DELETE FROM dinamikBilgi WHERE ID='$ID'");
            $i=0;
			foreach ($GLOBALS["db"]->query("SELECT title FROM irsaliyeayar") as $row)
			{
                $GLOBALS["db"]->exec("INSERT INTO dinamikBilgi (ID,stokID,title,value) VALUES ('$ID','$stok','$row[title]','$gec[$i]')");
				$i++;
			}
			$sth = $GLOBALS["db"]->prepare("SELECT ID FROM konum WHERE tanim='$musteri'");
			$sth->execute();
			$rowID = $sth->fetchColumn();
            //$GLOBALS["db"]->exec("REPLACE INTO irsaliye VALUES ('$ID','$rowID','$tarih','$teslimTarih','$tur') WHERE ID='$ID'");
            $GLOBALS["db"]->exec("DELETE FROM irsaliye WHERE ID='$ID'");
            $GLOBALS["db"]->exec("INSERT INTO irsaliye (ID,musteri,tarih,teslimTarih,tur) VALUES ('$ID','$rowID','$tarih','$teslimTarih','$tur')");
			$sth = $GLOBALS["db"]->prepare("SELECT * FROM depo WHERE ID='$depo'");
			$sth->execute();
			$row1 = $sth->fetch();
			$sth = $GLOBALS["db"]->prepare("SELECT miktar FROM stokDepo WHERE depoID='$depo' AND stokID='$stok'");
			$sth->execute();
			$rowMiktar = $sth->fetchColumn();
            if($rowMiktar=="")
                $rowMiktar=0;
            if($tur==0)
                if($row1['eksiBakiyeIzin']==0 || $miktar<$rowMiktar)
                {
                    $GLOBALS["db"]->exec("INSERT INTO irsaliyeDetay (detayID,ID,stokID,depoID,miktar) VALUES (null,'$ID','$stok','$depo','$miktar')");
                    $kalan=$rowMiktar-$miktar;       
                    $this->stok_depo_guncelle($stok,$depo,$rowMiktar,$kalan);
                    if($row1['eksiBakiyeUyari']==1 && $miktar>$rowMiktar)
                        echo "uyari1";
               }
               else if($row1['eksiBakiyeUyari']==1 && $miktar>$rowMiktar)
                    echo "uyari2";
            if($tur==1)
            {
                $GLOBALS["db"]->exec("INSERT INTO irsaliyeDetay (detayID,ID,stokID,depoID,miktar) VALUES (null,'$ID','$stok','$depo','$miktar')");
                $kalan=$rowMiktar+$miktar;       
                $this->stok_depo_guncelle($stok,$depo,$rowMiktar,$kalan);
            }
        }
        else
            echo "uyari";
    }

    public function detay_sil($detayID)
    {
		$sth = $GLOBALS["db"]->prepare("SELECT irsaliyeDetay.miktar as dmiktar,stokDepo.miktar as smiktar,stokDepo.stokID,stokDepo.depoID,irsaliyeDetay.ID FROM irsaliyeDetay,stokDepo WHERE irsaliyeDetay.detayID='$detayID' AND stokDepo.depoID=irsaliyeDetay.depoID AND stokDepo.stokID=irsaliyeDetay.stokID");
		$sth->execute();
		$row = $sth->fetch();
		$sth = $GLOBALS["db"]->prepare("SELECT tur FROM irsaliye WHERE ID='$row[ID]'");
		$sth->execute();
		$tur = $sth->fetchColumn();

        $kalan= $tur==0 ? $row['smiktar']+$row['dmiktar'] : $row['smiktar']-$row['dmiktar'];
        $this->stok_depo_guncelle($row['stokID'],$row['depoID'],$row['smiktar'],$kalan);
        $GLOBALS["db"]->exec ("DELETE FROM irsaliyeDetay WHERE detayID='$detayID'");
    }

    public function miktar_donusum($donID,$donMiktar)
    {
		$sth = $GLOBALS["db"]->prepare("SELECT ikinciBirimDeger,temelBirimDeger FROM stokBirimDonusum WHERE ID='$donID'");
		$sth->execute();
		$row = $sth->fetch();
        $sonuc= $row['ikinciBirimDeger']>0 ? ($donMiktar*$row['temelBirimDeger'])/$row['ikinciBirimDeger'] : 0;
        echo $sonuc;
    }

    public function irsaliye_iptal($iptalID)
    {
		$sth = $GLOBALS["db"]->prepare("SELECT tur FROM irsaliye WHERE ID='$iptalID'");
		$sth->execute();
		$tur = $sth->fetchColumn();
        $GLOBALS["db"]->exec ("DELETE FROM irsaliye WHERE ID='$iptalID'");
        $GLOBALS["db"]->exec ("DELETE FROM dinamikBilgi WHERE ID='$iptalID'");
		foreach ($GLOBALS["db"]->query("SELECT * FROM irsaliyeDetay WHERE ID='$iptalID'") as $row)
        {
            $detayID=$row['detayID'];
			$sth = $GLOBALS["db"]->prepare("SELECT miktar FROM stokDepo WHERE depoID='$row[depoID]' AND stokID='$row[stokID]'");
			$sth->execute();
			$miktar = $sth->fetchColumn();
            $kalan= $tur==0 ? $miktar+$row['miktar'] : $miktar-$row['miktar'];
            $this->stok_depo_guncelle($row['stokID'],$row['depoID'],$miktar,$kalan);
            $GLOBALS["db"]->exec("DELETE FROM irsaliyeDetay WHERE detayID='$detayID'");
        }
    }

    public function irsaliye_guncelle($detayID,$stokTanim,$depo,$miktar,$tur)
    {
		$sth = $GLOBALS["db"]->prepare("SELECT ID FROM stok WHERE tanim1='$stokTanim'");
		$sth->execute();
		$stok = $sth->fetchColumn();
		$sth = $GLOBALS["db"]->prepare("SELECT * FROM depo WHERE ID='$depo'");
		$sth->execute();
		$row1 = $sth->fetch();
		$sth = $GLOBALS["db"]->prepare("SELECT miktar FROM stokDepo WHERE depoID='$depo' AND stokID='$stok'");
		$sth->execute();
		$StokMiktar = $sth->fetchColumn();
        if($StokMiktar=="") 
            $StokMiktar=0;
		$sth = $GLOBALS["db"]->prepare("SELECT miktar FROM irsaliyeDetay WHERE detayID='$detayID'");
		$sth->execute();
		$DetayMiktar = $sth->fetchColumn();
        $fark=$miktar-$DetayMiktar;
        if($tur==0)
            if($row1['eksiBakiyeIzin']==0 || $fark<$StokMiktar)
            {
                $GLOBALS["db"]->exec("UPDATE irsaliyeDetay SET depoID='$depo',stokID='$stok',miktar='$miktar' WHERE detayID='$detayID'"); 
                $kalan=$StokMiktar-$fark;  
                $this->stok_depo_guncelle($stok,$depo,$StokMiktar,$kalan);
                if($row1['eksiBakiyeUyari']==1 && $fark>$StokMiktar)
                    echo "uyari1";
            }
            else if($row1['eksiBakiyeUyari']==1 && $fark>$StokMiktar)
                echo "uyari2";
        if($tur==1)
        {   
			$GLOBALS["db"]->exec("UPDATE irsaliyeDetay SET depoID='$depo',stokID='$stok',miktar='$miktar' WHERE detayID='$detayID'");
            $kalan=$StokMiktar+$fark;   
            $this->stok_depo_guncelle($stok,$depo,$StokMiktar,$kalan);
        }
    }

    public function irsaliye_arama($araID="",$araMusteri="",$araTarih="",$araTeslimTarih="",$araTur=2)
    {
        $sorgu="SELECT i.ID,k.tanim,i.tarih,i.teslimTarih,i.tur FROM irsaliye i, konum k WHERE (i.ID like '".$araID."%') AND (k.tanim like '".$araMusteri."%' OR k.tanim like '".(strtoupper($araMusteri))."%') AND i.musteri=k.ID AND (i.tarih like '".$araTarih."%') AND (i.teslimTarih like '".$araTeslimTarih."%')";
        if($araTur!=2)
            $sorgu.=" AND i.tur='$araTur'";
		$sth = $GLOBALS["db"]->prepare($sorgu);
		$sth->execute();
        echo $sth->rowCount();
		$stokRow = $sth->fetchAll();
        while(list(,$row)=each($stokRow))
            echo "|".$row['ID']."|".$row['tanim']."|".$row['tarih']."|".$row['teslimTarih']."|".$row['tur'];
    }

    public function guncelle_getir($ID)
    {
		$sth = $GLOBALS["db"]->prepare("SELECT depoID,miktar,stokID FROM irsaliyeDetay WHERE detayID='$ID'");
		$sth->execute();
		$row = $sth->fetch();
		$sth = $GLOBALS["db"]->prepare("SELECT tanim1 FROM stok WHERE ID='$row[stokID]'");
		$sth->execute();
		$tanim1 = $sth->fetchColumn();
        echo $tanim1."|".$row['depoID']."|".$row['miktar'];
    }

    public function yeni_tur()
    {
		$sth = $GLOBALS["db"]->prepare("SELECT MAX(ID) FROM irsaliye");
		$sth->execute();
		echo $sth->fetchColumn();
    }
    
    protected function stok_depo_guncelle($stok,$depo,$miktar,$kalan)
    { 
        $GLOBALS["db"]->exec ("DELETE FROM stokDepo WHERE stokID='$stok' AND depoID='$depo' AND miktar=$miktar");	
        $GLOBALS["db"]->exec ("INSERT INTO stokdepo (ID ,stokID ,depoID ,miktar) VALUES (NULL , '$stok', '$depo', $kalan)");
    }
}

$irsaliyeSecim=new irsaliyeAjax();
if(isset($_POST['musteriOneri'])) {     $irsaliyeSecim->musteri_oneri($_POST['musteriOneri']);}
if(isset($_POST['yeniTur'])){           $irsaliyeSecim->yeni_tur();}
if(isset($_POST['guncelleGetir'])){     $irsaliyeSecim->guncelle_getir(temizSayi($_POST['guncelleGetir']));}
if(isset($_POST['iptalID'])){           $irsaliyeSecim->irsaliye_iptal(temizSayi($_POST['iptalID']));}
if(isset($_POST['donID'])){             $irsaliyeSecim->miktar_donusum(temizSayi($_POST['donID']),temizSayi($_POST['donMiktar']));}
if(isset($_POST['detaySil'])){          $irsaliyeSecim->detay_sil(temizSayi($_POST['detaySil']));}
if(isset($_POST['stok'])){              $irsaliyeSecim->stok_getir(temizYazi($_POST['stok'])); }
if(isset($_POST['ID'])){                $irsaliyeSecim->id_getir(temizSayi($_POST['ID']));}
if(isset($_GET['part']) and $_GET['part'] != ''){   $irsaliyeSecim->musteri_tahmin(strtolower($_GET['part']));}

if(isset($_GET['araID']) || isset($_GET['araMusteri']) || isset($_GET['araTur']))
{    $irsaliyeSecim->irsaliye_arama(temizSayi($_GET['araID']),temizYazi($_GET['araMusteri']),temizYazi($_GET['araTarih']),temizYazi($_GET['araTeslimTarih']),temizSayi($_GET['araTur']));
}

if(isset($_POST['guncelle']))
{    $irsaliyeSecim->irsaliye_guncelle(temizSayi($_POST['guncelle']),temizYazi($_POST['stokG']),temizYazi($_POST['depoG']),temizSayi($_POST['miktarG']),temizSayi($_POST["turG"]));
}

if(isset($_POST['ekleStok']))
{    $irsaliyeSecim->detay_ekle(temizSayi($_POST["ekleID"]),temizYazi($_POST["musteri"]),temizYazi($_POST["tarih"]),temizYazi($_POST["teslimTarih"]),temizYazi($_POST["ekleStok"]),temizYazi($_POST["depo"]),temizSayi($_POST["miktar"]),temizSayi($_POST["irsaliyeTur"]));
}