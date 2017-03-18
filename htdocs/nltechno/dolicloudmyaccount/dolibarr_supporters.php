<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>NLTechno Dolibarr project</title>
<meta name="robots" content="index,follow" />
<meta name="keywords" content="softwares, dolibarr, doliwamp, project, modules, open, source, opensource, erp, crm, grc, sponsor, sponsoring, nltechno" />
<meta name="description" content="Page of NLTechno sponsoring for Dolibarr ERP/CRM open source software" />
<link href="/css/css.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="main">
	
	<!--  START NAVIGATION AT THE TOP OF THE PAGE -->
	<!--
		<div id="top-nav">
		
			<a href="#" class="small-navigation">LINK</a> // <a href="#" class="small-navigation">LINK</a> // <a href="#" class="small-navigation">LINK</a> // <a href="#" class="small-navigation">LINK</a> // <a href="#" class="small-navigation">LINK</a>
	
		</div>
	-->	
	<!-- END NAVIGATION AT THE TOP OF THE PAGE -->
	
	
	<?php include("../header.php"); ?>
	

	<?php include("../barre.php"); ?>

	
	<!-- START MAIN CONTENT BLOCK -->

		<div id="content">
		
			<?php include("../title.php");
			showtitle('Dolibarr/DoliWamp supporter page','dolibarr_logo2.png');
			?>
			
<br />
<br />
This is a list of all Dolibarr/DoliWamp supporters that made a donation to help me
to maintain and enhance Dolibarr/DoliWamp freely.<br />
To join them, you can make a Paypal donation:
<?php $price=25; ?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="8969427">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<br />
This page is here to thanks all of them and provide a link to their site<!-- (Google pagerank of this page should be <b>5</b>, check made on august 2009). --><br />
Note that every supporter is not reported here, because, only thoose who ask it, are visible.<br />
If you are an Dolibarr/DoliWamp supporter (for a recent or old donation) and if you want to appear here,
just send me a mail at <a href="mailto:eldy@users.sourceforge.net">eldy@users.sourceforge.net</a>
to provide me following informations:<br />
- An url<br />
- A text for link.<br />
- The project you support by this donation (<a href="http://www.awstats.org" target="blank" alt="AWStats log analyzer official web site">AWStats</a>, <a href="http://awbot.sourceforge.net" target="blank" alt="AWBot official web site">AWBot</a>, <a href="http://cvschangelogb.sourceforge.net" target="blank" alt="CVSChangeLog Builder official web site">CVSChangeLogBuilder</a>, <a href="http://www.dolibarr.org" target="blank" alt="Dolibarr ERP/CRM official web site">Dolibarr</a>)<br />
The only requirement is that you made a donation via Paypal (at least <?php echo $price ?> euros or dollars).<br />
Then go on this "supporter page" of project, 10 days later to see if your link has been added. If not send me another mail (I may be 
on holidays or simply busy so please be patient until I process my Dolibarr/DoliWamp email box...)<br />
So thanks again to all of you...

<hr>

<b>List of donors</b><br />

<?php
/*
$databaseserver="mysql4-a";
$databasename="a13764_Dolibarr/DoliWamp";
$databaseuser="a13764admin";
$databasepassword="ld101010";

$resourcebase=mysql_connect($databaseserver,$databaseuser,$databasepassword);
mysql_select_db($databasename,$resourcebase);

$query="SELECT NAME FROM T_WHP";
$resql=mysql_query($query);
$num=mysql_num_rows($resql);
*/
?>

<!-- 25 -->
<a href="http://www.silkplantsdirect.com/artificial-flowers/floral-arrangement/view-all-products.html" target="_blank">Silk Flower Arrangements</a><br />

<a href="http://www.doreymedia.com" target="_blank">Web Design Surrey</a><br />

<!-- 0 -->
<a href="http://www.chiensderace.com" title="ChiensDeRace.com" target="_blank">ChiensDeRace.com</a><br />

<a href="http://www.chatsderace.com" title="ChatsDeRace.com" target="_blank">ChatsDeRace.com</a><br />

<a href="http://www.lesbonnesannonces.com" title="LesBonnesAnnonces.com" target="_blank">LesBonnesAnnonces.com</a><br />

<a href="http://www.pourmaplanete.com" title="PourMaPlanete.com" target="_blank">PourMaPlanete.com</a><br />

<a href="http://www.destailleur.fr" title="Laurent Destailleur" target="_blank">Destailleur.fr</a><br />

<a href="http://www.nltechno.com" title="NLTechno" target="_blank">NLTechno</a><br />

<br />
Thanks again...
<br />
Laurent Destailler, Lead developper of Dolibarr and DoliWamp.<br />
<?php
/*
mysql_close($resourcebase);
*/
?>

</div>

</body>
</html>
