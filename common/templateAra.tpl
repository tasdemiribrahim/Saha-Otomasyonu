<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" lang="tr" xml:lang="tr">
<head>
    <title>{title}</title>
    <link rel="license" title="Kuyas" href="http://www.kuyas.net/" />
    <meta name="author" content="brahim Tademir,Ramis Tagn,Kelami Kaytaran"/>
    <meta name="copyright" content="2009 Kuyas Yazilim Limited Sirketi" />
    <meta name="keywords" content="online,mobil,saha,otomasyon,kuyas,yazilim,profesyonel,is cozumleri" />
    <meta name="robots" content="all" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="Window-target" content="_top" />
    <meta name="description" content="Kuyas Yazilim tarafindan gelistirilen online ve mobil saha otomasyonu" />
	<!--[if IE]><script src="js/html5.js"></script><![endif]-->
   	<style type="text/css">	@import "css/style.css"; </style>
    <link rel="shortcut icon" href="images/favicon.ico" />
</head>

<body>
<h2 align=center>{title}</h2>
    <table cellspacing="0" cellpadding="0" style="width:100%; height:100%; vertical-align:top; background-color:#FFFFFF; border:1px solid #CCCCCC">
        <tr>
            <td style="height:100%; width:100%; padding:8px; vertical-align:top">
            {govde}

            </td>
        </tr>
    </table>
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
<script language="javascript" type="text/javascript" src="js/{script}.js"></script>