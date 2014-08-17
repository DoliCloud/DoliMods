-- Clear data structure we should not get when module has been disabled.

ALTER table llx_cabinetmed_cons DROP foreign key fk_cabinetmed_cons_fk_soc;

DELETE from llx_cabinetmed_cons where fk_soc NOT IN (select rowid from llx_societe);

DROP TABLE llx_cabinetmed_c_banques;
DROP TABLE llx_cabinetmed_c_ccam;
DROP TABLE llx_cabinetmed_c_examconclusion;
DROP TABLE llx_cabinetmed_cons;
DROP TABLE llx_cabinetmed_diaglec;
DROP TABLE llx_cabinetmed_examaut;
DROP TABLE llx_cabinetmed_exambio;
DROP TABLE llx_cabinetmed_examenprescrit;
DROP TABLE llx_cabinetmed_motifcons;
DROP TABLE llx_cabinetmed_patient;
DROP TABLE llx_cabinetmed_societe;

