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
export vhostfile="$scriptdir/templates/vhostHttps-dolibarr.template"
export ZONES_PATH="/etc/bind/zones"
export ZONE="with.dolicloud.com.hosts" 


if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   #exit 1
fi

if [ "x$1" == "x" ]; then
	echo "Missing parameter 1 - mode (all|deploy)" 1>&2
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
	echo "Missing parameter 7 - dbusername" 1>&2
	exit 1
fi
if [ "x$8" == "x" ]; then
	echo "Missing parameter 8 - dbpassword" 1>&2
	exit 1
fi

export mode=$1
export osusername=$2
export ospassword=$3
export instancename=$4
export domainname=$5
export dbname=$6
export dbusername=$7
export dbpassword=$8

export dirforconfig1=$9
export targetdirforconfig1=${10}
export dirwithdumpfile=${11}
export dirwithsources1=${12}
export targetdirwithsources1=${13}
export dirwithsources2=${14}
export targetdirwithsources2=${15}
export dirwithsources3=${16}
export targetdirwithsources3=${17}
export cronfile=${18}
export targetdir=${19}

export instancedir=$targetdir/$osusername/$dbname
export fqn=$instancename.$domainname

# For debug
echo "...input params..."
echo "mode = $mode"
echo "osusername = $osusername"
echo "instancename = $instancename"
echo "domainname = $domainname"
echo "dbname = $dbname"
echo "dbusername = $dbusername"
echo "dbpassword = $dbpassword"
echo "dirforconfig1 = $dirforconfig1"
echo "targetdirforconfig1 = $targetdirforconfig1"
echo "dirwithdumpfile = $dirwithdumpfile"
echo "dirwithsources1 = $dirwithsources1"
echo "targetdirwithsources1 = $targetdirwithsources1"
echo "dirwithsources2 = $dirwithsources2"
echo "targetdirwithsources2 = $targetdirwithsources2"
echo "dirwithsources3 = $dirwithsources3"
echo "targetdirwithsources3 = $targetdirwithsources3"
echo "cronfile = $cronfile"
echo "vhostfile = $vhostfile"
echo "targetdir = $targetdir"
echo "...calculated params..."
echo "instancedir = $instancedir"
echo "fqn = $fqn"



# Create user and directory

if [[ "$mode" == "all" ]]; then
echo "***** Create user /home/jail/home/$osusername"
if [[ -d /home/jail/home/$osusername ]]
then
	echo "$osusername seems to already exists"
else
	echo "useradd -m -d /home/jail/home/$osusername -p XXXXXXXXXX -s '/bin/secureBash' $osusername"
	useradd -m -d $targetdir/$osusername -p $ospassword -s '/bin/secureBash' $osusername 
	if [[ "$?x" != "0x" ]]; then
		echo Error failed to create user $osusername 
		#exit 1
	fi
	chmod -R go-rwx /home/jail/home/$osusername
fi
fi



# Create DNS entry

if [[ "$mode" == "all" ]]; then
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

echo "cat /etc/bind/${ZONE} | grep -v '^$instancename ' > /tmp/${ZONE}.$PID"
cat /etc/bind/${ZONE} | grep -v '^$instancename ' > /tmp/${ZONE}.$PID

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
	echo Error after reloading DNS. nslookup of $fqn fails. 
	#exit 1
fi 

fi



# Deploy files

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


# Deploy config file

echo "Deploy config file"
echo "mv $dirforconfig1 $targetdirforconfig1/conf.php"
if [[ -s $targetdirforconfig1/conf.php ]]; then
	echo File $targetdirforconfig1/conf.php already exists. We change nothing.
else
	mv $dirforconfig1 $targetdirforconfig1/conf.php
fi

# Create apache virtual host

if [[ "$mode" == "all" ]]; then
export apacheconf="/etc/apache2/sites-available/$fqn.conf"
echo "***** Create apache conf $apacheconf from $vhostfile"
if [[ -s $apacheconf ]]
then
	echo "Apache conf $apacheconf already exists"
else
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
fi

echo Enabled conf with a2ensite $fqn.conf
a2ensite $fqn.conf

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


fi




# Create database (last step, the longer one)

if [[ "$mode" == "all" ]]; then
echo "***** Create database $dbname for user $dbusername"

echo Search sellyoursaas credential
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

MYSQL=`which mysql`

Q1="CREATE DATABASE IF NOT EXISTS $dbname; "
Q2="CREATE USER '$dbusername'@'localhost' IDENTIFIED BY '$dbpassword'; "
SQL="${Q1}${Q2}"
echo "$MYSQL -usellyoursaas -e $SQL"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL"

Q1="CREATE DATABASE IF NOT EXISTS $dbname; "
Q2="CREATE USER '$dbusername'@'%' IDENTIFIED BY '$dbpassword'; "
SQL="${Q1}${Q2}"
echo "$MYSQL -usellyoursaas -e $SQL"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL"

Q1="GRANT CREATE,CREATE TEMPORARY TABLES,CREATE VIEW,DROP,DELETE,INSERT,SELECT,UPDATE,ALTER,INDEX,LOCK TABLES,REFERENCES,SHOW VIEW ON $dbname.* TO '$dbusername'@'localhost'; "
Q2="GRANT CREATE,CREATE TEMPORARY TABLES,CREATE VIEW,DROP,DELETE,INSERT,SELECT,UPDATE,ALTER,INDEX,LOCK TABLES,REFERENCES,SHOW VIEW ON $dbname.* TO '$dbusername'@'%'; "
Q3="FLUSH PRIVILEGES; "
SQL="${Q1}${Q2}${Q3}"
echo "$MYSQL -usellyoursaas -e $SQL"
$MYSQL -usellyoursaas -p$passsellyoursaas -e "$SQL"

echo "You can test with mysql -h remotehost -u $dbusername -p$dbpassword"

# Load dump file
echo Search dumpfile into $dirwithdumpfile
for dumpfile in `ls $dirwithdumpfile/*.sql 2>/dev/null`
do
	echo "$MYSQL -usellyoursaas -p$passsellyoursaas -D $dbname < $dumpfile"
	$MYSQL -usellyoursaas -p$passsellyoursaas -D $dbname < $dumpfile
done

fi





#if ! grep test_$i /etc/hosts >/dev/null; then
#	echo Add name test_$i into /etc/hosts
#	echo 127.0.0.1 test_$i >> /etc/hosts
#fi

echo System deployment of $instancename.$domainname for user $osusername finished

exit 0
