<?php
require_once "../common/db_connect.php";
require_once "PHPDoc/redist/IT.php";
declare(encoding='UTF-8');

$GOVDE = <<<END
<div id="div1">
    <table class="konum_list">  
        <tr><td><label for="araID">No:</label></td><td><input type="text" class="arama" id="araID" name="araID"/></td> </tr>		
        <tr><td><label for="araMusteri">M/T:</label></td><td><input type="text" class="arama" name="araMusteri" id="araMusteri" /></td></tr>
        <tr>
            <td colspan=2 align="center">
                <select style="width:100px;" name="araTur" id="araTur" >
                    <option value="2" >Hepsi</option>
                    <option value="0" >Satış</option>
                    <option value="1" >Alış</option>
                </select>
            </td>
        </tr>
    </table>
</div>      
<div id="div2">
    <table class="konum_list">   
       <tr><td><label for="araTarih">Tarih:</label></td><td><input class="tarih" type="text" id="araTarih" name="araTarih"/></td> </tr>
       <tr><td><label for="araTeslimTarih">Teslim:</label></td><td><input type="text" class="tarih" id="araTeslimTarih" name="araTeslimTarih"/></td></tr>
    </table>
</div>
<br clear="all">
    <br><br>
    <table class="konum_list">
        <tr>
        <table class="tbl_list" id="araTablo" border="0" cellpadding="1" cellspacing="1" >
			<thead id="alan" align="center">
				<tr class="tbl_baslik" id="baslik" align="center">
					<th style='width: 5%'>No</th>
					<th style='width: 25%'>M/T</th>
					<th style='width: 15%'>Tarih</th>
					<th style='width: 15%'>Teslim</th>
					<th style='width: 10%'>Tur</th>
				</tr>
			</thead>
			<tbody id="tabloGovde" align="center"></tbody>
		</table>
        <br><label id="anaUyari" class="uyari"></label><br><br>
        <div id="sayfaNo"></div>
        </tr>
    </table>
END;

$tpl = new IntegratedTemplate('../common/');
$tpl->loadTemplateFile('templateAra.tpl');
$tpl->setVariable('title', 'İrsaliye Arama');
$tpl->setVariable('script', 'irsaliyeAra');
$tpl->setVariable('govde', $GOVDE);
$tpl->show();  

?>