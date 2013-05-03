alter table llx_cabinetmed_diaglec add column icd			    varchar(12) NULL;
alter table llx_cabinetmed_diaglec add column lang				varchar(12) NULL;

alter table llx_cabinetmed_patient add column alert_antemed       smallint;
alter table llx_cabinetmed_patient add column alert_antechirgen   smallint;
alter table llx_cabinetmed_patient add column alert_antechirortho smallint;
alter table llx_cabinetmed_patient add column alert_anterhum      smallint;
alter table llx_cabinetmed_patient add column alert_other         smallint;
alter table llx_cabinetmed_patient add column alert_traitclass    smallint;
alter table llx_cabinetmed_patient add column alert_traitallergie smallint;
alter table llx_cabinetmed_patient add column alert_traitintol    smallint;
alter table llx_cabinetmed_patient add column alert_traitspec     smallint;
alter table llx_cabinetmed_patient add column alert_note          smallint;

alter table llx_cabinetmed_cons    add column fk_user            integer;
alter table llx_cabinetmed_exambio add column fk_user            integer;
alter table llx_cabinetmed_examaut add column fk_user            integer;

alter table llx_cabinetmed_cons add column fk_user_m          integer;
alter table llx_cabinetmed_cons add column date_c             datetime NOT NULL;

