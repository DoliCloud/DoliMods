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
   exit 1
fi

if [ "x$1" == "x" ]; then
	echo "Missing parameter 1 - sellyoursaas admin databasename" 1>&2
	echo "Usage: ${0} [databasename] [test|confirm]"
fi
if [ "x$2" == "x" ]; then
	echo "Missing parameter 2 - test|confirm" 1>&2
fi
if [[ "x$1" == "x" || "x$2" == "x" ]]; then
	exit
fi

export database=$1
export testorconfirm=$2

# For debug
echo "database = $database"
echo "testorconfirm = $testorconfirm"


MYSQL=`which mysql`
MYSQLDUMP=`which mysqldump`
echo "Search sellyoursaas database credential in /root/sellyoursaas"
passsellyoursaas=`cat /root/sellyoursaas`		# First seach into root
#echo $passsellyoursaas
if [[ "x$passsellyoursaas" == "x" ]]; then
	echo Search sellyoursaas credential 2
	passsellyoursaas=`cat /tmp/sellyoursaas`	# Then search into /tmp
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
echo rm -f /tmp/osusernamefound
rm -f /tmp/osusernamefound
if [ -f /tmp/osusernamefound ]; then
	echo Failed to delete file /tmp/osusernamefound
	exit 1
fi



echo "***** Clean available virtualhost that are not enabled hosts"
for fic in `ls /etc/apache2/sites-available/*.*.dolicloud.com.conf 2>&1`
do
	basfic=`basename $fic` 
	if [ ! -L /etc/apache2/sites-enabled/$basfic ]; then
		echo Remove file $basfic
		if [[ $testorconfirm == "confirm" ]]; then
			rm /etc/apache2/sites-available/$basfic
		fi
	else
		echo "Site $basfic is enabled, we keep it"
	fi
done



echo "***** Get list of databases of known active instances and save into /tmp/instancefound"

echo "#url=ref_customer	username_os	database_db" > /tmp/instancefound

Q1="use $database; "
Q2="SELECT c.ref_customer, ce.username_os, ce.database_db, ce.deployment_status FROM llx_contrat as c, llx_contrat_extrafields as ce WHERE ce.fk_object = c.rowid AND ce.deployment_status IN ('processing','done')";
SQL="${Q1}${Q2}"

echo "$MYSQL -usellyoursaas -pxxxxxx -e '$SQL' | grep -v 'ref_customer'"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" | grep -v 'ref_customer' >> /tmp/instancefound
if [ "x$?" != "x0" ]; then
	echo "Failed to make first SQL request to get instances. Exit 1."
	exit 1
fi

Q1="use mysql; "
Q2="SHOW DATABASES; ";
SQL="${Q1}${Q2}"

echo "$MYSQL -usellyoursaas -pxxxxxx -e '$SQL' | grep 'dbn' "
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" | grep 'dbn' | awk ' { print "NULL unknown "$1" unknown" } ' >> /tmp/instancefound
if [ "x$?" != "x0" ]; then
	echo "Failed to make second SQL request to get instances. Exit 1."
	exit 1
fi


echo "***** Search osu unix account without home in $targetdir"
echo grep '^osu' /etc/passwd | cut -f 1 -d ':'
for osusername in `grep '^osu' /etc/passwd | cut -f 1 -d ':'`
do
	if [ ! -d $targetdir/$osusername ]; then
		echo User $osusername has no home
		echo $osusername >> /tmp/osutoclean
	else
		echo User $osusername has a home $targetdir/$osusername
	fi
done

echo "***** Search home in $targetdir without osu unix account and save into /tmp/osutoclean"
echo "ls -d $targetdir/osu*";
for osusername in `ls -d $targetdir/osu* 2>/dev/null`
do
	export osusername=`basename $osusername`
	if ! grep "$osusername" /etc/passwd; then
		echo User $osusername is not inside /etc/passwd, we add it to users to clean
		echo $osusername >> /tmp/osutoclean
	else
		echo $osusername is inside /etc/passwd
	fi
done

echo "***** Save osu unix account with very old undeployed database into /tmp/osutoclean" 
Q1="use $database; "
Q2="SELECT ce.username_os FROM llx_contrat as c, llx_contrat_extrafields as ce WHERE c.rowid = ce.fk_object AND c.rowid IN ";
Q3=" (SELECT fk_contrat FROM llx_contratdet as cd, llx_contrat_extrafields as ce2 WHERE cd.fk_contrat = ce2.fk_object AND cd.STATUT = 5 AND ce2.deployment_status = 'undeployed' AND ce2.undeployment_date < ADDDATE(NOW(), INTERVAL -2 MONTH)); ";
SQL="${Q1}${Q2}${Q3}"

echo "$MYSQL -usellyoursaas -e $SQL"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL" >> /tmp/osutoclean


echo "***** Search from /tmp/instancefound: osu unix account without record in /etc/passwd" 
cat /tmp/instancefound | awk '{ if ($2 != "username_os" && $2 != "NULL") print $2":" }' > /tmp/osusernamefound
if [ -s /tmp/osusernamefound ]; then
	for osusername in `grep -v /etc/passwd -f /tmp/osusernamefound | grep '^osu'`
	do
		tmpvar1=`echo $osusername | awk -F ":" ' { print $1 } '`
		echo User $tmpvar1 is an ^osu user in /etc/passwd but has no available instance in /tmp/instancefound
		echo $tmpvar1 >> /tmp/osutoclean
	done
fi


echo "***** Search from /tmp/instancefound: databases without unix users and archive/drop them" 
while read bidon osusername dbname deploymentstatus; do 
	if [[ "x$osusername" != "xusername_os" && "x$osusername" != "xNULL" && "x$dbname" != "xNULL" ]]; then
    	id $osusername >/dev/null 2>/dev/null
    	if [[ "x$?" == "x1" ]]; then
    		echo Line $bidon $osusername $dbname is for a user that does not exists
    		
			echo "Do a dump of database $dbname - may fails if already removed"
			mkdir -p $archivedir/$osusername
			echo "$MYSQLDUMP -usellyoursaas -p$passsellyoursaas $dbname > $archivedir/$osusername/dump.$dbname.$now.sql"
			$MYSQLDUMP -usellyoursaas -p$passsellyoursaas $dbname > $archivedir/$osusername/dump.$dbname.$now.sql

			echo "Now drop the database"
			echo "echo 'DROP DATABASE $dbname;' | $MYSQL -usellyoursaas -p$passsellyoursaas $dbname"
			if [[ $testorconfirm == "confirm" ]]; then
				echo "DROP DATABASE $dbname;" | $MYSQL -usellyoursaas -p$passsellyoursaas $dbname
			fi	
    	
    	fi
    
    fi
done < /tmp/instancefound


echo "***** Loop on each user in /tmp/osutoclean to make a clean"
cat /tmp/osutoclean | grep '^osu' | sort -u
for osusername in `grep '^osu' /tmp/osutoclean | sort -u`
do
	echo "   ** Archive and delete qualified user $osusername found in /tmp/osutoclean"
	
	echo Try to find database and instance name from username
	export instancename=""
	export dbname=""
	export instancename=`grep $osusername /tmp/instancefound | cut -f 1`
	export dbname=`grep $osusername /tmp/instancefound | cut -f 3`

	echo For osusername=$osusername, dbname is $dbname, instancename is $instancename
	
	# If dbname is known
	if [[ "x$dbname" != "x" ]]; then	
		if [[ "x$dbname" != "xNULL" ]]; then	
			echo "Do a dump of database $dbname - may fails if already removed"
			mkdir -p $archivedir/$osusername
			echo "$MYSQLDUMP -usellyoursaas -p$passsellyoursaas $dbname > $archivedir/$osusername/dump.$dbname.$now.sql"
			$MYSQLDUMP -usellyoursaas -p$passsellyoursaas $dbname > $archivedir/$osusername/dump.$dbname.$now.sql

			echo "Now drop the database"
			echo "echo 'DROP DATABASE $dbname;' | $MYSQL -usellyoursaas -p$passsellyoursaas $dbname"
			if [[ $testorconfirm == "confirm" ]]; then
				echo 'DROP DATABASE $dbname;' | $MYSQL -usellyoursaas -p$passsellyoursaas $dbname
			fi	
		fi
	fi
	
	
	# If osusername is known, remove user and archive dir
	if [[ "x$osusername" != "x" ]]; then	
		if [[ "x$osusername" != "xNULL" ]]; then	
			echo deluser --remove-home --backup --backup-to $archivedir $osusername
			if [[ $testorconfirm == "confirm" ]]; then
				deluser --remove-home --backup --backup-to $archivedir $osusername
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
				named-checkzone with.dolicloud.com /tmp/${ZONE}.$PID
				if [[ "$?x" != "0x" ]]; then
					echo Error when editing the DNS file during clean.sh. File /tmp/${ZONE}.$PID is not valid 
					exit 1
				fi 
				
				echo "   ** Archive file with cp /etc/bind/${ZONE} /etc/bind/archives/${ZONE}-$now"
				cp /etc/bind/with.dolicloud.com.hosts /etc/bind/archives/${ZONE}-$now
				
				echo "   ** Move new host file"
				echo mv -fu /tmp/${ZONE}.$PID /etc/bind/${ZONE}
				if [[ $testorconfirm == "confirm" ]]; then
					mv -fu /tmp/${ZONE}.$PID /etc/bind/${ZONE}
				fi
				
				echo "   ** Reload dns"
				if [[ $testorconfirm == "confirm" ]]; then
					rndc reload with.dolicloud.com
					#/etc/init.d/bind9 reload
				fi
			fi
			

			apacheconf=/etc/apache2/sites-enabled/$instancename.conf
			
			if [ -f $apacheconf ]; then
				# Remove apache virtual host
				echo "   ** Disable apache conf with a2dissite $instancename"
				echo a2dissite $instancename
				if [[ $testorconfirm == "confirm" ]]; then
					a2dissite $instancename
				fi
			fi
				
			echo "   ** Remove apache conf /etc/apache2/sites-available/$instancename"
			if [[ -f /etc/apache2/sites-available/$instancename ]]; then
				if [[ $testorconfirm == "confirm" ]]; then
					echo rm /etc/apache2/sites-available/$instancename
					rm /etc/apache2/sites-available/$instancename
				fi
			else
				echo File /etc/apache2/sites-available/$instancename already disabled
			fi
		
			/usr/sbin/apache2ctl configtest
			if [[ "x$?" != "x0" ]]; then
				echo Error when running apache2ctl configtest 
			else 
				echo "   ** Apache tasks finished with configtest ok, we reload apache."
				echo service apache2 reload
				if [[ $testorconfirm == "confirm" ]]; then
					service apache2 reload
					if [[ "x$?" != "x0" ]]; then
						echo Error when running service apache2 reload 
						exit 2
					fi
				fi 
			fi
		fi
	fi
	
	
done

