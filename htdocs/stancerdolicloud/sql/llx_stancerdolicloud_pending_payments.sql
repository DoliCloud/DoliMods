create table llx_stancerdolicloud_pending_payments
(
  rowid         integer AUTO_INCREMENT PRIMARY KEY,
  ref_payment   varchar(23) NOT NULL,
  data          text NOT NULL,
  date_creation datetime NOT NULL
)ENGINE=innodb;