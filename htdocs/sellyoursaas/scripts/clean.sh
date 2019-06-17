#!/bin/bash
# Purge data.
# This script can be run on master or agent servers.
#
# Put the following entry into your root cron
#40 4 4 * * /home/admin/wwwroot/dolibarr_nltechno/htdocs/sellyoursaas/scripts/clean.sh confirm

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
export archivedir="/mnt/diskbackup/archives-test"
export archivedirbind="/etc/bind/archives"
export archivedircron="/var/spool/cron/crontabs.disabled"
export ZONES_PATH="/etc/bind/zones"

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

export DOMAIN=`grep '^domain=' /etc/sellyoursaas.conf | cut -d '=' -f 2`
export SUBDOMAIN=`grep 'subdomain=' /etc/sellyoursaas.conf | cut -d '=' -f 2`
export databasehost=`grep 'databasehost=' /etc/sellyoursaas.conf | cut -d '=' -f 2`
export database=`grep 'database=' /etc/sellyoursaas.conf | cut -d '=' -f 2`
export databaseuser=`grep 'databaseuser=' /etc/sellyoursaas.conf | cut -d '=' -f 2`

if [ "x$DOMAIN" == "x" ]; then
   echo "Failed to find the DOMAIN by reading entry 'domain=' into file /etc/sellyoursaas.conf" 1>&2
   exit 1
fi

export ZONENOHOST="with.$DOMAIN" 
export ZONE="with.$DOMAIN.hosts" 

if [ "x$database" == "x" ]; then
    echo "Failed to find the DATABASE by reading entry 'database=' into file /etc/sellyoursaas.conf" 1>&2
	echo "Usage: ${0} [test|confirm]"
	exit 1
fi
if [ "x$databasehost" == "x" ]; then
    echo "Failed to find the DATABASEHOST by reading entry 'databasehost=' into file /etc/sellyoursaas.conf" 1>&2
	echo "Usage: ${0} [test|confirm]"
	exit 1
fi
if [ "x$databaseuser" == "x" ]; then
    echo "Failed to find the DATABASEUSER by reading entry 'databaseuser=' into file /etc/sellyoursaas.conf" 1>&2
	echo "Usage: ${0} [test|confirm]"
	exit 1
fi

if [ "x$1" == "x" ]; then
	echo "Missing parameter - test|confirm" 1>&2
	echo "Usage: ${0} [test|confirm]"
	exit 1
fi

export testorconfirm=$1

# For debug
echo "database = $database"
echo "testorconfirm = $testorconfirm"
echo "DOMAIN = $DOMAIN"


MYSQL=`which mysql`
MYSQLDUMP=`which mysqldump`
echo "Search sellyoursaas database credential in /etc/sellyoursaas.conf"
passsellyoursaas=`grep 'databasepass=' /etc/sellyoursaas.conf | cut -d '=' -f 2`
if [[ "x$passsellyoursaas" == "x" ]]; then
	echo Failed to get password for mysql user sellyoursaas 
	exit 1
fi 

if [[ ! -d $archivedir ]]; then
	echo Failed to find archive directory $archivedir
	exit 1
fi

echo "***** Clean temporary files"
echo rm -f /tmp/instancefound*
rm -f /tmp/instancefound*
if [ -f /tmp/instancefound-dbinsellyoursaas ]; then
	echo Failed to delete file /tmp/instancefound-dbinsellyoursaas
	exit 1
fi
if [ -f /tmp/instancefound-activedbinsellyoursaas ]; then
	echo Failed to delete file /tmp/instancefound-activedbinsellyoursaas
	exit 1
fi
if [ -f /tmp/instancefound-dbinmysqldic ]; then
	echo Failed to delete file /tmp/instancefound-dbinmysqldic
	exit 1
fi
echo rm -f /tmp/osutoclean*
rm -f /tmp/osutoclean*
if [ -f /tmp/osutoclean ]; then
	echo Failed to delete file /tmp/osutoclean
	exit 1
fi
if [ -f /tmp/osutoclean-oldundeployed ]; then
	echo Failed to delete file /tmp/osutoclean-oldundeployed
	exit 1
fi
echo rm -f /tmp/osusernamefound*
rm -f /tmp/osusernamefound*
if [ -f /tmp/osusernamefound ]; then
	echo Failed to delete file /tmp/osusernamefound
	exit 1
fi



echo "***** Clean available virtualhost that are not enabled hosts (safe)"
for fic in `ls /etc/apache2/sellyoursaas-available/*.*.$DOMAIN.conf /etc/apache2/sellyoursaas-available/*.*.$DOMAIN.custom.conf /etc/apache2/sellyoursaas-available/*.home.lan 2>/dev/null`
do
	basfic=`basename $fic` 
	if [ ! -L /etc/apache2/sellyoursaas-online/$basfic ]; then
		echo Remove file with rm /etc/apache2/sellyoursaas-available/$basfic
		if [[ $testorconfirm == "confirm" ]]; then
			rm /etc/apache2/sellyoursaas-available/$basfic
		fi
	else
		echo "Site $basfic is enabled, we keep it"
	fi
done


echo "***** Get list of databases of all instances and save into /tmp/instancefound-dbinsellyoursaas-dbinsellyoursaas"

echo "#url=ref_customer	username_os	database_db status" > /tmp/instancefound-dbinsellyoursaas

Q1="use $database; "
Q2="SELECT c.ref_customer, ce.username_os, ce.database_db, ce.deployment_status FROM llx_contrat as c, llx_contrat_extrafields as ce WHERE ce.fk_object = c.rowid AND ce.deployment_status IS NOT NULL";
SQL="${Q1}${Q2}"

echo "$MYSQL -usellyoursaas -pxxxxxx -e '$SQL' | grep -v 'ref_customer'"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" | grep -v 'ref_customer' >> /tmp/instancefound-dbinsellyoursaas
if [ "x$?" != "x0" ]; then
	echo "Failed to make first SQL request to get instances. Exit 1."
	exit 1
fi


echo "***** Get list of databases of known active instances and save into /tmp/instancefound-activedbinsellyoursaas"

echo "#url=ref_customer	username_os	database_db status" > /tmp/instancefound-activedbinsellyoursaas

Q1="use $database; "
Q2="SELECT c.ref_customer, ce.username_os, ce.database_db, ce.deployment_status FROM llx_contrat as c, llx_contrat_extrafields as ce WHERE ce.fk_object = c.rowid AND ce.deployment_status IN ('processing','done')";
SQL="${Q1}${Q2}"

echo "$MYSQL -usellyoursaas -pxxxxxx -e '$SQL' | grep -v 'ref_customer'"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" | grep -v 'ref_customer' >> /tmp/instancefound-activedbinsellyoursaas
if [ "x$?" != "x0" ]; then
	echo "Failed to make second SQL request to get instances. Exit 1."
	exit 1
fi


echo "***** Get list of databases available in mysql and save into /tmp/instancefound-dbinmysqldic"

Q1="use mysql; "
Q2="SHOW DATABASES; ";
SQL="${Q1}${Q2}"

echo "$MYSQL -usellyoursaas -pxxxxxx -e '$SQL' | grep 'dbn' "
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" | grep 'dbn' | awk ' { print $1 } ' >> /tmp/instancefound-dbinmysqldic
if [ "x$?" != "x0" ]; then
	echo "Failed to make third SQL request to get instances. Exit 1."
	exit 1
fi



echo "***** Search osu unix account without home in $targetdir (should never happen)"
echo grep '^osu' /etc/passwd | cut -f 1 -d ':'
for osusername in `grep '^osu' /etc/passwd | cut -f 1 -d ':'`
do
	if [ ! -d $targetdir/$osusername ]; then
		echo User $osusername has no home. Should not happen.
		exit 12
	fi
done

echo "***** Search home in $targetdir without osu unix account (should never happen)"
echo "ls -d $targetdir/osu*";
for osusername in `ls -d $targetdir/osu* 2>/dev/null`
do
	export osusername=`basename $osusername`
	if ! grep "$osusername" /etc/passwd; then
		echo User $osusername is not inside /etc/passwd. Should not happen.
		exit 11
	fi
done

echo "***** Search from /tmp/instancefound-activedbinsellyoursaas of active databases (with known osusername) with a non existing unix user (should never happen)" 
while read bidon osusername dbname deploymentstatus; do 
	if [[ "x$osusername" != "xusername_os" && "x$osusername" != "xunknown" && "x$osusername" != "xNULL" && "x$dbname" != "xNULL" ]]; then
    	id $osusername >/dev/null 2>/dev/null
    	if [[ "x$?" == "x1" ]]; then
			echo Line $bidon $osusername $dbname $deploymentstatus is for a user that does not exists. Should not happen.
    		exit 10
    	fi
    fi
done < /tmp/instancefound-activedbinsellyoursaas


# We disable this because when we undeploy, user is kept and we want to remove it only 1 month after undeployment date (processed by next point)
# TODO For contracts deleted from database, we must found something else: 
#echo "***** Search from /tmp/instancefound: osu unix account with record in /etc/passwd but not in instancefound" 
#cat /tmp/instancefound | awk '{ if ($2 != "username_os" && $2 != "unknown" && $2 != "NULL") print $2":" }' > /tmp/osusernamefound
#if [ -s /tmp/osusernamefound ]; then
#	for osusername in `grep -v /etc/passwd -f /tmp/osusernamefound | grep '^osu'`
#	do
#		tmpvar1=`echo $osusername | awk -F ":" ' { print $1 } '`
#		echo User $tmpvar1 is an ^osu user in /etc/passwd but has no available instance in /tmp/instancefound
#		exit 9
#	done
#fi



echo "***** Save osu unix account with very old undeployed database into /tmp/osutoclean-oldundeployed and search entries with existing home dir and without dbn* subdir, and save into /tmp/osutoclean" 
Q1="use $database; "
Q2="SELECT ce.username_os FROM llx_contrat as c, llx_contrat_extrafields as ce WHERE c.rowid = ce.fk_object AND c.rowid IN ";
Q3=" (SELECT fk_contrat FROM llx_contratdet as cd, llx_contrat_extrafields as ce2 WHERE cd.fk_contrat = ce2.fk_object AND cd.STATUT = 5 AND ce2.deployment_status = 'undeployed' AND ce2.undeployment_date < ADDDATE(NOW(), INTERVAL -1 MONTH)); ";
SQL="${Q1}${Q2}${Q3}"

echo "$MYSQL -usellyoursaas -e $SQL"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" | grep '^osu' >> /tmp/osutoclean-oldundeployed
if [ -s /tmp/osutoclean-oldundeployed ]; then
	for osusername in `cat /tmp/osutoclean-oldundeployed`
	do
		tmpvar1=`echo $osusername | awk -F ":" ' { print $1 } '`
		if [ -d /home/jail/home/$osusername ]; then
			nbdbn=`ls /home/jail/home/$osusername/ | grep ^dbn | wc -w`
			if [[ "x$nbdbn" == "x0" ]]; then
				echo "User $tmpvar1 is an ^osu user in /tmp/osutoclean-oldundeployed but has still a home dir with no more dbn... into, so we will remove it"
				echo $tmpvar1 >> /tmp/osutoclean
			fi
		fi
	done
fi



echo "***** Loop on each user in /tmp/osutoclean to make a clean"
if [ -s /tmp/osutoclean ]; then

	export reloadapache=1
	
	cat /tmp/osutoclean | grep '^osu' | sort -u
	for osusername in `grep '^osu' /tmp/osutoclean | sort -u`
	do
		echo "***** Archive and delete qualified user $osusername found in /tmp/osutoclean"
		
		echo Try to find database and instance name from username $osusername
		export instancename=""
		export dbname=""
		export instancename=`grep $osusername /tmp/instancefound-dbinsellyoursaas | cut -f 1`
		export dbname=`grep $osusername /tmp/instancefound-dbinsellyoursaas | cut -f 3`
	
		echo For osusername=$osusername, dbname is $dbname, instancename is $instancename
		
		# If dbname is known
		if [[ "x$dbname" != "x" ]]; then	
			if [[ "x$dbname" != "xNULL" ]]; then	
				echo "Do a dump of database $dbname - may fails if already removed"
				mkdir -p $archivedir/$osusername
				echo "$MYSQLDUMP -usellyoursaas -pxxxxxx $dbname | bzip2 > $archivedir/$osusername/dump.$dbname.$now.sql.bz2"
				$MYSQLDUMP -usellyoursaas -p$passsellyoursaas $dbname | bzip2 > $archivedir/$osusername/dump.$dbname.$now.sql.bz2
	
				echo "Now drop the database"
				echo "echo 'DROP DATABASE $dbname;' | $MYSQL -usellyoursaas -p$passsellyoursaas $dbname"
				if [[ $testorconfirm == "confirm" ]]; then
					echo "DROP DATABASE $dbname;" | $MYSQL -usellyoursaas -p$passsellyoursaas $dbname
				fi	
			fi
		fi
		
		
		# If osusername is known, remove user and archive dir (Note: archive with clean.sh is always done in test)
		if [[ "x$osusername" != "x" ]]; then	
			if [[ "x$osusername" != "xNULL" ]]; then
				echo rm -f $targetdir/$osusername/$dbname/*.log
				rm -f $targetdir/$osusername/$dbname/*.log >/dev/null 2>&1 
				echo rm -f $targetdir/$osusername/$dbname/*.log.*
				rm -f $targetdir/$osusername/$dbname/*.log.* >/dev/null 2>&1 
				
				echo "clean $instancename" >> $archivedir/$osusername/clean-$instancename.txt
				
				echo deluser --remove-home --backup --backup-to $archivedir/$osusername $osusername
				if [[ $testorconfirm == "confirm" ]]; then
					deluser --remove-home --backup --backup-to $archivedir/$osusername $osusername
				fi
				
				echo deluser --group $osusername
				if [[ $testorconfirm == "confirm" ]]; then
					deluser --group $osusername
				fi
				
				# If dir still exists, we move it manually
				if [ -d "$targetdir/$osusername" ]; then
					echo The dir $targetdir/$osusername still exists when user does not exists anymore, we archive it manually
					echo mv -f $targetdir/$osusername $archivedir
					echo cp -pr $targetdir/$osusername $archivedir
					if [[ $testorconfirm == "confirm" ]]; then
						mv -f $targetdir/$osusername $archivedir 2>/dev/null
						cp -pr $targetdir/$osusername $archivedir
						rm -fr $targetdir/$osusername
						chown -R root $archivedir/$osusername
					fi
				fi
			fi
		fi
		
	
		# If instance name known
		if [ "x$instancename" != "x" ]; then
			if [ "x$instancename" != "xNULL" ]; then
			
				echo "   ** Remove DNS entry for $instancename from ${ZONE}"
				cat /etc/bind/${ZONE} | grep "^$instancename '" > /dev/null 2>&1
				notfound=$?
				echo notfound=$notfound
				
				if [[ $notfound == 0 ]]; then
		
					echo "cat /etc/bind/${ZONE} | grep -v '^$instancename ' > /tmp/${ZONE}.$PID"
					cat /etc/bind/${ZONE} | grep -v "^$instancename " > /tmp/${ZONE}.$PID
				
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
				      if [ "$DATE" -eq "$prefix" ]; then 	# same day
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
					named-checkzone ${ZONENOHOST} /tmp/${ZONE}.$PID
					if [[ "$?x" != "0x" ]]; then
						echo Error when editing the DNS file during clean.sh. File /tmp/${ZONE}.$PID is not valid 
						exit 1
					fi 
					
					echo "   ** Archive file with cp /etc/bind/${ZONE} /etc/bind/archives/${ZONE}-$now"
					cp /etc/bind/${ZONE} /etc/bind/archives/${ZONE}-$now
					
					echo "   ** Move new host file"
					echo mv -fu /tmp/${ZONE}.$PID /etc/bind/${ZONE}
					if [[ $testorconfirm == "confirm" ]]; then
						mv -fu /tmp/${ZONE}.$PID /etc/bind/${ZONE}
					fi
					
					echo "   ** Reload dns with rndc reload ${ZONENOHOST}"
					if [[ $testorconfirm == "confirm" ]]; then
						rndc reload ${ZONENOHOST}
						#/etc/init.d/bind9 reload
					fi
				fi
				
	
				apacheconf=/etc/apache2/sellyoursaas-online/$instancename.conf
				
				if [ -f $apacheconf ]; then
					echo "   ** Disable apache conf with rm"
					echo rm /etc/apache2/sellyoursaas-online/$instancename.conf
					echo rm /etc/apache2/sellyoursaas-online/$instancename.custom.conf
					if [[ $testorconfirm == "confirm" ]]; then
						rm /etc/apache2/sellyoursaas-online/$instancename.conf
						rm /etc/apache2/sellyoursaas-online/$instancename.custom.conf
					fi
				fi
	
				echo "   ** Remove apache conf /etc/apache2/sellyoursaas-available/$instancename.conf"
				if [[ -f /etc/apache2/sellyoursaas-available/$instancename.conf ]]; then
					echo rm /etc/apache2/sellyoursaas-available/$instancename.conf
					if [[ $testorconfirm == "confirm" ]]; then
						rm /etc/apache2/sellyoursaas-available/$instancename.conf
					fi
				else
					echo File /etc/apache2/sellyoursaas-available/$instancename.conf already deleted
				fi
				echo "   ** Remove apache conf /etc/apache2/sellyoursaas-online/$instancename.custom.conf"
				if [[ -f /etc/apache2/sellyoursaas-online/$instancename.custom.conf ]]; then
					echo rm /etc/apache2/sellyoursaas-online/$instancename.custom.conf
					if [[ $testorconfirm == "confirm" ]]; then
						rm /etc/apache2/sellyoursaas-online/$instancename.custom.conf
					fi
				else
					echo File /etc/apache2/sellyoursaas-available/$instancename.custom.conf already deleted
				fi
			
				/usr/sbin/apache2ctl configtest
				if [[ "x$?" != "x0" ]]; then
					echo Error when running apache2ctl configtest 
				else 
					echo "   ** Apache tasks finished with configtest ok"
				fi
			fi
		fi
		
	done

	# Restart apache
	echo service apache2 reload
	if [[ "x$reloadapache" == "x1" ]]; then
		if [[ $testorconfirm == "confirm" ]]; then
			service apache2 reload
			if [[ "x$?" != "x0" ]]; then
				echo "Error when running service apache2 reload. Exit 3"
				exit 3
			fi
		fi
	else
		echo "An error was found with apache2ctl configtest so no service apache2 reload was done. Exit 2"
		exit 2
	fi
fi

# Now clean also old dir in archives-test
echo "Now clean also old dir in $archivedir - 15 days after being archived"
cd $archivedir
find $archivedir -maxdepth 1 -type d -mtime +15 -exec rm -fr {} \;

# Now clean also old files in $archivedirbind
echo "Now clean also old files in $archivedirbind - 15 days after being archived"
cd $archivedirbind
find $archivedirbind -maxdepth 1 -type f -mtime +15 -exec rm -f {} \;

# Now clean also old files in $archivedircron
echo "Now clean also old files in $archivedircron - 15 days after being archived"
cd $archivedircron
find $archivedircron -maxdepth 1 -type f -mtime +15 -exec rm -f {} \;

# Clean database users
echo "We should also clean mysql record for permission on old databases and old users"
SQL="use mysql; delete from db where Db NOT IN (SELECT schema_name FROM information_schema.schemata) and Db like 'dbn%';"
echo "$MYSQL -usellyoursaas -pxxxxxx -e '$SQL'"
#$MYSQL -usellyoursaas -pxxxxxx -e "$SQL"
SQL="use mysql; delete from user where User NOT IN (SELECT User from db) and User like 'dbu%';"
echo "$MYSQL -usellyoursaas -pxxxxxx -e '$SQL'"
#$MYSQL -usellyoursaas -pxxxxxx -e "$SQL"

exit 0
