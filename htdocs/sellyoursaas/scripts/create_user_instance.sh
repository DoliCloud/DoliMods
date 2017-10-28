#!/bin/bash

echo 
echo "# PWD ---------------------->  $PWD     " 
echo "# arguments called with ---->  ${@}     "
echo "# \$1 ---------------------->  $1       "
echo "# \$2 ---------------------->  $2       "
echo "# path to me --------------->  ${0}     "
echo "# parent path -------------->  ${0%/*}  "
echo "# my name ------------------>  ${0##*/} "



export vhostfile="templates/vhostHttps-dolibarr.template"
export targetdir="/home/test"
export contractname="aaaa"
export domain="ddd"

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

export osusername='aaa'

# Create user and directory
if [[ -d $targetdir/$osusernamei/htdocs ]]
then
	echo "Dir $targetdir/$osusername/htdocs already exists"
else
	echo "Create dir $targetdir/$osusername/htdocs"
	useradd -m -d $targetdir/$osusername $osusername
	mkdir -p "$targetdir/$osusername/htdocs"
	echo "HTML test page for $osusername" > "$targetdir/$osusername/htdocs/test.html"
fi

#if [[ ! -L "$targetdir/$osusername/htdocs/dolibarrtest" ]]
#then
#	echo "Add link $targetdir/test_$i/htdocs/dolibarrtest to dolibarr files"
#	ln -fs /home/ldestailleur/git/dolibarr/htdocs "$targetdir/test_$i/htdocs/dolibarrtest"
#fi
	
export apacheconf="/etc/apache2/sites-available/$domain.conf"
if [[ -f $apacheconf ]]
then
	echo "Apache conf $apacheconf already exists"
else
	echo "Create apache conf $apacheconf from $vhostfile"
	cat $vhostfile | sed -e "s/@webAppDomain@/$domain/g" | \
		  sed -e "s/@webAppAliases@/$domain/g" | \
		  sed -e "s/@webAppLogName@/$domain/g" | \
		  sed -e "s/@osUsername@/$osusername/g" | \
		  sed -e "s/@osGroupname@/$osusername/g" | \
		  sed -e "s/@webAppPath@/\/home\/test\/test_$i/" > $apacheconf
	a2ensite $domain.conf
fi

#if ! grep test_$i /etc/hosts >/dev/null; then
#	echo Add name test_$i into /etc/hosts
#	echo 127.0.0.1 test_$i >> /etc/hosts
#fi
	

#echo "Finished. To launch apache: service apache2 reload"

