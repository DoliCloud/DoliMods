#!/bin/bash
#------------------------------------------
# Syntax:  dumpsql.sh password
#------------------------------------------

export target="dolibarr"
mysqldump --add-drop-table -u dolicloud -p$1 dolicloud > /home/ldestailleur/git/nltechno/dolicloudimages/$target/config/$target.sql
