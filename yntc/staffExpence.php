<?php
require_once ('../common/template1.php');
?>           
  <h2>Personel Gider Hesapları</h2>
  <form method="POST">
    <table cellpadding="10" >
        <tr>
            <td>
                <select name="personel" id="personel">
                    <option value="">Personel Seçiniz</option>
                    <?php
						foreach ($db->query("SELECT * FROM personel") as $satir)
                            echo "<option value='$satir[ID]'>$satir[tanim]</option>";
                    ?>
                </select>
                <label for="personel" id="personelUyari" class="uyari"></label>
            </td>
            <td>
                <label for="miktar">Miktar:</label>
                <input type="text" name="miktar" id="miktar"/>
                <select name="paraBirim" id="paraBirim">
            		<option value=''>Seçiniz</option>
                    <?php
						$birimOption="";
						foreach ($db->query("SELECT * FROM birim WHERE birimTur='Para'") as $oku)
                            $birimOption.= "<option value='$oku[ID]' id='$oku[birim]'>$oku[birimKisaltma]</option>";
						echo $birimOption;
                    ?>
                </select>
                <label for="miktar" id="miktarUyari" class="uyari"></label>
            </td>
            <td>
                <label for="tarih">Tarih:</label>
                <input type="text" name="tarih" id="tarih" readonly value="<?php echo date('Y-m-d');?>" />
                <input type="button" class="button" name="datepicker" value="..." onclick="displayDatePicker('tarih');" />
            </td>
        </tr>
    </table>
    <div id="div1">
        <label id="anaUyari" class="uyari"></label>
    </div>
    <div id="div2">
        <input type="button" class="button" name="kaydet" id="kaydet" value="KAYDET" />
        <input type="button" class="button" name="guncelle" id="guncelle" value="GUNCELLE" />
        <input type="reset" class="button" name="iptal" id="iptal" value="IPTAL" />
    </div>
    <br clear="all";
  </form>
  	<table id="tablo" class="tbl_list" border="0" cellpadding="1" cellspacing="1">
        <thead id="alan" align="center">
        	<tr class="tbl_baslik" id="baslik" align="center">
            	<th style='width: 10%'>İşlem Kodu</th>
                <th style='width: 30%'>Personel Tanımı</th>
                <th style='width: 10%'>Miktar</th>
                <th style='width: 10%'>Birim</th>
                <th style='width: 10%'>Tarih</th>
                <th rowspan='2'>İşlemler</th>
            </tr>
            <tr class="tbl_baslik" id="araDeger" align="center">  
            	<th><input type='text' name='kodAra' class="ara" id='kodAra'></th>
                <th><input type='text' name='tanimAra' class="ara" id='tanimAra'></th>
                <th><input type='text' name='miktarAra' class="ara" id='miktarAra'></th> 
                <th><select name='birimAra' id='birimAra'>
                    <option value=''>Seçiniz</option>
                    <?php echo $birimOption ?>
                </select></th>  
                <th><input type='text' name='tarihAra' class="ara" id='tarihAra'></th>   
            </tr> 
        </thead> 
        <tbody id="tabloGovde" align="center"></tbody> 
  	</table>
  <br /><br /><br />
  <div id="sayfaNo"></div>
<?php
require_once ('../common/template2.php');
?>