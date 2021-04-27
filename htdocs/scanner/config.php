<?PHP
// CONFIG --------------------------------------------------------------

// system config
// =============

$SCANIMAGE  = "/usr/bin/scanimage";   //  auch mit
$PNMTOJPEG  = "/usr/bin/pnmtojpeg";   //  eigenen
$PNMTOTIFF  = "/usr/bin/pnmtotiff";   //  eigenen
$OCR        = "/usr/bin/gocr";        //  Parametern

// @CHANGE LDR
if (! empty($conf->global->PHPSANE_SCANIMAGE)) $SCANIMAGE=$conf->global->PHPSANE_SCANIMAGE;
if (! empty($conf->global->PHPSANE_PNMTOJPEG)) $PNMTOJPEG=$conf->global->PHPSANE_PNMTOJPEG;
if (! empty($conf->global->PHPSANE_PNMTOTIFF)) $PNMTOTIFF=$conf->global->PHPSANE_PNMTOTIFF;
if (! empty($conf->global->PHPSANE_OCR))       $OCR=$conf->global->PHPSANE_OCR;


// user config
// ===========
$lang_id=1;

$SAVE_PLACE="./";
$SAVE_PLACE=$conf->scanner->dir_temp;
//print "x".$SAVE_PLACE;


// Set default value of paramters
$PREVIEW_WIDTH_MM   = 210;
$PREVIEW_HEIGHT_MM  = 297;
$PREVIEW_DPI        = 100;

// set the preview image on-screen size
$PREVIEW_WIDTH_PX   = $PREVIEW_WIDTH_MM;
$PREVIEW_HEIGHT_PX  = $PREVIEW_HEIGHT_MM;


// set the list of page sizes to select from
// ref: page sizes in mm (http://en.wikipedia.org/wiki/Paper_size)
// NB. only pages within your scanner size will be included

$PAGE_SIZE_LIST = array();
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

$action="";
if (isset($_GET['action'])) { $action=$_GET['action']; }


// default options

$sid=time();

$preview_images="scan.jpg";

$geometry_l=0;
$geometry_t=0;
$geometry_x=0;
$geometry_y=0;

// By default
$format="jpg";
$mode="Color";
$resolution=100;

$negative="no";
$quality_cal= "no";
$brightness="0";

$usr_opt="";


// Set user options
if (GETPOST('sid')) $sid=GETPOST('sid');

if (GETPOST('preview_images')) $preview_images=GETPOST('preview_images');

if (GETPOST('geometry_l')) $geometry_l=GETPOST('geometry_l');
if (GETPOST('geometry_t')) $geometry_t=GETPOST('geometry_t');
if (GETPOST('geometry_x')) $geometry_x=GETPOST('geometry_x');
if (GETPOST('geometry_y')) $geometry_y=GETPOST('geometry_y');

if (GETPOST('format')) $format=GETPOST('format');
if (GETPOST('mode')) $mode=GETPOST('mode');
if (GETPOST('resolution')) $resolution=GETPOST('resolution');

if (GETPOST('negative')) $negative="yes";
if (GETPOST('quality_cal')) $quality_cal="yes";
if (GETPOST('brightness')) $brightness=GETPOST('brightness');

if (GETPOST('usr_opt')) $usr_opt=GETPOST('usr_opt');

if (empty($geometry_x)) $geometry_x=$PREVIEW_WIDTH_MM;
if (empty($geometry_y)) $geometry_y=$PREVIEW_HEIGHT_MM;


//if (GETPOST('scanner'])) $scanner=$_GET['scanner'];
//if (GETPOST('scan_name'])) $scan_name=$_GET['scan_name'];


// Check usr_opt - keep only valid chars, otherwise replace with an 'X'

$my_usr_opt = '';
for ($i = 0; $i < strlen($usr_opt); $i++) {
	if (preg_match('([0-9]|[a-z]|[A-Z]|[\ \%\+\-_=])', $usr_opt[$i])) {
		$my_usr_opt .= $usr_opt[$i];
	} else {
		$my_usr_opt .= 'X';
	}
}

$usr_opt = $my_usr_opt;


// Define output file name
$TMP_PREFIX=$SAVE_PLACE.'/'.$user->id.'/';
$file_base=$TMP_PREFIX.$sid;

// scale factor to map preview image -> scanner co-ords
$facktor = round($PREVIEW_WIDTH_MM / $PREVIEW_WIDTH_PX, 4);



// reset scanner informations
if (GETPOST('actionclean')) {
	unset($_SESSION['scannerlist']);
	//var_dump($_SESSION);
	foreach ($_SESSION as $key => $val) {
		if (preg_match('/^resolution_/', $key)) {
			unset($_SESSION[$key]);
		}
	}
	//dol_delete_file($file_save);
	//var_dump($_SESSION);exit;
}

// Scanner device detection
if ($do_test_mode) {
	$sane_scanner="device `umax:/dev/sg0' is a UMAX     Astra 1220S      flatbed scanner";
} else {
	// Retrieve list of possible resolutions into $list
	if (empty($_SESSION['scannerlist'])) {
		$out=array();
		$command=$SCANIMAGE.' --list-devices | grep device';    // Return lines: device `umax:/dev/sg0' is a UMAX     Astra 1220S      flatbed scanner
		//print "eeee".$command;
		dol_syslog("Detect list of scanner devices with command ".$command);
		$sane_scanner = exec($command, $out);
		//print $sane_scanner;
		$start=strpos($sane_scanner, "`")+1;
		$laenge=strpos($sane_scanner, "'")-$start;
		$scanner = "\"".substr($sane_scanner, $start, $laenge)."\"";
		unset($start);
		unset($laenge);
		dol_syslog("Found ".$sane_scanner);
		if ($sane_scanner) $_SESSION['scannerlist']=$sane_scanner;
	} else {
		$sane_scanner=$_SESSION['scannerlist'];
		dol_syslog("List of scanner already stored in cache with value ".$sane_scanner);
	}
}
// Define scanner and scan_name from sane_scanner
$start=strpos($sane_scanner, "`")+1;
$laenge=strpos($sane_scanner, "'")-$start;
$scanner = "\"".substr($sane_scanner, $start, $laenge)."\"";
unset($start);
unset($laenge);
$start=strpos($sane_scanner, "is a")+4;   // mit anderren scannern testen?
$laenge=strpos($sane_scanner, "scanner")-$start;
$scan_name = substr($sane_scanner, $start, $laenge);
unset($start);
unset($laenge);
//print "xx".$sane_scanner."rr".$scanner."yy".$scan_name;exit;

// Retrieve list of possible resolutions into $list
if (empty($_SESSION['resolution_'.$scanner])) {
	$out=array();
	$command=$SCANIMAGE.' --help | grep -m 1 resolution';
	//print "eeee".$command;
	dol_syslog("Detect resolution of scanner with command ".$command);

	// Warning: with some scanimage commande, the line with resolutions appears only for root user
	$res_list = exec($command, $out);

	//$res_list=`$command`;
	$start=strpos($res_list, "n")+2;
	$length = strpos($res_list, "dpi") -$start;
	$list = "".substr($res_list, $start, $length)."";
	unset($start);
	unset($length);

	if ($list) $_SESSION['resolution_'.$scanner]=$list;
	else $_SESSION['resolution_'.$scanner]='detection_res_not_possible';
} else {
	$list=$_SESSION['resolution_'.$scanner];
	dol_syslog("Resolution of scanner already stored in cache with value ".$list);
}


$scan_ausgabe=$scan_name."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Device = ".$scanner;
