<?php
require_once ('../common/template1.php');
?>
    <h3>**Alfa Surumu.</h3>
    <h3>**Menuden bir islem secin.</h3>
<?php
if(isset($_GET['code']))
{
	echo "<center><i>";
	switch($_GET['code'])
	{
		case 400:
			echo "Gezgininiz veya vekil sunucunuz bu sunucunun tanimadigi bir istemde bulundu";
			break;
		case 401:
			echo "Eger bu dokumana erisim izniniz varsa, lutfen kimliginizi ve parolanizi kontrol edip tekrar deneyin.";
			break;
		case 403:
			if(isset($REDIRECT_URL) && preg_match("/\/$/",$REDIRECT_URL))
				echo "Talep etti&#287;iniz dizine eri&#351;im izniniz yok. Ya belirteç doküman yok, ya da dizin okumaya kar&#351;&#305; korumal&#305;.";
			else
				echo "Talep etti&#287;iniz dizine eri&#351;im izniniz yok.Dizin, ya okumaya kar&#351;&#305; korumal&#305;, ya da sunucu tarafindan okunam&#305;yor.";
			break;
		case 404:   
			echo "Talep etti&#287;iniz URL, sunucu uzerinde bulunmuyor.";
			if(isset($HTTP_REFERER))
				echo "<a href='$HTTP_REFERER'>Referans sayfa</a> uzerindeki ba&#287;lant&#305; güncel de&#287;il. Lütfen <a href='$HTTP_REFERER'>referans sayfa</a>'n&#305;n yazar&#305;n&#305; konuyla ilgili bilgilendirin.";
			else
				echo "URL'i kendiniz elle girdiyseniz, yaz&#305;m&#305;n&#305;z&#305; denetleyip tekrar deneyin.";
			break;
		case 405:
			echo isset($REDIRECT_REQUEST_METHOD) ? $REDIRECT_REQUEST_METHOD : "";
			echo " yontemi talep etti&#287;iniz URL icin kullan&#305;lamaz.";
			break;
		case 408:
			echo "Sunucu a&#287; ba&#287;lant&#305;s&#305;n&#305; kapatt&#305; cunku gezgin talebini belirlenmi&#351; sure icinde tamamlayamad&#305;.";
			break;
		case 410:   
			echo "Talep etti&#287;iniz URL bu sunucu uzerinde bar&#305;nd&#305;r&#305;lm&#305;yor ve herhangi bir yoneltme de mevcut de&#287;il.";
			if(isset($HTTP_REFERER))
				echo "Lütfen <a href='$HTTP_REFERER'>referans sayfan&#305;n</a> yazar&#305;na, bu ba&#287;lant&#305;n&#305;n güncel olmad&#305;&#287;&#305;n&#305; bildirin.";  
			else
				echo "Yabanc&#305; bir sayfadan bu ba&#287;lant&#305;y&#305; izlediyseniz,lutfen sozkonusu sayfan&#305;n yazar&#305; ile ileti&#351;ime gecin.";
			break;
		case 411:
			echo isset($REDIRECT_REQUEST_METHOD) ? $REDIRECT_REQUEST_METHOD : "";
			echo " metodunu kullanan bir talep gecerli bir <code>Content-Length</code> (icerik uzunlu&#287;u) ba&#351;l&#305;&#287;&#305; gerektirir.";
			break;
		case 412:
			echo "URL talebinin on&#351;art&#305;, olumlu sureci ba&#351;ar&#305;s&#305;zl&#305;kla sonland&#305;rd&#305;.";
			break;
		case 413:
			echo isset($REDIRECT_REQUEST_METHOD) ? $REDIRECT_REQUEST_METHOD : "";
			echo " yontemi iletilen veri tipini desteklemez, ya da veri hacmi kapasite limitlerini a&#351;&#305;yor. ";
			break;
		case 414:
			echo "Talep edilen URL'nin uzunlu&#287;u, sunucunun kapasite limitlerini a&#351;&#305;yor. Talep i&#351;lenemiyor.";
			break;
		case 415:
			echo "Sunucu, talep icinde iletilen ortam turunu desteklemiyor.";
			break;
		case 500:  
			if(isset($REDIRECT_ERROR_NOTES))
				echo "Sunucu icinde bir hata olu&#351;tu ve sunucu talebinize hizmet vermekte ba&#351;ar&#305;l&#305; olamad&#305;.<br>Hata mesaj&#305;:<br />$REDIRECT_ERROR_NOTES";
			else
				echo "Sunucu icinde bir hata olu&#351;tu ve sunucu talebinize hizmet vermekte ba&#351;ar&#305;l&#305; olamad&#305;.Ya sunucuya cok yuklenildi, ya da CGI betiklerinde hata belirdi.";
			break;
		case 501:
			echo "Sunucu, gezgin taraf&#305;ndan talep edilen yontemi desteklemiyor.";
			break;
		case 502:
			echo "Vekil (proxy) sunucu ustbirim (upstream) sunucusundan anlams&#305;z bir cevap ald&#305;.";
		case 503:
			echo "Sunucu, kendi icindeki ce&#351;itli sorunlardan oturu, bir sureli&#287;ine taleplerinize cevap veremeyecek. Lutfen daha sonra tekrar deneyin.";
			break;
		case 506:
			echo "Talep edilen eleman&#305;n bir de&#287;i&#351;keninin kendisi zaten payla&#351;&#305;l&#305;r bir kaynak. Eri&#351;im mumkun de&#287;il.";
			break;
	}
	echo "</i></center>";
}
require_once ('../common/template2.php');
?>