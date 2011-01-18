<?php
require_once ('../common/template1.php');
?>     
    <h2></h2>
    <br>
    <label for="depoSor">Depo:&nbsp;&nbsp;&nbsp;&nbsp;</label>
    <select class='sorgu' name='depoSor' id='depoSor'>
        <option value=''>Hepsi</option>
        <?php
			foreach ($db->query("SELECT * FROM depo") as $row)
                echo " <option value='$row[ID]'>".$row['tanim']."</option>";
        ?>
    </select>
    <label for="turSor">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;		
    Tür:&nbsp;&nbsp;&nbsp;&nbsp;</label>
    <select class='sorgu' name='turSor' id='turSor'>
        <option value='3' >Hepsi</option>
        <option value='0' >Hammadde</option>
        <option value='1' >Yarı Mamül</option>
        <option value='2' >Mamül</option>
    </select>
    <label for="stokSor">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 		
    Stok:&nbsp;&nbsp;&nbsp;&nbsp;</label>
    <select class='sorgu' name='stokSor' id='stokSor'>
        <option value=''>Hepsi</option>
        <?php
			foreach ($db->query("SELECT * FROM stok") as $row)
                echo " <option value='$row[ID]'>".$row['tanim1']."</option>";
        ?>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 		
    <span id="grafikGetirHolder"><a href="#" id="grafikGetirLink" title="jpgraph ile dinamik olarak oluşturulan pasta grafiği">Grafik Getir</a></span>
    <br><label id="anaUyari" class="uyari"></label><br><br>
    <table class="tbl_list" id="tablo" cellpadding="1" cellspacing="1">
        <thead id="alan" align="center">
            <tr class="tbl_baslik" id="baslik" align="center">
                <th style="width: 25%">Depo</th>
                <th style="width: 25%">Personel</th>
                <th style="width: 20%">Stok</th>
                <th style="width: 20%">Miktar</th>
                <th style="width: 10%">Birim</th>
            </tr>
        </thead>
        <tbody id="tabloGovde" align="center"></tbody>
    </table>
    <br> <div id="sayfaNo"></div><br clear=all>
<table class="tbl_iki_sutun"><tr /><tr><td>
<table cellpadding="2" cellspacing="2" border="2px">
        <tbody id="arsiv" align="center"></tbody>
</table></td><td rowspan="5" align="center">
<input type="checkbox" name="pdf" id="pdf" value="1"><label for="pdf"><img src="images/pdf.ico" />PDF</label>
<input type="checkbox" name="excel" id="excel" value="1"><label for="excel"><img src="images/excel.png" />Excel</label>
<input type="checkbox" name="xml" id="xml" value="1"><label for="xml"><img src="images/xml.ico" />XML</label>
<input type="checkbox" name="archive" id="archive" value="1" title="PDF,XML,XLS Olarak Arşivler"><label for="archive" title="PDF,XML,XLS Olarak Arşivler"><img src="images/zip.png" />Arşivle</label><br><input type=button class="button" id="kaydet" value="KAYDET"><br><span class="uyari arsivUyari"></span>
</td></tr></table>
<?php
require_once ('../common/template2.php');
?>