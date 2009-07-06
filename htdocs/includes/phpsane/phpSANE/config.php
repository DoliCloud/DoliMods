<?PHP
// CONFIG --------------------------------------------------------------

// system config
// =============

$SCANIMAGE  = "/usr/bin/scanimage";   //  auch mit
$PNMTOJPEG  = "/usr/bin/pnmtojpeg";   //  eigenen
$PNMTOTIFF  = "/usr/bin/pnmtotiff";   //  eigenen
$OCR        = "/usr/bin/gocr";        //  Parametern


// user config
// ===========

// default language
// 0 = german
// 1 = english

$lang_id=1;


// where to save all working files (scans...)

//$SAVE_PLACE="/srv/www/htdocs/web/phpSANE/";
$SAVE_PLACE="./";


// set your scanner maxiumum page size, and a low dpi for previews

$PREVIEW_WIDTH_MM   = 216;
$PREVIEW_HEIGHT_MM  = 297;
$PREVIEW_DPI        = 100;


// set the preview image on-screen size

$PREVIEW_WIDTH_PX   = $PREVIEW_WIDTH_MM * 2;
$PREVIEW_HEIGHT_PX  = $PREVIEW_HEIGHT_MM * 2;
$PREVIEW_BORDER_PX  = 4;


// set the list of page sizes to select from

$PAGE_SIZE_LIST = array();

// ref: page sizes in mm (http://en.wikipedia.org/wiki/Paper_size)

// NB. only pages within your scanner size will be included

add_page_size('A0', 841, 1189);
add_page_size('A1', 594, 841);
add_page_size('A2', 420, 594);
add_page_size('A3', 297, 420);
add_page_size('A4', 210, 297);
add_page_size('A5', 148, 210);
add_page_size('A6', 105, 148);
//add_page_size('A7', 74, 105);
//add_page_size('A8', 52, 74);
//add_page_size('A9', 37, 52);
//add_page_size('A10', 26, 37);
add_page_size('US Letter', 216, 279);
add_page_size('US Legal', 216, 356);
add_page_size('US Ledger', 432, 279);
add_page_size('US Tabloid', 279, 432);


// enable features

$do_test_mode   = 0;

$do_negative    = 0;
$do_quality_cal = 0;
$do_brightness  = 0;
$do_usr_opt     = 1;

$do_ocr = 0;
if (`ls $OCR`) $do_ocr = 1;


// END CONFIG ----------------------------------------------------------

// first visit and clean/clear options

$first=1;
$clear=0; // jdw: does not do anything ?
$clean=0;

if (isset($_GET['first'])) $first=$_GET['first'];
if ($first) { $clean=1; $clear=1; }
$first=0;

if(isset($_GET['lang_id'])) { $lang_id=$_GET['lang_id']; }

$action="";
if(isset($_GET['action'])) { $action=$_GET['action']; }
if((ereg_replace("&#228;", "9", $lang[$lang_id][28])) == (ereg_replace("\xE4", "9", $action))) { $clean=1; $clear=1; }
if((ereg_replace("&#252;", "9", $lang[$lang_id][25])) == (ereg_replace("\xFC", "9", $action))) $clear=1;


// default options

$sid=time();

$preview_images="./bilder/scan.jpg";

$geometry_l=0;
$geometry_t=0;
$geometry_x=0;
$geometry_y=0;

$format="jpg";
$mode="Color";
$resolution=100;

$negative="no";
$quality_cal= "no";
$brightness="0";

$usr_opt="";


// user options

if (!$clean)
{
  if (isset($_GET['sid'])) $sid=$_GET['sid'];

  if (isset($_GET['preview_images'])) $preview_images=$_GET['preview_images'];

  if (isset($_GET['geometry_l'])) $geometry_l=$_GET['geometry_l'];
  if (isset($_GET['geometry_t'])) $geometry_t=$_GET['geometry_t'];
  if (isset($_GET['geometry_x'])) $geometry_x=$_GET['geometry_x'];
  if (isset($_GET['geometry_y'])) $geometry_y=$_GET['geometry_y'];

  if (isset($_GET['format'])) $format=$_GET['format'];
  if (isset($_GET['mode'])) $mode=$_GET['mode'];
  if (isset($_GET['resolution'])) $resolution=$_GET['resolution'];

  if (isset($_GET['negative'])) $negative="yes";
  if (isset($_GET['quality_cal'])) $quality_cal="yes";
  if (isset($_GET['brightness'])) $brightness=$_GET['brightness'];

  if (isset($_GET['usr_opt'])) $usr_opt=$_GET['usr_opt'];
}

//if (isset($_GET['scanner'])) $scanner=$_GET['scanner'];
//if (isset($_GET['scan_name'])) $scan_name=$_GET['scan_name'];
//if($_GET['depth']) $depth=$_GET['depth']; else $depth="8";   // wers braucht


// verify usr_opt - keep only valid chars, otherwise replace with an 'X'

$my_usr_opt = '';

for ($i = 0; $i < strlen($usr_opt); $i++)
{
  if (preg_match('([0-9]|[a-z]|[A-Z]|[\ \%\+\-_=])', $usr_opt[$i]))
  {
    $my_usr_opt .= $usr_opt[$i];
  }
  else
  {
    $my_usr_opt .= 'X';
  }
}

$usr_opt = $my_usr_opt;


// INTERNAL CONFIG -----------------------------------------------------

// file names setup

$TMP_PRAEFIX=$SAVE_PLACE."tmp/";   //  kein slach als abschluss und muss schreibrechte haben

$file_base=$TMP_PRAEFIX.$sid;

$cleaner="rm -f ".$TMP_PRAEFIX."*";


// scale factor to map preview image -> scanner co-ords

$facktor = round($PREVIEW_WIDTH_MM / $PREVIEW_WIDTH_PX, 4);


// scanner device detect

if ($do_test_mode)
{
  $sane_scanner="device `umax:/dev/sg0' is a UMAX     Astra 1220S      flatbed scanner";
}
else
{
  $cmd=$SCANIMAGE." --list-devices | grep device";
  $sane_scanner = `$cmd`;
  unset($cmd);
}

$start=strpos($sane_scanner,"`")+1;
$laenge=strpos($sane_scanner,"'")-$start;
$scanner = "\"".substr($sane_scanner,$start,$laenge)."\"";
unset($start);
unset($laenge);

$start=strpos($sane_scanner,"is a")+4;   // mit anderren scannern testen?
$laenge=strpos($sane_scanner,"scanner")-$start;
$scan_name = substr($sane_scanner,$start,$laenge);
unset($start);
unset($laenge);

// ----

$scan_ausgabe=$scan_name."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Device = ".$scanner;

?>
