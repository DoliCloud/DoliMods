#!/bin/bash
# Purge data

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
export targetdir="/home/jail/home"				
export archivedir="/home/archives"
export ZONES_PATH="/etc/bind/zones"
export ZONE="with.dolicloud.com.hosts" 

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   #exit 1
fi

if [ "x$1" == "x" ]; then
	echo "Missing parameter 1 - databasename" 1>&2
	exit 1
fi
if [ "x$2" == "x" ]; then
	echo "Missing parameter 2 - test|confirm" 1>&2
	exit 1
fi

export database=$1
export testorconfirm=$2

# For debug
echo "database = $database"
echo "testorconfirm = $testorconfirm"


MYSQL=`which mysql`
MYSQLDUMP=`which mysqldump`
echo "Search sellyoursaas credential"
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
echo rm -f /tmp/instancefound
rm -f /tmp/instancefound
if [ -f /tmp/instancefound ]; then
	echo Failed to delete file /tmp/instancefound
	exit 1
fi
echo rm -f /tmp/osutoclean
rm -f /tmp/osutoclean
if [ -f /tmp/osutoclean ]; then
	echo Failed to delete file /tmp/osutoclean
	exit 1
fi


echo "***** Get list of databases"
Q1="use $database; "
Q2="SELECT c.ref_customer, ce.username_os, ce.database_db FROM llx_contrat as c, llx_contrat_extrafields as ce WHERE ce.fk_object = c.rowid";
SQL="${Q1}${Q2}"

echo "$MYSQL -usellyoursaas -e $SQL"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" | grep -v "ref_customer" >> /tmp/instancefound

echo "***** Search osu unix account without home in $targetdir"
echo grep '^osu' /etc/passwd | cut -f 1 -d ':'
for osusername in `grep '^osu' /etc/passwd | cut -f 1 -d ':'`
do
	if [ -d $targetdir/$osusername ]; then
		echo User $osusername has no home
		echo $osusername >> /tmp/osutoclean
	else
		echo $targetdir/$osusername exits
	fi
done

echo "***** Search home in $targetdir without osu unix account"
echo grep "$osusername" /etc/passwd;
for osusername in `ls -d $targetdir/osu*`
do
	export osusername=`basename $osusername`
	if ! grep "$osusername" /etc/passwd; then
		echo User $osusername is not inside /etc/passwd
		echo $osusername >> /tmp/osutoclean
	else
		echo $osusername is inside /etc/passwd
	fi
done

echo "***** Search osu unix account with undeployed database" 
Q1="use $database; "
Q2="SELECT ce.username_os FROM llx_contrat as c, llx_contrat_extrafields as ce WHERE c.rowid = ce.fk_object AND c.rowid IN ";
Q3=" (SELECT fk_contrat FROM llx_contratdet as cd, llx_contrat_extrafields as ce2 WHERE cd.fk_contrat = ce2.fk_object AND cd.STATUT = 5 AND ce2.deployment_status = 'undeployed' AND ce2.undeployment_date < ADDDATE(NOW(), INTERVAL -2 MONTH)); ";
SQL="${Q1}${Q2}${Q3}"

echo "$MYSQL -usellyoursaas -e $SQL"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" >> /tmp/osutoclean


for osusername in `grep '^osu' /tmp/osutoclean`
do
	echo "***** Archive and delete qualified user $osusername"
	
	echo Try to find database and instance name from username
	export dbname=""
	export instancename=""
	export dbname=`grep $osusername /tmp/instancefound | cut -f 2`  	
	export instancename=`grep $osusername /tmp/instancefound | cut -f 3`  	

	echo For osusername=$osusername, dbname is $dbname, instancename is $instancename
	if [[ "x$dbname" != "x" ]]; then	
		echo "Do a dump of database $dbname - may fails if already removed"
		mkdir -p $targetdir/$osusername
		echo "$MYSQLDUMP -usellyoursaas -p$passsellyoursaas $dbname > $targetdir/$osusername/dump.$now.sql"
		$MYSQLDUMP -usellyoursaas -p$passsellyoursaas $dbname > $targetdir/$osusername/dump.$now.sql
	fi
	
	echo deluser --remove-home --backup --backup-to $archivedir $osusername
	if [[ $testorconfirm == "confirm" ]]
	then
		deluser --remove-home --backup --backup-to $archivedir $osusername
	fi
	
	echo deluser --group $osusername
	if [[ $testorconfirm == "confirm" ]]
	then
		deluser --group $osusername
	fi
	
	# If dir still exists, we move it manually
	if [ -d $targetdir/$osusername ]; then
		echo The dir $targetdir/$osusername still exists when user does not exists anymore, we archive it manually
		echo mv -f $targetdir/$osusername $archivedir/$osusername
		if [[ $testorconfirm == "confirm" ]]
		then
			mv -f $targetdir/$osusername $archivedir/$osusername
		fi
	fi


	echo "***** Remove DNS entry for $instancename in $domainname"
	echo "cat /etc/bind/${ZONE} | grep -v '^$instancename ' > /tmp/${ZONE}.$PID"
	cat /etc/bind/${ZONE} | grep -v '^$instancename ' > /tmp/${ZONE}.$PID

	# we're looking line containing this comment
	export DATE=`date +%y%m%d%H`
	export NEEDLE="serial number"
    curr=$(/bin/grep -e "${NEEDLE}$" /tmp/${ZONE}.$PID | /bin/sed -n "s/^\s*\([0-9]*\)\s*;\s*${NEEDLE}\s*/\1/p")
    # replace if current date is shorter (possibly using different format)
    echo "Current bind counter is $curr"
    if [ ${#curr} -lt ${#DATE} ]; then
      serial="${DATE}00"
    else
      prefix=${curr::-2}
      if [ "$DATE" -eq "$prefix" ]; then # same day
        num=${curr: -2} # last two digits from serial number
        num=$((10#$num + 1)) # force decimal representation, increment
        serial="${DATE}$(printf '%02d' $num )" # format for 2 digits
      else
        serial="${DATE}00" # just update date
      fi
    fi
    echo Replace serial in /tmp/${ZONE}.$PID with ${serial}
    /bin/sed -i -e "s/^\(\s*\)[0-9]\{0,\}\(\s*;\s*${NEEDLE}\)$/\1${serial}\2/" /tmp/${ZONE}.$PID
    
    echo Test temporary file /tmp/${ZONE}.$PID
	named-checkzone with.dolicloud.com /tmp/${ZONE}.$PID
	if [[ "$?x" != "0x" ]]; then
		echo Error when editing the DNS file. File /tmp/${ZONE}.$PID is not valid 
		exit 1
	fi 
	
	echo "**** Archive file with cp /etc/bind/${ZONE} /etc/bind/archives/${ZONE}-$now"
	cp /etc/bind/with.dolicloud.com.hosts /etc/bind/archives/${ZONE}-$now
	
	echo "**** Move new host file"
	mv -fu /tmp/${ZONE}.$PID /etc/bind/${ZONE}
	
	echo "**** Reload dns"
	rndc reload with.dolicloud.com
	/etc/init.d/bind9 reload

	# Remove apache virtual host
	echo "***** Disable apache conf with a2dissite $instancename.with.dolicloud.conf"
	a2dissite $instancename.with.dolicloud.conf
	
	echo "***** Remove apache conf /etc/apache2/sites-available/$instancename.with.dolicloud.conf"
	if [[ -f $apacheconf ]]
	then
		rm /etc/apache2/sites-available/$instancename.with.dolicloud.conf
	else
		echo File /etc/apache2/sites-available/$instancename.with.dolicloud.conf already disabled
	fi

	/usr/sbin/apache2ctl configtest
	if [[ "x$?" != "x0" ]]; then
		echo Error when running apache2ctl configtest 
		#exit 1
	fi 

	echo "***** Apache tasks finished. service apache2 reload"
	service apache2 reload
	if [[ "x$?" != "x0" ]]; then
		echo Error when running service apache2 reload 
		exit 2
	fi 

done

