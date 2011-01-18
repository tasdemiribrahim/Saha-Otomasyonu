<?php
declare(encoding='UTF-8');
require_once '../common/db_connect.php';
@session_start();
if(isset($_SESSION['fsoYonetici']) && $_SESSION['fsoYonetici']!="")
{
	header("HTTP/1.1 301 Moved Permanently"); 
	header('Location:index.php');
}   	
if(isset($_POST['giris'])) 
{
	$password = $_POST['sifre'];
	$username = $_POST['login'];
	
	$root = new SimpleXMLElement('../common/sabitler.xml', NULL, true);
	$gizliAnahtar=$root->gizliAnahtar;    
	
	$password = hash_hmac('ripemd160',$password, $gizliAnahtar);	
	
	$sth = $db->prepare("SELECT tanim FROM personel WHERE ID='$username' AND sifre='$password' ");
	if ($sth->execute())
	{
		$record = $sth->fetch();
		if($record['tanim'])
		{
			$db->exec("DELETE FROM sessions WHERE data LIKE 'fsoYonetici|s:3:\"".$username."\";%'");
			$_SESSION['fsoYonetici'] = $username;
			$_SESSION["tarayici"] = $_SERVER["HTTP_USER_AGENT"];
			header("HTTP/1.1 301 Moved Permanently"); 
			header('Location:index.php');
			exit;
		}
	}
}
session_destroy();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="tr" xml:lang="tr">
<head>
    <link rel="license" title="Kuyas" href="http://www.kuyas.net/" />
    <title>Kuyas Yazılım Saha Otomasyonu</title>
    <meta name="author" content="İbrahim Taşdemir,Ramis Taşgın,Kelami Kaytaran,Gökhan Gökçe"/>
    <meta name="copyright" content="&copy;2009 Kuyas Yazılım Limited ?irketi" />
    <meta name="keywords" content="online,mobil,saha,otomasyon,kuyas,yazılım,profesyonel,iş çözümleri" />
    <meta name="robots" content="all" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="Window-target" content="_top" />
    <meta name="description" content="Kuyas Yazılım tarafından geliştirilen online ve mobil saha otomasyonu" />
	<!--[if IE]><script src="js/html5.js"></script><![endif]-->
	<link rel="stylesheet" type="text/css" href="css/login.css" />
    <link rel="shortcut icon" href="images/favicon.ico" />
</head>
<body>

<form id="login-form" method="post">

    <label for="login">Yönetici No:</label>
    <input type="text" id="login" name="login"/>
  	<div class="clear"></div>

    <p>
      <label for="password">Sifre:</label>
      <input type="password" id="password" name="sifre"/>
    </p>
    <p>&nbsp; </p>
<span class="left">
    	<input type="button" class="button" onClick="window.open('../common/sunucuKontrol.php','','status=1,resizable=1,scrollbars=yes')" value="Sunucu Kontrol" />
</span><span class="right">
<input type="submit" class="button" name="giris" value="Giriş"/>
    </span>
</form>
</body>
</html>