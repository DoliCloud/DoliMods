#!/bin/bash
# Purge data
#
# Put the following entry into your root cron
#40 4 4 * * /home/admin/wwwroot/dolibarr_nltechno/htdocs/sellyoursaas/scripts/clean.sh databasename confirm

#set -e

export now=`date +%Y%m%d%H%M%S`

echo
echo "**** ${0}"
echo "${0} ${@}"
echo "# user id --------> $(id -u)"
echo "# now ------------> $now"
echo "# PID ------------> ${$}"
echo "# PWD ------------> $PWD" 
echo "# arguments ------> ${@}"
echo "# path to me -----> ${0}"
echo "# parent path ----> ${0%/*}"
echo "# my name --------> ${0##*/}"
echo "# realname -------> $(realpath ${0})"
echo "# realname name --> $(basename $(realpath ${0}))"
echo "# realname dir ---> $(dirname $(realpath ${0}))"

export PID=${$}
export scriptdir=$(dirname $(realpath ${0}))
export targetdir="/home/admin/backup/mysql"				

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

if [ "x$1" == "x" ]; then
	echo "Missing parameter 1 - test|confirm" 1>&2
	echo "Usage: ${0} [test|confirm]"
fi
if [[ "x$1" == "x" ]]; then
	exit 1
fi

export testorconfirm=$1

# For debug
echo "testorconfirm = $testorconfirm"


MYSQLDUMP=`which mysqldump`

if [[ ! -d $targetdir ]]; then
	echo Failed to find archive directory $targetdir
	exit 1
fi

echo "Do a dump of database $dbname"
export dbname="mysql" 
echo "$MYSQLDUMP $dbname | bzip2 > $targetdir/$dbname.sql.bz2"
$MYSQLDUMP $dbname | bzip2 > $targetdir/$dbname.sql.bz2
chown root.admin $targetdir/$dbname.sql.bz2
chmod o-rwx $targetdir/$dbname.sql.bz2

export dbname="dolibarr" 
echo "$MYSQLDUMP $dbname | bzip2 > $targetdir/$dbname.sql.bz2"
$MYSQLDUMP $dbname | bzip2 > $targetdir/$dbname.sql.bz2
chown root.admin $targetdir/$dbname.sql.bz2
chmod o-rwx $targetdir/$dbname.sql.bz2

export dbname="nltechno_dolibarr" 
echo "$MYSQLDUMP $dbname | bzip2 > $targetdir/$dbname.sql.bz2"
$MYSQLDUMP $dbname | bzip2 > $targetdir/$dbname.sql.bz2
chown root.admin $targetdir/$dbname.sql.bz2
chmod o-rwx $targetdir/$dbname.sql.bz2

exit 0
