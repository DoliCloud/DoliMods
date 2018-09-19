#!/bin/bash
#---------------------------------------------------------
# Script to make all instances offline
#---------------------------------------------------------

if [ "x$2" == "x" ]; then
   echo "Usage:   $0  newurl  test|offline|online"
   echo "Example: $0  https://myaccount.dolicloud.com/offline.php  test"
   exit 1
fi

realdir=$(dirname $(dirname $(realpath ${0})))

echo "Loop on each enabled virtual host, create a new one and switch it"
echo "Path for template is $realdir"
mkdir /etc/apache2/sellyoursaas-offline 2>/dev/null

for file in `ls /etc/apache2/sellyoursaas-online/*`
do
        echo -- Process file $file
		export fileshort=`basename $file`
		export domain=$(echo $fileshort | /bin/sed "s/\.conf//g")
        
        rm -f /etc/apache2/sellyoursaas-offline/$domain.conf 2>/dev/null
        rm -f /etc/apache2/sellyoursaas-offline/$domain.custom.conf 2>/dev/null
		
		echo Create file /etc/apache2/sellyoursaas-offline/$domain.conf for domain $domain
		cat $realdir/scripts/templates/vhostHttps-sellyoursaas-offline.template | \
			sed 's!__webAppDomain__!'$domain'!g' | \
			sed 's!__webMyAccount__!'$1'!g' \
			> /etc/apache2/sellyoursaas-offline/$domain.conf
	
		cat $realdir/scripts/templates/vhostHttps-sellyoursaas-offline.template | \
			sed 's!__webAppDomain__!'$domain'!g' | \
			sed 's!__webMyAccount__!'$1'!g' \
			> /etc/apache2/sellyoursaas-offline/$domain.custom.conf
done

if [ "x$2" = "xoffline" ]; then
	rm /etc/apache2/sellyoursaas-enabled
	ln -fs /etc/apache2/sellyoursaas-offline /etc/apache2/sellyoursaas-enabled
	
	echo Reload Apache
	/etc/init.d/apache2 reload 
fi

if [ "x$2" = "xonline" ]; then
	rm /etc/apache2/sellyoursaas-enabled
	ln -fs /etc/apache2/sellyoursaas-online /etc/apache2/sellyoursaas-enabled
	
	echo Reload Apache
	/etc/init.d/apache2 reload 
fi

if [ "x$2" != "xoffline" -a "x$2" != "xonline" ]; then
	echo Nothing more done. We are in test mode.
fi

echo "Finished."
