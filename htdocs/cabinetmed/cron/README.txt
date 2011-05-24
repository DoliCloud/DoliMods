#!/bin/sh
# This is an example of command to put into a cron script to launch automatic 
# database dump and automatic backup of dolibarr directories into another disk.
# We assume that database is called 'dolimed'

# This is a command to make a mysql dump
/Applications/MAMP/Library/bin/mysqldump -h localhost -uroot -pmypassword -r /Applications/MAMP/dolibarr_documents/admin/backup/mysqldump_dolimed_auto_`date +%d`.sql dolimed >/Applications/MAMP/dolibarr_logs/mysqldump_dolimed.log 2>&1

# This is a command to synchronize to another directory
rsync -a --delete --stats /Applications/MAMP/dolibarr* /Volumes/SCM34_OSX/Backups_Dolimed >/Applications/MAMP/dolibarr_logs/backup_dolimed.log 2>&1
# rsync -a --stats rhumato@apollon1.nltechno.com:/home/rhumato/wwwroot/dolibarr/* /Applications/MAMP/dolibarr 

# Put this into your cron
# 0 22 * * *  Applications/MAMP/backup.sh
