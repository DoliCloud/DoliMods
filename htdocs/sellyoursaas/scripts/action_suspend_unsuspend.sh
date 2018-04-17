#!/bin/bash

# To use this script with remote ssh (not required when using the remote agent):
# Create a symbolic link to this file .../action_suspend_unsuspend.sh into /usr/bin
# Grant adequate permissions (550 mean root and group www-data can read and execute, nobody can write)
# sudo chown root:www-data /usr/bin/action_suspend_unsuspend.sh
# sudo chmod 550 /usr/bin/action_suspend_unsuspend.sh
# And allow apache to sudo on this script by doing visudo to add line:
#www-data        ALL=(ALL) NOPASSWD: /usr/bin/action_suspend_unsuspend.sh


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
export targetdir="/home/jail/home"				
export archivedir="/home/archives"
export ZONES_PATH="/etc/bind/zones"
export scriptdir=$(dirname $(realpath ${0}))
export vhostfile="$scriptdir/templates/vhostHttps-sellyoursaas.template"
export vhostfilesuspended="$scriptdir/templates/vhostHttps-sellyoursaas-suspended.template"


if [ "$(id -u)" != "0" ]; then
	echo "This script must be run as root" 1>&2
	exit 1
fi

if [ "x$1" == "x" ]; then
	echo "Missing parameter 1 - mode (suspend|unsuspend)" 1>&2
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
if [ "x${22}" == "x" ]; then
	echo "Missing parameter 22 - EMAILFROM" 1>&2
	exit 1
fi
if [ "x${23}" == "x" ]; then
	echo "Missing parameter 23 - REMOTEIP" 1>&2
	exit 1
fi

export mode=$1
export osusername=$2
export ospassword=$3
export instancename=$4
export domainname=$5
export dbname=$6
export port=$7

export targetdir=${21}
export EMAILFROM=${22}

export instancedir=$targetdir/$osusername/$dbname
export fqn=$instancename.$domainname

# For debug
echo `date +%Y%m%d%H%M%S`" input params for $0:"
echo "mode = $mode"
echo "osusername = $osusername"
echo "ospassword = XXXXXX"
echo "instancename = $instancename"
echo "domainname = $domainname"
echo "targetdir = $targetdir"
echo `date +%Y%m%d%H%M%S`" calculated params:"
echo "instancedir = $instancedir"
echo "fqn = $fqn"

if [[ ! -d $archivedir ]]; then
	echo Failed to find archive directory $archivedir
	exit 1
fi

testorconfirm="confirm"




# Suspend

if [[ "$mode" == "suspend" ]]; then
	echo `date +%Y%m%d%H%M%S`" ***** Suspend instance in /home/jail/home/$osusername/$dbname"

	export apacheconf="/etc/apache2/sites-available/$fqn.conf"
	echo "Create a suspended apache conf $apacheconf from $vhostfilesuspended"

	if [[ -s $apacheconf ]]
	then
		echo "Apache conf $apacheconf already exists, we delete it since it may be a file from an old instance with same name"
		rm -f $apacheconf
	fi

	echo "cat $vhostfilesuspended | sed -e 's/__webAppDomain__/$instancename.$domainname/g' | \
			  sed -e 's/__webAppAliases__/$instancename.$domainname/g' | \
			  sed -e 's/__webAppLogName__/$instancename/g' | \
			  sed -e 's/__myMainDomain__/dolicloud.com/g' | \
			  sed -e 's/__osUsername__/$osusername/g' | \
			  sed -e 's/__osGroupname__/$osusername/g' | \
			  sed -e 's;__webAppPath__;$instancedir;' > $apacheconf"
	cat $vhostfilesuspended | sed -e "s/__webAppDomain__/$instancename.$domainname/g" | \
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
		echo "Failed to suspend instance $instancename.$domainname with: Error when running apache2ctl configtest" | mail -aFrom:$EMAILFROM -s "[Alert] Pb in suspend" supervision@dolicloud.com 
		exit 1
	fi 
	
	echo `date +%Y%m%d%H%M%S`" ***** Apache tasks finished. service apache2 reload"
	service apache2 reload
	if [[ "x$?" != "x0" ]]; then
		echo Error when running service apache2 reload
		echo "Failed to suspend instance $instancename.$domainname with: Error when running service apache2 reload" | mail -aFrom:$EMAILFROM -s "[Alert] Pb in suspend" supervision@dolicloud.com 
		exit 2
	fi
			
fi


# Unsuspend

if [[ "$mode" == "unsuspend" ]]; then
	echo `date +%Y%m%d%H%M%S`" ***** Unsuspend instance in /home/jail/home/$osusername/$dbname"

	export apacheconf="/etc/apache2/sites-available/$fqn.conf"
	echo "Create a new apache conf $apacheconf from $vhostfile"

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
		echo "Failed to unsuspend instance $instancename.$domainname with: Error when running apache2ctl configtest" | mail -aFrom:$EMAILFROM -s "[Alert] Pb in suspend" supervision@dolicloud.com 
		exit 1
	fi 

	echo `date +%Y%m%d%H%M%S`" ***** Apache tasks finished. service apache2 reload"
	service apache2 reload
	if [[ "x$?" != "x0" ]]; then
		echo Error when running service apache2 reload
		echo "Failed to unsuspend instance $instancename.$domainname with: Error when running service apache2 reload" | mail -aFrom:$EMAILFROM -s "[Alert] Pb in suspend" supervision@dolicloud.com 
		exit 2
	fi

fi


# Cron

if [[ "$mode" == "unsuspend" ]]; then

	echo `date +%Y%m%d%H%M%S`" ***** Restore cron file $cronfile"
	echo mv /var/spool/cron/crontabs.disabled/$osusername /var/spool/cron/crontabs/$osusername
	mv /var/spool/cron/crontabs.disabled/$osusername /var/spool/cron/crontabs/$osusername
	chmod 600 /var/spool/cron/crontabs/$osusername
fi

if [[ "$mode" == "suspend" ]]; then

	echo `date +%Y%m%d%H%M%S`" ***** Disable cron file /var/spool/cron/crontabs/$osusername"
	mkdir -p /var/spool/cron/crontabs.disabled
	echo mv /var/spool/cron/crontabs/$osusername /var/spool/cron/crontabs.disabled/$osusername
	mv /var/spool/cron/crontabs/$osusername /var/spool/cron/crontabs.disabled/$osusername 

fi


echo `date +%Y%m%d%H%M%S`" Process of action $mode of $instancename.$domainname for user $osusername finished"
echo

exit 0
