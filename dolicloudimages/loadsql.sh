#!/bin/bash

export target="dolibarr"
mysql -u dolicloud -pxxx -D dolicloud < /home/ldestailleur/git/nltechno/dolicloudimages/$target/$target.sql

