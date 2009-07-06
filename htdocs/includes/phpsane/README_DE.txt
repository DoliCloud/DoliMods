   phpSANE   (Version 0.5.0)
  ~~~~~~~~~

phpSANE ist ein in PHP geschriebenes web-basierendes Frontend für SANE.
Jetzt können Sie auch mit Ihrem Web-Browser scannen.

Copyright (C) 2009  David Fröhlich <dfroe@gmx.de>
                    and John Walsh <john.walsh@mini-net.co.uk>

Dieses Programm ist freie Software. Sie können es unter den Bedingungen der GNU
General Public License, wie von der Free Software Foundation veröffentlicht,
weitergeben und/oder modifizieren, entweder gemäß Version 2 der Lizenz oder
(nach Ihrer Option) jeder späteren Version.
Die Veröffentlichung dieses Programms erfolgt in der Hoffnung, daß es Ihnen von
Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE, sogar ohne die implizite
Garantie der MARKTREIFE oder der VERWENDBARKEIT FÜR EINEN BESTIMMTEN ZWECK.
Details finden Sie in der GNU General Public License.
Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
Programm erhalten haben. Falls nicht, schreiben Sie an die Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.


------------------------------------------------------------------------

Voraussetzungen:
^^^^^^^^^^^^^^^^
* SANE
* netpbm
* Apache web-server mit PHP-Unterstützung
* gocr (optional)


------------------------------------------------------------------------

Installation:
^^^^^^^^^^^^^
Kopieren Sie einfach das phpSANE-Verzeichnis in Ihr www-root Verzeichnis.
Dann können Sie durch Öffnen der phpsane.php in Ihrem Web-Browser scannen.


------------------------------------------------------------------------

FAQ:
^^^^

F: Wie uberprufe ich, ob mein Scanner arbeitet?

A: Stellen Sie sicher, dass Ihr Scanner erkannt wurde und auf dem lokalen
Computer ordnunsgemaess funktioniert. Um dies zu ueberpruefen, verwenden
Sie bitte eine der Scanner-Anwendungen.

Application Menu->Graphics->Scanner Tool
Application Menu->Graphics->The GIMP, File->Aquire->XSane: Device Dialog...

Sollte Ihr Scanner nicht lokal auf Ihrem Computer funktionieren, wird er
auch nicht mit phpSANE funktionieren.

----------

Q: Mein Scanner wird nicht mit phpSANE gefunden?
Q: Mein Scanner wird erkannt, aber wenn ich Vorschau waehle oder scanne
ist die Bilddatei leer?

A: Dies ist moeglicherweise auf ein Problem mit der Rechtevergabe
zurueckzufuehren. Versuche :-

chmod +s /usr/bin/scanimage
chmod 775 WWW_PHPSANE_DIR/tmp

WWW_PHPSANE_DIR = Der www Bereich, in dem phpSANE verwendet wird.

z.B.: Stellen Sie sicher, dass Ihr Apache Benutzer scanimage verwenden
kann und Schreibrechte fuer das phpSANE tmp Verzeichnis hat.

----------

Q: phpSANE zeigt fuer meinen Scanner einen anderen Namen/Model an?

A: In verschiedenen Laendern kann ein Scanner unter unterschiedlichen
Namen und Nummern verkauft werden, obwohl die Hardware identisch ist.
Daher ist es moeglich, dass der Scanner mit einem anderen Namen
aufgefuehrt wird. Zum Beispiel wird die Scannerserie Epson Perfection
in Japan unter dem Namen GT-**** angeboten.

----------

Q: phpSANE funktioniert, aber wenn ich "Scan" verwende passiert nichts?

A: Zwei Dinge koennen falsch sein:-

a) Der Scan Bereich ist ungueltig, z.B. haben Sie keinen Bereich zum
Scannen ausgewaehlt.

b) Wenn ein Scan fertig ist, oeffnet sich ein neues Fenster mit der zu
speichernden Datei - dieses kann durch einen 'pop-up' blockiert
unterdrueckt worden sein.

----------

Q: phpSANE funktioniert, aber wie limitiere ich die Zugangsberechtigung
auf mein internes Netzwerk?

A: In httpd.conf :-

<Directory "WWW_PHPSANE_DIR/">
    DirectoryIndex phpsane.php
    Order Deny,Allow
    Deny from all
    Allow from 127.0.0.0/8
    Allow from 192.168.1.0/24
</Directory>

------------------------------------------------------------------------
