#!/bin/bash
#------------------------------------------
# Syntax:  loadsql.sh password
#------------------------------------------

export target="dolibarr"
mysql -u dolicloud -p$1 -D dolicloud < /home/ldestailleur/git/nltechno/dolicloudimages/$target/config/$target.sql

