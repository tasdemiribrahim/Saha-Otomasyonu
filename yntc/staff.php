<?php
require_once ('../common/template1.php');
?>
<h2>Personel Listeleme ve Duzenleme</h2>
<table style="width:100%">
    <tr>
        <td><label for="tanim">Tanım:</label></td><td><input type="text" name="tanim" id="tanim"/><label for="tanim" id="tanimUyari" class="uyari"></label></td>
        <td><label for="sifre">Sifre:</label></td><td><input type="text" name="sifre" id="sifre"/><label for="sifre" id="sifreUyari" class="uyari"></label></td>
        <td>
            <input type="button" class="button kaydet" name="kaydet" id="kaydet" value="KAYDET"/>
            <input type="button" class="button guncelle" name="guncelle" id="guncelle" value="GUNCELLE"/>
            <input type="button" class="button iptal" name="iptal" id="iptal" value="IPTAL"/>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <br><label id="anaUyari" class="uyari"></label><br><br>
            <table id="tablo" class="tbl_list" border="0" cellspacing="1">
            	<thead id="alan" align="center">
                	<tr class="tbl_baslik" id="baslik" align="center">
                    	<th style='width: 20%'>Personel Kodu</th>
                        <th style='width: 50%'>Personel Tanımı</th>
                        <th rowspan='2' style='width: 30%'>İşlemler</th>
                    </tr>
                    <tr class="tbl_baslik" id="araDeger" align="center">
                    	<th><input type='text' name='kodAra' class="ara" id='kodAra'></th>
                        <th><input type='text' name='tanimAra' class="ara" id='tanimAra'></th>
                    </tr>
                </thead>
                <tbody id="tabloGovde" align="center"></tbody>
            </table>
            <br><br>
            <div id="sayfaNo"></div>
        </td>
    </tr>
</table>
<?php
require_once ('../common/template2.php');
?>  