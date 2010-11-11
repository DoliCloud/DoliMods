<?PHP
echo "<table class=\"tab_menu\">\n";
echo "<col width=\"50%\">\n";
echo "<col width=\"50%\">\n";

// Scanner device

echo "<tr>\n";
echo "<th colspan=\"2\">".$lang[$lang_id][31]."</th>\n";
echo "</tr>\n";

//print $scan_ausgabe;exit;
echo "<tr>\n";
echo "<td colspan=\"2\">".$scan_ausgabe." &nbsp; &nbsp; &nbsp; <INPUT type=\"submit\" class=\"button\" name=\"actionclean\" value=\"".$lang[$lang_id][28]."\"></td>\n";

echo "</tr>\n";

// Scan area

echo "<tr>\n";
echo "<th colspan=\"2\">".$lang[$lang_id][0]."</th>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][1]."&nbsp;<INPUT type=\"text\" name=\"geometry_l\" value=\"".$geometry_l."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][2]."&nbsp;<INPUT type=\"text\" name=\"geometry_t\" value=\"".$geometry_t."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][3]."&nbsp;<INPUT type=\"text\" name=\"geometry_x\" value=\"".$geometry_x."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][4]."&nbsp;<INPUT type=\"text\" name=\"geometry_y\" value=\"".$geometry_y."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td align=\"right\">\n";
echo "<select name='pagesize' size=1>\n";
echo "<option value='0,0' onclick=\"setPageSize(this.form)\" selected>{$lang[$lang_id][40]}</option>\n";
foreach ($PAGE_SIZE_LIST as $index => $page_values)
{
    echo "<option value='{$page_values[1]},{$page_values[2]}' onclick=\"setPageSize(this.form)\">{$page_values[0]}</option>\n";
}
echo "</select>\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<th colspan=\"2\">".$lang[$lang_id][9]."</th>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][10]."&nbsp;";
echo "<SELECT name=\"format\" size=\"1\">\n";
if($format=="jpg") $selected_1="selected"; else $selected_1="";
if($format=="pnm") $selected_2="selected"; else $selected_2="";
if($format=="tif") $selected_3="selected"; else $selected_3="";
echo "<option value=\"jpg\" $selected_1>".$lang[$lang_id][11]."\n";
echo "<option value=\"pnm\" $selected_2>".$lang[$lang_id][12]."\n";
echo "<option value=\"tif\" $selected_3>".$lang[$lang_id][13]."\n";
echo "</SELECT>\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][14]."&nbsp;";
echo "<SELECT name=\"mode\" size=\"1\">\n";
if($mode=="Color")  $selected_1="selected"; else $selected_1="";
if($mode=="Gray")   $selected_2="selected"; else $selected_2="";
if($mode=="Binary") $selected_3="selected"; else $selected_3="";
echo "<option value=\"Color\"  $selected_1>".$lang[$lang_id][15]."\n";
echo "<option value=\"Gray\"   $selected_2>".$lang[$lang_id][16]."\n";
echo "<option value=\"Binary\" $selected_3>".$lang[$lang_id][17]."\n";
echo "</SELECT>\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][18]."&nbsp;";

// change "|" separated string $list into array $resolution values.
$resolution_list = explode("|",$list);
//generate html selectbox and store in string $res_box
$res_box = html_selectbox('resolution',$resolution_list,$resolution);
//display the select box
echo "$res_box";
echo "</td>\n";

if ($do_brightness)
{
    echo "<td align=\"right\">".$lang[$lang_id][22]."&nbsp;<INPUT type=\"text\" value=\"".$brightness."\" name=\"brightness\" size=\"5\" maxlength=\"5\"></td>\n";
}
else
{
    echo "<td>&nbsp;</td>\n";
}
echo "</tr>\n";

// Options
if ($do_usr_opt)
{
    echo "<tr>\n";
    echo "<td colspan=\"2\" align=\"center\">".$lang[$lang_id][38]."&nbsp;<INPUT type=\"text\" value=\"".$usr_opt."\" name=\"usr_opt\" size=\"40\"></td>\n";
    echo "</tr>\n";
}

echo "<tr>\n";
echo "<td colspan=\"2\" align=\"center\" style=\"white-space: normal;\">";
echo "<INPUT type=\"submit\" class=\"button\" name=\"actionpreview\" value=\"".$lang[$lang_id][24]."\">\n";
echo "&nbsp;\n";
echo "<INPUT type=\"submit\" class=\"button\" name=\"actionscanimg\" value=\"".$lang[$lang_id][27]."\">\n";
if ($do_ocr)
{
    echo "&nbsp;\n";
    echo "<INPUT type=\"submit\" class=\"button\" name=\"actionocr\" value=\"".$lang[$lang_id][26]."\">\n";
}
echo "</td></tr>\n";

echo "</table>\n";
?>