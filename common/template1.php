<?php
require_once ('../common/db_connect.php');
declare(encoding='UTF-8');
ob_start("ob_gzhandler");
@session_start();
if(!isset($_SESSION['fsoYonetici']) || $_SESSION['fsoYonetici']=="" || !isset($_SESSION["tarayici"]) || $_SESSION["tarayici"] != $_SERVER["HTTP_USER_AGENT"])
{
 	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: admin_login.php"); 
	exit;
} 
$dir='./js/';
$file=substr($_SERVER['PHP_SELF'],14,-4);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" lang="tr" xml:lang="tr">
<head>
    <link rel="license" title="Kuyas" href="http://www.kuyas.net/" />
    <title>Kuyas Yazýlým Saha Otomasyonu</title>
    <meta name="author" content="Ýbrahim Taþdemir,Ramis Taþgýn,Kelami Kaytaran,Gökhan Gökçe"/>
    <meta name="copyright" content="2009 Kuyas Yazýlým Limited Sirketi" />
    <meta name="keywords" content="online,mobil,saha,otomasyon,kuyas,yazýlým,profesyonel,iþ çözümleri"/>
    <meta name="robots" content="all" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="Window-target" content="_top" />
    <meta name="description" content="Kuyas Yazýlým tarafýndan geliþtirilen online ve mobil saha otomasyonu" />
	<!--[if IE]><script src="js/html5.js"></script><![endif]-->
    <style type="text/css">	@import "css/style.css"; </style>
    <link rel="shortcut icon" href="images/favicon.ico" />
</head>

<body class="mainBody">           
<div id="container">
    <div id="navbar">
        <ul id="navigation" class="horizontal">
            <li><a href="#" class="acilir">Stok <img src="../yntc/images/leftArrow.png" width="10px" height="10px"></a>
                <ul>
                    <li><a href="movements.php">Hareketler</a></li>
                    <li><a href="irsaliye.php">Irsaliye</a></li>
                    <li><a href="stok.php">Stok Islemleri</a></li>
                    <li><a href="prices.php">Fiyat Girisi</a></li>
                </ul>
            </li>
            <li><a href="#" class="acilir">Depo <img src="../yntc/images/leftArrow.png" width="10px" height="10px"></a>
                <ul>
                    <li><a href="depo.php">Islemler</a></li>
                    <li><a href="depoSorgu.php">Sorgu</a></li>
                </ul>
            </li>
            <li><a href="#" class="acilir">Personel <img src="../yntc/images/leftArrow.png" width="10px" height="10px"></a>
                <ul>
                    <li><a href="staff.php">Islemler</a></li>
                    <li><a href="staffExpence.php">Gider</a></li>
                </ul>
            </li>
            <li><a href="customerSupplier.php">Konum Islemleri</a></li>
            <li><a href="../common/cikis.php">Cikis</a></li>
          </ul>
    </div>
    <div id="kullaniciBar" align="right">
        <label id="kullaniciIsim"><?php    
				$sth = $db->prepare("SELECT tanim FROM personel WHERE ID='$_SESSION[fsoYonetici]'");
				if ($sth->execute())
				{
					$record = $sth->fetch(PDO::FETCH_ASSOC);
					echo $record['tanim'];
				}
        ?></label>
    </div>
<div id="main">