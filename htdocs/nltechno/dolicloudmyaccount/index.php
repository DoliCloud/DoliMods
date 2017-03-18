<?php
/*
 * Page to download files provided by NLTechno
 */

$mesg='';
$salt='saltnltechno';
//key for module "dolidroid" with current salt is substr(md5("dolidroid".$salt), 0, 8) = "72f37402"
//print 'Download key for module dolidroid is '.substr(md5("dolidroid".$salt), 0, 8)."<br>\n";


if ($_SERVER['SERVER_PORT'] == 443) 
{
	//header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
   	exit();
}

if ($_POST["action"] == 'getdownloadlink')
{
	// Check $_POST["updatekey"] is   module-YYYYMMDD-key
	$email=$_POST["email"];
	$tmp=explode('-',strtolower($_POST["updatekey"]));
	$module=$tmp[0];
	$date=$tmp[1];
	$key=$tmp[2];	
	var_dump($tmp);

}

if ($_POST["action"] == 'getsources')
{
	// Check $_POST["updatekey"] is module-version-key
	$downloadkey=$_POST["downloadkey"];
	$tmp=explode('-',strtolower($downloadkey));
	$module=$tmp[0];
	$version=$tmp[1];	
	$key=$tmp[2];	
	// key must be $salt
	$hash=substr(md5($module.$version.$salt), 0, 8);
	//print 'ee'.$module.$version.$salt.'  '.$hash;exit;
	if (strtolower($hash) != $key) 
	{
		$mesg='<font style="color: #800">The key '.$downloadkey.' is not recognized.</font>';
	}
	else
	{
		$mesg='You can download sources with <a href="/download/modules/src_'.$module.'-'.$version.'-'.$key.'.zip">this link</a>';
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <base href="http://www.nltechno.com/" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="robots" content="index, follow" />
  <meta name="keywords" content="dolibarr, modules, update" />
  <meta name="title" content="NLTechno, the OpenSource company" />
  <meta name="description" content="NLTechno, the OpenSource company" />
  <meta name="generator" content="Joomla! 1.5 - Open Source Content Management" />
  <title>NLTechno, the OpenSource company</title>
  <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
  <link rel="stylesheet" href="http://www.nltechno.com/modules/mod_socialmedialinks/style.css" type="text/css" />
  <link rel="stylesheet" href="http://www.nltechno.com/modules/mod_jflanguageselection/tmpl/mod_jflanguageselection.css" type="text/css" />
<meta http-equiv="Content-Type" content="text/html" charset=utf-8 />
<meta name="Updowner-verification" content="1328821adac0666e174e84809a4cc72e" />
<meta name="verify-v1" content="5uTEtcSaRHlZVnb3L4x4QrpRzdw3zMZ51+mJxf/4Cd8=" />
<meta name="verify-v1" content="ygCOli7T1nnmmIz2ikasGV2Y+1DLmLcsblrDp+tSo/Q=" />
<meta name="msvalidate.01" content="DE3DAF65CA33EDC146378EBA3E0CAA72" />
<link rel="stylesheet" href="http://www.nltechno.com/templates/nltechno/css/template_css.css" type="text/css"/><!-- Google analytics -->
</head>
<body class="body">

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-46658874-1', 'nltechno.com');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>

<style type="text/css">
<!--
#rt-main-surround, #rt-variation .bg3 .module-content, #rt-variation .title3 .module-title {background:#ffffff;}
#rt-variation .bg3, #rt-variation .bg3 .title, #rt-variation .title3 .title, #rt-variation .bg3 ul.menu li > a:hover, #rt-variation .bg3 ul.menu li.active > a {color:#474747;}
#rt-variation .bg3 a, #rt-variation .bg3 .title span, #rt-variation .bg3 .button, #rt-variation .title3 .title span {color:#cc9a73;}
#rt-main-header, .menutop ul, .menutop .drop-top, #rt-variation .bg1 .module-content, #rt-variation .title1 .module-title {background:#cc9a73;}
#rt-main-header, #rt-main-header .title, #rt-header, #rt-main-header .menutop li > .item, .menutop ul li .item, #rt-variation .bg1, #rt-variation .bg1 .title, #rt-variation .title1 .title, #rt-variation .bg1 ul.menu li > a:hover, #rt-variation .bg1 ul.menu li.active > a, #rt-navigation li.root .item {color:#dedede;}
#rt-main-header .title span, #rt-variation .bg1 a, #rt-variation .bg1 .title span, #rt-variation .bg1 .button, #rt-variation .title1 .title span {color:#cce6ff;}
#rt-feature, #rt-utility, #roksearch_results, #roksearch_results .rokajaxsearch-overlay, #rt-variation .bg2 .module-content, #rt-variation .title2 .module-title {background:#ffffff;}
#rt-feature, #rt-feature .title, #rt-utility, #rt-utility .title, #roksearch_results, #roksearch_results span, #rt-variation .bg2, #rt-variation .bg2 .title, #rt-variation .title2 .title, #rt-variation .bg2 ul.menu li > a:hover, #rt-variation .bg2 ul.menu li.active > a {color:#474747;}
#rt-feature a, #rt-utility a, #rt-feature .title span, #rt-utility .title span, #roksearch_results a, #roksearch_results h3, #rt-variation .bg2 a, #rt-variation .bg2 .title span, #rt-variation .bg2 .button, #rt-variation .title2 .title span {color:#cc9a73;}
#rt-mainbody-bg, #rt-variation .bg4 .module-content, #rt-variation .title4 .module-title {background:#ffffff;}
#rt-mainbody-bg, #rt-mainbody-bg .title, #rt-mainbody-bg .rt-article-title, #rt-mainbody-bg ul.menu li > a:hover, #rt-mainbody-bg ul.menu li.active > a, #rt-variation .bg4, #rt-variation .bg4 .title, #rt-variation .title4 .title, #rt-variation .bg4 ul.menu li > a:hover, #rt-variation .bg4 ul.menu li.active > a {color:#474747;}
#rt-mainbody-bg a, #rt-mainbody-bg .title span, #rt-mainbody-bg .rt-article-title span, #rt-variation .bg4 a, #rt-variation .bg4 .title span, #rt-variation .bg4 .button, #rt-variation .title4 .title span {color:#cc9a73;}
#rt-bottom, #rt-main-footer, #rt-variation .bg5 .module-content, #rt-variation .title5 .module-title {background:#cc9a73;}
#rt-bottom, #rt-bottom .title, #rt-footer, #rt-footer .title, #rt-copyright, #rt-copyright .title, #rt-debug, #rt-debug .title, #rt-variation .bg5, #rt-variation .bg5 .title, #rt-variation .title5 .title, #rt-variation .bg5 ul.menu li > a:hover, #rt-variation .bg5 ul.menu li.active > a {color:#474747;}
#rt-bottom a, #rt-bottom .title span, #rt-footer a, #rt-footer .title span, #rt-copyright a, #rt-copyright .title span, #rt-debug a, #rt-debug .title span, #rt-variation .bg5 a, #rt-variation .bg5 .title span, #rt-variation .bg5 .button, #rt-variation .title5 .title span {color:#cc9a73;}
-->
</style>
<div id="rt-main-header" class="header-shadows-light"><div id="rt-header-overlay" class="header-overlay-none">
<div id="rt-main-header2">
<div id="rt-header-graphic" class="header-graphic-header-6">


<!-- Top banner Logo -->
<div id="rt-header">
<div class="rt-container">
<div class=""><div class="shadow-right"><div class="shadow-bottom">
					
<div class="rt-grid-12 rt-alpha rt-omega">
<div class="rt-block" style="height: 130px;">

<div style="float: left">
<br><br>
<div>
<div id="nltechno_title_logo" style="float: left">
<img src="/images/stories/logo_nltechno_2.gif" height="80px" alt="NLTechno logo">
</div>
<div id="nltechno_title_text" style="float: right">
<h1>NLTechno, Open-Source company</h1>
</div>
</div>
</div>
<div style="float: right" id="dolbanner">
</div>

</div>
</div>

<div class="clear"></div>
</div></div></div>
</div>
</div>
<!-- End top banner logo -->

<!-- navigation -->
<div id="rt-navigation" style="height: 54px;"><div id="rt-navigation2" style="height: 54px;"><div id="rt-navigation3" style="height: 54px;">
<div class="rt-container" style="height: 54px;">
<div class="shadow-left" style="height: 54px;"><div class="shadow-right" style="height: 54px;">

<div id="menu">
	<div class="menuc">
		<div id="topnavi">
			<ul>
			<!-- row->id=72 --><li><a href="/en/home" class="current" ><span>Home</span></a></li>
<!-- row->id=74 --><li><a href="/en/opensource-projects" ><span>OpenSource projects</span></a></li>
<!-- row->id=73 --><li><a href="/en/contacts" ><span>Contacts</span></a></li>
			</ul>
			
		</div>
		
<div class="plusone">
<!-- Plus one -->
<!--
<g:plusone size="small" href="http://www.dolicloud.com/"></g:plusone>
<script type="text/javascript">
(function() {
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
po.src = 'http://apis.google.com/js/plusone.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
</script>
-->
</div>

		<div align="right">
			<table cellpadding="0" cellspacing="0" class="moduletablejflanguage">
			<tr>
			<td>
				<div id="jflanguageselection"><div class="rawimages"><span><a href="http://www.nltechno.com/en"><img src="/media/com_joomfish/default/flags/en.gif" alt="English" title="English" /></a></span><span id="active_language"><a href="http://www.nltechno.com/fr"><img src="/media/com_joomfish/default/flags/fr.gif" alt="Français" title="Français" /></a></span></div></div><!--JoomFish V2.2.3 (Feng Huang)-->
<!-- &copy; 2003-2012 Think Network, released under the GPL. -->
<!-- More information: at http://www.joomfish.net -->
			</td>
			</tr>
			</table>
	<!--		<table cellpadding="0" cellspacing="0" class="moduletabletable.moduletable">
			<tr>
			<td>
			<div id="jflanguageselection"><div class="rawimages">
			<span><a href="http://www.dolibarr.es/" target="_blank"><img src="/images/flags/flags_es.png" alt="Dolibarr spanish portal" title="Dolibarr spanish portal" /></a></span>
			<span><a href="http://www.dolibarr.fr/" target="_blank"><img src="/images/flags/flags_fr.png" alt="Dolibarr french portal" title="Dolibarr french portal" /></a></span>
			</div></div>
			</td>
			</tr>
		</table>
-->
		</div>
		
<!--		<div id="submenu">
			<ul>
						</ul>
		</div>
-->
	</div>
</div>
	

<div class="clear"></div>
</div></div>
</div>
</div></div></div>
<!-- End navigation -->



<div id="main-surround" class="main-shadows-light">
<div id="container"><div class="shadow-left"><div class="shadow-right">




<table cellpadding="0" cellspacing="0">

	<tr>
<!--	<td class="leftcol" valign="top">
			</td>
-->
	<td class="maincol" valign="top">


<br><br>

<center>
Welcome on the 
<b>Download and Update service</b>
for all modules and softwares provided by NLTechno
</center>

<br><br>

<div id="dolistore" style="border: 1px solid #888; padding: 10px; margin: 10px; box-shadow: 4px 4px 6px #ccc;">
<div style="float: left"><br>
<img src="/images/stories/dolistore_100x100.png" width="100px">
</div>
<div style="float: left"><br><br>
<b><h2>&nbsp; Buy a new module</h2></b>
</div>
<div style="clear: both">
<br>
To find and buy a module provided by NLTechno, go on: 
<a href="http://www.dolistore.com/search.php?orderby=position&orderway=desc&search_query=nltechno">NLTechno modules in DoliStore</a> 
</div>
</div>

<br>

<div id="getdownloadlink" style="border: 1px solid #888; padding: 10px; margin: 10px; box-shadow: 4px 4px 6px #ccc;">
<div style="float: left">
<img src="/images/stories/download_package.png" width="100px">
</div>
<div style="float: left"><br><br>
<b><h2>&nbsp; Download an update of a module</h2></b><br>
</div>
<div style="clear: both">
NLTechno provide some free and paid modules. Any time you buy a NLTechno paid module, you benefit freely of an upgrade for 2 major versions.
You can get here a link to download updated modules you can get when you paid for a module:
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>#getdownloadlink">
<input type="hidden" name="action" value="getdownloadlink">
Enter here your <b>'EMail'</b> where to send download link of updated module:
<input type="email" name="email" value="<?php echo $_POST['email']; ?>"><br>
Enter here the <b>'Update Key'</b> found into the tab "About" page in the setup of module:
<input type="text" name="updatekey" value="<?php echo $_POST['updatekey']; ?>">
<br><br>
<input type="submit" name="submit" value="Send me update link">
</form>
</div>
</div>


<br>

<div id="getsources" style="border: 1px solid #888; padding: 10px; margin: 10px; box-shadow: 4px 4px 6px #ccc;">
<div style="float: left">
<img src="/images/stories/dolidroid_114x114.png" width="100px">
</div>
<div style="float: left"><br><br>
<b><h2>&nbsp; Download sources of an Android application</h2></b><br>
</div>
<div style="clear: both">
NLTechno provide some Android application licenced under GPL.
If you got such a binary, you can download here sources:
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>#getsources">
<input type="hidden" name="action" value="getsources">
Enter here the <b>'Download key'</b> found into the menu "About" of application:
<input type="text" name="downloadkey" value="<?php echo $_POST['downloadkey']; ?>">
<br><br>
<input type="submit" name="submit" value="Download sources">
</form>
</div>
<?php echo '<div style="margin-top: 5px; padding: 4px; background-color: #ddccaa; text-align: center;">'.$mesg.'</div>'; ?>
</div>


	
	<span class="article_separator">&nbsp;</span>

	</td>

	<td class="rightcol" valign="top">
			<table cellpadding="0" cellspacing="0" class="moduletablesocial">
			<tr>
			<th valign="top">NLTechno social network</th>
		</tr>
			<tr>
			<td>
				
<div class="modulesocial">

<ul id="socialmedialinks" class="32">
<li>
<a href="https://plus.google.com/+NLTechno" target="_blank" rel="nofollow"><img src="http://www.nltechno.com/modules/mod_socialmedialinks/icons/32/google.png" width="32px" height="32px" alt="Follow us on Google"></a>
</li>
<li>
<a href="https://twitter.com/nltechno_sarl" target="_blank"  rel="nofollow" ><img src="http://www.nltechno.com/modules/mod_socialmedialinks/icons/32/twitter.png" width="32px" height="32px" alt="Follow us on Twitter" /></a>
</li>
</ul>

<div class="clr"></div>
</div>
			</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="moduletablelargebutton">
			<tr>
			<td>
				<br /> 
<table class="buttonlarge" style="margin-left: auto; margin-right: auto;">
<tbody>
<tr>
<td><a target="_blank" href="https://www.on.dolicloud.com/signUp/index/1?origin=nltechnoright">
<p style="text-align: center;"> </p>
<p style="text-align: center;"><span style="color: #800080;">15 days trial</span></p>
<br />
<p style="text-align: center; line-height: 18px;"><span style="font-size: 12pt;">Start your<br />Dolibarr ERP &amp; CRM<br /></span></p>
<p style="text-align: center;"><span style="font-size: 8pt;"><br /></span></p>
</a></td>
</tr>
</tbody>
</table>
<p style="text-align: center;"><span style="color: #999999;">(for new users)</span></p>
<p style="text-align: center;"><span style="color: #999999;"><br /></span></p>			</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="moduletablenone">
			<tr>
			<td>
				<p>&nbsp;</p>
<p>&nbsp;</p>
<p><a href="http://wiki.dolibarr.org/index.php/Dolibarr_Preferred_Partner" target="_blank"><img style="display: block; margin-left: auto; margin-right: auto;" alt="DoliCloud is a service provided by a preferred partner" height="55" width="140" src="/images/stories/dolibarr_preferred_partner_int_140x55.png" title="DoliCloud is a service provided by NLTechno, a Dolibarr preferred partner" /></a></p>			</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="moduletablenone">
			<tr>
			<td>
				<p>&nbsp;</p>
<p>&nbsp;</p>
<p><a target="_blank" href="http://www.dolibarr.org"><img title="Dolibarr project portal" src="/images/stories/dolibarr_logo_120x35.png" width="120" height="35" alt="DoliCloud project portal" style="display: block; margin-left: auto; margin-right: auto;" /></a></p>			</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" class="moduletablenone">
			<tr>
			<td>
				<p>&nbsp;</p>
<p>&nbsp;</p>
<p><a href="http://www.dolicloud.com" target="_blank"><img style="display: block; margin-left: auto; margin-right: auto;" alt="DoliCloud ERP &amp; CRM hosting solution" height="37" width="120" src="/images/stories/dolicloud_logo_120x37.png" title="DoliCloud ERP &amp; CRM hosting solution" /></a></p>			</td>
		</tr>
		</table>
		</td>

	</tr>
</table>

<div class="footer_bg">
<div class="footer">
<br/><br/>
(c)2007-2014 NLTechno, all rights reserved. 
NLTechno, 32 rue Jules Ferry, 92100 Boulogne Billancourt, France - SIRET: 49386149600039
</div>
</div>

</div>
</div>
</div>
</div>

</center>

</body>
</html>

