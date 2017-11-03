#!/bin/bash

# To use this script:
# Create a symbolic link to this file .../create_user_instance.sh into /usr/bin

# Grant adequate permissions (550 mean root and group www-data can read and execute, nobody can write)
# sudo chown root:www-data /usr/bin/create_user_instance.sh
# sudo chmod 550 /usr/bin/create_user_instance.sh
# And allow apache to sudo on this script by doing visudo to add line:
#www-data        ALL=(ALL) NOPASSWD: /usr/bin/create_user_instance.sh

export now=`date +%Y%m%d%H%M%S`

echo 
echo "# now --------------------> $now"
echo "# PID --------------------> ${$}"
echo "# user id ----------------> $(id -u)"
echo "# PWD --------------------> $PWD" 
echo "# arguments called with --> ${@}"
echo "# path to me -------------> ${0}"
echo "# parent path ------------> ${0%/*}"
echo "# my name ----------------> ${0##*/}"
echo "# realname ---------------> $(realpath ${0})"
echo "# realname name ----------> $(basename $(realpath ${0}))"
echo "# realname dir -----------> $(dirname $(realpath ${0}))"

export PID=${$}
export scriptdir=$(dirname $(realpath ${0}))
export vhostfile="$scriptdir/templates/vhostHttps-dolibarr.template"
export targetdir="/home/jail/home"				


if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   #exit 1
fi

if [ "x$1" == "x" ]; then
	echo "Missing parameter 1 - osusername" 1>&2
	exit 1
fi
if [ "x$2" == "x" ]; then
	echo "Missing parameter 2 - ospassword" 1>&2
	exit 1
fi
if [ "x$3" == "x" ]; then
	echo "Missing parameter 3 - instancename" 1>&2
	exit 1
fi
if [ "x$4" == "x" ]; then
	echo "Missing parameter 4 - domainname" 1>&2
	exit 1
fi
if [ "x$5" == "x" ]; then
	echo "Missing parameter 5 - dbname" 1>&2
	exit 1
fi
if [ "x$6" == "x" ]; then
	echo "Missing parameter 6 - dbusername" 1>&2
	exit 1
fi
if [ "x$7" == "x" ]; then
	echo "Missing parameter 7 - dbpassword" 1>&2
	exit 1
fi

export osusername=$1
export ospassword=$2
export instancename=$3
export domainname=$4
export dbname=$5
export dbusername=$6
export dbpassword=$7
export fqn=$instancename.$domainname

echo "vhostfile = $vhostfile"
echo "targetdir = $targetdir"
echo "osusername = $osusername"
echo "instancename = $instancename"
echo "domainname = $domainname"
echo "dbname = $dbname"
echo "fqn = $fqn"


# Create user and directory
if [[ -d $targetdir/$osusername ]]
then
	echo "Dir $targetdir/$osusername already exists"
else
	echo "Create dir $targetdir/$osusername"
	useradd -m -d $targetdir/$osusername $osusername
	mkdir -p "$targetdir/$osusername/$dbname/documents"
	mkdir -p "$targetdir/$osusername/$dbname/htdocs"
	echo "HTML test page for $osusername" > "$targetdir/$osusername/$dbname/test.html"
fi

#if [[ ! -L "$targetdir/$osusername/$dbname/dolibarrtest" ]]
#then
#	echo "Add link $targetdir/$osusername/$dbname/dolibarrtest to dolibarr files"
#	ln -fs /home/ldestailleur/git/dolibarr/htdocs "$targetdir/$osusername/$instancename/dolibarrtest"
#fi


# Create DNS entry
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

echo Add DNS entry for $instancename in $domainname

cat /etc/bind/with.dolicloud.com.hosts > /tmp/with.dolicloud.com.hosts.$PID
echo $instancename A 79.137.96.15 >> /tmp/with.dolicloud.com.hosts.$PID  
named-checkzone with.dolicloud.com /tmp/with.dolicloud.com.hosts.$PID
if [[ "$?x" != "0x" ]]; then
	echo Error when editing the DNS file. File /tmp/with.dolicloud.com.hosts.$PID is not valid 
	exit 1
fi 

echo cp /etc/bind/with.dolicloud.com.hosts /etc/bind/with.dolicloud.com.hosts.back-$now
cp /etc/bind/with.dolicloud.com.hosts /etc/bind/with.dolicloud.com.hosts.back-$now

mv /tmp/with.dolicloud.com.hosts.$PID /etc/bind/with.dolicloud.com.hosts

rndc reload with.dolicloud.com.hosts
/etc/init.d/bind9 reload

echo nslookup $fqn 127.0.0.1
nslookup $fqn 127.0.0.1
if [[ "$?x" != "0x" ]]; then
	echo Error after reloading DNS. nslookup of $fqn fails. 
	#exit 1
fi 

# Deploy files
echo Deploy files into $targetdir/$osusername/$dbname/htdocs and $targetdir/$osusername/$dbname/documents




# Create database
echo Create database $dbname for user $dbusername

MYSQL=`which mysql`
Q1="CREATE DATABASE IF NOT EXISTS $dbname; "
Q2="GRANT USAGE ON $dbname.* TO $dbusername@% IDENTIFIED BY '$dbpassword'; "
Q3="GRANT ALL PRIVILEGES ON $dbname.* TO $dbusername@%; "
Q4="FLUSH PRIVILEGES; "
SQL="${Q1}${Q2}${Q3}${Q4}"

echo $MYSQL -uroot -e "$SQL"
$MYSQL -uroot -e "$SQL"


# Create apache virtual host
export apacheconf="/etc/apache2/sites-available/$fqn.conf"
if [[ -f $apacheconf ]]
then
	echo "Apache conf $apacheconf already exists"
else
	echo "Create apache conf $apacheconf from $vhostfile"
	cat $vhostfile | sed -e "s/__webAppDomain__/$instancename/g" | \
		  sed -e "s/__webAppAliases__/$instancename/g" | \
		  sed -e "s/__webAppLogName__/$instancename/g" | \
		  sed -e "s/__osUsername__/$osusername/g" | \
		  sed -e "s/__osGroupname__/$osusername/g" | \
		  sed -e 's/%__webAppPath__%$targetdir%' > $apacheconf

	echo a2ensite $fqn.conf
	
	a2ensite $fqn.conf

	/usr/sbin/apache2ctl configtest
	if [[ "$?x" != "0x" ]]; then
		echo Error when running apache2ctl configtest 
		exit 1
	fi 

	#echo "Apache tasks finished. service apache2 reload"
	service apache2 reload
	if [[ "$?x" != "0x" ]]; then
		echo Error when running service apache2 reload 
		exit 2
	fi 
	
fi



#if ! grep test_$i /etc/hosts >/dev/null; then
#	echo Add name test_$i into /etc/hosts
#	echo 127.0.0.1 test_$i >> /etc/hosts
#fi
	
exit 0
