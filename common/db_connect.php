<?php 
include_once('hataYakalayici.php');
declare(encoding='UTF-8');
if (count(get_included_files()) < 2) 
{
    header("HTTP/1.1 301 Moved Permanently");
	header("Location: admin_login.php"); 
	exit;
}
$db = new PDO('mysql:host=localhost;dbname=fso_ekmekci', "root", "");

session_set_save_handler('_open','_close','_read','_write','_destroy','_clean'); 
function _open() 
{
	if(isset($GLOBALS["db"]))
		return true; 
	else
		return false;
}

function _close() 
{  
	$GLOBALS["db"]=null;
	return true;
}
 
function _read($id)
{
	$id = temizYazi($id); 
	$sth = $GLOBALS["db"]->prepare("SELECT data FROM sessions WHERE id = '$id'");
	if ($sth->execute())
	{
		$record = $sth->fetch(PDO::FETCH_ASSOC);
		return $record['data'];
	}
	return ''; 
}
 
function _write($id, $data) 
{
	$_sess_db = mysql_connect("localhost","root", "" );
	mysql_select_db("fso_moto" ); 
	$access = time();
	$id = temizYazi($id);
	$access = temizYazi($access);
	$data = temizYazi($data);
	$sonucDeger = mysql_query("REPLACE INTO sessions VALUES ('$id', '$access', '$data')",$_sess_db);
	mysql_close($_sess_db);
	return $sonucDeger;
} 
function _destroy($id) 
{
	$id = temizYazi($id);
	return  $GLOBALS["db"]->exec("DELETE FROM sessions WHERE id = '$id'");
	//mysql_query("DELETE FROM sessions WHERE id = '$id'",$_sess_db); 
}
function _clean($max)
{
	$old = time() - $max;
	$old = temizYazi($old);
	return $GLOBALS["db"]->exec("DELETE FROM sessions WHERE access < '$old'");
	//mysql_query("DELETE FROM sessions WHERE access < '$old'",$_sess_db); 
} 
$db->exec("SET NAMES 'utf8'");
$db->exec("SET CHARACTER SET utf8");
$db->exec("SET COLLATION_CONNECTION = 'utf8_unicode_ci'");