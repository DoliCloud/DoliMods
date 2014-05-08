-- Clear data structure we should not get when module has been disabled.

ALTER table llx_cabinetmed_cons DROP foreign key fk_cabinetmed_cons_fk_soc;

DELETE from llx_cabinetmed_cons where fk_soc NOT IN (select rowid from llx_societe);
