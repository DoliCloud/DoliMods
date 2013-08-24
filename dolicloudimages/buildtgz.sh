#!/bin/bash

if [ "x$1" = "x" ]
then
	echo Usage: buildtgz.sh target
	echo Note : Must be into directory dolicloudimages
	exit
fi

export target="$1"
cd $target/src && tar --exclude custom --exclude documents --exclude doli*.tgz --exclude doli*.deb --exclude doli*.exe --exclude doli*.zip --exclude doli*.rpm --exclude .cache --exclude .settings --exclude conf.php --exclude conf.php.mysql --exclude conf.php.old --exclude conf.php.postgres -cvzf $target.tgz *
mv $target.tgz ../config
cd .. 
