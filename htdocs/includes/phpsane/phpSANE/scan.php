<?php
$lang_error=$lang[$lang_id][32];
$error_input=0;

function scan_error(&$scan_ausgabe, &$error_input, $lang_error)
{
  $scan_ausgabe="!!!!!!!! ".$lang_error." !!!!!!!!";
  $error_input=1;
}

////////////////////////////////////////////////////////////////////////

// build the scan command options

$cmd_geometry_l="";
if (($geometry_l >= 0) && ($geometry_l <= $PREVIEW_WIDTH_MM))
{
  $cmd_geometry_l=" -l ".$geometry_l."mm";
}
else
{
  $lang[$lang_id][1]="<span class=\"input_error\">".$lang[$lang_id][1]."</span>";
  scan_error($scan_ausgabe, $error_input, $lang_error);
}

$cmd_geometry_t="";
if (($geometry_t >= 0) && ($geometry_t <= $PREVIEW_HEIGHT_MM))
{
  $cmd_geometry_t=" -t ".$geometry_t."mm";
}
else
{
  $lang[$lang_id][2]="<span class=\"input_error\">".$lang[$lang_id][2]."</span>";
  scan_error($scan_ausgabe, $error_input, $lang_error);
}

$cmd_geometry_x="";
if (($geometry_x >= 0) && ($geometry_x <= $PREVIEW_WIDTH_MM))
{
  $cmd_geometry_x=" -x ".$geometry_x."mm";
}
else
{
  $lang[$lang_id][3]="<span class=\"input_error\">".$lang[$lang_id][3]."</span>";
  scan_error($scan_ausgabe, $error_input, $lang_error);
}

$cmd_geometry_y="";
if (($geometry_y >= 0) && ($geometry_y <= $PREVIEW_HEIGHT_MM))
{
  $cmd_geometry_y=" -y ".$geometry_y."mm";
}
else
{
  $lang[$lang_id][4]="<span class=\"input_error\">".$lang[$lang_id][4]."</span>";
  scan_error($scan_ausgabe, $error_input, $lang_error);
}

$cmd_mode=" --mode=\"".$mode."\"";

//$cmd_depth=" --depth ".$depth;

$cmd_resolution="";
if ($resolution >= 5 && $resolution <= 9600)
{
  $cmd_resolution=" --resolution ".$resolution."dpi";
}
else
{
  $lang[$lang_id][18]="<span class=\"input_error\">".$lang[$lang_id][18]."</span>";
  scan_error($scan_ausgabe, $error_input, $lang_error);
}

$cmd_negative="";
if ($do_negative)
{
  if ($negative == "yes") $cmd_negative="";
}

$cmd_quality_cal="";
if ($do_quality_cal)
{
  if ($quality_cal == "yes") $cmd_quality_cal="";
}

$cmd_brightness="";
if ($do_brightness)
{
  if (1)
  {
    if ($brightness) $cmd_brightness=" --brightness ".$brightness;
  }
  else
  {
    if (($brightness >= -100) && ($brightness <= 100))
    {
      $cmd_brightness=" --brightness ".$brightness;
    }
    else
    {
      $lang[$lang_id][22]="<span class=\"input_error\">".$lang[$lang_id][22]."</span>";
      scan_error($scan_ausgabe, $lang_error);
    }
  }
}

$cmd_usr_opt="";
if ($do_usr_opt)
{
  $cmd_usr_opt=" ".$usr_opt;
}

////////////////////////////////////////////////////////////////////////

// build the device command

$scan_yes='';
$cmd_device = '';
$file_save = '';
$file_save_image = 0;

$cmd_scan=$SCANIMAGE." -d ".$scanner.$cmd_geometry_l.$cmd_geometry_t.$cmd_geometry_x.$cmd_geometry_y.$cmd_mode.$cmd_resolution.$cmd_negative.$cmd_quality_cal.$cmd_brightness.$cmd_usr_opt;

if ($error_input == 0)
{
  // preview
  if ($action == $lang[$lang_id][24])
  {
    $preview_images = $TMP_PRAEFIX."/preview_".$sid.".jpg";
    $cmd_device = $SCANIMAGE." -d ".$scanner." --resolution ".$PREVIEW_DPI."dpi -l 0mm -t 0mm -x ".$PREVIEW_WIDTH_MM."mm -y ".$PREVIEW_HEIGHT_MM."mm".$cmd_mode.$cmd_negative.$cmd_quality_cal.$cmd_brightness.$cmd_usr_opt." | ".$PNMTOJPEG." --quality=50 > ".$preview_images;
  }

  // scan
  if ($action == $lang[$lang_id][27])
  {
    $file_save = $file_base . "." . $format;
    $file_save_image = 1;

    if ($format == "jpg")
    {
      $cmd_device = $cmd_scan." | {$PNMTOJPEG} --quality=100 > ".$file_save;
    }
    if ($format == "pnm")
    {
      $cmd_device = $cmd_scan." > ".$file_save;
    }
    if ($format == "tif")
    {
      $cmd_device = $cmd_scan." | {$PNMTOTIFF} > ".$file_save;
    }
  }

  // ocr
  if ($action == $lang[$lang_id][26])
  {
    $file_save = $file_base . ".txt";
    $cmd_device = $cmd_scan." | ".$OCR." - > ".$file_save;
  }
}

////////////////////////////////////////////////////////////////////////

// perform actions required

if ($cmd_device !== '')
{
  $scan_yes=`$cmd_device`;
}
else
{
  $cmd_device = $lang[$lang_id][39];
}

if ($file_save !== '')
{
  echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
  echo "window.open(\"./save.php?file_save=".$file_save."&file_save_image=".$file_save_image."&lang_id=".$lang_id."\",\"_blank\", \"width=400,height=500,left=320,top=200,scrollbars=yes,location=no,status=no,menubar=no\");\n";
  echo "</script>\n";
}

if ($clean == 1) $cleaner_yes=`$cleaner`;
?>