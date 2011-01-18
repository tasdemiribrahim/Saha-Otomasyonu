<?php
require_once ('../common/template1.php');
$ID=0;

if(isset($_GET['ID'])){$ID=$_GET['ID'];}
?>
<h2>
    <input type="radio" title="Yeni satış irsaliye girmek için tıklayın" class="irTur" name="irsaliyeTur" id="satisIrsaliye" value="0" checked /><label for="satisIrsaliye"> Satış İrsaliyesi</label>
    <input type="radio" title="Yeni alış irsaliye girmek için tıklayın" class="irTur" name="irsaliyeTur" id="alisIrsaliye" value="1" /><label for="alisIrsaliye"> Alış İrsaliyesi</label>
</h2>
<table class="konum_list">
    <tr>
        <td><input type=button class="button" title="İrsaliye aramak için tıklayın" onclick="window.open('irsaliyeAra.php','','status=1,width=650,height=550,resizable=0,resize=no')" value="No:" /></td>
        <td><input type="text" id="ID" name="ID" value="<?php echo $ID?>" /><label for="ID" id="IDUyari" class="uyari"></label></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><label for="musteri">Müş/Ted:</label></td>
        <td><input type="text" class="suggest"  name="musteri" id="musteri" /><label for="musteri" id="musteriUyari" class="uyari"></label></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="ayarButon" name="ayarButon" title="İrsaliyelere yeni veri alanı eklemek için tıklayın" type="button" class="button" onclick="window.open('ayar.php','','status=1,width=400,height=300,resizable = 0,resize=no')" value="AYARLAR"></td>
    </tr>
    <tr>
        <td><label for="tarih">Tarih:</label></td>
        <td><input type="text" id="tarih" name="tarih" value=""/><label for="tarih" id="tarihUyari" class="uyari"></label></td> 
        <td />
        <td><label for="teslimTarih">Teslim:</label></td>
        <td><input type="text" id="teslimTarih" name="teslimTarih" value=""/><label for="teslimTarih" id="teslimUyari" class="uyari"></label></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
        <td><input id="irsaliyeSil" name="irsaliyeSil" title="İrsaliyeyi ve detay bilgilerini siler.Depo girdi ve çıktılarını geri verir." type="button" class="button" value="  KALDIR  "></td>
    </tr>
    <?php
        $i=1;
		foreach ($db->query("SELECT * FROM irsaliyeayar") as $row)
        {
            if($i%2==1)
                echo "<tr>";
			$sth = $db->prepare("SELECT value FROM dinamikBilgi where ID='$row[ID]' and stokID='$ID'");
			$sth->execute();
			$value = $sth->fetchColumn();
            echo "<td><label for='".$row['title']."'>".$row['title'].":</label></td><td><input id='".$row['title']."' type='text' class='ekstra' value='".$value."'/></td>";
            if($i%2==0)
                echo "</tr>";
            else
                echo "<td \>";
            $i=$i+1;
        }
        if($i%2==0)
            echo "</tr>";
    ?>
</table>
<input type='hidden' id='irDetay' name='irDetay' value=''/>
<br clear="all">
<label id="anaUyari" class="uyari"></label>
<fieldset id="detayIcerik">
<legend>İrsaliye Detay</legend>
    <table>
        <tr>
            <td><label for="stok">Stok:</label></td>
            <td><select name='stok' id='stok'>
            <option value=''>Seçiniz</option>
            <?php
				foreach ($db->query("SELECT tanim1 FROM stok") as $row)
                    echo " <option value='$row[tanim1]'>".$row['tanim1']."</option>";
            ?>
            </select><label for="stok" id="stokUyari" class="uyari"></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><label for="depo">Depo:</label></td>
            <td><select name='depo' id='depo'>
            <option value=''>Seçiniz</option>
            <?php
				foreach ($db->query("SELECT * FROM depo") as $row)
                    echo " <option value='$row[ID]'>".$row['tanim']."</option>";
            ?>
            </select><label for="depo" id="depoUyari" class="uyari"></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><label for="miktar">Miktar:</label></td>
            <td><input type='text' id='miktar' name='miktar' size=10><label id="miktarUyari" for="miktar" class="uyari"></label></td>
            <td><label id="birimLabel"></label> <label id="detayUyari" class="uyari"></label></td>
        </tr>
        <tr>
            <td />
            <td><input type='button' class="button" id='ekle' value='EKLE'></td>
            <td />
            <td><input type='button' class="button" id='guncelle' value='GUNCELLE'>&nbsp;&nbsp;<input type='button' class="button" id='iptal' value='IPTAL'></td>
            <td />
            <td><input type='text' class='donusum' id='ikinciMiktar' name='ikinciMiktar' size=10></td>
            <td><select class='donusum' name='ikinciBirim' id='ikinciBirim'></select></td>
        </tr>
    </table>
</fieldset>
<table class="tbl_list" id="tablo" border="0" cellpadding="1" cellspacing="1">
    <thead id="alan" align="center">
        <tr class="tbl_baslik" id="baslik" align="center">
            <th style="width: 10%">NO</th><th style="width: 20%">Stok</th><th style="width: 20%">Depo</th><th style="width: 20%">Miktar</th><th style="width: 20%">Birim</th><th style="width: 10%">Sil</th>
        </tr>
    </thead>
    <tbody id="tabloGovde" align="center"></tbody>
</table>
<br>
<div id="sayfaNo"></div>

<?php
require_once ('../common/template2.php');
?>