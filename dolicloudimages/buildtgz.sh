#!/bin/bash

if [ "x$1" = "x" ]
then
	echo Usage: buildtgz.sh target
	echo Note : Must be into directory dolicloudimages
	exit
fi

export target="$1"
cd $target/src && tar --exclude-vcs --exclude conf.php --exclude documents -cvzf $target.tgz *
mv $target.tgz ../config
cd .. 
