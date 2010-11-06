<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<meta name="author" content="root">
<meta name="robots" content="noindex">
<link rel="stylesheet" type="text/css" href="css/style.css">
<title>phpSANE: help</title>
</head>
<body>

<?PHP

// English Help
// ============

$SCANIMAGE = "/usr/bin/scanimage";
$cmd = $SCANIMAGE . " -h";
$sane_help = `$cmd`;
unset($cmd);

$start = strpos($sane_help, "\nOptions specific to device") + 1;
if ($start !== FALSE)
{
  $sane_help = substr($sane_help, $start);
}

$start = strpos($sane_help, "\nType ``scanimage --help");
if ($start !== FALSE)
{
  $sane_help = substr($sane_help, 0, $start);
}

echo <<<EOT

<h1>
phpSANE: Help
</h1>

<h3>
Scan Area
</h3>

<p>
Choosing a page size will set the required page dimensions.
</p>

<p>
Clicking on the 'preview image' area will set the appropriate
corner (top left or bottom right) to the mouse position.
</p>


<h3>
Scan Options
</h3>

<p>
Only the most basic options are supported directly,
that give you control over the image quality
(--mode and --resolution)
and the file format type to save the image as.
</p>

<h4>
Extra :-
</h4>

<p>
For your scanner,
the full list of options (from: scanimage -h) is :-
</p>

<p>
<pre>
{$sane_help}
</pre>
</p>


<p>
Any extra options you want to add to the scan command,
you can add them in this 'extra' field.
</p>

<p>
NB. Invalid characters are replaced with an 'X'.
</p>

<p>
eg. To control the brightness :-
</p>

<p>
The value is not stanard across all scanners,
so you need to see what options your scanner takes.
It may be a percentage,
or a number in a range (eg. -4..3).
</p>

<p>
eg.<br>
--brightness=50%<br>
--brightness 2<br>
</p>


<h3>
Action Buttons
</h3>

<h4>
Preview :-
</h4>

<p>
Does a scan of the whole area,
at a low resolution and displays it,
so you can view your document and select areas from it.
</p>


<h4>
Scan :-
</h4>

<p>
Does a scan of the area selected and lets you save the
output file (image or text).
</p>


<h4>
OCR :- (only available if 'gocr' is installed)
</h4>

<p>
Does a scan and uses OCR to convert the contents into an ASCII text file.
</p>

<p>
Recommend using Grayscale at 300dpi or above.
</p>


<h4>
Reset :-
</h4>

<p>
Re-loads the page,
but does nothing else.
</p>


<h4>
Clean :-
</h4>

<p>
Resets all parameters to their default values and clears the preview.
</p>


<h3>
Scan Command
</h3>

<p>
At the bottom of the page,
the command line used to perform the scan is displayed.
This allows you to manually check the format of the command
in case of errors.
</p>

EOT;

?>

</body>
</html>

