<?PHP
echo "<table class=\"tab_menu\">\n";
echo "<col width=\"50%\">\n";
echo "<col width=\"50%\">\n";

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<th colspan=\"2\">".$lang[$lang_id][31]."</th>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

if (0)
{
echo "<tr>\n";
echo "<td align=\"left\">1</td>\n";
echo "<td align=\"left\">2</td>\n";
//echo "<td align=\"left\">3</td>\n";
//echo "<td align=\"left\">4</td>\n";
echo "</tr>\n";
}

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<td colspan=\"2\" >".$scan_ausgabe."</td>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<th colspan=\"2\">".$lang[$lang_id][0]."</th>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][1]."&nbsp;<INPUT type=\"text\" name=\"geometry_l\" value=\"".$geometry_l."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
echo "<td align=\"right\"><font id=\"ecke_rot1\" class=\"ecke_rot1\">".$lang[$lang_id][5]."</font>&nbsp;<INPUT type=\"radio\" name=\"ecke\" value=\"lo\" checked></td>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][2]."&nbsp;<INPUT type=\"text\" name=\"geometry_t\" value=\"".$geometry_t."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
echo "<td align=\"right\"><font id=\"ecke_rot2\">".$lang[$lang_id][6]."</font>&nbsp;<INPUT type=\"radio\" name=\"ecke\" value=\"ru\"></td>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][3]."&nbsp;<INPUT type=\"text\" name=\"geometry_x\" value=\"".$geometry_x."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
echo "<td align=\"right\">".$lang[$lang_id][7]."&nbsp;<INPUT type=\"text\" name=\"PosX\" value=\"0\" size=\"4\"></td>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][4]."&nbsp;<INPUT type=\"text\" name=\"geometry_y\" value=\"".$geometry_y."\" size=\"4\" maxlength=\"3\">&nbsp;mm</td>\n";
echo "<td align=\"right\">".$lang[$lang_id][8]."&nbsp;<INPUT type=\"text\" name=\"PosY\" value=\"0\" size=\"4\"></td>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

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
echo "<td>&nbsp;</td>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<th colspan=\"2\">".$lang[$lang_id][9]."</th>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

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

//jdw:
if ($do_negative)
{
$checked="";
if($negative=="yes") $checked="checked";
echo "<td align=\"right\">".$lang[$lang_id][20]."&nbsp;<INPUT type=\"checkbox\" name=\"negative\" value=\"yes\" ".$checked."></td>\n";
}
else
{
echo "<td>&nbsp;</td>\n";
}
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

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

// jdw:
if ($do_quality_cal)
{
$checked1="";
if($quality_cal=="yes") $checked1="checked";
echo "<td align=\"right\">".$lang[$lang_id][21]."&nbsp;<INPUT type=\"checkbox\" name=\"quality_cal\" value=\"yes\" ".$checked1."></td>\n";
}
else
{
echo "<td>&nbsp;</td>\n";
}
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<td align=\"right\">".$lang[$lang_id][18]."&nbsp;";
// retrieve possible resolutions
$res_list = `/usr/bin/scanimage --help | grep -m 1 resolution`;
$start=strpos($res_list,"n")+2;
$length = strpos($res_list,"dpi") -$start;
$list = "".substr($res_list,$start,$length)."";
unset($start);
unset($length);
// change "|" separated string $list into array $resolution values.
$resolution_list = explode("|",$list);
//generate html selectbox and store in string $res_box
$res_box = html_selectbox('resolution',$resolution_list,$resolution);
//display the select box
echo "$res_box";
echo "</td>\n";

// jdw:
if ($do_brightness)
{
echo "<td align=\"right\">".$lang[$lang_id][22]."&nbsp;<INPUT type=\"text\" value=\"".$brightness."\" name=\"brightness\" size=\"5\" maxlength=\"5\"></td>\n";
}
else
{
echo "<td>&nbsp;</td>\n";
}
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

// jdw:
if ($do_usr_opt)
{
echo "<tr>\n";
echo "<td colspan=\"2\" align=\"center\">".$lang[$lang_id][38]."&nbsp;<INPUT type=\"text\" value=\"".$usr_opt."\" name=\"usr_opt\" size=\"40\"></td>\n";
echo "</tr>\n";
}

////////////////////////////////////////////////////////////////////////

if (0)
{
echo "<tr>\n";
echo "<td>&nbsp;</td>\n";
//echo "<td align=\"right\">".$lang[$lang_id][19]."&nbsp;<SELECT name=\"depth\" size=\"1\">\n";
//if($depth=="8") $selected_1="selected"; else $selected_1="";
//if($depth=="12") $selected_2="selected"; else $selected_2="";
//echo "<option value=\"8\" $selected_1>8\n";
//echo "<option value=\"12\" $selected_2>12\n";
//echo "</SELECT></td>\n";
echo "<td>&nbsp;</td>\n";
echo "</tr>\n";
}

////////////////////////////////////////////////////////////////////////

if (1)
{
echo "<tr>\n";
echo "<td colspan=\"2\"><IMG src=\"./bilder/clear.gif\" width=\"1\" style=\"height: 1.0em;\" align=\"middle\" border=\"0\"></td>\n";
echo "</tr>\n";
}

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<td colspan=\"2\" align=\"center\" style=\"white-space: normal;\">";
echo "<INPUT type=\"submit\" name=\"action\" value=\"".$lang[$lang_id][24]."\">\n";
echo "&nbsp;\n";
echo "<INPUT type=\"submit\" name=\"action\" value=\"".$lang[$lang_id][27]."\">\n";
if ($do_ocr)
{
echo "&nbsp;\n";
echo "<INPUT type=\"submit\" name=\"action\" value=\"".$lang[$lang_id][26]."\">\n";
}
echo "&nbsp;\n";
echo "<span style=\"white-space: nowrap;\">";
echo "<INPUT type=\"submit\" name=\"action\" value=\"".$lang[$lang_id][25]."\">\n";
echo "&nbsp;\n";
echo "<INPUT type=\"submit\" name=\"action\" value=\"".$lang[$lang_id][28]."\">\n";
echo "</span>";
echo "</td>\n";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

if (1)
{
echo "<tr>\n";
echo "<td colspan=\"2\"><IMG src=\"./bilder/clear.gif\" width=\"1\" style=\"height: 0.0em;\" align=\"middle\" border=\"0\"></td>\n";
echo "</tr>\n";
}

////////////////////////////////////////////////////////////////////////

echo "<tr>\n";
echo "<td colspan=\"2\" align=\"center\">";
echo "<input type=\"button\" value=\"{$lang[$lang_id][37]}\" onclick=\"window.open('./help_{$lang_id}.php', 'help', '');\">";
echo "</tr>\n";

////////////////////////////////////////////////////////////////////////

echo "</table>\n";

?>