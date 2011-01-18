<?php
require_once ('../common/db_connect.php');
require_once "PHPDoc\\redist\IT.php";
declare(encoding='UTF-8');
$anaDeger="";
if(isset($_POST['ana']))
    $anaDeger=$_POST['ana'];

if(isset($_POST['kaydet']))
{
	if($anaDeger!="")
	{
		$girdiler=explode("-",$anaDeger);
    	for($i=1;$i<count($girdiler);$i++)
			$db->exec("INSERT INTO irsaliyeayar (ID,title) VALUES (NULL,'$girdiler[$i]')");
	}
	
	?>
	<script language="javascript">
		 opener.location.reload();
		 window.close(); 
	 </script>
	<?
}
$options=""; 
foreach ($db->query("SELECT title FROM irsaliyeayar") as $row)
	$options.=$row['title']."<br>";

$GOVDE = <<<END
<form method="post">
	<label for="deger">Tanım Ana Bilgi:&nbsp;&nbsp;</label><input type="text" name="deger" id="deger" />&nbsp;&nbsp;<input type="button" class="button" id="ekle" value="EKLE" />
	<br /><br/>
	<div class="ayarlar" id="ayarlar">
		$options
	</div>
	<div class="ayarlar" >
		<input type="hidden" id="ana" name="ana" value=""/>
		<input type="submit" class="button" id="kaydet" name="kaydet" value="KAYDET"/>
	</div>
</form>
END;

$tpl = new IntegratedTemplate('../common/');
$tpl->loadTemplateFile('templateAra.tpl');
$tpl->setVariable('title', 'İrsaliye Ayarlari');
$tpl->setVariable('script', 'ayar');
$tpl->setVariable('govde', $GOVDE);
$tpl->show();  