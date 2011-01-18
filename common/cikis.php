<?php
require_once ('../common/db_connect.php');
declare(encoding='UTF-8');
$_SERVER['PHP_AUTH_USER']=$_SERVER['PHP_AUTH_PW']='';
@session_start();
unset($_SESSION['fsoYonetici'],$_SESSION["tarayici"]);
session_unset();
session_destroy();	
header('Location: ../yntc/admin_login.php');