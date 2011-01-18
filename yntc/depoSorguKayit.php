<?php
setlocale(LC_ALL, "tr_TR");
define('FPDF_FONTPATH', '../fpdf/fonts/');
require_once ('../common/db_connect.php');
@include_once "Spreadsheet/Excel/Writer.php";
@include_once "../fpdf/ufpdf.php";
@include_once "System/Folders.php";
@include_once "../jpgraph/jpgraph.php";
@include_once "../jpgraph/jpgraph_pie.php";
declare(encoding='UTF-8');
@session_start();

$Ad="depoSorgu".date ("Y-m-d");
$sf = new System_Folders();
$geciciAd=$sf->getDesktop().$_SESSION['fsoYonetici'];
if(isset($_POST['baslik']))
	$baslik=preg_split("/[=]/", $_POST['baslik']);
class depoSorguKayit
{
    public function arsivle($tar)
    {
		$zip = new ZipArchive();
		$filename = "depo_sorgu_arsiv/".date ("Y-m-d").".zip";
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) 
		{
			echo "Hata1";
			exit;
		}		
        $this->pdf_olustur($GLOBALS["Ad"].".pdf",$tar);
        $this->xml_olustur($GLOBALS["Ad"].".xml",$tar);
        $this->excel_olustur($GLOBALS["Ad"].".xls",$tar);
		$v=array($GLOBALS["Ad"].".pdf",$GLOBALS["Ad"].".xml",$GLOBALS["Ad"].".xls");
		foreach($v as $arcDosya)
			$zip->addFile($arcDosya); 
		$zip->setArchiveComment($_SESSION['fsoYonetici'] . ' Tarafından Oluşturuldu.');
		$zip->close();
		foreach($v as $arcDosya)
			unlink($arcDosya);
	}
	
	public function grafik_olustur($var)					// STOK=DEPO=MIKTAR|STOK=DEPO=MIKTAR|...
    {
		$yeniStok="";
        $satirlar = preg_split("/[|]/", $var);
		array_pop($satirlar);
		while (list ($an, $satir) = each ($satirlar))
        {   
			$kayit = preg_split("/[=]/", $satir);
			$grap_vars[$kayit[0]][$kayit[1]]=$kayit[2];
        } 
		unset($var,$satirlar,$kayit);
		$size=0.5;
		while (list ($anahtar, $grup) = each ($grap_vars))
		{	
			unset($grup_veri,$grup_headers);
			while (list ($grup_head, $grup_deger) = each ($grup))
			{
				$grup_veri[]=$grup_deger;
				$grup_headers[]=$grup_head.PHP_EOL."%.1f%%";
			}
			$graph = new PieGraph(200,250,'auto');
			$graph->SetFrame(false);
			$graph->SetAntiAliasing();
			$p1 = new PiePlot($grup_veri);	
			$p1->SetSize($size);
			$p1->value->SetFont(FF_ARIAL,FS_NORMAL,10);
			$p1->value->Show();
			$p1->title->Set($anahtar);
			$p1->SetLabelType(PIE_VALUE_PER);
			$p1->SetLabels($grup_headers);
			$p1->SetLabelPos(0.6);

			$graph->Add($p1);
			$graphIsim="images/".$_SESSION['fsoYonetici'].$anahtar.".png";
			if(file_exists($graphIsim))
				unlink($graphIsim);
			$graph->Stroke($graphIsim);
			$resimler[]=$graphIsim;
		}
		echo implode("|",$resimler);
		unset($resimler);
    }
	
    public function excel_olustur($excelAd,$excelVeri)			// DEPO=PERSONEL=STOK=MIKTAR=BIRIM|DEPO=PERSONEL=STOK=MIKTAR=BIRIM|...
    {
        $xls =& new Spreadsheet_Excel_Writer($excelAd);
        $xls->setVersion(8);

        $sheet =& $xls->addWorksheet(date ("Y-m-d"));
        $sheet->setInputEncoding('utf-8');
        $sheet->setColumn(0,1,20);
        $sheet->freezePanes(array(1, 0));
        $sheet->hideScreenGridlines ();
        $sheet->setPaper(9);
        $sheet->hideGridlines ( );
        $sheet->setZoom ( 200 );

        $xls->setCustomColor(60,217,216,214);
        $xls->setCustomColor(62,234,234,234);

        $kalin =& $xls->addFormat();
        $kalin->setColor("black");
        $kalin->setAlign("left");
        $kalin->setPattern(1);
        $kalin->setLeft("1");
        $kalin->setRight("1");
        $kalin->setTop("1");
        $kalin->setFgColor(60);

        $ince =& $xls->addFormat();
        $ince->setAlign("left");
        $ince->setLeft("1");
        $ince->setRight("1");
        $ince->setFgColor(62);

        $baslikFormat =& $xls->addFormat();
        $baslikFormat->setBold();
        $baslikFormat->setAlign("center");
        $baslikFormat->setFgColor("black");
        $baslikFormat->setColor("white");
        $baslikFormat->setBorder("2");

        $alt =& $xls->addFormat();
        $alt->setTop("1");

        $satirlar = $this->parcala($excelVeri);
		unset($excelVeri);
		while (list ($anahtar, $satir) = each ($satirlar))
			while (list ($anahtar2, $veri) = each ($satir))
            {
                if($satir[0]=="")
                    $sheet->write($anahtar+1, $anahtar2, $veri, $ince);
                else
                    $sheet->write($anahtar+1, $anahtar2, $veri, $kalin);
            }
		$satirSay=count($satirlar)+1;
		foreach($GLOBALS["baslik"] as $anahtar=>$bas)
		{
        	$sheet->write(0, $anahtar, $bas, $baslikFormat);
			$sheet->write($satirSay,$anahtar, "", $alt);
		}
		unset($satirlar);
        $xls->close();
    }

    public function xml_olustur($xmlAd,$xmlVeri)				// DEPO=PERSONEL=STOK=MIKTAR=BIRIM|DEPO=PERSONEL=STOK=MIKTAR=BIRIM|...
    {
		$dom = new DOMDocument('1.0', 'iso-8859-1');
		$dom->loadXML("<deposorgu tarih=\"".date("Y-m-d")."\"></deposorgu>");
		$satirlar = $this->parcala($xmlVeri);
		unset($xmlVeri);
		while (list ($anahtar, $satir) = each ($satirlar))
		{
			if($satir[0]!="")
			{	
				$depo = $dom->createElement("depo");
				$depo->setAttribute("tanim", $satir[0]);
				$depo->setAttribute("personel", $satir[1]);
				$depo->setAttribute("stokSayisi", $satir[2]);
				$depo->setAttribute("toplamMiktar", $satir[3]);
				$dom->documentElement->appendChild($depo);
			}
			else
			{
				$stok = $dom->createElement("stok",$satir[3]);
				$stok->setAttribute("tanim", $satir[2]);
				$stok->setAttribute("birim", $satir[4]);
				$depo->appendChild($stok);
			}
		}
		$dom->save($xmlAd);
    }

    public function pdf_olustur($pdfAd,$pdfVeri)			// DEPO=PERSONEL=STOK=MIKTAR=BIRIM|DEPO=PERSONEL=STOK=MIKTAR=BIRIM|...
    { 
		global $Ad,$baslik;
		@$pdf = new UFPDF();
		$pdf->Open();
        $i=0;
		
		$satirlar = $this->parcala($pdfVeri);
		unset($pdfVeri);
		while (list ($anahtar, $satir) = each ($satirlar))
			while (list ($anahtar2, $veri) = each ($satir))
				$data[$anahtar][$baslik[$anahtar2]]=$veri;
		$pdf->AddFont('Arial', '', 'arial.php');
		$pdf->SetFont('Arial', '', 14);
		$pdf->AddPage();
		$pdf->SetTitle($Ad);
		$pdf->SetAuthor('Kuyas Yazılım LTD. ŞTD.');
		
        $pdf->SetFillColor("#4A4342");
        $pdf->SetTextColor("#FFFFFF");
        $pdf->SetLineWidth(.3);
		$baslikSay=count($baslik);
		$width=190/$baslikSay;
		foreach($baslik as $anahtar=>$satir)
            $pdf->Cell($width,7,$satir,1,0,'C',1);
        $pdf->Ln();
        $pdf->SetTextColor(0);
		
		while (list ($anahtar, $row) = each ($data))
        {
            if($row[$baslik[0]]!="")
			{
				$pdf->Bookmark($row[$baslik[0]],0,-1);
                $pdf->SetFillColor("#D9D8D6");
			}
            else
                $pdf->SetFillColor("#EAEAEA");
			for($i=0;$i<$baslikSay;$i++)
            	$pdf->Cell($width,8,$row[$baslik[$i]],'LR',0,'C',1);
            $pdf->Ln();
        }
		
		$pdf->Cell($width*$baslikSay,0,'','T');
		unset($data);
		$pdf->AddPage();
		$pdf->Bookmark('İçerik',0,-1);
		$pdf->CreateIndex();
		
		$pdf->Close();
        $pdf->Output($pdfAd);
    }
	
	protected function parcala($string)
	{
		$satirlar = preg_split("/[|]/", $string);
		while (list ($anahtar, $satir) = each ($satirlar))
		{   
			$hucre = preg_split("/[=]/", $satir);
			while (list ($anahtar2, $veri) = each ($hucre))
			{
				if($veri=="null")
					$veri="";
				$deger[$anahtar][$anahtar2]=$veri;
			}
		}	
		return $deger;
	}
}
$depoSorguSecim=new depoSorguKayit();
if(isset($_POST['archive'])){      $depoSorguSecim->arsivle($_POST['archive']);}
if(isset($_POST['excel'])){    $depoSorguSecim->excel_olustur($geciciAd.".xls",$_POST['excel']);}
if(isset($_POST['pdf'])){      $depoSorguSecim->pdf_olustur($geciciAd.".pdf",$_POST['pdf']);}
if(isset($_POST['xml'])){      $depoSorguSecim->xml_olustur($geciciAd.".xml",$_POST['xml']);}
if(isset($_POST['grafik'])){	  $depoSorguSecim->grafik_olustur($_POST['grafik']);}