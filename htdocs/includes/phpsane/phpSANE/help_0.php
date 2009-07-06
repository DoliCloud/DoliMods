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

// German Help
// ===========

$SCANIMAGE = "/usr/bin/scanimage";
$cmd = $SCANIMAGE . " -h";
$sane_help = `$cmd`;
unset($cmd);

$start = strpos($sane_help, "\nOptions specific to device") + 1;
$sane_help = substr($sane_help, $start);

$start = strpos($sane_help, "\nType ``scanimage --help");
$sane_help = substr($sane_help, 0, $start);

echo <<<EOT

<h1>
phpSANE: Hilfe
</h1>

<h3>
Scan-Bereich
</h3>

<p>
Das W&#228;hlen einer Seitengr&#246;&#223;e stellt die erforderlichen
Gr&#246;&#223;en der Seiten ein.
</p>

<p>
Ein Klick auf 'Bildvorschau' setzt die gew&#228;hlte Ecke
(oben linkes oder unteren rechts) an die Cursorposition.
</p>


<h3>
Scan-W&#228;hlen
</h3>

<p>
Nur die grundlegendsten Einstellm&#246;glichkeiten werden direkt
unterst&#252;tzt, wie die Einstellung der Bildqualit&#228;t
(-Modus und -Aufl&#246;sung)
und das zu speichernde Dateiformat.
</p>

<h4>
Extra :-
</h4>

<p>
F&#252;r Ihren Scanner lautet die gesamte Liste von Optionen
(Hilfe: scanimage - h) :-
</p>

<p>
<pre>
{$sane_help}
</pre>
</p>


<p>
Extraoptionen,
die Sie dem Scan-Befehl hinzuf&#252;gen m&#246;chten,
k&#246;nnen Sie in das 'Extra' Feld eintragen.
</p>

<p>
Anmerkung: Unzul&#228;ssige Buchstaben werden durch ein 'x' ersetzt.
</p>

<p>
z.B. Steuerung der Helligkeit :-
</p>

<p>
Die Angabe ist nicht bei allen Scannern gleich.
Demnach m&#252;ssen Sie probieren,
welche Angabe Ihr Scanner erfordert.
Es kann ein Prozentsatz sein,
oder eine Zahl (z.B. -4..3).
</p>

<p>
z.B.<br>
--brightness=50%<br>
--brightness 2<br>
</p>


<h3>
Befehlsfelder
</h3>

<h4>
Vorschau :-
</h4>

<p>
Scant den vollst&#228;ndigen Bereich in einer niedrigen Aufl&#246;sung,
so dass Sie Ihr Dokument ansehen und Ausschnitte ausw&#228;hlen
k&#246;nnen.
</p>


<h4>
Scannen :-
</h4>

<p>
Scant den vorgew&#228;hlten Bereich und speichert ihn in
der Ausgabedatei (Bild oder Text).
</p>


<h4>
OCR :- (nur vorhanden wenn 'gocr' installiert ist)
</h4>

<p>
Scant und verwendet OCR,
um den Inhalt in eine ASCII-Textdatei umzuwandeln.
</p>

<p>
Empfohlen wird Grayscale bei 300dpi oder mehr.
</p>


<h4>
Zur&#252;cksetzen :-
</h4>

<p>
L&#228;dt die Seite neu,
aber veraendert sonst nichts.
</p>


<h4>
Zur&#252;ecksetzen :-
</h4>

<p>
Stellt alle Parameter auf ihre Ausgangswerte zur&#252;ck und
l&#246;scht die Vorschau.
</p>


<h3>
Scan Befehl
</h3>

<p>
Unten auf der Seite wird die Befehlszeile angezeigt,
mit der der Scan durchgefuehrt wurde.
Dies erlaubt Ihnen,
den Befehl im Falle von Fehlern manuell zu
&#252;berpr&#252;fen und zu ver&#228;ndern.
</p>

EOT;

?>

</body>
</html>

