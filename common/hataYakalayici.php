<?php
declare(encoding='UTF-8');
date_default_timezone_set('Europe/Istanbul');
setlocale(LC_ALL, "tr_TR");
define("SCRIPT_PATH",realpath($_SERVER['DOCUMENT_ROOT']."/ekmekci"));
if(isset($_GET['parent']) && isset($_GET['url']) && isset($_GET['line']) && isset($_GET['agent']) && isset($_GET['msg']))
{
	$errMesaj=$_GET['parent'].":".$_GET['url'].":".$_GET['line'].":".$_GET['agent'].":".$_GET['msg'].PHP_EOL;
	$fp = fopen(SCRIPT_PATH."/hataJS.log", "a+");
	while(!feof($fp))
	{
		$satir=fgets($fp);
		if(0 == strcmp($satir,$errMesaj))
		{
			fclose($fp);
			exit;
		}
	}
	echo $errMesaj;
	fwrite($fp,$errMesaj);
	fclose($fp);
	exit;
}

if (count(get_included_files()) < 2) 
{
    header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: admin_login.php"); 
	exit;
}

function __autoload($name) 
{
    include_once str_replace("_", DIRECTORY_SEPARATOR, $name) . ".php";
}

function shutdown()
{
	echo "<br>Bellek Kullanimi:".memory_get_usage()." Byte<br>";
	if(in_array("ob_gzhandler",ob_list_handlers()))
	{
		echo "OB Tampon Buyuklugu:".ob_get_length()." Karakter<br>";
		ob_end_flush();
	}
}

//register_shutdown_function('shutdown');

function hataYakalayici($errno, $errmsg, $filename, $linenum, $vars) 
{ 
	if(error_reporting()==0)
		return;
    $errortype = array (
                E_ERROR              => 'Error',
                E_WARNING            => 'Warning',
                E_PARSE              => 'Parsing Error',
                E_NOTICE             => 'Notice',
                E_CORE_ERROR         => 'Core Error',
                E_CORE_WARNING       => 'Core Warning',
                E_COMPILE_ERROR      => 'Compile Error',
                E_COMPILE_WARNING    => 'Compile Warning',
                E_USER_ERROR         => 'User Error',
                E_USER_WARNING       => 'User Warning',
                E_USER_NOTICE        => 'User Notice',
                E_STRICT             => 'Runtime Notice',
                E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
                );
	
    $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
	//if(strpos($filename,SCRIPT_PATH)!==0)									// Dis hatalar atlanir
	//	return;	
	$filename = basename($filename);
	$errMesaj="$errortype[$errno]:$filename:$linenum:$errmsg";
    if (in_array($errno, $user_errors))
        $errMesaj .= serialize($vars);
	$errMesaj .=PHP_EOL;
	
	$fp = fopen(SCRIPT_PATH."/hata.log", "a+");
	while(!feof($fp))
	{
		$satir=fgets($fp);
		if(0 == strcmp($satir,$errMesaj))
		{
			fclose($fp);
			return;
		}
	}
	
	fwrite($fp,$errMesaj);
	fclose($fp);
}

function exYakalayici($exception) 
{
	$exMesaj="Exception: ".$exception->getMessage().PHP_EOL;
	$fp = fopen(SCRIPT_PATH."/hataEX.log", "a+");
	while(!feof($fp))
	{
		$satir=fgets($fp);
		if(0 == strcmp($satir,$exMesaj))
		{
			fclose($fp);
			return true;
		}
	}
	
	fwrite($fp,$exMesaj);
	fclose($fp);
	return true;
}

set_exception_handler('exYakalayici');
set_error_handler("hataYakalayici");

function temizYazi($string) 
{   
    $string = strip_tags($string);   
    if(get_magic_quotes_gpc()) 
        $string = stripslashes($string);   
    $string = mysql_real_escape_string($string); 
    return $string;   
}  

function temizSayi($int)
{
	return $int=="" ? $int : strval(intval($int));
}

function temizTel($no)
{
	$tel = filter_var($no, FILTER_SANITIZE_NUMBER_INT);
	$tel = str_replace('-', '', $tel);
	$tel = str_replace('+', '', $tel);
	$tel = str_replace('.', '', $tel);
	
	if(strlen($no) < 10 || strlen($no) > 10)
		return $no;
	 
	$alanKodu = substr($no, 0, 3);
	$orta = substr($no, 3, 3);
	$sonDort = substr($no, 6);
	 
	return $alanKodu . ' ' . $orta . ' ' . $sonDort;
}

function guvenliResim($file)
{
	if(($fp = fopen($file, "rb")) !== false)
	{
		$imageinfo = getimagesize($file);
		$line = fread($fp, 4);
		fclose($fp);
		if($line === "\377\330\377\340" && (strtolower(substr($file,-3))==="jpg" || strtolower(substr($file,-4))==="jpeg") && ($imageinfo['mime'] === "image/jpeg" || $imageinfo['mime'] === "image/pjpeg")) 
			return true;
		elseif(strtolower(substr($line,1)) === "png" && strtolower(substr($file,-3))==="png" && $imageinfo['mime'] === "image/png") 
			return true;
		elseif(strtolower(substr($line,0,3)) === "gif" && strtolower(substr($file,-3))==="gif" && $imageinfo['mime'] === "image/gif") 
			return true;
		else
			return false;
	}
	else
		return false;
}

function guvenliDosya($file)
{
	$uzanti=strtolower(substr($file,-3));
	if(strcmp($uzanti,"mp3")===0)
	{
		if(function_exists("id3_set_tag"))
		{
			if(id3_set_tag( $file, array("comment" => $_SESSION['fsoYonetici']." Tarafindan Yuklendi."), ID3_V1_0))
				return true;
			else
				return false;
		}
	}
	elseif(strcmp($uzanti,"ogg")===0)
	{
		if(extension_loaded("oggVorbis"))
		{
			$fp = fopen('ogg://'.$file, 'r');
			$metadata = stream_get_meta_data($fp);
			fclose($fp);
			$songdata = $metadata['wrapper_data'][0];
			if(isset($songdata['channels'],$songdata['bitrate'],$songdata['rate']))
				return true;
			else
				return false;
		}
	}
	else
		return true;
}