#!/bin/bash
#------------------------------------------
# Syntax:  loadsql.sh password
#------------------------------------------

if [ "x$1" = "x" ]
then
	echo Usage:   loadsql.sh target
	echo Example: dumpsql.sh dolicloud
	echo Note :   Must be into directory dolicloudimages
	exit
fi

export target="$1"
mysql -u $target -p$2 -D $target < /home/ldestailleur/git/nltechno/dolicloudimages/$target/config/$target.sql

