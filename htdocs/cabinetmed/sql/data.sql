
--INSERT INTO llx_const (name, value, type, note, visible, entity) VALUES ('MYMODULE_IT_WORKS','1','chaine','A constant vor my module',1,'__ENTITY__');

-- New action type
insert into llx_c_actioncomm (id, code, type, libelle, module, position) values (100700, 'AC_CABMED', 'module', 'Send document by email', 'cabinetmed', 100);

-- Type de tiers -> Sexe
--update llx_c_typent set module = 'cabinetmed' where id > 100;
insert into llx_c_typent (id,code,libelle,active,module) values (101,'TE_HOMME', 'Homme',            1,'cabinetmed');
insert into llx_c_typent (id,code,libelle,active,module) values (102,'TE_FEMME', 'Femme',            1,'cabinetmed');
update llx_c_typent set active=0 where module != 'cabinetmed' and code != 'TE_UNKNOWN';


-- Formes juridiques -> Secteur d'activité
--update llx_c_forme_juridique set module = 'cabinetmed' where rowid > 100000;
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active,module) values (100001, 100001, 1,'Etudiant',            1,'cabinetmed');
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active,module) values (100002, 100002, 1,'Retraité',            1,'cabinetmed');
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active,module) values (100003, 100003, 1,'Artisan',             1,'cabinetmed');
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active,module) values (100004, 100004, 1,'Femme de ménage',     1,'cabinetmed');
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active,module) values (100005, 100005, 1,'Professeur',          1,'cabinetmed');
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active,module) values (100006, 100006, 1,'Profession libérale', 1,'cabinetmed');
insert into llx_c_forme_juridique (rowid,code,fk_pays,libelle,active,module) values (100007, 100007, 1,'Informaticien',       1,'cabinetmed');
update llx_c_forme_juridique set active=0 where module != 'cabinetmed';


-- llx_cabinetmed_motifcons
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,position,active) VALUES (5,'AUTRE','Autre',1,1);
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
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (17,'DOUL_MIN','Douleur Membre Inférieur',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (18,'POLYAR','Polyarthralgie',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (19,'SUIVIPR','Suivi PR',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (20,'SUIVISPA','Suivi SPA',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (21,'SUIVIRIC','Suivi RI',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (22,'SUIVIPPR','Suivi PPR',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (23,'DOLINGD','Douleur inguinale Droit',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (24,'DOLINGG','Douleur inguinale Gauche',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (25,'DOLCOUDD','Douleur coude Droit',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (26,'DOLCOUDG','Douleur coude Gauche',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (27,'TALAL','Talalgie',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (28,'DOLTENDC','Douleur tandous Calcanien',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (29,'DEROB','Dérobement Membres Inférieurs',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (30,'LOMB_MEC','Lombalgies Mécaniques',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (31,'LOMB_INF','Lombalgies Inflammatoires',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (32,'DORS_MEC','Dorsalgies Mécaniques',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (33,'DORS_INF','Dorsalgies Inflammatoires',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (34,'CERV_MEC','Cervicalgies Mécaniques',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (35,'SCIAT','LomboSciatique ',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (36,'CRUR','LomboCruralgie',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (37,'DOUL_SUP','Douleur Membre Supérieur',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (38,'INGUINAL','Inguinalgie',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (39,'CERV_INF','Cervicalgies Inflammatoires',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (40,'DOUL_EP','Douleur Epaule',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (41,'DOUL_POI','Douleur Poignet',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (42,'DOUL_GEN','Douleur Genou',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (43,'DOUL_COU','Douleur Coude',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (44,'DOUL_HAN','Douleur Hanche',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (45,'PAR_MBRS','Paresthésies Membres Inférieurs',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (46,'PAR_MBRI','Paresthésies Membres Supérieurs',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (47,'TR_RACHI','Traumatisme Rachis',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (48,'TR_MBRS','Traumatisme Membres Supérieurs',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (49,'TR_MBRI','Traumatisme Membres Inférieurs',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (50,'FAT_MBRI','Fatiguabilité Membres Inférieurs',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (51,'DOUL_CHE','Douleur Cheville',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (52,'DOUL_PD','Douleur Pied',1);
INSERT INTO llx_cabinetmed_motifcons (rowid,code,label,active) VALUES (53,'DOUL_MA','Douleur Main',1);


-- llx_cabinetmed_diaglec
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,position,active) VALUES ( 1,'AUTRE','Autre',1,1);
insert into llx_cabinetmed_diaglec (rowid,code,label,active) values ( 2,'LOMBL5D', 'Lombosciatique L5 droite',1);
insert into llx_cabinetmed_diaglec (rowid,code,label,active) values ( 3,'LOMBL5G', 'Lombosciatique L5 gauche',1);
insert into llx_cabinetmed_diaglec (rowid,code,label,active) values ( 4,'LOMBS1D', 'Lombosciatique S1 droite',1);
insert into llx_cabinetmed_diaglec (rowid,code,label,active) values ( 5,'LOMBS1G', 'Lombosciatique S1 gauche',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES ( 6,'NCB','Névralgie cervico-brachiale',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES ( 7,'PR','Polyarthrite rhumatoide',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES ( 8,'SA','Spondylarthrite ankylosante',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES ( 9,'GFTI','Gonarthrose fémoro-tibaile interne',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (10,'GFTE','Gonarthrose fémoro-tibiale externe',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (11,'COX','Coxarthrose',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (12,'CC','Canal Carpien',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (16,'CLER','Canal Lombaire Etroit et/ou  Rétréci',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (22,'RH_PSO','Rhumatisme Psoriasique',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (23,'LEAD','Lupus',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (24,'LBDISC','Lombalgie Discale',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (25,'LBRADD','Lomboradiculalgie Discale',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (26,'LBRADND','Lomboradiculalgie Non Discale',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (27,'CH_ROT','Chondropathie Rotulienne',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (28,'AFP','Arthrose FémoroPatellaire',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (29,'PPR','Pseudo Polyarthrite Rhizomélique',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (30,'SHARP','Maladie de Sharp',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (31,'SAPHO','SAPHO',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (32,'OMARTHC','Omarthrose Centrée',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (33,'RH_CCA','Rhumatisme Chondro Calcinosique',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (34,'GOUTTE','Arthrite Goutteuse',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (35,'CCA','Arthrite Chondro Calcinosique',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (36,'ARTH_MCR','Arthrite Microcristalline',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (37,'CSA','Conflit Sous Acromial',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (38,'TDCALCE','Tendinopathie Calcifiante d''Epaule',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (39,'TDCALCH','Tendinopathie Calcifiante de Hanche',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (40,'TBT','TendinoBursite Trochantérienne',1);
INSERT INTO llx_cabinetmed_diaglec (rowid,code,label,active) VALUES (41,'OMARTHE','Omarthrose Excentrée',1);


-- llx_cabinetmed_examenprescrit
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,position,active) VALUES (1,'AUTRE','Autre','OTHER',1,1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (2,'IRMLOMB','IRM lombaire','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (5,'TDMLOMB','TDM lombaires','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (6,'RX_BRL','Radios Bassin-Rachis Lombaire','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (7,'RX_RL','Radios Rachis Lombaire','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (8,'RX_BASS','Radios Bassin','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (9,'RX_BH','Radios Bassin et Hanches','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (10,'RX_GEN','Radios Genoux','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (11,'RX_CHEV','Radios Chevilles','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (12,'RX_AVPD','Radios Avants-Pieds','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (13,'RX_EP','Radio Epaule','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (14,'RX_MAINS','Radios Mains','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (15,'RX_COUDE','Radios Coude','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (16,'RX_RC','Radios Rachis Cervical','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (17,'RX_RD','Radios Rachis Dorsal','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (18,'RX_RCD','Radios Rachis CervicoDorsal','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (19,'RX_RDL','Radios DorsoLombaire','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (20,'RX_SCO','Bilan Radio Scoliose','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (21,'RX_RIC','Bilan Radio Rhumatisme  Inflammatoire','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (22,'TDM_LOMB','Scanner Lombaire','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (23,'TDM_DORS','Scanner Dorsal','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (24,'TDM_CERV','Scanner Cervical','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (25,'TDM_HANC','Scanner Hanche','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (26,'TDM_GEN','Scanner Genou','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (27,'RX_RDL','Radios Rachis DorsoLombaire','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (28,'ARTTDMG','ArthroScanner Genou','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (29,'ARTTDME','ArthroScanner Epaule','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (30,'ARTTDMH','ArthroScanner Hanche','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (31,'IRM_GEN','IRM Genou','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (32,'IRM_HANC','IRM Hanche','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (33,'IRM_EP','IRM Epaule','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (34,'IRM_SIL','IRM SacroIliaques','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (35,'IRM_RL','IRM Rachis Lombaire','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (36,'IRM_RD','IRM Rachis Dorsal','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (37,'IRM_RC','IRM Rachis Cervical','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (38,'ELECMI','Electromiogramme','RADIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (39,'NFS','NFS','BIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (40,'BILPHO','Bilan Phosphocalcique','BIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (41,'VSCRP','VS/CRP','BIO',1);
INSERT INTO llx_cabinetmed_examenprescrit (rowid,code,label,biorad,active) VALUES (42,'EPP','Electrophorèse Protéine Plasmatique','BIO',1);


-- llx_cabinetmed_c_examconclusion
INSERT INTO llx_cabinetmed_c_examconclusion (rowid,code,label,position,active) VALUES (1,'AUTRE','Autre',1,1);


-- Add type pour lien societe-contact
--update llx_c_type_contact set module = 'cabinetmed' where rowid >= 200;
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (200,'societe','external','GENERALREF', 'Généraliste (référent)',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (201,'societe','external','GENERALISTE','Généraliste',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (210,'societe','external','SPECCHIROR', 'Chirurgien ortho',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (211,'societe','external','SPECCHIROT', 'Chirurgien autre',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (220,'societe','external','SPECDERMA',  'Dermatologue',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (225,'societe','external','SPECENDOC',  'Endocrinologue',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (230,'societe','external','SPECGYNECO', 'Gynécologue',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (240,'societe','external','SPECGASTRO', 'Gastroantérologue',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (245,'societe','external','SPECINTERNE','Interniste',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (250,'societe','external','SPECCARDIO', 'Cardiologue',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (260,'societe','external','SPECNEPHRO', 'Néphrologue',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (263,'societe','external','SPECPNEUMO', 'Pneumologue',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (265,'societe','external','SPECNEURO',  'Neurologue',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (270,'societe','external','SPECRHUMATO','Rhumatologue',1,'cabinetmed');
INSERT INTO llx_c_type_contact (rowid,element,source,code,libelle,active,module) VALUES (280,'societe','external','KINE',       'Kinésithérapeute',1,'cabinetmed');
update llx_c_type_contact set active=0 where element='societe' and source='external' and module != 'cabinetmed';

