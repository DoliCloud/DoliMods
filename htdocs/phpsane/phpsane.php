<?PHP

// phpSANE
// Version: 0.5.0
// John Walsh <john.walsh@mini-net.co.uk>


if (! defined('NOCSRFCHECK')) define('NOCSRFCHECK',1);
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");   // If pre.inc.php is called by jawstats
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
include(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");

include("functions.php");
include("language.php");
include("config.php");
include("scan.php");


/*
 * View
 */

// Note: An help of option of device can be find with command
// scanimage -h

$help_url="EN:Module_PHPSane_En|FR:Module_PHPSane|ES:M&oacute;dulo_PHPSane";
llxHeader('','PHPSane',$help_url);

$form=new Form($db);
$formfile=new FormFile($db);


echo "<FORM name=\"menueForm\" action=\"phpsane.php\" method=\"GET\">\n";

echo "<input type=hidden name=\"first\" value=\"$first\">\n";
echo "<input type=hidden name=\"lang_id\" value=\"$lang_id\">\n";
echo "<input type=hidden name=\"sid\" value=\"$sid\">\n";
echo "<input type=hidden name=\"preview_images\" value=\"$preview_images\">\n";
echo "<input type=hidden name=\"preview_width\" value=\"$PREVIEW_WIDTH_MM\">\n";
echo "<input type=hidden name=\"preview_height\" value=\"$PREVIEW_HEIGHT_MM\">\n";
echo "<input type=hidden name=\"preview_scale\" value=\"$facktor\">\n";

// test
if ($do_test_mode)
{
    echo "<table class=\"page_body\">\n";
    echo "<tr>\n";
    echo "<td align=\"center\">\n";
    echo "Debug <INPUT type=\"text\" name=\"debug\" value=\"\" size=\"64\">\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
}


echo "<table class=\"page_body\">\n";
echo "<tr>\n";

// control panel area

echo '<td valign="top">'."\n";

if (strlen($scanner) > 2)
{
    include("menu.php");
}
else
{
    echo "<table cellspacing=\"0\" border=\"0\" cellpadding=\"0\" align=\"left\">\n";
    echo "<tr>\n";
    echo "<td class=\"achtung\" align=\"center\" valign=\"middle\">".$lang[$lang_id][33]."<br><br></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td align=\"center\" valign=\"middle\"><INPUT type=\"submit\" name=\"action\" value=\"".$lang[$lang_id][34]."\"></td>\n";
    echo "</tr>\n";
    echo "</table>\n";
}

echo "</td>\n";

// Preview

echo "<td class=\"photo\">\n";
if (basename($preview_images) != 'scan.jpg')
{
    echo "<IMG src=\"".DOL_URL_ROOT.'/viewimage.php?file='.basename($preview_images).'&modulepart=phpsane_user_temp'."\" width=\"$PREVIEW_WIDTH_PX\" height=\"$PREVIEW_HEIGHT_PX\" name=\"Preview\"><br>\n";
}
else
{
    echo "<IMG src=\"".DOL_URL_ROOT.'/phpsane/images/scan.jpg'."\" width=\"$PREVIEW_WIDTH_PX\" height=\"$PREVIEW_HEIGHT_PX\" name=\"Preview\"><br>\n";
}
echo "</td>\n";

echo "</tr>\n";
echo "</table>\n";

echo "</FORM>\n";


echo '<br>';

print '<hr>';

echo "<table class=\"border\" width=\"100%\">\n";
echo "<tr>\n";
echo "<td>\n";
echo "# $cmd_device\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo '<hr>';


// Add list of scan files
$nbrows=$formfile->show_documents('phpsane_user_temp','',$conf->phpsane->dir_temp.'/'.$user->id,$_SERVER["PHP_SELF"],0,1);



// inline javascript functions, after form areas
echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
echo "<!--\n";
include("javascript/js_fns.js");
echo "//-->\n";
echo "</script>\n";



llxFooter();
?>