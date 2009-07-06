<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<meta name="author" content="root">
<meta name="robots" content="noindex">
<link rel="stylesheet" type="text/css" href="./css/style.css">
<title>phpSANE</title>
</head>
<body>
<?PHP

// phpSANE
// Version: 0.5.0
// John Walsh <john.walsh@mini-net.co.uk>

include("functions.php");
include("language.php");
include("config.php");
include("scan.php");

////////////////////////////////////////////////////////////////////////

if (0)
{
echo "<style type=\"text/css\">\n";
echo "<!--\n";
include("css/style.css");
echo "-->\n";
echo "</style>\n";
}

////////////////////////////////////////////////////////////////////////

echo "<FORM name=\"menueForm\" action=\"phpsane.php\" method=\"GET\">\n";

echo "<input type=hidden name=\"first\" value=\"$first\">\n";
echo "<input type=hidden name=\"lang_id\" value=\"$lang_id\">\n";
echo "<input type=hidden name=\"sid\" value=\"$sid\">\n";
echo "<input type=hidden name=\"preview_images\" value=\"$preview_images\">\n";
echo "<input type=hidden name=\"preview_width\" value=\"$PREVIEW_WIDTH_MM\">\n";
echo "<input type=hidden name=\"preview_height\" value=\"$PREVIEW_HEIGHT_MM\">\n";
echo "<input type=hidden name=\"preview_border\" value=\"$PREVIEW_BORDER_PX\">\n";
echo "<input type=hidden name=\"preview_scale\" value=\"$facktor\">\n";

////////////////////////////////////////////////////////////////////////

// page header

echo "<table class=\"page_header\">\n";
echo "  <tr>\n";
echo "    <td width=1px>\n";
echo "      <input type=\"image\" name=\"lang_id\" value=\"0\" src=\"./bilder/de.gif\">\n";
echo "      &nbsp;\n";
echo "      <input type=\"image\" name=\"lang_id\" value=\"1\" src=\"./bilder/en.gif\">\n";
echo "    </td>\n";
echo "    <td align=\"center\">\n";
echo "      <img src=\"./bilder/logo.jpg\" alt=\"phpSANE\" border=\"0\">\n";
echo "    </td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td colspan=2>\n";
echo "<IMG src=\"./bilder/black.gif\" width=\"100%\" height=\"2px\" align=\"middle\" border=\"0\">\n";
echo "    </td>\n";
echo "  </tr>\n";
echo "</table>\n";

// page header - end

////////////////////////////////////////////////////////////////////////

// testing debug box

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


////////////////////////////////////////////////////////////////////////

// page body

echo "<table class=\"page_body\">\n";
echo "<tr>\n";

////////////////////////////////////////////////////////////////////////

// control panel area

echo "<td>\n";

if (strlen($scanner) > 2)
{
include("menu.php");
}
else
{
if (0)
{
echo "<input type=hidden name=\"geometry_l\" value=\"".$geometry_l."\">\n";
echo "<input type=hidden name=\"geometry_t\" value=\"".$geometry_t."\">\n";
echo "<input type=hidden name=\"geometry_x\" value=\"".$geometry_x."\">\n";
echo "<input type=hidden name=\"geometry_y\" value=\"".$geometry_y."\">\n";
echo "<input type=hidden name=\"format\" value=\"".$format."\">\n";
echo "<input type=hidden name=\"mode\" value=\"".$mode."\">\n";
echo "<input type=hidden name=\"resolution\" value=\"".$resolution."\">\n";
echo "<input type=hidden name=\"negative\" value=\"".$negative."\">\n";
echo "<input type=hidden name=\"quality_cal\" value=\"".$quality_cal."\">\n";
echo "<input type=hidden name=\"brightness\" value=\"".$brightness."\">\n";
}

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

////////////////////////////////////////////////////////////////////////

// preview area

echo "<td class=\"tab_preview\">\n";
echo "<IMG src=\"$preview_images\" width=\"$PREVIEW_WIDTH_PX\" height=\"$PREVIEW_HEIGHT_PX\" border=\"{$PREVIEW_BORDER_PX}px\" name=\"Preview\"><br>\n";
echo "</td>\n";

////////////////////////////////////////////////////////////////////////

echo "</tr>\n";
echo "</table>\n";

if (0)
{
echo "<p>\n";
echo "width=\"$PREVIEW_WIDTH_PX\" height=\"$PREVIEW_HEIGHT_PX\"\n";
echo "</p>\n";
}

// page body - end

////////////////////////////////////////////////////////////////////////

// page footer

echo "<table class=\"page_footer\">\n";

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<td>\n";
echo "<IMG src=\"./bilder/black.gif\" width=\"100%\" height=\"2px\" align=\"middle\" border=\"0\">\n";
echo "</td>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

// jdw:
echo "<tr>\n";
echo "<td>\n";
echo "# $cmd_device\n";
echo "</td>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

if (0)
{
echo "<tr>\n";
echo "<td>\n";
echo "$lang[$lang_id][30]</td>\n";
echo "</td>\n";
echo "</tr>\n";
}

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<td>\n";
echo "<IMG src=\"./bilder/black.gif\" width=\"100%\" height=\"2px\" align=\"middle\" border=\"0\">\n";
echo "</td>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

echo "</table>\n";

// page footer - end

////////////////////////////////////////////////////////////////////////

// inline javascript functions, after form areas

echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
echo "<!--\n";
include("javascript/js_fns.js");
echo "//-->\n";
echo "</script>\n";

echo "</FORM>\n";
?>

</body>
</html>
