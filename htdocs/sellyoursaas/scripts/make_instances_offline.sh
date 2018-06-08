#!/bin/bash
#---------------------------------------------------------
# Script to update sources found into document dir
#
# To include into cron
# /pathto/git_update_sources.sh documentdir/sellyoursaas/git > /pathto/git_update_sources.log 2>&
#---------------------------------------------------------

if [ "x$2" == "x" ]; then
   echo "Usage:   $0  newurl  test|offline|online"
   echo "Example: $0  https://myaccount/dolicloud.com/offline.php  test"
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
        
        rm -f /etc/apache2/sellyoursaas-offline/$fileshort 2>/dev/null
		
		echo Create file /etc/apache2/sellyoursaas-offline/$fileshort for domain $domain
		cat $realdir/scripts/templates/vhostHttps-sellyoursaas-offline.template | \
			sed 's!__webAppDomain__!'$domain'!g' | \
			sed 's!__webMyAccount__!'$1'!g' \
			> /etc/apache2/sellyoursaas-offline/$fileshort
		
        #if [ -s build/generate_filelist_xml.php ]; then
        #        echo "Found generate_filelist_xml.php"
        #        php build/generate_filelist_xml.php release=auto-dolicloud
        #fi

        #cd -
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

echo "Finished."
