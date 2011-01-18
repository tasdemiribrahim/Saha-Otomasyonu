<?
require ('../common/db_connect.php');
require_once 'Crypt/CHAP.php';
$giris=$_POST['giris'];
if(isset($giris)) {
    
	$cr = new Crypt_CHAP_MSv1();
    $personelNo=$_POST['personelNo'];
    $sifre=$_POST['sifre'];
	$cr->password = $sifre;
	$sifre = bin2hex($cr->lmPasswordHash());
    $query = sprintf("SELECT 1 FROM personel WHERE ID='%s' AND sifre='%s';",temizYazi($personelNo), $sifre);
    $result = mysql_query($query);
    if (mysql_num_rows($result) > 0) 
    {   
        setcookie("fso_kullanici", $personelNo, time() + (2*365*24*60*60 )); // 2 sene boyunca silinmez
        header('Location:index.php');
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title></title>
<head></head>
<body>

<form method="post">
<table align="left" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>Kullanıcı Kodu:</td>
    <td>
      <input type='text' name ='personelNo' maxlength="3" style="width:90px"/>
    </td>
  </tr>
  <tr>
    <td>?ifre:</td>
    <td>
      <input type='password' name ='sifre' maxlength="8"  style="width:90px"/>
    </td>
  </tr>
  <tr>
    <td></td>
    <td>
      <input type='submit' name='giris' value='Giriş'>
    </td>
  </tr>
</table>
</form>

</body>
</html>
