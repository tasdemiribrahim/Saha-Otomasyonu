<?php
require_once ('../common/template1.php');
$root = new SimpleXMLElement('../common/sabitler.xml', NULL, true);
$HAMMADDE=$root->stokTuru->hammadde;
$YARIMAMUL=$root->stokTuru->yarimamul;
$MAMUL=$root->stokTuru->mamul;
$kod= isset($_GET['araKod']) ? $_GET['araKod'] : NULL;
?>
<h2>Stok İşlemleri</h2>
<form name="form" id="form" action="stok.php" method="post" enctype="multipart/form-data">
    <table class="konum_list">
        <tr>
            <td><input type=button id="stokAra" class="button" value="Stok Kodu:" title="Stok araması yapmak için tıklayın"/></td>
            <td><input type="text" name="kod" id="kod" class="suggest" size="10" <?php if($kod!="") echo "value='$kod'"; ?>/></td>
            <td><label for="kod" id="kodUyari" class="uyari"></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    		<td><label for="tanim1">Tanım 1:</label></td><td><input type="text" name="tanim1" id="tanim1" class="suggest" size="20"/><label for="tanim1" id="tanimUyari" class="uyari"></label></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><label for="depoID">Depo:</label></td>
        	<td>
                <select name="depoKod" id="depoID" >
                    <option value="0">Seçiniz</option>
                    <?php
					foreach ($db->query("SELECT tanim,ID FROM depo") as $oku)
                    	echo "<option value='$oku[ID]' id='$oku[ID]'>$oku[tanim]</option>";
                    ?>
                </select>
    		</td>
    	</tr>
   	 	<tr>
        	<td><label for="stokTur">Stok Türü</label></td>
        	<td>
                <select name="stokTur" id="stokTur" >
                    <option value="">Seçiniz</option>
                    <option value="<?php echo $HAMMADDE; ?>">Hammadde</option>
                    <option value="<?php echo $YARIMAMUL; ?>">Yarı Mamül</option>
                    <option value="<?php echo $MAMUL; ?>">Mamül</option>
                </select>
    		</td>
            <td><label for="stokTur" id="turUyari" class="uyari"></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    		<td><label for="tanim2">Tanım 2:</label></td><td><input type="text" name="tanim2" id="tanim2" class="suggest" size="20"/></td><td colspan="4"><label id="anaUyari" class="uyari"></label></td>
    	</tr>
	</table>    
    <hr width="100%" />
    <table class="konum_list stokDosyaResimTable">  
    	<tr>         
            <td align="center">
            	<div id="resimUpload" >Resim Yükle</div>
            </td>
    		<td align="left">
            	<div id="dosyaUpload" >Dosya Yükle</div>
            </td>
   		</tr>
        <tr>
        	<td>
                <div id="mainbody">
                <center id="resimStatus" ></center>
                <ul id="resimler" ></ul>
                </div>
            </td>
    		<td>
                <center id="dosyaStatus" ></center>
                <ul id="dosyalar" ></ul>
            </td>
        </tr>
    </table>
    <hr width="100%" />
    <table class="konum_list">
        <tr>
            <td><label for="fiyatBirim">Fiyat Birimi:</label></td><td>
                <select name="fiyatBirim" id="fiyatBirim">
                <option value="0" id="0">Seçiniz</option>
                <?php
					foreach ($db->query("SELECT * FROM birim WHERE birimTur='Para'") as $oku)
                        echo "<option value='$oku[ID]' id='$oku[ID]'>$oku[birimKisaltma]</option>";
                ?>
                </select>
            </td>
            <td /><td /><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
   			<td><label for="temelOlcuBirim">Temel Ölçü Birimi:</label>
    			<select name="temelOlcuBirim" id="temelOlcuBirim">
   				<option value=''>Seçiniz</option>
				<?php
					foreach ($db->query("SELECT * FROM birim WHERE birimTur!='Para'") as $oku)
						echo "<option value='$oku[ID]' id='$oku[ID]'>$oku[birimKisaltma]</option>";
                ?>
    			</select>
    		</td>
        </tr>
        <tr>
            <td><label for="alisFiyat">Alış Fiyatı:</label></td>
            <td><input type="text" name="alisFiyat" id="alisFiyat" size="8" maxlength="11"/></td>
            <td><label for="alisKDV">Alış KDV&nbsp;&nbsp;:</label></td>
            <td><input type="text" name="alisKDV" id="alisKDV" size="1" maxlength="2" /></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td colspan="5" rowspan="10" width="80%" id="birimDonusumTablo">
    			<table id="donusumTablo" class="tbl_list" cellspacing="0" cellpadding="5" width="100%"></table>
    		</td>
        </tr>
        <tr>
            <td><label for="satisFiyat">Satış Fiyatı:</label></td>
            <td><input type="text" name="satisFiyat"  id="satisFiyat" size="8" maxlength="11" /></td>
            <td><label for="satisKDV">Satış KDV:</label></td>
            <td><input type="text" name="satisKDV" id="satisKDV" size="1" maxlength="2" /></td>
        </tr>
    	<tr><td>&nbsp;</td></tr>
    	<tr>
            <td><label for="agirlik">Ağırlık:</label></td>
            <td><input type="text" name="agirlik" id="agirlik" size="11" maxlength="11" /></td>
            <td>
                <select name="agirlikBirim" id="agirlikBirim">
                <option value="0">Seçiniz</option>
                <?php
					foreach ($db->query("SELECT * FROM birim WHERE birimTur='Agirlik'") as $oku)
                        echo "<option value='$oku[ID]' id='$oku[ID]'>$oku[birimKisaltma]</option>";
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="en">En:</label></td>
            <td><input type="text" name="en" id="en" size="11" maxlength="11" /></td>
            <td>
                <select name="enBirim" id="enBirim">
                <option value="0">Seçiniz</option>
                <?php
					$uzunluk="";
					foreach ($db->query("SELECT * FROM birim WHERE birimTur='Uzunluk'") as $oku)
                        $uzunluk.= "<option value='$oku[ID]' id='$oku[ID]'>$oku[birimKisaltma]</option>";
					echo $uzunluk;
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="boy">Boy:</label></td>
            <td><input type="text" name="boy" id="boy" size="11" maxlength="11" /></td>
            <td>
                <select name="boyBirim" id="boyBirim">
                    <option value="0">Seçiniz</option>
                    <?php echo $uzunluk?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="yukseklik">Yükseklik:</label></td>
            <td><input type="text" name="yukseklik" id="yukseklik" size="11" maxlength="11" /></td>
            <td>
                <select name="yukseklikBirim" id="yukseklikBirim">
                	<option value="0">Seçiniz</option>
                    <?php echo $uzunluk?>
                </select>
            </td>
        </tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
        <td><label for="barkod">Barkod:</label></td>
        <td><input type="text" name="barkod" id="barkod" size="11" maxlength="11" /></td>
    </tr><tr><td>&nbsp;</td><td id="barkodImage"></td></tr>
    </table>
<input type="hidden" id="eskiTemelOlcuBirim" value="" />
<input type="hidden" id="id" value="" />
<input type="hidden" id="eskiKod" value="" />
    <span id="div2">
        <input type="button" class="button" name="kaydet" id="kaydet" value='KAYDET'/>&nbsp;
        <input type="button" class="button" name="guncelle" id="guncelle" value='GUNCELLE'/>&nbsp;
        <input type="button" class="button" name="iptal" id="iptal" value='IPTAL'/>
    </span>
    <span id="buttonBul"></span>
    <br clear="all" />
</form>
<?php
require_once ('../common/template2.php');
?>