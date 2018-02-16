#!/bin/bash

# To use this script:
# Create a symbolic link to this file .../suspend_unsuspened.sh into /usr/bin

# Grant adequate permissions (550 mean root and group www-data can read and execute, nobody can write)
# sudo chown root:www-data /usr/bin/suspend_unsuspened.sh
# sudo chmod 550 /usr/bin/suspend_unsuspened.sh
# And allow apache to sudo on this script by doing visudo to add line:
#www-data        ALL=(ALL) NOPASSWD: /usr/bin/suspend_unsuspened.sh

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

export mode=$1
export osusername=$2
export ospassword=$3
export instancename=$4
export domainname=$5

export targetdirwithsources1=${6}
export targetdir=${6}

export instancedir=$targetdir/$osusername/$dbname
export fqn=$instancename.$domainname

# For debug
echo "...input params..."
echo "mode = $mode"
echo "osusername = $osusername"
echo "instancename = $instancename"
echo "domainname = $domainname"
echo "targetdirforconfig1 = $targetdirforconfig1"
echo "targetdir = $targetdir"
echo "...calculated params..."
echo "instancedir = $instancedir"
echo "fqn = $fqn"



# Suspend

if [[ "$mode" == "suspend" ]]; then
echo "***** Suspend instance in /home/jail/home/$osusername"


fi


# Suspend

if [[ "$mode" == "unsuspend" ]]; then
echo "***** Unsuspend instance in /home/jail/home/$osusername"


fi

	
exit 0
