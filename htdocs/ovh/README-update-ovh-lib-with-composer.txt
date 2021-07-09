
* Be sure to use PHP 5.6:
sudo update-alternatives --config php


* Delete file composer.lock
cd ovh
rm composer.lock


* Then load ovh lib and dependencies with:
composer require ovh/ovh vX.Y.Z -W

