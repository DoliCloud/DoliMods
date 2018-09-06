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
export archivedir="/home/jail/archives"
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
export EMAILFROM=${22}
export REMOTEIP=${23}
export SELLYOURSAAS_ACCOUNT_URL=${24}
export instancenameold=${25}
export domainnameold=${26}
export customurl=${27}
if [ "x$customurl" == "x-" ]; then
	customurl=""
fi

export instancedir=$targetdir/$osusername/$dbname
export fqn=$instancename.$domainname
export fqnold=$instancenameold.$domainnameold



# For debug
echo `date +%Y%m%d%H%M%S`" input params for $0:"
echo "mode = $mode"
echo "osusername = $osusername"
echo "ospassword = XXXXXX"
echo "instancename = $instancename"
echo "domainname = $domainname"
echo "targetdir = $targetdir"
echo "EMAILFROM = $EMAILFROM"
echo "REMOTEIP = $REMOTEIP"
echo "SELLYOURSAAS_ACCOUNT_URL = $SELLYOURSAAS_ACCOUNT_URL" 
echo "instancenameold = $instancenameold" 
echo "domainnameold = $domainnameold" 
echo "customurl = $customurl" 

echo `date +%Y%m%d%H%M%S`" calculated params:"
echo "instancedir = $instancedir"
echo "fqn = $fqn"
echo "fqnold = $fqnold"

if [[ ! -d $archivedir ]]; then
	echo Failed to find archive directory $archivedir
	exit 1
fi

testorconfirm="confirm"



# Rename

if [[ "$mode" == "rename" ]]; then

	if [[ "$fqn" != "$fqnold" ]]; then
		echo `date +%Y%m%d%H%M%S`" ***** For instance in /home/jail/home/$osusername/$dbname, check if new virtual host $fqn exists"

		export apacheconf="/etc/apache2/sellyoursaas-online/$fqn.conf"
		if [ -f $apacheconf ]; then
				echo "Error failed to rename. New name is already used." 
				exit 1
		fi
	fi
	
	# TODO
	# Add DNS entry for $fqn


	echo `date +%Y%m%d%H%M%S`" ***** For instance in /home/jail/home/$osusername/$dbname, create a new virtual name $fqn"

	export apacheconf="/etc/apache2/sellyoursaas-available/$fqn.conf"
	echo `date +%Y%m%d%H%M%S`" ***** Create a new apache conf $apacheconf from $vhostfile"

	if [[ -s $apacheconf ]]
	then
		echo "Apache conf $apacheconf already exists, we delete it since it may be a file from an old instance with same name"
		rm -f $apacheconf
	fi

	echo "cat $vhostfile | sed -e 's/__webAppDomain__/$instancename.$domainname/g' | \
			  sed -e 's/__webAppAliases__/$instancename.$domainname $customurl/g' | \
			  sed -e 's/__webAppLogName__/$instancename/g' | \
			  sed -e 's/__webAdminEmail__/$EMAILFROM/g' | \
			  sed -e 's/__osUsername__/$osusername/g' | \
			  sed -e 's/__osGroupname__/$osusername/g' | \
			  sed -e 's;__osUserPath__;/home/jail/home/$osusername/$dbname;g' | \
			  sed -e 's;__webMyAccount__;$SELLYOURSAAS_ACCOUNT_URL;g' | \
			  sed -e 's;__webAppPath__;$instancedir;g' > $apacheconf"
	cat $vhostfile | sed -e "s/__webAppDomain__/$instancename.$domainname/g" | \
			  sed -e "s/__webAppAliases__/$instancename.$domainname $customurl/g" | \
			  sed -e "s/__webAppLogName__/$instancename/g" | \
			  sed -e "s/__webAdminEmail__/$EMAILFROM/g" | \
			  sed -e "s/__osUsername__/$osusername/g" | \
			  sed -e "s/__osGroupname__/$osusername/g" | \
			  sed -e "s;__osUserPath__;/home/jail/home/$osusername/$dbname;g" | \
			  sed -e "s;__webMyAccount__;$SELLYOURSAAS_ACCOUNT_URL;g" | \
			  sed -e "s;__webAppPath__;$instancedir;g" > $apacheconf


	#echo Enable conf with a2ensite $fqn.conf
	#a2ensite $fqn.conf
	echo Enable conf with ln -fs /etc/apache2/sellyoursaas-available/$fqn.conf /etc/apache2/sellyoursaas-enabled
	ln -fs /etc/apache2/sellyoursaas-available/$fqn.conf /etc/apache2/sellyoursaas-enabled
	
	echo /usr/sbin/apache2ctl configtest
	/usr/sbin/apache2ctl configtest
	if [[ "x$?" != "x0" ]]; then
		echo Error when running apache2ctl configtest 
		echo "Failed to unsuspend instance $instancename.$domainname with: Error when running apache2ctl configtest" | mail -aFrom:$EMAILFROM -s "[Alert] Pb in suspend" $EMAILFROM 
		exit 1
	fi 

	echo `date +%Y%m%d%H%M%S`" ***** Apache tasks finished. service apache2 reload"
	service apache2 reload
	if [[ "x$?" != "x0" ]]; then
		echo Error when running service apache2 reload
		echo "Failed to unsuspend instance $instancename.$domainname with: Error when running service apache2 reload" | mail -aFrom:$EMAILFROM -s "[Alert] Pb in suspend" $EMAILFROM 
		exit 2
	fi



	echo `date +%Y%m%d%H%M%S`" ***** For instance in /home/jail/home/$osusername/$dbname, delete old virtual name $fqnold"

	export apacheconf="/etc/apache2/sellyoursaas-online/$fqnold.conf"
	echo `date +%Y%m%d%H%M%S`" ***** Remove apache conf $apacheconf"

	if [ -f $apacheconf ]; then
	
		echo Disable conf with a2dissite $fqnold.conf
		#a2dissite $fqn.conf
		rm /etc/apache2/sellyoursaas-online/$fqnold.conf
		
		/usr/sbin/apache2ctl configtest
		if [[ "x$?" != "x0" ]]; then
			echo Error when running apache2ctl configtest 
			echo "Failed to delete virtual host with old name instance $instancenameold.$domainnameold with: Error when running apache2ctl configtest" | mail -aFrom:$EMAILFROM -s "[Alert] Pb in rename" $EMAILFROM
			exit 1
		fi
		
		echo `date +%Y%m%d%H%M%S`" ***** Apache tasks finished. service apache2 reload"
		service apache2 reload
		if [[ "x$?" != "x0" ]]; then
			echo Error when running service apache2 reload 
			echo "Failed to delete virtual host with old name instance $instancenameold.$domainnameold with: Error when running service apache2 reload" | mail -aFrom:$EMAILFROM -s "[Alert] Pb in rename" $EMAILFROM
			exit 2
		fi
	else
		echo "Virtual host $apacheconf seems already disabled"
	fi


	# TODO
	# Remove DNS entry for $fqnold




fi

# Suspend

if [[ "$mode" == "suspend" ]]; then
	echo `date +%Y%m%d%H%M%S`" ***** Suspend instance in /home/jail/home/$osusername/$dbname"

	export apacheconf="/etc/apache2/sellyoursaas-available/$fqn.conf"
	echo "Create a suspended apache conf $apacheconf from $vhostfilesuspended"

	if [[ -s $apacheconf ]]
	then
		echo "Apache conf $apacheconf already exists, we delete it since it may be a file from an old instance with same name"
		rm -f $apacheconf
	fi

	echo "cat $vhostfilesuspended | sed -e 's/__webAppDomain__/$instancename.$domainname/g' | \
			  sed -e 's/__webAppAliases__/$instancename.$domainname $customurl/g' | \
			  sed -e 's/__webAppLogName__/$instancename/g' | \
			  sed -e 's/__webAdminEmail__/$EMAILFROM/g' | \
			  sed -e 's/__osUsername__/$osusername/g' | \
			  sed -e 's/__osGroupname__/$osusername/g' | \
			  sed -e 's;__osUserPath__;/home/jail/home/$osusername/$dbname;g' | \
			  sed -e 's;__webMyAccount__;$SELLYOURSAAS_ACCOUNT_URL;g' | \
			  sed -e 's;__webAppPath__;$instancedir;g' > $apacheconf"
	cat $vhostfilesuspended | sed -e "s/__webAppDomain__/$instancename.$domainname/g" | \
			  sed -e "s/__webAppAliases__/$instancename.$domainname $customurl/g" | \
			  sed -e "s/__webAppLogName__/$instancename/g" | \
			  sed -e "s/__webAdminEmail__/$EMAILFROM/g" | \
			  sed -e "s/__osUsername__/$osusername/g" | \
			  sed -e "s/__osGroupname__/$osusername/g" | \
			  sed -e "s;__osUserPath__;/home/jail/home/$osusername/$dbname;g" | \
			  sed -e "s;__webMyAccount__;$SELLYOURSAAS_ACCOUNT_URL;g" | \
			  sed -e "s;__webAppPath__;$instancedir;g" > $apacheconf


	#echo Enable conf with a2ensite $fqn.conf
	#a2ensite $fqn.conf
	echo Enable conf with ln -fs /etc/apache2/sellyoursaas-available/$fqn.conf /etc/apache2/sellyoursaas-online
	ln -fs /etc/apache2/sellyoursaas-available/$fqn.conf /etc/apache2/sellyoursaas-online
	
	echo /usr/sbin/apache2ctl configtest
	/usr/sbin/apache2ctl configtest
	if [[ "x$?" != "x0" ]]; then
		echo Error when running apache2ctl configtest. We remove the new created virtual host /etc/apache2/sellyoursaas-online/$fqn.conf to hope to restore configtest ok.
		rm -f /etc/apache2/sellyoursaas-online/$fqn.conf
		echo "Failed to suspend instance $instancename.$domainname with: Error when running apache2ctl configtest" | mail -aFrom:$EMAILFROM -s "[Alert] Pb when suspending $instancename.$domainname" $EMAILFROM 
		exit 1
	fi 
	
	echo `date +%Y%m%d%H%M%S`" ***** Apache tasks finished. service apache2 reload"
	service apache2 reload
	if [[ "x$?" != "x0" ]]; then
		echo Error when running service apache2 reload
		echo "Failed to suspend instance $instancename.$domainname with: Error when running service apache2 reload" | mail -aFrom:$EMAILFROM -s "[Alert] Pb when suspending $instancename.$domainname" $EMAILFROM 
		exit 2
	fi

fi


# Unsuspend

if [[ "$mode" == "unsuspend" ]]; then
	echo `date +%Y%m%d%H%M%S`" ***** Unsuspend instance in /home/jail/home/$osusername/$dbname"

	export apacheconf="/etc/apache2/sellyoursaas-available/$fqn.conf"
	echo "Create a new apache conf $apacheconf from $vhostfile"

	if [[ -s $apacheconf ]]
	then
		echo "Apache conf $apacheconf already exists, we delete it since it may be a file from an old instance with same name"
		rm -f $apacheconf
	fi

	echo "cat $vhostfile | sed -e 's/__webAppDomain__/$instancename.$domainname/g' | \
			  sed -e 's/__webAppAliases__/$instancename.$domainname $customurl/g' | \
			  sed -e 's/__webAppLogName__/$instancename/g' | \
			  sed -e 's/__webAdminEmail__/$EMAILFROM/g' | \
			  sed -e 's/__osUsername__/$osusername/g' | \
			  sed -e 's/__osGroupname__/$osusername/g' | \
			  sed -e 's;__osUserPath__;/home/jail/home/$osusername/$dbname;g' | \
			  sed -e 's;__webMyAccount__;$SELLYOURSAAS_ACCOUNT_URL;g' | \
			  sed -e 's;__webAppPath__;$instancedir;g' > $apacheconf"
	cat $vhostfile | sed -e "s/__webAppDomain__/$instancename.$domainname/g" | \
			  sed -e "s/__webAppAliases__/$instancename.$domainname $customurl/g" | \
			  sed -e "s/__webAppLogName__/$instancename/g" | \
			  sed -e "s/__webAdminEmail__/$EMAILFROM/g" | \
			  sed -e "s/__osUsername__/$osusername/g" | \
			  sed -e "s/__osGroupname__/$osusername/g" | \
			  sed -e "s;__osUserPath__;/home/jail/home/$osusername/$dbname;g" | \
			  sed -e "s;__webMyAccount__;$SELLYOURSAAS_ACCOUNT_URL;g" | \
			  sed -e "s;__webAppPath__;$instancedir;g" > $apacheconf


	#echo Enable conf with a2ensite $fqn.conf
	#a2ensite $fqn.conf
	echo Enable conf with ln -fs /etc/apache2/sellyoursaas-available/$fqn.conf /etc/apache2/sellyoursaas-enabled
	ln -fs /etc/apache2/sellyoursaas-available/$fqn.conf /etc/apache2/sellyoursaas-enabled
	
	echo /usr/sbin/apache2ctl configtest
	/usr/sbin/apache2ctl configtest
	if [[ "x$?" != "x0" ]]; then
		echo Error when running apache2ctl configtest 
		echo "Failed to unsuspend instance $instancename.$domainname with: Error when running apache2ctl configtest" | mail -aFrom:$EMAILFROM -s "[Alert] Pb in suspend" $EMAILFROM 
		exit 1
	fi 

	echo `date +%Y%m%d%H%M%S`" ***** Apache tasks finished. service apache2 reload"
	service apache2 reload
	if [[ "x$?" != "x0" ]]; then
		echo Error when running service apache2 reload
		echo "Failed to unsuspend instance $instancename.$domainname with: Error when running service apache2 reload" | mail -aFrom:$EMAILFROM -s "[Alert] Pb in suspend" $EMAILFROM 
		exit 2
	fi

fi


# Cron

if [[ "$mode" == "unsuspend" ]]; then

	echo `date +%Y%m%d%H%M%S`" ***** Reinstall cron file $cronfile"
	if [[ -f /var/spool/cron/crontabs/$osusername ]]; then
		echo merge existing $cronfile with existing /var/spool/cron/crontabs/$osusername
		echo "cat /var/spool/cron/crontabs/$osusername | grep -v $dbname > /tmp/$dbname.tmp"
		cat /var/spool/cron/crontabs/$osusername | grep -v $dbname > /tmp/$dbname.tmp
		echo "cat $cronfile >> /tmp/$dbname.tmp"
		cat $cronfile >> /tmp/$dbname.tmp
		echo cp /tmp/$dbname.tmp /var/spool/cron/crontabs/$osusername
		cat /tmp/$dbname.tmp cp $cronfile /var/spool/cron/crontabs/$osusername
	else
		echo cron file /var/spool/cron/crontabs/$osusername does not exists yet
		echo cp $cronfile /var/spool/cron/crontabs/$osusername
		cp $cronfile /var/spool/cron/crontabs/$osusername
	fi

	chown $osusername.$osusername /var/spool/cron/crontabs/$osusername
	chmod 600 /var/spool/cron/crontabs/$osusername
fi

if [[ "$mode" == "suspend" ]]; then

	echo `date +%Y%m%d%H%M%S`" ***** Remove cron file /var/spool/cron/crontabs/$osusername"
	if [ -s /var/spool/cron/crontabs/$osusername ]; then
		mkdir -p /var/spool/cron/crontabs.disabled
		rm -f /var/spool/cron/crontabs.disabled/$osusername
		echo cp /var/spool/cron/crontabs/$osusername /var/spool/cron/crontabs.disabled/$osusername
		cp /var/spool/cron/crontabs/$osusername /var/spool/cron/crontabs.disabled/$osusername
		cat /var/spool/cron/crontabs/$osusername | grep -v $dbname > /tmp/$dbname.tmp
		#echo rm -f /var/spool/cron/crontabs/$osusername
		echo cp /tmp/$dbname.tmp /var/spool/cron/crontabs/$osusername
	else
		echo cron file /var/spool/cron/crontabs/$osusername already removed or empty
	fi 

fi


echo `date +%Y%m%d%H%M%S`" Process of action $mode of $instancename.$domainname for user $osusername finished"
echo

exit 0
