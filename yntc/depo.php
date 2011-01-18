<?php
require_once ('../common/template1.php');
?>
      <h2>Depo İşlemleri</h2>
     <form name="form" id="form" action="depo.php" method="post">
     <table class="konum_list">
         <tr>
             <td><label for="kod">Kod:</label></td><td><input type="text" name="kod" id="kod" size="10" /><label for="kod" id="kodUyari" class="uyari"></label></td>
             <td><label for="tanim">Tanım:</label></td><td><input type="text" name="tanim" id="tanim" size="20" /><label for="tanim" id="tanimUyari" class="uyari"></label></td>
             <td><label for="personel">&nbsp;&nbsp;&nbsp;&nbsp;Personel:</label></td>
             <td><select name='personel' id='personel'>
             <option value=''>Seçiniz</option>
             <?php
				$sql = 'SELECT ID,tanim FROM personel';
				foreach ($db->query($sql) as $row)
                     echo " <option value='$row[ID]'>".$row['tanim']."</option>";
             ?>
             </select></td>
        </tr>
        <tr>
             <td colspan="2"><input type="checkbox" name="durum" id="durum" value="1"><label for="durum">Depoyu Kapat</label>
             <td colspan="5"><label id="anaUyari" class="uyari"></label>
        </tr>
        <tr>
             <td colspan="2"><input type="checkbox" name="eksiBakiyeUyari" id="eksiBakiyeUyari" value="1"><label for="eksiBakiyeUyari">Eksi Bakiyede Uyarı Ver</label></td>
        </tr>
        <tr>
             <td colspan="2"><input type="checkbox" name="eksiBakiyeIzin" id="eksiBakiyeIzin" value="1"><label for="eksiBakiyeIzin">Eksi Bakiyeye İzin Verme</label></td>
        </tr>
        <tr><td colspan="6" align="right" >
                <input type='button' class="button" name='kaydet' id='kaydet' value='KAYDET' />
            <input type='button' class="button" name='guncelle' id='guncelle' value='GUNCELLE' />
            <input type='reset' class="button" name ='iptal' id='iptal' value='IPTAL' />
            </td>
        </tr>
     </table>
     </form>
     <table class="tbl_list" id="tablo" border="0" cellpadding="1" cellspacing="1">
     	<thead id="alan" align="center">
        	<tr class="tbl_baslik" id="baslik" align="center">
            	<th>Depo Kodu</th>
            	<th>Depo Tanımı</th>
                <th>Personel</th>
                <th rowspan='2' colspan='2'>İşlemler</th>
            </tr>
            <tr class="tbl_baslik" id="araDeger" align="center">
            	<th><input type='text' class="ara" name='kodAra' id='kodAra' style='width:75px;'></th>
                <th><input type='text' class="ara" name='tanimAra' id='tanimAra' style='width:150px;'></th>
                <th><input type='text' class="ara" name='personelAra' id='personelAra' style='width:150px;'></th>
            </tr>
        </thead>
        <tbody id="tabloGovde" align="center"></tbody>
     </table>
     <br><br>
     <div id="sayfaNo"></div>
<?php
require_once ('../common/template2.php');
?>