var sayfaKayitSay = 10;
function sayfaNoEkle()
{   
    var aktifSayfa = 0;
    var satirSay = $(".tbl_list").find('tbody tr').length;
    var sayfaSay = Math.ceil(satirSay / sayfaKayitSay);
	$('<ul id="pagination-flickr"></ul>').appendTo($("#sayfaNo"));
	$('<li class="active"><a class="sayfaLink">1</a></li>').appendTo($("#pagination-flickr"));
    for (var i=1; i< sayfaSay; i++) 
            $('<li><a href="#" class="sayfaLink">'+(i + 1) +' </a></li>').appendTo($("#pagination-flickr"));
    sayfaDegistir(aktifSayfa);
}

function sayfaDegistir(aktifSayfa)
{
    var bas = aktifSayfa * sayfaKayitSay;
    var son = (aktifSayfa + 1) * sayfaKayitSay - 1;
    $(".tbl_list").find("tbody tr").show().end().find("tbody tr:lt("+bas+")").hide().end().find("tbody tr:gt("+son+")").hide();
}

function sayfaClick()
{
	$(".active a").attr('href','#');
    $(".active").removeClass('active');
    $(this).removeAttr('href').parent().addClass('active');
    var aktifSayfa = $(this).html() - 1;
    sayfaDegistir(aktifSayfa);
}