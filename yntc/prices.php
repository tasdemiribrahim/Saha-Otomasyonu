<?php
require_once ('../common/template1.php');
?>
<h2>Müşteri Fiyat Matrisi</h2>
    <form name="form" action="stok.php" method="post" enctype="multipart/form-data">
       <table>
        <tr>
            <td>&nbsp;&nbsp;
                <select name ="konum" id="konum">
                    <option value="0">Müşteri Seçiniz</option>
                    <?php
						foreach ($db->query("SELECT * FROM konum WHERE tur='0' || tur='2'") as $satir)
                            echo "<option value=\"".$satir['ID']."\">".$satir['tanim']."</option>";
                    ?>
                </select>
                <input type="text" readonly name="tarih" id="tarih" value="<?php echo date('Y-m-d');?>" />
                <input type="button" class="button" name="tarihSec"  id="tarihSec" value="..." onclick="displayDatePicker('tarih');" />
                <br><br><br><br>
            </td>
        </tr>
    
        <tr>
            <td>
                <table id="tablo" class="tbl_list2" border="0" cellspacing="1" align="center"></table>
                <p align="center"><input type=button class="button" name="guncelle" id="guncelle" value="GUNCELLE">
            </td>
        </tr>  
        <tr>
            <td>
            	<label id="anaUyari" class="uyari"></label>
            </td>
        </tr>
      </table>
    </form>
<?php
require_once ('../common/template2.php');
?>