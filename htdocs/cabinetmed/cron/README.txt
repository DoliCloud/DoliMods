#!/bin/sh
# This is an example of command to put into a cron script to launch automatic 
# database dump and automatic backup of dolibarr directories into another disk.
# We assume that database is called 'dolimed'

# This is example of command to connect to mysql through socket (MAC OS MAMP)
mysql -u root -p'mypassword' -h localhost -P 8889 --socket=/Applications/MAMP/tmp/mysql/mysql.sock
# Same using TCP
mysql -u root -p'mypassword' -h localhost -P 8889 -h 127.0.0.1

# This is a command to synchronize to another directory
rsync -a --delete --stats /Applications/MAMP/dolibarr* /Volumes/SCM34_OSX/Backups_Dolimed >/Applications/MAMP/dolibarr_logs/backup_dolimed.log 2>&1

# This is example of command to synchronize with other servers
# rsync -a --stats --exclude conf.php --exclude htdocs/index.html backup_user@apollon1.nltechno.com:/home/rhumato/wwwroot/dolibarr/* /Applications/MAMP/dolibarr 
# rsync -a --stats /Applications/MAMP/dolibarr backup_user@apollon1.nltechno.com:/home/rhumato/backup_scm
# rsync -a --stats /Applications/MAMP/dolibarrmodsf backup_user@apollon1.nltechno.com:/home/rhumato/backup_scm
# rsync -a --stats /Applications/MAMP/dolibarr_documents backup_user@apollon1.nltechno.com:/home/rhumato/backup_scm

# This is an example of command to make a mysql dump into a cron
# 0 23 * * * /Applications/MAMP/Library/bin/mysqldump -h localhost -uroot -pmypassword -l --single-transaction -K --add-drop-table=TRUE --tables -c -e --hex-blob --default-character-set=utf8 -r /Applications/MAMP/dolibarr_documents/admin/backup/mysqldump_dolimed_auto_`date +%d`.sql dolimed >/Applications/MAMP/dolibarr_logs/mysqldump_dolimed.log 2>&1
# This is an example of command to have backup done daily from your cron
# 0 22 * * * /Applications/MAMP/dolibarr_nltechno/htdocs/cabinetmed/cron/backup.sh >/Applications/MAMP/dolibarr_logs/backup.log 2>&1


