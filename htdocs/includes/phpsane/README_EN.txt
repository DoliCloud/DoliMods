   phpSANE   (Version 0.5.0)
  ~~~~~~~~~

phpSANE is a web-based frontend for SANE written in PHP.
Now you can scan with your web-browser, too.

Copyright (C) 2009  David Froehlich <dfroe@gmx.de>
                    and John Walsh <john.walsh@mini-net.co.uk>

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later
version.
This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
details.
You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc., 59
Temple Place, Suite 330, Boston, MA 02111-1307, USA.


------------------------------------------------------------------------

Requirements:
^^^^^^^^^^^^^
* SANE
* netpbm
* Apache web-server with PHP-support
* gocr (optional)


------------------------------------------------------------------------

Installation:
^^^^^^^^^^^^^
Just copy the phpSANE-directory into your www-root.
Then you can scan by opening phpsane.php in your web-browser.


------------------------------------------------------------------------

FAQ:
^^^^

Q: How do I check if my scanner is working ?

A: Before trying phpSANE, it is a good idea to make sure that your
scanner is detected and working from the local machine. To do this,
just use one of the scanner applications :-

Application Menu->Graphics->Scanner Tool
Application Menu->Graphics->The GIMP, File->Aquire->XSane: Device Dialog...

If your scanner does not work directly from your machine, then it will
not work through phpSANE.

----------

Q: My scanner isn't found by phpSANE ?
Q: My scanner is detected, but when I preview or scan, the image file
is empty ?

A: this is probably a permissions problem, try :-

chmod +s /usr/bin/scanimage
chmod 775 WWW_PHPSANE_DIR/tmp

WWW_PHPSANE_DIR = the www file area you put phpSANE at.

ie. Make sure that your apache user is able to scan with scanimage,
and your apache user must have write-access to the phpSANE tmp directory.

----------

Q: phpSANE is showing my scanner by a different name/model ?

A: In different regions of the world, a scanner may be sold by different
model names and numbers, but the internal hardware is exactly the same.
So it may be that to the SANE project, the scanner is recognised by a
different name to the one that you know your scanner as. For example,
the Epson Perfection range of scanners are sold in Japan under the
model names GT-****.

----------

Q: phpSANE is working, but when I 'Scan', nothing happens ?

A: There could be two things wrong :-

a) the 'Scan Area' could be invalid,
ie. you have not selected an area to scan.

b) when a scan completes, a new window is opened with the scan
results to allow you to save it - this may be blocked by a
'pop-up' blocker.

----------

Q: phpSANE is working fine, but how do I restrict access to it to my
internal network only ?
A: in httpd.conf :-

<Directory "WWW_PHPSANE_DIR/">
    DirectoryIndex phpsane.php
    Order Deny,Allow
    Deny from all
    Allow from 127.0.0.0/8
    Allow from 192.168.1.0/24
</Directory>

------------------------------------------------------------------------
