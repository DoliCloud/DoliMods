#!/bin/bash


# Get database saasplex localy.


# Build filetomigrate
# select * from app_instance as a, customer as c  where a.customer_id = c.id and a.status = 'DEPLOYED' AND ip_address <> '79.137.96.15';
# Keep the file



for fic in `ls filetomigrate`
do
	./old_migrate_v1v2.php  xxx  zzz
done



# update /etc/bind/on.dolicloud.com to replace 'A   176.9.35.249' and 'A   176.34.178.16'  into  'A   79.137.96.15'
# update customer set manual_collection = 1 where id in (select customer_id from app_instance where name = 'testldr5');
# update app_instance set ip_address = '79.137.96.15', db_server = '79.137.96.15' where name = 'testldr5';
# update record set address = '79.137.96.15' where address <> '79.137.96.15' AND domain_id IN (select id from domain where sld = 'testldr5');


