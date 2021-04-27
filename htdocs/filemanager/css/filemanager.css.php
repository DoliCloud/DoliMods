<?php
//if (! defined('NOREQUIREUSER')) define('NOREQUIREUSER','1');  // Not disabled cause need to load personalized language
//if (! defined('NOREQUIREDB'))   define('NOREQUIREDB','1');    // Not disabled to increase speed. Language code is found on url.
if (! defined('NOREQUIRESOC'))    define('NOREQUIRESOC', '1');
//if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');  // Not disabled cause need to do translations
if (! defined('NOCSRFCHECK'))     define('NOCSRFCHECK', 1);
if (! defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL', 1);
if (! defined('NOLOGIN'))         define('NOLOGIN', 1);
if (! defined('NOREQUIREMENU'))   define('NOREQUIREMENU', 1);
if (! defined('NOREQUIREHTML'))   define('NOREQUIREHTML', 1);
if (! defined('NOREQUIREAJAX'))   define('NOREQUIREAJAX', '1');

session_cache_limiter('public');

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

// Define css type
header('Content-type: text/css');
// Important: Following code is to avoid page request by browser and PHP CPU at
// each Dolibarr page access.
if (empty($dolibarr_nocache)) header('Cache-Control: max-age=10800, public, must-revalidate');
else header('Cache-Control: no-cache');

if (! empty($_GET["lang"])) $langs->setDefaultLang($_GET["lang"]);  // If language was forced on URL by the main.inc.php
if (! empty($_GET["theme"])) $conf->theme=$_GET["theme"];  // If theme was forced on URL
$langs->load("main", 0, 1);
$right=($langs->trans("DIRECTION")=='rtl'?'left':'right');
$left=($langs->trans("DIRECTION")=='rtl'?'right':'left');
$fontsize='12';
$fontsizesmaller='11';

$fontlist='arial,tahoma,verdana,helvetica';
//$fontlist='Verdana,Helvetica,Arial,sans-serif';

?>


/* For themes that use menu images */
div.mainmenu.filemanager {
	background-image: url(<?php echo DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/menus/globe.png' ?>);
}



.filetree {
	width: 99%;
	height: 99%;
	background: #FFF;
	padding-left: 2px;
	font-weight: normal;
	overflow: auto;
}

.fileview {
	width: 99%;
	height: 99%;
	background: #FFF;
	padding-left: 2px;
	padding-top: 4px;
	font-weight: normal;
	overflow: auto;
}

div.filedirelem {
	position: relative;
	display: block;
	text-decoration: none;
}

ul.filedirelem {
	padding: 2px;
	margin: 0 5px 5px 5px;
}
ul.filedirelem li {
	list-style: none;
	padding: 2px;
	margin: 0 10px 20px 10px;
	width: 160px;
	height: 120px;
	text-align: center;
	display: block;
	float: <?php print $left; ?>;
	border: solid 1px #DDDDDD;
}

ui-layout-north {

}

ul.jqueryFileTree {
	font-family: Verdana, sans-serif;
	font-size: 11px;
	line-height: 18px;
	padding: 0px;
	margin: 0px;
				font-weight: normal;
}

ul.jqueryFileTree li {
	list-style: none;
	padding: 0px;
	padding-left: 20px;
	margin: 0px;
	white-space: nowrap;
}

/* Seems to have all links "visited"
ul.jqueryFileTree a:active {
	background: #BDF !important;
}
ul.jqueryFileTree a:visited {
	background: #BDF !important;
}*/

ul.jqueryFileTree a {
	color: #333;
	text-decoration: none;
	display: block;
	padding: 0px 0px;
	font-weight:normal;
}

ul.jqueryFileTree A:hover {
	background: #BDF;
}

/* Core Styles */
.jqueryFileTree LI.directory { font-weight:normal; background: url(../includes/jqueryFileTree/images/directory.png) left top no-repeat; }
.jqueryFileTree LI.expanded { font-weight:normal; background: url(../includes/jqueryFileTree/images/folder_open.png) left top no-repeat; }
.jqueryFileTree LI.file { font-weight:normal; background: url(../includes/jqueryFileTree/images/file.png) left top no-repeat; }
.jqueryFileTree LI.wait { font-weight:normal; background: url(../includes/jqueryFileTree/images/spinner.gif) left top no-repeat; }
/* File Extensions*/
.jqueryFileTree LI.ext_3gp { background: url(../includes/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_afp { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_afpa { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_asp { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_aspx { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_avi { background: url(../includes/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_bat { background: url(../includes/jqueryFileTree/images/application.png) left top no-repeat; }
.jqueryFileTree LI.ext_bmp { background: url(../includes/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_c { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_cfm { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_cgi { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_com { background: url(../includes/jqueryFileTree/images/application.png) left top no-repeat; }
.jqueryFileTree LI.ext_cpp { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_css { background: url(../includes/jqueryFileTree/images/css.png) left top no-repeat; }
.jqueryFileTree LI.ext_doc { background: url(../includes/jqueryFileTree/images/doc.png) left top no-repeat; }
.jqueryFileTree LI.ext_exe { background: url(../includes/jqueryFileTree/images/application.png) left top no-repeat; }
.jqueryFileTree LI.ext_gif { background: url(../includes/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_fla { background: url(../includes/jqueryFileTree/images/flash.png) left top no-repeat; }
.jqueryFileTree LI.ext_h { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_htm { background: url(../includes/jqueryFileTree/images/html.png) left top no-repeat; }
.jqueryFileTree LI.ext_html { background: url(../includes/jqueryFileTree/images/html.png) left top no-repeat; }
.jqueryFileTree LI.ext_jar { background: url(../includes/jqueryFileTree/images/java.png) left top no-repeat; }
.jqueryFileTree LI.ext_jpg { background: url(../includes/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_jpeg { background: url(../includes/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_js { background: url(../includes/jqueryFileTree/images/script.png) left top no-repeat; }
.jqueryFileTree LI.ext_lasso { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_log { background: url(../includes/jqueryFileTree/images/txt.png) left top no-repeat; }
.jqueryFileTree LI.ext_m4p { background: url(../includes/jqueryFileTree/images/music.png) left top no-repeat; }
.jqueryFileTree LI.ext_mov { background: url(../includes/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_mp3 { background: url(../includes/jqueryFileTree/images/music.png) left top no-repeat; }
.jqueryFileTree LI.ext_mp4 { background: url(../includes/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_mpg { background: url(../includes/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_mpeg { background: url(../includes/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_ogg { background: url(../includes/jqueryFileTree/images/music.png) left top no-repeat; }
.jqueryFileTree LI.ext_ogv { background: url(../includes/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_pcx { background: url(../includes/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_pdf { background: url(../includes/jqueryFileTree/images/pdf.png) left top no-repeat; }
.jqueryFileTree LI.ext_php { background: url(../includes/jqueryFileTree/images/php.png) left top no-repeat; }
.jqueryFileTree LI.ext_png { background: url(../includes/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_ppt { background: url(../includes/jqueryFileTree/images/ppt.png) left top no-repeat; }
.jqueryFileTree LI.ext_psd { background: url(../includes/jqueryFileTree/images/psd.png) left top no-repeat; }
.jqueryFileTree LI.ext_pl { background: url(../includes/jqueryFileTree/images/script.png) left top no-repeat; }
.jqueryFileTree LI.ext_py { background: url(../includes/jqueryFileTree/images/script.png) left top no-repeat; }
.jqueryFileTree LI.ext_rb { background: url(../includes/jqueryFileTree/images/ruby.png) left top no-repeat; }
.jqueryFileTree LI.ext_rbx { background: url(../includes/jqueryFileTree/images/ruby.png) left top no-repeat; }
.jqueryFileTree LI.ext_rhtml { background: url(../includes/jqueryFileTree/images/ruby.png) left top no-repeat; }
.jqueryFileTree LI.ext_rpm { background: url(../includes/jqueryFileTree/images/linux.png) left top no-repeat; }
.jqueryFileTree LI.ext_ruby { background: url(../includes/jqueryFileTree/images/ruby.png) left top no-repeat; }
.jqueryFileTree LI.ext_sql { background: url(../includes/jqueryFileTree/images/db.png) left top no-repeat; }
.jqueryFileTree LI.ext_swf { background: url(../includes/jqueryFileTree/images/flash.png) left top no-repeat; }
.jqueryFileTree LI.ext_tif { background: url(../includes/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_tiff { background: url(../includes/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_txt { background: url(../includes/jqueryFileTree/images/txt.png) left top no-repeat; }
.jqueryFileTree LI.ext_vb { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_wav { background: url(../includes/jqueryFileTree/images/music.png) left top no-repeat; }
.jqueryFileTree LI.ext_webm { background: url(../includes/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_wmv { background: url(../includes/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_xls { background: url(../includes/jqueryFileTree/images/xls.png) left top no-repeat; }
.jqueryFileTree LI.ext_xml { background: url(../includes/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_zip { background: url(../includes/jqueryFileTree/images/zip.png) left top no-repeat; }

/* Right panel */

.fmvalue {
	color: #001166;
}

#fmeditor {
	width: 95%;
}





/* ============================================================================== */
/* Panes for ECM or Filemanager                                                   */
/* ============================================================================== */

#containerlayout .layout-with-no-border {
	border: 0 !important;
	border-width: 0 !important;
}

#containerlayout .layout-padding {
	padding: 2px !important;
}

/*
 *  PANES and CONTENT-DIVs
 */
#containerlayout .ui-layout-pane { /* all 'panes' */
	background: #FFF;
	border:     1px solid #BBB;
	/* DO NOT add scrolling (or padding) to 'panes' that have a content-div,
	   otherwise you may get double-scrollbars - on the pane AND on the content-div
	*/
	padding:    0px;
	overflow:   auto;
}
/* (scrolling) content-div inside pane allows for fixed header(s) and/or footer(s) */
#containerlayout .ui-layout-content {
	padding:    10px;
	position:   relative; /* contain floated or positioned elements */
	overflow:   auto; /* add scrolling to content-div */
}


/*
 *  RESIZER-BARS
 */
.ui-layout-resizer  { /* all 'resizer-bars' */
	width: <?php echo (empty($conf->dol_optimize_smallscreen)?'8':'24'); ?>px !important;
}
.ui-layout-resizer-hover    {   /* affects both open and closed states */
}
/* NOTE: It looks best when 'hover' and 'dragging' are set to the same color,
	otherwise color shifts while dragging when bar can't keep up with mouse */
/*.ui-layout-resizer-open-hover ,*/ /* hover-color to 'resize' */
.ui-layout-resizer-dragging {   /* resizer beging 'dragging' */
	background: #DDD;
	width: <?php echo (empty($conf->dol_optimize_smallscreen)?'8':'24'); ?>px;
}
.ui-layout-resizer-dragging {   /* CLONED resizer being dragged */
	border-left:  1px solid #BBB;
	border-right: 1px solid #BBB;
}
/* NOTE: Add a 'dragging-limit' color to provide visual feedback when resizer hits min/max size limits */
.ui-layout-resizer-dragging-limit { /* CLONED resizer at min or max size-limit */
	background: #E1A4A4; /* red */
}
.ui-layout-resizer-closed {
	background-color: #DDDDDD;
}
.ui-layout-resizer-closed:hover {
	background-color: #EEDDDD;
}
.ui-layout-resizer-sliding {    /* resizer when pane is 'slid open' */
	opacity: .10; /* show only a slight shadow */
	filter:  alpha(opacity=10);
}
.ui-layout-resizer-sliding-hover {  /* sliding resizer - hover */
	opacity: 1.00; /* on-hover, show the resizer-bar normally */
	filter:  alpha(opacity=100);
}
/* sliding resizer - add 'outside-border' to resizer on-hover */
/* this sample illustrates how to target specific panes and states */
/*.ui-layout-resizer-north-sliding-hover  { border-bottom-width:  1px; }
.ui-layout-resizer-south-sliding-hover  { border-top-width:     1px; }
.ui-layout-resizer-west-sliding-hover   { border-right-width:   1px; }
.ui-layout-resizer-east-sliding-hover   { border-left-width:    1px; }
*/

/*
 *  TOGGLER-BUTTONS
 */
.ui-layout-toggler {
	<?php if (empty($conf->dol_optimize_smallscreen)) { ?>
	border-top: 1px solid #AAA; /* match pane-border */
	border-right: 1px solid #AAA; /* match pane-border */
	border-bottom: 1px solid #AAA; /* match pane-border */
	background-color: #DDD;
	top: 5px !important;
	<?php } else { ?>
	diplay: none;
	<?php } ?>
}
.ui-layout-toggler-open {
	height: 54px !important;
	width: <?php echo (empty($conf->dol_optimize_smallscreen)?'7':'22'); ?>px !important;
	-moz-border-radius:0px 10px 10px 0px;
	-webkit-border-radius:0px 10px 10px 0px;
	border-radius:0px 10px 10px 0px;
}
.ui-layout-toggler-closed {
	height: <?php echo (empty($conf->dol_optimize_smallscreen)?'54':'2'); ?>px !important;
	width: <?php echo (empty($conf->dol_optimize_smallscreen)?'7':'22'); ?>px !important;
	-moz-border-radius:0px 10px 10px 0px;
	-webkit-border-radius:0px 10px 10px 0px;
	border-radius:0px 10px 10px 0px;
}
.ui-layout-toggler .content {	/* style the text we put INSIDE the togglers */
	color:          #666;
	font-size:      12px;
	font-weight:    bold;
	width:          100%;
	padding-bottom: 0.35ex; /* to 'vertically center' text inside text-span */
}

/* hide the toggler-button when the pane is 'slid open' */
.ui-layout-resizer-sliding .ui-layout-toggler {
	display: none;
}

.ui-layout-north {
	height: <?php print (empty($conf->dol_optimize_smallscreen)?'54':'21'); ?>px !important;
}


/* ECM */

#containerlayout .ecm-layout-pane { /* all 'panes' */
	background: #FFF;
	border:     1px solid #BBB;
	/* DO NOT add scrolling (or padding) to 'panes' that have a content-div,
	   otherwise you may get double-scrollbars - on the pane AND on the content-div
	*/
	padding:    0px;
	overflow:   auto;
}
/* (scrolling) content-div inside pane allows for fixed header(s) and/or footer(s) */
#containerlayout .ecm-layout-content {
	padding:    10px;
	position:   relative; /* contain floated or positioned elements */
	overflow:   auto; /* add scrolling to content-div */
}

.ecm-layout-toggler {
	border-top: 1px solid #AAA; /* match pane-border */
	border-right: 1px solid #AAA; /* match pane-border */
	border-bottom: 1px solid #AAA; /* match pane-border */
	background-color: #CCC;
	}
.ecm-layout-toggler-open {
	height: 48px !important;
	width: 6px !important;
	-moz-border-radius:0px 10px 10px 0px;
	-webkit-border-radius:0px 10px 10px 0px;
	border-radius:0px 10px 10px 0px;
}
.ecm-layout-toggler-closed {
	height: 48px !important;
	width: 6px !important;
}

.ecm-layout-toggler .content {	/* style the text we put INSIDE the togglers */
	color:          #666;
	font-size:      12px;
	font-weight:    bold;
	width:          100%;
	padding-bottom: 0.35ex; /* to 'vertically center' text inside text-span */
}
#ecm-layout-west-resizer {
	width: 6px !important;
}

.ecm-layout-resizer  { /* all 'resizer-bars' */
	border:         1px solid #BBB;
	border-width:   0;
	}
.ecm-layout-resizer-closed {
}

div#ecm-layout-north {
	padding-top: 0px !important;
	padding-bottom: 0px !important;
}

.ecm-in-layout-center {
	border-left: 1px !important;
	border-right: 0px !important;
	border-top: 0px !important;
}

.ecm-in-layout-south {
	border-top: 0px !important;
	border-left: 0px !important;
	border-right: 0px !important;
	border-bottom: 0px !important;
	padding: 4px 0 4px 4px !important;
}

<?php
$db->close();
