#!/bin/bash

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
export scriptdir=$(dirname $(realpath ${0}))


export ZONE="on.dolicloud.com.hosts" 


if [[ ! -f $scriptdir/filetomigrate.txt ]]; then
	echo Failed to find file $scriptdir/filetomigrate.txt with list of instances to migrate.
	exit 1
fi


# Make migration
echo "----- Make migration"
for instancename in `cat $scriptdir/filetomigrate.txt | grep -v '#'`
do
	if [[ "x$instancename" != "x" ]]; then
		echo Try to migrate $instance with php old_migrate_v1v2.php $instancename $instancename confirm
		#php old_migrate_v1v2.php $instancename $instancename confirm
		result=$?
		echo Result = $result
	fi
done


# Fix DNS
echo "----- Change DNS"
for instancename in `cat $scriptdir/filetomigrate.txt | grep -v '#'`
do
	echo `date +%Y%m%d%H%M%S`" **** Archive file with cp /etc/bind/${ZONE} /etc/bind/archives/${ZONE}-$now"
	cp /etc/bind/${ZONE} /etc/bind/archives/${ZONE}-$now

	if [[ "x$instance" != "x" ]]; then
		
		echo Remove and add DNS for $instance
		
		echo "cat /etc/bind/${ZONE} | grep -v '^$instancename ' > /tmp/${ZONE}.$PID"
		cat /etc/bind/${ZONE} | grep -v "^$instancename " > /tmp/${ZONE}.$PID

		echo `date +%Y%m%d%H%M%S`" ***** Add $instancename A $REMOTEIP into tmp host file"
		echo $instancename A $REMOTEIP >> /tmp/${ZONE}.$PID  

		echo `date +%Y%m%d%H%M%S`" **** Move new host file with mv -fu /tmp/${ZONE}.$PID /etc/bind/${ZONE}"
		#mv -fu /tmp/${ZONE}.$PID /etc/bind/${ZONE}

	fi
done


# Disable V1
echo "----- Disable V1 by switching to manual collection"
for instancename in `cat $scriptdir/filetomigrate.txt | grep -v '#'`
do
	sql="UPDATE dolicloud_saasplex set manual_collection = true where id IN SELECT customer_id FROM app_package WHERE name IN ("
	if [[ "x$instancename" != "x" ]]; then
		sql="$sql'$instancename'," 
	fi
	sql="$sql'bidon');"
	echo $sql
done


