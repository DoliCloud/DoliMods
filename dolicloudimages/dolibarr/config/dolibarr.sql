-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dolicloud
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `llx_accountingaccount`
--

DROP TABLE IF EXISTS `llx_accountingaccount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_accountingaccount` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_pcg_version` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `pcg_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `pcg_subtype` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `account_number` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `account_parent` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_accountingaccount_fk_pcg_version` (`fk_pcg_version`),
  CONSTRAINT `fk_accountingaccount_fk_pcg_version` FOREIGN KEY (`fk_pcg_version`) REFERENCES `llx_accountingsystem` (`pcg_version`)
) ENGINE=InnoDB AUTO_INCREMENT=439 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_accountingaccount`
--

LOCK TABLES `llx_accountingaccount` WRITE;
/*!40000 ALTER TABLE `llx_accountingaccount` DISABLE KEYS */;
INSERT INTO `llx_accountingaccount` VALUES (1,'PCG99-ABREGE','CAPIT','CAPITAL','101','1','Capital'),(2,'PCG99-ABREGE','CAPIT','XXXXXX','105','1','Ecarts de réévaluation'),(3,'PCG99-ABREGE','CAPIT','XXXXXX','1061','1','Réserve légale'),(4,'PCG99-ABREGE','CAPIT','XXXXXX','1063','1','Réserves statutaires ou contractuelles'),(5,'PCG99-ABREGE','CAPIT','XXXXXX','1064','1','Réserves réglementées'),(6,'PCG99-ABREGE','CAPIT','XXXXXX','1068','1','Autres réserves'),(7,'PCG99-ABREGE','CAPIT','XXXXXX','108','1','Compte de l\'exploitant'),(8,'PCG99-ABREGE','CAPIT','XXXXXX','12','1','Résultat de l\'exercice'),(9,'PCG99-ABREGE','CAPIT','XXXXXX','145','1','Amortissements dérogatoires'),(10,'PCG99-ABREGE','CAPIT','XXXXXX','146','1','Provision spéciale de réévaluation'),(11,'PCG99-ABREGE','CAPIT','XXXXXX','147','1','Plus-values réinvesties'),(12,'PCG99-ABREGE','CAPIT','XXXXXX','148','1','Autres provisions réglementées'),(13,'PCG99-ABREGE','CAPIT','XXXXXX','15','1','Provisions pour risques et charges'),(14,'PCG99-ABREGE','CAPIT','XXXXXX','16','1','Emprunts et dettes assimilees'),(15,'PCG99-ABREGE','IMMO','XXXXXX','20','2','Immobilisations incorporelles'),(16,'PCG99-ABREGE','IMMO','XXXXXX','201','20','Frais d\'établissement'),(17,'PCG99-ABREGE','IMMO','XXXXXX','206','20','Droit au bail'),(18,'PCG99-ABREGE','IMMO','XXXXXX','207','20','Fonds commercial'),(19,'PCG99-ABREGE','IMMO','XXXXXX','208','20','Autres immobilisations incorporelles'),(20,'PCG99-ABREGE','IMMO','XXXXXX','21','2','Immobilisations corporelles'),(21,'PCG99-ABREGE','IMMO','XXXXXX','23','2','Immobilisations en cours'),(22,'PCG99-ABREGE','IMMO','XXXXXX','27','2','Autres immobilisations financieres'),(23,'PCG99-ABREGE','IMMO','XXXXXX','280','2','Amortissements des immobilisations incorporelles'),(24,'PCG99-ABREGE','IMMO','XXXXXX','281','2','Amortissements des immobilisations corporelles'),(25,'PCG99-ABREGE','IMMO','XXXXXX','290','2','Provisions pour dépréciation des immobilisations incorporelles'),(26,'PCG99-ABREGE','IMMO','XXXXXX','291','2','Provisions pour dépréciation des immobilisations corporelles'),(27,'PCG99-ABREGE','IMMO','XXXXXX','297','2','Provisions pour dépréciation des autres immobilisations financières'),(28,'PCG99-ABREGE','STOCK','XXXXXX','31','3','Matieres premières'),(29,'PCG99-ABREGE','STOCK','XXXXXX','32','3','Autres approvisionnements'),(30,'PCG99-ABREGE','STOCK','XXXXXX','33','3','En-cours de production de biens'),(31,'PCG99-ABREGE','STOCK','XXXXXX','34','3','En-cours de production de services'),(32,'PCG99-ABREGE','STOCK','XXXXXX','35','3','Stocks de produits'),(33,'PCG99-ABREGE','STOCK','XXXXXX','37','3','Stocks de marchandises'),(34,'PCG99-ABREGE','STOCK','XXXXXX','391','3','Provisions pour dépréciation des matières premières'),(35,'PCG99-ABREGE','STOCK','XXXXXX','392','3','Provisions pour dépréciation des autres approvisionnements'),(36,'PCG99-ABREGE','STOCK','XXXXXX','393','3','Provisions pour dépréciation des en-cours de production de biens'),(37,'PCG99-ABREGE','STOCK','XXXXXX','394','3','Provisions pour dépréciation des en-cours de production de services'),(38,'PCG99-ABREGE','STOCK','XXXXXX','395','3','Provisions pour dépréciation des stocks de produits'),(39,'PCG99-ABREGE','STOCK','XXXXXX','397','3','Provisions pour dépréciation des stocks de marchandises'),(40,'PCG99-ABREGE','TIERS','SUPPLIER','400','4','Fournisseurs et Comptes rattachés'),(41,'PCG99-ABREGE','TIERS','XXXXXX','409','4','Fournisseurs débiteurs'),(42,'PCG99-ABREGE','TIERS','CUSTOMER','410','4','Clients et Comptes rattachés'),(43,'PCG99-ABREGE','TIERS','XXXXXX','419','4','Clients créditeurs'),(44,'PCG99-ABREGE','TIERS','XXXXXX','421','4','Personnel'),(45,'PCG99-ABREGE','TIERS','XXXXXX','428','4','Personnel'),(46,'PCG99-ABREGE','TIERS','XXXXXX','43','4','Sécurité sociale et autres organismes sociaux'),(47,'PCG99-ABREGE','TIERS','XXXXXX','444','4','Etat - impôts sur bénéfice'),(48,'PCG99-ABREGE','TIERS','XXXXXX','445','4','Etat - Taxes sur chiffre affaire'),(49,'PCG99-ABREGE','TIERS','XXXXXX','447','4','Autres impôts, taxes et versements assimilés'),(50,'PCG99-ABREGE','TIERS','XXXXXX','45','4','Groupe et associes'),(51,'PCG99-ABREGE','TIERS','XXXXXX','455','45','Associés'),(52,'PCG99-ABREGE','TIERS','XXXXXX','46','4','Débiteurs divers et créditeurs divers'),(53,'PCG99-ABREGE','TIERS','XXXXXX','47','4','Comptes transitoires ou d\'attente'),(54,'PCG99-ABREGE','TIERS','XXXXXX','481','4','Charges à répartir sur plusieurs exercices'),(55,'PCG99-ABREGE','TIERS','XXXXXX','486','4','Charges constatées d\'avance'),(56,'PCG99-ABREGE','TIERS','XXXXXX','487','4','Produits constatés d\'avance'),(57,'PCG99-ABREGE','TIERS','XXXXXX','491','4','Provisions pour dépréciation des comptes de clients'),(58,'PCG99-ABREGE','TIERS','XXXXXX','496','4','Provisions pour dépréciation des comptes de débiteurs divers'),(59,'PCG99-ABREGE','FINAN','XXXXXX','50','5','Valeurs mobilières de placement'),(60,'PCG99-ABREGE','FINAN','BANK','51','5','Banques, établissements financiers et assimilés'),(61,'PCG99-ABREGE','FINAN','CASH','53','5','Caisse'),(62,'PCG99-ABREGE','FINAN','XXXXXX','54','5','Régies d\'avance et accréditifs'),(63,'PCG99-ABREGE','FINAN','XXXXXX','58','5','Virements internes'),(64,'PCG99-ABREGE','FINAN','XXXXXX','590','5','Provisions pour dépréciation des valeurs mobilières de placement'),(65,'PCG99-ABREGE','CHARGE','PRODUCT','60','6','Achats'),(66,'PCG99-ABREGE','CHARGE','XXXXXX','603','60','Variations des stocks'),(67,'PCG99-ABREGE','CHARGE','SERVICE','61','6','Services extérieurs'),(68,'PCG99-ABREGE','CHARGE','XXXXXX','62','6','Autres services extérieurs'),(69,'PCG99-ABREGE','CHARGE','XXXXXX','63','6','Impôts, taxes et versements assimiles'),(70,'PCG99-ABREGE','CHARGE','XXXXXX','641','6','Rémunérations du personnel'),(71,'PCG99-ABREGE','CHARGE','XXXXXX','644','6','Rémunération du travail de l\'exploitant'),(72,'PCG99-ABREGE','CHARGE','SOCIAL','645','6','Charges de sécurité sociale et de prévoyance'),(73,'PCG99-ABREGE','CHARGE','XXXXXX','646','6','Cotisations sociales personnelles de l\'exploitant'),(74,'PCG99-ABREGE','CHARGE','XXXXXX','65','6','Autres charges de gestion courante'),(75,'PCG99-ABREGE','CHARGE','XXXXXX','66','6','Charges financières'),(76,'PCG99-ABREGE','CHARGE','XXXXXX','67','6','Charges exceptionnelles'),(77,'PCG99-ABREGE','CHARGE','XXXXXX','681','6','Dotations aux amortissements et aux provisions'),(78,'PCG99-ABREGE','CHARGE','XXXXXX','686','6','Dotations aux amortissements et aux provisions'),(79,'PCG99-ABREGE','CHARGE','XXXXXX','687','6','Dotations aux amortissements et aux provisions'),(80,'PCG99-ABREGE','CHARGE','XXXXXX','691','6','Participation des salariés aux résultats'),(81,'PCG99-ABREGE','CHARGE','XXXXXX','695','6','Impôts sur les bénéfices'),(82,'PCG99-ABREGE','CHARGE','XXXXXX','697','6','Imposition forfaitaire annuelle des sociétés'),(83,'PCG99-ABREGE','CHARGE','XXXXXX','699','6','Produits'),(84,'PCG99-ABREGE','PROD','PRODUCT','701','7','Ventes de produits finis'),(85,'PCG99-ABREGE','PROD','SERVICE','706','7','Prestations de services'),(86,'PCG99-ABREGE','PROD','PRODUCT','707','7','Ventes de marchandises'),(87,'PCG99-ABREGE','PROD','PRODUCT','708','7','Produits des activités annexes'),(88,'PCG99-ABREGE','PROD','XXXXXX','709','7','Rabais, remises et ristournes accordés par l\'entreprise'),(89,'PCG99-ABREGE','PROD','XXXXXX','713','7','Variation des stocks'),(90,'PCG99-ABREGE','PROD','XXXXXX','72','7','Production immobilisée'),(91,'PCG99-ABREGE','PROD','XXXXXX','73','7','Produits nets partiels sur opérations à long terme'),(92,'PCG99-ABREGE','PROD','XXXXXX','74','7','Subventions d\'exploitation'),(93,'PCG99-ABREGE','PROD','XXXXXX','75','7','Autres produits de gestion courante'),(94,'PCG99-ABREGE','PROD','XXXXXX','753','75','Jetons de présence et rémunérations d\'administrateurs, gérants,...'),(95,'PCG99-ABREGE','PROD','XXXXXX','754','75','Ristournes perçues des coopératives'),(96,'PCG99-ABREGE','PROD','XXXXXX','755','75','Quotes-parts de résultat sur opérations faites en commun'),(97,'PCG99-ABREGE','PROD','XXXXXX','76','7','Produits financiers'),(98,'PCG99-ABREGE','PROD','XXXXXX','77','7','Produits exceptionnels'),(99,'PCG99-ABREGE','PROD','XXXXXX','781','7','Reprises sur amortissements et provisions'),(100,'PCG99-ABREGE','PROD','XXXXXX','786','7','Reprises sur provisions pour risques'),(101,'PCG99-ABREGE','PROD','XXXXXX','787','7','Reprises sur provisions'),(102,'PCG99-ABREGE','PROD','XXXXXX','79','7','Transferts de charges'),(103,'PCG99-BASE','CAPIT','XXXXXX','10','1','Capital  et réserves'),(104,'PCG99-BASE','CAPIT','CAPITAL','101','10','Capital'),(105,'PCG99-BASE','CAPIT','XXXXXX','104','10','Primes liées au capital social'),(106,'PCG99-BASE','CAPIT','XXXXXX','105','10','Ecarts de réévaluation'),(107,'PCG99-BASE','CAPIT','XXXXXX','106','10','Réserves'),(108,'PCG99-BASE','CAPIT','XXXXXX','107','10','Ecart d\'equivalence'),(109,'PCG99-BASE','CAPIT','XXXXXX','108','10','Compte de l\'exploitant'),(110,'PCG99-BASE','CAPIT','XXXXXX','109','10','Actionnaires : capital souscrit - non appelé'),(111,'PCG99-BASE','CAPIT','XXXXXX','11','1','Report à nouveau (solde créditeur ou débiteur)'),(112,'PCG99-BASE','CAPIT','XXXXXX','110','11','Report à nouveau (solde créditeur)'),(113,'PCG99-BASE','CAPIT','XXXXXX','119','11','Report à nouveau (solde débiteur)'),(114,'PCG99-BASE','CAPIT','XXXXXX','12','1','Résultat de l\'exercice (bénéfice ou perte)'),(115,'PCG99-BASE','CAPIT','XXXXXX','120','12','Résultat de l\'exercice (bénéfice)'),(116,'PCG99-BASE','CAPIT','XXXXXX','129','12','Résultat de l\'exercice (perte)'),(117,'PCG99-BASE','CAPIT','XXXXXX','13','1','Subventions d\'investissement'),(118,'PCG99-BASE','CAPIT','XXXXXX','131','13','Subventions d\'équipement'),(119,'PCG99-BASE','CAPIT','XXXXXX','138','13','Autres subventions d\'investissement'),(120,'PCG99-BASE','CAPIT','XXXXXX','139','13','Subventions d\'investissement inscrites au compte de résultat'),(121,'PCG99-BASE','CAPIT','XXXXXX','14','1','Provisions réglementées'),(122,'PCG99-BASE','CAPIT','XXXXXX','142','14','Provisions réglementées relatives aux immobilisations'),(123,'PCG99-BASE','CAPIT','XXXXXX','143','14','Provisions réglementées relatives aux stocks'),(124,'PCG99-BASE','CAPIT','XXXXXX','144','14','Provisions réglementées relatives aux autres éléments de l\'actif'),(125,'PCG99-BASE','CAPIT','XXXXXX','145','14','Amortissements dérogatoires'),(126,'PCG99-BASE','CAPIT','XXXXXX','146','14','Provision spéciale de réévaluation'),(127,'PCG99-BASE','CAPIT','XXXXXX','147','14','Plus-values réinvesties'),(128,'PCG99-BASE','CAPIT','XXXXXX','148','14','Autres provisions réglementées'),(129,'PCG99-BASE','CAPIT','XXXXXX','15','1','Provisions pour risques et charges'),(130,'PCG99-BASE','CAPIT','XXXXXX','151','15','Provisions pour risques'),(131,'PCG99-BASE','CAPIT','XXXXXX','153','15','Provisions pour pensions et obligations similaires'),(132,'PCG99-BASE','CAPIT','XXXXXX','154','15','Provisions pour restructurations'),(133,'PCG99-BASE','CAPIT','XXXXXX','155','15','Provisions pour impôts'),(134,'PCG99-BASE','CAPIT','XXXXXX','156','15','Provisions pour renouvellement des immobilisations (entreprises concessionnaires)'),(135,'PCG99-BASE','CAPIT','XXXXXX','157','15','Provisions pour charges à répartir sur plusieurs exercices'),(136,'PCG99-BASE','CAPIT','XXXXXX','158','15','Autres provisions pour charges'),(137,'PCG99-BASE','CAPIT','XXXXXX','16','1','Emprunts et dettes assimilees'),(138,'PCG99-BASE','CAPIT','XXXXXX','161','16','Emprunts obligataires convertibles'),(139,'PCG99-BASE','CAPIT','XXXXXX','163','16','Autres emprunts obligataires'),(140,'PCG99-BASE','CAPIT','XXXXXX','164','16','Emprunts auprès des établissements de crédit'),(141,'PCG99-BASE','CAPIT','XXXXXX','165','16','Dépôts et cautionnements reçus'),(142,'PCG99-BASE','CAPIT','XXXXXX','166','16','Participation des salariés aux résultats'),(143,'PCG99-BASE','CAPIT','XXXXXX','167','16','Emprunts et dettes assortis de conditions particulières'),(144,'PCG99-BASE','CAPIT','XXXXXX','168','16','Autres emprunts et dettes assimilées'),(145,'PCG99-BASE','CAPIT','XXXXXX','169','16','Primes de remboursement des obligations'),(146,'PCG99-BASE','CAPIT','XXXXXX','17','1','Dettes rattachées à des participations'),(147,'PCG99-BASE','CAPIT','XXXXXX','171','17','Dettes rattachées à des participations (groupe)'),(148,'PCG99-BASE','CAPIT','XXXXXX','174','17','Dettes rattachées à des participations (hors groupe)'),(149,'PCG99-BASE','CAPIT','XXXXXX','178','17','Dettes rattachées à des sociétés en participation'),(150,'PCG99-BASE','CAPIT','XXXXXX','18','1','Comptes de liaison des établissements et sociétés en participation'),(151,'PCG99-BASE','CAPIT','XXXXXX','181','18','Comptes de liaison des établissements'),(152,'PCG99-BASE','CAPIT','XXXXXX','186','18','Biens et prestations de services échangés entre établissements (charges)'),(153,'PCG99-BASE','CAPIT','XXXXXX','187','18','Biens et prestations de services échangés entre établissements (produits)'),(154,'PCG99-BASE','CAPIT','XXXXXX','188','18','Comptes de liaison des sociétés en participation'),(155,'PCG99-BASE','IMMO','XXXXXX','20','2','Immobilisations incorporelles'),(156,'PCG99-BASE','IMMO','XXXXXX','201','20','Frais d\'établissement'),(157,'PCG99-BASE','IMMO','XXXXXX','203','20','Frais de recherche et de développement'),(158,'PCG99-BASE','IMMO','XXXXXX','205','20','Concessions et droits similaires, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires'),(159,'PCG99-BASE','IMMO','XXXXXX','206','20','Droit au bail'),(160,'PCG99-BASE','IMMO','XXXXXX','207','20','Fonds commercial'),(161,'PCG99-BASE','IMMO','XXXXXX','208','20','Autres immobilisations incorporelles'),(162,'PCG99-BASE','IMMO','XXXXXX','21','2','Immobilisations corporelles'),(163,'PCG99-BASE','IMMO','XXXXXX','211','21','Terrains'),(164,'PCG99-BASE','IMMO','XXXXXX','212','21','Agencements et aménagements de terrains'),(165,'PCG99-BASE','IMMO','XXXXXX','213','21','Constructions'),(166,'PCG99-BASE','IMMO','XXXXXX','214','21','Constructions sur sol d\'autrui'),(167,'PCG99-BASE','IMMO','XXXXXX','215','21','Installations techniques, matériels et outillage industriels'),(168,'PCG99-BASE','IMMO','XXXXXX','218','21','Autres immobilisations corporelles'),(169,'PCG99-BASE','IMMO','XXXXXX','22','2','Immobilisations mises en concession'),(170,'PCG99-BASE','IMMO','XXXXXX','23','2','Immobilisations en cours'),(171,'PCG99-BASE','IMMO','XXXXXX','231','23','Immobilisations corporelles en cours'),(172,'PCG99-BASE','IMMO','XXXXXX','232','23','Immobilisations incorporelles en cours'),(173,'PCG99-BASE','IMMO','XXXXXX','237','23','Avances et acomptes versés sur immobilisations incorporelles'),(174,'PCG99-BASE','IMMO','XXXXXX','238','23','Avances et acomptes versés sur commandes d\'immobilisations corporelles'),(175,'PCG99-BASE','IMMO','XXXXXX','25','2','Parts dans des entreprises liées et créances sur des entreprises liées'),(176,'PCG99-BASE','IMMO','XXXXXX','26','2','Participations et créances rattachées à des participations'),(177,'PCG99-BASE','IMMO','XXXXXX','261','26','Titres de participation'),(178,'PCG99-BASE','IMMO','XXXXXX','266','26','Autres formes de participation'),(179,'PCG99-BASE','IMMO','XXXXXX','267','26','Créances rattachées à des participations'),(180,'PCG99-BASE','IMMO','XXXXXX','268','26','Créances rattachées à des sociétés en participation'),(181,'PCG99-BASE','IMMO','XXXXXX','269','26','Versements restant à effectuer sur titres de participation non libérés'),(182,'PCG99-BASE','IMMO','XXXXXX','27','2','Autres immobilisations financieres'),(183,'PCG99-BASE','IMMO','XXXXXX','271','27','Titres immobilisés autres que les titres immobilisés de l\'activité de portefeuille (droit de propriété)'),(184,'PCG99-BASE','IMMO','XXXXXX','272','27','Titres immobilisés (droit de créance)'),(185,'PCG99-BASE','IMMO','XXXXXX','273','27','Titres immobilisés de l\'activité de portefeuille'),(186,'PCG99-BASE','IMMO','XXXXXX','274','27','Prêts'),(187,'PCG99-BASE','IMMO','XXXXXX','275','27','Dépôts et cautionnements versés'),(188,'PCG99-BASE','IMMO','XXXXXX','276','27','Autres créances immobilisées'),(189,'PCG99-BASE','IMMO','XXXXXX','277','27','(Actions propres ou parts propres)'),(190,'PCG99-BASE','IMMO','XXXXXX','279','27','Versements restant à effectuer sur titres immobilisés non libérés'),(191,'PCG99-BASE','IMMO','XXXXXX','28','2','Amortissements des immobilisations'),(192,'PCG99-BASE','IMMO','XXXXXX','280','28','Amortissements des immobilisations incorporelles'),(193,'PCG99-BASE','IMMO','XXXXXX','281','28','Amortissements des immobilisations corporelles'),(194,'PCG99-BASE','IMMO','XXXXXX','282','28','Amortissements des immobilisations mises en concession'),(195,'PCG99-BASE','IMMO','XXXXXX','29','2','Dépréciations des immobilisations'),(196,'PCG99-BASE','IMMO','XXXXXX','290','29','Dépréciations des immobilisations incorporelles'),(197,'PCG99-BASE','IMMO','XXXXXX','291','29','Dépréciations des immobilisations corporelles'),(198,'PCG99-BASE','IMMO','XXXXXX','292','29','Dépréciations des immobilisations mises en concession'),(199,'PCG99-BASE','IMMO','XXXXXX','293','29','Dépréciations des immobilisations en cours'),(200,'PCG99-BASE','IMMO','XXXXXX','296','29','Provisions pour dépréciation des participations et créances rattachées à des participations'),(201,'PCG99-BASE','IMMO','XXXXXX','297','29','Provisions pour dépréciation des autres immobilisations financières'),(202,'PCG99-BASE','STOCK','XXXXXX','31','3','Matières premières (et fournitures)'),(203,'PCG99-BASE','STOCK','XXXXXX','311','31','Matières (ou groupe) A'),(204,'PCG99-BASE','STOCK','XXXXXX','312','31','Matières (ou groupe) B'),(205,'PCG99-BASE','STOCK','XXXXXX','317','31','Fournitures A, B, C,'),(206,'PCG99-BASE','STOCK','XXXXXX','32','3','Autres approvisionnements'),(207,'PCG99-BASE','STOCK','XXXXXX','321','32','Matières consommables'),(208,'PCG99-BASE','STOCK','XXXXXX','322','32','Fournitures consommables'),(209,'PCG99-BASE','STOCK','XXXXXX','326','32','Emballages'),(210,'PCG99-BASE','STOCK','XXXXXX','33','3','En-cours de production de biens'),(211,'PCG99-BASE','STOCK','XXXXXX','331','33','Produits en cours'),(212,'PCG99-BASE','STOCK','XXXXXX','335','33','Travaux en cours'),(213,'PCG99-BASE','STOCK','XXXXXX','34','3','En-cours de production de services'),(214,'PCG99-BASE','STOCK','XXXXXX','341','34','Etudes en cours'),(215,'PCG99-BASE','STOCK','XXXXXX','345','34','Prestations de services en cours'),(216,'PCG99-BASE','STOCK','XXXXXX','35','3','Stocks de produits'),(217,'PCG99-BASE','STOCK','XXXXXX','351','35','Produits intermédiaires'),(218,'PCG99-BASE','STOCK','XXXXXX','355','35','Produits finis'),(219,'PCG99-BASE','STOCK','XXXXXX','358','35','Produits résiduels (ou matières de récupération)'),(220,'PCG99-BASE','STOCK','XXXXXX','37','3','Stocks de marchandises'),(221,'PCG99-BASE','STOCK','XXXXXX','371','37','Marchandises (ou groupe) A'),(222,'PCG99-BASE','STOCK','XXXXXX','372','37','Marchandises (ou groupe) B'),(223,'PCG99-BASE','STOCK','XXXXXX','39','3','Provisions pour dépréciation des stocks et en-cours'),(224,'PCG99-BASE','STOCK','XXXXXX','391','39','Provisions pour dépréciation des matières premières'),(225,'PCG99-BASE','STOCK','XXXXXX','392','39','Provisions pour dépréciation des autres approvisionnements'),(226,'PCG99-BASE','STOCK','XXXXXX','393','39','Provisions pour dépréciation des en-cours de production de biens'),(227,'PCG99-BASE','STOCK','XXXXXX','394','39','Provisions pour dépréciation des en-cours de production de services'),(228,'PCG99-BASE','STOCK','XXXXXX','395','39','Provisions pour dépréciation des stocks de produits'),(229,'PCG99-BASE','STOCK','XXXXXX','397','39','Provisions pour dépréciation des stocks de marchandises'),(230,'PCG99-BASE','TIERS','XXXXXX','40','4','Fournisseurs et Comptes rattachés'),(231,'PCG99-BASE','TIERS','XXXXXX','400','40','Fournisseurs et Comptes rattachés'),(232,'PCG99-BASE','TIERS','SUPPLIER','401','40','Fournisseurs'),(233,'PCG99-BASE','TIERS','XXXXXX','403','40','Fournisseurs - Effets à payer'),(234,'PCG99-BASE','TIERS','XXXXXX','404','40','Fournisseurs d\'immobilisations'),(235,'PCG99-BASE','TIERS','XXXXXX','405','40','Fournisseurs d\'immobilisations - Effets à payer'),(236,'PCG99-BASE','TIERS','XXXXXX','408','40','Fournisseurs - Factures non parvenues'),(237,'PCG99-BASE','TIERS','XXXXXX','409','40','Fournisseurs débiteurs'),(238,'PCG99-BASE','TIERS','XXXXXX','41','4','Clients et comptes rattachés'),(239,'PCG99-BASE','TIERS','XXXXXX','410','41','Clients et Comptes rattachés'),(240,'PCG99-BASE','TIERS','CUSTOMER','411','41','Clients'),(241,'PCG99-BASE','TIERS','XXXXXX','413','41','Clients - Effets à recevoir'),(242,'PCG99-BASE','TIERS','XXXXXX','416','41','Clients douteux ou litigieux'),(243,'PCG99-BASE','TIERS','XXXXXX','418','41','Clients - Produits non encore facturés'),(244,'PCG99-BASE','TIERS','XXXXXX','419','41','Clients créditeurs'),(245,'PCG99-BASE','TIERS','XXXXXX','42','4','Personnel et comptes rattachés'),(246,'PCG99-BASE','TIERS','XXXXXX','421','42','Personnel - Rémunérations dues'),(247,'PCG99-BASE','TIERS','XXXXXX','422','42','Comités d\'entreprises, d\'établissement, ...'),(248,'PCG99-BASE','TIERS','XXXXXX','424','42','Participation des salariés aux résultats'),(249,'PCG99-BASE','TIERS','XXXXXX','425','42','Personnel - Avances et acomptes'),(250,'PCG99-BASE','TIERS','XXXXXX','426','42','Personnel - Dépôts'),(251,'PCG99-BASE','TIERS','XXXXXX','427','42','Personnel - Oppositions'),(252,'PCG99-BASE','TIERS','XXXXXX','428','42','Personnel - Charges à payer et produits à recevoir'),(253,'PCG99-BASE','TIERS','XXXXXX','43','4','Sécurité sociale et autres organismes sociaux'),(254,'PCG99-BASE','TIERS','XXXXXX','431','43','Sécurité sociale'),(255,'PCG99-BASE','TIERS','XXXXXX','437','43','Autres organismes sociaux'),(256,'PCG99-BASE','TIERS','XXXXXX','438','43','Organismes sociaux - Charges à payer et produits à recevoir'),(257,'PCG99-BASE','TIERS','XXXXXX','44','4','État et autres collectivités publiques'),(258,'PCG99-BASE','TIERS','XXXXXX','441','44','État - Subventions à recevoir'),(259,'PCG99-BASE','TIERS','XXXXXX','442','44','Etat - Impôts et taxes recouvrables sur des tiers'),(260,'PCG99-BASE','TIERS','XXXXXX','443','44','Opérations particulières avec l\'Etat, les collectivités publiques, les organismes internationaux'),(261,'PCG99-BASE','TIERS','XXXXXX','444','44','Etat - Impôts sur les bénéfices'),(262,'PCG99-BASE','TIERS','XXXXXX','445','44','Etat - Taxes sur le chiffre d\'affaires'),(263,'PCG99-BASE','TIERS','XXXXXX','446','44','Obligations cautionnées'),(264,'PCG99-BASE','TIERS','XXXXXX','447','44','Autres impôts, taxes et versements assimilés'),(265,'PCG99-BASE','TIERS','XXXXXX','448','44','Etat - Charges à payer et produits à recevoir'),(266,'PCG99-BASE','TIERS','XXXXXX','449','44','Quotas d\'émission à restituer à l\'Etat'),(267,'PCG99-BASE','TIERS','XXXXXX','45','4','Groupe et associes'),(268,'PCG99-BASE','TIERS','XXXXXX','451','45','Groupe'),(269,'PCG99-BASE','TIERS','XXXXXX','455','45','Associés - Comptes courants'),(270,'PCG99-BASE','TIERS','XXXXXX','456','45','Associés - Opérations sur le capital'),(271,'PCG99-BASE','TIERS','XXXXXX','457','45','Associés - Dividendes à payer'),(272,'PCG99-BASE','TIERS','XXXXXX','458','45','Associés - Opérations faites en commun et en G.I.E.'),(273,'PCG99-BASE','TIERS','XXXXXX','46','4','Débiteurs divers et créditeurs divers'),(274,'PCG99-BASE','TIERS','XXXXXX','462','46','Créances sur cessions d\'immobilisations'),(275,'PCG99-BASE','TIERS','XXXXXX','464','46','Dettes sur acquisitions de valeurs mobilières de placement'),(276,'PCG99-BASE','TIERS','XXXXXX','465','46','Créances sur cessions de valeurs mobilières de placement'),(277,'PCG99-BASE','TIERS','XXXXXX','467','46','Autres comptes débiteurs ou créditeurs'),(278,'PCG99-BASE','TIERS','XXXXXX','468','46','Divers - Charges à payer et produits à recevoir'),(279,'PCG99-BASE','TIERS','XXXXXX','47','4','Comptes transitoires ou d\'attente'),(280,'PCG99-BASE','TIERS','XXXXXX','471','47','Comptes d\'attente'),(281,'PCG99-BASE','TIERS','XXXXXX','476','47','Différence de conversion - Actif'),(282,'PCG99-BASE','TIERS','XXXXXX','477','47','Différences de conversion - Passif'),(283,'PCG99-BASE','TIERS','XXXXXX','478','47','Autres comptes transitoires'),(284,'PCG99-BASE','TIERS','XXXXXX','48','4','Comptes de régularisation'),(285,'PCG99-BASE','TIERS','XXXXXX','481','48','Charges à répartir sur plusieurs exercices'),(286,'PCG99-BASE','TIERS','XXXXXX','486','48','Charges constatées d\'avance'),(287,'PCG99-BASE','TIERS','XXXXXX','487','48','Produits constatés d\'avance'),(288,'PCG99-BASE','TIERS','XXXXXX','488','48','Comptes de répartition périodique des charges et des produits'),(289,'PCG99-BASE','TIERS','XXXXXX','489','48','Quotas d\'émission alloués par l\'Etat'),(290,'PCG99-BASE','TIERS','XXXXXX','49','4','Provisions pour dépréciation des comptes de tiers'),(291,'PCG99-BASE','TIERS','XXXXXX','491','49','Provisions pour dépréciation des comptes de clients'),(292,'PCG99-BASE','TIERS','XXXXXX','495','49','Provisions pour dépréciation des comptes du groupe et des associés'),(293,'PCG99-BASE','TIERS','XXXXXX','496','49','Provisions pour dépréciation des comptes de débiteurs divers'),(294,'PCG99-BASE','FINAN','XXXXXX','50','5','Valeurs mobilières de placement'),(295,'PCG99-BASE','FINAN','XXXXXX','501','50','Parts dans des entreprises liées'),(296,'PCG99-BASE','FINAN','XXXXXX','502','50','Actions propres'),(297,'PCG99-BASE','FINAN','XXXXXX','503','50','Actions'),(298,'PCG99-BASE','FINAN','XXXXXX','504','50','Autres titres conférant un droit de propriété'),(299,'PCG99-BASE','FINAN','XXXXXX','505','50','Obligations et bons émis par la société et rachetés par elle'),(300,'PCG99-BASE','FINAN','XXXXXX','506','50','Obligations'),(301,'PCG99-BASE','FINAN','XXXXXX','507','50','Bons du Trésor et bons de caisse à court terme'),(302,'PCG99-BASE','FINAN','XXXXXX','508','50','Autres valeurs mobilières de placement et autres créances assimilées'),(303,'PCG99-BASE','FINAN','XXXXXX','509','50','Versements restant à effectuer sur valeurs mobilières de placement non libérées'),(304,'PCG99-BASE','FINAN','XXXXXX','51','5','Banques, établissements financiers et assimilés'),(305,'PCG99-BASE','FINAN','XXXXXX','511','51','Valeurs à l\'encaissement'),(306,'PCG99-BASE','FINAN','BANK','512','51','Banques'),(307,'PCG99-BASE','FINAN','XXXXXX','514','51','Chèques postaux'),(308,'PCG99-BASE','FINAN','XXXXXX','515','51','\"Caisses\" du Trésor et des établissements publics'),(309,'PCG99-BASE','FINAN','XXXXXX','516','51','Sociétés de bourse'),(310,'PCG99-BASE','FINAN','XXXXXX','517','51','Autres organismes financiers'),(311,'PCG99-BASE','FINAN','XXXXXX','518','51','Intérêts courus'),(312,'PCG99-BASE','FINAN','XXXXXX','519','51','Concours bancaires courants'),(313,'PCG99-BASE','FINAN','XXXXXX','52','5','Instruments de trésorerie'),(314,'PCG99-BASE','FINAN','CASH','53','5','Caisse'),(315,'PCG99-BASE','FINAN','XXXXXX','531','53','Caisse siège social'),(316,'PCG99-BASE','FINAN','XXXXXX','532','53','Caisse succursale (ou usine) A'),(317,'PCG99-BASE','FINAN','XXXXXX','533','53','Caisse succursale (ou usine) B'),(318,'PCG99-BASE','FINAN','XXXXXX','54','5','Régies d\'avance et accréditifs'),(319,'PCG99-BASE','FINAN','XXXXXX','58','5','Virements internes'),(320,'PCG99-BASE','FINAN','XXXXXX','59','5','Provisions pour dépréciation des comptes financiers'),(321,'PCG99-BASE','FINAN','XXXXXX','590','59','Provisions pour dépréciation des valeurs mobilières de placement'),(322,'PCG99-BASE','CHARGE','PRODUCT','60','6','Achats'),(323,'PCG99-BASE','CHARGE','XXXXXX','601','60','Achats stockés - Matières premières (et fournitures)'),(324,'PCG99-BASE','CHARGE','XXXXXX','602','60','Achats stockés - Autres approvisionnements'),(325,'PCG99-BASE','CHARGE','XXXXXX','603','60','Variations des stocks (approvisionnements et marchandises)'),(326,'PCG99-BASE','CHARGE','XXXXXX','604','60','Achats stockés - Matières premières (et fournitures)'),(327,'PCG99-BASE','CHARGE','XXXXXX','605','60','Achats de matériel, équipements et travaux'),(328,'PCG99-BASE','CHARGE','XXXXXX','606','60','Achats non stockés de matière et fournitures'),(329,'PCG99-BASE','CHARGE','XXXXXX','607','60','Achats de marchandises'),(330,'PCG99-BASE','CHARGE','XXXXXX','608','60','(Compte réservé, le cas échéant, à la récapitulation des frais accessoires incorporés aux achats)'),(331,'PCG99-BASE','CHARGE','XXXXXX','609','60','Rabais, remises et ristournes obtenus sur achats'),(332,'PCG99-BASE','CHARGE','SERVICE','61','6','Services extérieurs'),(333,'PCG99-BASE','CHARGE','XXXXXX','611','61','Sous-traitance générale'),(334,'PCG99-BASE','CHARGE','XXXXXX','612','61','Redevances de crédit-bail'),(335,'PCG99-BASE','CHARGE','XXXXXX','613','61','Locations'),(336,'PCG99-BASE','CHARGE','XXXXXX','614','61','Charges locatives et de copropriété'),(337,'PCG99-BASE','CHARGE','XXXXXX','615','61','Entretien et réparations'),(338,'PCG99-BASE','CHARGE','XXXXXX','616','61','Primes d\'assurances'),(339,'PCG99-BASE','CHARGE','XXXXXX','617','61','Etudes et recherches'),(340,'PCG99-BASE','CHARGE','XXXXXX','618','61','Divers'),(341,'PCG99-BASE','CHARGE','XXXXXX','619','61','Rabais, remises et ristournes obtenus sur services extérieurs'),(342,'PCG99-BASE','CHARGE','XXXXXX','62','6','Autres services extérieurs'),(343,'PCG99-BASE','CHARGE','XXXXXX','621','62','Personnel extérieur à l\'entreprise'),(344,'PCG99-BASE','CHARGE','XXXXXX','622','62','Rémunérations d\'intermédiaires et honoraires'),(345,'PCG99-BASE','CHARGE','XXXXXX','623','62','Publicité, publications, relations publiques'),(346,'PCG99-BASE','CHARGE','XXXXXX','624','62','Transports de biens et transports collectifs du personnel'),(347,'PCG99-BASE','CHARGE','XXXXXX','625','62','Déplacements, missions et réceptions'),(348,'PCG99-BASE','CHARGE','XXXXXX','626','62','Frais postaux et de télécommunications'),(349,'PCG99-BASE','CHARGE','XXXXXX','627','62','Services bancaires et assimilés'),(350,'PCG99-BASE','CHARGE','XXXXXX','628','62','Divers'),(351,'PCG99-BASE','CHARGE','XXXXXX','629','62','Rabais, remises et ristournes obtenus sur autres services extérieurs'),(352,'PCG99-BASE','CHARGE','XXXXXX','63','6','Impôts, taxes et versements assimilés'),(353,'PCG99-BASE','CHARGE','XXXXXX','631','63','Impôts, taxes et versements assimilés sur rémunérations (administrations des impôts)'),(354,'PCG99-BASE','CHARGE','XXXXXX','633','63','Impôts, taxes et versements assimilés sur rémunérations (autres organismes)'),(355,'PCG99-BASE','CHARGE','XXXXXX','635','63','Autres impôts, taxes et versements assimilés (administrations des impôts)'),(356,'PCG99-BASE','CHARGE','XXXXXX','637','63','Autres impôts, taxes et versements assimilés (autres organismes)'),(357,'PCG99-BASE','CHARGE','XXXXXX','64','6','Charges de personnel'),(358,'PCG99-BASE','CHARGE','XXXXXX','641','64','Rémunérations du personnel'),(359,'PCG99-BASE','CHARGE','XXXXXX','644','64','Rémunération du travail de l\'exploitant'),(360,'PCG99-BASE','CHARGE','SOCIAL','645','64','Charges de sécurité sociale et de prévoyance'),(361,'PCG99-BASE','CHARGE','XXXXXX','646','64','Cotisations sociales personnelles de l\'exploitant'),(362,'PCG99-BASE','CHARGE','XXXXXX','647','64','Autres charges sociales'),(363,'PCG99-BASE','CHARGE','XXXXXX','648','64','Autres charges de personnel'),(364,'PCG99-BASE','CHARGE','XXXXXX','65','6','Autres charges de gestion courante'),(365,'PCG99-BASE','CHARGE','XXXXXX','651','65','Redevances pour concessions, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires'),(366,'PCG99-BASE','CHARGE','XXXXXX','653','65','Jetons de présence'),(367,'PCG99-BASE','CHARGE','XXXXXX','654','65','Pertes sur créances irrécouvrables'),(368,'PCG99-BASE','CHARGE','XXXXXX','655','65','Quote-part de résultat sur opérations faites en commun'),(369,'PCG99-BASE','CHARGE','XXXXXX','658','65','Charges diverses de gestion courante'),(370,'PCG99-BASE','CHARGE','XXXXXX','66','6','Charges financières'),(371,'PCG99-BASE','CHARGE','XXXXXX','661','66','Charges d\'intérêts'),(372,'PCG99-BASE','CHARGE','XXXXXX','664','66','Pertes sur créances liées à des participations'),(373,'PCG99-BASE','CHARGE','XXXXXX','665','66','Escomptes accordés'),(374,'PCG99-BASE','CHARGE','XXXXXX','666','66','Pertes de change'),(375,'PCG99-BASE','CHARGE','XXXXXX','667','66','Charges nettes sur cessions de valeurs mobilières de placement'),(376,'PCG99-BASE','CHARGE','XXXXXX','668','66','Autres charges financières'),(377,'PCG99-BASE','CHARGE','XXXXXX','67','6','Charges exceptionnelles'),(378,'PCG99-BASE','CHARGE','XXXXXX','671','67','Charges exceptionnelles sur opérations de gestion'),(379,'PCG99-BASE','CHARGE','XXXXXX','672','67','(Compte à la disposition des entités pour enregistrer, en cours d\'exercice, les charges sur exercices antérieurs)'),(380,'PCG99-BASE','CHARGE','XXXXXX','675','67','Valeurs comptables des éléments d\'actif cédés'),(381,'PCG99-BASE','CHARGE','XXXXXX','678','67','Autres charges exceptionnelles'),(382,'PCG99-BASE','CHARGE','XXXXXX','68','6','Dotations aux amortissements et aux provisions'),(383,'PCG99-BASE','CHARGE','XXXXXX','681','68','Dotations aux amortissements et aux provisions - Charges d\'exploitation'),(384,'PCG99-BASE','CHARGE','XXXXXX','686','68','Dotations aux amortissements et aux provisions - Charges financières'),(385,'PCG99-BASE','CHARGE','XXXXXX','687','68','Dotations aux amortissements et aux provisions - Charges exceptionnelles'),(386,'PCG99-BASE','CHARGE','XXXXXX','69','6','Participation des salariés - impôts sur les bénéfices et assimiles'),(387,'PCG99-BASE','CHARGE','XXXXXX','691','69','Participation des salariés aux résultats'),(388,'PCG99-BASE','CHARGE','XXXXXX','695','69','Impôts sur les bénéfices'),(389,'PCG99-BASE','CHARGE','XXXXXX','696','69','Suppléments d\'impôt sur les sociétés liés aux distributions'),(390,'PCG99-BASE','CHARGE','XXXXXX','697','69','Imposition forfaitaire annuelle des sociétés'),(391,'PCG99-BASE','CHARGE','XXXXXX','698','69','Intégration fiscale'),(392,'PCG99-BASE','CHARGE','XXXXXX','699','69','Produits - Reports en arrière des déficits'),(393,'PCG99-BASE','PROD','XXXXXX','70','7','Ventes de produits fabriqués, prestations de services, marchandises'),(394,'PCG99-BASE','PROD','PRODUCT','701','70','Ventes de produits finis'),(395,'PCG99-BASE','PROD','XXXXXX','702','70','Ventes de produits intermédiaires'),(396,'PCG99-BASE','PROD','XXXXXX','703','70','Ventes de produits résiduels'),(397,'PCG99-BASE','PROD','XXXXXX','704','70','Travaux'),(398,'PCG99-BASE','PROD','XXXXXX','705','70','Etudes'),(399,'PCG99-BASE','PROD','SERVICE','706','70','Prestations de services'),(400,'PCG99-BASE','PROD','PRODUCT','707','70','Ventes de marchandises'),(401,'PCG99-BASE','PROD','PRODUCT','708','70','Produits des activités annexes'),(402,'PCG99-BASE','PROD','XXXXXX','709','70','Rabais, remises et ristournes accordés par l\'entreprise'),(403,'PCG99-BASE','PROD','XXXXXX','71','7','Production stockée (ou déstockage)'),(404,'PCG99-BASE','PROD','XXXXXX','713','71','Variation des stocks (en-cours de production, produits)'),(405,'PCG99-BASE','PROD','XXXXXX','72','7','Production immobilisée'),(406,'PCG99-BASE','PROD','XXXXXX','721','72','Immobilisations incorporelles'),(407,'PCG99-BASE','PROD','XXXXXX','722','72','Immobilisations corporelles'),(408,'PCG99-BASE','PROD','XXXXXX','74','7','Subventions d\'exploitation'),(409,'PCG99-BASE','PROD','XXXXXX','75','7','Autres produits de gestion courante'),(410,'PCG99-BASE','PROD','XXXXXX','751','75','Redevances pour concessions, brevets, licences, marques, procédés, logiciels, droits et valeurs similaires'),(411,'PCG99-BASE','PROD','XXXXXX','752','75','Revenus des immeubles non affectés à des activités professionnelles'),(412,'PCG99-BASE','PROD','XXXXXX','753','75','Jetons de présence et rémunérations d\'administrateurs, gérants,...'),(413,'PCG99-BASE','PROD','XXXXXX','754','75','Ristournes perçues des coopératives (provenant des excédents)'),(414,'PCG99-BASE','PROD','XXXXXX','755','75','Quotes-parts de résultat sur opérations faites en commun'),(415,'PCG99-BASE','PROD','XXXXXX','758','75','Produits divers de gestion courante'),(416,'PCG99-BASE','PROD','XXXXXX','76','7','Produits financiers'),(417,'PCG99-BASE','PROD','XXXXXX','761','76','Produits de participations'),(418,'PCG99-BASE','PROD','XXXXXX','762','76','Produits des autres immobilisations financières'),(419,'PCG99-BASE','PROD','XXXXXX','763','76','Revenus des autres créances'),(420,'PCG99-BASE','PROD','XXXXXX','764','76','Revenus des valeurs mobilières de placement'),(421,'PCG99-BASE','PROD','XXXXXX','765','76','Escomptes obtenus'),(422,'PCG99-BASE','PROD','XXXXXX','766','76','Gains de change'),(423,'PCG99-BASE','PROD','XXXXXX','767','76','Produits nets sur cessions de valeurs mobilières de placement'),(424,'PCG99-BASE','PROD','XXXXXX','768','76','Autres produits financiers'),(425,'PCG99-BASE','PROD','XXXXXX','77','7','Produits exceptionnels'),(426,'PCG99-BASE','PROD','XXXXXX','771','77','Produits exceptionnels sur opérations de gestion'),(427,'PCG99-BASE','PROD','XXXXXX','772','77','(Compte à la disposition des entités pour enregistrer, en cours d\'exercice, les produits sur exercices antérieurs)'),(428,'PCG99-BASE','PROD','XXXXXX','775','77','Produits des cessions d\'éléments d\'actif'),(429,'PCG99-BASE','PROD','XXXXXX','777','77','Quote-part des subventions d\'investissement virée au résultat de l\'exercice'),(430,'PCG99-BASE','PROD','XXXXXX','778','77','Autres produits exceptionnels'),(431,'PCG99-BASE','PROD','XXXXXX','78','7','Reprises sur amortissements et provisions'),(432,'PCG99-BASE','PROD','XXXXXX','781','78','Reprises sur amortissements et provisions (à inscrire dans les produits d\'exploitation)'),(433,'PCG99-BASE','PROD','XXXXXX','786','78','Reprises sur provisions pour risques (à inscrire dans les produits financiers)'),(434,'PCG99-BASE','PROD','XXXXXX','787','78','Reprises sur provisions (à inscrire dans les produits exceptionnels)'),(435,'PCG99-BASE','PROD','XXXXXX','79','7','Transferts de charges'),(436,'PCG99-BASE','PROD','XXXXXX','791','79','Transferts de charges d\'exploitation '),(437,'PCG99-BASE','PROD','XXXXXX','796','79','Transferts de charges financières'),(438,'PCG99-BASE','PROD','XXXXXX','797','79','Transferts de charges exceptionnelles');
/*!40000 ALTER TABLE `llx_accountingaccount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_accountingdebcred`
--

DROP TABLE IF EXISTS `llx_accountingdebcred`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_accountingdebcred` (
  `fk_transaction` int(11) NOT NULL,
  `account_number` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `amount` double NOT NULL,
  `direction` varchar(1) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_accountingdebcred`
--

LOCK TABLES `llx_accountingdebcred` WRITE;
/*!40000 ALTER TABLE `llx_accountingdebcred` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_accountingdebcred` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_accountingsystem`
--

DROP TABLE IF EXISTS `llx_accountingsystem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_accountingsystem` (
  `pcg_version` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `fk_pays` int(11) NOT NULL,
  `label` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `datec` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `fk_author` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` smallint(6) DEFAULT '0',
  PRIMARY KEY (`pcg_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_accountingsystem`
--

LOCK TABLES `llx_accountingsystem` WRITE;
/*!40000 ALTER TABLE `llx_accountingsystem` DISABLE KEYS */;
INSERT INTO `llx_accountingsystem` VALUES ('PCG99-ABREGE',1,'The simple accountancy french plan','2012-10-11',NULL,'2012-10-11 14:07:45',0),('PCG99-BASE',1,'The base accountancy french plan','2012-10-11',NULL,'2012-10-11 14:07:45',0);
/*!40000 ALTER TABLE `llx_accountingsystem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_accountingtransaction`
--

DROP TABLE IF EXISTS `llx_accountingtransaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_accountingtransaction` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `datec` date NOT NULL,
  `fk_author` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_source` int(11) NOT NULL,
  `sourcetype` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_accountingtransaction`
--

LOCK TABLES `llx_accountingtransaction` WRITE;
/*!40000 ALTER TABLE `llx_accountingtransaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_accountingtransaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_actioncomm`
--

DROP TABLE IF EXISTS `llx_actioncomm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_actioncomm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_ext` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datep` datetime DEFAULT NULL,
  `datep2` datetime DEFAULT NULL,
  `datea` datetime DEFAULT NULL,
  `datea2` datetime DEFAULT NULL,
  `fk_action` int(11) DEFAULT NULL,
  `label` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_mod` int(11) DEFAULT NULL,
  `fk_project` int(11) DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_contact` int(11) DEFAULT NULL,
  `fk_parent` int(11) NOT NULL DEFAULT '0',
  `fk_user_action` int(11) DEFAULT NULL,
  `fk_user_done` int(11) DEFAULT NULL,
  `priority` smallint(6) DEFAULT NULL,
  `fulldayevent` smallint(6) NOT NULL DEFAULT '0',
  `punctual` smallint(6) NOT NULL DEFAULT '1',
  `percent` smallint(6) NOT NULL DEFAULT '0',
  `location` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `durationp` double DEFAULT NULL,
  `durationa` double DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `fk_element` int(11) DEFAULT NULL,
  `elementtype` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_actioncomm_datea` (`datea`),
  KEY `idx_actioncomm_fk_soc` (`fk_soc`),
  KEY `idx_actioncomm_fk_contact` (`fk_contact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_actioncomm`
--

LOCK TABLES `llx_actioncomm` WRITE;
/*!40000 ALTER TABLE `llx_actioncomm` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_actioncomm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_adherent`
--

DROP TABLE IF EXISTS `llx_adherent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_adherent` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `civilite` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prenom` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pass` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_adherent_type` int(11) NOT NULL,
  `morphy` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `societe` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `adresse` text COLLATE utf8_unicode_ci,
  `cp` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_departement` int(11) DEFAULT NULL,
  `pays` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_perso` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_mobile` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `naiss` date DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  `public` smallint(6) NOT NULL DEFAULT '0',
  `datefin` datetime DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `datevalid` datetime DEFAULT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_mod` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_adherent_login` (`login`,`entity`),
  UNIQUE KEY `uk_adherent_fk_soc` (`fk_soc`),
  KEY `idx_adherent_fk_adherent_type` (`fk_adherent_type`),
  CONSTRAINT `fk_adherent_adherent_type` FOREIGN KEY (`fk_adherent_type`) REFERENCES `llx_adherent_type` (`rowid`),
  CONSTRAINT `adherent_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_adherent`
--

LOCK TABLES `llx_adherent` WRITE;
/*!40000 ALTER TABLE `llx_adherent` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_adherent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_adherent_extrafields`
--

DROP TABLE IF EXISTS `llx_adherent_extrafields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_adherent_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_object` int(11) NOT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_adherent_extrafields` (`fk_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_adherent_extrafields`
--

LOCK TABLES `llx_adherent_extrafields` WRITE;
/*!40000 ALTER TABLE `llx_adherent_extrafields` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_adherent_extrafields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_adherent_type`
--

DROP TABLE IF EXISTS `llx_adherent_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_adherent_type` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  `libelle` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `cotisation` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `vote` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `note` text COLLATE utf8_unicode_ci,
  `mail_valid` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_adherent_type_libelle` (`libelle`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_adherent_type`
--

LOCK TABLES `llx_adherent_type` WRITE;
/*!40000 ALTER TABLE `llx_adherent_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_adherent_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_bank`
--

DROP TABLE IF EXISTS `llx_bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_bank` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `datev` date DEFAULT NULL,
  `dateo` date DEFAULT NULL,
  `amount` double(24,8) NOT NULL DEFAULT '0.00000000',
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_account` int(11) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_rappro` int(11) DEFAULT NULL,
  `fk_type` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num_releve` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num_chq` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rappro` tinyint(4) DEFAULT '0',
  `note` text COLLATE utf8_unicode_ci,
  `fk_bordereau` int(11) DEFAULT '0',
  `banque` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emetteur` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `author` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_bank_datev` (`datev`),
  KEY `idx_bank_dateo` (`dateo`),
  KEY `idx_bank_fk_account` (`fk_account`),
  KEY `idx_bank_rappro` (`rappro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_bank`
--

LOCK TABLES `llx_bank` WRITE;
/*!40000 ALTER TABLE `llx_bank` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_bank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_bank_account`
--

DROP TABLE IF EXISTS `llx_bank_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_bank_account` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ref` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `bank` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_banque` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_guichet` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cle_rib` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bic` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iban_prefix` varchar(34) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_iban` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cle_iban` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domiciliation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_departement` int(11) DEFAULT NULL,
  `fk_pays` int(11) NOT NULL,
  `proprio` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse_proprio` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `courant` smallint(6) NOT NULL DEFAULT '0',
  `clos` smallint(6) NOT NULL DEFAULT '0',
  `rappro` smallint(6) DEFAULT '1',
  `url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_number` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency_code` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `min_allowed` int(11) DEFAULT '0',
  `min_desired` int(11) DEFAULT '0',
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_bank_account_label` (`label`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_bank_account`
--

LOCK TABLES `llx_bank_account` WRITE;
/*!40000 ALTER TABLE `llx_bank_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_bank_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_bank_categ`
--

DROP TABLE IF EXISTS `llx_bank_categ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_bank_categ` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_bank_categ`
--

LOCK TABLES `llx_bank_categ` WRITE;
/*!40000 ALTER TABLE `llx_bank_categ` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_bank_categ` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_bank_class`
--

DROP TABLE IF EXISTS `llx_bank_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_bank_class` (
  `lineid` int(11) NOT NULL,
  `fk_categ` int(11) NOT NULL,
  UNIQUE KEY `uk_bank_class_lineid` (`lineid`,`fk_categ`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_bank_class`
--

LOCK TABLES `llx_bank_class` WRITE;
/*!40000 ALTER TABLE `llx_bank_class` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_bank_class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_bank_url`
--

DROP TABLE IF EXISTS `llx_bank_url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_bank_url` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_bank` int(11) DEFAULT NULL,
  `url_id` int(11) DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_bank_url` (`fk_bank`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_bank_url`
--

LOCK TABLES `llx_bank_url` WRITE;
/*!40000 ALTER TABLE `llx_bank_url` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_bank_url` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_bookmark`
--

DROP TABLE IF EXISTS `llx_bookmark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_bookmark` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_user` int(11) NOT NULL,
  `dateb` datetime DEFAULT NULL,
  `url` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `target` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `favicon` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_bookmark_url` (`fk_user`,`url`),
  UNIQUE KEY `uk_bookmark_title` (`fk_user`,`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_bookmark`
--

LOCK TABLES `llx_bookmark` WRITE;
/*!40000 ALTER TABLE `llx_bookmark` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_bookmark` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_bordereau_cheque`
--

DROP TABLE IF EXISTS `llx_bordereau_cheque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_bordereau_cheque` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime NOT NULL,
  `date_bordereau` date DEFAULT NULL,
  `number` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `amount` double(24,8) NOT NULL,
  `nbcheque` smallint(6) NOT NULL,
  `fk_bank_account` int(11) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_bordereau_cheque` (`number`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_bordereau_cheque`
--

LOCK TABLES `llx_bordereau_cheque` WRITE;
/*!40000 ALTER TABLE `llx_bordereau_cheque` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_bordereau_cheque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_boxes`
--

DROP TABLE IF EXISTS `llx_boxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_boxes` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `box_id` int(11) NOT NULL,
  `position` smallint(6) NOT NULL,
  `box_order` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `fk_user` int(11) NOT NULL DEFAULT '0',
  `maxline` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_boxes` (`box_id`,`position`,`fk_user`),
  KEY `idx_boxes_boxid` (`box_id`),
  KEY `idx_boxes_fk_user` (`fk_user`),
  CONSTRAINT `fk_boxes_box_id` FOREIGN KEY (`box_id`) REFERENCES `llx_boxes_def` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_boxes`
--

LOCK TABLES `llx_boxes` WRITE;
/*!40000 ALTER TABLE `llx_boxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_boxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_boxes_def`
--

DROP TABLE IF EXISTS `llx_boxes_def`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_boxes_def` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note` varchar(130) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_boxes_def` (`file`,`entity`,`note`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_boxes_def`
--

LOCK TABLES `llx_boxes_def` WRITE;
/*!40000 ALTER TABLE `llx_boxes_def` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_boxes_def` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_action_trigger`
--

DROP TABLE IF EXISTS `llx_c_action_trigger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_action_trigger` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `elementtype` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_action_trigger_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_action_trigger`
--

LOCK TABLES `llx_c_action_trigger` WRITE;
/*!40000 ALTER TABLE `llx_c_action_trigger` DISABLE KEYS */;
INSERT INTO `llx_c_action_trigger` VALUES (1,'FICHEINTER_VALIDATE','Intervention validated','Executed when a intervention is validated','ficheinter',18),(2,'BILL_VALIDATE','Customer invoice validated','Executed when a customer invoice is approved','facture',6),(3,'ORDER_SUPPLIER_APPROVE','Supplier order request approved','Executed when a supplier order is approved','order_supplier',11),(4,'ORDER_SUPPLIER_REFUSE','Supplier order request refused','Executed when a supplier order is refused','order_supplier',12),(5,'ORDER_VALIDATE','Customer order validate','Executed when a customer order is validated','commande',4),(6,'PROPAL_VALIDATE','Customer proposal validated','Executed when a commercial proposal is validated','propal',2),(7,'WITHDRAW_TRANSMIT','Withdraw command transmitted','Executed when a withdrawal command is transmited','withdraw',25),(8,'WITHDRAW_CREDIT','Withdraw credited','Executed when a withdrawal is credited','withdraw',26),(9,'WITHDRAW_EMIT','Withdraw emit','Executed when a withdrawal is emited','withdraw',27),(10,'COMPANY_CREATE','Third party created','Executed when a third party is created','societe',1),(11,'CONTRACT_VALIDATE','Contract validated','Executed when a contract is validated','contrat',17),(12,'PROPAL_SENTBYMAIL','Commercial proposal sent by mail','Executed when a commercial proposal is sent by mail','propal',3),(13,'ORDER_SENTBYMAIL','Customer order sent by mail','Executed when a customer order is sent by mail ','commande',5),(14,'BILL_PAYED','Customer invoice payed','Executed when a customer invoice is payed','facture',7),(15,'BILL_CANCEL','Customer invoice canceled','Executed when a customer invoice is conceled','facture',8),(16,'BILL_SENTBYMAIL','Customer invoice sent by mail','Executed when a customer invoice is sent by mail','facture',9),(17,'ORDER_SUPPLIER_VALIDATE','Supplier order validated','Executed when a supplier order is validated','order_supplier',10),(18,'ORDER_SUPPLIER_SENTBYMAIL','Supplier order sent by mail','Executed when a supplier order is sent by mail','order_supplier',13),(19,'BILL_SUPPLIER_VALIDATE','Supplier invoice validated','Executed when a supplier invoice is validated','invoice_supplier',14),(20,'BILL_SUPPLIER_PAYED','Supplier invoice payed','Executed when a supplier invoice is payed','invoice_supplier',15),(21,'BILL_SUPPLIER_SENTBYMAIL','Supplier invoice sent by mail','Executed when a supplier invoice is sent by mail','invoice_supplier',16),(22,'SHIPPING_VALIDATE','Shipping validated','Executed when a shipping is validated','shipping',19),(23,'SHIPPING_SENTBYMAIL','Shipping sent by mail','Executed when a shipping is sent by mail','shipping',20),(24,'MEMBER_VALIDATE','Member validated','Executed when a member is validated','member',21),(25,'MEMBER_SUBSCRIPTION','Member subscribed','Executed when a member is subscribed','member',22),(26,'MEMBER_RESILIATE','Member resiliated','Executed when a member is resiliated','member',23),(27,'MEMBER_DELETE','Member deleted','Executed when a member is deleted','member',24);
/*!40000 ALTER TABLE `llx_c_action_trigger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_actioncomm`
--

DROP TABLE IF EXISTS `llx_c_actioncomm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_actioncomm` (
  `id` int(11) NOT NULL,
  `code` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'system',
  `libelle` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `todo` tinyint(4) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_actioncomm`
--

LOCK TABLES `llx_c_actioncomm` WRITE;
/*!40000 ALTER TABLE `llx_c_actioncomm` DISABLE KEYS */;
INSERT INTO `llx_c_actioncomm` VALUES (1,'AC_TEL','system','Phone call',NULL,1,NULL,2),(2,'AC_FAX','system','Send Fax',NULL,1,NULL,3),(3,'AC_PROP','system','Send commercial proposal by email','propal',1,NULL,10),(4,'AC_EMAIL','system','Send Email',NULL,1,NULL,4),(5,'AC_RDV','system','Rendez-vous',NULL,1,NULL,1),(8,'AC_COM','system','Send customer order by email','order',1,NULL,8),(9,'AC_FAC','system','Send customer invoice by email','invoice',1,NULL,6),(10,'AC_SHIP','system','Send shipping by email','shipping',1,NULL,11),(30,'AC_SUP_ORD','system','Send supplier order by email','order_supplier',1,NULL,9),(31,'AC_SUP_INV','system','Send supplier invoice by email','invoice_supplier',1,NULL,7),(50,'AC_OTH','system','Other',NULL,1,NULL,5);
/*!40000 ALTER TABLE `llx_c_actioncomm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_availability`
--

DROP TABLE IF EXISTS `llx_c_availability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_availability` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_availability` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_availability`
--

LOCK TABLES `llx_c_availability` WRITE;
/*!40000 ALTER TABLE `llx_c_availability` DISABLE KEYS */;
INSERT INTO `llx_c_availability` VALUES (1,'AV_NOW','Immediate',1),(2,'AV_1W','1 week',1),(3,'AV_2W','2 weeks',1),(4,'AV_3W','3 weeks',1);
/*!40000 ALTER TABLE `llx_c_availability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_barcode_type`
--

DROP TABLE IF EXISTS `llx_c_barcode_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_barcode_type` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `libelle` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `coder` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `example` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_barcode_type` (`code`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_barcode_type`
--

LOCK TABLES `llx_c_barcode_type` WRITE;
/*!40000 ALTER TABLE `llx_c_barcode_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_c_barcode_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_chargesociales`
--

DROP TABLE IF EXISTS `llx_c_chargesociales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_chargesociales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deductible` smallint(6) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `code` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `fk_pays` int(11) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=231 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_chargesociales`
--

LOCK TABLES `llx_c_chargesociales` WRITE;
/*!40000 ALTER TABLE `llx_c_chargesociales` DISABLE KEYS */;
INSERT INTO `llx_c_chargesociales` VALUES (1,'Allocations familiales',1,1,'TAXFAM',1,NULL),(2,'CSG Deductible',1,1,'TAXCSGD',1,NULL),(3,'CSG/CRDS NON Deductible',0,1,'TAXCSGND',1,NULL),(10,'Taxe apprentissage',0,1,'TAXAPP',1,NULL),(11,'Taxe professionnelle',0,1,'TAXPRO',1,NULL),(12,'Cotisation fonciere des entreprises',0,1,'TAXCFE',1,NULL),(13,'Cotisation sur la valeur ajoutee des entreprises',0,1,'TAXCVAE',1,NULL),(20,'Impots locaux/fonciers',0,1,'TAXFON',1,NULL),(25,'Impots revenus',0,1,'TAXREV',1,NULL),(30,'Assurance Sante',0,1,'TAXSECU',1,NULL),(40,'Mutuelle',0,1,'TAXMUT',1,NULL),(50,'Assurance vieillesse',0,1,'TAXRET',1,NULL),(60,'Assurance Chomage',0,1,'TAXCHOM',1,NULL),(201,'ONSS',1,1,'TAXBEONSS',2,NULL),(210,'Precompte professionnel',1,1,'TAXBEPREPRO',2,NULL),(220,'Prime existence',1,1,'TAXBEPRIEXI',2,NULL),(230,'Precompte immobilier',1,1,'TAXBEPREIMMO',2,NULL);
/*!40000 ALTER TABLE `llx_c_chargesociales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_civilite`
--

DROP TABLE IF EXISTS `llx_c_civilite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_civilite` (
  `rowid` int(11) NOT NULL,
  `code` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `civilite` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_civilite`
--

LOCK TABLES `llx_c_civilite` WRITE;
/*!40000 ALTER TABLE `llx_c_civilite` DISABLE KEYS */;
INSERT INTO `llx_c_civilite` VALUES (1,'MME','Madame',1,NULL),(3,'MR','Monsieur',1,NULL),(5,'MLE','Mademoiselle',1,NULL),(7,'MTRE','Maître',1,NULL),(8,'DR','Docteur',1,NULL);
/*!40000 ALTER TABLE `llx_c_civilite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_currencies`
--

DROP TABLE IF EXISTS `llx_c_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_currencies` (
  `code_iso` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `unicode` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`code_iso`),
  UNIQUE KEY `uk_c_currencies_code_iso` (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_currencies`
--

LOCK TABLES `llx_c_currencies` WRITE;
/*!40000 ALTER TABLE `llx_c_currencies` DISABLE KEYS */;
INSERT INTO `llx_c_currencies` VALUES ('AED','United Arab Emirates Dirham',NULL,1),('AFN','Afghanistan Afghani','[1547]',1),('ALL','Albania Lek','[76,101,107]',1),('ANG','Netherlands Antilles Guilder','[402]',1),('ARP','Pesos argentins',NULL,0),('ARS','Argentino Peso','[36]',1),('ATS','Shiliing autrichiens',NULL,0),('AUD','Australia Dollar','[36]',1),('AWG','Aruba Guilder','[402]',1),('AZN','Azerbaijan New Manat','[1084,1072,1085]',1),('BAM','Bosnia and Herzegovina Convertible Marka','[75,77]',1),('BBD','Barbados Dollar','[36]',1),('BEF','Francs belges',NULL,0),('BGN','Bulgaria Lev','[1083,1074]',1),('BMD','Bermuda Dollar','[36]',1),('BND','Brunei Darussalam Dollar','[36]',1),('BOB','Bolivia Boliviano','[36,98]',1),('BRL','Brazil Real','[82,36]',1),('BSD','Bahamas Dollar','[36]',1),('BWP','Botswana Pula','[80]',1),('BYR','Belarus Ruble','[112,46]',1),('BZD','Belize Dollar','[66,90,36]',1),('CAD','Canada Dollar','[36]',1),('CHF','Switzerland Franc','[67,72,70]',1),('CLP','Chile Peso','[36]',1),('CNY','China Yuan Renminbi','[165]',1),('COP','Colombia Peso','[36]',1),('CRC','Costa Rica Colon','[8353]',1),('CUP','Cuba Peso','[8369]',1),('CZK','Czech Republic Koruna','[75,269]',1),('DEM','Deutsch mark',NULL,0),('DKK','Denmark Krone','[107,114]',1),('DOP','Dominican Republic Peso','[82,68,36]',1),('DZD','Algeria Dinar',NULL,1),('EEK','Estonia Kroon','[107,114]',1),('EGP','Egypt Pound','[163]',1),('ESP','Pesete',NULL,0),('EUR','Euro Member Countries','[8364]',1),('FIM','Mark finlandais',NULL,0),('FJD','Fiji Dollar','[36]',1),('FKP','Falkland Islands (Malvinas) Pound','[163]',1),('FRF','Francs francais',NULL,0),('GBP','United Kingdom Pound','[163]',1),('GGP','Guernsey Pound','[163]',1),('GHC','Ghana Cedis','[162]',1),('GIP','Gibraltar Pound','[163]',1),('GRD','Drachme (grece)',NULL,0),('GTQ','Guatemala Quetzal','[81]',1),('GYD','Guyana Dollar','[36]',1),('HKD','Hong Kong Dollar','[36]',1),('HNL','Honduras Lempira','[76]',1),('HRK','Croatia Kuna','[107,110]',1),('HUF','Hungary Forint','[70,116]',1),('IDR','Indonesia Rupiah','[82,112]',1),('IEP','Livres irlandaises',NULL,0),('ILS','Israel Shekel','[8362]',1),('IMP','Isle of Man Pound','[163]',1),('INR','India Rupee',NULL,1),('IRR','Iran Rial','[65020]',1),('ISK','Iceland Krona','[107,114]',1),('ITL','Lires',NULL,0),('JEP','Jersey Pound','[163]',1),('JMD','Jamaica Dollar','[74,36]',1),('JPY','Japan Yen','[165]',1),('KGS','Kyrgyzstan Som','[1083,1074]',1),('KHR','Cambodia Riel','[6107]',1),('KPW','Korea (North) Won','[8361]',1),('KRW','Korea (South) Won','[8361]',1),('KYD','Cayman Islands Dollar','[36]',1),('KZT','Kazakhstan Tenge','[1083,1074]',1),('LAK','Laos Kip','[8365]',1),('LBP','Lebanon Pound','[163]',1),('LKR','Sri Lanka Rupee','[8360]',1),('LRD','Liberia Dollar','[36]',1),('LTL','Lithuania Litas','[76,116]',1),('LUF','Francs luxembourgeois',NULL,0),('LVL','Latvia Lat','[76,115]',1),('MAD','Morocco Dirham',NULL,1),('MKD','Macedonia Denar','[1076,1077,1085]',1),('MNT','Mongolia Tughrik','[8366]',1),('MRO','Mauritania Ouguiya',NULL,1),('MUR','Mauritius Rupee','[8360]',1),('MXN','Mexico Peso','[36]',1),('MXP','Pesos Mexicans',NULL,0),('MYR','Malaysia Ringgit','[82,77]',1),('MZN','Mozambique Metical','[77,84]',1),('NAD','Namibia Dollar','[36]',1),('NGN','Nigeria Naira','[8358]',1),('NIO','Nicaragua Cordoba','[67,36]',1),('NLG','Florins',NULL,0),('NOK','Norway Krone','[107,114]',1),('NPR','Nepal Rupee','[8360]',1),('NZD','New Zealand Dollar','[36]',1),('OMR','Oman Rial','[65020]',1),('PAB','Panama Balboa','[66,47,46]',1),('PEN','Peru Nuevo Sol','[83,47,46]',1),('PHP','Philippines Peso','[8369]',1),('PKR','Pakistan Rupee','[8360]',1),('PLN','Poland Zloty','[122,322]',1),('PTE','Escudos',NULL,0),('PYG','Paraguay Guarani','[71,115]',1),('QAR','Qatar Riyal','[65020]',1),('RON','Romania New Leu','[108,101,105]',1),('RSD','Serbia Dinar','[1044,1080,1085,46]',1),('RUB','Russia Ruble','[1088,1091,1073]',1),('SAR','Saudi Arabia Riyal','[65020]',1),('SBD','Solomon Islands Dollar','[36]',1),('SCR','Seychelles Rupee','[8360]',1),('SEK','Sweden Krona','[107,114]',1),('SGD','Singapore Dollar','[36]',1),('SHP','Saint Helena Pound','[163]',1),('SKK','Couronnes slovaques',NULL,0),('SOS','Somalia Shilling','[83]',1),('SRD','Suriname Dollar','[36]',1),('SUR','Rouble',NULL,0),('SVC','El Salvador Colon','[36]',1),('SYP','Syria Pound','[163]',1),('THB','Thailand Baht','[3647]',1),('TND','Tunisia Dinar',NULL,1),('TRL','Turkey Lira','[84,76]',1),('TRY','Turkey Lira','[8356]',1),('TTD','Trinidad and Tobago Dollar','[84,84,36]',1),('TVD','Tuvalu Dollar','[36]',1),('TWD','Taiwan New Dollar','[78,84,36]',1),('UAH','Ukraine Hryvna','[8372]',1),('USD','United States Dollar','[36]',1),('UYU','Uruguay Peso','[36,85]',1),('UZS','Uzbekistan Som','[1083,1074]',1),('VEF','Venezuela Bolivar Fuerte','[66,115]',1),('VND','Viet Nam Dong','[8363]',1),('XAF','Communaute Financiere Africaine (BEAC) CFA Franc',NULL,1),('XCD','East Caribbean Dollar','[36]',1),('XEU','Ecus',NULL,0),('XOF','Communaute Financiere Africaine (BCEAO) Franc',NULL,1),('YER','Yemen Rial','[65020]',1),('ZAR','South Africa Rand','[82]',1),('ZWD','Zimbabwe Dollar','[90,36]',1);
/*!40000 ALTER TABLE `llx_c_currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_departements`
--

DROP TABLE IF EXISTS `llx_c_departements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_departements` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code_departement` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `fk_region` int(11) DEFAULT NULL,
  `cheflieu` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tncc` int(11) DEFAULT NULL,
  `ncc` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_departements` (`code_departement`,`fk_region`),
  KEY `idx_departements_fk_region` (`fk_region`)
) ENGINE=InnoDB AUTO_INCREMENT=1301 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_departements`
--

LOCK TABLES `llx_c_departements` WRITE;
/*!40000 ALTER TABLE `llx_c_departements` DISABLE KEYS */;
INSERT INTO `llx_c_departements` VALUES (1,'0',0,'0',0,'-','-',1),(2,'01',82,'01053',5,'AIN','Ain',1),(3,'02',22,'02408',5,'AISNE','Aisne',1),(4,'03',83,'03190',5,'ALLIER','Allier',1),(5,'04',93,'04070',4,'ALPES-DE-HAUTE-PROVENCE','Alpes-de-Haute-Provence',1),(6,'05',93,'05061',4,'HAUTES-ALPES','Hautes-Alpes',1),(7,'06',93,'06088',4,'ALPES-MARITIMES','Alpes-Maritimes',1),(8,'07',82,'07186',5,'ARDECHE','Ardèche',1),(9,'08',21,'08105',4,'ARDENNES','Ardennes',1),(10,'09',73,'09122',5,'ARIEGE','Ariège',1),(11,'10',21,'10387',5,'AUBE','Aube',1),(12,'11',91,'11069',5,'AUDE','Aude',1),(13,'12',73,'12202',5,'AVEYRON','Aveyron',1),(14,'13',93,'13055',4,'BOUCHES-DU-RHONE','Bouches-du-Rhône',1),(15,'14',25,'14118',2,'CALVADOS','Calvados',1),(16,'15',83,'15014',2,'CANTAL','Cantal',1),(17,'16',54,'16015',3,'CHARENTE','Charente',1),(18,'17',54,'17300',3,'CHARENTE-MARITIME','Charente-Maritime',1),(19,'18',24,'18033',2,'CHER','Cher',1),(20,'19',74,'19272',3,'CORREZE','Corrèze',1),(21,'2A',94,'2A004',3,'CORSE-DU-SUD','Corse-du-Sud',1),(22,'2B',94,'2B033',3,'HAUTE-CORSE','Haute-Corse',1),(23,'21',26,'21231',3,'COTE-D OR','Côte-d Or',1),(24,'22',53,'22278',4,'COTES-D ARMOR','Côtes-d Armor',1),(25,'23',74,'23096',3,'CREUSE','Creuse',1),(26,'24',72,'24322',3,'DORDOGNE','Dordogne',1),(27,'25',43,'25056',2,'DOUBS','Doubs',1),(28,'26',82,'26362',3,'DROME','Drôme',1),(29,'27',23,'27229',5,'EURE','Eure',1),(30,'28',24,'28085',1,'EURE-ET-LOIR','Eure-et-Loir',1),(31,'29',53,'29232',2,'FINISTERE','Finistère',1),(32,'30',91,'30189',2,'GARD','Gard',1),(33,'31',73,'31555',3,'HAUTE-GARONNE','Haute-Garonne',1),(34,'32',73,'32013',2,'GERS','Gers',1),(35,'33',72,'33063',3,'GIRONDE','Gironde',1),(36,'34',91,'34172',5,'HERAULT','Hérault',1),(37,'35',53,'35238',1,'ILLE-ET-VILAINE','Ille-et-Vilaine',1),(38,'36',24,'36044',5,'INDRE','Indre',1),(39,'37',24,'37261',1,'INDRE-ET-LOIRE','Indre-et-Loire',1),(40,'38',82,'38185',5,'ISERE','Isère',1),(41,'39',43,'39300',2,'JURA','Jura',1),(42,'40',72,'40192',4,'LANDES','Landes',1),(43,'41',24,'41018',0,'LOIR-ET-CHER','Loir-et-Cher',1),(44,'42',82,'42218',3,'LOIRE','Loire',1),(45,'43',83,'43157',3,'HAUTE-LOIRE','Haute-Loire',1),(46,'44',52,'44109',3,'LOIRE-ATLANTIQUE','Loire-Atlantique',1),(47,'45',24,'45234',2,'LOIRET','Loiret',1),(48,'46',73,'46042',2,'LOT','Lot',1),(49,'47',72,'47001',0,'LOT-ET-GARONNE','Lot-et-Garonne',1),(50,'48',91,'48095',3,'LOZERE','Lozère',1),(51,'49',52,'49007',0,'MAINE-ET-LOIRE','Maine-et-Loire',1),(52,'50',25,'50502',3,'MANCHE','Manche',1),(53,'51',21,'51108',3,'MARNE','Marne',1),(54,'52',21,'52121',3,'HAUTE-MARNE','Haute-Marne',1),(55,'53',52,'53130',3,'MAYENNE','Mayenne',1),(56,'54',41,'54395',0,'MEURTHE-ET-MOSELLE','Meurthe-et-Moselle',1),(57,'55',41,'55029',3,'MEUSE','Meuse',1),(58,'56',53,'56260',2,'MORBIHAN','Morbihan',1),(59,'57',41,'57463',3,'MOSELLE','Moselle',1),(60,'58',26,'58194',3,'NIEVRE','Nièvre',1),(61,'59',31,'59350',2,'NORD','Nord',1),(62,'60',22,'60057',5,'OISE','Oise',1),(63,'61',25,'61001',5,'ORNE','Orne',1),(64,'62',31,'62041',2,'PAS-DE-CALAIS','Pas-de-Calais',1),(65,'63',83,'63113',2,'PUY-DE-DOME','Puy-de-Dôme',1),(66,'64',72,'64445',4,'PYRENEES-ATLANTIQUES','Pyrénées-Atlantiques',1),(67,'65',73,'65440',4,'HAUTES-PYRENEES','Hautes-Pyrénées',1),(68,'66',91,'66136',4,'PYRENEES-ORIENTALES','Pyrénées-Orientales',1),(69,'67',42,'67482',2,'BAS-RHIN','Bas-Rhin',1),(70,'68',42,'68066',2,'HAUT-RHIN','Haut-Rhin',1),(71,'69',82,'69123',2,'RHONE','Rhône',1),(72,'70',43,'70550',3,'HAUTE-SAONE','Haute-Saône',1),(73,'71',26,'71270',0,'SAONE-ET-LOIRE','Saône-et-Loire',1),(74,'72',52,'72181',3,'SARTHE','Sarthe',1),(75,'73',82,'73065',3,'SAVOIE','Savoie',1),(76,'74',82,'74010',3,'HAUTE-SAVOIE','Haute-Savoie',1),(77,'75',11,'75056',0,'PARIS','Paris',1),(78,'76',23,'76540',3,'SEINE-MARITIME','Seine-Maritime',1),(79,'77',11,'77288',0,'SEINE-ET-MARNE','Seine-et-Marne',1),(80,'78',11,'78646',4,'YVELINES','Yvelines',1),(81,'79',54,'79191',4,'DEUX-SEVRES','Deux-Sèvres',1),(82,'80',22,'80021',3,'SOMME','Somme',1),(83,'81',73,'81004',2,'TARN','Tarn',1),(84,'82',73,'82121',0,'TARN-ET-GARONNE','Tarn-et-Garonne',1),(85,'83',93,'83137',2,'VAR','Var',1),(86,'84',93,'84007',0,'VAUCLUSE','Vaucluse',1),(87,'85',52,'85191',3,'VENDEE','Vendée',1),(88,'86',54,'86194',3,'VIENNE','Vienne',1),(89,'87',74,'87085',3,'HAUTE-VIENNE','Haute-Vienne',1),(90,'88',41,'88160',4,'VOSGES','Vosges',1),(91,'89',26,'89024',5,'YONNE','Yonne',1),(92,'90',43,'90010',0,'TERRITOIRE DE BELFORT','Territoire de Belfort',1),(93,'91',11,'91228',5,'ESSONNE','Essonne',1),(94,'92',11,'92050',4,'HAUTS-DE-SEINE','Hauts-de-Seine',1),(95,'93',11,'93008',3,'SEINE-SAINT-DENIS','Seine-Saint-Denis',1),(96,'94',11,'94028',2,'VAL-DE-MARNE','Val-de-Marne',1),(97,'95',11,'95500',2,'VAL-D OISE','Val-d Oise',1),(98,'971',1,'97105',3,'GUADELOUPE','Guadeloupe',1),(99,'972',2,'97209',3,'MARTINIQUE','Martinique',1),(100,'973',3,'97302',3,'GUYANE','Guyane',1),(101,'974',4,'97411',3,'REUNION','Réunion',1),(102,'01',201,'',1,'ANVERS','Anvers',1),(103,'02',203,'',3,'BRUXELLES-CAPITALE','Bruxelles-Capitale',1),(104,'03',202,'',2,'BRABANT-WALLON','Brabant-Wallon',1),(105,'04',201,'',1,'BRABANT-FLAMAND','Brabant-Flamand',1),(106,'05',201,'',1,'FLANDRE-OCCIDENTALE','Flandre-Occidentale',1),(107,'06',201,'',1,'FLANDRE-ORIENTALE','Flandre-Orientale',1),(108,'07',202,'',2,'HAINAUT','Hainaut',1),(109,'08',201,'',2,'LIEGE','Liège',1),(110,'09',202,'',1,'LIMBOURG','Limbourg',1),(111,'10',202,'',2,'LUXEMBOURG','Luxembourg',1),(112,'11',201,'',2,'NAMUR','Namur',1),(113,'AG',315,NULL,NULL,NULL,'AGRIGENTO',1),(114,'AL',312,NULL,NULL,NULL,'ALESSANDRIA',1),(115,'AN',310,NULL,NULL,NULL,'ANCONA',1),(116,'AO',319,NULL,NULL,NULL,'AOSTA',1),(117,'AR',316,NULL,NULL,NULL,'AREZZO',1),(118,'AP',310,NULL,NULL,NULL,'ASCOLI PICENO',1),(119,'AT',312,NULL,NULL,NULL,'ASTI',1),(120,'AV',304,NULL,NULL,NULL,'AVELLINO',1),(121,'BA',313,NULL,NULL,NULL,'BARI',1),(122,'BT',313,NULL,NULL,NULL,'BARLETTA-ANDRIA-TRANI',1),(123,'BL',320,NULL,NULL,NULL,'BELLUNO',1),(124,'BN',304,NULL,NULL,NULL,'BENEVENTO',1),(125,'BG',309,NULL,NULL,NULL,'BERGAMO',1),(126,'BI',312,NULL,NULL,NULL,'BIELLA',1),(127,'BO',305,NULL,NULL,NULL,'BOLOGNA',1),(128,'BZ',317,NULL,NULL,NULL,'BOLZANO',1),(129,'BS',309,NULL,NULL,NULL,'BRESCIA',1),(130,'BR',313,NULL,NULL,NULL,'BRINDISI',1),(131,'CA',314,NULL,NULL,NULL,'CAGLIARI',1),(132,'CL',315,NULL,NULL,NULL,'CALTANISSETTA',1),(133,'CB',311,NULL,NULL,NULL,'CAMPOBASSO',1),(134,'CI',314,NULL,NULL,NULL,'CARBONIA-IGLESIAS',1),(135,'CE',304,NULL,NULL,NULL,'CASERTA',1),(136,'CT',315,NULL,NULL,NULL,'CATANIA',1),(137,'CZ',303,NULL,NULL,NULL,'CATANZARO',1),(138,'CH',301,NULL,NULL,NULL,'CHIETI',1),(139,'CO',309,NULL,NULL,NULL,'COMO',1),(140,'CS',303,NULL,NULL,NULL,'COSENZA',1),(141,'CR',309,NULL,NULL,NULL,'CREMONA',1),(142,'KR',303,NULL,NULL,NULL,'CROTONE',1),(143,'CN',312,NULL,NULL,NULL,'CUNEO',1),(144,'EN',315,NULL,NULL,NULL,'ENNA',1),(145,'FM',310,NULL,NULL,NULL,'FERMO',1),(146,'FE',305,NULL,NULL,NULL,'FERRARA',1),(147,'FI',316,NULL,NULL,NULL,'FIRENZE',1),(148,'FG',313,NULL,NULL,NULL,'FOGGIA',1),(149,'FC',305,NULL,NULL,NULL,'FORLI-CESENA',1),(150,'FR',307,NULL,NULL,NULL,'FROSINONE',1),(151,'GE',308,NULL,NULL,NULL,'GENOVA',1),(152,'GO',306,NULL,NULL,NULL,'GORIZIA',1),(153,'GR',316,NULL,NULL,NULL,'GROSSETO',1),(154,'IM',308,NULL,NULL,NULL,'IMPERIA',1),(155,'IS',311,NULL,NULL,NULL,'ISERNIA',1),(156,'SP',308,NULL,NULL,NULL,'LA SPEZIA',1),(157,'AQ',301,NULL,NULL,NULL,'L AQUILA',1),(158,'LT',307,NULL,NULL,NULL,'LATINA',1),(159,'LE',313,NULL,NULL,NULL,'LECCE',1),(160,'LC',309,NULL,NULL,NULL,'LECCO',1),(161,'LI',314,NULL,NULL,NULL,'LIVORNO',1),(162,'LO',309,NULL,NULL,NULL,'LODI',1),(163,'LU',316,NULL,NULL,NULL,'LUCCA',1),(164,'MC',310,NULL,NULL,NULL,'MACERATA',1),(165,'MN',309,NULL,NULL,NULL,'MANTOVA',1),(166,'MS',316,NULL,NULL,NULL,'MASSA-CARRARA',1),(167,'MT',302,NULL,NULL,NULL,'MATERA',1),(168,'VS',314,NULL,NULL,NULL,'MEDIO CAMPIDANO',1),(169,'ME',315,NULL,NULL,NULL,'MESSINA',1),(170,'MI',309,NULL,NULL,NULL,'MILANO',1),(171,'MB',309,NULL,NULL,NULL,'MONZA e BRIANZA',1),(172,'MO',305,NULL,NULL,NULL,'MODENA',1),(173,'NA',304,NULL,NULL,NULL,'NAPOLI',1),(174,'NO',312,NULL,NULL,NULL,'NOVARA',1),(175,'NU',314,NULL,NULL,NULL,'NUORO',1),(176,'OG',314,NULL,NULL,NULL,'OGLIASTRA',1),(177,'OT',314,NULL,NULL,NULL,'OLBIA-TEMPIO',1),(178,'OR',314,NULL,NULL,NULL,'ORISTANO',1),(179,'PD',320,NULL,NULL,NULL,'PADOVA',1),(180,'PA',315,NULL,NULL,NULL,'PALERMO',1),(181,'PR',305,NULL,NULL,NULL,'PARMA',1),(182,'PV',309,NULL,NULL,NULL,'PAVIA',1),(183,'PG',318,NULL,NULL,NULL,'PERUGIA',1),(184,'PU',310,NULL,NULL,NULL,'PESARO e URBINO',1),(185,'PE',301,NULL,NULL,NULL,'PESCARA',1),(186,'PC',305,NULL,NULL,NULL,'PIACENZA',1),(187,'PI',316,NULL,NULL,NULL,'PISA',1),(188,'PT',316,NULL,NULL,NULL,'PISTOIA',1),(189,'PN',306,NULL,NULL,NULL,'PORDENONE',1),(190,'PZ',302,NULL,NULL,NULL,'POTENZA',1),(191,'PO',316,NULL,NULL,NULL,'PRATO',1),(192,'RG',315,NULL,NULL,NULL,'RAGUSA',1),(193,'RA',305,NULL,NULL,NULL,'RAVENNA',1),(194,'RC',303,NULL,NULL,NULL,'REGGIO CALABRIA',1),(195,'RE',305,NULL,NULL,NULL,'REGGIO NELL EMILIA',1),(196,'RI',307,NULL,NULL,NULL,'RIETI',1),(197,'RN',305,NULL,NULL,NULL,'RIMINI',1),(198,'RM',307,NULL,NULL,NULL,'ROMA',1),(199,'RO',320,NULL,NULL,NULL,'ROVIGO',1),(200,'SA',304,NULL,NULL,NULL,'SALERNO',1),(201,'SS',314,NULL,NULL,NULL,'SASSARI',1),(202,'SV',308,NULL,NULL,NULL,'SAVONA',1),(203,'SI',316,NULL,NULL,NULL,'SIENA',1),(204,'SR',315,NULL,NULL,NULL,'SIRACUSA',1),(205,'SO',309,NULL,NULL,NULL,'SONDRIO',1),(206,'TA',313,NULL,NULL,NULL,'TARANTO',1),(207,'TE',301,NULL,NULL,NULL,'TERAMO',1),(208,'TR',318,NULL,NULL,NULL,'TERNI',1),(209,'TO',312,NULL,NULL,NULL,'TORINO',1),(210,'TP',315,NULL,NULL,NULL,'TRAPANI',1),(211,'TN',317,NULL,NULL,NULL,'TRENTO',1),(212,'TV',320,NULL,NULL,NULL,'TREVISO',1),(213,'TS',306,NULL,NULL,NULL,'TRIESTE',1),(214,'UD',306,NULL,NULL,NULL,'UDINE',1),(215,'VA',309,NULL,NULL,NULL,'VARESE',1),(216,'VE',320,NULL,NULL,NULL,'VENEZIA',1),(217,'VB',312,NULL,NULL,NULL,'VERBANO-CUSIO-OSSOLA',1),(218,'VC',312,NULL,NULL,NULL,'VERCELLI',1),(219,'VR',320,NULL,NULL,NULL,'VERONA',1),(220,'VV',303,NULL,NULL,NULL,'VIBO VALENTIA',1),(221,'VI',320,NULL,NULL,NULL,'VICENZA',1),(222,'VT',307,NULL,NULL,NULL,'VITERBO',1),(223,'NSW',2801,'',1,'','New South Wales',1),(224,'VIC',2801,'',1,'','Victoria',1),(225,'QLD',2801,'',1,'','Queensland',1),(226,'SA',2801,'',1,'','South Australia',1),(227,'ACT',2801,'',1,'','Australia Capital Territory',1),(228,'TAS',2801,'',1,'','Tasmania',1),(229,'WA',2801,'',1,'','Western Australia',1),(230,'NT',2801,'',1,'','Northern Territory',1),(231,'01',419,'',19,'PAIS VASCO','País Vasco',1),(232,'02',404,'',4,'ALBACETE','Albacete',1),(233,'03',411,'',11,'ALICANTE','Alicante',1),(234,'04',401,'',1,'ALMERIA','Almería',1),(235,'05',403,'',3,'AVILA','Avila',1),(236,'06',412,'',12,'BADAJOZ','Badajoz',1),(237,'07',414,'',14,'ISLAS BALEARES','Islas Baleares',1),(238,'08',406,'',6,'BARCELONA','Barcelona',1),(239,'09',403,'',8,'BURGOS','Burgos',1),(240,'10',412,'',12,'CACERES','Cáceres',1),(241,'11',401,'',1,'CADIz','Cádiz',1),(242,'12',411,'',11,'CASTELLON','Castellón',1),(243,'13',404,'',4,'CIUDAD REAL','Ciudad Real',1),(244,'14',401,'',1,'CORDOBA','Córdoba',1),(245,'15',413,'',13,'LA CORUÑA','La Coruña',1),(246,'16',404,'',4,'CUENCA','Cuenca',1),(247,'17',406,'',6,'GERONA','Gerona',1),(248,'18',401,'',1,'GRANADA','Granada',1),(249,'19',404,'',4,'GUADALAJARA','Guadalajara',1),(250,'20',419,'',19,'GUIPUZCOA','Guipúzcoa',1),(251,'21',401,'',1,'HUELVA','Huelva',1),(252,'22',402,'',2,'HUESCA','Huesca',1),(253,'23',401,'',1,'JAEN','Jaén',1),(254,'24',403,'',3,'LEON','León',1),(255,'25',406,'',6,'LERIDA','Lérida',1),(256,'26',415,'',15,'LA RIOJA','La Rioja',1),(257,'27',413,'',13,'LUGO','Lugo',1),(258,'28',416,'',16,'MADRID','Madrid',1),(259,'29',401,'',1,'MALAGA','Málaga',1),(260,'30',417,'',17,'MURCIA','Murcia',1),(261,'31',408,'',8,'NAVARRA','Navarra',1),(262,'32',413,'',13,'ORENSE','Orense',1),(263,'33',418,'',18,'ASTURIAS','Asturias',1),(264,'34',403,'',3,'PALENCIA','Palencia',1),(265,'35',405,'',5,'LAS PALMAS','Las Palmas',1),(266,'36',413,'',13,'PONTEVEDRA','Pontevedra',1),(267,'37',403,'',3,'SALAMANCA','Salamanca',1),(268,'38',405,'',5,'STA. CRUZ DE TENERIFE','Sta. Cruz de Tenerife',1),(269,'39',410,'',10,'CANTABRIA','Cantabria',1),(270,'40',403,'',3,'SEGOVIA','Segovia',1),(271,'41',401,'',1,'SEVILLA','Sevilla',1),(272,'42',403,'',3,'SORIA','Soria',1),(273,'43',406,'',6,'TARRAGONA','Tarragona',1),(274,'44',402,'',2,'TERUEL','Teruel',1),(275,'45',404,'',5,'TOLEDO','Toledo',1),(276,'46',411,'',11,'VALENCIA','Valencia',1),(277,'47',403,'',3,'VALLADOLID','Valladolid',1),(278,'48',419,'',19,'VIZCAYA','Vizcaya',1),(279,'49',403,'',3,'ZAMORA','Zamora',1),(280,'50',402,'',1,'ZARAGOZA','Zaragoza',1),(281,'51',407,'',7,'CEUTA','Ceuta',1),(282,'52',409,'',9,'MELILLA','Melilla',1),(283,'53',420,'',20,'OTROS','Otros',1),(284,'BW',501,NULL,NULL,'BADEN-WÜRTTEMBERG','Baden-Württemberg',1),(285,'BY',501,NULL,NULL,'BAYERN','Bayern',1),(286,'BE',501,NULL,NULL,'BERLIN','Berlin',1),(287,'BB',501,NULL,NULL,'BRANDENBURG','Brandenburg',1),(288,'HB',501,NULL,NULL,'BREMEN','Bremen',1),(289,'HH',501,NULL,NULL,'HAMBURG','Hamburg',1),(290,'HE',501,NULL,NULL,'HESSEN','Hessen',1),(291,'MV',501,NULL,NULL,'MECKLENBURG-VORPOMMERN','Mecklenburg-Vorpommern',1),(292,'NI',501,NULL,NULL,'NIEDERSACHSEN','Niedersachsen',1),(293,'NW',501,NULL,NULL,'NORDRHEIN-WESTFALEN','Nordrhein-Westfalen',1),(294,'RP',501,NULL,NULL,'RHEINLAND-PFALZ','Rheinland-Pfalz',1),(295,'SL',501,NULL,NULL,'SAARLAND','Saarland',1),(296,'SN',501,NULL,NULL,'SACHSEN','Sachsen',1),(297,'ST',501,NULL,NULL,'SACHSEN-ANHALT','Sachsen-Anhalt',1),(298,'SH',501,NULL,NULL,'SCHLESWIG-HOLSTEIN','Schleswig-Holstein',1),(299,'TH',501,NULL,NULL,'THÜRINGEN','Thüringen',1),(300,'AG',601,NULL,NULL,'ARGOVIE','Argovie',1),(301,'AI',601,NULL,NULL,'APPENZELL RHODES INTERIEURES','Appenzell Rhodes intérieures',1),(302,'AR',601,NULL,NULL,'APPENZELL RHODES EXTERIEURES','Appenzell Rhodes extérieures',1),(303,'BE',601,NULL,NULL,'BERNE','Berne',1),(304,'BL',601,NULL,NULL,'BALE CAMPAGNE','Bâle Campagne',1),(305,'BS',601,NULL,NULL,'BALE VILLE','Bâle Ville',1),(306,'FR',601,NULL,NULL,'FRIBOURG','Fribourg',1),(307,'GE',601,NULL,NULL,'GENEVE','Genève',1),(308,'GL',601,NULL,NULL,'GLARIS','Glaris',1),(309,'GR',601,NULL,NULL,'GRISONS','Grisons',1),(310,'JU',601,NULL,NULL,'JURA','Jura',1),(311,'LU',601,NULL,NULL,'LUCERNE','Lucerne',1),(312,'NE',601,NULL,NULL,'NEUCHATEL','Neuchâtel',1),(313,'NW',601,NULL,NULL,'NIDWALD','Nidwald',1),(314,'OW',601,NULL,NULL,'OBWALD','Obwald',1),(315,'SG',601,NULL,NULL,'SAINT-GALL','Saint-Gall',1),(316,'SH',601,NULL,NULL,'SCHAFFHOUSE','Schaffhouse',1),(317,'SO',601,NULL,NULL,'SOLEURE','Soleure',1),(318,'SZ',601,NULL,NULL,'SCHWYZ','Schwyz',1),(319,'TG',601,NULL,NULL,'THURGOVIE','Thurgovie',1),(320,'TI',601,NULL,NULL,'TESSIN','Tessin',1),(321,'UR',601,NULL,NULL,'URI','Uri',1),(322,'VD',601,NULL,NULL,'VAUD','Vaud',1),(323,'VS',601,NULL,NULL,'VALAIS','Valais',1),(324,'ZG',601,NULL,NULL,'ZUG','Zug',1),(325,'ZH',601,NULL,NULL,'ZURICH','Zürich',1),(326,'AL',1101,'',0,'ALABAMA','Alabama',1),(327,'AK',1101,'',0,'ALASKA','Alaska',1),(328,'AZ',1101,'',0,'ARIZONA','Arizona',1),(329,'AR',1101,'',0,'ARKANSAS','Arkansas',1),(330,'CA',1101,'',0,'CALIFORNIA','California',1),(331,'CO',1101,'',0,'COLORADO','Colorado',1),(332,'CT',1101,'',0,'CONNECTICUT','Connecticut',1),(333,'DE',1101,'',0,'DELAWARE','Delaware',1),(334,'FL',1101,'',0,'FLORIDA','Florida',1),(335,'GA',1101,'',0,'GEORGIA','Georgia',1),(336,'HI',1101,'',0,'HAWAII','Hawaii',1),(337,'ID',1101,'',0,'IDAHO','Idaho',1),(338,'IL',1101,'',0,'ILLINOIS','Illinois',1),(339,'IN',1101,'',0,'INDIANA','Indiana',1),(340,'IA',1101,'',0,'IOWA','Iowa',1),(341,'KS',1101,'',0,'KANSAS','Kansas',1),(342,'KY',1101,'',0,'KENTUCKY','Kentucky',1),(343,'LA',1101,'',0,'LOUISIANA','Louisiana',1),(344,'ME',1101,'',0,'MAINE','Maine',1),(345,'MD',1101,'',0,'MARYLAND','Maryland',1),(346,'MA',1101,'',0,'MASSACHUSSETTS','Massachusetts',1),(347,'MI',1101,'',0,'MICHIGAN','Michigan',1),(348,'MN',1101,'',0,'MINNESOTA','Minnesota',1),(349,'MS',1101,'',0,'MISSISSIPPI','Mississippi',1),(350,'MO',1101,'',0,'MISSOURI','Missouri',1),(351,'MT',1101,'',0,'MONTANA','Montana',1),(352,'NE',1101,'',0,'NEBRASKA','Nebraska',1),(353,'NV',1101,'',0,'NEVADA','Nevada',1),(354,'NH',1101,'',0,'NEW HAMPSHIRE','New Hampshire',1),(355,'NJ',1101,'',0,'NEW JERSEY','New Jersey',1),(356,'NM',1101,'',0,'NEW MEXICO','New Mexico',1),(357,'NY',1101,'',0,'NEW YORK','New York',1),(358,'NC',1101,'',0,'NORTH CAROLINA','North Carolina',1),(359,'ND',1101,'',0,'NORTH DAKOTA','North Dakota',1),(360,'OH',1101,'',0,'OHIO','Ohio',1),(361,'OK',1101,'',0,'OKLAHOMA','Oklahoma',1),(362,'OR',1101,'',0,'OREGON','Oregon',1),(363,'PA',1101,'',0,'PENNSYLVANIA','Pennsylvania',1),(364,'RI',1101,'',0,'RHODE ISLAND','Rhode Island',1),(365,'SC',1101,'',0,'SOUTH CAROLINA','South Carolina',1),(366,'SD',1101,'',0,'SOUTH DAKOTA','South Dakota',1),(367,'TN',1101,'',0,'TENNESSEE','Tennessee',1),(368,'TX',1101,'',0,'TEXAS','Texas',1),(369,'UT',1101,'',0,'UTAH','Utah',1),(370,'VT',1101,'',0,'VERMONT','Vermont',1),(371,'VA',1101,'',0,'VIRGINIA','Virginia',1),(372,'WA',1101,'',0,'WASHINGTON','Washington',1),(373,'WV',1101,'',0,'WEST VIRGINIA','West Virginia',1),(374,'WI',1101,'',0,'WISCONSIN','Wisconsin',1),(375,'WY',1101,'',0,'WYOMING','Wyoming',1),(376,'SS',8601,NULL,NULL,NULL,'San Salvador',1),(377,'SA',8603,NULL,NULL,NULL,'Santa Ana',1),(378,'AH',8603,NULL,NULL,NULL,'Ahuachapan',1),(379,'SO',8603,NULL,NULL,NULL,'Sonsonate',1),(380,'US',8602,NULL,NULL,NULL,'Usulutan',1),(381,'SM',8602,NULL,NULL,NULL,'San Miguel',1),(382,'MO',8602,NULL,NULL,NULL,'Morazan',1),(383,'LU',8602,NULL,NULL,NULL,'La Union',1),(384,'LL',8601,NULL,NULL,NULL,'La Libertad',1),(385,'CH',8601,NULL,NULL,NULL,'Chalatenango',1),(386,'CA',8601,NULL,NULL,NULL,'Cabañas',1),(387,'LP',8601,NULL,NULL,NULL,'La Paz',1),(388,'SV',8601,NULL,NULL,NULL,'San Vicente',1),(389,'CU',8601,NULL,NULL,NULL,'Cuscatlan',1),(390,'2301',2301,'',0,'CATAMARCA','Catamarca',1),(391,'2302',2301,'',0,'JUJUY','Jujuy',1),(392,'2303',2301,'',0,'TUCAMAN','Tucamán',1),(393,'2304',2301,'',0,'SANTIAGO DEL ESTERO','Santiago del Estero',1),(394,'2305',2301,'',0,'SALTA','Salta',1),(395,'2306',2302,'',0,'CHACO','Chaco',1),(396,'2307',2302,'',0,'CORRIENTES','Corrientes',1),(397,'2308',2302,'',0,'ENTRE RIOS','Entre Ríos',1),(398,'2309',2302,'',0,'FORMOSA MISIONES','Formosa Misiones',1),(399,'2310',2302,'',0,'SANTA FE','Santa Fe',1),(400,'2311',2303,'',0,'LA RIOJA','La Rioja',1),(401,'2312',2303,'',0,'MENDOZA','Mendoza',1),(402,'2313',2303,'',0,'SAN JUAN','San Juan',1),(403,'2314',2303,'',0,'SAN LUIS','San Luis',1),(404,'2315',2304,'',0,'CORDOBA','Córdoba',1),(405,'2316',2304,'',0,'BUENOS AIRES','Buenos Aires',1),(406,'2317',2304,'',0,'CABA','Caba',1),(407,'2318',2305,'',0,'LA PAMPA','La Pampa',1),(408,'2319',2305,'',0,'NEUQUEN','Neuquén',1),(409,'2320',2305,'',0,'RIO NEGRO','Río Negro',1),(410,'2321',2305,'',0,'CHUBUT','Chubut',1),(411,'2322',2305,'',0,'SANTA CRUZ','Santa Cruz',1),(412,'2323',2305,'',0,'TIERRA DEL FUEGO','Tierra del Fuego',1),(413,'2324',2305,'',0,'ISLAS MALVINAS','Islas Malvinas',1),(414,'2325',2305,'',0,'ANTARTIDA','Antártida',1),(415,'AC',5601,'ACRE',0,'AC','Acre',1),(416,'AL',5601,'ALAGOAS',0,'AL','Alagoas',1),(417,'AP',5601,'AMAPA',0,'AP','Amapá',1),(418,'AM',5601,'AMAZONAS',0,'AM','Amazonas',1),(419,'BA',5601,'BAHIA',0,'BA','Bahia',1),(420,'CE',5601,'CEARA',0,'CE','Ceará',1),(421,'ES',5601,'ESPIRITO SANTO',0,'ES','Espirito Santo',1),(422,'GO',5601,'GOIAS',0,'GO','Goiás',1),(423,'MA',5601,'MARANHAO',0,'MA','Maranhão',1),(424,'MT',5601,'MATO GROSSO',0,'MT','Mato Grosso',1),(425,'MS',5601,'MATO GROSSO DO SUL',0,'MS','Mato Grosso do Sul',1),(426,'MG',5601,'MINAS GERAIS',0,'MG','Minas Gerais',1),(427,'PA',5601,'PARA',0,'PA','Pará',1),(428,'PB',5601,'PARAIBA',0,'PB','Paraiba',1),(429,'PR',5601,'PARANA',0,'PR','Paraná',1),(430,'PE',5601,'PERNAMBUCO',0,'PE','Pernambuco',1),(431,'PI',5601,'PIAUI',0,'PI','Piauí',1),(432,'RJ',5601,'RIO DE JANEIRO',0,'RJ','Rio de Janeiro',1),(433,'RN',5601,'RIO GRANDE DO NORTE',0,'RN','Rio Grande do Norte',1),(434,'RS',5601,'RIO GRANDE DO SUL',0,'RS','Rio Grande do Sul',1),(435,'RO',5601,'RONDONIA',0,'RO','Rondônia',1),(436,'RR',5601,'RORAIMA',0,'RR','Roraima',1),(437,'SC',5601,'SANTA CATARINA',0,'SC','Santa Catarina',1),(438,'SE',5601,'SERGIPE',0,'SE','Sergipe',1),(439,'SP',5601,'SAO PAULO',0,'SP','Sao Paulo',1),(440,'TO',5601,'TOCANTINS',0,'TO','Tocantins',1),(441,'DF',5601,'DISTRITO FEDERAL',0,'DF','Distrito Federal',1),(442,'151',6715,'',0,'151','Arica',1),(443,'152',6715,'',0,'152','Parinacota',1),(444,'011',6701,'',0,'011','Iquique',1),(445,'014',6701,'',0,'014','Tamarugal',1),(446,'021',6702,'',0,'021','Antofagasa',1),(447,'022',6702,'',0,'022','El Loa',1),(448,'023',6702,'',0,'023','Tocopilla',1),(449,'031',6703,'',0,'031','Copiapó',1),(450,'032',6703,'',0,'032','Chañaral',1),(451,'033',6703,'',0,'033','Huasco',1),(452,'041',6704,'',0,'041','Elqui',1),(453,'042',6704,'',0,'042','Choapa',1),(454,'043',6704,'',0,'043','Limarí',1),(455,'051',6705,'',0,'051','Valparaíso',1),(456,'052',6705,'',0,'052','Isla de Pascua',1),(457,'053',6705,'',0,'053','Los Andes',1),(458,'054',6705,'',0,'054','Petorca',1),(459,'055',6705,'',0,'055','Quillota',1),(460,'056',6705,'',0,'056','San Antonio',1),(461,'057',6705,'',0,'057','San Felipe de Aconcagua',1),(462,'058',6705,'',0,'058','Marga Marga',1),(463,'061',6706,'',0,'061','Cachapoal',1),(464,'062',6706,'',0,'062','Cardenal Caro',1),(465,'063',6706,'',0,'063','Colchagua',1),(466,'071',6707,'',0,'071','Talca',1),(467,'072',6707,'',0,'072','Cauquenes',1),(468,'073',6707,'',0,'073','Curicó',1),(469,'074',6707,'',0,'074','Linares',1),(470,'081',6708,'',0,'081','Concepción',1),(471,'082',6708,'',0,'082','Arauco',1),(472,'083',6708,'',0,'083','Biobío',1),(473,'084',6708,'',0,'084','Ñuble',1),(474,'091',6709,'',0,'091','Cautín',1),(475,'092',6709,'',0,'092','Malleco',1),(476,'141',6714,'',0,'141','Valdivia',1),(477,'142',6714,'',0,'142','Ranco',1),(478,'101',6710,'',0,'101','Llanquihue',1),(479,'102',6710,'',0,'102','Chiloé',1),(480,'103',6710,'',0,'103','Osorno',1),(481,'104',6710,'',0,'104','Palena',1),(482,'111',6711,'',0,'111','Coihaique',1),(483,'112',6711,'',0,'112','Aisén',1),(484,'113',6711,'',0,'113','Capitán Prat',1),(485,'114',6711,'',0,'114','General Carrera',1),(486,'121',6712,'',0,'121','Magallanes',1),(487,'122',6712,'',0,'122','Antártica Chilena',1),(488,'123',6712,'',0,'123','Tierra del Fuego',1),(489,'124',6712,'',0,'124','Última Esperanza',1),(490,'131',6713,'',0,'131','Santiago',1),(491,'132',6713,'',0,'132','Cordillera',1),(492,'133',6713,'',0,'133','Chacabuco',1),(493,'134',6713,'',0,'134','Maipo',1),(494,'135',6713,'',0,'135','Melipilla',1),(495,'136',6713,'',0,'136','Talagante',1),(496,'AN',11701,NULL,0,'AN','Andaman & Nicobar',1),(497,'AP',11701,NULL,0,'AP','Andhra Pradesh',1),(498,'AR',11701,NULL,0,'AR','Arunachal Pradesh',1),(499,'AS',11701,NULL,0,'AS','Assam',1),(500,'BR',11701,NULL,0,'BR','Bihar',1),(501,'CG',11701,NULL,0,'CG','Chattisgarh',1),(502,'CH',11701,NULL,0,'CH','Chandigarh',1),(503,'DD',11701,NULL,0,'DD','Daman & Diu',1),(504,'DL',11701,NULL,0,'DL','Delhi',1),(505,'DN',11701,NULL,0,'DN','Dadra and Nagar Haveli',1),(506,'GA',11701,NULL,0,'GA','Goa',1),(507,'GJ',11701,NULL,0,'GJ','Gujarat',1),(508,'HP',11701,NULL,0,'HP','Himachal Pradesh',1),(509,'HR',11701,NULL,0,'HR','Haryana',1),(510,'JH',11701,NULL,0,'JH','Jharkhand',1),(511,'JK',11701,NULL,0,'JK','Jammu & Kashmir',1),(512,'KA',11701,NULL,0,'KA','Karnataka',1),(513,'KL',11701,NULL,0,'KL','Kerala',1),(514,'LD',11701,NULL,0,'LD','Lakshadweep',1),(515,'MH',11701,NULL,0,'MH','Maharashtra',1),(516,'ML',11701,NULL,0,'ML','Meghalaya',1),(517,'MN',11701,NULL,0,'MN','Manipur',1),(518,'MP',11701,NULL,0,'MP','Madhya Pradesh',1),(519,'MZ',11701,NULL,0,'MZ','Mizoram',1),(520,'NL',11701,NULL,0,'NL','Nagaland',1),(521,'OR',11701,NULL,0,'OR','Orissa',1),(522,'PB',11701,NULL,0,'PB','Punjab',1),(523,'PY',11701,NULL,0,'PY','Puducherry',1),(524,'RJ',11701,NULL,0,'RJ','Rajasthan',1),(525,'SK',11701,NULL,0,'SK','Sikkim',1),(526,'TN',11701,NULL,0,'TN','Tamil Nadu',1),(527,'TR',11701,NULL,0,'TR','Tripura',1),(528,'UL',11701,NULL,0,'UL','Uttarakhand',1),(529,'UP',11701,NULL,0,'UP','Uttar Pradesh',1),(530,'WB',11701,NULL,0,'WB','West Bengal',1),(531,'DIF',15401,'',0,'DIF','Distrito Federal',1),(532,'AGS',15401,'',0,'AGS','Aguascalientes',1),(533,'BCN',15401,'',0,'BCN','Baja California Norte',1),(534,'BCS',15401,'',0,'BCS','Baja California Sur',1),(535,'CAM',15401,'',0,'CAM','Campeche',1),(536,'CHP',15401,'',0,'CHP','Chiapas',1),(537,'CHI',15401,'',0,'CHI','Chihuahua',1),(538,'COA',15401,'',0,'COA','Coahuila',1),(539,'COL',15401,'',0,'COL','Colima',1),(540,'DUR',15401,'',0,'DUR','Durango',1),(541,'GTO',15401,'',0,'GTO','Guanajuato',1),(542,'GRO',15401,'',0,'GRO','Guerrero',1),(543,'HGO',15401,'',0,'HGO','Hidalgo',1),(544,'JAL',15401,'',0,'JAL','Jalisco',1),(545,'MEX',15401,'',0,'MEX','México',1),(546,'MIC',15401,'',0,'MIC','Michoacán de Ocampo',1),(547,'MOR',15401,'',0,'MOR','Morelos',1),(548,'NAY',15401,'',0,'NAY','Nayarit',1),(549,'NLE',15401,'',0,'NLE','Nuevo León',1),(550,'OAX',15401,'',0,'OAX','Oaxaca',1),(551,'PUE',15401,'',0,'PUE','Puebla',1),(552,'QRO',15401,'',0,'QRO','Querétaro',1),(553,'ROO',15401,'',0,'ROO','Quintana Roo',1),(554,'SLP',15401,'',0,'SLP','San Luis Potosí',1),(555,'SIN',15401,'',0,'SIN','Sinaloa',1),(556,'SON',15401,'',0,'SON','Sonora',1),(557,'TAB',15401,'',0,'TAB','Tabasco',1),(558,'TAM',15401,'',0,'TAM','Tamaulipas',1),(559,'TLX',15401,'',0,'TLX','Tlaxcala',1),(560,'VER',15401,'',0,'VER','Veracruz',1),(561,'YUC',15401,'',0,'YUC','Yucatán',1),(562,'ZAC',15401,'',0,'ZAC','Zacatecas',1),(563,'ANT',7001,'',0,'ANT','Antioquia',1),(564,'BOL',7001,'',0,'BOL','Bolívar',1),(565,'BOY',7001,'',0,'BOY','Boyacá',1),(566,'CAL',7001,'',0,'CAL','Caldas',1),(567,'CAU',7001,'',0,'CAU','Cauca',1),(568,'CUN',7001,'',0,'CUN','Cundinamarca',1),(569,'HUI',7001,'',0,'HUI','Huila',1),(570,'LAG',7001,'',0,'LAG','La Guajira',1),(571,'MET',7001,'',0,'MET','Meta',1),(572,'NAR',7001,'',0,'NAR','Nariño',1),(573,'NDS',7001,'',0,'NDS','Norte de Santander',1),(574,'SAN',7001,'',0,'SAN','Santander',1),(575,'SUC',7001,'',0,'SUC','Sucre',1),(576,'TOL',7001,'',0,'TOL','Tolima',1),(577,'VAC',7001,'',0,'VAC','Valle del Cauca',1),(578,'RIS',7001,'',0,'RIS','Risalda',1),(579,'ATL',7001,'',0,'ATL','Atlántico',1),(580,'COR',7001,'',0,'COR','Córdoba',1),(581,'SAP',7001,'',0,'SAP','San Andrés, Providencia y Santa Catalina',1),(582,'ARA',7001,'',0,'ARA','Arauca',1),(583,'CAS',7001,'',0,'CAS','Casanare',1),(584,'AMA',7001,'',0,'AMA','Amazonas',1),(585,'CAQ',7001,'',0,'CAQ','Caquetá',1),(586,'CHO',7001,'',0,'CHO','Chocó',1),(587,'GUA',7001,'',0,'GUA','Guainía',1),(588,'GUV',7001,'',0,'GUV','Guaviare',1),(589,'PUT',7001,'',0,'PUT','Putumayo',1),(590,'QUI',7001,'',0,'QUI','Quindío',1),(591,'VAU',7001,'',0,'VAU','Vaupés',1),(592,'BOG',7001,'',0,'BOG','Bogotá',1),(593,'VID',7001,'',0,'VID','Vichada',1),(594,'CES',7001,'',0,'CES','Cesar',1),(595,'MAG',7001,'',0,'MAG','Magdalena',1),(596,'AT',11401,'',0,'AT','Atlántida',1),(597,'CH',11401,'',0,'CH','Choluteca',1),(598,'CL',11401,'',0,'CL','Colón',1),(599,'CM',11401,'',0,'CM','Comayagua',1),(600,'CO',11401,'',0,'CO','Copán',1),(601,'CR',11401,'',0,'CR','Cortés',1),(602,'EP',11401,'',0,'EP','El Paraíso',1),(603,'FM',11401,'',0,'FM','Francisco Morazán',1),(604,'GD',11401,'',0,'GD','Gracias a Dios',1),(605,'IN',11401,'',0,'IN','Intibucá',1),(606,'IB',11401,'',0,'IB','Islas de la Bahía',1),(607,'LP',11401,'',0,'LP','La Paz',1),(608,'LM',11401,'',0,'LM','Lempira',1),(609,'OC',11401,'',0,'OC','Ocotepeque',1),(610,'OL',11401,'',0,'OL','Olancho',1),(611,'SB',11401,'',0,'SB','Santa Bárbara',1),(612,'VL',11401,'',0,'VL','Valle',1),(613,'YO',11401,'',0,'YO','Yoro',1),(614,'DC',11401,'',0,'DC','Distrito Central',1),(615,'CC',4601,'Oistins',0,'CC','Christ Church',1),(616,'SA',4601,'Greenland',0,'SA','Saint Andrew',1),(617,'SG',4601,'Bulkeley',0,'SG','Saint George',1),(618,'JA',4601,'Holetown',0,'JA','Saint James',1),(619,'SJ',4601,'Four Roads',0,'SJ','Saint John',1),(620,'SB',4601,'Bathsheba',0,'SB','Saint Joseph',1),(621,'SL',4601,'Crab Hill',0,'SL','Saint Lucy',1),(622,'SM',4601,'Bridgetown',0,'SM','Saint Michael',1),(623,'SP',4601,'Speightstown',0,'SP','Saint Peter',1),(624,'SC',4601,'Crane',0,'SC','Saint Philip',1),(625,'ST',4601,'Hillaby',0,'ST','Saint Thomas',1),(626,'VE-L',23201,'',0,'VE-L','Mérida',1),(627,'VE-T',23201,'',0,'VE-T','Trujillo',1),(628,'VE-E',23201,'',0,'VE-E','Barinas',1),(629,'VE-M',23202,'',0,'VE-M','Miranda',1),(630,'VE-W',23202,'',0,'VE-W','Vargas',1),(631,'VE-A',23202,'',0,'VE-A','Distrito Capital',1),(632,'VE-D',23203,'',0,'VE-D','Aragua',1),(633,'VE-G',23203,'',0,'VE-G','Carabobo',1),(634,'VE-I',23204,'',0,'VE-I','Falcón',1),(635,'VE-K',23204,'',0,'VE-K','Lara',1),(636,'VE-U',23204,'',0,'VE-U','Yaracuy',1),(637,'VE-F',23205,'',0,'VE-F','Bolívar',1),(638,'VE-X',23205,'',0,'VE-X','Amazonas',1),(639,'VE-Y',23205,'',0,'VE-Y','Delta Amacuro',1),(640,'VE-O',23206,'',0,'VE-O','Nueva Esparta',1),(641,'VE-Z',23206,'',0,'VE-Z','Dependencias Federales',1),(642,'VE-C',23207,'',0,'VE-C','Apure',1),(643,'VE-J',23207,'',0,'VE-J','Guárico',1),(644,'VE-H',23207,'',0,'VE-H','Cojedes',1),(645,'VE-P',23207,'',0,'VE-P','Portuguesa',1),(646,'VE-B',23208,'',0,'VE-B','Anzoátegui',1),(647,'VE-N',23208,'',0,'VE-N','Monagas',1),(648,'VE-R',23208,'',0,'VE-R','Sucre',1),(649,'VE-V',23209,'',0,'VE-V','Zulia',1),(650,'VE-S',23209,'',0,'VE-S','Táchira',1);
/*!40000 ALTER TABLE `llx_c_departements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_ecotaxe`
--

DROP TABLE IF EXISTS `llx_c_ecotaxe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_ecotaxe` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` double(24,8) DEFAULT NULL,
  `organization` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_pays` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_ecotaxe` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_ecotaxe`
--

LOCK TABLES `llx_c_ecotaxe` WRITE;
/*!40000 ALTER TABLE `llx_c_ecotaxe` DISABLE KEYS */;
INSERT INTO `llx_c_ecotaxe` VALUES (1,'ER-A-A','Materiels electriques < 0,2kg',0.01000000,'ERP',1,1),(2,'ER-A-B','Materiels electriques >= 0,2 kg et < 0,5 kg',0.03000000,'ERP',1,1),(3,'ER-A-C','Materiels electriques >= 0,5 kg et < 1 kg',0.04000000,'ERP',1,1),(4,'ER-A-D','Materiels electriques >= 1 kg et < 2 kg',0.13000000,'ERP',1,1),(5,'ER-A-E','Materiels electriques >= 2 kg et < 4kg',0.21000000,'ERP',1,1),(6,'ER-A-F','Materiels electriques >= 4 kg et < 8 kg',0.42000000,'ERP',1,1),(7,'ER-A-G','Materiels electriques >= 8 kg et < 15 kg',0.84000000,'ERP',1,1),(8,'ER-A-H','Materiels electriques >= 15 kg et < 20 kg',1.25000000,'ERP',1,1),(9,'ER-A-I','Materiels electriques >= 20 kg et < 30 kg',1.88000000,'ERP',1,1),(10,'ER-A-J','Materiels electriques >= 30 kg',3.34000000,'ERP',1,1),(11,'ER-M-1','TV, Moniteurs < 9kg',0.84000000,'ERP',1,1),(12,'ER-M-2','TV, Moniteurs >= 9kg et < 15kg',1.67000000,'ERP',1,1),(13,'ER-M-3','TV, Moniteurs >= 15kg et < 30kg',3.34000000,'ERP',1,1),(14,'ER-M-4','TV, Moniteurs >= 30 kg',6.69000000,'ERP',1,1),(15,'EC-A-A','Materiels electriques  0,2 kg max',0.00840000,'Ecologic',1,1),(16,'EC-A-B','Materiels electriques 0,21 kg min - 0,50 kg max',0.02500000,'Ecologic',1,1),(17,'EC-A-C','Materiels electriques  0,51 kg min - 1 kg max',0.04000000,'Ecologic',1,1),(18,'EC-A-D','Materiels electriques  1,01 kg min - 2,5 kg max',0.13000000,'Ecologic',1,1),(19,'EC-A-E','Materiels electriques  2,51 kg min - 4 kg max',0.21000000,'Ecologic',1,1),(20,'EC-A-F','Materiels electriques 4,01 kg min - 8 kg max',0.42000000,'Ecologic',1,1),(21,'EC-A-G','Materiels electriques  8,01 kg min - 12 kg max',0.63000000,'Ecologic',1,1),(22,'EC-A-H','Materiels electriques 12,01 kg min - 20 kg max',1.05000000,'Ecologic',1,1),(23,'EC-A-I','Materiels electriques  20,01 kg min',1.88000000,'Ecologic',1,1),(24,'EC-M-1','TV, Moniteurs 9 kg max',0.84000000,'Ecologic',1,1),(25,'EC-M-2','TV, Moniteurs 9,01 kg min - 18 kg max',1.67000000,'Ecologic',1,1),(26,'EC-M-3','TV, Moniteurs 18,01 kg min - 36 kg max',3.34000000,'Ecologic',1,1),(27,'EC-M-4','TV, Moniteurs 36,01 kg min',6.69000000,'Ecologic',1,1),(28,'ES-M-1','TV, Moniteurs <= 20 pouces',0.84000000,'Eco-systemes',1,1),(29,'ES-M-2','TV, Moniteurs > 20 pouces et <= 32 pouces',3.34000000,'Eco-systemes',1,1),(30,'ES-M-3','TV, Moniteurs > 32 pouces et autres grands ecrans',6.69000000,'Eco-systemes',1,1),(31,'ES-A-A','Ordinateur fixe, Audio home systems (HIFI), elements hifi separes',0.84000000,'Eco-systemes',1,1),(32,'ES-A-B','Ordinateur portable, CD-RCR, VCR, lecteurs et enregistreurs DVD, instruments de musique et caisses de resonance, haut parleurs...',0.25000000,'Eco-systemes',1,1),(33,'ES-A-C','Imprimante, photocopieur, telecopieur',0.42000000,'Eco-systemes',1,1),(34,'ES-A-D','Accessoires, clavier, souris, PDA, imprimante photo, appareil photo, gps, telephone, repondeur, telephone sans fil, modem, telecommande, casque, camescope, baladeur mp3, radio portable, radio K7 et CD portable, radio reveil',0.08400000,'Eco-systemes',1,1),(35,'ES-A-E','GSM',0.00840000,'Eco-systemes',1,1),(36,'ES-A-F','Jouets et equipements de loisirs et de sports < 0,5 kg',0.04200000,'Eco-systemes',1,1),(37,'ES-A-G','Jouets et equipements de loisirs et de sports > 0,5 kg',0.17000000,'Eco-systemes',1,1),(38,'ES-A-H','Jouets et equipements de loisirs et de sports > 10 kg',1.25000000,'Eco-systemes',1,1);
/*!40000 ALTER TABLE `llx_c_ecotaxe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_effectif`
--

DROP TABLE IF EXISTS `llx_c_effectif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_effectif` (
  `id` int(11) NOT NULL,
  `code` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_effectif`
--

LOCK TABLES `llx_c_effectif` WRITE;
/*!40000 ALTER TABLE `llx_c_effectif` DISABLE KEYS */;
INSERT INTO `llx_c_effectif` VALUES (0,'EF0','-',1,NULL),(1,'EF1-5','1 - 5',1,NULL),(2,'EF6-10','6 - 10',1,NULL),(3,'EF11-50','11 - 50',1,NULL),(4,'EF51-100','51 - 100',1,NULL),(5,'EF100-500','100 - 500',1,NULL),(6,'EF500-','> 500',1,NULL);
/*!40000 ALTER TABLE `llx_c_effectif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_field_list`
--

DROP TABLE IF EXISTS `llx_c_field_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_field_list` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `element` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `alias` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `align` varchar(6) COLLATE utf8_unicode_ci DEFAULT 'left',
  `sort` tinyint(4) NOT NULL DEFAULT '1',
  `search` tinyint(4) NOT NULL DEFAULT '0',
  `enabled` varchar(255) COLLATE utf8_unicode_ci DEFAULT '1',
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_field_list`
--

LOCK TABLES `llx_c_field_list` WRITE;
/*!40000 ALTER TABLE `llx_c_field_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_c_field_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_forme_juridique`
--

DROP TABLE IF EXISTS `llx_c_forme_juridique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_forme_juridique` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` int(11) NOT NULL,
  `fk_pays` int(11) NOT NULL,
  `libelle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isvatexempted` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_forme_juridique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=307 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_forme_juridique`
--

LOCK TABLES `llx_c_forme_juridique` WRITE;
/*!40000 ALTER TABLE `llx_c_forme_juridique` DISABLE KEYS */;
INSERT INTO `llx_c_forme_juridique` VALUES (154,0,0,'-',0,1,NULL),(155,2301,23,'Monotributista',0,1,NULL),(156,2302,23,'Sociedad Civil',0,1,NULL),(157,2303,23,'Sociedades Comerciales',0,1,NULL),(158,2304,23,'Sociedades de Hecho',0,1,NULL),(159,2305,23,'Sociedades Irregulares',0,1,NULL),(160,2306,23,'Sociedad Colectiva',0,1,NULL),(161,2307,23,'Sociedad en Comandita Simple',0,1,NULL),(162,2308,23,'Sociedad de Capital e Industria',0,1,NULL),(163,2309,23,'Sociedad Accidental o en participación',0,1,NULL),(164,2310,23,'Sociedad de Responsabilidad Limitada',0,1,NULL),(165,2311,23,'Sociedad Anónima',0,1,NULL),(166,2312,23,'Sociedad Anónima con Participación Estatal Mayoritaria',0,1,NULL),(167,2313,23,'Sociedad en Comandita por Acciones (arts. 315 a 324, LSC)',0,1,NULL),(168,11,1,'Artisan Commerçant (EI)',0,1,NULL),(169,12,1,'Commerçant (EI)',0,1,NULL),(170,13,1,'Artisan (EI)',0,1,NULL),(171,14,1,'Officier public ou ministériel',0,1,NULL),(172,15,1,'Profession libérale (EI)',0,1,NULL),(173,16,1,'Exploitant agricole',0,1,NULL),(174,17,1,'Agent commercial',0,1,NULL),(175,18,1,'Associé Gérant de société',0,1,NULL),(176,19,1,'Personne physique',0,1,NULL),(177,21,1,'Indivision',0,1,NULL),(178,22,1,'Société créée de fait',0,1,NULL),(179,23,1,'Société en participation',0,1,NULL),(180,27,1,'Paroisse hors zone concordataire',0,1,NULL),(181,29,1,'Groupement de droit privé non doté de la personnalité morale',0,1,NULL),(182,31,1,'Personne morale de droit étranger, immatriculée au RCS',0,1,NULL),(183,32,1,'Personne morale de droit étranger, non immatriculée au RCS',0,1,NULL),(184,35,1,'Régime auto-entrepreneur',0,1,NULL),(185,41,1,'Établissement public ou régie à caractère industriel ou commercial',0,1,NULL),(186,51,1,'Société coopérative commerciale particulière',0,1,NULL),(187,52,1,'Société en nom collectif',0,1,NULL),(188,53,1,'Société en commandite',0,1,NULL),(189,54,1,'Société à responsabilité limitée (SARL)',0,1,NULL),(190,55,1,'Société anonyme à conseil d administration',0,1,NULL),(191,56,1,'Société anonyme à directoire',0,1,NULL),(192,57,1,'Société par actions simplifiée',0,1,NULL),(193,58,1,'Entreprise Unipersonnelle à Responsabilité Limitée (EURL)',0,1,NULL),(194,61,1,'Caisse d\'épargne et de prévoyance',0,1,NULL),(195,62,1,'Groupement d\'intérêt économique (GIE)',0,1,NULL),(196,63,1,'Société coopérative agricole',0,1,NULL),(197,64,1,'Société non commerciale d assurances',0,1,NULL),(198,65,1,'Société civile',0,1,NULL),(199,69,1,'Personnes de droit privé inscrites au RCS',0,1,NULL),(200,71,1,'Administration de l état',0,1,NULL),(201,72,1,'Collectivité territoriale',0,1,NULL),(202,73,1,'Établissement public administratif',0,1,NULL),(203,74,1,'Personne morale de droit public administratif',0,1,NULL),(204,81,1,'Organisme gérant régime de protection social à adhésion obligatoire',0,1,NULL),(205,82,1,'Organisme mutualiste',0,1,NULL),(206,83,1,'Comité d entreprise',0,1,NULL),(207,84,1,'Organisme professionnel',0,1,NULL),(208,85,1,'Organisme de retraite à adhésion non obligatoire',0,1,NULL),(209,91,1,'Syndicat de propriétaires',0,1,NULL),(210,92,1,'Association loi 1901 ou assimilé',0,1,NULL),(211,93,1,'Fondation',0,1,NULL),(212,99,1,'Personne morale de droit privé',0,1,NULL),(213,200,2,'Indépendant',0,1,NULL),(214,201,2,'SPRL - Société à responsabilité limitée',0,1,NULL),(215,202,2,'SA   - Société Anonyme',0,1,NULL),(216,203,2,'SCRL - Société coopérative à responsabilité limitée',0,1,NULL),(217,204,2,'ASBL - Association sans but Lucratif',0,1,NULL),(218,205,2,'SCRI - Société coopérative à responsabilité illimitée',0,1,NULL),(219,206,2,'SCS  - Société en commandite simple',0,1,NULL),(220,207,2,'SCA  - Société en commandite par action',0,1,NULL),(221,208,2,'SNC  - Société en nom collectif',0,1,NULL),(222,209,2,'GIE  - Groupement d intérêt économique',0,1,NULL),(223,210,2,'GEIE - Groupement européen d intérêt économique',0,1,NULL),(224,500,5,'GmbH - Gesellschaft mit beschränkter Haftung',0,1,NULL),(225,501,5,'AG - Aktiengesellschaft ',0,1,NULL),(226,502,5,'GmbH&Co. KG - Gesellschaft mit beschränkter Haftung & Compagnie Kommanditgesellschaft',0,1,NULL),(227,503,5,'Gewerbe - Personengesellschaft',0,1,NULL),(228,504,5,'UG - Unternehmergesellschaft -haftungsbeschränkt-',0,1,NULL),(229,505,5,'GbR - Gesellschaft des bürgerlichen Rechts',0,1,NULL),(230,506,5,'KG - Kommanditgesellschaft',0,1,NULL),(231,507,5,'Ltd. - Limited Company',0,1,NULL),(232,508,5,'OHG - Offene Handelsgesellschaft',0,1,NULL),(233,301,3,'Società semplice',0,1,NULL),(234,302,3,'Società in nome collettivo s.n.c.',0,1,NULL),(235,303,3,'Società in accomandita semplice s.a.s.',0,1,NULL),(236,304,3,'Società per azioni s.p.a.',0,1,NULL),(237,305,3,'Società a responsabilità limitata s.r.l.',0,1,NULL),(238,306,3,'Società in accomandita per azioni s.a.p.a.',0,1,NULL),(239,307,3,'Società cooperativa',0,1,NULL),(240,308,3,'Società consortile',0,1,NULL),(241,309,3,'Società europea',0,1,NULL),(242,310,3,'Società cooperativa europea',0,1,NULL),(243,311,3,'Società unipersonale',0,1,NULL),(244,312,3,'Società di professionisti',0,1,NULL),(245,313,3,'Società di fatto',0,1,NULL),(246,314,3,'Società occulta',0,1,NULL),(247,315,3,'Società apparente',0,1,NULL),(248,316,3,'Impresa individuale ',0,1,NULL),(249,317,3,'Impresa coniugale',0,1,NULL),(250,318,3,'Impresa familiare',0,1,NULL),(251,600,6,'Raison Individuelle',0,1,NULL),(252,601,6,'Société Simple',0,1,NULL),(253,602,6,'Société en nom collectif',0,1,NULL),(254,603,6,'Société en commandite',0,1,NULL),(255,604,6,'Société anonyme (SA)',0,1,NULL),(256,605,6,'Société en commandite par actions',0,1,NULL),(257,606,6,'Société à responsabilité limitée (SARL)',0,1,NULL),(258,607,6,'Société coopérative',0,1,NULL),(259,608,6,'Association',0,1,NULL),(260,609,6,'Fondation',0,1,NULL),(261,700,7,'Sole Trader',0,1,NULL),(262,701,7,'Partnership',0,1,NULL),(263,702,7,'Private Limited Company by shares (LTD)',0,1,NULL),(264,703,7,'Public Limited Company',0,1,NULL),(265,704,7,'Workers Cooperative',0,1,NULL),(266,705,7,'Limited Liability Partnership',0,1,NULL),(267,706,7,'Franchise',0,1,NULL),(268,1000,10,'Société à responsabilité limitée (SARL)',0,1,NULL),(269,1001,10,'Société en Nom Collectif (SNC)',0,1,NULL),(270,1002,10,'Société en Commandite Simple (SCS)',0,1,NULL),(271,1003,10,'société en participation',0,1,NULL),(272,1004,10,'Société Anonyme (SA)',0,1,NULL),(273,1005,10,'Société Unipersonnelle à Responsabilité Limitée (SUARL)',0,1,NULL),(274,1006,10,'Groupement d\'intérêt économique (GEI)',0,1,NULL),(275,1007,10,'Groupe de sociétés',0,1,NULL),(276,401,4,'Empresario Individual',0,1,NULL),(277,402,4,'Comunidad de Bienes',0,1,NULL),(278,403,4,'Sociedad Civil',0,1,NULL),(279,404,4,'Sociedad Colectiva',0,1,NULL),(280,405,4,'Sociedad Limitada',0,1,NULL),(281,406,4,'Sociedad Anónima',0,1,NULL),(282,407,4,'Sociedad Comandataria por Acciones',0,1,NULL),(283,408,4,'Sociedad Comandataria Simple',0,1,NULL),(284,409,4,'Sociedad Laboral',0,1,NULL),(285,410,4,'Sociedad Cooperativa',0,1,NULL),(286,411,4,'Sociedad de Garantía Recíproca',0,1,NULL),(287,412,4,'Entidad de Capital-Riesgo',0,1,NULL),(288,413,4,'Agrupación de Interés Económico',0,1,NULL),(289,414,4,'Sociedad de Inversión Mobiliaria',0,1,NULL),(290,415,4,'Agrupación sin Ánimo de Lucro',0,1,NULL),(291,15201,152,'Mauritius Private Company Limited By Shares',0,1,NULL),(292,15202,152,'Mauritius Company Limited By Guarantee',0,1,NULL),(293,15203,152,'Mauritius Public Company Limited By Shares',0,1,NULL),(294,15204,152,'Mauritius Foreign Company',0,1,NULL),(295,15205,152,'Mauritius GBC1 (Offshore Company)',0,1,NULL),(296,15206,152,'Mauritius GBC2 (International Company)',0,1,NULL),(297,15207,152,'Mauritius General Partnership',0,1,NULL),(298,15208,152,'Mauritius Limited Partnership',0,1,NULL),(299,15209,152,'Mauritius Sole Proprietorship',0,1,NULL),(300,15210,152,'Mauritius Trusts',0,1,NULL),(301,15401,154,'Sociedad en nombre colectivo',0,1,NULL),(302,15402,154,'Sociedad en comandita simple',0,1,NULL),(303,15403,154,'Sociedad de responsabilidad limitada',0,1,NULL),(304,15404,154,'Sociedad anónima',0,1,NULL),(305,15405,154,'Sociedad en comandita por acciones',0,1,NULL),(306,15406,154,'Sociedad cooperativa',0,1,NULL);
/*!40000 ALTER TABLE `llx_c_forme_juridique` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_input_method`
--

DROP TABLE IF EXISTS `llx_c_input_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_input_method` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `libelle` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_input_method` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_input_method`
--

LOCK TABLES `llx_c_input_method` WRITE;
/*!40000 ALTER TABLE `llx_c_input_method` DISABLE KEYS */;
INSERT INTO `llx_c_input_method` VALUES (1,'OrderByMail','Courrier',1,NULL),(2,'OrderByFax','Fax',1,NULL),(3,'OrderByEMail','EMail',1,NULL),(4,'OrderByPhone','Téléphone',1,NULL),(5,'OrderByWWW','En ligne',1,NULL);
/*!40000 ALTER TABLE `llx_c_input_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_input_reason`
--

DROP TABLE IF EXISTS `llx_c_input_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_input_reason` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_c_input_reason` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_input_reason`
--

LOCK TABLES `llx_c_input_reason` WRITE;
/*!40000 ALTER TABLE `llx_c_input_reason` DISABLE KEYS */;
INSERT INTO `llx_c_input_reason` VALUES (1,'SRC_INTE','Web site',1,NULL),(2,'SRC_CAMP_MAIL','Mailing campaign',1,NULL),(3,'SRC_CAMP_PHO','Phone campaign',1,NULL),(4,'SRC_CAMP_FAX','Fax campaign',1,NULL),(5,'SRC_COMM','Commercial contact',1,NULL),(6,'SRC_SHOP','Shop contact',1,NULL),(7,'SRC_CAMP_EMAIL','EMailing campaign',1,NULL);
/*!40000 ALTER TABLE `llx_c_input_reason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_paiement`
--

DROP TABLE IF EXISTS `llx_c_paiement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_paiement` (
  `id` int(11) NOT NULL,
  `code` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_paiement`
--

LOCK TABLES `llx_c_paiement` WRITE;
/*!40000 ALTER TABLE `llx_c_paiement` DISABLE KEYS */;
INSERT INTO `llx_c_paiement` VALUES (0,'','-',3,1,NULL),(1,'TIP','TIP',2,1,NULL),(2,'VIR','Virement',2,1,NULL),(3,'PRE','Prélèvement',2,1,NULL),(4,'LIQ','Espèces',2,1,NULL),(6,'CB','Carte Bancaire',2,1,NULL),(7,'CHQ','Chèque',2,1,NULL),(50,'VAD','Paiement en ligne',2,0,NULL),(51,'TRA','Traite',2,0,NULL),(52,'LCR','LCR',2,0,NULL),(53,'FAC','Factor',2,0,NULL),(54,'PRO','Proforma',2,0,NULL);
/*!40000 ALTER TABLE `llx_c_paiement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_paper_format`
--

DROP TABLE IF EXISTS `llx_c_paper_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_paper_format` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `width` float(6,2) DEFAULT '0.00',
  `height` float(6,2) DEFAULT '0.00',
  `unit` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_paper_format`
--

LOCK TABLES `llx_c_paper_format` WRITE;
/*!40000 ALTER TABLE `llx_c_paper_format` DISABLE KEYS */;
INSERT INTO `llx_c_paper_format` VALUES (1,'EU4A0','Format 4A0',1682.00,2378.00,'mm',1,NULL),(2,'EU2A0','Format 2A0',1189.00,1682.00,'mm',1,NULL),(3,'EUA0','Format A0',840.00,1189.00,'mm',1,NULL),(4,'EUA1','Format A1',594.00,840.00,'mm',1,NULL),(5,'EUA2','Format A2',420.00,594.00,'mm',1,NULL),(6,'EUA3','Format A3',297.00,420.00,'mm',1,NULL),(7,'EUA4','Format A4',210.00,297.00,'mm',1,NULL),(8,'EUA5','Format A5',148.00,210.00,'mm',1,NULL),(9,'EUA6','Format A6',105.00,148.00,'mm',1,NULL),(100,'USLetter','Format Letter (A)',216.00,279.00,'mm',1,NULL),(105,'USLegal','Format Legal',216.00,356.00,'mm',1,NULL),(110,'USExecutive','Format Executive',190.00,254.00,'mm',1,NULL),(115,'USLedger','Format Ledger/Tabloid (B)',279.00,432.00,'mm',1,NULL),(200,'CAP1','Format Canadian P1',560.00,860.00,'mm',1,NULL),(205,'CAP2','Format Canadian P2',430.00,560.00,'mm',1,NULL),(210,'CAP3','Format Canadian P3',280.00,430.00,'mm',1,NULL),(215,'CAP4','Format Canadian P4',215.00,280.00,'mm',1,NULL),(220,'CAP5','Format Canadian P5',140.00,215.00,'mm',1,NULL),(225,'CAP6','Format Canadian P6',107.00,140.00,'mm',1,NULL);
/*!40000 ALTER TABLE `llx_c_paper_format` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_payment_term`
--

DROP TABLE IF EXISTS `llx_c_payment_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_payment_term` (
  `rowid` int(11) NOT NULL,
  `code` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sortorder` smallint(6) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `libelle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `libelle_facture` text COLLATE utf8_unicode_ci,
  `fdm` tinyint(4) DEFAULT NULL,
  `nbjour` smallint(6) DEFAULT NULL,
  `decalage` smallint(6) DEFAULT NULL,
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_payment_term`
--

LOCK TABLES `llx_c_payment_term` WRITE;
/*!40000 ALTER TABLE `llx_c_payment_term` DISABLE KEYS */;
INSERT INTO `llx_c_payment_term` VALUES (1,'RECEP',1,1,'A réception','Réception de facture',0,0,NULL,NULL),(2,'30D',2,1,'30 jours','Réglement à 30 jours',0,30,NULL,NULL),(3,'30DENDMONTH',3,1,'30 jours fin de mois','Réglement à 30 jours fin de mois',1,30,NULL,NULL),(4,'60D',4,1,'60 jours','Réglement à 60 jours',0,60,NULL,NULL),(5,'60DENDMONTH',5,1,'60 jours fin de mois','Réglement à 60 jours fin de mois',1,60,NULL,NULL);
/*!40000 ALTER TABLE `llx_c_payment_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_pays`
--

DROP TABLE IF EXISTS `llx_c_pays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_pays` (
  `rowid` int(11) NOT NULL,
  `code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `code_iso` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `libelle` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_c_pays_code` (`code`),
  UNIQUE KEY `idx_c_pays_libelle` (`libelle`),
  UNIQUE KEY `idx_c_pays_code_iso` (`code_iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_pays`
--

LOCK TABLES `llx_c_pays` WRITE;
/*!40000 ALTER TABLE `llx_c_pays` DISABLE KEYS */;
INSERT INTO `llx_c_pays` VALUES (0,'',NULL,'-',1),(1,'FR',NULL,'France',1),(2,'BE',NULL,'Belgium',1),(3,'IT',NULL,'Italy',1),(4,'ES',NULL,'Spain',1),(5,'DE',NULL,'Germany',1),(6,'CH',NULL,'Suisse',1),(7,'GB',NULL,'United Kingdom',1),(8,'IE',NULL,'Irland',1),(9,'CN',NULL,'China',1),(10,'TN',NULL,'Tunisie',1),(11,'US',NULL,'United States',1),(12,'MA',NULL,'Maroc',1),(13,'DZ',NULL,'Algérie',1),(14,'CA',NULL,'Canada',1),(15,'TG',NULL,'Togo',1),(16,'GA',NULL,'Gabon',1),(17,'NL',NULL,'Nerderland',1),(18,'HU',NULL,'Hongrie',1),(19,'RU',NULL,'Russia',1),(20,'SE',NULL,'Sweden',1),(21,'CI',NULL,'Côte d\'Ivoire',1),(22,'SN',NULL,'Sénégal',1),(23,'AR',NULL,'Argentine',1),(24,'CM',NULL,'Cameroun',1),(25,'PT',NULL,'Portugal',1),(26,'SA',NULL,'Arabie Saoudite',1),(27,'MC',NULL,'Monaco',1),(28,'AU',NULL,'Australia',1),(29,'SG',NULL,'Singapour',1),(30,'AF',NULL,'Afghanistan',1),(31,'AX',NULL,'Iles Aland',1),(32,'AL',NULL,'Albanie',1),(33,'AS',NULL,'Samoa américaines',1),(34,'AD',NULL,'Andorre',1),(35,'AO',NULL,'Angola',1),(36,'AI',NULL,'Anguilla',1),(37,'AQ',NULL,'Antarctique',1),(38,'AG',NULL,'Antigua-et-Barbuda',1),(39,'AM',NULL,'Arménie',1),(40,'AW',NULL,'Aruba',1),(41,'AT',NULL,'Autriche',1),(42,'AZ',NULL,'Azerbaïdjan',1),(43,'BS',NULL,'Bahamas',1),(44,'BH',NULL,'Bahreïn',1),(45,'BD',NULL,'Bangladesh',1),(46,'BB',NULL,'Barbade',1),(47,'BY',NULL,'Biélorussie',1),(48,'BZ',NULL,'Belize',1),(49,'BJ',NULL,'Bénin',1),(50,'BM',NULL,'Bermudes',1),(51,'BT',NULL,'Bhoutan',1),(52,'BO',NULL,'Bolivie',1),(53,'BA',NULL,'Bosnie-Herzégovine',1),(54,'BW',NULL,'Botswana',1),(55,'BV',NULL,'Ile Bouvet',1),(56,'BR',NULL,'Brazil',1),(57,'IO',NULL,'Territoire britannique de l\'Océan Indien',1),(58,'BN',NULL,'Brunei',1),(59,'BG',NULL,'Bulgarie',1),(60,'BF',NULL,'Burkina Faso',1),(61,'BI',NULL,'Burundi',1),(62,'KH',NULL,'Cambodge',1),(63,'CV',NULL,'Cap-Vert',1),(64,'KY',NULL,'Iles Cayman',1),(65,'CF',NULL,'République centrafricaine',1),(66,'TD',NULL,'Tchad',1),(67,'CL',NULL,'Chili',1),(68,'CX',NULL,'Ile Christmas',1),(69,'CC',NULL,'Iles des Cocos (Keeling)',1),(70,'CO',NULL,'Colombie',1),(71,'KM',NULL,'Comores',1),(72,'CG',NULL,'Congo',1),(73,'CD',NULL,'République démocratique du Congo',1),(74,'CK',NULL,'Iles Cook',1),(75,'CR',NULL,'Costa Rica',1),(76,'HR',NULL,'Croatie',1),(77,'CU',NULL,'Cuba',1),(78,'CY',NULL,'Chypre',1),(79,'CZ',NULL,'République Tchèque',1),(80,'DK',NULL,'Danemark',1),(81,'DJ',NULL,'Djibouti',1),(82,'DM',NULL,'Dominique',1),(83,'DO',NULL,'République Dominicaine',1),(84,'EC',NULL,'Equateur',1),(85,'EG',NULL,'Egypte',1),(86,'SV',NULL,'Salvador',1),(87,'GQ',NULL,'Guinée Equatoriale',1),(88,'ER',NULL,'Erythrée',1),(89,'EE',NULL,'Estonie',1),(90,'ET',NULL,'Ethiopie',1),(91,'FK',NULL,'Iles Falkland',1),(92,'FO',NULL,'Iles Féroé',1),(93,'FJ',NULL,'Iles Fidji',1),(94,'FI',NULL,'Finlande',1),(95,'GF',NULL,'Guyane française',1),(96,'PF',NULL,'Polynésie française',1),(97,'TF',NULL,'Terres australes françaises',1),(98,'GM',NULL,'Gambie',1),(99,'GE',NULL,'Géorgie',1),(100,'GH',NULL,'Ghana',1),(101,'GI',NULL,'Gibraltar',1),(102,'GR',NULL,'Grèce',1),(103,'GL',NULL,'Groenland',1),(104,'GD',NULL,'Grenade',1),(106,'GU',NULL,'Guam',1),(107,'GT',NULL,'Guatemala',1),(108,'GN',NULL,'Guinée',1),(109,'GW',NULL,'Guinée-Bissao',1),(111,'HT',NULL,'Haiti',1),(112,'HM',NULL,'Iles Heard et McDonald',1),(113,'VA',NULL,'Saint-Siège (Vatican)',1),(114,'HN',NULL,'Honduras',1),(115,'HK',NULL,'Hong Kong',1),(116,'IS',NULL,'Islande',1),(117,'IN',NULL,'India',1),(118,'ID',NULL,'Indonésie',1),(119,'IR',NULL,'Iran',1),(120,'IQ',NULL,'Iraq',1),(121,'IL',NULL,'Israel',1),(122,'JM',NULL,'Jamaïque',1),(123,'JP',NULL,'Japon',1),(124,'JO',NULL,'Jordanie',1),(125,'KZ',NULL,'Kazakhstan',1),(126,'KE',NULL,'Kenya',1),(127,'KI',NULL,'Kiribati',1),(128,'KP',NULL,'Corée du Nord',1),(129,'KR',NULL,'Corée du Sud',1),(130,'KW',NULL,'Koweït',1),(131,'KG',NULL,'Kirghizistan',1),(132,'LA',NULL,'Laos',1),(133,'LV',NULL,'Lettonie',1),(134,'LB',NULL,'Liban',1),(135,'LS',NULL,'Lesotho',1),(136,'LR',NULL,'Liberia',1),(137,'LY',NULL,'Libye',1),(138,'LI',NULL,'Liechtenstein',1),(139,'LT',NULL,'Lituanie',1),(140,'LU',NULL,'Luxembourg',1),(141,'MO',NULL,'Macao',1),(142,'MK',NULL,'ex-République yougoslave de Macédoine',1),(143,'MG',NULL,'Madagascar',1),(144,'MW',NULL,'Malawi',1),(145,'MY',NULL,'Malaisie',1),(146,'MV',NULL,'Maldives',1),(147,'ML',NULL,'Mali',1),(148,'MT',NULL,'Malte',1),(149,'MH',NULL,'Iles Marshall',1),(151,'MR',NULL,'Mauritanie',1),(152,'MU',NULL,'Maurice',1),(153,'YT',NULL,'Mayotte',1),(154,'MX',NULL,'Mexique',1),(155,'FM',NULL,'Micronésie',1),(156,'MD',NULL,'Moldavie',1),(157,'MN',NULL,'Mongolie',1),(158,'MS',NULL,'Monserrat',1),(159,'MZ',NULL,'Mozambique',1),(160,'MM',NULL,'Birmanie (Myanmar)',1),(161,'NA',NULL,'Namibie',1),(162,'NR',NULL,'Nauru',1),(163,'NP',NULL,'Népal',1),(164,'AN',NULL,'Antilles néerlandaises',1),(165,'NC',NULL,'Nouvelle-Calédonie',1),(166,'NZ',NULL,'Nouvelle-Zélande',1),(167,'NI',NULL,'Nicaragua',1),(168,'NE',NULL,'Niger',1),(169,'NG',NULL,'Nigeria',1),(170,'NU',NULL,'Nioué',1),(171,'NF',NULL,'Ile Norfolk',1),(172,'MP',NULL,'Mariannes du Nord',1),(173,'NO',NULL,'Norvège',1),(174,'OM',NULL,'Oman',1),(175,'PK',NULL,'Pakistan',1),(176,'PW',NULL,'Palaos',1),(177,'PS',NULL,'Territoire Palestinien Occupé',1),(178,'PA',NULL,'Panama',1),(179,'PG',NULL,'Papouasie-Nouvelle-Guinée',1),(180,'PY',NULL,'Paraguay',1),(181,'PE',NULL,'Pérou',1),(182,'PH',NULL,'Philippines',1),(183,'PN',NULL,'Iles Pitcairn',1),(184,'PL',NULL,'Pologne',1),(185,'PR',NULL,'Porto Rico',1),(186,'QA',NULL,'Qatar',1),(188,'RO',NULL,'Roumanie',1),(189,'RW',NULL,'Rwanda',1),(190,'SH',NULL,'Sainte-Hélène',1),(191,'KN',NULL,'Saint-Christophe-et-Niévès',1),(192,'LC',NULL,'Sainte-Lucie',1),(193,'PM',NULL,'Saint-Pierre-et-Miquelon',1),(194,'VC',NULL,'Saint-Vincent-et-les-Grenadines',1),(195,'WS',NULL,'Samoa',1),(196,'SM',NULL,'Saint-Marin',1),(197,'ST',NULL,'Sao Tomé-et-Principe',1),(198,'RS',NULL,'Serbie',1),(199,'SC',NULL,'Seychelles',1),(200,'SL',NULL,'Sierra Leone',1),(201,'SK',NULL,'Slovaquie',1),(202,'SI',NULL,'Slovénie',1),(203,'SB',NULL,'Iles Salomon',1),(204,'SO',NULL,'Somalie',1),(205,'ZA',NULL,'Afrique du Sud',1),(206,'GS',NULL,'Iles Géorgie du Sud et Sandwich du Sud',1),(207,'LK',NULL,'Sri Lanka',1),(208,'SD',NULL,'Soudan',1),(209,'SR',NULL,'Suriname',1),(210,'SJ',NULL,'Iles Svalbard et Jan Mayen',1),(211,'SZ',NULL,'Swaziland',1),(212,'SY',NULL,'Syrie',1),(213,'TW',NULL,'Taïwan',1),(214,'TJ',NULL,'Tadjikistan',1),(215,'TZ',NULL,'Tanzanie',1),(216,'TH',NULL,'Thaïlande',1),(217,'TL',NULL,'Timor Oriental',1),(218,'TK',NULL,'Tokélaou',1),(219,'TO',NULL,'Tonga',1),(220,'TT',NULL,'Trinité-et-Tobago',1),(221,'TR',NULL,'Turquie',1),(222,'TM',NULL,'Turkménistan',1),(223,'TC',NULL,'Iles Turks-et-Caicos',1),(224,'TV',NULL,'Tuvalu',1),(225,'UG',NULL,'Ouganda',1),(226,'UA',NULL,'Ukraine',1),(227,'AE',NULL,'Émirats arabes unis',1),(228,'UM',NULL,'Iles mineures éloignées des États-Unis',1),(229,'UY',NULL,'Uruguay',1),(230,'UZ',NULL,'Ouzbékistan',1),(231,'VU',NULL,'Vanuatu',1),(232,'VE',NULL,'Vénézuela',1),(233,'VN',NULL,'Viêt Nam',1),(234,'VG',NULL,'Iles Vierges britanniques',1),(235,'VI',NULL,'Iles Vierges américaines',1),(236,'WF',NULL,'Wallis-et-Futuna',1),(237,'EH',NULL,'Sahara occidental',1),(238,'YE',NULL,'Yémen',1),(239,'ZM',NULL,'Zambie',1),(240,'ZW',NULL,'Zimbabwe',1),(241,'GG',NULL,'Guernesey',1),(242,'IM',NULL,'Ile de Man',1),(243,'JE',NULL,'Jersey',1),(244,'ME',NULL,'Monténégro',1),(245,'BL',NULL,'Saint-Barthélemy',1),(246,'MF',NULL,'Saint-Martin',1);
/*!40000 ALTER TABLE `llx_c_pays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_propalst`
--

DROP TABLE IF EXISTS `llx_c_propalst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_propalst` (
  `id` smallint(6) NOT NULL,
  `code` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_propalst`
--

LOCK TABLES `llx_c_propalst` WRITE;
/*!40000 ALTER TABLE `llx_c_propalst` DISABLE KEYS */;
INSERT INTO `llx_c_propalst` VALUES (0,'PR_DRAFT','Brouillon',1),(1,'PR_OPEN','Ouverte',1),(2,'PR_SIGNED','Signée',1),(3,'PR_NOTSIGNED','Non Signée',1),(4,'PR_FAC','Facturée',1);
/*!40000 ALTER TABLE `llx_c_propalst` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_prospectlevel`
--

DROP TABLE IF EXISTS `llx_c_prospectlevel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_prospectlevel` (
  `code` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sortorder` smallint(6) DEFAULT NULL,
  `active` smallint(6) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_prospectlevel`
--

LOCK TABLES `llx_c_prospectlevel` WRITE;
/*!40000 ALTER TABLE `llx_c_prospectlevel` DISABLE KEYS */;
INSERT INTO `llx_c_prospectlevel` VALUES ('PL_HIGH','High',4,1,NULL),('PL_LOW','Low',2,1,NULL),('PL_MEDIUM','Medium',3,1,NULL),('PL_NONE','None',1,1,NULL);
/*!40000 ALTER TABLE `llx_c_prospectlevel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_regions`
--

DROP TABLE IF EXISTS `llx_c_regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_regions` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code_region` int(11) NOT NULL,
  `fk_pays` int(11) NOT NULL,
  `cheflieu` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tncc` int(11) DEFAULT NULL,
  `nom` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `code_region` (`code_region`),
  KEY `idx_c_regions_fk_pays` (`fk_pays`),
  CONSTRAINT `fk_c_regions_fk_pays` FOREIGN KEY (`fk_pays`) REFERENCES `llx_c_pays` (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=23210 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_regions`
--

LOCK TABLES `llx_c_regions` WRITE;
/*!40000 ALTER TABLE `llx_c_regions` DISABLE KEYS */;
INSERT INTO `llx_c_regions` VALUES (1,0,0,'0',0,'-',1),(101,1,1,'97105',3,'Guadeloupe',1),(102,2,1,'97209',3,'Martinique',1),(103,3,1,'97302',3,'Guyane',1),(104,4,1,'97411',3,'Réunion',1),(105,11,1,'75056',1,'Île-de-France',1),(106,21,1,'51108',0,'Champagne-Ardenne',1),(107,22,1,'80021',0,'Picardie',1),(108,23,1,'76540',0,'Haute-Normandie',1),(109,24,1,'45234',2,'Centre',1),(110,25,1,'14118',0,'Basse-Normandie',1),(111,26,1,'21231',0,'Bourgogne',1),(112,31,1,'59350',2,'Nord-Pas-de-Calais',1),(113,41,1,'57463',0,'Lorraine',1),(114,42,1,'67482',1,'Alsace',1),(115,43,1,'25056',0,'Franche-Comté',1),(116,52,1,'44109',4,'Pays de la Loire',1),(117,53,1,'35238',0,'Bretagne',1),(118,54,1,'86194',2,'Poitou-Charentes',1),(119,72,1,'33063',1,'Aquitaine',1),(120,73,1,'31555',0,'Midi-Pyrénées',1),(121,74,1,'87085',2,'Limousin',1),(122,82,1,'69123',2,'Rhône-Alpes',1),(123,83,1,'63113',1,'Auvergne',1),(124,91,1,'34172',2,'Languedoc-Roussillon',1),(125,93,1,'13055',0,'Provence-Alpes-Côte d\'Azur',1),(126,94,1,'2A004',0,'Corse',1),(201,201,2,'',1,'Flandre',1),(202,202,2,'',2,'Wallonie',1),(203,203,2,'',3,'Bruxelles-Capitale',1),(301,301,3,NULL,1,'Abruzzo',1),(302,302,3,NULL,1,'Basilicata',1),(303,303,3,NULL,1,'Calabria',1),(304,304,3,NULL,1,'Campania',1),(305,305,3,NULL,1,'Emilia-Romagna',1),(306,306,3,NULL,1,'Friuli-Venezia Giulia',1),(307,307,3,NULL,1,'Lazio',1),(308,308,3,NULL,1,'Liguria',1),(309,309,3,NULL,1,'Lombardia',1),(310,310,3,NULL,1,'Marche',1),(311,311,3,NULL,1,'Molise',1),(312,312,3,NULL,1,'Piemonte',1),(313,313,3,NULL,1,'Puglia',1),(314,314,3,NULL,1,'Sardegna',1),(315,315,3,NULL,1,'Sicilia',1),(316,316,3,NULL,1,'Toscana',1),(317,317,3,NULL,1,'Trentino-Alto Adige',1),(318,318,3,NULL,1,'Umbria',1),(319,319,3,NULL,1,'Valle d Aosta',1),(320,320,3,NULL,1,'Veneto',1),(401,401,4,'',0,'Andalucia',1),(402,402,4,'',0,'Aragón',1),(403,403,4,'',0,'Castilla y León',1),(404,404,4,'',0,'Castilla la Mancha',1),(405,405,4,'',0,'Canarias',1),(406,406,4,'',0,'Cataluña',1),(407,407,4,'',0,'Comunidad de Ceuta',1),(408,408,4,'',0,'Comunidad Foral de Navarra',1),(409,409,4,'',0,'Comunidad de Melilla',1),(410,410,4,'',0,'Cantabria',1),(411,411,4,'',0,'Comunidad Valenciana',1),(412,412,4,'',0,'Extemadura',1),(413,413,4,'',0,'Galicia',1),(414,414,4,'',0,'Islas Baleares',1),(415,415,4,'',0,'La Rioja',1),(416,416,4,'',0,'Comunidad de Madrid',1),(417,417,4,'',0,'Región de Murcia',1),(418,418,4,'',0,'Principado de Asturias',1),(419,419,4,'',0,'Pais Vasco',1),(420,420,4,'',0,'Otros',1),(501,501,5,'',0,'Deutschland',1),(601,601,6,'',1,'Cantons',1),(1001,1001,10,'',0,'Ariana',1),(1002,1002,10,'',0,'Béja',1),(1003,1003,10,'',0,'Ben Arous',1),(1004,1004,10,'',0,'Bizerte',1),(1005,1005,10,'',0,'Gabès',1),(1006,1006,10,'',0,'Gafsa',1),(1007,1007,10,'',0,'Jendouba',1),(1008,1008,10,'',0,'Kairouan',1),(1009,1009,10,'',0,'Kasserine',1),(1010,1010,10,'',0,'Kébili',1),(1011,1011,10,'',0,'La Manouba',1),(1012,1012,10,'',0,'Le Kef',1),(1013,1013,10,'',0,'Mahdia',1),(1014,1014,10,'',0,'Médenine',1),(1015,1015,10,'',0,'Monastir',1),(1016,1016,10,'',0,'Nabeul',1),(1017,1017,10,'',0,'Sfax',1),(1018,1018,10,'',0,'Sidi Bouzid',1),(1019,1019,10,'',0,'Siliana',1),(1020,1020,10,'',0,'Sousse',1),(1021,1021,10,'',0,'Tataouine',1),(1022,1022,10,'',0,'Tozeur',1),(1023,1023,10,'',0,'Tunis',1),(1024,1024,10,'',0,'Zaghouan',1),(1101,1101,11,'',0,'United-States',1),(2301,2301,23,'',0,'Norte',1),(2302,2302,23,'',0,'Litoral',1),(2303,2303,23,'',0,'Cuyana',1),(2304,2304,23,'',0,'Central',1),(2305,2305,23,'',0,'Patagonia',1),(2801,2801,28,'',0,'Australia',1),(4601,4601,46,'',0,'Barbados',1),(5601,5601,56,'',0,'Brasil',1),(6701,6701,67,NULL,NULL,'Tarapacá',1),(6702,6702,67,NULL,NULL,'Antofagasta',1),(6703,6703,67,NULL,NULL,'Atacama',1),(6704,6704,67,NULL,NULL,'Coquimbo',1),(6705,6705,67,NULL,NULL,'Valparaíso',1),(6706,6706,67,NULL,NULL,'General Bernardo O Higgins',1),(6707,6707,67,NULL,NULL,'Maule',1),(6708,6708,67,NULL,NULL,'Biobío',1),(6709,6709,67,NULL,NULL,'Raucanía',1),(6710,6710,67,NULL,NULL,'Los Lagos',1),(6711,6711,67,NULL,NULL,'Aysén General Carlos Ibáñez del Campo',1),(6712,6712,67,NULL,NULL,'Magallanes y Antártica Chilena',1),(6713,6713,67,NULL,NULL,'Metropolitana de Santiago',1),(6714,6714,67,NULL,NULL,'Los Ríos',1),(6715,6715,67,NULL,NULL,'Arica y Parinacota',1),(7001,7001,70,'',0,'Colombie',1),(8601,8601,86,NULL,NULL,'Central',1),(8602,8602,86,NULL,NULL,'Oriental',1),(8603,8603,86,NULL,NULL,'Occidental',1),(11401,11401,114,'',0,'Honduras',1),(11701,11701,117,'',0,'India',1),(15201,15201,152,'',0,'Rivière Noire',1),(15202,15202,152,'',0,'Flacq',1),(15203,15203,152,'',0,'Grand Port',1),(15204,15204,152,'',0,'Moka',1),(15205,15205,152,'',0,'Pamplemousses',1),(15206,15206,152,'',0,'Plaines Wilhems',1),(15207,15207,152,'',0,'Port-Louis',1),(15208,15208,152,'',0,'Rivière du Rempart',1),(15209,15209,152,'',0,'Savanne',1),(15210,15210,152,'',0,'Rodrigues',1),(15211,15211,152,'',0,'Les îles Agaléga',1),(15212,15212,152,'',0,'Les écueils des Cargados Carajos',1),(15401,15401,154,'',0,'Mexique',1),(23201,23201,232,'',0,'Los Andes',1),(23202,23202,232,'',0,'Capital',1),(23203,23203,232,'',0,'Central',1),(23204,23204,232,'',0,'Cento Occidental',1),(23205,23205,232,'',0,'Guayana',1),(23206,23206,232,'',0,'Insular',1),(23207,23207,232,'',0,'Los Llanos',1),(23208,23208,232,'',0,'Nor-Oriental',1),(23209,23209,232,'',0,'Zuliana',1);
/*!40000 ALTER TABLE `llx_c_regions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_shipment_mode`
--

DROP TABLE IF EXISTS `llx_c_shipment_mode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_shipment_mode` (
  `rowid` int(11) NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `code` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `active` tinyint(4) DEFAULT '0',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_shipment_mode`
--

LOCK TABLES `llx_c_shipment_mode` WRITE;
/*!40000 ALTER TABLE `llx_c_shipment_mode` DISABLE KEYS */;
INSERT INTO `llx_c_shipment_mode` VALUES (1,'2012-10-11 14:07:48','CATCH','Catch','Catch by client',1,NULL),(2,'2012-10-11 14:07:48','TRANS','Transporter','Generic transporter',1,NULL),(3,'2012-10-11 14:07:48','COLSUI','Colissimo Suivi','Colissimo Suivi',0,NULL),(4,'2012-10-11 14:07:48','LETTREMAX','Lettre Max','Courrier Suivi et Lettre Max',0,NULL);
/*!40000 ALTER TABLE `llx_c_shipment_mode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_stcomm`
--

DROP TABLE IF EXISTS `llx_c_stcomm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_stcomm` (
  `id` int(11) NOT NULL,
  `code` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_stcomm`
--

LOCK TABLES `llx_c_stcomm` WRITE;
/*!40000 ALTER TABLE `llx_c_stcomm` DISABLE KEYS */;
INSERT INTO `llx_c_stcomm` VALUES (-1,'ST_NO','Ne pas contacter',1),(0,'ST_NEVER','Jamais contacté',1),(1,'ST_TODO','A contacter',1),(2,'ST_PEND','Contact en cours',1),(3,'ST_DONE','Contactée',1);
/*!40000 ALTER TABLE `llx_c_stcomm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_tva`
--

DROP TABLE IF EXISTS `llx_c_tva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_tva` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_pays` int(11) NOT NULL,
  `taux` double NOT NULL,
  `localtax1` double NOT NULL DEFAULT '0',
  `localtax2` double NOT NULL DEFAULT '0',
  `recuperableonly` int(11) NOT NULL DEFAULT '0',
  `note` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `accountancy_code` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=2462 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_tva`
--

LOCK TABLES `llx_c_tva` WRITE;
/*!40000 ALTER TABLE `llx_c_tva` DISABLE KEYS */;
INSERT INTO `llx_c_tva` VALUES (11,1,19.6,0,0,0,'VAT standard rate (France hors DOM-TOM)',1,NULL),(12,1,8.5,0,0,0,'VAT standard rate (DOM sauf Guyane et Saint-Martin)',0,NULL),(13,1,8.5,0,0,1,'VAT standard rate (DOM sauf Guyane et Saint-Martin), non perçu par le vendeur mais récupérable par acheteur',0,NULL),(14,1,5.5,0,0,0,'VAT reduced rate (France hors DOM-TOM)',1,NULL),(15,1,0,0,0,0,'VAT Rate 0 ou non applicable',1,NULL),(16,1,2.1,0,0,0,'VAT super-reduced rate',1,NULL),(17,1,7,0,0,0,'VAT reduced rate',1,NULL),(21,2,21,0,0,0,'VAT standard rate',1,NULL),(22,2,6,0,0,0,'VAT reduced rate',1,NULL),(23,2,0,0,0,0,'VAT Rate 0 ou non applicable',1,NULL),(24,2,12,0,0,0,'VAT reduced rate',1,NULL),(31,3,21,0,0,0,'VAT standard rate',1,NULL),(32,3,10,0,0,0,'VAT reduced rate',1,NULL),(33,3,4,0,0,0,'VAT super-reduced rate',1,NULL),(34,3,0,0,0,0,'VAT Rate 0',1,NULL),(41,4,21,5.2,0,0,'VAT standard rate',1,NULL),(42,4,10,1.4,0,0,'VAT reduced rate',1,NULL),(43,4,4,0.5,0,0,'VAT super-reduced rate',1,NULL),(44,4,0,0,0,0,'VAT Rate 0',1,NULL),(51,5,19,0,0,0,'allgemeine Ust.',1,NULL),(52,5,7,0,0,0,'ermäßigte USt.',1,NULL),(53,5,0,0,0,0,'keine USt.',1,NULL),(54,5,5.5,0,0,0,'USt. Forst',0,NULL),(55,5,10.7,0,0,0,'USt. Landwirtschaft',0,NULL),(61,6,7.6,0,0,0,'VAT standard rate',1,NULL),(62,6,3.6,0,0,0,'VAT reduced rate',1,NULL),(63,6,2.4,0,0,0,'VAT super-reduced rate',1,NULL),(64,6,0,0,0,0,'VAT Rate 0',1,NULL),(71,7,20,0,0,0,'VAT standard rate',1,NULL),(72,7,17.5,0,0,0,'VAT standard rate before 2011',1,NULL),(73,7,5,0,0,0,'VAT reduced rate',1,NULL),(74,7,0,0,0,0,'VAT Rate 0',1,NULL),(91,9,17,0,0,0,'VAT standard rate',1,NULL),(92,9,13,0,0,0,'VAT reduced rate 0',1,NULL),(93,9,3,0,0,0,'VAT super reduced rate 0',1,NULL),(94,9,0,0,0,0,'VAT Rate 0',1,NULL),(101,10,6,0,0,0,'VAT 6%',1,NULL),(102,10,12,0,0,0,'VAT 12%',1,NULL),(103,10,18,0,0,0,'VAT 18%',1,NULL),(104,10,7.5,0,0,0,'VAT 6% Majoré à 25% (7.5%)',1,NULL),(105,10,15,0,0,0,'VAT 12% Majoré à 25% (15%)',1,NULL),(106,10,22.5,0,0,0,'VAT 18% Majoré à 25% (22.5%)',1,NULL),(107,10,0,0,0,0,'VAT Rate 0',1,NULL),(111,11,0,0,0,0,'No Sales Tax',1,NULL),(112,11,4,0,0,0,'Sales Tax 4%',1,NULL),(113,11,6,0,0,0,'Sales Tax 6%',1,NULL),(121,12,20,0,0,0,'VAT standard rate',1,NULL),(122,12,14,0,0,0,'VAT reduced rate',1,NULL),(123,12,10,0,0,0,'VAT reduced rate',1,NULL),(124,12,7,0,0,0,'VAT super-reduced rate',1,NULL),(125,12,0,0,0,0,'VAT Rate 0',1,NULL),(141,14,7,0,0,0,'VAT standard rate',1,NULL),(142,14,0,0,0,0,'VAT Rate 0',1,NULL),(171,17,19,0,0,0,'Algemeen BTW tarief',1,NULL),(172,17,6,0,0,0,'Verlaagd BTW tarief',1,NULL),(173,17,0,0,0,0,'0 BTW tarief',1,NULL),(174,17,21,0,0,0,'Algemeen BTW tarief (vanaf 1 oktober 2012)',0,NULL),(201,20,25,0,0,0,'VAT standard rate',1,NULL),(202,20,12,0,0,0,'VAT reduced rate',1,NULL),(203,20,6,0,0,0,'VAT super-reduced rate',1,NULL),(204,20,0,0,0,0,'VAT Rate 0',1,NULL),(231,23,21,0,0,0,'IVA standard rate',1,NULL),(232,23,10.5,0,0,0,'IVA reduced rate',1,NULL),(233,23,0,0,0,0,'IVA Rate 0',1,NULL),(251,25,20,0,0,0,'VAT standard rate',1,NULL),(252,25,12,0,0,0,'VAT reduced rate',1,NULL),(253,25,0,0,0,0,'VAT Rate 0',1,NULL),(254,25,5,0,0,0,'VAT reduced rate',1,NULL),(281,28,10,0,0,0,'VAT standard rate',1,NULL),(282,28,0,0,0,0,'VAT Rate 0',1,NULL),(411,41,20,0,0,0,'VAT standard rate',1,NULL),(412,41,10,0,0,0,'VAT reduced rate',1,NULL),(413,41,0,0,0,0,'VAT Rate 0',1,NULL),(461,46,0,0,0,0,'No VAT',1,NULL),(462,46,15,0,0,0,'VAT 15%',1,NULL),(463,46,7.5,0,0,0,'VAT 7.5%',1,NULL),(561,56,0,0,0,0,'VAT reduced rate',1,NULL),(591,59,20,0,0,0,'VAT standard rate',1,NULL),(592,59,7,0,0,0,'VAT reduced rate',1,NULL),(593,59,0,0,0,0,'VAT Rate 0',1,NULL),(671,67,19,0,0,0,'VAT standard rate',1,NULL),(672,67,0,0,0,0,'VAT Rate 0',1,NULL),(801,80,25,0,0,0,'VAT standard rate',1,NULL),(802,80,0,0,0,0,'VAT Rate 0',1,NULL),(861,86,13,0,0,0,'IVA 13',1,NULL),(862,86,0,0,0,0,'SIN IVA',1,NULL),(1141,114,0,0,0,0,'No ISV',1,NULL),(1142,114,12,0,0,0,'ISV 12%',1,NULL),(1161,116,25.5,0,0,0,'VAT standard rate',1,NULL),(1162,116,7,0,0,0,'VAT reduced rate',1,NULL),(1163,116,0,0,0,0,'VAT rate 0',1,NULL),(1171,117,12.5,0,0,0,'VAT standard rate',1,NULL),(1172,117,4,0,0,0,'VAT reduced rate',1,NULL),(1173,117,1,0,0,0,'VAT super-reduced rate',1,NULL),(1174,117,0,0,0,0,'VAT Rate 0',1,NULL),(1231,123,0,0,0,0,'VAT Rate 0',1,NULL),(1232,123,5,0,0,0,'VAT Rate 5',1,NULL),(1401,140,15,0,0,0,'VAT standard rate',1,NULL),(1402,140,12,0,0,0,'VAT reduced rate',1,NULL),(1403,140,6,0,0,0,'VAT reduced rate',1,NULL),(1404,140,3,0,0,0,'VAT super-reduced rate',1,NULL),(1405,140,0,0,0,0,'VAT Rate 0',1,NULL),(1521,152,0,0,0,0,'VAT Rate 0',1,NULL),(1522,152,15,0,0,0,'VAT Rate 15',1,NULL),(1541,154,0,0,0,0,'No VAT',1,NULL),(1542,154,16,0,0,0,'VAT 16%',1,NULL),(1543,154,10,0,0,0,'VAT Frontero',1,NULL),(1662,166,15,0,0,0,'VAT standard rate',1,NULL),(1663,166,0,0,0,0,'VAT Rate 0',1,NULL),(1731,173,25,0,0,0,'VAT standard rate',1,NULL),(1732,173,14,0,0,0,'VAT reduced rate',1,NULL),(1733,173,8,0,0,0,'VAT reduced rate',1,NULL),(1734,173,0,0,0,0,'VAT Rate 0',1,NULL),(1841,184,20,0,0,0,'VAT standard rate',1,NULL),(1842,184,7,0,0,0,'VAT reduced rate',1,NULL),(1843,184,3,0,0,0,'VAT reduced rate',1,NULL),(1844,184,0,0,0,0,'VAT Rate 0',1,NULL),(1881,188,24,0,0,0,'VAT standard rate',1,NULL),(1882,188,9,0,0,0,'VAT reduced rate',1,NULL),(1883,188,0,0,0,0,'VAT Rate 0',1,NULL),(1884,188,5,0,0,0,'VAT reduced rate',1,NULL),(1931,193,0,0,0,0,'No VAT in SPM',1,NULL),(2011,201,19,0,0,0,'VAT standard rate',1,NULL),(2012,201,10,0,0,0,'VAT reduced rate',1,NULL),(2013,201,0,0,0,0,'VAT Rate 0',1,NULL),(2021,202,20,0,0,0,'VAT standard rate',1,NULL),(2022,202,8.5,0,0,0,'VAT reduced rate',1,NULL),(2023,202,0,0,0,0,'VAT Rate 0',1,NULL),(2261,226,20,0,0,0,'VAT standart rate',1,NULL),(2262,226,0,0,0,0,'VAT Rate 0',1,NULL),(2321,232,0,0,0,0,'No VAT',1,NULL),(2322,232,12,0,0,0,'VAT 12%',1,NULL),(2323,232,8,0,0,0,'VAT 8%',1,NULL),(2461,246,0,0,0,0,'VAT Rate 0',1,NULL);
/*!40000 ALTER TABLE `llx_c_tva` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_type_contact`
--

DROP TABLE IF EXISTS `llx_c_type_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_type_contact` (
  `rowid` int(11) NOT NULL,
  `element` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `source` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'external',
  `code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_c_type_contact_uk` (`element`,`source`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_type_contact`
--

LOCK TABLES `llx_c_type_contact` WRITE;
/*!40000 ALTER TABLE `llx_c_type_contact` DISABLE KEYS */;
INSERT INTO `llx_c_type_contact` VALUES (10,'contrat','internal','SALESREPSIGN','Commercial signataire du contrat',1,NULL),(11,'contrat','internal','SALESREPFOLL','Commercial suivi du contrat',1,NULL),(20,'contrat','external','BILLING','Contact client facturation contrat',1,NULL),(21,'contrat','external','CUSTOMER','Contact client suivi contrat',1,NULL),(22,'contrat','external','SALESREPSIGN','Contact client signataire contrat',1,NULL),(31,'propal','internal','SALESREPFOLL','Commercial à l\'origine de la propale',1,NULL),(40,'propal','external','BILLING','Contact client facturation propale',1,NULL),(41,'propal','external','CUSTOMER','Contact client suivi propale',1,NULL),(50,'facture','internal','SALESREPFOLL','Responsable suivi du paiement',1,NULL),(60,'facture','external','BILLING','Contact client facturation',1,NULL),(61,'facture','external','SHIPPING','Contact client livraison',1,NULL),(62,'facture','external','SERVICE','Contact client prestation',1,NULL),(70,'invoice_supplier','internal','SALESREPFOLL','Responsable suivi du paiement',1,NULL),(71,'invoice_supplier','external','BILLING','Contact fournisseur facturation',1,NULL),(72,'invoice_supplier','external','SHIPPING','Contact fournisseur livraison',1,NULL),(73,'invoice_supplier','external','SERVICE','Contact fournisseur prestation',1,NULL),(91,'commande','internal','SALESREPFOLL','Responsable suivi de la commande',1,NULL),(100,'commande','external','BILLING','Contact client facturation commande',1,NULL),(101,'commande','external','CUSTOMER','Contact client suivi commande',1,NULL),(102,'commande','external','SHIPPING','Contact client livraison commande',1,NULL),(120,'fichinter','internal','INTERREPFOLL','Responsable suivi de l\'intervention',1,NULL),(121,'fichinter','internal','INTERVENING','Intervenant',1,NULL),(130,'fichinter','external','BILLING','Contact client facturation intervention',1,NULL),(131,'fichinter','external','CUSTOMER','Contact client suivi de l\'intervention',1,NULL),(140,'order_supplier','internal','SALESREPFOLL','Responsable suivi de la commande',1,NULL),(141,'order_supplier','internal','SHIPPING','Responsable réception de la commande',1,NULL),(142,'order_supplier','external','BILLING','Contact fournisseur facturation commande',1,NULL),(143,'order_supplier','external','CUSTOMER','Contact fournisseur suivi commande',1,NULL),(145,'order_supplier','external','SHIPPING','Contact fournisseur livraison commande',1,NULL),(160,'project','internal','PROJECTLEADER','Chef de Projet',1,NULL),(161,'project','internal','CONTRIBUTOR','Intervenant',1,NULL),(170,'project','external','PROJECTLEADER','Chef de Projet',1,NULL),(171,'project','external','CONTRIBUTOR','Intervenant',1,NULL),(180,'project_task','internal','TASKEXECUTIVE','Responsable',1,NULL),(181,'project_task','internal','CONTRIBUTOR','Intervenant',1,NULL),(190,'project_task','external','TASKEXECUTIVE','Responsable',1,NULL),(191,'project_task','external','CONTRIBUTOR','Intervenant',1,NULL);
/*!40000 ALTER TABLE `llx_c_type_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_type_fees`
--

DROP TABLE IF EXISTS `llx_c_type_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_type_fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_type_fees`
--

LOCK TABLES `llx_c_type_fees` WRITE;
/*!40000 ALTER TABLE `llx_c_type_fees` DISABLE KEYS */;
INSERT INTO `llx_c_type_fees` VALUES (1,'TF_OTHER','Other',1,NULL),(2,'TF_TRIP','Trip',1,NULL),(3,'TF_LUNCH','Lunch',1,NULL);
/*!40000 ALTER TABLE `llx_c_type_fees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_typent`
--

DROP TABLE IF EXISTS `llx_c_typent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_typent` (
  `id` int(11) NOT NULL,
  `code` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_typent`
--

LOCK TABLES `llx_c_typent` WRITE;
/*!40000 ALTER TABLE `llx_c_typent` DISABLE KEYS */;
INSERT INTO `llx_c_typent` VALUES (0,'TE_UNKNOWN','-',1,NULL),(1,'TE_STARTUP','Start-up',0,NULL),(2,'TE_GROUP','Grand groupe',1,NULL),(3,'TE_MEDIUM','PME/PMI',1,NULL),(4,'TE_SMALL','TPE',1,NULL),(5,'TE_ADMIN','Administration',1,NULL),(6,'TE_WHOLE','Grossiste',0,NULL),(7,'TE_RETAIL','Revendeur',0,NULL),(8,'TE_PRIVATE','Particulier',1,NULL),(100,'TE_OTHER','Autres',1,NULL);
/*!40000 ALTER TABLE `llx_c_typent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_c_ziptown`
--

DROP TABLE IF EXISTS `llx_c_ziptown`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_c_ziptown` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_county` int(11) DEFAULT NULL,
  `fk_pays` int(11) NOT NULL DEFAULT '0',
  `zip` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `town` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_ziptown_fk_pays` (`zip`,`town`,`fk_pays`),
  KEY `idx_c_ziptown_fk_county` (`fk_county`),
  KEY `idx_c_ziptown_fk_pays` (`fk_pays`),
  KEY `idx_c_ziptown_zip` (`zip`),
  CONSTRAINT `fk_c_ziptown_fk_county` FOREIGN KEY (`fk_county`) REFERENCES `llx_c_departements` (`rowid`),
  CONSTRAINT `fk_c_ziptown_fk_pays` FOREIGN KEY (`fk_pays`) REFERENCES `llx_c_pays` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_c_ziptown`
--

LOCK TABLES `llx_c_ziptown` WRITE;
/*!40000 ALTER TABLE `llx_c_ziptown` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_c_ziptown` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_categorie`
--

DROP TABLE IF EXISTS `llx_categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_categorie` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `entity` int(11) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_unicode_ci,
  `fk_soc` int(11) DEFAULT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_categorie_ref` (`label`,`type`,`entity`),
  KEY `idx_categorie_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_categorie`
--

LOCK TABLES `llx_categorie` WRITE;
/*!40000 ALTER TABLE `llx_categorie` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_categorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_categorie_association`
--

DROP TABLE IF EXISTS `llx_categorie_association`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_categorie_association` (
  `fk_categorie_mere` int(11) NOT NULL,
  `fk_categorie_fille` int(11) NOT NULL,
  UNIQUE KEY `uk_categorie_association` (`fk_categorie_mere`,`fk_categorie_fille`),
  UNIQUE KEY `uk_categorie_association_fk_categorie_fille` (`fk_categorie_fille`),
  CONSTRAINT `fk_categorie_asso_fk_categorie_fille` FOREIGN KEY (`fk_categorie_fille`) REFERENCES `llx_categorie` (`rowid`),
  CONSTRAINT `fk_categorie_asso_fk_categorie_mere` FOREIGN KEY (`fk_categorie_mere`) REFERENCES `llx_categorie` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_categorie_association`
--

LOCK TABLES `llx_categorie_association` WRITE;
/*!40000 ALTER TABLE `llx_categorie_association` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_categorie_association` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_categorie_fournisseur`
--

DROP TABLE IF EXISTS `llx_categorie_fournisseur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_categorie_fournisseur` (
  `fk_categorie` int(11) NOT NULL,
  `fk_societe` int(11) NOT NULL,
  PRIMARY KEY (`fk_categorie`,`fk_societe`),
  KEY `idx_categorie_fournisseur_fk_categorie` (`fk_categorie`),
  KEY `idx_categorie_fournisseur_fk_societe` (`fk_societe`),
  CONSTRAINT `fk_categorie_fournisseur_fk_soc` FOREIGN KEY (`fk_societe`) REFERENCES `llx_societe` (`rowid`),
  CONSTRAINT `fk_categorie_fournisseur_categorie_rowid` FOREIGN KEY (`fk_categorie`) REFERENCES `llx_categorie` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_categorie_fournisseur`
--

LOCK TABLES `llx_categorie_fournisseur` WRITE;
/*!40000 ALTER TABLE `llx_categorie_fournisseur` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_categorie_fournisseur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_categorie_member`
--

DROP TABLE IF EXISTS `llx_categorie_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_categorie_member` (
  `fk_categorie` int(11) NOT NULL,
  `fk_member` int(11) NOT NULL,
  PRIMARY KEY (`fk_categorie`,`fk_member`),
  KEY `idx_categorie_member_fk_categorie` (`fk_categorie`),
  KEY `idx_categorie_member_fk_member` (`fk_member`),
  CONSTRAINT `fk_categorie_member_member_rowid` FOREIGN KEY (`fk_member`) REFERENCES `llx_adherent` (`rowid`),
  CONSTRAINT `fk_categorie_member_categorie_rowid` FOREIGN KEY (`fk_categorie`) REFERENCES `llx_categorie` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_categorie_member`
--

LOCK TABLES `llx_categorie_member` WRITE;
/*!40000 ALTER TABLE `llx_categorie_member` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_categorie_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_categorie_product`
--

DROP TABLE IF EXISTS `llx_categorie_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_categorie_product` (
  `fk_categorie` int(11) NOT NULL,
  `fk_product` int(11) NOT NULL,
  PRIMARY KEY (`fk_categorie`,`fk_product`),
  KEY `idx_categorie_product_fk_categorie` (`fk_categorie`),
  KEY `idx_categorie_product_fk_product` (`fk_product`),
  CONSTRAINT `fk_categorie_product_product_rowid` FOREIGN KEY (`fk_product`) REFERENCES `llx_product` (`rowid`),
  CONSTRAINT `fk_categorie_product_categorie_rowid` FOREIGN KEY (`fk_categorie`) REFERENCES `llx_categorie` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_categorie_product`
--

LOCK TABLES `llx_categorie_product` WRITE;
/*!40000 ALTER TABLE `llx_categorie_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_categorie_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_categorie_societe`
--

DROP TABLE IF EXISTS `llx_categorie_societe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_categorie_societe` (
  `fk_categorie` int(11) NOT NULL,
  `fk_societe` int(11) NOT NULL,
  PRIMARY KEY (`fk_categorie`,`fk_societe`),
  KEY `idx_categorie_societe_fk_categorie` (`fk_categorie`),
  KEY `idx_categorie_societe_fk_societe` (`fk_societe`),
  CONSTRAINT `fk_categorie_societe_fk_soc` FOREIGN KEY (`fk_societe`) REFERENCES `llx_societe` (`rowid`),
  CONSTRAINT `fk_categorie_societe_categorie_rowid` FOREIGN KEY (`fk_categorie`) REFERENCES `llx_categorie` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_categorie_societe`
--

LOCK TABLES `llx_categorie_societe` WRITE;
/*!40000 ALTER TABLE `llx_categorie_societe` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_categorie_societe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_chargesociales`
--

DROP TABLE IF EXISTS `llx_chargesociales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_chargesociales` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `date_ech` datetime NOT NULL,
  `libelle` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_creation` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `fk_type` int(11) NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `paye` smallint(6) NOT NULL DEFAULT '0',
  `periode` date DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_chargesociales`
--

LOCK TABLES `llx_chargesociales` WRITE;
/*!40000 ALTER TABLE `llx_chargesociales` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_chargesociales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_commande`
--

DROP TABLE IF EXISTS `llx_commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_commande` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_int` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_client` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_soc` int(11) NOT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_creation` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `date_commande` date DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_user_cloture` int(11) DEFAULT NULL,
  `source` smallint(6) DEFAULT NULL,
  `fk_statut` smallint(6) DEFAULT '0',
  `amount_ht` double DEFAULT '0',
  `remise_percent` double DEFAULT '0',
  `remise_absolue` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `note` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `model_pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facture` tinyint(4) DEFAULT '0',
  `fk_account` int(11) DEFAULT NULL,
  `fk_currency` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_cond_reglement` int(11) DEFAULT NULL,
  `fk_mode_reglement` int(11) DEFAULT NULL,
  `date_livraison` date DEFAULT NULL,
  `fk_availability` int(11) DEFAULT NULL,
  `fk_demand_reason` int(11) DEFAULT NULL,
  `fk_adresse_livraison` int(11) DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extraparams` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_commande_ref` (`ref`,`entity`),
  KEY `idx_commande_fk_soc` (`fk_soc`),
  KEY `idx_commande_fk_user_author` (`fk_user_author`),
  KEY `idx_commande_fk_user_valid` (`fk_user_valid`),
  KEY `idx_commande_fk_user_cloture` (`fk_user_cloture`),
  KEY `idx_commande_fk_projet` (`fk_projet`),
  KEY `idx_commande_fk_account` (`fk_account`),
  KEY `idx_commande_fk_currency` (`fk_currency`),
  CONSTRAINT `fk_commande_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  CONSTRAINT `fk_commande_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  CONSTRAINT `fk_commande_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_commande_fk_user_cloture` FOREIGN KEY (`fk_user_cloture`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_commande_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_commande`
--

LOCK TABLES `llx_commande` WRITE;
/*!40000 ALTER TABLE `llx_commande` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_commande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_commande_fournisseur`
--

DROP TABLE IF EXISTS `llx_commande_fournisseur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_commande_fournisseur` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_supplier` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_soc` int(11) NOT NULL,
  `fk_projet` int(11) DEFAULT '0',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_creation` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `date_commande` date DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_user_cloture` int(11) DEFAULT NULL,
  `source` smallint(6) NOT NULL,
  `fk_statut` smallint(6) DEFAULT '0',
  `amount_ht` double DEFAULT '0',
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `note` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `model_pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_cond_reglement` int(11) DEFAULT NULL,
  `fk_mode_reglement` int(11) DEFAULT NULL,
  `fk_methode_commande` int(11) DEFAULT '0',
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extraparams` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_commande_fournisseur_ref` (`ref`,`fk_soc`,`entity`),
  KEY `idx_commande_fournisseur_fk_soc` (`fk_soc`),
  CONSTRAINT `fk_commande_fournisseur_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_commande_fournisseur`
--

LOCK TABLES `llx_commande_fournisseur` WRITE;
/*!40000 ALTER TABLE `llx_commande_fournisseur` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_commande_fournisseur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_commande_fournisseur_dispatch`
--

DROP TABLE IF EXISTS `llx_commande_fournisseur_dispatch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_commande_fournisseur_dispatch` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_commande` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `qty` float DEFAULT NULL,
  `fk_entrepot` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `datec` datetime DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_commande_fournisseur_dispatch_fk_commande` (`fk_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_commande_fournisseur_dispatch`
--

LOCK TABLES `llx_commande_fournisseur_dispatch` WRITE;
/*!40000 ALTER TABLE `llx_commande_fournisseur_dispatch` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_commande_fournisseur_dispatch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_commande_fournisseur_log`
--

DROP TABLE IF EXISTS `llx_commande_fournisseur_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_commande_fournisseur_log` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datelog` datetime NOT NULL,
  `fk_commande` int(11) NOT NULL,
  `fk_statut` smallint(6) NOT NULL,
  `fk_user` int(11) NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_commande_fournisseur_log`
--

LOCK TABLES `llx_commande_fournisseur_log` WRITE;
/*!40000 ALTER TABLE `llx_commande_fournisseur_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_commande_fournisseur_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_commande_fournisseurdet`
--

DROP TABLE IF EXISTS `llx_commande_fournisseurdet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_commande_fournisseurdet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_commande` int(11) NOT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `ref` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `tva_tx` double(6,3) DEFAULT '0.000',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `subprice` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_tva` double(24,8) DEFAULT '0.00000000',
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `product_type` int(11) DEFAULT '0',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `info_bits` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_commande_fournisseurdet`
--

LOCK TABLES `llx_commande_fournisseurdet` WRITE;
/*!40000 ALTER TABLE `llx_commande_fournisseurdet` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_commande_fournisseurdet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_commandedet`
--

DROP TABLE IF EXISTS `llx_commandedet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_commandedet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_commande` int(11) NOT NULL,
  `fk_parent_line` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `tva_tx` double(6,3) DEFAULT NULL,
  `localtax1_tx` double(6,3) DEFAULT NULL,
  `localtax2_tx` double(6,3) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `fk_remise_except` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `subprice` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_tva` double(24,8) DEFAULT '0.00000000',
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `product_type` int(11) DEFAULT '0',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `info_bits` int(11) DEFAULT '0',
  `marge_tx` double(6,3) DEFAULT '0.000',
  `marque_tx` double(6,3) DEFAULT '0.000',
  `special_code` int(10) unsigned DEFAULT '0',
  `rang` int(11) DEFAULT '0',
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_commandedet_fk_commande` (`fk_commande`),
  KEY `idx_commandedet_fk_product` (`fk_product`),
  CONSTRAINT `fk_commandedet_fk_commande` FOREIGN KEY (`fk_commande`) REFERENCES `llx_commande` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_commandedet`
--

LOCK TABLES `llx_commandedet` WRITE;
/*!40000 ALTER TABLE `llx_commandedet` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_commandedet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_compta`
--

DROP TABLE IF EXISTS `llx_compta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_compta` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `datev` date DEFAULT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_compta_account` int(11) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `valid` tinyint(4) DEFAULT '0',
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_compta`
--

LOCK TABLES `llx_compta` WRITE;
/*!40000 ALTER TABLE `llx_compta` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_compta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_compta_account`
--

DROP TABLE IF EXISTS `llx_compta_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_compta_account` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `number` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_compta_account`
--

LOCK TABLES `llx_compta_account` WRITE;
/*!40000 ALTER TABLE `llx_compta_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_compta_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_compta_compte_generaux`
--

DROP TABLE IF EXISTS `llx_compta_compte_generaux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_compta_compte_generaux` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `date_creation` datetime DEFAULT NULL,
  `numero` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `intitule` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `numero` (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_compta_compte_generaux`
--

LOCK TABLES `llx_compta_compte_generaux` WRITE;
/*!40000 ALTER TABLE `llx_compta_compte_generaux` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_compta_compte_generaux` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_const`
--

DROP TABLE IF EXISTS `llx_const`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_const` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  `note` text COLLATE utf8_unicode_ci,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_const` (`name`,`entity`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_const`
--

LOCK TABLES `llx_const` WRITE;
/*!40000 ALTER TABLE `llx_const` DISABLE KEYS */;
INSERT INTO `llx_const` VALUES (2,'MAIN_FEATURES_LEVEL',0,'0','chaine',1,'Level of features to show (0=stable only, 1=stable+experimental, 2=stable+experimental+development','2012-10-11 14:07:49'),(3,'SYSLOG_FILE_ON',0,'0','chaine',0,'Log to file Directory where to write log file','2013-06-08 18:50:50'),(4,'SYSLOG_FILE',0,'DOL_DATA_ROOT/dolibarr.log','chaine',0,'Directory where to write log file','2012-10-11 14:07:49'),(5,'SYSLOG_LEVEL',0,'7','chaine',0,'Level of debug info to show','2012-10-11 14:07:49'),(6,'MAIN_MAIL_SMTP_SERVER',0,'','chaine',0,'Host or ip address for SMTP server','2012-10-11 14:07:49'),(7,'MAIN_MAIL_SMTP_PORT',0,'','chaine',0,'Port for SMTP server','2012-10-11 14:07:49'),(8,'MAIN_UPLOAD_DOC',0,'2048','chaine',0,'Max size for file upload (0 means no upload allowed)','2012-10-11 14:07:49'),(9,'MAIN_MONNAIE',1,'EUR','chaine',0,'Monnaie','2012-10-11 14:07:49'),(10,'MAIN_MAIL_EMAIL_FROM',1,'dolibarr-robot@domain.com','chaine',0,'EMail emetteur pour les emails automatiques Dolibarr','2012-10-11 14:07:49'),(11,'MAIN_SIZE_LISTE_LIMIT',0,'25','chaine',0,'Longueur maximum des listes','2012-10-11 14:07:49'),(12,'MAIN_SHOW_WORKBOARD',0,'1','yesno',0,'Affichage tableau de bord de travail Dolibarr','2012-10-11 14:07:49'),(13,'MAIN_MENU_STANDARD',1,'eldy_backoffice.php','chaine',0,'Module de gestion de la barre de menu pour utilisateurs internes','2012-10-11 14:07:49'),(14,'MAIN_MENUFRONT_STANDARD',1,'eldy_frontoffice.php','chaine',0,'Module de gestion de la barre de menu pour utilisateurs externes','2012-10-11 14:07:49'),(15,'MAIN_MENU_SMARTPHONE',1,'eldy_backoffice.php','chaine',0,'Module de gestion de la barre de menu smartphone pour utilisateurs internes','2012-10-11 14:07:49'),(16,'MAIN_MENUFRONT_SMARTPHONE',1,'eldy_frontoffice.php','chaine',0,'Module de gestion de la barre de menu smartphone pour utilisateurs externes','2012-10-11 14:07:49'),(17,'MAIN_DELAY_ACTIONS_TODO',1,'7','chaine',0,'Tolérance de retard avant alerte (en jours) sur actions planifiées non réalisées','2012-10-11 14:07:49'),(18,'MAIN_DELAY_ORDERS_TO_PROCESS',1,'2','chaine',0,'Tolérance de retard avant alerte (en jours) sur commandes clients non traitées','2012-10-11 14:07:49'),(19,'MAIN_DELAY_SUPPLIER_ORDERS_TO_PROCESS',1,'7','chaine',0,'Tolérance de retard avant alerte (en jours) sur commandes fournisseurs non traitées','2012-10-11 14:07:49'),(20,'MAIN_DELAY_PROPALS_TO_CLOSE',1,'31','chaine',0,'Tolérance de retard avant alerte (en jours) sur propales à cloturer','2012-10-11 14:07:49'),(21,'MAIN_DELAY_PROPALS_TO_BILL',1,'7','chaine',0,'Tolérance de retard avant alerte (en jours) sur propales non facturées','2012-10-11 14:07:49'),(22,'MAIN_DELAY_CUSTOMER_BILLS_UNPAYED',1,'31','chaine',0,'Tolérance de retard avant alerte (en jours) sur factures client impayées','2012-10-11 14:07:49'),(23,'MAIN_DELAY_SUPPLIER_BILLS_TO_PAY',1,'2','chaine',0,'Tolérance de retard avant alerte (en jours) sur factures fournisseur impayées','2012-10-11 14:07:49'),(24,'MAIN_DELAY_NOT_ACTIVATED_SERVICES',1,'0','chaine',0,'Tolérance de retard avant alerte (en jours) sur services à activer','2012-10-11 14:07:49'),(25,'MAIN_DELAY_RUNNING_SERVICES',1,'0','chaine',0,'Tolérance de retard avant alerte (en jours) sur services expirés','2012-10-11 14:07:49'),(26,'MAIN_DELAY_MEMBERS',1,'31','chaine',0,'Tolérance de retard avant alerte (en jours) sur cotisations adhérent en retard','2012-10-11 14:07:49'),(27,'MAIN_DELAY_TRANSACTIONS_TO_CONCILIATE',1,'62','chaine',0,'Tolérance de retard avant alerte (en jours) sur rapprochements bancaires à faire','2012-10-11 14:07:49'),(28,'SOCIETE_NOLIST_COURRIER',0,'1','yesno',0,'Liste les fichiers du repertoire courrier','2012-10-11 14:07:49'),(29,'SOCIETE_CODECLIENT_ADDON',1,'mod_codeclient_leopard','yesno',0,'Module to control third parties codes','2012-10-11 14:07:49'),(30,'SOCIETE_CODECOMPTA_ADDON',1,'mod_codecompta_panicum','yesno',0,'Module to control third parties codes','2012-10-11 14:07:49'),(31,'MAILING_EMAIL_FROM',1,'dolibarr@domain.com','chaine',0,'EMail emmetteur pour les envois d emailings','2012-10-11 14:07:49'),(63,'MAIN_MODULE_USER',0,'1',NULL,0,NULL,'2012-10-11 14:11:12'),(64,'MAIN_VERSION_LAST_INSTALL',0,'3.2.3','chaine',0,'Dolibarr version when install','2012-10-11 14:11:12'),(65,'MAIN_LANG_DEFAULT',1,'auto','chaine',0,'Default language','2012-10-11 14:11:12'),(66,'MAIN_MODULE_MEMCACHED',1,'1',NULL,0,NULL,'2012-05-17 16:11:10'),(67,'MEMCACHED_SERVER',1,'localhost:11211','chaine',0,'','2012-05-17 16:14:36'),(70,'MAIN_FIX_FOR_BUGGED_MTA',1,'1','chaine',1,'Do not delete this value','2013-06-08 18:49:46');
/*!40000 ALTER TABLE `llx_const` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_contrat`
--

DROP TABLE IF EXISTS `llx_contrat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_contrat` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `date_contrat` datetime DEFAULT NULL,
  `statut` smallint(6) DEFAULT '0',
  `mise_en_service` datetime DEFAULT NULL,
  `fin_validite` datetime DEFAULT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `fk_soc` int(11) NOT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `fk_commercial_signature` int(11) NOT NULL,
  `fk_commercial_suivi` int(11) NOT NULL,
  `fk_user_author` int(11) NOT NULL DEFAULT '0',
  `fk_user_mise_en_service` int(11) DEFAULT NULL,
  `fk_user_cloture` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extraparams` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_contrat_ref` (`ref`,`entity`),
  KEY `idx_contrat_fk_soc` (`fk_soc`),
  KEY `idx_contrat_fk_user_author` (`fk_user_author`),
  CONSTRAINT `fk_contrat_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_contrat_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_contrat`
--

LOCK TABLES `llx_contrat` WRITE;
/*!40000 ALTER TABLE `llx_contrat` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_contrat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_contratdet`
--

DROP TABLE IF EXISTS `llx_contratdet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_contratdet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_contrat` int(11) NOT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `statut` smallint(6) DEFAULT '0',
  `label` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `fk_remise_except` int(11) DEFAULT NULL,
  `date_commande` datetime DEFAULT NULL,
  `date_ouverture_prevue` datetime DEFAULT NULL,
  `date_ouverture` datetime DEFAULT NULL,
  `date_fin_validite` datetime DEFAULT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `tva_tx` double(6,3) DEFAULT '0.000',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `qty` double NOT NULL,
  `remise_percent` double DEFAULT '0',
  `subprice` double(24,8) DEFAULT '0.00000000',
  `price_ht` double DEFAULT NULL,
  `remise` double DEFAULT '0',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_tva` double(24,8) DEFAULT '0.00000000',
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `info_bits` int(11) DEFAULT '0',
  `fk_user_author` int(11) NOT NULL DEFAULT '0',
  `fk_user_ouverture` int(11) DEFAULT NULL,
  `fk_user_cloture` int(11) DEFAULT NULL,
  `commentaire` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`),
  KEY `idx_contratdet_fk_contrat` (`fk_contrat`),
  KEY `idx_contratdet_fk_product` (`fk_product`),
  KEY `idx_contratdet_date_ouverture_prevue` (`date_ouverture_prevue`),
  KEY `idx_contratdet_date_ouverture` (`date_ouverture`),
  KEY `idx_contratdet_date_fin_validite` (`date_fin_validite`),
  CONSTRAINT `fk_contratdet_fk_product` FOREIGN KEY (`fk_product`) REFERENCES `llx_product` (`rowid`),
  CONSTRAINT `fk_contratdet_fk_contrat` FOREIGN KEY (`fk_contrat`) REFERENCES `llx_contrat` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_contratdet`
--

LOCK TABLES `llx_contratdet` WRITE;
/*!40000 ALTER TABLE `llx_contratdet` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_contratdet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_contratdet_log`
--

DROP TABLE IF EXISTS `llx_contratdet_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_contratdet_log` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_contratdet` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `statut` smallint(6) NOT NULL,
  `fk_user_author` int(11) NOT NULL,
  `commentaire` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`),
  KEY `idx_contratdet_log_fk_contratdet` (`fk_contratdet`),
  KEY `idx_contratdet_log_date` (`date`),
  CONSTRAINT `fk_contratdet_log_fk_contratdet` FOREIGN KEY (`fk_contratdet`) REFERENCES `llx_contratdet` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_contratdet_log`
--

LOCK TABLES `llx_contratdet_log` WRITE;
/*!40000 ALTER TABLE `llx_contratdet_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_contratdet_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_cotisation`
--

DROP TABLE IF EXISTS `llx_cotisation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_cotisation` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `fk_adherent` int(11) DEFAULT NULL,
  `dateadh` datetime DEFAULT NULL,
  `datef` date DEFAULT NULL,
  `cotisation` double DEFAULT NULL,
  `fk_bank` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_cotisation` (`fk_adherent`,`dateadh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_cotisation`
--

LOCK TABLES `llx_cotisation` WRITE;
/*!40000 ALTER TABLE `llx_cotisation` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_cotisation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_deplacement`
--

DROP TABLE IF EXISTS `llx_deplacement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_deplacement` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datec` datetime NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dated` datetime DEFAULT NULL,
  `fk_user` int(11) NOT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  `type` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `fk_statut` int(11) NOT NULL DEFAULT '1',
  `km` double DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT '0',
  `note` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `extraparams` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_deplacement`
--

LOCK TABLES `llx_deplacement` WRITE;
/*!40000 ALTER TABLE `llx_deplacement` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_deplacement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_document`
--

DROP TABLE IF EXISTS `llx_document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_document` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_extension` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `date_generation` datetime DEFAULT NULL,
  `fk_owner` int(11) DEFAULT NULL,
  `fk_group` int(11) DEFAULT NULL,
  `permissions` char(9) COLLATE utf8_unicode_ci DEFAULT 'rw-rw-rw',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_document`
--

LOCK TABLES `llx_document` WRITE;
/*!40000 ALTER TABLE `llx_document` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_document_generator`
--

DROP TABLE IF EXISTS `llx_document_generator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_document_generator` (
  `rowid` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `classfile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_document_generator`
--

LOCK TABLES `llx_document_generator` WRITE;
/*!40000 ALTER TABLE `llx_document_generator` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_document_generator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_document_model`
--

DROP TABLE IF EXISTS `llx_document_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_document_model` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_document_model` (`nom`,`type`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_document_model`
--

LOCK TABLES `llx_document_model` WRITE;
/*!40000 ALTER TABLE `llx_document_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_document_model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_dolibarr_modules`
--

DROP TABLE IF EXISTS `llx_dolibarr_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_dolibarr_modules` (
  `numero` int(11) NOT NULL DEFAULT '0',
  `entity` int(11) NOT NULL DEFAULT '1',
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `active_date` datetime NOT NULL,
  `active_version` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`numero`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_dolibarr_modules`
--

LOCK TABLES `llx_dolibarr_modules` WRITE;
/*!40000 ALTER TABLE `llx_dolibarr_modules` DISABLE KEYS */;
INSERT INTO `llx_dolibarr_modules` VALUES (0,1,1,'2012-10-11 14:11:12','dolibarr');
/*!40000 ALTER TABLE `llx_dolibarr_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_domain`
--

DROP TABLE IF EXISTS `llx_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_domain` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_domain`
--

LOCK TABLES `llx_domain` WRITE;
/*!40000 ALTER TABLE `llx_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_don`
--

DROP TABLE IF EXISTS `llx_don`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_don` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `datec` datetime DEFAULT NULL,
  `datedon` datetime DEFAULT NULL,
  `amount` double DEFAULT '0',
  `fk_paiement` int(11) DEFAULT NULL,
  `prenom` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `societe` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse` text COLLATE utf8_unicode_ci,
  `cp` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pays` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_mobile` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `public` smallint(6) NOT NULL DEFAULT '1',
  `fk_don_projet` int(11) DEFAULT NULL,
  `fk_user_author` int(11) NOT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `model_pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_don`
--

LOCK TABLES `llx_don` WRITE;
/*!40000 ALTER TABLE `llx_don` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_don` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_ecm_directories`
--

DROP TABLE IF EXISTS `llx_ecm_directories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_ecm_directories` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_parent` int(11) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cachenbofdoc` int(11) NOT NULL DEFAULT '0',
  `date_c` datetime DEFAULT NULL,
  `date_m` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_user_c` int(11) DEFAULT NULL,
  `fk_user_m` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_ecm_directories` (`label`,`fk_parent`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_ecm_directories`
--

LOCK TABLES `llx_ecm_directories` WRITE;
/*!40000 ALTER TABLE `llx_ecm_directories` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_ecm_directories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_ecm_documents`
--

DROP TABLE IF EXISTS `llx_ecm_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_ecm_documents` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filesize` int(11) NOT NULL,
  `filemime` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fullpath_dol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fullpath_orig` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `manualkeyword` text COLLATE utf8_unicode_ci,
  `fk_create` int(11) NOT NULL,
  `fk_update` int(11) DEFAULT NULL,
  `date_c` datetime NOT NULL,
  `date_u` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_directory` int(11) DEFAULT NULL,
  `fk_status` smallint(6) DEFAULT '0',
  `private` smallint(6) DEFAULT '0',
  `crc` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cryptkey` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cipher` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'twofish',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_ecm_documents` (`fullpath_dol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_ecm_documents`
--

LOCK TABLES `llx_ecm_documents` WRITE;
/*!40000 ALTER TABLE `llx_ecm_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_ecm_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_element_contact`
--

DROP TABLE IF EXISTS `llx_element_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_element_contact` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datecreate` datetime DEFAULT NULL,
  `statut` smallint(6) DEFAULT '5',
  `element_id` int(11) NOT NULL,
  `fk_c_type_contact` int(11) NOT NULL,
  `fk_socpeople` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_element_contact_idx1` (`element_id`,`fk_c_type_contact`,`fk_socpeople`),
  KEY `fk_element_contact_fk_c_type_contact` (`fk_c_type_contact`),
  KEY `idx_element_contact_fk_socpeople` (`fk_socpeople`),
  CONSTRAINT `fk_element_contact_fk_c_type_contact` FOREIGN KEY (`fk_c_type_contact`) REFERENCES `llx_c_type_contact` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_element_contact`
--

LOCK TABLES `llx_element_contact` WRITE;
/*!40000 ALTER TABLE `llx_element_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_element_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_element_element`
--

DROP TABLE IF EXISTS `llx_element_element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_element_element` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_source` int(11) NOT NULL,
  `sourcetype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fk_target` int(11) NOT NULL,
  `targettype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_element_element_idx1` (`fk_source`,`sourcetype`,`fk_target`,`targettype`),
  KEY `idx_element_element_fk_target` (`fk_target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_element_element`
--

LOCK TABLES `llx_element_element` WRITE;
/*!40000 ALTER TABLE `llx_element_element` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_element_element` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_element_lock`
--

DROP TABLE IF EXISTS `llx_element_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_element_lock` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_element` int(11) NOT NULL,
  `elementtype` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `datel` datetime DEFAULT NULL,
  `datem` datetime DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_element_lock`
--

LOCK TABLES `llx_element_lock` WRITE;
/*!40000 ALTER TABLE `llx_element_lock` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_element_lock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_entrepot`
--

DROP TABLE IF EXISTS `llx_entrepot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_entrepot` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_unicode_ci,
  `lieu` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cp` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_departement` int(11) DEFAULT NULL,
  `fk_pays` int(11) DEFAULT '0',
  `statut` tinyint(4) DEFAULT '1',
  `valo_pmp` float(12,4) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_entrepot_label` (`label`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_entrepot`
--

LOCK TABLES `llx_entrepot` WRITE;
/*!40000 ALTER TABLE `llx_entrepot` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_entrepot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_events`
--

DROP TABLE IF EXISTS `llx_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_events` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `dateevent` datetime DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_object` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_events_dateevent` (`dateevent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_events`
--

LOCK TABLES `llx_events` WRITE;
/*!40000 ALTER TABLE `llx_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_expedition`
--

DROP TABLE IF EXISTS `llx_expedition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_expedition` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ref` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_soc` int(11) NOT NULL,
  `ref_ext` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_int` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_customer` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `date_expedition` datetime DEFAULT NULL,
  `date_delivery` datetime DEFAULT NULL,
  `fk_address` int(11) DEFAULT NULL,
  `fk_expedition_methode` int(11) DEFAULT NULL,
  `tracking_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_statut` smallint(6) DEFAULT '0',
  `height` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `size_units` int(11) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `weight_units` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `model_pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_expedition_uk_ref` (`ref`,`entity`),
  KEY `idx_expedition_fk_soc` (`fk_soc`),
  KEY `idx_expedition_fk_user_author` (`fk_user_author`),
  KEY `idx_expedition_fk_user_valid` (`fk_user_valid`),
  KEY `idx_expedition_fk_expedition_methode` (`fk_expedition_methode`),
  CONSTRAINT `fk_expedition_fk_expedition_methode` FOREIGN KEY (`fk_expedition_methode`) REFERENCES `llx_c_shipment_mode` (`rowid`),
  CONSTRAINT `fk_expedition_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  CONSTRAINT `fk_expedition_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_expedition_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_expedition`
--

LOCK TABLES `llx_expedition` WRITE;
/*!40000 ALTER TABLE `llx_expedition` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_expedition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_expeditiondet`
--

DROP TABLE IF EXISTS `llx_expeditiondet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_expeditiondet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_expedition` int(11) NOT NULL,
  `fk_origin_line` int(11) DEFAULT NULL,
  `fk_entrepot` int(11) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  KEY `idx_expeditiondet_fk_expedition` (`fk_expedition`),
  CONSTRAINT `fk_expeditiondet_fk_expedition` FOREIGN KEY (`fk_expedition`) REFERENCES `llx_expedition` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_expeditiondet`
--

LOCK TABLES `llx_expeditiondet` WRITE;
/*!40000 ALTER TABLE `llx_expeditiondet` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_expeditiondet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_export_compta`
--

DROP TABLE IF EXISTS `llx_export_compta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_export_compta` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `date_export` datetime NOT NULL,
  `fk_user` int(11) NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_export_compta`
--

LOCK TABLES `llx_export_compta` WRITE;
/*!40000 ALTER TABLE `llx_export_compta` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_export_compta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_export_model`
--

DROP TABLE IF EXISTS `llx_export_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_export_model` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL DEFAULT '0',
  `label` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `field` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_export_model` (`label`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_export_model`
--

LOCK TABLES `llx_export_model` WRITE;
/*!40000 ALTER TABLE `llx_export_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_export_model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_extrafields`
--

DROP TABLE IF EXISTS `llx_extrafields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `elementtype` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'member',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int(11) DEFAULT '0',
  `pos` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_extrafields_name` (`name`,`entity`,`elementtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_extrafields`
--

LOCK TABLES `llx_extrafields` WRITE;
/*!40000 ALTER TABLE `llx_extrafields` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_extrafields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_facture`
--

DROP TABLE IF EXISTS `llx_facture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_facture` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `facnumber` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_int` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_client` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '0',
  `increment` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_soc` int(11) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `datef` date DEFAULT NULL,
  `date_valid` date DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `paye` smallint(6) NOT NULL DEFAULT '0',
  `amount` double(24,8) NOT NULL DEFAULT '0.00000000',
  `remise_percent` double DEFAULT '0',
  `remise_absolue` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `close_code` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `close_note` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_facture_source` int(11) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `fk_account` int(11) DEFAULT NULL,
  `fk_currency` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_cond_reglement` int(11) NOT NULL DEFAULT '1',
  `fk_mode_reglement` int(11) DEFAULT NULL,
  `date_lim_reglement` date DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `model_pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extraparams` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_facture_uk_facnumber` (`facnumber`,`entity`),
  KEY `idx_facture_fk_soc` (`fk_soc`),
  KEY `idx_facture_fk_user_author` (`fk_user_author`),
  KEY `idx_facture_fk_user_valid` (`fk_user_valid`),
  KEY `idx_facture_fk_facture_source` (`fk_facture_source`),
  KEY `idx_facture_fk_projet` (`fk_projet`),
  KEY `idx_facture_fk_account` (`fk_account`),
  KEY `idx_facture_fk_currency` (`fk_currency`),
  CONSTRAINT `fk_facture_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  CONSTRAINT `fk_facture_fk_facture_source` FOREIGN KEY (`fk_facture_source`) REFERENCES `llx_facture` (`rowid`),
  CONSTRAINT `fk_facture_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  CONSTRAINT `fk_facture_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_facture_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_facture`
--

LOCK TABLES `llx_facture` WRITE;
/*!40000 ALTER TABLE `llx_facture` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_facture` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_facture_fourn`
--

DROP TABLE IF EXISTS `llx_facture_fourn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_facture_fourn` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `facnumber` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` smallint(6) NOT NULL DEFAULT '0',
  `fk_soc` int(11) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `datef` date DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `libelle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paye` smallint(6) NOT NULL DEFAULT '0',
  `amount` double(24,8) NOT NULL DEFAULT '0.00000000',
  `remise` double(24,8) DEFAULT '0.00000000',
  `close_code` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `close_note` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_tva` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_facture_source` int(11) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `fk_cond_reglement` int(11) NOT NULL DEFAULT '1',
  `date_lim_reglement` date DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `model_pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extraparams` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_facture_fourn_ref` (`facnumber`,`fk_soc`,`entity`),
  KEY `idx_facture_fourn_date_lim_reglement` (`date_lim_reglement`),
  KEY `idx_facture_fourn_fk_soc` (`fk_soc`),
  KEY `idx_facture_fourn_fk_user_author` (`fk_user_author`),
  KEY `idx_facture_fourn_fk_user_valid` (`fk_user_valid`),
  KEY `idx_facture_fourn_fk_projet` (`fk_projet`),
  CONSTRAINT `fk_facture_fourn_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  CONSTRAINT `fk_facture_fourn_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  CONSTRAINT `fk_facture_fourn_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_facture_fourn_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_facture_fourn`
--

LOCK TABLES `llx_facture_fourn` WRITE;
/*!40000 ALTER TABLE `llx_facture_fourn` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_facture_fourn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_facture_fourn_det`
--

DROP TABLE IF EXISTS `llx_facture_fourn_det`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_facture_fourn_det` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_facture_fourn` int(11) NOT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `ref` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `pu_ht` double(24,8) DEFAULT NULL,
  `pu_ttc` double(24,8) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `tva_tx` double(6,3) DEFAULT NULL,
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `total_ht` double(24,8) DEFAULT NULL,
  `tva` double(24,8) DEFAULT NULL,
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT NULL,
  `product_type` int(11) DEFAULT '0',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_facture_fourn_det_fk_facture` (`fk_facture_fourn`),
  CONSTRAINT `fk_facture_fourn_det_fk_facture` FOREIGN KEY (`fk_facture_fourn`) REFERENCES `llx_facture_fourn` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_facture_fourn_det`
--

LOCK TABLES `llx_facture_fourn_det` WRITE;
/*!40000 ALTER TABLE `llx_facture_fourn_det` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_facture_fourn_det` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_facture_rec`
--

DROP TABLE IF EXISTS `llx_facture_rec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_facture_rec` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_soc` int(11) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `amount` double(24,8) NOT NULL DEFAULT '0.00000000',
  `remise` double DEFAULT '0',
  `remise_percent` double DEFAULT '0',
  `remise_absolue` double DEFAULT '0',
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `fk_cond_reglement` int(11) DEFAULT '0',
  `fk_mode_reglement` int(11) DEFAULT '0',
  `date_lim_reglement` date DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `frequency` int(11) DEFAULT NULL,
  `unit_frequency` varchar(2) COLLATE utf8_unicode_ci DEFAULT 'd',
  `date_when` datetime DEFAULT NULL,
  `date_last_gen` datetime DEFAULT NULL,
  `nb_gen_done` int(11) DEFAULT NULL,
  `nb_gen_max` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_facture_rec_uk_titre` (`titre`,`entity`),
  KEY `idx_facture_rec_fk_soc` (`fk_soc`),
  KEY `idx_facture_rec_fk_user_author` (`fk_user_author`),
  KEY `idx_facture_rec_fk_projet` (`fk_projet`),
  CONSTRAINT `fk_facture_rec_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  CONSTRAINT `fk_facture_rec_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  CONSTRAINT `fk_facture_rec_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_facture_rec`
--

LOCK TABLES `llx_facture_rec` WRITE;
/*!40000 ALTER TABLE `llx_facture_rec` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_facture_rec` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_facturedet`
--

DROP TABLE IF EXISTS `llx_facturedet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_facturedet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_facture` int(11) NOT NULL,
  `fk_parent_line` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `tva_tx` double(6,3) DEFAULT NULL,
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `fk_remise_except` int(11) DEFAULT NULL,
  `subprice` double(24,8) DEFAULT NULL,
  `price` double(24,8) DEFAULT NULL,
  `total_ht` double(24,8) DEFAULT NULL,
  `total_tva` double(24,8) DEFAULT NULL,
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT NULL,
  `product_type` int(11) DEFAULT '0',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `info_bits` int(11) DEFAULT '0',
  `fk_code_ventilation` int(11) NOT NULL DEFAULT '0',
  `fk_export_compta` int(11) NOT NULL DEFAULT '0',
  `special_code` int(10) unsigned DEFAULT '0',
  `rang` int(11) DEFAULT '0',
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_fk_remise_except` (`fk_remise_except`,`fk_facture`),
  KEY `idx_facturedet_fk_facture` (`fk_facture`),
  KEY `idx_facturedet_fk_product` (`fk_product`),
  CONSTRAINT `fk_facturedet_fk_facture` FOREIGN KEY (`fk_facture`) REFERENCES `llx_facture` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_facturedet`
--

LOCK TABLES `llx_facturedet` WRITE;
/*!40000 ALTER TABLE `llx_facturedet` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_facturedet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_facturedet_rec`
--

DROP TABLE IF EXISTS `llx_facturedet_rec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_facturedet_rec` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_facture` int(11) NOT NULL,
  `fk_parent_line` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `product_type` int(11) DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `tva_tx` double(6,3) DEFAULT '19.600',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `subprice` double(24,8) DEFAULT NULL,
  `price` double(24,8) DEFAULT NULL,
  `total_ht` double(24,8) DEFAULT NULL,
  `total_tva` double(24,8) DEFAULT NULL,
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT NULL,
  `special_code` int(10) unsigned DEFAULT '0',
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_facturedet_rec`
--

LOCK TABLES `llx_facturedet_rec` WRITE;
/*!40000 ALTER TABLE `llx_facturedet_rec` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_facturedet_rec` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_fichinter`
--

DROP TABLE IF EXISTS `llx_fichinter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_fichinter` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) NOT NULL,
  `fk_projet` int(11) DEFAULT '0',
  `fk_contrat` int(11) DEFAULT '0',
  `ref` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `datei` date DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_statut` smallint(6) DEFAULT '0',
  `duree` double DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `note_private` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `model_pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extraparams` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_fichinter_ref` (`ref`,`entity`),
  KEY `idx_fichinter_fk_soc` (`fk_soc`),
  CONSTRAINT `fk_fichinter_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_fichinter`
--

LOCK TABLES `llx_fichinter` WRITE;
/*!40000 ALTER TABLE `llx_fichinter` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_fichinter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_fichinterdet`
--

DROP TABLE IF EXISTS `llx_fichinterdet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_fichinterdet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_fichinter` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `duree` int(11) DEFAULT NULL,
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  KEY `idx_fichinterdet_fk_fichinter` (`fk_fichinter`),
  CONSTRAINT `fk_fichinterdet_fk_fichinter` FOREIGN KEY (`fk_fichinter`) REFERENCES `llx_fichinter` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_fichinterdet`
--

LOCK TABLES `llx_fichinterdet` WRITE;
/*!40000 ALTER TABLE `llx_fichinterdet` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_fichinterdet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_import_model`
--

DROP TABLE IF EXISTS `llx_import_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_import_model` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL DEFAULT '0',
  `label` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `field` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_import_model` (`label`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_import_model`
--

LOCK TABLES `llx_import_model` WRITE;
/*!40000 ALTER TABLE `llx_import_model` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_import_model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_livraison`
--

DROP TABLE IF EXISTS `llx_livraison`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_livraison` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ref` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_soc` int(11) NOT NULL,
  `ref_ext` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_int` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_customer` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `date_delivery` date DEFAULT NULL,
  `fk_address` int(11) DEFAULT NULL,
  `fk_statut` smallint(6) DEFAULT '0',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `note` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `model_pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_livraison_uk_ref` (`ref`,`entity`),
  KEY `idx_livraison_fk_soc` (`fk_soc`),
  KEY `idx_livraison_fk_user_author` (`fk_user_author`),
  KEY `idx_livraison_fk_user_valid` (`fk_user_valid`),
  CONSTRAINT `fk_livraison_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_livraison_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  CONSTRAINT `fk_livraison_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_livraison`
--

LOCK TABLES `llx_livraison` WRITE;
/*!40000 ALTER TABLE `llx_livraison` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_livraison` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_livraisondet`
--

DROP TABLE IF EXISTS `llx_livraisondet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_livraisondet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_livraison` int(11) DEFAULT NULL,
  `fk_origin_line` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `qty` double DEFAULT NULL,
  `subprice` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  KEY `idx_livraisondet_fk_expedition` (`fk_livraison`),
  CONSTRAINT `fk_livraisondet_fk_livraison` FOREIGN KEY (`fk_livraison`) REFERENCES `llx_livraison` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_livraisondet`
--

LOCK TABLES `llx_livraisondet` WRITE;
/*!40000 ALTER TABLE `llx_livraisondet` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_livraisondet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_localtax`
--

DROP TABLE IF EXISTS `llx_localtax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_localtax` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datep` date DEFAULT NULL,
  `datev` date DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `fk_bank` int(11) DEFAULT NULL,
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_localtax`
--

LOCK TABLES `llx_localtax` WRITE;
/*!40000 ALTER TABLE `llx_localtax` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_localtax` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_mailing`
--

DROP TABLE IF EXISTS `llx_mailing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_mailing` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `statut` smallint(6) DEFAULT '0',
  `titre` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `sujet` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `bgcolor` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bgimage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cible` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nbemail` int(11) DEFAULT NULL,
  `email_from` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_replyto` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_errorsto` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tag` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_creat` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `date_appro` datetime DEFAULT NULL,
  `date_envoi` datetime DEFAULT NULL,
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_user_appro` int(11) DEFAULT NULL,
  `joined_file1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `joined_file2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `joined_file3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `joined_file4` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_mailing`
--

LOCK TABLES `llx_mailing` WRITE;
/*!40000 ALTER TABLE `llx_mailing` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_mailing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_mailing_cibles`
--

DROP TABLE IF EXISTS `llx_mailing_cibles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_mailing_cibles` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_mailing` int(11) NOT NULL,
  `fk_contact` int(11) NOT NULL,
  `nom` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prenom` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(160) COLLATE utf8_unicode_ci NOT NULL,
  `other` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tag` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  `source_url` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `source_type` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_envoi` datetime DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_mailing_cibles` (`fk_mailing`,`email`),
  KEY `idx_mailing_cibles_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_mailing_cibles`
--

LOCK TABLES `llx_mailing_cibles` WRITE;
/*!40000 ALTER TABLE `llx_mailing_cibles` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_mailing_cibles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_menu`
--

DROP TABLE IF EXISTS `llx_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_menu` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `menu_handler` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `module` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `mainmenu` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `leftmenu` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_menu` int(11) NOT NULL,
  `fk_mainmenu` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_leftmenu` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `target` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `langs` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `perms` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` varchar(255) COLLATE utf8_unicode_ci DEFAULT '1',
  `usertype` int(11) NOT NULL DEFAULT '0',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `idx_menu_uk_menu` (`menu_handler`,`fk_menu`,`position`,`url`,`entity`),
  KEY `idx_menu_menuhandler_type` (`menu_handler`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_menu`
--

LOCK TABLES `llx_menu` WRITE;
/*!40000 ALTER TABLE `llx_menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_notify`
--

DROP TABLE IF EXISTS `llx_notify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_notify` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `daten` datetime DEFAULT NULL,
  `fk_action` int(11) NOT NULL,
  `fk_contact` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `objet_type` varchar(24) COLLATE utf8_unicode_ci NOT NULL,
  `objet_id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_notify`
--

LOCK TABLES `llx_notify` WRITE;
/*!40000 ALTER TABLE `llx_notify` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_notify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_notify_def`
--

DROP TABLE IF EXISTS `llx_notify_def`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_notify_def` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` date DEFAULT NULL,
  `fk_action` int(11) NOT NULL,
  `fk_soc` int(11) NOT NULL,
  `fk_contact` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `type` varchar(16) COLLATE utf8_unicode_ci DEFAULT 'email',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_notify_def`
--

LOCK TABLES `llx_notify_def` WRITE;
/*!40000 ALTER TABLE `llx_notify_def` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_notify_def` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_paiement`
--

DROP TABLE IF EXISTS `llx_paiement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_paiement` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datep` datetime DEFAULT NULL,
  `amount` double(24,8) DEFAULT '0.00000000',
  `fk_paiement` int(11) NOT NULL,
  `num_paiement` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `fk_bank` int(11) NOT NULL DEFAULT '0',
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  `fk_export_compta` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_paiement`
--

LOCK TABLES `llx_paiement` WRITE;
/*!40000 ALTER TABLE `llx_paiement` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_paiement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_paiement_facture`
--

DROP TABLE IF EXISTS `llx_paiement_facture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_paiement_facture` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_paiement` int(11) DEFAULT NULL,
  `fk_facture` int(11) DEFAULT NULL,
  `amount` double(24,8) DEFAULT '0.00000000',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_paiement_facture` (`fk_paiement`,`fk_facture`),
  KEY `idx_paiement_facture_fk_facture` (`fk_facture`),
  KEY `idx_paiement_facture_fk_paiement` (`fk_paiement`),
  CONSTRAINT `fk_paiement_facture_fk_facture` FOREIGN KEY (`fk_facture`) REFERENCES `llx_facture` (`rowid`),
  CONSTRAINT `fk_paiement_facture_fk_paiement` FOREIGN KEY (`fk_paiement`) REFERENCES `llx_paiement` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_paiement_facture`
--

LOCK TABLES `llx_paiement_facture` WRITE;
/*!40000 ALTER TABLE `llx_paiement_facture` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_paiement_facture` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_paiementcharge`
--

DROP TABLE IF EXISTS `llx_paiementcharge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_paiementcharge` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_charge` int(11) DEFAULT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datep` datetime DEFAULT NULL,
  `amount` double DEFAULT '0',
  `fk_typepaiement` int(11) NOT NULL,
  `num_paiement` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `fk_bank` int(11) NOT NULL,
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_paiementcharge`
--

LOCK TABLES `llx_paiementcharge` WRITE;
/*!40000 ALTER TABLE `llx_paiementcharge` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_paiementcharge` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_paiementfourn`
--

DROP TABLE IF EXISTS `llx_paiementfourn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_paiementfourn` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `datep` datetime DEFAULT NULL,
  `amount` double DEFAULT '0',
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_paiement` int(11) NOT NULL,
  `num_paiement` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `fk_bank` int(11) NOT NULL,
  `statut` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_paiementfourn`
--

LOCK TABLES `llx_paiementfourn` WRITE;
/*!40000 ALTER TABLE `llx_paiementfourn` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_paiementfourn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_paiementfourn_facturefourn`
--

DROP TABLE IF EXISTS `llx_paiementfourn_facturefourn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_paiementfourn_facturefourn` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_paiementfourn` int(11) DEFAULT NULL,
  `fk_facturefourn` int(11) DEFAULT NULL,
  `amount` double(24,8) DEFAULT '0.00000000',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_paiementfourn_facturefourn` (`fk_paiementfourn`,`fk_facturefourn`),
  KEY `idx_paiementfourn_facturefourn_fk_facture` (`fk_facturefourn`),
  KEY `idx_paiementfourn_facturefourn_fk_paiement` (`fk_paiementfourn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_paiementfourn_facturefourn`
--

LOCK TABLES `llx_paiementfourn_facturefourn` WRITE;
/*!40000 ALTER TABLE `llx_paiementfourn_facturefourn` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_paiementfourn_facturefourn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_prelevement_bons`
--

DROP TABLE IF EXISTS `llx_prelevement_bons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_prelevement_bons` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datec` datetime DEFAULT NULL,
  `amount` double DEFAULT '0',
  `statut` smallint(6) DEFAULT '0',
  `credite` smallint(6) DEFAULT '0',
  `note` text COLLATE utf8_unicode_ci,
  `date_trans` datetime DEFAULT NULL,
  `method_trans` smallint(6) DEFAULT NULL,
  `fk_user_trans` int(11) DEFAULT NULL,
  `date_credit` datetime DEFAULT NULL,
  `fk_user_credit` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_prelevement_bons_ref` (`ref`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_prelevement_bons`
--

LOCK TABLES `llx_prelevement_bons` WRITE;
/*!40000 ALTER TABLE `llx_prelevement_bons` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_prelevement_bons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_prelevement_facture`
--

DROP TABLE IF EXISTS `llx_prelevement_facture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_prelevement_facture` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_facture` int(11) NOT NULL,
  `fk_prelevement_lignes` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_prelevement_facture_fk_prelevement_lignes` (`fk_prelevement_lignes`),
  CONSTRAINT `fk_prelevement_facture_fk_prelevement_lignes` FOREIGN KEY (`fk_prelevement_lignes`) REFERENCES `llx_prelevement_lignes` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_prelevement_facture`
--

LOCK TABLES `llx_prelevement_facture` WRITE;
/*!40000 ALTER TABLE `llx_prelevement_facture` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_prelevement_facture` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_prelevement_facture_demande`
--

DROP TABLE IF EXISTS `llx_prelevement_facture_demande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_prelevement_facture_demande` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_facture` int(11) NOT NULL,
  `amount` double NOT NULL,
  `date_demande` datetime NOT NULL,
  `traite` smallint(6) DEFAULT '0',
  `date_traite` datetime DEFAULT NULL,
  `fk_prelevement_bons` int(11) DEFAULT NULL,
  `fk_user_demande` int(11) NOT NULL,
  `code_banque` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_guichet` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cle_rib` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_prelevement_facture_demande`
--

LOCK TABLES `llx_prelevement_facture_demande` WRITE;
/*!40000 ALTER TABLE `llx_prelevement_facture_demande` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_prelevement_facture_demande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_prelevement_lignes`
--

DROP TABLE IF EXISTS `llx_prelevement_lignes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_prelevement_lignes` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_prelevement_bons` int(11) DEFAULT NULL,
  `fk_soc` int(11) NOT NULL,
  `statut` smallint(6) DEFAULT '0',
  `client_nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double DEFAULT '0',
  `code_banque` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_guichet` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cle_rib` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`),
  KEY `idx_prelevement_lignes_fk_prelevement_bons` (`fk_prelevement_bons`),
  CONSTRAINT `fk_prelevement_lignes_fk_prelevement_bons` FOREIGN KEY (`fk_prelevement_bons`) REFERENCES `llx_prelevement_bons` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_prelevement_lignes`
--

LOCK TABLES `llx_prelevement_lignes` WRITE;
/*!40000 ALTER TABLE `llx_prelevement_lignes` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_prelevement_lignes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_prelevement_rejet`
--

DROP TABLE IF EXISTS `llx_prelevement_rejet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_prelevement_rejet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_prelevement_lignes` int(11) DEFAULT NULL,
  `date_rejet` datetime DEFAULT NULL,
  `motif` int(11) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `fk_user_creation` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `afacturer` tinyint(4) DEFAULT '0',
  `fk_facture` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_prelevement_rejet`
--

LOCK TABLES `llx_prelevement_rejet` WRITE;
/*!40000 ALTER TABLE `llx_prelevement_rejet` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_prelevement_rejet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_product`
--

DROP TABLE IF EXISTS `llx_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_product` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `virtual` tinyint(4) NOT NULL DEFAULT '0',
  `fk_parent` int(11) DEFAULT '0',
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `note` text COLLATE utf8_unicode_ci,
  `customcode` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_country` int(11) DEFAULT NULL,
  `price` double(24,8) DEFAULT '0.00000000',
  `price_ttc` double(24,8) DEFAULT '0.00000000',
  `price_min` double(24,8) DEFAULT '0.00000000',
  `price_min_ttc` double(24,8) DEFAULT '0.00000000',
  `price_base_type` varchar(3) COLLATE utf8_unicode_ci DEFAULT 'HT',
  `tva_tx` double(6,3) DEFAULT NULL,
  `recuperableonly` int(11) NOT NULL DEFAULT '0',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `fk_user_author` int(11) DEFAULT NULL,
  `tosell` tinyint(4) DEFAULT '1',
  `tobuy` tinyint(4) DEFAULT '1',
  `fk_product_type` int(11) DEFAULT '0',
  `duration` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `seuil_stock_alerte` int(11) DEFAULT '0',
  `barcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_barcode_type` int(11) DEFAULT '0',
  `accountancy_code_sell` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `accountancy_code_buy` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partnumber` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `weight_units` tinyint(4) DEFAULT NULL,
  `length` float DEFAULT NULL,
  `length_units` tinyint(4) DEFAULT NULL,
  `surface` float DEFAULT NULL,
  `surface_units` tinyint(4) DEFAULT NULL,
  `volume` float DEFAULT NULL,
  `volume_units` tinyint(4) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `pmp` double(24,8) NOT NULL DEFAULT '0.00000000',
  `canvas` varchar(32) COLLATE utf8_unicode_ci DEFAULT 'default@product',
  `finished` tinyint(4) DEFAULT NULL,
  `hidden` tinyint(4) DEFAULT '0',
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_ref` (`ref`,`entity`),
  KEY `idx_product_label` (`label`),
  KEY `idx_product_barcode` (`barcode`),
  KEY `idx_product_import_key` (`import_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_product`
--

LOCK TABLES `llx_product` WRITE;
/*!40000 ALTER TABLE `llx_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_product_association`
--

DROP TABLE IF EXISTS `llx_product_association`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_product_association` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product_pere` int(11) NOT NULL DEFAULT '0',
  `fk_product_fils` int(11) NOT NULL DEFAULT '0',
  `qty` double DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_association` (`fk_product_pere`,`fk_product_fils`),
  KEY `idx_product_association_fils` (`fk_product_fils`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_product_association`
--

LOCK TABLES `llx_product_association` WRITE;
/*!40000 ALTER TABLE `llx_product_association` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_product_association` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_product_ca`
--

DROP TABLE IF EXISTS `llx_product_ca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_product_ca` (
  `fk_product` int(11) DEFAULT NULL,
  `date_calcul` datetime DEFAULT NULL,
  `year` smallint(5) unsigned DEFAULT NULL,
  `ca_genere` float DEFAULT NULL,
  UNIQUE KEY `fk_product` (`fk_product`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_product_ca`
--

LOCK TABLES `llx_product_ca` WRITE;
/*!40000 ALTER TABLE `llx_product_ca` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_product_ca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_product_extrafields`
--

DROP TABLE IF EXISTS `llx_product_extrafields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_product_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_object` int(11) NOT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_product_extrafields` (`fk_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_product_extrafields`
--

LOCK TABLES `llx_product_extrafields` WRITE;
/*!40000 ALTER TABLE `llx_product_extrafields` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_product_extrafields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_product_fournisseur_price`
--

DROP TABLE IF EXISTS `llx_product_fournisseur_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_product_fournisseur_price` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_product` int(11) DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `ref_fourn` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_availability` int(11) DEFAULT NULL,
  `price` double(24,8) DEFAULT '0.00000000',
  `quantity` double DEFAULT NULL,
  `unitprice` double(24,8) DEFAULT '0.00000000',
  `tva_tx` double(6,3) NOT NULL,
  `fk_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_fournisseur_price_ref` (`ref_fourn`,`fk_soc`,`quantity`,`entity`),
  KEY `idx_product_fournisseur_price_fk_user` (`fk_user`),
  KEY `idx_product_fourn_price_fk_product` (`fk_product`,`entity`),
  KEY `idx_product_fourn_price_fk_soc` (`fk_soc`,`entity`),
  CONSTRAINT `fk_product_fournisseur_price_fk_product` FOREIGN KEY (`fk_product`) REFERENCES `llx_product` (`rowid`),
  CONSTRAINT `fk_product_fournisseur_price_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_product_fournisseur_price`
--

LOCK TABLES `llx_product_fournisseur_price` WRITE;
/*!40000 ALTER TABLE `llx_product_fournisseur_price` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_product_fournisseur_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_product_fournisseur_price_log`
--

DROP TABLE IF EXISTS `llx_product_fournisseur_price_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_product_fournisseur_price_log` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `fk_product_fournisseur` int(11) NOT NULL,
  `price` double(24,8) DEFAULT '0.00000000',
  `quantity` double DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_product_fournisseur_price_log`
--

LOCK TABLES `llx_product_fournisseur_price_log` WRITE;
/*!40000 ALTER TABLE `llx_product_fournisseur_price_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_product_fournisseur_price_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_product_lang`
--

DROP TABLE IF EXISTS `llx_product_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_product_lang` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product` int(11) NOT NULL DEFAULT '0',
  `lang` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_lang` (`fk_product`,`lang`),
  CONSTRAINT `fk_product_lang_fk_product` FOREIGN KEY (`fk_product`) REFERENCES `llx_product` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_product_lang`
--

LOCK TABLES `llx_product_lang` WRITE;
/*!40000 ALTER TABLE `llx_product_lang` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_product_lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_product_price`
--

DROP TABLE IF EXISTS `llx_product_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_product_price` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_product` int(11) NOT NULL,
  `date_price` datetime NOT NULL,
  `price_level` smallint(6) DEFAULT '1',
  `price` double(24,8) DEFAULT NULL,
  `price_ttc` double(24,8) DEFAULT NULL,
  `price_min` double(24,8) DEFAULT NULL,
  `price_min_ttc` double(24,8) DEFAULT NULL,
  `price_base_type` varchar(3) COLLATE utf8_unicode_ci DEFAULT 'HT',
  `tva_tx` double(6,3) NOT NULL,
  `recuperableonly` int(11) NOT NULL DEFAULT '0',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `fk_user_author` int(11) DEFAULT NULL,
  `tosell` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_product_price`
--

LOCK TABLES `llx_product_price` WRITE;
/*!40000 ALTER TABLE `llx_product_price` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_product_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_product_stock`
--

DROP TABLE IF EXISTS `llx_product_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_product_stock` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_product` int(11) NOT NULL,
  `fk_entrepot` int(11) NOT NULL,
  `reel` double DEFAULT NULL,
  `pmp` double(24,8) NOT NULL DEFAULT '0.00000000',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_stock` (`fk_product`,`fk_entrepot`),
  KEY `idx_product_stock_fk_product` (`fk_product`),
  KEY `idx_product_stock_fk_entrepot` (`fk_entrepot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_product_stock`
--

LOCK TABLES `llx_product_stock` WRITE;
/*!40000 ALTER TABLE `llx_product_stock` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_product_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_projet`
--

DROP TABLE IF EXISTS `llx_projet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_projet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) DEFAULT NULL,
  `datec` date DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dateo` date DEFAULT NULL,
  `datee` date DEFAULT NULL,
  `ref` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `fk_user_creat` int(11) NOT NULL,
  `public` int(11) DEFAULT NULL,
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `note_private` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `model_pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_projet_ref` (`ref`,`entity`),
  KEY `idx_projet_fk_soc` (`fk_soc`),
  CONSTRAINT `fk_projet_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_projet`
--

LOCK TABLES `llx_projet` WRITE;
/*!40000 ALTER TABLE `llx_projet` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_projet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_projet_task`
--

DROP TABLE IF EXISTS `llx_projet_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_projet_task` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_projet` int(11) NOT NULL,
  `fk_task_parent` int(11) NOT NULL DEFAULT '0',
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dateo` datetime DEFAULT NULL,
  `datee` datetime DEFAULT NULL,
  `datev` datetime DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `duration_effective` double NOT NULL DEFAULT '0',
  `progress` int(11) DEFAULT '0',
  `priority` int(11) DEFAULT '0',
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `note_private` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  KEY `idx_projet_task_fk_projet` (`fk_projet`),
  KEY `idx_projet_task_fk_user_creat` (`fk_user_creat`),
  KEY `idx_projet_task_fk_user_valid` (`fk_user_valid`),
  CONSTRAINT `fk_projet_task_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_projet_task_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  CONSTRAINT `fk_projet_task_fk_user_creat` FOREIGN KEY (`fk_user_creat`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_projet_task`
--

LOCK TABLES `llx_projet_task` WRITE;
/*!40000 ALTER TABLE `llx_projet_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_projet_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_projet_task_time`
--

DROP TABLE IF EXISTS `llx_projet_task_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_projet_task_time` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_task` int(11) NOT NULL,
  `task_date` date DEFAULT NULL,
  `task_duration` double DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_projet_task_time`
--

LOCK TABLES `llx_projet_task_time` WRITE;
/*!40000 ALTER TABLE `llx_projet_task_time` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_projet_task_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_propal`
--

DROP TABLE IF EXISTS `llx_propal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_propal` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_int` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_client` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `datep` date DEFAULT NULL,
  `fin_validite` datetime DEFAULT NULL,
  `date_valid` datetime DEFAULT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `fk_user_valid` int(11) DEFAULT NULL,
  `fk_user_cloture` int(11) DEFAULT NULL,
  `fk_statut` smallint(6) NOT NULL DEFAULT '0',
  `price` double DEFAULT '0',
  `remise_percent` double DEFAULT '0',
  `remise_absolue` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `tva` double(24,8) DEFAULT '0.00000000',
  `localtax1` double(24,8) DEFAULT '0.00000000',
  `localtax2` double(24,8) DEFAULT '0.00000000',
  `total` double(24,8) DEFAULT '0.00000000',
  `fk_account` int(11) DEFAULT NULL,
  `fk_currency` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_cond_reglement` int(11) DEFAULT NULL,
  `fk_mode_reglement` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `note_public` text COLLATE utf8_unicode_ci,
  `model_pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_livraison` date DEFAULT NULL,
  `fk_availability` int(11) DEFAULT NULL,
  `fk_demand_reason` int(11) DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extraparams` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_adresse_livraison` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_propal_ref` (`ref`,`entity`),
  KEY `idx_propal_fk_soc` (`fk_soc`),
  KEY `idx_propal_fk_user_author` (`fk_user_author`),
  KEY `idx_propal_fk_user_valid` (`fk_user_valid`),
  KEY `idx_propal_fk_user_cloture` (`fk_user_cloture`),
  KEY `idx_propal_fk_projet` (`fk_projet`),
  KEY `idx_propal_fk_account` (`fk_account`),
  KEY `idx_propal_fk_currency` (`fk_currency`),
  CONSTRAINT `fk_propal_fk_projet` FOREIGN KEY (`fk_projet`) REFERENCES `llx_projet` (`rowid`),
  CONSTRAINT `fk_propal_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  CONSTRAINT `fk_propal_fk_user_author` FOREIGN KEY (`fk_user_author`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_propal_fk_user_cloture` FOREIGN KEY (`fk_user_cloture`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_propal_fk_user_valid` FOREIGN KEY (`fk_user_valid`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_propal`
--

LOCK TABLES `llx_propal` WRITE;
/*!40000 ALTER TABLE `llx_propal` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_propal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_propaldet`
--

DROP TABLE IF EXISTS `llx_propaldet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_propaldet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_propal` int(11) NOT NULL,
  `fk_parent_line` int(11) DEFAULT NULL,
  `fk_product` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `fk_remise_except` int(11) DEFAULT NULL,
  `tva_tx` double(6,3) DEFAULT '0.000',
  `localtax1_tx` double(6,3) DEFAULT '0.000',
  `localtax2_tx` double(6,3) DEFAULT '0.000',
  `qty` double DEFAULT NULL,
  `remise_percent` double DEFAULT '0',
  `remise` double DEFAULT '0',
  `price` double DEFAULT NULL,
  `subprice` double(24,8) DEFAULT '0.00000000',
  `total_ht` double(24,8) DEFAULT '0.00000000',
  `total_tva` double(24,8) DEFAULT '0.00000000',
  `total_localtax1` double(24,8) DEFAULT '0.00000000',
  `total_localtax2` double(24,8) DEFAULT '0.00000000',
  `total_ttc` double(24,8) DEFAULT '0.00000000',
  `product_type` int(11) DEFAULT '0',
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `info_bits` int(11) DEFAULT '0',
  `pa_ht` double(24,8) DEFAULT '0.00000000',
  `marge_tx` double(6,3) DEFAULT '0.000',
  `marque_tx` double(6,3) DEFAULT '0.000',
  `special_code` int(11) DEFAULT '0',
  `rang` int(11) DEFAULT '0',
  PRIMARY KEY (`rowid`),
  KEY `idx_propaldet_fk_propal` (`fk_propal`),
  KEY `idx_propaldet_fk_product` (`fk_product`),
  CONSTRAINT `fk_propaldet_fk_propal` FOREIGN KEY (`fk_propal`) REFERENCES `llx_propal` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_propaldet`
--

LOCK TABLES `llx_propaldet` WRITE;
/*!40000 ALTER TABLE `llx_propaldet` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_propaldet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_rights_def`
--

DROP TABLE IF EXISTS `llx_rights_def`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_rights_def` (
  `id` int(11) NOT NULL DEFAULT '0',
  `libelle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `perms` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subperms` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bydefault` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_rights_def`
--

LOCK TABLES `llx_rights_def` WRITE;
/*!40000 ALTER TABLE `llx_rights_def` DISABLE KEYS */;
INSERT INTO `llx_rights_def` VALUES (251,'Consulter les autres utilisateurs','user',1,'user','lire','r',0),(252,'Consulter les permissions des autres utilisateurs','user',1,'user_advance','readperms','r',0),(253,'Creer/modifier utilisateurs internes et externes','user',1,'user','creer','w',0),(254,'Creer/modifier utilisateurs externes seulement','user',1,'user_advance','write','w',0),(255,'Modifier le mot de passe des autres utilisateurs','user',1,'user','password','w',0),(256,'Supprimer ou desactiver les autres utilisateurs','user',1,'user','supprimer','d',0),(341,'Consulter ses propres permissions','user',1,'self_advance','readperms','r',1),(342,'Creer/modifier ses propres infos utilisateur','user',1,'self','creer','w',1),(343,'Modifier son propre mot de passe','user',1,'self','password','w',1),(344,'Modifier ses propres permissions','user',1,'self_advance','writeperms','w',1),(351,'Consulter les groupes','user',1,'group_advance','read','r',0),(352,'Consulter les permissions des groupes','user',1,'group_advance','readperms','r',0),(353,'Creer/modifier les groupes et leurs permissions','user',1,'group_advance','write','w',0),(354,'Supprimer ou desactiver les groupes','user',1,'group_advance','delete','d',0),(358,'Exporter les utilisateurs','user',1,'user','export','r',0);
/*!40000 ALTER TABLE `llx_rights_def` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_societe`
--

DROP TABLE IF EXISTS `llx_societe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_societe` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_int` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `statut` tinyint(4) DEFAULT '0',
  `parent` int(11) DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `datea` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `code_client` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_fournisseur` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_compta` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_compta_fournisseur` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cp` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_departement` int(11) DEFAULT '0',
  `fk_pays` int(11) DEFAULT '0',
  `tel` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_secteur` int(11) DEFAULT '0',
  `fk_effectif` int(11) DEFAULT '0',
  `fk_typent` int(11) DEFAULT '0',
  `fk_forme_juridique` int(11) DEFAULT '0',
  `fk_currency` int(11) DEFAULT '0',
  `siren` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siret` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ape` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idprof4` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idprof5` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tva_intra` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `capital` double DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `fk_stcomm` int(11) NOT NULL DEFAULT '0',
  `note` text COLLATE utf8_unicode_ci,
  `services` tinyint(4) DEFAULT '0',
  `prefix_comm` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client` tinyint(4) DEFAULT '0',
  `fournisseur` tinyint(4) DEFAULT '0',
  `supplier_account` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_prospectlevel` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_bad` tinyint(4) DEFAULT '0',
  `customer_rate` double DEFAULT '0',
  `supplier_rate` double DEFAULT '0',
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  `remise_client` double DEFAULT '0',
  `mode_reglement` tinyint(4) DEFAULT NULL,
  `cond_reglement` tinyint(4) DEFAULT NULL,
  `tva_assuj` tinyint(4) DEFAULT '1',
  `localtax1_assuj` tinyint(4) DEFAULT '0',
  `localtax2_assuj` tinyint(4) DEFAULT '0',
  `barcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_barcode_type` int(11) DEFAULT '0',
  `price_level` int(11) DEFAULT NULL,
  `default_lang` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `canvas` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_societe_prefix_comm` (`prefix_comm`,`entity`),
  UNIQUE KEY `uk_societe_code_client` (`code_client`,`entity`),
  KEY `idx_societe_user_creat` (`fk_user_creat`),
  KEY `idx_societe_user_modif` (`fk_user_modif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_societe`
--

LOCK TABLES `llx_societe` WRITE;
/*!40000 ALTER TABLE `llx_societe` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_societe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_societe_address`
--

DROP TABLE IF EXISTS `llx_societe_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_societe_address` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_soc` int(11) DEFAULT '0',
  `name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cp` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_pays` int(11) DEFAULT '0',
  `tel` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_societe_address`
--

LOCK TABLES `llx_societe_address` WRITE;
/*!40000 ALTER TABLE `llx_societe_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_societe_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_societe_commerciaux`
--

DROP TABLE IF EXISTS `llx_societe_commerciaux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_societe_commerciaux` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_societe_commerciaux` (`fk_soc`,`fk_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_societe_commerciaux`
--

LOCK TABLES `llx_societe_commerciaux` WRITE;
/*!40000 ALTER TABLE `llx_societe_commerciaux` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_societe_commerciaux` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_societe_extrafields`
--

DROP TABLE IF EXISTS `llx_societe_extrafields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_societe_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_object` int(11) NOT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_societe_extrafields` (`fk_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_societe_extrafields`
--

LOCK TABLES `llx_societe_extrafields` WRITE;
/*!40000 ALTER TABLE `llx_societe_extrafields` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_societe_extrafields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_societe_log`
--

DROP TABLE IF EXISTS `llx_societe_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_societe_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datel` datetime DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_statut` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `author` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_societe_log`
--

LOCK TABLES `llx_societe_log` WRITE;
/*!40000 ALTER TABLE `llx_societe_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_societe_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_societe_prices`
--

DROP TABLE IF EXISTS `llx_societe_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_societe_prices` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) DEFAULT '0',
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `price_level` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_societe_prices`
--

LOCK TABLES `llx_societe_prices` WRITE;
/*!40000 ALTER TABLE `llx_societe_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_societe_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_societe_remise`
--

DROP TABLE IF EXISTS `llx_societe_remise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_societe_remise` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datec` datetime DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `remise_client` double(6,3) NOT NULL DEFAULT '0.000',
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_societe_remise`
--

LOCK TABLES `llx_societe_remise` WRITE;
/*!40000 ALTER TABLE `llx_societe_remise` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_societe_remise` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_societe_remise_except`
--

DROP TABLE IF EXISTS `llx_societe_remise_except`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_societe_remise_except` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `amount_ht` double(24,8) NOT NULL,
  `amount_tva` double(24,8) NOT NULL DEFAULT '0.00000000',
  `amount_ttc` double(24,8) NOT NULL DEFAULT '0.00000000',
  `tva_tx` double(6,3) NOT NULL DEFAULT '0.000',
  `fk_user` int(11) NOT NULL,
  `fk_facture_line` int(11) DEFAULT NULL,
  `fk_facture` int(11) DEFAULT NULL,
  `fk_facture_source` int(11) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_societe_remise_except_fk_user` (`fk_user`),
  KEY `idx_societe_remise_except_fk_soc` (`fk_soc`),
  KEY `idx_societe_remise_except_fk_facture_line` (`fk_facture_line`),
  KEY `idx_societe_remise_except_fk_facture` (`fk_facture`),
  KEY `idx_societe_remise_except_fk_facture_source` (`fk_facture_source`),
  CONSTRAINT `fk_societe_remise_fk_facture_source` FOREIGN KEY (`fk_facture_source`) REFERENCES `llx_facture` (`rowid`),
  CONSTRAINT `fk_societe_remise_fk_facture` FOREIGN KEY (`fk_facture`) REFERENCES `llx_facture` (`rowid`),
  CONSTRAINT `fk_societe_remise_fk_facture_line` FOREIGN KEY (`fk_facture_line`) REFERENCES `llx_facturedet` (`rowid`),
  CONSTRAINT `fk_societe_remise_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`),
  CONSTRAINT `fk_societe_remise_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_societe_remise_except`
--

LOCK TABLES `llx_societe_remise_except` WRITE;
/*!40000 ALTER TABLE `llx_societe_remise_except` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_societe_remise_except` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_societe_rib`
--

DROP TABLE IF EXISTS `llx_societe_rib`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_societe_rib` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_soc` int(11) NOT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bank` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_banque` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_guichet` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cle_rib` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bic` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iban_prefix` varchar(34) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domiciliation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `proprio` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse_proprio` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_societe_rib`
--

LOCK TABLES `llx_societe_rib` WRITE;
/*!40000 ALTER TABLE `llx_societe_rib` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_societe_rib` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_socpeople`
--

DROP TABLE IF EXISTS `llx_socpeople`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_socpeople` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_soc` int(11) DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `civilite` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cp` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_departement` int(11) DEFAULT NULL,
  `fk_pays` int(11) DEFAULT '0',
  `birthday` date DEFAULT NULL,
  `poste` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_perso` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_mobile` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jabberid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `priv` smallint(6) NOT NULL DEFAULT '0',
  `fk_user_creat` int(11) DEFAULT '0',
  `fk_user_modif` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `default_lang` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `canvas` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_socpeople_fk_soc` (`fk_soc`),
  KEY `idx_socpeople_fk_user_creat` (`fk_user_creat`),
  CONSTRAINT `fk_socpeople_user_creat_user_rowid` FOREIGN KEY (`fk_user_creat`) REFERENCES `llx_user` (`rowid`),
  CONSTRAINT `fk_socpeople_fk_soc` FOREIGN KEY (`fk_soc`) REFERENCES `llx_societe` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_socpeople`
--

LOCK TABLES `llx_socpeople` WRITE;
/*!40000 ALTER TABLE `llx_socpeople` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_socpeople` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_stock_mouvement`
--

DROP TABLE IF EXISTS `llx_stock_mouvement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_stock_mouvement` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datem` datetime DEFAULT NULL,
  `fk_product` int(11) NOT NULL,
  `fk_entrepot` int(11) NOT NULL,
  `value` int(11) DEFAULT NULL,
  `price` float(13,4) DEFAULT '0.0000',
  `type_mouvement` smallint(6) DEFAULT NULL,
  `fk_user_author` int(11) DEFAULT NULL,
  `label` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx_stock_mouvement_fk_product` (`fk_product`),
  KEY `idx_stock_mouvement_fk_entrepot` (`fk_entrepot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_stock_mouvement`
--

LOCK TABLES `llx_stock_mouvement` WRITE;
/*!40000 ALTER TABLE `llx_stock_mouvement` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_stock_mouvement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_texts`
--

DROP TABLE IF EXISTS `llx_texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_texts` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `typemodele` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sortorder` smallint(6) DEFAULT NULL,
  `private` smallint(6) NOT NULL DEFAULT '0',
  `fk_user` int(11) DEFAULT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `filename` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_texts`
--

LOCK TABLES `llx_texts` WRITE;
/*!40000 ALTER TABLE `llx_texts` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_texts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_tva`
--

DROP TABLE IF EXISTS `llx_tva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_tva` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datep` date DEFAULT NULL,
  `datev` date DEFAULT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `note` text COLLATE utf8_unicode_ci,
  `fk_bank` int(11) DEFAULT NULL,
  `fk_user_creat` int(11) DEFAULT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_tva`
--

LOCK TABLES `llx_tva` WRITE;
/*!40000 ALTER TABLE `llx_tva` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_tva` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_user`
--

DROP TABLE IF EXISTS `llx_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_user` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `ref_ext` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ref_int` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `login` varchar(24) COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pass_crypted` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pass_temp` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `civilite` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `office_phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `office_fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signature` text COLLATE utf8_unicode_ci,
  `admin` smallint(6) DEFAULT '0',
  `webcal_login` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phenix_login` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phenix_pass` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module_comm` smallint(6) DEFAULT '1',
  `module_compta` smallint(6) DEFAULT '1',
  `fk_societe` int(11) DEFAULT NULL,
  `fk_socpeople` int(11) DEFAULT NULL,
  `fk_member` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `datelastlogin` datetime DEFAULT NULL,
  `datepreviouslogin` datetime DEFAULT NULL,
  `egroupware_id` int(11) DEFAULT NULL,
  `ldap_sid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `openid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `statut` tinyint(4) DEFAULT '1',
  `photo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lang` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_user_login` (`login`,`entity`),
  UNIQUE KEY `uk_user_fk_socpeople` (`fk_socpeople`),
  UNIQUE KEY `uk_user_fk_member` (`fk_member`),
  KEY `uk_user_fk_societe` (`fk_societe`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_user`
--

LOCK TABLES `llx_user` WRITE;
/*!40000 ALTER TABLE `llx_user` DISABLE KEYS */;
INSERT INTO `llx_user` VALUES (1,0,NULL,NULL,'2012-10-11 14:11:12','2012-10-11 14:11:12','admin','q1w2e3r4','c62d929e7b7e7b6165923a5dfc60cb56',NULL,NULL,'SuperAdmin','','','','','','',1,'','','',1,1,NULL,NULL,NULL,'','2013-06-09 00:03:08','2012-10-11 14:13:22',NULL,'',NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `llx_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_user_alert`
--

DROP TABLE IF EXISTS `llx_user_alert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_user_alert` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL,
  `fk_contact` int(11) DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_user_alert`
--

LOCK TABLES `llx_user_alert` WRITE;
/*!40000 ALTER TABLE `llx_user_alert` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_user_alert` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_user_clicktodial`
--

DROP TABLE IF EXISTS `llx_user_clicktodial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_user_clicktodial` (
  `fk_user` int(11) NOT NULL,
  `login` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pass` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poste` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`fk_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_user_clicktodial`
--

LOCK TABLES `llx_user_clicktodial` WRITE;
/*!40000 ALTER TABLE `llx_user_clicktodial` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_user_clicktodial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_user_param`
--

DROP TABLE IF EXISTS `llx_user_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_user_param` (
  `fk_user` int(11) NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `param` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `uk_user_param` (`fk_user`,`param`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_user_param`
--

LOCK TABLES `llx_user_param` WRITE;
/*!40000 ALTER TABLE `llx_user_param` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_user_param` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_user_rights`
--

DROP TABLE IF EXISTS `llx_user_rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_user_rights` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_user` int(11) NOT NULL,
  `fk_id` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_user_rights` (`fk_user`,`fk_id`),
  CONSTRAINT `fk_user_rights_fk_user_user` FOREIGN KEY (`fk_user`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_user_rights`
--

LOCK TABLES `llx_user_rights` WRITE;
/*!40000 ALTER TABLE `llx_user_rights` DISABLE KEYS */;
INSERT INTO `llx_user_rights` VALUES (1,1,341),(2,1,342),(3,1,343),(4,1,344);
/*!40000 ALTER TABLE `llx_user_rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_usergroup`
--

DROP TABLE IF EXISTS `llx_usergroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_usergroup` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `datec` datetime DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_usergroup_name` (`nom`,`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_usergroup`
--

LOCK TABLES `llx_usergroup` WRITE;
/*!40000 ALTER TABLE `llx_usergroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_usergroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_usergroup_rights`
--

DROP TABLE IF EXISTS `llx_usergroup_rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_usergroup_rights` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_usergroup` int(11) NOT NULL,
  `fk_id` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `fk_usergroup` (`fk_usergroup`,`fk_id`),
  CONSTRAINT `fk_usergroup_rights_fk_usergroup` FOREIGN KEY (`fk_usergroup`) REFERENCES `llx_usergroup` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_usergroup_rights`
--

LOCK TABLES `llx_usergroup_rights` WRITE;
/*!40000 ALTER TABLE `llx_usergroup_rights` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_usergroup_rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `llx_usergroup_user`
--

DROP TABLE IF EXISTS `llx_usergroup_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `llx_usergroup_user` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_user` int(11) NOT NULL,
  `fk_usergroup` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_usergroup_user` (`entity`,`fk_user`,`fk_usergroup`),
  KEY `fk_usergroup_user_fk_user` (`fk_user`),
  KEY `fk_usergroup_user_fk_usergroup` (`fk_usergroup`),
  CONSTRAINT `fk_usergroup_user_fk_usergroup` FOREIGN KEY (`fk_usergroup`) REFERENCES `llx_usergroup` (`rowid`),
  CONSTRAINT `fk_usergroup_user_fk_user` FOREIGN KEY (`fk_user`) REFERENCES `llx_user` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llx_usergroup_user`
--

LOCK TABLES `llx_usergroup_user` WRITE;
/*!40000 ALTER TABLE `llx_usergroup_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `llx_usergroup_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-06-09  0:03:54
