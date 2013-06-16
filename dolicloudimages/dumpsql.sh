#!/bin/bash

export target="dolibarr"
mysqldump --add-drop-table -u dolicloud -pxxx dolicloud > /home/ldestailleur/git/nltechno/dolicloudimages/$target/$target.sql
