#!/bin/bash

# Purge data

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
export targetdir="/home/jail/home"				
export archivedir="/home/archives"

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   #exit 1
fi

if [ "x$1" == "x" ]; then
	echo "Missing parameter 1 - test|confirm" 1>&2
	exit 1
fi

export testorconfirm=$1

# For debug
echo "testorconfirm = $testorconfirm"


MYSQL=`which mysql`
echo "Search sellyoursaas credential
passsellyoursaas=`cat /root/sellyoursaas`
echo $passsellyoursaas
if [[ "x$passsellyoursaas" == "x" ]]; then
	echo Search sellyoursaas credential 2
	passsellyoursaas=`cat /tmp/sellyoursaas`
	if [[ "x$passsellyoursaas" == "x" ]]; then
		echo Failed to get password for mysql user sellyoursaas 
		exit 1
	fi
fi 

if [[ ! -d $archivedir ]]; then
	echo Failed to find archive directory $archivedir
	exit 1
fi

echo "***** Clean temporary files"
rm -f /tmp/dbnfound
if [ -f /tmp/dbnfound ]; then
	echo Failed to delete file /tmp/dbnfound
	exit 1
fi
rm -f /tmp/osutoclean
if [ -f /tmp/osutoclean ]; then
	echo Failed to delete file /tmp/osutoclean
	exit 1
fi


#echo "***** Get list of databases"
#SQL="show databases; "
#echo "$MYSQL -usellyoursaas -e $SQL"
#$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" > /tmp/dbnfound

echo "***** Search unix account osu without home" 
for osusername in `grep '^osu' /etc/passwd`
do
	if [ -d $targetdir/$osusername ]; then
		echo $osusername >> /tmp/osutoclean
	fi
done

echo "***** Search unix account osu with undeployed database" 
echo Process $osusername
Q1="use dolibarr_nltechno; "
Q2="SELECT ce.username_os FROM llx_contrat as c, llx_contrat_extrafield as ce WHERE c.rowid = ce.fk_object AND c.rowid IN ";
Q3=" (SELECT fk_contrat FROM llx_contratdet as cd, llx_contrat_extrafields as ce2 WHERE cd.fk_contrat = ce2.fk_object AND cd.STATUT = 5 AND ce2.deployment_status = 'undeployed' AND ce2.undeployment_date < ADDDATE(NOW(), INTERVAL -2 MONTH)); ";
SQL="${Q1}${Q2}${Q3}${Q4}"

echo "$MYSQL -usellyoursaas -e $SQL"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" >> /tmp/osutoclean

echo "***** Archive and delete users"
for osusername in `grep '^osu' /etc/osutoclean`
do
	echo deluser --remove-home --backup --backup-to $archivedir $osusername
	if [[ $testorconfirm == "confirm" ]]
	then
		deluser --remove-home --backup --backup-to $archivedir $osusername
	fi
	
	deluser --group $osusername
	if [[ $testorconfirm == "confirm" ]]
	then
		deluser --group $osusername
	fi
done

