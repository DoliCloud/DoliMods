#!/bin/bash
#
# To use this script with remote ssh (not required when using the remote agent):
# Create a symbolic link to this file .../create_deploy_undeploy.sh into /usr/bin
# Grant adequate permissions (550 mean root and group www-data can read and execute, nobody can write)
# sudo chown root:www-data /usr/bin/create_deploy_undeploy.sh
# sudo chmod 550 /usr/bin/create_deploy_undeploy.sh
# And allow apache to sudo on this script by doing visudo to add line:
#www-data        ALL=(ALL) NOPASSWD: /usr/bin/create_deploy_undeploy.sh
#
# deployall   create user and instance
# deploy      create only instance
# undeployall remove user and instance
# undeploy    remove instance (must be easy to restore)


export now=`date +%Y%m%d%H%M%S`

echo
echo
echo "####################################### ${0} ${1}"
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
export targetdir="/home/jail/home"				
export archivedir="/home/archives"
export ZONES_PATH="/etc/bind/zones"
export ZONE="with.dolicloud.com.hosts" 
export scriptdir=$(dirname $(realpath ${0}))
export vhostfile="$scriptdir/templates/vhostHttps-sellyoursaas.template"


if [ "$(id -u)" != "0" ]; then
	echo "This script must be run as root" 1>&2
	exit 1
fi

if [ "x$1" == "x" ]; then
	echo "Missing parameter 1 - mode (deploy|deployall|undeploy|undeployall)" 1>&2
	exit 1
fi
if [ "x$2" == "x" ]; then
	echo "Missing parameter 2 - osusername" 1>&2
	exit 1
fi
if [ "x$3" == "x" ]; then
	echo "Missing parameter 3 - ospassword" 1>&2
	exit 1
fi
if [ "x$4" == "x" ]; then
	echo "Missing parameter 4 - instancename" 1>&2
	exit 1
fi
if [ "x$5" == "x" ]; then
	echo "Missing parameter 5 - domainname" 1>&2
	exit 1
fi
if [ "x$6" == "x" ]; then
	echo "Missing parameter 6 - dbname" 1>&2
	exit 1
fi
if [ "x$7" == "x" ]; then
	echo "Missing parameter 7 - dbport" 1>&2
	exit 1
fi
if [ "x$8" == "x" ]; then
	echo "Missing parameter 8 - dbusername" 1>&2
	exit 1
fi
if [ "x$9" == "x" ]; then
	echo "Missing parameter 9 - dbpassword" 1>&2
	exit 1
fi

export mode=$1
export osusername=$2
export ospassword=$3
export instancename=$4
export domainname=$5

export dbname=$6
export dbport=$7
export dbusername=$8
export dbpassword=$9

export fileforconfig1=${10}
export targetfileforconfig1=${11}
export dirwithdumpfile=${12}
export dirwithsources1=${13}
export targetdirwithsources1=${14}
export dirwithsources2=${15}
export targetdirwithsources2=${16}
export dirwithsources3=${17}
export targetdirwithsources3=${18}
export cronfile=${19}
export cliafter=${20}
export targetdir=${21}

export instancedir=$targetdir/$osusername/$dbname
export fqn=$instancename.$domainname

# For debug
echo "...input params..."
echo "mode = $mode"
echo "osusername = $osusername"
echo "ospassword = XXXXXX"
echo "instancename = $instancename"
echo "domainname = $domainname"
echo "dbname = $dbname"
echo "dbport = $dbport"
echo "dbusername = $dbusername"
echo "dbpassword = $dbpassword"
echo "fileforconfig1 = $fileforconfig1"
echo "targetfileforconfig1 = $targetfileforconfig1"
echo "dirwithdumpfile = $dirwithdumpfile"
echo "dirwithsources1 = $dirwithsources1"
echo "targetdirwithsources1 = $targetdirwithsources1"
echo "dirwithsources2 = $dirwithsources2"
echo "targetdirwithsources2 = $targetdirwithsources2"
echo "dirwithsources3 = $dirwithsources3"
echo "targetdirwithsources3 = $targetdirwithsources3"
echo "cronfile = $cronfile"
echo "cliafter = $cliafter"
echo "targetdir = $targetdir"
echo "...calculated params..."
echo "vhostfile = $vhostfile"
echo "instancedir = $instancedir"
echo "fqn = $fqn"

if [[ ! -d $archivedir ]]; then
	echo Failed to find archive directory $archivedir
	echo "Failed to deployall instance $instancename.$domainname with: Failed to find archive directory $archivedir" | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
	exit 1
fi

testorconfirm="confirm"



# Create user and directory

if [[ "$mode" == "deployall" ]]; then

	echo "***** Create user $osusername with home into /home/jail/home/$osusername"
	
	id -u $osusername
	notfound=$?
	echo notfound=$notfound
	
	if [[ $notfound == 0 ]]
	then
		echo "$osusername seems to already exists"
	else
		echo "perl -e'print crypt(\"'XXXXXX'\", "saltsalt")'"
		export passcrypted=`perl -e'print crypt("'$ospassword'", "saltsalt")'`
		echo "useradd -m -d /home/jail/home/$osusername -p 'YYYYYY' -s '/bin/secureBash' $osusername"
		useradd -m -d $targetdir/$osusername -p "$passcrypted" -s '/bin/secureBash' $osusername 
		if [[ "$?x" != "0x" ]]; then
			echo Error failed to create user $osusername 
			echo "Failed to deployall instance $instancename.$domainname with: useradd -m -d $targetdir/$osusername -p $ospassword -s '/bin/secureBash' $osusername" | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
			exit 1
		fi
		chmod -R go-rwx /home/jail/home/$osusername
	fi

	if [[ -d /home/jail/home/$osusername ]]
	then
		echo "/home/jail/home/$osusername exists"
	else
		mkdir /home/jail/home/$osusername
		chmod -R go-rwx /home/jail/home/$osusername
	fi
fi

if [[ "$mode" == "undeployall" ]]; then

	echo "***** Delete user $osusername with home into /home/jail/home/$osusername"
	
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

fi



# Create/Remove DNS entry

if [[ "$mode" == "deploy" || "$mode" == "deployall" ]]; then

	#$ttl 1d
	#$ORIGIN with.dolicloud.com.
	#@               IN     SOA   ns1with.dolicloud.com. admin.dolicloud.com. (
	#                2017051526       ; serial number
	#                600              ; refresh = 10 minutes
	#                300              ; update retry = 5 minutes
	#                604800           ; expiry = 3 weeks + 12 hours
	#                660              ; negative ttl
	#                )
	#                NS              ns1with.dolicloud.com.
	#                NS              ns2with.dolicloud.com.
	#                IN      TXT     "v=spf1 mx ~all".
	#
	#@               IN      A       79.137.96.15
	#
	#
	#$ORIGIN with.dolicloud.com.
	#
	#; other sub-domain records

	echo "***** Add DNS entry for $instancename in $domainname"

	cat /etc/bind/${ZONE} | grep "^$instancename '" > /dev/null 2>&1
	notfound=$?
	echo notfound=$notfound

	if [[ $notfound == 0 ]]; then
		echo "entry $instancename already found into host /etc/bind/${ZONE}"
	else
		echo "cat /etc/bind/${ZONE} | grep -v '^$instancename ' > /tmp/${ZONE}.$PID"
		cat /etc/bind/${ZONE} | grep -v "^$instancename " > /tmp/${ZONE}.$PID

		echo "***** Add $instancename A 79.137.96.15 into tmp host file"
		echo $instancename A 79.137.96.15 >> /tmp/${ZONE}.$PID  

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
			echo "Failed to deployall instance $instancename.$domainname with: Error when editing the DNS file. File /tmp/${ZONE}.$PID is not valid" | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
			exit 1
		fi
		
		echo "**** Archive file with cp /etc/bind/${ZONE} /etc/bind/archives/${ZONE}-$now"
		cp /etc/bind/${ZONE} /etc/bind/archives/${ZONE}-$now
		
		echo "**** Move new host file"
		mv -fu /tmp/${ZONE}.$PID /etc/bind/${ZONE}
		
		echo "**** Reload dns"
		rndc reload with.dolicloud.com
		#/etc/init.d/bind9 reload
		
		echo "**** nslookup $fqn 127.0.0.1"
		nslookup $fqn 127.0.0.1
		if [[ "$?x" != "0x" ]]; then
			echo Error after reloading DNS. nslookup of $fqn fails
			echo "Failed to deployall instance $instancename.$domainname with: Error after reloading DNS. nslookup of $fqn fails" | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
			exit 1
		fi 
	fi
fi

if [[ "$mode" == "undeploy" || "$mode" == "undeployall" ]]; then

	echo "***** Remove DNS entry for $instancename in $domainname - Test with cat /etc/bind/${ZONE} | grep '^$instancename '"

	cat /etc/bind/${ZONE} | grep "^$instancename '" > /dev/null 2>&1
	notfound=$?
	echo notfound=$notfound
	
	if [[ $notfound == 1 ]]; then
		echo "entry $instancename already not found into host /etc/bind/${ZONE}"
	else
		echo "cat /etc/bind/${ZONE} | grep -v '^$instancename ' > /tmp/${ZONE}.$PID"
		cat /etc/bind/${ZONE} | grep -v "^$instancename " > /tmp/${ZONE}.$PID

		#echo "***** Add $instancename A 79.137.96.15 into tmp host file"
		#echo $instancename A 79.137.96.15 >> /tmp/${ZONE}.$PID  

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
			echo "Failed to deployall instance $instancename.$domainname with: Error when editing the DNS file. File /tmp/${ZONE}.$PID is not valid" | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
			exit 1
		fi
		
		echo "**** Archive file with cp /etc/bind/${ZONE} /etc/bind/archives/${ZONE}-$now"
		cp /etc/bind/${ZONE} /etc/bind/archives/${ZONE}-$now
		
		echo "**** Move new host file with mv -fu /tmp/${ZONE}.$PID /etc/bind/${ZONE}"
		mv -fu /tmp/${ZONE}.$PID /etc/bind/${ZONE}
		
		echo "**** Reload dns"
		rndc reload with.dolicloud.com
		#/etc/init.d/bind9 reload
		
		#echo "**** nslookup $fqn 127.0.0.1"
		#nslookup $fqn 127.0.0.1
		#if [[ "$?x" != "0x" ]]; then
		#	echo Error after reloading DNS. nslookup of $fqn fails. 
		#	echo "Failed to deployall instance $instancename.$domainname with: Error after reloading DNS. nslookup of $fqn fails. " | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
		#	exit 1
		#fi 
	fi

fi



# Deploy/Archive files

if [[ "$mode" == "deploy" || "$mode" == "deployall" ]]; then

	echo "***** Deploy files"
	
	echo "Create dir for instance = /home/jail/home/$osusername/$dbname"
	mkdir -p /home/jail/home/$osusername/$dbname
	
	echo "Check dirwithsources1=$dirwithsources1 targetdirwithsources1=$targetdirwithsources1"
	if [ -d $dirwithsources1 ]; then
	if [[ "x$targetdirwithsources1" != "x" ]]; then
		mkdir -p $targetdirwithsources1
		echo "cp -pr  $dirwithsources1/ $targetdirwithsources1"
		cp -pr  $dirwithsources1/. $targetdirwithsources1
		cp -pr $scriptdir/templates/suspended.php $targetdirwithsources1/suspended.php
	fi
	fi
	echo "Check dirwithsources2=$dirwithsources2 targetdirwithsources2=$targetdirwithsources2"
	if [ -d $dirwithsources2 ]; then
	if [[ "x$targetdirwithsources2" != "x" ]]; then
		mkdir -p $targetdirwithsources2
		echo "cp -pr  $dirwithsources2/ $targetdirwithsources2"
		cp -pr  $dirwithsources2/. $targetdirwithsources2
	fi
	fi
	echo "Check dirwithsources3=$dirwithsources3 targetdirwithsources3=$targetdirwithsources3"
	if [ -d $dirwithsources3 ]; then
	if [[ "x$targetdirwithsources3" != "x" ]]; then
		mkdir -p $targetdirwithsources3
		echo "cp -pr  $dirwithsources3/ $targetdirwithsources3"
		cp -pr  $dirwithsources3/. $targetdirwithsources3
	fi
	fi

	chown -R $osusername.$osusername /home/jail/home/$osusername/$dbname
	chmod -R go-rwx /home/jail/home/$osusername/$dbname
fi

if [[ "$mode" == "undeploy" || "$mode" == "undeployall" ]]; then

	echo "***** Undeploy files into $targetdir/$osusername/$dbname"
			
	# If dir still exists, we move it manually
	if [ -d $targetdir/$osusername/$dbname ]; then
		echo The dir $targetdir/$osusername/$dbname still exists, we archive it
		echo mv -f $targetdir/$osusername/$dbname $archivedir/$osusername/$dbname
		if [[ $testorconfirm == "confirm" ]]
		then
			mkdir $archivedir/$osusername
			mv -f $targetdir/$osusername/$dbname $archivedir/$osusername/$dbname
		fi
	else
		echo The dir $targetdir/$osusername/$dbname seems already removed/archived
	fi

fi


# Deploy config file

if [[ "$mode" == "deploy" || "$mode" == "deployall" ]]; then
	
	echo "***** Deploy config file"
	mkdir -p `dirname $targetfileforconfig1`
	
	echo "mv $fileforconfig1 $targetfileforconfig1"
	if [[ -s $targetfileforconfig1 ]]; then
		echo File $targetfileforconfig1 already exists. We change nothing.
	else
		mv $fileforconfig1 $targetfileforconfig1
	fi

fi



# Create/Disable Apache virtual host

if [[ "$mode" == "deploy" || "$mode" == "deployall" ]]; then

	export apacheconf="/etc/apache2/sites-available/$fqn.conf"
	echo "***** Create apache conf $apacheconf from $vhostfile"
	if [[ -s $apacheconf ]]
	then
		echo "Apache conf $apacheconf already exists, we delete it since it may be a file from an old instance with same name"
		rm -f $apacheconf
	fi

	echo "cat $vhostfile | sed -e 's/__webAppDomain__/$instancename.$domainname/g' | \
			  sed -e 's/__webAppAliases__/$instancename.$domainname/g' | \
			  sed -e 's/__webAppLogName__/$instancename/g' | \
			  sed -e 's/__myMainDomain__/dolicloud.com/g' | \
			  sed -e 's/__osUsername__/$osusername/g' | \
			  sed -e 's/__osGroupname__/$osusername/g' | \
			  sed -e 's;__webAppPath__;$instancedir;' > $apacheconf"
	cat $vhostfile | sed -e "s/__webAppDomain__/$instancename.$domainname/g" | \
			  sed -e "s/__webAppAliases__/$instancename.$domainname/g" | \
			  sed -e "s/__webAppLogName__/$instancename/g" | \
			  sed -e 's/__myMainDomain__/dolicloud.com/g' | \
			  sed -e "s/__osUsername__/$osusername/g" | \
			  sed -e "s/__osGroupname__/$osusername/g" | \
			  sed -e "s;__webAppPath__;$instancedir;" > $apacheconf


	echo Enabled conf with a2ensite $fqn.conf
	a2ensite $fqn.conf
	
	echo /usr/sbin/apache2ctl configtest
	/usr/sbin/apache2ctl configtest
	if [[ "x$?" != "x0" ]]; then
		echo Error when running apache2ctl configtest 
		echo "Failed to deployall instance $instancename.$domainname with: Error when running apache2ctl configtest" | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
		exit 1
	fi 
	
	echo "***** Apache tasks finished. service apache2 reload"
	service apache2 reload
	if [[ "x$?" != "x0" ]]; then
		echo Error when running service apache2 reload 
		echo "Failed to deployall instance $instancename.$domainname with: Error when running service apache2 reload" | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
		exit 2
	fi

fi

if [[ "$mode" == "undeploy" || "$mode" == "undeployall" ]]; then

	export apacheconf="/etc/apache2/sites-enabled/$fqn.conf"
	echo "***** Remove apache conf $apacheconf"

	if [ -f $apacheconf ]; then
	
		echo Disable conf with a2dissite $fqn.conf
		a2dissite $fqn.conf
		
		/usr/sbin/apache2ctl configtest
		if [[ "x$?" != "x0" ]]; then
			echo Error when running apache2ctl configtest 
			echo "Failed to deployall instance $instancename.$domainname with: Error when running apache2ctl configtest" | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
			exit 1
		fi 
		
		echo "***** Apache tasks finished. service apache2 reload"
		service apache2 reload
		if [[ "x$?" != "x0" ]]; then
			echo Error when running service apache2 reload 
			echo "Failed to deployall instance $instancename.$domainname with: Error when running service apache2 reload" | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
			exit 2
		fi
	else
		echo "Virtual host $apacheconf seems already disabled"
	fi
fi



# Install/Uninstall cron

if [[ "$mode" == "deploy" || "$mode" == "deployall" ]]; then

	echo "***** Install cron file $cronfile"
	echo cp $cronfile /var/spool/cron/crontabs/$osusername
	cp $cronfile /var/spool/cron/crontabs/$osusername
	chown $osusername.$osusername /var/spool/cron/crontabs/$osusername
	chmod 644 /var/spool/cron/crontabs/$osusername

fi

if [[ "$mode" == "unsuspend" ]]; then

	echo "***** Restore cron file $cronfile"
	echo mv /var/spool/cron/crontabs.disabled/$osusername /var/spool/cron/crontabs/$osusername
	mv /var/spool/cron/crontabs.disabled/$osusername /var/spool/cron/crontabs/$osusername

fi

if [[ "$mode" == "suspend" ]]; then

	echo "***** Disable cron file /var/spool/cron/crontabs/$osusername"
	mkdir -p /var/spool/cron/crontabs.disabled
	echo mv /var/spool/cron/crontabs/$osusername /var/spool/cron/crontabs.disabled/$osusername
	mv /var/spool/cron/crontabs/$osusername /var/spool/cron/crontabs.disabled/$osusername 

fi

if [[ "$mode" == "undeploy" || "$mode" == "undeployall" ]]; then

	echo "***** Remove cron file $cronfile"
	echo rm -f /var/spool/cron/crontabs/$osusername
	rm -f /var/spool/cron/crontabs/$osusername
	mkdir -p /var/spool/cron/crontabs.disabled
	echo mv /var/spool/cron/crontabs/$osusername /var/spool/cron/crontabs.disabled/$osusername
	mv /var/spool/cron/crontabs/$osusername /var/spool/cron/crontabs.disabled/$osusername 
fi
if [[ "$mode" == "undeployall" ]]; then

	echo rm -f /var/spool/cron/crontabs.disabled/$osusername
	rm -f /var/spool/cron/crontabs.disabled/$osusername 
fi


# Create database (last step, the longer one)

if [[ "$mode" == "deploy" || "$mode" == "deployall" ]]; then

	echo "***** Create database $dbname for user $dbusername"
	
	echo Search sellyoursaas credential
	passsellyoursaas=`cat /root/sellyoursaas`
	echo $passsellyoursaas
	if [[ "x$passsellyoursaas" == "x" ]]; then
		echo Search sellyoursaas credential 2
		passsellyoursaas=`cat /tmp/sellyoursaas`
		if [[ "x$passsellyoursaas" == "x" ]]; then
			echo Failed to get password for mysql user sellyoursaas 
			echo "Failed to deployall instance $instancename.$domainname with: Failed to get password for mysql user sellyoursaas" | mail -s "[Alert] Pb in deployment" supervision@dolicloud.com 
			exit 1
		fi
	fi 
	
	MYSQL=`which mysql`
	
	Q1="CREATE DATABASE IF NOT EXISTS $dbname; "
	Q2="CREATE USER '$dbusername'@'localhost' IDENTIFIED BY '$dbpassword'; "
	SQL="${Q1}${Q2}"
	echo "$MYSQL -A -usellyoursaas -pXXXXXX -e \"$SQL\""
	$MYSQL -A -usellyoursaas -p$passsellyoursaas -e "$SQL"
	
	Q1="CREATE DATABASE IF NOT EXISTS $dbname; "
	Q2="CREATE USER '$dbusername'@'%' IDENTIFIED BY '$dbpassword'; "
	SQL="${Q1}${Q2}"
	echo "$MYSQL -A -usellyoursaas -pXXXXXXX -e \"$SQL\""
	$MYSQL -A -usellyoursaas -p$passsellyoursaas -e "$SQL"
	
	Q1="GRANT CREATE,CREATE TEMPORARY TABLES,CREATE VIEW,DROP,DELETE,INSERT,SELECT,UPDATE,ALTER,INDEX,LOCK TABLES,REFERENCES,SHOW VIEW ON $dbname.* TO '$dbusername'@'localhost'; "
	Q2="GRANT CREATE,CREATE TEMPORARY TABLES,CREATE VIEW,DROP,DELETE,INSERT,SELECT,UPDATE,ALTER,INDEX,LOCK TABLES,REFERENCES,SHOW VIEW ON $dbname.* TO '$dbusername'@'%'; "
	Q3="FLUSH PRIVILEGES; "
	SQL="${Q1}${Q2}${Q3}"
	echo "$MYSQL -A -usellyoursaas -e \"$SQL\""
	$MYSQL -A -usellyoursaas -p$passsellyoursaas -e "$SQL"
	
	echo "You can test with mysql -h remotehost -u $dbusername -p$dbpassword"
	
	# Load dump file
	echo Search dumpfile into $dirwithdumpfile
	for dumpfile in `ls $dirwithdumpfile/*.sql 2>/dev/null`
	do
		echo "$MYSQL -A -usellyoursaas -p$passsellyoursaas -D $dbname < $dumpfile"
		$MYSQL -A -usellyoursaas -p$passsellyoursaas -D $dbname < $dumpfile
	done

fi



# Execute after CLI

if [[ "$mode" == "deploy" || "$mode" == "deployall" ]]; then
	if [[ "x$cliafter" != "x" ]]; then
		if [ -f $cliafter ]; then
				echo ". $cliafter"
				. $cliafter
		fi
	fi
fi


#if ! grep test_$i /etc/hosts >/dev/null; then
#	echo Add name test_$i into /etc/hosts
#	echo 127.0.0.1 test_$i >> /etc/hosts
#fi

echo Process of action $mode of $instancename.$domainname for user $osusername finished with no error
echo

exit 0
