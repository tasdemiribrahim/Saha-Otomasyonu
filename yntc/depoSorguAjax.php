<?php
require_once ('../common/db_connect.php');
declare(encoding='UTF-8');

class depoSorguAjax
{
    public function listele()
    {   
		$za = new ZipArchive();
		$liste=array();
        $handle = dir("depo_sorgu_arsiv");
        while ($filename = $handle->read())
		{
			$inName=substr($filename,0,-4);
			$za->open("depo_sorgu_arsiv/".$filename);
			if($za->numFiles!=3)
				continue;
			for ($i=0; $i<$za->numFiles;$i++) 
			{	
				$si=$za->statIndex($i);
				if(strcmp($inName,substr($si['name'],9,-4))!=0)
					continue 2;
			}
			$liste[]= $inName;
        	$za->close();
		}
        $handle->close();
		$liste = array_diff($liste,array(""));  
		echo implode("|",$liste);
    }

    public function sorgula($turSor,$depoSor,$stokSor)
    {
        $eskiDepo="";
        $yazi="";
        $mesaj="";
		$i=0;
		foreach ($GLOBALS["db"]->query("SELECT * FROM stokDepo ORDER BY depoID") as $row)
            if(($depoSor=="" || $depoSor==$row['depoID']) && ($stokSor=="" || $stokSor==$row['stokID']))
            {  
				$sth = $GLOBALS["db"]->prepare("SELECT * FROM stok WHERE ID='$row[stokID]'");
				$sth->execute();
				$row3 = $sth->fetch();
                if($turSor==3 || $turSor==$row3['stokTur'])
                {   
                    if($eskiDepo!=$row['depoID'])
                    {   
                        if($i!=0)
                            $yazi.="|".$i.$mesaj;
                        $i=0;
                        $mesaj="";
                        $eskiDepo=$row['depoID'];
						$sth = $GLOBALS["db"]->prepare("SELECT * FROM depo WHERE ID='$eskiDepo'");
						$sth->execute();
						$row1 = $sth->fetch();
						$sth = $GLOBALS["db"]->prepare("SELECT * FROM personel WHERE ID='$row1[personel]'");
						$sth->execute();
						$row2 = $sth->fetch();
						$sth = $GLOBALS["db"]->prepare("SELECT COUNT(stokID) as csi,SUM(miktar) as sm FROM stokDepo WHERE depoID='$eskiDepo'");
						$sth->execute();
						$row4 = $sth->fetch();
                        $mesaj.="|".$row1['tanim']."|".$row2['tanim']."|".$row4['csi']."|".$row4['sm'];
                    }
                    $i++;
					$sth = $GLOBALS["db"]->prepare("SELECT birimKisaltma FROM birim WHERE ID='$row3[temelOlcuBirim]'");
					$sth->execute();
					$BK = $sth->fetchColumn();
                    $mesaj.="|".$row3['tanim1']."|".$row['miktar']."|".$BK;
                }
            }
       	 	echo $i.$mesaj.$yazi;
    }
	
	public function pngGoster($file)
	{
		if(guvenliResim($file))
			echo $file;
		else
			unlink($file);
	}
}
$depoSorguSecim=new depoSorguAjax();
if(isset($_GET['turSor'])){   $depoSorguSecim->sorgula(temizSayi($_GET['turSor']),temizYazi($_GET['depoSor']),temizYazi($_GET['stokSor']));}
if(isset($_GET['ls'])){   $depoSorguSecim->listele();}
if(isset($_GET['png'])){   $depoSorguSecim->pngGoster($_GET['png']);}