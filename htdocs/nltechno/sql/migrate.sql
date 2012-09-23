
alter table llx_dolicloud_customers modify column status varchar(16);
alter table llx_dolicloud_customers add column remind_trial_expired datetime default NULL;
alter table llx_dolicloud_customers add column remind_trial_closed datetime default NULL;
alter table llx_dolicloud_customers add column paymentmethod varchar(16);
alter table llx_dolicloud_customers add column paymentinfo varchar(255);
alter table llx_dolicloud_customers add column paymentstatus varchar(16);
    
update llx_dolicloud_customers set status = 'ACTIVE' where status IS NULL or status = '';

