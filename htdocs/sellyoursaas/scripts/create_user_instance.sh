#!/bin/bash

# To use this script:
# Create a symbolic link to this file .../create_user_instance.sh into /usr/bin

# Grant adequate permissions (550 mean root and group www-data can read and execute, nobody can write)
# sudo chown root:www-data /usr/bin/create_user_instance.sh
# sudo chmod 550 /usr/bin/create_user_instance.sh
# And allow apache to sudo on this script by doing visudo to add line:
#www-data        ALL=(ALL) NOPASSWD: /usr/bin/create_user_instance.sh


echo 
echo "# user id ----------------> $(id -u)"
echo "# PWD --------------------> $PWD" 
echo "# arguments called with --> ${@}"
echo "# path to me -------------> ${0}"
echo "# parent path ------------> ${0%/*}"
echo "# my name ----------------> ${0##*/}"
echo "# realname ---------------> $(realpath ${0})"
echo "# realname name ----------> $(basename $(realpath ${0}))"
echo "# realname dir -----------> $(dirname $(realpath ${0}))"

export scriptdir=$(dirname $(realpath ${0}))
export vhostfile="$scriptdir/templates/vhostHttps-dolibarr.template"
export targetdir="/home/jail/home"				
export osusername="$1"
export domain="$2"


if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   #exit 1
fi

if [ "x$1" == "x" ]; then
	echo "Missing parameter 1 - osusername" 1>&2
fi
if [ "x$2" == "x" ]; then
	echo "Missing parameter 2 - instancename" 1>&2
fi

export osusername=$1
export instancename=$1

echo "vhostfile = $vhostfile"
echo "targetdir = $targetdir"
echo "osusername = $osusername"
echo "instancename = $instancename"



# Create user and directory
if [[ -d $targetdir/$osusername ]]
then
	echo "Dir $targetdir/$osusername already exists"
else
	echo "Create dir $targetdir/$osusername"
	useradd -m -d $targetdir/$osusername $osusername
	mkdir -p "$targetdir/$osusername/$instancename"
	echo "HTML test page for $osusername" > "$targetdir/$osusername/$instancename/test.html"
fi

#if [[ ! -L "$targetdir/$osusername/$instancename/dolibarrtest" ]]
#then
#	echo "Add link $targetdir/$osusername/$instancename/dolibarrtest to dolibarr files"
#	ln -fs /home/ldestailleur/git/dolibarr/htdocs "$targetdir/$osusername/$instancename/dolibarrtest"
#fi
	
export apacheconf="/etc/apache2/sites-available/$instancename.conf"
if [[ -f $apacheconf ]]
then
	echo "Apache conf $apacheconf already exists"
else
	echo "Create apache conf $apacheconf from $vhostfile"
	cat $vhostfile | sed -e "s/@webAppDomain@/$instancename/g" | \
		  sed -e "s/@webAppAliases@/$instancename/g" | \
		  sed -e "s/@webAppLogName@/$instancename/g" | \
		  sed -e "s/@osUsername@/$osusername/g" | \
		  sed -e "s/@osGroupname@/$osusername/g" | \
		  sed -e 's%@webAppPath@%$targetdir%' > $apacheconf
	echo a2ensite $domain.conf
	a2ensite $domain.conf
	#/usr/sbin/apache2ctl configtest
fi

#if ! grep test_$i /etc/hosts >/dev/null; then
#	echo Add name test_$i into /etc/hosts
#	echo 127.0.0.1 test_$i >> /etc/hosts
#fi
	

#echo "Finished. To launch apache: service apache2 reload"
