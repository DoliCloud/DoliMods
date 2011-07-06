<?php
//if (! defined('NOREQUIREUSER')) define('NOREQUIREUSER','1');  // Not disabled cause need to load personalized language
//if (! defined('NOREQUIREDB'))   define('NOREQUIREDB','1');    // Not disabled to increase speed. Language code is found on url.
if (! defined('NOREQUIRESOC'))    define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN')) define('NOREQUIRETRAN','1');  // Not disabled cause need to do translations
if (! defined('NOCSRFCHECK'))     define('NOCSRFCHECK',1);
if (! defined('NOTOKENRENEWAL'))  define('NOTOKENRENEWAL',1);
if (! defined('NOLOGIN'))         define('NOLOGIN',1);
if (! defined('NOREQUIREMENU'))   define('NOREQUIREMENU',1);
if (! defined('NOREQUIREHTML'))   define('NOREQUIREHTML',1);
if (! defined('NOREQUIREAJAX'))   define('NOREQUIREAJAX','1');

session_cache_limiter( FALSE );

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/lib/functions.lib.php");

// Define css type
header('Content-type: text/css');
// Important: Following code is to avoid page request by browser and PHP CPU at
// each Dolibarr page access.
if (empty($dolibarr_nocache)) header('Cache-Control: max-age=3600, public, must-revalidate');
else header('Cache-Control: no-cache');

// On the fly GZIP compression for all pages (if browser support it). Must set the bit 3 of constant to 1.
if (isset($conf->global->MAIN_OPTIMIZE_SPEED) && ($conf->global->MAIN_OPTIMIZE_SPEED & 0x04)) { ob_start("ob_gzhandler"); }

if (! empty($_GET["lang"])) $langs->setDefaultLang($_GET["lang"]);  // If language was forced on URL by the main.inc.php
if (! empty($_GET["theme"])) $conf->theme=$_GET["theme"];  // If theme was forced on URL
$langs->load("main",0,1);
$right=($langs->trans("DIRECTION")=='rtl'?'left':'right');
$left=($langs->trans("DIRECTION")=='rtl'?'right':'left');
$fontsize=empty($conf->browser->phone)?'12':'12';
$fontsizesmaller=empty($conf->browser->phone)?'11':'11';

$fontlist='arial,tahoma,verdana,helvetica';
//$fontlist='Verdana,Helvetica,Arial,sans-serif';

?>


/* For themes that use menu images */
div.mainmenu.filemanager {
	background-image: url(<?php echo DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/menus/globe.png' ?>);
}



.filetree {
/*				width: 350px; */
				width: 99%;
/*				height: 500px; */
				height: 99%;
/*				border-top: solid 1px #BBB;
				border-left: solid 1px #BBB;
				border-bottom: solid 1px #FFF;
				border-right: solid 1px #FFF;
*/
				background: #FFF;
/*				overflow: scroll; */
				padding-left: 2px;
				font-weight: normal;
			}

.fileview {
				width: 99%;
/*				height: 500px; */
				height: 99%;
/*				border-top: solid 1px #BBB;
				border-left: solid 1px #BBB;
				border-bottom: solid 1px #FFF;
				border-right: solid 1px #FFF; */
				background: #FFF;
/*				overflow: scroll; */
				padding-left: 2px;
				padding-top: 4px;
				font-weight: normal;
			}

div.filedirelem {
    position: relative;
    display: block;
    text-decoration: none;
}

ul.filedirelem {
/*    line-height: 120px; */
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


UL.jqueryFileTree {
	font-family: Verdana, sans-serif;
	font-size: 11px;
	line-height: 18px;
	padding: 0px;
	margin: 0px;
				font-weight: normal;
}

UL.jqueryFileTree LI {
	list-style: none;
	padding: 0px;
	padding-left: 20px;
	margin: 0px;
	white-space: nowrap;
}

/* Seems to have all links "visited"
UL.jqueryFileTree A:active {
    background: #BDF !important;
}
UL.jqueryFileTree A:visited {
    background: #BDF !important;
}*/


UL.jqueryFileTree A {
	color: #333;
	text-decoration: none;
	display: block;
	padding: 0px 0px;
	font-weight:normal;
}

UL.jqueryFileTree A:hover {
    background: #BDF;
}



/* Core Styles */
.jqueryFileTree LI.directory { font-weight:normal; background: url(../inc/jqueryFileTree/images/directory.png) left top no-repeat; }
.jqueryFileTree LI.expanded { font-weight:normal; background: url(../inc/jqueryFileTree/images/folder_open.png) left top no-repeat; }
.jqueryFileTree LI.file { font-weight:normal; background: url(../inc/jqueryFileTree/images/file.png) left top no-repeat; }
.jqueryFileTree LI.wait { font-weight:normal; background: url(../inc/jqueryFileTree/images/spinner.gif) left top no-repeat; }
/* File Extensions*/
.jqueryFileTree LI.ext_3gp { background: url(../inc/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_afp { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_afpa { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_asp { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_aspx { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_avi { background: url(../inc/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_bat { background: url(../inc/jqueryFileTree/images/application.png) left top no-repeat; }
.jqueryFileTree LI.ext_bmp { background: url(../inc/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_c { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_cfm { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_cgi { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_com { background: url(../inc/jqueryFileTree/images/application.png) left top no-repeat; }
.jqueryFileTree LI.ext_cpp { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_css { background: url(../inc/jqueryFileTree/images/css.png) left top no-repeat; }
.jqueryFileTree LI.ext_doc { background: url(../inc/jqueryFileTree/images/doc.png) left top no-repeat; }
.jqueryFileTree LI.ext_exe { background: url(../inc/jqueryFileTree/images/application.png) left top no-repeat; }
.jqueryFileTree LI.ext_gif { background: url(../inc/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_fla { background: url(../inc/jqueryFileTree/images/flash.png) left top no-repeat; }
.jqueryFileTree LI.ext_h { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_htm { background: url(../inc/jqueryFileTree/images/html.png) left top no-repeat; }
.jqueryFileTree LI.ext_html { background: url(../inc/jqueryFileTree/images/html.png) left top no-repeat; }
.jqueryFileTree LI.ext_jar { background: url(../inc/jqueryFileTree/images/java.png) left top no-repeat; }
.jqueryFileTree LI.ext_jpg { background: url(../inc/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_jpeg { background: url(../inc/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_js { background: url(../inc/jqueryFileTree/images/script.png) left top no-repeat; }
.jqueryFileTree LI.ext_lasso { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_log { background: url(../inc/jqueryFileTree/images/txt.png) left top no-repeat; }
.jqueryFileTree LI.ext_m4p { background: url(../inc/jqueryFileTree/images/music.png) left top no-repeat; }
.jqueryFileTree LI.ext_mov { background: url(../inc/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_mp3 { background: url(../inc/jqueryFileTree/images/music.png) left top no-repeat; }
.jqueryFileTree LI.ext_mp4 { background: url(../inc/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_mpg { background: url(../inc/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_mpeg { background: url(../inc/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_ogg { background: url(../inc/jqueryFileTree/images/music.png) left top no-repeat; }
.jqueryFileTree LI.ext_ogv { background: url(../inc/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_pcx { background: url(../inc/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_pdf { background: url(../inc/jqueryFileTree/images/pdf.png) left top no-repeat; }
.jqueryFileTree LI.ext_php { background: url(../inc/jqueryFileTree/images/php.png) left top no-repeat; }
.jqueryFileTree LI.ext_png { background: url(../inc/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_ppt { background: url(../inc/jqueryFileTree/images/ppt.png) left top no-repeat; }
.jqueryFileTree LI.ext_psd { background: url(../inc/jqueryFileTree/images/psd.png) left top no-repeat; }
.jqueryFileTree LI.ext_pl { background: url(../inc/jqueryFileTree/images/script.png) left top no-repeat; }
.jqueryFileTree LI.ext_py { background: url(../inc/jqueryFileTree/images/script.png) left top no-repeat; }
.jqueryFileTree LI.ext_rb { background: url(../inc/jqueryFileTree/images/ruby.png) left top no-repeat; }
.jqueryFileTree LI.ext_rbx { background: url(../inc/jqueryFileTree/images/ruby.png) left top no-repeat; }
.jqueryFileTree LI.ext_rhtml { background: url(../inc/jqueryFileTree/images/ruby.png) left top no-repeat; }
.jqueryFileTree LI.ext_rpm { background: url(../inc/jqueryFileTree/images/linux.png) left top no-repeat; }
.jqueryFileTree LI.ext_ruby { background: url(../inc/jqueryFileTree/images/ruby.png) left top no-repeat; }
.jqueryFileTree LI.ext_sql { background: url(../inc/jqueryFileTree/images/db.png) left top no-repeat; }
.jqueryFileTree LI.ext_swf { background: url(../inc/jqueryFileTree/images/flash.png) left top no-repeat; }
.jqueryFileTree LI.ext_tif { background: url(../inc/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_tiff { background: url(../inc/jqueryFileTree/images/picture.png) left top no-repeat; }
.jqueryFileTree LI.ext_txt { background: url(../inc/jqueryFileTree/images/txt.png) left top no-repeat; }
.jqueryFileTree LI.ext_vb { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_wav { background: url(../inc/jqueryFileTree/images/music.png) left top no-repeat; }
.jqueryFileTree LI.ext_webm { background: url(../inc/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_wmv { background: url(../inc/jqueryFileTree/images/film.png) left top no-repeat; }
.jqueryFileTree LI.ext_xls { background: url(../inc/jqueryFileTree/images/xls.png) left top no-repeat; }
.jqueryFileTree LI.ext_xml { background: url(../inc/jqueryFileTree/images/code.png) left top no-repeat; }
.jqueryFileTree LI.ext_zip { background: url(../inc/jqueryFileTree/images/zip.png) left top no-repeat; }


/* Right panel */

.fmvalue {
    color: #001166;
}
