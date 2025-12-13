<?php
/* Copyright (C) 2010-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *  \file           htdocs/scanner/index.php
 *  \brief          Main page of scanner module
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

include_once DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php";
include_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
include_once "./functions.php";
include_once "./config.php";        // We can't use dol_include once for this because it set global variables used into this page, so we use relateive path

$langs->load("other");
$langs->load("scanner@scanner");


/*
 * Actions
 */

$lang_error='Error';
$error_input=0;

$tmplog = $conf->scanner->dir_temp.'/'.$user->id.'_scantmp.log';
dol_delete_file($tmplog);

if ($action == 'remove_file') {
		require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";

		$langs->load("other");
		$upload_dir = $conf->scanner->dir_temp.'/'.$user->id;
		$file = $upload_dir . '/' . GETPOST('file');
		$ret=dol_delete_file($file, 0, 0, 0, $object);
		if ($ret) setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
	else setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
}



$cmd_geometry_l="";
if (($geometry_l >= 0) && ($geometry_l <= $PREVIEW_WIDTH_MM)) {
	$cmd_geometry_l=" -l ".$geometry_l."mm";
} else {
	$lang[$lang_id][1]="<span class=\"input_error\">".$lang[$lang_id][1]."</span>";
}

$cmd_geometry_t="";
if (($geometry_t >= 0) && ($geometry_t <= $PREVIEW_HEIGHT_MM)) {
	$cmd_geometry_t=" -t ".$geometry_t."mm";
} else {
	$lang[$lang_id][2]="<span class=\"input_error\">".$lang[$lang_id][2]."</span>";
}

$cmd_geometry_x="";
if (($geometry_x >= 0) && ($geometry_x <= $PREVIEW_WIDTH_MM)) {
	$cmd_geometry_x=" -x ".$geometry_x."mm";
} else {
	$lang[$lang_id][3]="<span class=\"input_error\">".$lang[$lang_id][3]."</span>";
}

$cmd_geometry_y="";
if (($geometry_y >= 0) && ($geometry_y <= $PREVIEW_HEIGHT_MM)) {
	$cmd_geometry_y=" -y ".$geometry_y."mm";
} else {
	$lang[$lang_id][4]="<span class=\"input_error\">".$lang[$lang_id][4]."</span>";
}

//$cmd_mode=" --mode=\"".$mode."\"";
//$cmd_depth=" --depth ".$depth;

$cmd_resolution="";
if ($resolution >= 5 && $resolution <= 9600) {
	$cmd_resolution=" --resolution ".$resolution."dpi";
} else {
	$lang[$lang_id][18]="<span class=\"input_error\">".$lang[$lang_id][18]."</span>";
}

$cmd_negative="";
if ($do_negative) {
	if ($negative == "yes") $cmd_negative="";
}

$cmd_quality_cal="";
if ($do_quality_cal) {
	if ($quality_cal == "yes") $cmd_quality_cal="";
}

$cmd_brightness="";
if ($do_brightness) {
	if (1) {
		if ($brightness) $cmd_brightness=" --brightness ".$brightness;
	} else {
		if (($brightness >= -100) && ($brightness <= 100)) {
			$cmd_brightness=" --brightness ".$brightness;
		} else {
			$lang[$lang_id][22]="<span class=\"input_error\">".$lang[$lang_id][22]."</span>";
		}
	}
}

$cmd_usr_opt="";
if ($do_usr_opt) {
	$cmd_usr_opt=" ".$usr_opt;
}

$scan_yes='';
$rescmd='';
$cmd_device = '';
$file_save = '';
$file_save_image = 0;

$cmd_scan=escapeshellcmd($SCANIMAGE)." -d ".escapeshellarg(str_replace('"', '', $scanner)).$cmd_geometry_l.$cmd_geometry_t.$cmd_geometry_x.$cmd_geometry_y.$cmd_mode.$cmd_resolution.$cmd_negative.$cmd_quality_cal.$cmd_brightness.$cmd_usr_opt;

if ($error_input == 0) {
	// preview
	if (GETPOST('actionpreview')) {
		$preview_images = $TMP_PREFIX."preview_".$sid.".jpg";
		$cmd_device = escapeshellcmd($SCANIMAGE)." -d ".escapeshellarg(str_replace('"', '', $scanner))." --resolution ".$PREVIEW_DPI."dpi -l 0mm -t 0mm -x ".$PREVIEW_WIDTH_MM."mm -y ".$PREVIEW_HEIGHT_MM."mm".$cmd_mode.$cmd_negative.$cmd_quality_cal.$cmd_brightness.$cmd_usr_opt." 2> ".escapeshellarg(str_replace('"', '', $tmplog))." | ".$PNMTOJPEG." --quality=50 > \"".$preview_images."\"";
		//$cmd_device = escapeshellcmd($SCANIMAGE).' 2> '.escapeshellarg(str_replace('"','',$tmplog));
	}

	// scan
	if (GETPOST('actionscanimg')) {
		$file_save = $file_base . "." . $format;
		$file_save_image = 1;

		if ($format == "jpg") {
			$cmd_device = $cmd_scan." | {$PNMTOJPEG} --quality=100 > \"".$file_save."\"";
		}
		if ($format == "pnm") {
			$cmd_device = $cmd_scan." > \"".$file_save."\"";
		}
		if ($format == "tif") {
			$cmd_device = $cmd_scan." | {$PNMTOTIFF} > \"".$file_save."\"";
		}
	}

	// ocr
	if (GETPOST('actionocr')) {
		$file_save = $file_base . ".txt";
		$cmd_device = $cmd_scan." | ".$OCR." - > \"".$file_save."\"";
	}
}

if ($cmd_device !== '') {
	dol_mkdir($conf->scanner->dir_temp.'/'.$user->id);
	dol_syslog("Launch sane commande: ".$cmd_device);

	if (! dol_is_file($SCANIMAGE)) {
		$langs->load("errors");
		$langs->load("admin");
		print $langs->trans("ErrorFileNotFound", $SCANIMAGE).'<br>';
		print $langs->trans("NoteOnPathLocation").'<br>';
		exit;
	} else {
		$out=array();
		$return_var=0;
		$scan_yes=exec($cmd_device, $out, $return_var);
		$rescmd='Return: '.$return_var."\n".join(',', $out);
		$rescmd.=file_get_contents($tmplog);
		//print $scan_yes." | ".$cmd_device." | ".$rescmd;
	}
} else {
	$cmd_device = $lang[$lang_id][39];
}



/*
 * View
 */

// Note: An help of option of device can be find with command
// scanimage -h

$help_url="EN:Module_PHPSane_En|FR:Module_PHPSane|ES:M&oacute;dulo_PHPSane";
llxHeader('', 'Scanner', $help_url);

$safmodeon=ini_get('safe_mode');
//print 'ee'.$safmodeon;

$form=new Form($db);
$formfile=new FormFile($db);


print  "<FORM name=\"menueForm\" action=\"".$_SERVER["PHP_SELF"]."\" method=\"GET\">\n";

print  "<input type=hidden name=\"first\" value=\"$first\">\n";
print  "<input type=hidden name=\"sid\" value=\"$sid\">\n";
print  "<input type=hidden name=\"preview_images\" value=\"$preview_images\">\n";
print  "<input type=hidden name=\"preview_width\" value=\"$PREVIEW_WIDTH_MM\">\n";
print  "<input type=hidden name=\"preview_height\" value=\"$PREVIEW_HEIGHT_MM\">\n";
print  "<input type=hidden name=\"preview_scale\" value=\"$facktor\">\n";

// test
if ($do_test_mode) {
	print  "<table class=\"nobordernopadding\">\n";
	print  "<tr>\n";
	print  "<td align=\"center\">\n";
	print  "Debug <INPUT type=\"text\" name=\"debug\" value=\"\" size=\"64\">\n";
	print  "</td>\n";
	print  "</tr>\n";
	print  "</table>\n";
}


if (! strlen($scanner) > 2) {
	print  "<table cellspacing=\"0\" border=\"0\" cellpadding=\"0\" align=\"left\">\n";
	print  "<tr>\n";
	print  "<td class=\"achtung\" align=\"center\" valign=\"middle\">".$lang[$lang_id][33]."<br><br></td>\n";
	print  "</tr>\n";
	print  "<tr>\n";
	print  "<td align=\"center\" valign=\"middle\"><INPUT type=\"submit\" name=\"action\" value=\"".$lang[$lang_id][34]."\"></td>\n";
	print  "</tr>\n";
	print  "</table>\n";
} else {
	print  "<table class=\"nobordernopadding\">\n";
	print  "<tr>\n";
	print  '<td class="tdtop">'."\n";

	print "<table class=\"tab_menu\">\n";
	print "<col width=\"50%\">\n";
	print "<col width=\"50%\">\n";

	// Scanner device

	print "<tr class=\"liste_titre\">\n";
	print "<td colspan=\"2\">".$langs->trans("Scanner")."</td>\n";
	print "</tr>\n";

	//print $scan_ausgabe;exit;
	print "<tr>\n";
	print "<td colspan=\"2\">".$scan_ausgabe." &nbsp; &nbsp; &nbsp; <INPUT type=\"submit\" class=\"button\" name=\"actionclean\" value=\"".$langs->trans("AutoDetect")."\"></td>\n";

	print "</tr>\n";

	// Scan area

	print "<tr class=\"liste_titre\">\n";
	print "<td colspan=\"2\">".$langs->trans("ScanArea")."</td>\n";
	print "</tr>\n";

	print "<tr>\n";
	print "<td align=\"right\">".$langs->trans("Left")."&nbsp;<INPUT type=\"text\" name=\"geometry_l\" value=\"".$geometry_l."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
	print "</tr>\n";

	print "<tr>\n";
	print "<td align=\"right\">".$langs->trans("Top")."&nbsp;<INPUT type=\"text\" name=\"geometry_t\" value=\"".$geometry_t."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
	print "</tr>\n";

	print "<tr>\n";
	print "<td align=\"right\">".$langs->trans("Width")."&nbsp;<INPUT type=\"text\" name=\"geometry_x\" value=\"".$geometry_x."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
	print "</tr>\n";

	print "<tr>\n";
	print "<td align=\"right\">".$langs->trans("Height")."&nbsp;<INPUT type=\"text\" name=\"geometry_y\" value=\"".$geometry_y."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
	print "</tr>\n";

	print "<tr>\n";
	print "<td align=\"right\">\n";
	print '<select class="flat" name="pagesize" size="1">'."\n";
	print "<option value='0,0' onclick=\"setPageSize(this.form)\" selected>".$langs->trans("PageSize")."</option>\n";
	foreach ($PAGE_SIZE_LIST as $index => $page_values) {
		print "<option value='{$page_values[1]},{$page_values[2]}' onclick=\"setPageSize(this.form)\">{$page_values[0]}</option>\n";
	}
	print "</select>\n";
	print "</td>\n";
	print "</tr>\n";

	print "<tr class=\"liste_titre\">\n";
	print "<td colspan=\"2\">".$langs->trans("Options")."</td>\n";
	print "</tr>\n";

	print "<tr>\n";
	print "<td align=\"right\">".$langs->trans("FileFormat")."&nbsp;";
	print '<select class="flat" name="format" size="1">'."\n";
	if ($format=="jpg") $selected_1="selected"; else $selected_1="";
	if ($format=="pnm") $selected_2="selected"; else $selected_2="";
	if ($format=="tif") $selected_3="selected"; else $selected_3="";
	print "<option value=\"jpg\" $selected_1>Jpg\n";
	print "<option value=\"pnm\" $selected_2>Pnm\n";
	print "<option value=\"tif\" $selected_3>Tif\n";
	print "</select>\n";
	print "</td>\n";
	print "</tr>\n";

	/*
	print "<tr>\n";
	print "<td align=\"right\">".$langs->trans("Mode")."&nbsp;";
	print "<SELECT name=\"mode\" size=\"1\">\n";
	if($mode=="Color")  $selected_1="selected"; else $selected_1="";
	if($mode=="Gray")   $selected_2="selected"; else $selected_2="";
	if($mode=="Binary") $selected_3="selected"; else $selected_3="";
	print "<option value=\"Color\"  $selected_1>Color\n";
	print "<option value=\"Gray\"   $selected_2>Gray\n";
	print "<option value=\"Binary\" $selected_3>Binary\n";
	print "</SELECT>\n";
	print "</td>\n";
	print "</tr>\n";
	*/

	print "<tr>\n";
	print "<td align=\"right\">".$langs->trans("ResolutionDPI")."&nbsp;";

	// change "|" separated string $list into array $resolution values.
	if (empty($list) || $list=='detection_res_not_possible') $list='100|150|300|600';
	$resolution_list = explode("|", $list);
	//generate html selectbox and store in string $res_box
	$res_box = html_selectbox('resolution', $resolution_list, $resolution);
	//display the select box
	print "$res_box";
	print "</td>\n";

	if ($do_brightness) {
		print "<td align=\"right\">".$langs->trans("Brightness")."&nbsp;<INPUT type=\"text\" value=\"".$brightness."\" name=\"brightness\" size=\"5\" maxlength=\"5\"></td>\n";
	} else {
		print "<td>&nbsp;</td>\n";
	}
	print "</tr>\n";

	// Options
	if ($do_usr_opt) {
		print "<tr>\n";
		print "<td colspan=\"2\" align=\"center\">".$langs->trans("Extra")."&nbsp;<INPUT type=\"text\" value=\"".$usr_opt."\" name=\"usr_opt\" size=\"40\"></td>\n";
		print "</tr>\n";
	}

	print "<tr>\n";
	print "<td colspan=\"2\" align=\"center\" style=\"white-space: normal;\">";
	print "<INPUT type=\"submit\" class=\"button\" name=\"actionpreview\" value=\"".$langs->trans("Preview")."\">\n";
	print "&nbsp;\n";
	print "<INPUT type=\"submit\" class=\"button\" name=\"actionscanimg\" value=\"".$langs->trans("Scan")."\">\n";
	if ($do_ocr) {
		print "&nbsp;\n";
		print "<INPUT type=\"submit\" class=\"button\" name=\"actionocr\" value=\"".$langs->trans("OCR")."\">\n";
	}
	print "</td></tr>\n";

	print "</table>\n";

	print  "</td>\n";


	// Preview
	print  "<td class=\"photo\">\n";
	if (basename($preview_images) != 'scan.jpg') {
		print  "<IMG src=\"".DOL_URL_ROOT.'/viewimage.php?file='.basename($preview_images).'&modulepart=scanner_user_temp'."\" width=\"$PREVIEW_WIDTH_PX\" height=\"$PREVIEW_HEIGHT_PX\" name=\"Preview\"><br>\n";
	} else {
		print  "<IMG src=\"".dol_buildpath('/scanner/img/scan.jpg', 1)."\" width=\"$PREVIEW_WIDTH_PX\" height=\"$PREVIEW_HEIGHT_PX\" name=\"Preview\"><br>\n";
	}
	print  "</td>\n";

	print  "</tr>\n";
	print  "</table>\n";
}

print  "</FORM>\n";


print  '<br>';
print  '<hr>';

if ($cmd_device) {
	print  "# ".$cmd_device."<br>\n";
	print dol_nl2br($rescmd);
	print  '<hr>';
}

// Add list of scan files
print $formfile->showdocuments('scanner_user_temp', '', $conf->scanner->dir_temp.'/'.$user->id, $_SERVER["PHP_SELF"], 0, 1, '', 0, 1, 0, 0, 1, '', $langs->trans("Files"));


// Inline javascript functions, after form areas
print  "<script language=\"JavaScript\" type=\"text/javascript\">\n";
print  "<!--\n";
print '
function setPageSize(form)
{
  var page_size = form.pagesize[form.pagesize.selectedIndex].value.split(",");
  var page_x = parseInt(page_size[0]);
  var page_y = parseInt(page_size[1]);

  if ((page_x > 0) && (page_y > 0))
  {
    setGeometry(0, 0, page_x, page_y);
  }

  //document.menueForm.debug.value = form.pagesize[form.pagesize.selectedIndex].value;

  return(true);
}

function setGeometry(l, t, x, y)
{
  document.menueForm.geometry_l.value = l;
  document.menueForm.geometry_t.value = t;
  document.menueForm.geometry_x.value = x;
  document.menueForm.geometry_y.value = y;
}
';
print  "//-->\n";
print  "</script>\n";


llxFooter();

$db->close();
