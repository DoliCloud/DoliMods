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


print  "<FORM name=\"menueForm\" action=\"phpsane.php\" method=\"GET\">\n";

print  "<input type=hidden name=\"first\" value=\"$first\">\n";
print  "<input type=hidden name=\"lang_id\" value=\"$lang_id\">\n";
print  "<input type=hidden name=\"sid\" value=\"$sid\">\n";
print  "<input type=hidden name=\"preview_images\" value=\"$preview_images\">\n";
print  "<input type=hidden name=\"preview_width\" value=\"$PREVIEW_WIDTH_MM\">\n";
print  "<input type=hidden name=\"preview_height\" value=\"$PREVIEW_HEIGHT_MM\">\n";
print  "<input type=hidden name=\"preview_scale\" value=\"$facktor\">\n";

// test
if ($do_test_mode)
{
    print  "<table class=\"page_body\">\n";
    print  "<tr>\n";
    print  "<td align=\"center\">\n";
    print  "Debug <INPUT type=\"text\" name=\"debug\" value=\"\" size=\"64\">\n";
    print  "</td>\n";
    print  "</tr>\n";
    print  "</table>\n";
}


if (! strlen($scanner) > 2)
{
    print  "<table cellspacing=\"0\" border=\"0\" cellpadding=\"0\" align=\"left\">\n";
    print  "<tr>\n";
    print  "<td class=\"achtung\" align=\"center\" valign=\"middle\">".$lang[$lang_id][33]."<br><br></td>\n";
    print  "</tr>\n";
    print  "<tr>\n";
    print  "<td align=\"center\" valign=\"middle\"><INPUT type=\"submit\" name=\"action\" value=\"".$lang[$lang_id][34]."\"></td>\n";
    print  "</tr>\n";
    print  "</table>\n";
}
else
{
    print  "<table class=\"page_body\">\n";
    print  "<tr>\n";
    print  '<td valign="top">'."\n";
    include("menu.php");
    print  "</td>\n";

    // Preview
    print  "<td class=\"photo\">\n";
    if (basename($preview_images) != 'scan.jpg')
    {
        print  "<IMG src=\"".DOL_URL_ROOT.'/viewimage.php?file='.basename($preview_images).'&modulepart=phpsane_user_temp'."\" width=\"$PREVIEW_WIDTH_PX\" height=\"$PREVIEW_HEIGHT_PX\" name=\"Preview\"><br>\n";
    }
    else
    {
        print  "<IMG src=\"".DOL_URL_ROOT.'/phpsane/images/scan.jpg'."\" width=\"$PREVIEW_WIDTH_PX\" height=\"$PREVIEW_HEIGHT_PX\" name=\"Preview\"><br>\n";
    }
    print  "</td>\n";

    print  "</tr>\n";
    print  "</table>\n";
}

print  "</FORM>\n";


print  '<br>';
print  '<hr>';

if ($cmd_device)
{

    print  "# $cmd_device\n";

    print  '<hr>';
}

// Add list of scan files
$nbrows=$formfile->show_documents('phpsane_user_temp','',$conf->phpsane->dir_temp.'/'.$user->id,$_SERVER["PHP_SELF"],0,1,'',0,1,0,0,1,'',$langs->trans("Files"));



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
?>