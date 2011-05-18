# This is an example of cron script to launch automatic database dump
# and automatic backup of dolibarr directories into another disk
# We assume that database is called 'dolimed'
0 21 * * * /Applications/MAMP/Library/bin/mysqldump -h localhost -uroot -pmypassword dolimed > /Applications/MAMP/dolibarr_documents/admin/backup/mysqldump_dolimed_auto.sql
0 22 * * * rsync -a --delete --stats /Applications/MAMP/dolibarr* /Volumes/SCM34_OSX/Backups_Dolimed >/Applications/MAMP/dolibarr_logs/backup_dolimed.log 2>&1
