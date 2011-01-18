<?php
declare(encoding='UTF-8');
if(isset($_GET['eklentiYonet']))
{ 
	$kontrol=$_GET['eklentiYonet'];
	$REQUIRED_EXTENSIONS = array('Spreadsheet_Excel_Writer','fpdf_fpdf','System_Folders','PHPDoc_redist_IT','Image_Barcode');
	if($kontrol==2)
	{
		$r['extensions']=array();
		$kaynak = dirname(dirname($_SERVER['SCRIPT_FILENAME']))."/yntc";
		$ayarlar = array('ignore_dirs' => array('css', 'depo_sorgu_arsiv','images','js','stok_dosyalari','stok_resimleri'));
		do
		{
			foreach (split(";", ini_get('include_path')) as $incPath)
				if(file_exists($incPath."PHP/CompatInfo.php"))
				{	
					require_once 'PHP/CompatInfo.php';
					$pci = new PHP_CompatInfo('null');
					$r = $pci->parseArray(array($kaynak,"./"),$ayarlar);
					break 2;
				}
			echo "<script language='javascript'> alert('CompatInfo Yuklu Olmadigindan Bazi Paket Bilgileri Alinamiyor');</script>";
		}while(0);
		
		$REQUIRED_EXTENSIONS = $r['extensions'];
		unset($r);
	}
	$_allErrors = null;
	
	class InstallationChecker {
		
		const PHP_EXTENSION_ERRORS = 'PHP Fonksiyonlari';
		const PEAR_EXTENSION_ERRORS = 'PEAR Paketleri';
		const PHP_MANUAL_LINK_FRAGMENT = 'http://php.net/manual/en/book.';
		const PHP_PEAR_LINK_FRAGMENT = 'http://pear.php.net/package-info.php?package=';

		public function __construct()
		{
			global $kontrol,$_allErrors;
			
			$this->validatePHPExtensions();
			while(list($key,$value) = each($_allErrors))
			{	
				echo "<tr><td colspan=2 class=\"verification_type\"><strong>".$key."</strong></td></tr>";
				while(list($i,$AllErrorsErrors) = each($value['errors']))
				{
					if($value['warn'][$i])
						echo "<tr><td class=\"error\">" . $value['tested'][$i] . "</td><td class=\"error\">" . $AllErrorsErrors . "</td></tr>";
					else
						echo "<tr><td class=\"notError\">" . $value['tested'][$i] . "</td><td class=\"notError\">" . $AllErrorsErrors . "</td></tr>";
				}
			}
		}
		
		private function validatePHPExtensions()
		{
			global $kontrol,$_allErrors,$REQUIRED_EXTENSIONS;
			$phpExtensionErrorFunctions = array();
			$phpExtensionErrors = array();
			while(list($key,$requiredExtension) = each($REQUIRED_EXTENSIONS))
			{	
				$chk=0;
				if($kontrol==1)
				{
					$path=split(";", ini_get('include_path'));
					while(list($key,$incPath) = each($path))
						if(file_exists($incPath.str_replace("_","/",$requiredExtension).".php"))
						{
							$chk=1;
							break;
						}
				}
				elseif (extension_loaded($requiredExtension)) 
					$chk=1;
				if(!$chk)
				{
					$phpExtensionErrors[] = $kontrol==2 ? 
					$this->checkAndAddHTMLLink(self::PHP_MANUAL_LINK_FRAGMENT . $requiredExtension . '.php') : 
					$this->checkAndAddHTMLLink(self::PHP_PEAR_LINK_FRAGMENT . $requiredExtension);
					$phpExtensionErrorWarnings[] = true;
					$requiredExtension .= $kontrol==2 ? ' fonksiyonu eksik' : ' paketi eksik';
				}
				else
				{
					$phpExtensionErrors[] = $kontrol==2 ? ' Fonksiyon var' : ' Paket var';
					$phpExtensionErrorWarnings[] = false;
				}
				$phpExtensionErrorFunctions[]=$requiredExtension;	
			}
			$icerik= $kontrol==2 ? self::PHP_EXTENSION_ERRORS : self::PEAR_EXTENSION_ERRORS;
			
			$_allErrors[$icerik]['tested'] = $phpExtensionErrorFunctions; 
			$_allErrors[$icerik]['warn'] = $phpExtensionErrorWarnings; 
			$_allErrors[$icerik]['errors'] = $phpExtensionErrors;
			
			unset($phpExtensionErrorFunctions,$phpExtensionErrorWarnings,$phpExtensionErrors);
		}
	
		private function checkAndAddHTMLLink($inputString) 
		{
			return '<a href="'. $inputString . '" target="_blank">' . $inputString . '</a>';
		}
	}
	
	$installationChecker = new InstallationChecker();
}
elseif(isset($_GET['filtre']))
{
	echo "<tr><td colspan=2 class=\"verification_type\"><strong>Filtreler</strong></td></tr>";
	foreach(filter_list() as $id =>$filter) 
		echo "<tr><td class=\"notError\">$filter</td><td class=\"notError\">".filter_id($filter)."</td></tr> ";
}
elseif(isset($_GET['veriTabani']))
{
	function veriTabaniKontrol($vt)
	{
		echo " <tr> <td ";
		if($vt=="ibase")
			echo ( function_exists( 'ibase_connect' ) && function_exists( 'ibase_server_info' )) ? 'class="notError">iBase</td><td class="notError">'.ibase_server_info() : 'class="error">iBase</td><td class="error">Yok';
		elseif($vt=="mysql")
			echo function_exists( 'mysql_connect' ) ? 'class="notError">MySQL</td><td class="notError">'.@mysql_get_server_info() : 'class="error">MySQL</td><td class="error">Yok';
		elseif($vt=="oci")
			echo function_exists( 'oci_connect' ) ? 'class="notError">Oracle</td><td class="notError">'.ociserverversion() : 'class="error">Oracle</td><td class="error">Yok';
		elseif($vt=="sqlite")
			echo function_exists( 'sqlite_open' ) ? 'class="notError">SQLite</td><td class="notError">'.sqlite_libversion() : 'class="error">SQLite</td><td class="error">Yok';
		else
			echo function_exists( $vt.'_connect' ) ? 'class="notError">'.strtoupper($vt).'</td><td class="notError">Var' : 'class="error">'.strtoupper($vt).'</td><td class="error">Yok';
	echo "</td></tr>";	
	}
	
	echo "<tr><td colspan=2 class=\"verification_type\"><strong>Veri Tabanlari</strong></td></tr>";
	
	$veriTabanlari=array("ibase","ifx","ldap","msql","mssql","mysql","odbc","oci","pg","sqlite","sybase");
	sort($veriTabanlari);
	while(list($key,$value)=each($veriTabanlari))
		veriTabaniKontrol($value);
	echo "<tr><td colspan=2 class=\"verification_type\"><strong>PDO Veritabani Suruculeri</strong></td></tr>";
	$drivers=PDO::getAvailableDrivers();
	while(list(,$driver)=each($drivers))
		echo "<tr><td class=\"notError\"></td><td class=\"notError\">".$driver."</td></tr> ";
	
}
elseif(isset($_GET['diger']))
{
	function dPgetIniSize($val) 
	{
	   $val = trim($val);
	   if (strlen($val <= 1)) return $val;
	   $last = $val{strlen($val)-1};
	   switch($last) 
	   {
		   case 'k':
		   case 'K':
			   return (int) $val * 1024;
			   break;
		   case 'm':
		   case 'M':
			   return (int) $val * 1048576;
			   break;
		   default:
			   return $val;
	   }
	}
	
	function phpIniKontrol($iniVeri)
	{
		echo " <tr> <td ";
		if($iniVeri=="phpversion")
		{
			echo version_compare(PHP_VERSION, '5', '<') ? 'class="error">PHP Versiyon</td><td class="error">' : 'class="notError">PHP Versiyon</td><td class="notError">';
			echo phpversion();
		}
		elseif($iniVeri=="cgi")
		{
			echo (php_sapi_name() == 'cgi') ? 'class="error">Sunucu API</td><td class="error">' : 'class="notError">Sunucu API</td><td class="notError">';
			echo php_sapi_name();
		}
		elseif($iniVeri=="file_uploads")
		{
			echo ini_get("file_uploads") ? 'class="notError">Dosya Yukleme</td><td class="notError">' : 'class="error">Dosya Yukleme</td><td class="error">';
			$maxfileuploadsize = min(dPgetIniSize(ini_get('upload_max_filesize')), dPgetIniSize(ini_get('post_max_size')));
			$memory_limit = dPgetIniSize(ini_get('memory_limit'));
			if ($memory_limit > 0 && $memory_limit < $maxfileuploadsize) $maxfileuploadsize = $memory_limit;
			if ($maxfileuploadsize > 1048576) 
				echo (int)($maxfileuploadsize / 1048576) . 'M';
			 else if ($maxfileuploadsize > 1024) 
				echo (int)($maxfileuploadsize / 1024) . 'K';
		}
		elseif($iniVeri=="tarayici")
			echo (stristr($_SERVER['HTTP_USER_AGENT'], 'msie') == true) ? 'class="error">Tarayici</td><td class="error">IE guvenlik riskleri, onbellekleme problemleri, javascript sorunlari, HTML5 teknolojisini desteklememesi vb. problemler yuzunden Firefox kullanmaniz tavsiye edilir.' : 'class="notError">Tarayici</td><td class="notError">'.$_SERVER['HTTP_USER_AGENT'];
		elseif($iniVeri=="sunucu")
			echo (stristr($_SERVER['SERVER_SOFTWARE'], 'apache') == false) ? 'class="error">Sunucu</td><td class="error">Bu yazilim apache sunucusunda gelistirilmis ve denenmistir. Diger sunucularda hatalar olusabilir.' : 'class="notError">Sunucu</td><td class="notError">'.$_SERVER['SERVER_SOFTWARE'];
		else
		{
			echo ini_get($iniVeri) ? 'class="error">'.strtoupper($iniVeri).'</td><td class="error">' : 'class="notError">'.strtoupper($iniVeri).'</td><td class="notError">';
			echo "Kapali Olmasi Tavsiye Edilir";	
		}
		echo "</td></tr>";
	}
	
	echo "<tr><td colspan=2 class=\"verification_type\"><strong>Genel Ayarlar</strong></td></tr>";
	
	$iniVerileri=array('file_uploads','phpversion','cgi','safe_mode','register_globals','magic_quotes_gpc','magic_quotes_runtime','scream.enabled','session.auto_start','tarayici','sunucu');
	while(list($key,$value)=each($iniVerileri))
		phpIniKontrol($value);
}
elseif(isset($_GET['sifre']))
{
	echo "<tr><td colspan=2 class=\"verification_type\"><strong>HASH Algoritmalari</strong></td></tr>";
	$algos=hash_algos();
	while(list(,$algo)=each($algos))
		echo "<tr><td class=\"notError\"></td><td class=\"notError\">".$algo."</td></tr> ";
}
elseif(php_sapi_name() != "cli")
{
echo '
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN\" "http://www.w3.org/TR/html4/strict.dtd\">
	<html>
		<head>
			<link rel="stylesheet" type="text/css" href="../yntc/css/style.css">
			<script language="javascript" type="text/javascript" src="../yntc/js/jquery-1.3.1.js"></script>
			<title>Sunucu Kontrolu</title>
		</head>
		<body id="sunucuKontrol">
			<table class="verification_table">
			</table>
		</body>
	</html>
	<script>
	
	$(document).ready(function(){
		$.ajaxSetup({
			type: "GET",
			url: "sunucuKontrol.php"
    	});
		$(".verification_table").append("<caption>PHP Gereklilik Kontrolcusu Calistirildi '.gmdate("c").'</caption>");
		$.ajax({data: "diger=1",success: function(cevap){
				$(".verification_table").append(cevap);
				$.ajax({data: "filtre=1",success: function(cevap){
						$(".verification_table").append(cevap);
						$.ajax({data: "veriTabani=1",success: function(cevap){
								$(".verification_table").append(cevap);
								$.ajax({data: "eklentiYonet=2",success: function(cevap){
										$(".verification_table").append(cevap);
										$.ajax({data: "eklentiYonet=1",success: function(cevap){
												$(".verification_table").append(cevap);
												$.ajax({data: "sifre=1",success: function(cevap){
														$(".verification_table").append(cevap);
		}});}});}});}});}});}});});
	</script>';
}