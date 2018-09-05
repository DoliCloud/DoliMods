#!/bin/bash


# Get database saasplex localy.


# Build filetomigrate-nonmanual of paying instance with manual_collection != 1 and status = 'ACTIVE'
# Keep the file


# Build filetomigrate-manual of paying instance with manual_collection = 1 
# Keep the file



# update customer set manual_collection = 1 where id in (select customer_id from app_instance where name like 'testldr5%');
# update app_instance set ip_address = '79.137.96.15', db_server = '79.137.96.15' where name like 'testldr5.%';



for fic in `ls filetomigrate`
do
	./old_migrate_v1v2.php  xxx   zzz
done



# update /etc/bind/on.dolicloud.com to replace 'A   176.9.35.249' and 'A   176.34.178.16'  into  'A   79.137.96.15'
# make file /etc/bind/on.dolicloud.com as a readonly file



