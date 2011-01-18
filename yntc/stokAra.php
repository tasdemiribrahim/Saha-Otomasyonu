<?php
require_once ('../common/db_connect.php');
require_once "PHPDoc\\redist\IT.php";
declare(encoding='UTF-8');
$root = new SimpleXMLElement('../common/sabitler.xml', NULL, true);
$HAMMADDE=$root->stokTuru->hammadde;
$YARIMAMUL=$root->stokTuru->yarimamul;
$MAMUL=$root->stokTuru->mamul;
$kod="";
if(isset($_GET['araKod']))
	$kod=$_GET['araKod'];

$options="";
foreach ($db->query("SELECT * FROM depo") as $row)
    $options.=" <option value='$row[ID]'>".$row['tanim']."</option>";
$GOVDE = <<<END
<div id="div1">
    <table class="konum_list">
        <tr><td><label for="stokAraKod">Stok Kodu:</label></td><td><input class="arama" type="text" id="stokAraKod" name="stokAraKod"/></td></tr>		
        <tr><td><label for="stokAraTanim">Tanim:</label></td><td><input class="arama" type="text" id="stokAraTanim" name="stokAraTanim"/></td><tr>
    </table>
</div>      
<div id="div2">
    <table class="konum_list"> 
        <tr><td><label for="stokAraDepo">Depo:&nbsp;&nbsp;&nbsp;&nbsp;</label></td><td><select class="aramaSelect" name='stokAraDepo' id='stokAraDepo'>
                <option value=''>Hepsi</option>
                $options;
            </select> </td></tr>
        <tr><td><label for="stokAraTur">Stok Türü:</label></td><td><select class="aramaSelect" name='stokAraTur' id='stokAraTur'>
                <option value="3">Hepsi</option>
                <option value="$HAMMADDE">Hammadde</option>
                <option value="$YARIMAMUL">Yarı Mamül</option>
                <option value="$MAMUL">Mamül</option>
            </select></td></tr>
    </table>
</div>      
<br clear="all">
    <br><br>
        <table class="tbl_list" id="araTablo" border="0" cellpadding="1" cellspacing="1">
			<thead id="alan" align="center">
				<tr class="tbl_baslik" id="baslik" align="center">
					<th style='width: 10%'>Kod</th>
					<th style='width: 20%'>Tür</th>
					<th style='width: 25%'>Tanım1</th>
					<th style='width: 25%'>Tanım2</th>
					<th style='width: 20%'>Depo</th>
				</tr>
			</thead>
			<tbody id="tabloGovde" align="center"></tbody>
		</table>
        <br><label id="anaUyari" class="uyari"></label><br><br>
        <div id="sayfaNo"></div>
END;

$tpl = new IntegratedTemplate('../common/');
$tpl->loadTemplateFile('templateAra.tpl');
$tpl->setVariable('title', 'Stok Arama');
$tpl->setVariable('script', 'stokAra');
$tpl->setVariable('govde', $GOVDE);
$tpl->show();  

?>