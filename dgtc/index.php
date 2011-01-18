<?
if(!isset($_COOKIE["fso_kullanici"])) {
  header('Location: login.php') ;
}

include ('../common/db_connect.php');

if (isset($yeniParti)) {

  $personel=$_POST['personelid'];
  if ($_POST['konum'] == 0) {
    
    $validationMessages = "Lutfen Konum Seciniz";

  } else {

    $sorguTamam = true;
    $datetime = date("Y-m-d H:i:s");
    $urunlist = mysql_query ("SELECT * FROM stok order by ID");
    $eklenenBorc = 0.0;
    $teslimat_miktar = 0;
    $iade_miktar = 0;


    while ($oku = mysql_fetch_assoc($urunlist)) {

      $teslimat_miktar = ${"g_".$oku['kod']};
      $iade_miktar = ${"i_".$oku['kod']};
      ${"g_".$oku['kod']}="";
      ${"i_".$oku['kod']}="";
      $fiyatSql = mysql_query("SELECT fiyat FROM fiyat WHERE  konumID = $konum AND stokID = $oku[ID]");
      while ($okuFiyat = mysql_fetch_assoc($fiyatSql)) {
        $fiyat = $okuFiyat['fiyat'];
      }
      if((!empty($teslimat_miktar) && $teslimat_miktar > 0)||($iade_miktar) && $iade_miktar > 0) {
        $ekleHareket = "INSERT INTO hareket (konum, personelID, urunID, alinanMiktar,iadeMiktar,tarih) VALUES ($konum,$personel, $oku[ID], $teslimat_miktar, $iade_miktar, '$datetime')";
        $sorguTamam = mysql_query($ekleHareket);
        $urunKontrol="SELECT * FROM stok WHERE ID=$oku[ID]";
        $urunSay=mysql_query($urunKontrol);
        while ($okudepoID = mysql_fetch_assoc($urunSay)) {
             $urunDepoID=$okudepoID['depoID'];
        }
        $stokDepoKontrol=mysql_query("SELECT * FROM stokDepo WHERE depoID=$urunDepoID AND stokID=$oku[ID]");
        $count=0;
        while ($okuStokDepo = mysql_fetch_assoc($stokDepoKontrol)) {
            $count++;
        }
        if($count==0)
        {
            $ekleStokDepo = "INSERT INTO stokDepo (stokID,depoID,miktar) VALUES ($oku[ID],$urunDepoID,'0')";
            $ekleTamam = mysql_query($ekleStokDepo);
        }
        $updateStokDepo="UPDATE stokDepo SET miktar=miktar-$teslimat_miktar WHERE stokID=$oku[ID]";
        $sorguTamam1 = mysql_query($updateStokDepo);
       
      }
      $eklenenBorc += (float)((float) $teslimat_miktar - (float) $iade_miktar) * (float) $fiyat;
    }

    if (empty($alinanMiktar)) {
      $alinanMiktar = 0;
    }

    $borcEkle = mysql_query("INSERT INTO borc VALUES('$datetime', $konum, $alinanMiktar, $eklenenBorc)");
    $alinanMiktar="";
  }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<head>
  <title>KUYAS Saha Otomasyonu</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <style>
    table {
      font-size:12px;
    }
  </style>
</head>

<body>

<?php
  $personelName = "";
  $secPersonel = mysql_query ("SELECT tanim FROM personel WHERE ID = " . $_COOKIE["fso_kullanici"]);
  $rsPersonel = mysql_fetch_object($secPersonel);
  if ($rsPersonel != null) {
    $personelName = $rsPersonel->tanim;
  }
?>
<form method="post">
<table cellpadding="1" cellspacing="0">
  <tr><td>Personel: <b><?= isset($personelName) ? $personelName : ""; ?></b> </td></tr>
  <tr>
    <td>
	  <input type="hidden" name="personelid" size="5" value="<?php echo $_COOKIE["fso_kullanici"]; ?>"/>
      <select name="konum" onChange="submit();" style="width:100%; border:1px solid gray">
      <option value="0">Konum Seciniz</option>
      <?
      $konumList = mysql_query ("SELECT * FROM konum WHERE tur = '2' OR tur = '0'  order by tanim"); 
      while($oku = mysql_fetch_assoc($konumList)) {

        if(isset($konum) && $konum==$oku['ID']) {
          echo "<option selected=\"selected\" value=\"".$oku['ID']."\">".$oku['tanim']."</option>";
        } else {
          echo "<option value=\"".$oku['ID']."\">".$oku['tanim']."</option>";
        }
      }
      ?>
      </select>
    </td>
  </tr>
  <tr>
    <td>
      <table border="0" width="200" cellpadding="1" cellspacing="1" style="color:white">
        <tr bgcolor="#5f4f4f">
          <td>Stok</td>
          <td>Miktar</td>
          <td>Iade</td>
        </tr>

        <?
        $i = 1;
        $sonuc = mysql_query ("SELECT * FROM stok WHERE stokTur = '2'"); 
        while ($oku = mysql_fetch_assoc($sonuc)) {
          $trcolor ="#948c8c";
          if($i % 2 == 0) {
              $trcolor = "#a09f9e";
          }
         echo "<tr bgcolor=$trcolor>
                  <td>$oku[tanim1]</td>
                  <td><input type=\"text\" name=\"g_$oku[kod]\" maxlength=\"5\" size=\"5\" value=\"${'g_'.$oku['kod']}\" style=\"border:1px solid gray\" /></td>
                  <td><input type=\"text\" name=\"i_$oku[kod]\" maxlength=\"5\" size=\"5\" value=\"${'i_'.$oku['kod']}\" style=\"border:1px solid gray\" /></td>
                </tr>";
           $i++;
        }
        if(!empty($konum)) {
          $datetime = date("Y-m-d");
          $alacak = 0;
          $borc = mysql_query("SELECT * FROM borc where konumID = $konum");
          while($oku = mysql_fetch_assoc($borc)) {            
            $alacak+= $oku['eklenen']-$oku['alinan'];
          }
        }
        
        ?>
         <tr bgcolor="#5f4f4f">
          <td>
            Borc | Alinan:
          </td>
          <td><?= isset($alacak) ? $alacak : 0; ?><input type="hidden" id="borcMiktar" size="5" disabled value="<?= isset($alacak) ? $alacak : 0; ?>" style="border:1px solid gray" /></td>
          <td>
            <input type="text" name="alinanMiktar" size="5" value="<?= isset($alinanMiktar) ? $alinanMiktar : 0; ?>" style="border:1px solid gray"/>
          </td>
        </tr>
        <tr>
          <td colspan="3" align=right>
            <input type="submit" name="yeniParti" value="Gönder" />
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr><td>Copyright &copy; 2009 <br/>Kuyas Yazlm Ltd. Sti.</td></tr>
</table>
</form>
</body>
</html>
