#!/bin/bash
#------------------------------------------
# Syntax:  dumpsql.sh password
#------------------------------------------

if [ "x$1" = "x" ]
then
	echo Usage:   dumpsql.sh target
	echo Example: dumpsql.sh dolicloud
	echo Note :   Must be into directory dolicloudimages
	exit
fi

export target="$1"
mysqldump --add-drop-table -u $target -p$2 $target > /home/ldestailleur/git/nltechno/dolicloudimages/$target/config/$target.sql
