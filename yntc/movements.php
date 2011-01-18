<?php
require_once ('../common/template1.php');
$root = new SimpleXMLElement('../common/sabitler.xml', NULL, true);
$MUSTERI=$root->konumTuru->musteri;
$TEDERIKCI=$root->konumTuru->tedarikci;
$MUSTERI_TEDERIKCI=$root->konumTuru->musteri_tedarikci;
?>
<h2>Hareket Girişi ve Düzenleme</h2>
    <form method="post">
        <table style="width:100%;">
            <tr>
                <td>
                    <select name=personel id="personel" class="degisim">
                        <option value="">Personel Seçiniz</option>
                        <?php
							foreach ($db->query("SELECT * FROM personel") as $satir)
                                echo "<option value='$satir[ID]'>$satir[tanim]</option>";
                        ?>
                    </select><label id="personelUyari" class="uyari"></label>
                </td>
                <td>
                &nbsp;&nbsp;
                    <select name = konum id="konum" class="degisim">
                        <option value=0>Konum</option>
                        <?php
			foreach ($db->query("SELECT * FROM konum WHERE tur=".$MUSTERI." || tur=".$MUSTERI_TEDERIKCI) as $satir)
                                echo "<option value='$satir[ID]'>$satir[tanim]</option>";
                         ?>
                    </select><label id="konumUyari" class="uyari"></label>
                </td>
                <td>&nbsp;&nbsp;<input type="text" name="tarih" id="tarih" readonly value="<?php echo date('Y-m-d');?>" />
                <input type="button" class="button" name="datepicker" value="..." title="Tarih seçmek için tıklayın" onclick="displayDatePicker('tarih');" /></td>
                <td>&nbsp;&nbsp;<input type="button" class="button" name="listele" id="listele" value="Listele"/></td>
            </tr>
        </table>
    </form>
    <br><label id="anaUyari" class="uyari"></label><br><br>
    <table id="hareketListe" class="tbl_list2" border="0" cellspacing="1" align="center"></table>
    <table id="satisListe" class="tbl_list2" border="0" cellspacing="1" align="center"></table>
    <br><br>
    <table align="center">
        <tr>
            <td align="right">
                <input type="button" class="button" id="ekle"  value="EKLE" />
                <input type="button" class="button" id="guncelle"  value="GUNCELLE" />
                <input type="button" class="button" id="kaydet"  value="KAYDET" />
                <input type="button" class="button" id="iptal"  value="IPTAL" />
            </td>
        </tr>
    </table>
<?php
require_once ('../common/template2.php');
?>