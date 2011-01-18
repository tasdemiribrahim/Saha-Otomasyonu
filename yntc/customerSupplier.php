<?php
require_once ('../common/template1.php');
$root = new SimpleXMLElement('../common/sabitler.xml', NULL, true);
$MUSTERI=$root->konumTuru->musteri;
$TEDARIKCI=$root->konumTuru->tedarikci;
$MUSTERI_TEDARIKCI=$root->konumTuru->musteri_tedarikci;
$kod= isset($_GET['araKod']) ? $_GET['araKod'] : NULL;
?>
   <h2>Müşteri ve Tedarikçi</h2>
      <form method="POST" id="form">
      <table class="konum_list">
          <tr>
            <td><label for="kod">Konum Kodu:</label></td>
            <td><input type="text" name="kod" id="kod"/></td><td><label for="kod" id="kodUyari" class="uyari"></label>&nbsp;&nbsp;&nbsp;</td>
            <td><label for="tanim">Konum Tanimi:</label></td>
            <td><input type="text" name="tanim" id="tanim"/><label for="tanim" id="tanimUyari" class="uyari"></label></td>
          </tr>
          <tr>
            <td><label for="adres">Adres:</label></td>
            <td><input type="text" name="adres" id="adres"/></td><td>&nbsp;&nbsp;&nbsp;</td>
            <td><label for="telefon">Telefon:</label></td>
            <td><input type="text" name="telefon" id="telefon"/></td>
          </tr>
          <tr>
              <td><label for="vergiNo">Vergi No:</label></td>
              <td><input type="text" name="vergiNo" id="vergiNo"/></td><td>&nbsp;&nbsp;&nbsp;</td>
              <td><label for="vergiDaire">Vergi Dairesi:</label></td>
              <td><input type="text" name="vergiDaire" id="vergiDaire"/></td>
          </tr>
          <tr>
            <td>Tür:</td>
            <td>
              <label for="turMusteri">Müşteri</label><input type="checkbox" name="turMusteri" id="turMusteri"/>
              <label for="turTedarikci">Tedarikçi</label><input type="checkbox" name="turTedarikci" id="turTedarikci"/><br>
              <input type="hidden" name="musteriDeger" id="musteriDeger" value="<?php echo $MUSTERI; ?>"/>
              <input type="hidden" name="tedarikciDeger" id="tedarikciDeger" value="<?php echo $TEDARIKCI; ?>"/>
              <input type="hidden" name="musteriTedarikciDeger" id="musteriTedarikciDeger" value="<?php echo $MUSTERI_TEDARIKCI;?>"/>
            </td><td><label id="turUyari" class="uyari"></label></td>
          </tr>
          <tr><td colspan="4"><label id="anaUyari" class="uyari"></label></td>
            <td colspan="5" align="right">
                <input type="button" class="button" name="kaydet" id="kaydet" value="KAYDET"/>
                <input type="button" class="button" name="guncelle" id="guncelle" value="GUNCELLE"/>
                <input type="reset" class="button" name="iptal" id="iptal" value="IPTAL"/>
            </td>
          </tr>
      </table>
      </form>
      <table class="tbl_list" id="tablo" border="0" cellpadding="1" cellspacing="1">
          <thead id="alan" align="center">
          	<tr class="tbl_baslik" id="baslik" align="center">
            	<th style='width: 15%'>Konum Kodu</th>
                <th style='width: 35%'>Konum Tanımı</th>
                <th style='width: 25%'>Konum Tur</th>
                <th style='width: 25%' rowspan='2'>İşlemler</th>
            </tr>
            <tr class="tbl_baslik" id="araDeger" align="center">
            	<th><input type='text' name='kodAra' class="ara" id='kodAra' style='width:75px;'></th>
                <th><input type='text' name='tanimAra' class="ara" id='tanimAra' style='width:150px;'></th>
                <th><select name='turAra' id='turAra' style='width:150px;'>
                    <option value="">Seçiniz</option>
                    <option value="<?php echo $MUSTERI; ?>">Müşteri</option>
                    <option value="<?php echo $TEDARIKCI; ?>">Tedarikçi</option>
                    <option value="<?php echo $MUSTERI_TEDARIKCI;?>">Müşteri&Tedarikçi </option>
                    </select>
                </th>
            </tr>
          </thead>
          <tbody id="tabloGovde" align="center"></tbody>
      </table>
      <br>
      <div id="sayfaNo"></div>
<?php
require_once ('../common/template2.php');
?>