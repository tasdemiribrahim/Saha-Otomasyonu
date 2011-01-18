<?php
require_once ('../common/db_connect.php');
require_once "PHPDoc\\redist\IT.php";
declare(encoding='UTF-8');

$depo="";
$personel="";
$stok="";

foreach ($db->query("SELECT * FROM depo") as $row)
    $depo.=" <option value='$row[ID]'>".$row['tanim']."</option>";
foreach ($db->query("SELECT * FROM personel") as $row)
    $personel.=" <option value='$row[ID]'>".$row['tanim']."</option>";
foreach ($db->query("SELECT * FROM stok") as $row)
    $stok.= " <option value='$row[tanim1]'>".$row['tanim1']."</option>";

$kisi=$_GET['kisi'];
$sth = $db->prepare("SELECT * FROM konum WHERE tanim='$kisi'");
$sth->execute();
$row = $sth->fetch();


$GOVDE = <<<END
<div>
    <table class="konum_list">
        <tr><td>Konum:</td><td><input type=hidden id=isim value="$row[tanim]">$kisi</td></tr>
        <tr><td>Adres:</td><td>$row[adres]</td></tr>
        <tr><td>Tel:</td><td>$row[telefon]</td></tr> 
    </table>
</div>
<br clear="all">
<div id="div1">
    <table class="konum_list"> 
		<tr>
			<td><label for="depo">Depo:&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
			<td><select name='depo' id='depo'>
				<option value=''>Seciniz</option>
				$depo
			</select><label class="uyari" id="depoUyari"></label></td>
		</tr> 
		<tr>
			<td><label for="stok">Stok:&nbsp;</label></td>
			<td><select name='stok' id='stok'>
				<option value=''>Seciniz</option>
				$stok
			</select><label class="uyari" id="stokUyari"></label></td>
		</tr>
    </table>
</div>      
<div id="div2">
    <table class="konum_list"> 
            <tr>
				<td><label for="personel">Personel:&nbsp;&nbsp;&nbsp;&nbsp;</label></td>
                <td><select name='personel' id='personel'>
                    <option value=''>Seciniz</option>
                    $personel
                </select><label class="uyari" id="personelUyari"></label></td>
			</tr>
            <tr>
				<td><label for="miktar">Miktar</label></td><td><input id="miktar"><label class="uyari" id="miktarUyari"></label></td>
                <td>&nbsp;<label id="birimLabel">Birim Yok</label></td>
			</tr>
    </table>
</div>
<input style="position: relative; left: 40%;" type="button" class="button" id="ekle" value="EKLE">
<br><label id="anaUyari" class="uyari"></label><br>

<table class="tbl_list" border="0" cellpadding="1" cellspacing="1">
<thead align="center">
    <tr class="tbl_baslik" id="baslik" align="center">
        <th width=20%>Depo</th>
        <th width=20%>Personel</th>
        <th width=20%>Stok</th>
        <th width=10%>Miktar</th>
        <th width=20%>Birim</th>
        <th width=10%>Sil</th>
    </tr>
</thead>
</table>
<div id="kutu">
<table style="width:100%" id="araTablo" border="0" cellpadding="1" cellspacing="1">
    <tbody id="tabloGovde" align="center"></tbody>
</table>
</div>
<br><br><br>
<input type=button class="button" id="kaydet" value="KAYDET">
END;

$tpl = new IntegratedTemplate('../common/');
$tpl->loadTemplateFile('templateAra.tpl');
$tpl->setVariable('title', 'Mobil Sipariş');
$tpl->setVariable('script', 'tupcu');
$tpl->setVariable('govde', $GOVDE);
$tpl->show();  