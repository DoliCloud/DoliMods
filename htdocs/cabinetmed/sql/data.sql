
--INSERT INTO llx_const (name, value, type, note, visible, entity) VALUES ('MYMODULE_IT_WORKS','1','chaine','A constant vor my module',1,'__ENTITY__');


-- Sexe into type
insert into llx_c_typent (id,code,libelle,active) values (101,'TE_HOMME', 'Homme',            1);
insert into llx_c_typent (id,code,libelle,active) values (102,'TE_FEMME', 'Femme',            1);
update llx_c_typent set active=0 where id > 0 and id < 100;


-- Secteur d'activité dans Forme juridiques
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active) values (100001, 100001, 1,'Etudiant',  1);
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active) values (100002, 100002, 1,'Retraité',     1);
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active) values (100003, 100003, 1,'Artisan',  1);
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active) values (100004, 100004, 1,'Femme de ménage',  1);
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active) values (100005, 100005, 1,'Professeur',  1);
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active) values (100006, 100006, 1,'Profession libérale',  1);
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active) values (100007, 100007, 1,'Informaticien',  1);
update llx_c_forme_juridique set active=0 where rowid < 100000;


insert into llx_c_effectif (id,code,libelle) values (100, 'EFTS',     'TS');
insert into llx_c_effectif (id,code,libelle) values (101, 'EFTNS',    'TNS');
insert into llx_c_effectif (id,code,libelle) values (102, 'EFCMU',    'CMU');
update llx_c_effectif set active=0 where id < 100 and id != 0;


-- llx_cabinetmed_motifcons
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (5,'CERV','Cervicalgie',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (6,'DORS','Dorsalgie',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (7,'DOLMSD','Douleur Membre supérieur Droit',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (8,'DOLMSG','Douleur Membre supérieur Gauche',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (9,'DOLMID','Douleur Membre inférieur Droit',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (10,'DOLMIG','Douleur Membre inférieur Gauche',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (11,'PARESM','Paresthésie des mains',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (12,'DOLEPG','Douleur épaule gauche',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (13,'DOLEPD','Douleur épaule droite',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (14,'GONAD','Gonaglie droite',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (15,'GONAG','Gonalgie gauche',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (16,'DOLPD','Douleur Pied Droit',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (17,'DOLPG','Douleur Pied Gauche',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (18,'POLYAR','Polyarthralgie',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (19,'SUIVIPR','Suivi PR',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (20,'SUIVISPA','Suivi SPA',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (21,'SUIVIRI','Suivi RI',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (22,'SUIVIPPR','Suivi PPR',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (23,'DOLINGD','Douleur inguinale Droit',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (24,'DOLINGG','Douleur inguinale Gauche',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (25,'DOLCOUDD','Douleur coude Droit',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (26,'DOLCOUDG','Douleur coude Gauche',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (27,'TALAL','Talalgie',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (28,'DOLTENDC','Douleur tandous Calcanien',1);

-- llx_cabinetmed_diaglec
insert into llx_cabinetmed_diaglec (rowid,code,label) values (1, 'LOMBL5D', 'Lombosciatique L5 droite');
insert into llx_cabinetmed_diaglec (rowid,code,label) values (2, 'LOMBL5G', 'Lombosciatique L5 gauche');
insert into llx_cabinetmed_diaglec (rowid,code,label) values (3, 'LOMBS1D', 'Lombosciatique S1 droite');
insert into llx_cabinetmed_diaglec (rowid,code,label) values (4, 'LOMBS1G', 'Lombosciatique S1 gauche');

-- llx_cabinetmed_examenprescrit
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,active) VALUES (1,'IRMLOMB','IRM lombaire',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,active) VALUES (2,'RADBASS','Radiographie bassin',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,active) VALUES (3,'RADRACH','Radiographie rachis',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,active) VALUES (4,'TDMLOMB','TDM lombaires',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,active) VALUES (5,'AUTRE','Autre',1);


-- Add type pour lien societe
delete from llx_c_type_contact where rowid >= 200;
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active) VALUES (200,'societe','external','GENERALISTE','Généraliste',1);
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active) VALUES (201,'societe','external','SPECCHIROR','Chirurgien ortho',1);
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active) VALUES (202,'societe','external','SPECDERMA','Dermatologue',1);
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active) VALUES (203,'societe','external','SPECGYNECO','Gynécologue',1);
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active) VALUES (204,'societe','external','SPECCARDIO','Cardiologue',1);
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active) VALUES (205,'societe','external','SPECNEPHRO','Néphrologue',1);
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active) VALUES (206,'societe','external','SPECRHUMATO','Rhumatologue',1);




