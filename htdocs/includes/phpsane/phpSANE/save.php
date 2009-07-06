<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<meta name="author" content="root">
<meta name="robots" content="noindex">
<link rel="stylesheet" type="text/css" href="./css/style.css">
<title>Save</title>
</head>
<body>
<?PHP
include("language.php");

$file_save = $_GET['file_save'];
$file_save_image = $_GET['file_save_image'];
$lang_id = $_GET['lang_id'];

if ($file_save_image)
{
  echo "<p class=\"align_center\">\n";
  echo "<img src=\"".$file_save."\" border=\"2\">\n";
  echo "</p>\n";
}
else
{
  // my_pre my_mono
  echo "<p class=\"my_pre\">\n";
  include($file_save);
  echo "</p>\n";
  echo "<hr>\n";
}

echo "<p class=\"align_center\">\n";
echo "<a href=\"$file_save\" target=\"_blank\">".$file_save."</a>\n";
echo "</p>\n";

echo "<p class=\"align_center\">\n";
echo $lang[$lang_id][35]."\n";
echo "</p>\n";

echo "<p class=\"align_center\">\n";
echo "<input type=\"button\" name=\"close\" value=\"".$lang[$lang_id][36]."\" onClick=\"javascript:window.close();\">\n";
echo "</p>\n";

echo "</body>\n";
echo "</html>\n";
?>
