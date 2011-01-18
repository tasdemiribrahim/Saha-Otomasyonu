   </div>
    <div id="footer">Copyright &copy; 2009, <a href="http://www.kuyas.net" target="_blank" style="text-decoration:none;">Kuyas Yazilim Limited Sirketi</a></div>
</div>
</body>
</html>
<script language="javascript" type="text/javascript" src="js/jquery-1.3.1.js"></script>
<script type="text/javascript">

$(window).error(function(msg, url, line){
	$.ajax({
        type: 'GET',
        url: '../common/hataYakalayici.php',
        data: 'msg='+escape(msg)+'&url='+escape(url)+'&line='+escape(line)+'&parent='+escape(document.location.href)+'&agent='+escape(navigator.userAgent)
	});
});

$("img").error(function(){
  $(this).hide();
});

if (!this["console"]) {
    this.console = {};
}
var cn = ["assert", "count", "debug", "dir", "dirxml", "error", "group", "groupEnd", "info", "profile", "profileEnd", "time", "timeEnd", "trace", "warn", "log"];
var i = 0,
tn;
while ((tn = cn[i++])) {
    if (!console[tn]) { (function() {
            var tcn = tn + "";
            console[tcn] = ('log' in console) ?
            function() {
                var a = Array.apply({},
                arguments);
                a.unshift(tcn + ":");
                console["log"](a.join(" "));
            }: function() {}
        })();
    }
}

</script>
<script language="javascript" type="text/javascript" src="js/autocomplete.js"></script>
<script language="javascript" type="text/javascript" src="js/dropdownMenuKeyboard.js"></script>
<?php
$handle = dir($dir);
while ($filename = $handle->read())
	if($filename==$file.".js")
   	echo "<script language='javascript' type='text/javascript' src='$dir$filename'></script>";
?>