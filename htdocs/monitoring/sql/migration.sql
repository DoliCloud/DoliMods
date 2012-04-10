alter table llx_monitoring_probes ADD column typeprot varchar(16) DEFAULT 'GET' after rowid;
alter table llx_monitoring_probes ADD column url_params text after url;

