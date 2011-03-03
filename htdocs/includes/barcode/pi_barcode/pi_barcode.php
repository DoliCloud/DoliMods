<?php

// ******************************************************* ©2008 Pitoo.com *****
// *****                   CODES A BARRES - Php script                     *****
// *****************************************************************************
// *****              (c) 2002 - pitoo.com - mail@pitoo.com                *****
// *****************************************************************************
// *****************************************************************************
// ***** Ce script est "FREEWARE",  il  peut  etre  librement copie et reutilise
// ***** dans vos propres pages et applications.  Il peut egalement etre modifie
// ***** ou ameliore.
// ***** CEPENDANT :  par  respect  pour l'auteur,  avant d'utiliser,  recopier, 
// ***** modifier ce code vous vous engagez a :
// ***** - conserver intact l'entete de ce fichier ( les commentaires comportant
// *****   Le nom du script,  le copyright le nom de l'auteur et son e-mail,  ce
// *****   texte et l'historique des mises a jour ).
// ***** - conserver intact la  mention  'pitoo.com'  imprimee aleatoirement sur 
// *****   l'image du code genere dans environ 2% des cas.
// ***** - envoyer un  e-mail  a l'auteur  mail(a)pitoo.com  lui indiquant votre
// *****   intention d'utiliser le resultat de son travail.
// *****************************************************************************
// ***** Toute remarque, tout commentaire, tout rapport de bug, toute recompense
// ***** sont la bienvenue : mail(a)pitoo.com
// ***** faire un don sur PayPal : paypal(a)pitoo.com
// *****************************************************************************
// *****************************************************************************
// *****                       Historique des versions                     *****
// *****************************************************************************
$last_version = "V2.9" ;
// ***** V2.9  - 25/09/2008 - pitoo.com
// *****       - Corrections pour eviter l'affichage de messages "Notice" de PHP
// ***** V2.8  - 10/07/2008 - pitoo.com
// *****       - Correction de bogue
// ***** V2.7  - 10/07/2008 - pitoo.com
// *****       - Ajout du format JPG
// ***** V2.6  - 10/07/2008 - pitoo.com
// *****       - Petites corrections de bugs d'affichage et de positionnement
// ***** V2.5  - 08/07/2008 - pitoo.com
// *****       - Réécriture/Encapsulation de toutes les fonctions dans la Classe
// *****       - Ajout d'une fonction permettant d'utiliser le script pour 
// *****         enregistrer l'image sur le disque au lieu de l'afficher
// *****       - Ajout de la possibilité de colorer le code
// *****       - Ajout de la posibilité de générer deux formats PNG ou GIF
// *****       - correction d'un bug dans le checksum (10='-') du C11
// *****	   - corrections majeures de structures de code
// ***** V2.05 - 13/06/2006 - pitoo.com
// *****       - Suppression des fonctions inutiles (V1)
// *****       - Ajout de commentaires
// ***** V2.04 - 23/01/2006 - pitoo.com
// *****       - Correction erreur codage Lettre A du code 39
// ***** V2.03 - 20/11/2004 - pitoo.com
// *****       - Supression de messages warning php
// ***** V2.02 - 07/04/2004 - pitoo.com
// *****       - Suppression du checksum et des Start/Stop sur le code KIX
// ***** V2.01 - 18/12/2003 - pitoo.com
// *****       - Correction de bug pour checksum C128 = 100 / 101 / 102
// ***** V2.00 - 19/06/2003 - pitoo.com
// *****       - Réécriture de toutes les fonctions pour génération directe de
// *****         l'image du code barre en PNG plutôt que d'utiliser une 
// *****         multitude de petits fichiers GIFs
// ***** V1.32 - 21/12/2002 - pitoo.com
// *****       - Ecriture du code 39
// *****       - Amelioration des codes UPC et 25 ()
// ***** V1.31 - 17/12/2002 - pitoo.com
// *****       - Amelioration du code 128 (ajout du Set de caracteres C)
// *****       - Amelioration du code 128 (ajout du code lisible en dessous )
// ***** V1.3  - 12/12/2002 - pitoo.com
// *****       - Ecriture du code 128 B
// ***** V1.2  - 01/08/2002 - pitoo.com
// *****       - Ecriture du code UPC / EAN
// ***** V1.0  - 01/01/2002 - pitoo.com
// *****       - Ecriture du code 25



// *****************************************************************************
// *****                        CLASSE pi_barcode                          *****
// *****************************************************************************
// ***** pi_barcode()               : Constructeur et ré-initialisation
// ***** 
// *****************************************************************************
// ***** Méthodes Publiques :
// *****************************************************************************
// ***** setSize($h, $w=0, $cz=0)   : Hauteur mini=15px
// *****                            : Largeur
// *****                            : Zones Calmes mini=10px
// ***** setText($text='AUTO')      : Texte sous les barres (ou AUTO ou '')
// ***** hideCodeType()             : Désactive l'impression du Type de code
// ***** setColors($fg, $bg=0)      : Couleur des Barres et du Fond
// ***** 
// ***** setCode($code)*            : Enregistre le code a générer
// ***** setType($type)*            : EAN, UPC, C39...
// ***** 
// ***** utiliser l'une ou l'autre de ces deux méthodes :
// ***** showBarcodeImage()**       : Envoie l'image PNG du code à l'affichage
// ***** writeBarcodeFile($file)**  : crée un fichier image du Code à Barres
// ***** 
// ***** * = appel requis
// ***** ** = appel requis pour l'un ou l'autre ou les 2
// ***** 
// *****************************************************************************
// ***** Méthodes Privées :
// *****************************************************************************
// ***** checkCode()                : Vérifie le CODE et positionne FULLCODE
// ***** encode()                   : Converti FULLCODE en barres
// ***** 
if ( !class_exists( "pi_barcode" ) ) {
	class pi_barcode
	{
		/**
		* ***** Définition des variables *****
		*/
		
		var $CODE;
		var $FULLCODE;
		var $TYPE;
		var $HEIGHT;
		var $WIDTH;
		var $CODEWIDTH;
		var $CALMZONE;
		var $HR;
		var $SHOWTYPE;
		var $BACKGROUND;
		var $FOREGROUND;
		var $FILETYPE;
		var $ENCODED;
		var $IH = NULL;
		
		/**
		* Définition des symbologies
		*/
		var $C128 = array(
					0 => "11011001100",     1 => "11001101100",     2 => "11001100110",
					3 => "10010011000",     4 => "10010001100",     5 => "10001001100",
					6 => "10011001000",     7 => "10011000100",     8 => "10001100100",
					9 => "11001001000",     10 => "11001000100",    11 => "11000100100",
					12 => "10110011100",    13 => "10011011100",    14 => "10011001110",
					15 => "10111001100",    16 => "10011101100",    17 => "10011100110",
					18 => "11001110010",    19 => "11001011100",    20 => "11001001110",
					21 => "11011100100",    22 => "11001110100",    23 => "11101101110",
					24 => "11101001100",    25 => "11100101100",    26 => "11100100110",
					27 => "11101100100",    28 => "11100110100",    29 => "11100110010",
					30 => "11011011000",    31 => "11011000110",    32 => "11000110110",
					33 => "10100011000",    34 => "10001011000",    35 => "10001000110",
					36 => "10110001000",    37 => "10001101000",    38 => "10001100010",
					39 => "11010001000",    40 => "11000101000",    41 => "11000100010",
					42 => "10110111000",    43 => "10110001110",    44 => "10001101110",
					45 => "10111011000",    46 => "10111000110",    47 => "10001110110",
					48 => "11101110110",    49 => "11010001110",    50 => "11000101110",
					51 => "11011101000",    52 => "11011100010",    53 => "11011101110",
					54 => "11101011000",    55 => "11101000110",    56 => "11100010110",
					57 => "11101101000",    58 => "11101100010",    59 => "11100011010",
					60 => "11101111010",    61 => "11001000010",    62 => "11110001010",
					63 => "10100110000",    64 => "10100001100",    65 => "10010110000",
					66 => "10010000110",    67 => "10000101100",    68 => "10000100110",
					69 => "10110010000",    70 => "10110000100",    71 => "10011010000",
					72 => "10011000010",    73 => "10000110100",    74 => "10000110010",
					75 => "11000010010",    76 => "11001010000",    77 => "11110111010",
					78 => "11000010100",    79 => "10001111010",    80 => "10100111100",
					81 => "10010111100",    82 => "10010011110",    83 => "10111100100",
					84 => "10011110100",    85 => "10011110010",    86 => "11110100100",
					87 => "11110010100",    88 => "11110010010",    89 => "11011011110",
					90 => "11011110110",    91 => "11110110110",    92 => "10101111000",
					93 => "10100011110",    94 => "10001011110",    95 => "10111101000",
					96 => "10111100010",    97 => "11110101000",    98 => "11110100010",
					99  => "10111011110",    // 99 et 'c' sont identiques ne nous sert que pour le checksum
					100 => "10111101110",    // 100 et 'b' sont identiques ne nous sert que pour le checksum
					101 => "11101011110",    // 101 et 'a' sont identiques ne nous sert que pour le checksum
					102 => "11110101110",    // 102 correspond à FNC1 ne nous sert que pour le checksum
					'c' => "10111011110",   'b' => "10111101110",   'a' => "11101011110",
					'A' => "11010000100",   'B' => "11010010000",   'C' => "11010011100",
					'S' => "1100011101011"
				);
		
		var $C25 =  array(
					0 => "11331",           1 => "31113",
					2 => "13113",           3 => "33111",
					4 => "11313",           5 => "31311",
					6 => "13311",           7 => "11133",
					8 => "31131",           9 => "13131",
					'D' => "111011101",       'F' => "111010111", // Code 2 parmi 5
					'd' => "1010",          'f' => "11101"   // Code 2/5 entrelacé
				);
					 
		var $C39 =  array(
					'0' => "101001101101",  '1' => "110100101011",  '2' => "101100101011",
					'3' => "110110010101",  '4' => "101001101011",  '5' => "110100110101",
					'6' => "101100110101",  '7' => "101001011011",  '8' => "110100101101",
					'9' => "101100101101",  'A' => "110101001011",  'B' => "101101001011",
					'C' => "110110100101",  'D' => "101011001011",  'E' => "110101100101",
					'F' => "101101100101",  'G' => "101010011011",  'H' => "110101001101",
					'I' => "101101001101",  'J' => "101011001101",  'K' => "110101010011",
					'L' => "101101010011",  'M' => "110110101001",  'N' => "101011010011",
					'O' => "110101101001",  'P' => "101101101001",  'Q' => "101010110011",
					'R' => "110101011001",  'S' => "101101011001",  'T' => "101011011001",
					'U' => "110010101011",  'V' => "100110101011",  'W' => "110011010101",
					'X' => "100101101011",  'Y' => "110010110101",  'Z' => "100110110101",
					'-' => "100101011011",  '.' => "110010101101",  ' ' => "100110101101",
					'$' => "100100100101",  '/' => "100100101001",  '+' => "100101001001",
					'%' => "101001001001",  '*' => "100101101101"
				);
					 
		var $codabar = array(
					'0' => "101010011",     '1' => "101011001",     '2' => "101001011",
					'3' => "110010101",     '4' => "101101001",     '5' => "110101001",
					'6' => "100101011",     '7' => "100101101",     '8' => "100110101",
					'9' => "110100101",     '-' => "101001101",     '$' => "101100101",
					':' => "1101011011",    '/' => "1101101011",    '.' => "1101101101",
					'+' => "1011011011",    'A' => "1011001001",    'B' => "1010010011",
					'C' => "1001001011",    'D' => "1010011001"
				);
				
		var $MSI = array(
					0 => "100100100100", 
					1 => "100100100110", 
					2 => "100100110100", 
					3 => "100100110110", 
					4 => "100110100100", 
					5 => "100110100110", 
					6 => "100110110100", 
					7 => "100110110110", 
					8 => "110100100100", 
					9 => "110100100110", 
					'D' => "110", 
					'F' => "1001"
				);
					 
		var $C11 = array(
					'0' => "101011", 
					'1' => "1101011", 
					'2' => "1001011", 
					'3' => "1100101", 
					'4' => "1011011", 
					'5' => "1101101", 
					'6' => "1001101", 
					'7' => "1010011", 
					'8' => "1101001", 
					'9' => "110101", 
					'-' => "101101", 
					'S' => "1011001" 
				);
	
		var $postnet = array(
					'0' => "11000", 
					'1' => "00011", 
					'2' => "00101", 
					'3' => "00110", 
					'4' => "01001", 
					'5' => "01010", 
					'6' => "01100", 
					'7' => "10001", 
					'8' => "10010", 
					'9' => "10100"
				);
	
		var $kix = array(       //0=haut, 1=bas, 2=milieu, 3=toute la hauteur
					'0' => '2233',          '1' => '2103',          '2' => '2130',
					'3' => '1203',          '4' => '1230',          '5' => '1100',
					'6' => '2013',          '7' => '2323',          '8' => '2310',
					'9' => '1023',          'A' => '1010',          'B' => '1320',
					'C' => '2031',          'D' => '2301',          'E' => '2332',
					'F' => '1001',          'G' => '1032',          'H' => '1302',
					'I' => '0213',          'J' => '0123',          'K' => '0110',
					'L' => '3223',          '2' => '3210',          'N' => '3120',
					'O' => '0231',          'P' => '0101',          'Q' => '0132',
					'R' => '3201',          'S' => '3232',          'T' => '3102',
					'U' => '0011',          'V' => '0321',          'W' => '0312',
					'X' => '3021',          'Y' => '3021',          'Z' => '3322'
				);
	
		var $CMC7 = array(
					0 => "0,3-0,22|2,1-2,24|4,0-4,8|4,18-4,25|8,0-8,8|8,18-8,25|12,0-12,8|12,18-12,25|14,1-14,24|16,3-16,22",
					1 => "0,5-0,12|0,17-0,25|4,3-4,10|4,17-4,25|6,2-6,9|6,17-6,25|8,1-8,25|10,0-10,25|14,14-14,25|16,14-16,25",
					2 => "0,2-0,9|0,17-0,25|2,0-2,9|2,16-2,25|6,0-6,6|6,13-6,25|10,0-10,6|10,11-10,17|10,20-10,25|12,0-12,6|12,10-12,16|12,20-12,25|14,0-14,14|14,20-14,25|16,2-16,13|16,20-16,25",
					3 => "0,2-0,9|0,17-0,23|4,0-4,9|4,17-4,25|6,0-6,8|6,18-6,25|10,0-10,7|10,10-10,16|10,19-10,25|12,0-12,7|12,10-12,16|12,19-12,25|14,0-14,25|16,2-16,12|16,14-16,23",
					4 => "0,6-0,21|4,4-4,21|6,3-6,11|6,16-6,21|8,2-8,10|8,16-8,21|12,0-12,8|12,15-12,25|14,0-14,8|14,15-14,25|16,0-16,8|16,15-16,25",
					5 => "0,0-0,14|0,19-0,25|2,0-2,14|2,19-2,25|4,0-4,6|4,9-4,14|4,19-4,25|6,0-6,6|6,9-6,14|6,19-6,25|10,0-10,6|10,9-10,14|10,19-10,25|14,0-14,6|14,9-14,25|16,0-16,6|16,11-16,23",
					6 => "0,2-0,23|2,0-2,25|4,0-4,6|4,10-4,15|4,19-4,25|8,0-8,6|8,10-8,15|8,19-8,25|10,0-10,6|10,10-10,15|10,19-10,25|14,0-14,7|14,10-14,25|16,2-16,7|16,12-16,23",
					7 => "0,0-0,9|0,19-0,25|4,0-4,6|4,16-4,25|8,0-8,6|8,12-8,21|10,0-10,6|10,9-10,19|12,0-12,17|14,0-14,15|16,0-16,13",
					8 => "0,2-0,10|0,15-0,23|2,0-2,11|2,14-2,25|6,0-6,6|6,10-6,15|6,19-6,25|8,0-8,6|8,10-8,15|8,19-8,25|10,0-10,6|10,10-10,15|10,19-10,25|14,0-14,11|14,14-14,25|16,2-16,10|16,15-16,23",
					9 => "0,2-0,13|0,18-0,23|2,0-2,15|2,18-2,25|6,0-6,6|6,10-6,15|6,19-6,25|8,0-8,6|8,10-8,15|8,19-8,25|12,0-12,6|12,10-12,15|12,19-12,25|14,0-14,25|16,2-16,23",
					'A' => "0,4-0,15|0,19-0,24|2,4-2,15|2,19-2,24|4,4-4,15|4,19-4,24|8,4-8,15|8,19-8,24|10,4-10,15|10,19-10,24|12,4-12,15|12,19-12,24|16,4-16,15|16,19-16,24",
					'B' => "0,9-0,24|4,7-4,22|6,6-6,21|8,5-8,20|10,4-10,19|12,3-12,18|16,1-16,16",
					'C' => "0,4-0,12|0,16-0,24|2,4-2,12|2,16-2,24|4,4-4,12|4,16-4,24|6,4-6,12|6,16-6,24|10,7-10,21|12,7-12,21|16,7-16,21",
					'D' => "0,10-0,24|2,10-2,24|6,10-6,24|8,10-8,24|10,4-10,24|12,4-12,24|16,4-16,24",
					'E' => "0,7-0,12|0,16-0,25|2,5-2,23|4,3-4,21|6,1-6,19|8,0-8,18|12,3-12,21|16,7-16,12|16,16-16,25",
				);
				
		var $EANbars = array('A' => array(
					0 => "0001101",         1 => "0011001",
					2 => "0010011",         3 => "0111101",
					4 => "0100011",         5 => "0110001",
					6 => "0101111",         7 => "0111011",
					8 => "0110111",         9 => "0001011"
					),
					'B' => array(
					0 => "0100111",         1 => "0110011",
					2 => "0011011",         3 => "0100001",
					4 => "0011101",         5 => "0111001",
					6 => "0000101",         7 => "0010001",
					8 => "0001001",         9 => "0010111"
					),
					'C' => array(
					0 => "1110010",         1 => "1100110",
					2 => "1101100",         3 => "1000010",
					4 => "1011100",         5 => "1001110",
					6 => "1010000",         7 => "1000100",
					8 => "1001000",         9 => "1110100"
					)
				);
		
		var $EANparity = array(
					0 => array('A','A','A','A','A','A'),
					1 => array('A','A','B','A','B','B'),
					2 => array('A','A','B','B','A','B'),
					3 => array('A','A','B','B','B','A'),
					4 => array('A','B','A','A','B','B'),
					5 => array('A','B','B','A','A','B'),
					6 => array('A','B','B','B','A','A'),
					7 => array('A','B','A','B','A','B'),
					8 => array('A','B','A','B','B','A'),
					9 => array('A','B','B','A','B','A')
				);
		
		/**
		* Constructeur // est appelé automatiquement à l'instanciation de l'objet
		*/
		function pi_barcode()
		{
			$this->CODE = '';
			$this->FULLCODE = 'NO CODE SET';
			$this->TYPE = 'ERR';
			$this->HEIGHT = 15;
			$this->WIDTH = 0;
			$this->CALMZONE = 10;
			$this->HR = 'AUTO';
			$this->SHOWTYPE = 'Y';
			$this->FOREGROUND = hexdec('#000000');
			$this->BACKGROUND = hexdec('#FFFFFF');
			$this->FILETYPE = 'PNG';
			$this->ENCODED = '';
			// detruire eventuellement l'image existante
			if ($this->IH) imagedestroy($this->IH);
			$this->IH = NULL;
		}
		
		/**
		* Set Barcode Type
		*/
		function setType($type)
		{
			$this->TYPE = $type;
		}
		/**
		* Set Barcode String
		*/
		function setCode($code)
		{
			$this->CODE = $code;
		}
		/**
		* Set Image Height and Extra-Width
		*/
		function setSize($height, $width=0, $calmZone=0)
		{
			$this->HEIGHT = ($height > 15 ? $height : 15);
			$this->WIDTH = ($width > 0 ? $width : 0);
			$this->CALMZONE = ($calmZone > 10 ? $calmZone : 10);
		}
		/**
		* Set the Printed Text under Bars
		*/
		function setText($text='AUTO')
		{
			$this->HR = $text;
		}
		/**
		* Disable CodeType printing
		*/
		function hideCodeType()
		{
			$this->SHOWTYPE = 'N';
		}
		/**
		* Set Colors
		*/
		function setColors($fg, $bg='#FFFFFF')
		{
			$this->FOREGROUND = hexdec($fg);
			$this->BACKGROUND = hexdec($bg);
		}
		/**
		* Set File Type (PNG, GIF or JPG)
		*/
		function setFiletype($ft='PNG')
		{
			$ft = strtoupper($ft);
			$this->FILETYPE = ($ft == 'GIF' ? 'GIF' : ($ft == 'JPG' ? 'JPG' : 'PNG'));
		}
		
		/**
		* Vérification du Code
		* 
		* calcul ou vérification du Checksum
		*/
		function checkCode()
		{
			switch( $this->TYPE ) {
				case "C128C" :
				
					if (preg_match("/^[0-9]{2,48}$/", $this->CODE))
					{
						$tmp = strlen($this->CODE);
						if (($tmp%2)!=0) $this->FULLCODE = '0'.$this->CODE;
						else             $this->FULLCODE = $this->CODE;
					}
					else
					{
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "CODE 128C REQUIRES DIGITS ONLY";
					  break;
					}
					
				case "C128" :
					
					$carok = true;
					$long = strlen( $this->CODE );
					$i = 0;
					while (($carok) && ($i<$long))
					{
						$tmp = ord( $this->CODE{$i} ) ;
						if (($tmp < 32) OR ($tmp > 126)) $carok = false;
						$i++;
					}
					if ($carok) $this->FULLCODE = $this->CODE;
					else
					{
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "UNAUTHORIZED CHARS IN 128 CODE";
					}
				
				  break;
				case "UPC" :
				
					$this->CODE = '0'.$this->CODE;
					$this->TYPE = 'EAN';
					
				case "EAN" :
				
					$long = strlen( $this->CODE ) ;
					$factor = 3;
					$checksum = 0;
					
					if (preg_match("/^[0-9]{8}$/", $this->CODE) OR preg_match("/^[0-9]{13}$/", $this->CODE))
					{
				   
						for ($index = ($long - 1); $index > 0; $index--)
						{
							$checksum += intval($this->CODE{$index-1}) * $factor ;
							$factor = 4 - $factor ;
						}
						$cc = ( (1000 - $checksum) % 10 ) ;
				
						if ( substr( $this->CODE, -1, 1) != $cc )
						{
							$this->TYPE = "ERR";
							$this->FULLCODE = "CHECKSUM ERROR IN EAN/UPC CODE";
						}
						else $this->FULLCODE = $this->CODE;
				   
					}
					elseif (preg_match("/^[0-9]{7}$/", $this->CODE) OR preg_match("/^[0-9]{12}$/", $this->CODE))
					{
				   
						for ($index = $long; $index > 0; $index--) {
							$checksum += intval($this->CODE{$index-1}) * $factor ;
							$factor = 4 - $factor ;
						}
						$cc = ( ( 1000 - $checksum ) % 10 ) ;
				
						$this->FULLCODE = $this->CODE.$cc ;
				   
					}
					else
					{
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "THIS CODE IS NOT EAN/UPC TYPE";
					}
				
				  break;
				case "C25I" :
				
					$long = strlen($this->CODE);
					if(($long%2)==0) $this->CODE = '0'.$this->CODE;
			
				case "C25" :
				
					if (preg_match("/^[0-9]{1,48}$/", $this->CODE))
					{
						$checksum = 0;
						$factor = 3;
						$long = strlen($this->CODE);
						for ($i = $long; $i > 0; $i--) {
							$checksum += intval($this->CODE{$i-1}) * $factor;
							$factor = 4-$factor;
						}
						$checksum = 10 - ($checksum % 10);
						if ($checksum == 10) $checksum = 0;
						$this->FULLCODE = $this->CODE.$checksum;
					}
					else
					{
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "CODE C25 REQUIRES DIGITS ONLY";
					}
					
				  break;
				case "C39" :
					
					if (preg_match("/^[0-9A-Z\-\.\$\/+% ]{1,48}$/i", $this->CODE))
					{
					  $this->FULLCODE = '*'.$this->CODE.'*';
					}
					else
					{
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "UNAUTHORIZED CHARS IN CODE 39";
					}
					
				  break;
				case "CODABAR" :
				
					if (!preg_match("/^(A|B|C|D)[0-9\-\$:\/\.\+]{1,48}(A|B|C|D)$/i", $this->CODE))
					{
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "CODABAR START/STOP : ABCD";
					}
					else $this->FULLCODE = $this->CODE;
					
				  break;
				case "MSI" :
				
					if (preg_match("/^[0-9]{1,48}$/", $this->CODE))
					{
						$checksum = 0;
						$factor = 1;
						$tmp = strlen($this->CODE);
						for ($i = 0; $i < $tmp; $i++) {
							$checksum += intval($this->CODE{$i}) * $factor;
							$factor++;
							if ($factor > 10) $factor = 1; 
						}
						$checksum = (1000 - $checksum) % 10;
						$this->FULLCODE = $this->CODE.$checksum;
					}
					else
					{
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "CODE MSI REQUIRES DIGITS ONLY";
					}
					
				  break;
				case "C11" :
				
					if (preg_match("/^[0-9\-]{1,48}$/", $this->CODE))
					{
						$checksum = 0;
						$factor = 1;
						$tmp = strlen($this->CODE);
						for ($i = $tmp; $i > 0; $i--) {
							$tmp = $this->CODE{$i-1};
							if ($tmp == "-") $tmp = 10;
							else $tmp = intval($tmp);
							$checksum += ($tmp * $factor);
							$factor++; 
							if ($factor > 10) $factor=1;
						}
						$checksum = $checksum % 11;
						if ($checksum == 10) $this->FULLCODE = $this->CODE . "-";
						else $this->FULLCODE .= $this->CODE.$checksum;
					}
					else
					{
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "UNAUTHORIZED CHARS IN CODE 11";
					}
					
				  break;
				case "POSTNET" :
				
					if (preg_match("/^[0-9]{5}$/", $this->CODE) OR preg_match("/^[0-9]{9}$/", $this->CODE) OR preg_match("/^[0-9]{11}$/", $this->CODE))
					{
						$checksum = 0;
						$tmp = strlen($this->CODE);
						for ($i = $tmp; $i > 0; $i--) {
							$checksum += intval($this->CODE{$i-1});
						}
						$checksum = 10 - ($checksum % 10);
						if($checksum == 10) $checksum = 0;
						$this->FULLCODE = $this->CODE.$checksum;
					}
					else
					{
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "POSTNET MUST BE 5/9/11 DIGITS";
					}
					
				  break;
				case "KIX" :
				
					if (preg_match("/^[A-Z0-9]{1,50}$/", $this->CODE))
					{
/* ***** LE CODE KIX n'a pas de checksum (correction V2.02)
						$checksum = 0;
						$tmp = strlen("$this->CODE");
						for($i=$tmp-1; $i>=0; $i--) {
							$checksum += intval(substr($this->CODE,$i,1));
						}
						$checksum = 10-($checksum%10);
						if($checksum==10) $checksum=0;
						$this->CODE .= "$checksum";
*/
						$this->FULLCODE = $this->CODE;
					}
					else
					{
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "UNAUTHORIZED CHARS IN KIX CODE";
					}
					
				  break;
				case "CMC7" :
				
					if(!preg_match("/^[0-9A-E]{1,48}$/", $this->CODE)) {
					  $this->TYPE = "ERR";
					  $this->FULLCODE = "CMC7 MUST BE NUMERIC or ABCDE";
					}
					else $this->FULLCODE = $this->CODE;
					
				  break;
				default :
				
					$this->TYPE = "ERR";
					$this->FULLCODE = "UNKWOWN BARCODE TYPE";
					
				  break;
			}
		}
		
		/**
		* Encodage
		* 
		* Encode des symboles (a-Z, 0-9, ...) vers des barres
		*/
		function encode()
		{
			settype($this->FULLCODE, 'string');
			$lencode = strlen($this->FULLCODE);
			
			$encodedString = '';
			
			// Copie de la chaine dans un tableau
			$a_tmp = array();
			for($i = 0; $i < $lencode ; $i++) $a_tmp[$i] = $this->FULLCODE{$i};
	
			switch( $this->TYPE ) {
			
				case "EAN" :
				case "UPC" :
					if ($lencode == 8)
					{
						$encodedString = '101'; //Premier séparateur (101)
						for ($i = 0; $i < 4; $i++) $encodedString .= $this->EANbars['A'][$a_tmp[$i]]; //Codage partie gauche (tous de classe A)
						$encodedString .= '01010'; //Séparateur central (01010) //Codage partie droite (tous de classe C)
						for ($i = 4; $i < 8; $i++) $encodedString .= $this->EANbars['C'][$a_tmp[$i]];
						$encodedString .= '101'; //Dernier séparateur (101)
					}
					else
					{
						$parity = $this->EANparity[$a_tmp[0]]; //On récupère la classe de codage de la partie qauche
						$encodedString = '101'; //Premier séparateur (101)
						for ($i = 1; $i < 7; $i++) $encodedString .= $this->EANbars[$parity[$i-1]][$a_tmp[$i]]; //Codage partie gauche
						$encodedString .= '01010'; //Séparateur central (01010) //Codage partie droite (tous de classe C)
						for ($i = 7; $i < 13; $i++) $encodedString .= $this->EANbars['C'][$a_tmp[$i]];
						$encodedString .= '101'; //Dernier séparateur (101)
					}
		
				  break;
				case "C128C" :
					$encodedString = $this->C128['C']; //Start
					$checksum = 105 ;
					$j = 1 ;
					for ($i = 0; $i < $lencode; $i += 2)
					{
						$tmp = intval(substr($this->FULLCODE, $i, 2)) ;
						$checksum += ( $j++ * $tmp ) ;
						$encodedString .= $this->C128[$tmp];
					}
					$checksum %= 103 ;
					$encodedString .= $this->C128[$checksum];
					$encodedString .= $this->C128['S']; //Stop
				  break;
				case "C128" :
					$encodedString = $this->C128['B']; //Start
					$checksum = 104 ;
					$j = 1 ;
					for ($i = 0; $i < $lencode; $i++)
					{
						$tmp = ord($a_tmp[$i]) - 32 ;
						$checksum += ( $j++ * $tmp ) ;
						$encodedString .= $this->C128[$tmp];
					}
					$checksum %= 103 ;
					$encodedString .= $this->C128[$checksum];
					$encodedString .= $this->C128['S']; //Stop
				  break;
				case "C25" :
					$encodedString = $this->C25['D']."0"; //Start
					for ($i = 0; $i < $lencode; $i++)
					{
						$num = intval($a_tmp[$i]) ;
						$tmp = $this->C25[$num];
						for ($j = 0; $j < 5; $j++)
						{
							$tmp2 = intval(substr($tmp,$j,1)) ;
							for ($k = 1; $k <= $tmp2; $k++) $encodedString .= "1";
							$encodedString .= "0";
						}
					}
					$encodedString .= $this->C25['F']; //Stop
				  break;
				case "C25I" :
					$encodedString = $this->C25['d']; //Start
					$checksum = 0;
					for ($i = 0; $i < $lencode; $i += 2)
					{
						$num1 = intval($a_tmp[$i]) ;
						$num2 = intval($a_tmp[$i+1]) ;
						$checksum += ($num1+$num2);
						$tmp1 = $this->C25[$num1];
						$tmp2 = $this->C25[$num2];
						for ($j = 0; $j < 5; $j++)
						{
							$t1 = intval(substr($tmp1, $j, 1)) ;
							$t2 = intval(substr($tmp2, $j, 1)) ;
							for ($k = 1; $k <= $t1; $k++) $encodedString .= "1";
							for ($k = 1; $k <= $t2; $k++) $encodedString .= "0";
						}
					}
					$encodedString .= $this->C25['f']; //Stop
				  break;
				case "C39" :
					for ($i = 0; $i < $lencode; $i++)$encodedString .= $this->C39[$a_tmp[$i]] . "0";
					$encodedString = substr($encodedString, 0, -1);
				  break;
				case "CODABAR" :
					for ($i = 0; $i < $lencode; $i++) $encodedString .= $this->codabar[$a_tmp[$i]] . "0";
					$encodedString = substr($encodedString, 0, -1);
				  break;
				case "MSI" :
					$encodedString = $this->MSI['D']; //Start
					for ($i = 0; $i < $lencode; $i++) $encodedString .= $this->MSI[intval($a_tmp[$i])];
					$encodedString .= $this->MSI['F']; //Stop
				  break;
				case "C11" :
					$encodedString = $this->C11['S']."0"; //Start
					for ($i = 0; $i < $lencode; $i++) $encodedString .= $this->C11[$a_tmp[$i]]."0";
					$encodedString .= $this->C11['S']; //Stop
				  break;
				case "POSTNET" :
					$encodedString = '1'; //Start
					for ($i = 0; $i < $lencode; $i++) $encodedString .= $this->postnet[$a_tmp[$i]];
					$encodedString .= '1'; //Stop
					
					$this->CODEWIDTH = ( strlen($encodedString) * 4 ) - 4;
					if( $this->HR != '' ) $this->HEIGHT = 32;
					else $this->HEIGHT = 22;
				  break;
				case "KIX" :
//	                $encodedString = "31"; //Start
					for ($i = 0; $i < $lencode; $i++) $encodedString .= $this->kix[$a_tmp[$i]];
//	                $encodedString .= "32"; //Stop
					
					$this->CODEWIDTH = ( strlen($encodedString) * 4 ) - 4;
					if( $this->HR != '' ) $this->HEIGHT = 32;
					else $this->HEIGHT = 22;
				  break;
				case "CMC7" :
					$encodedString = $this->FULLCODE;
									
					$this->CODEWIDTH = ( $lencode * 24 ) - 8;
					$this->HEIGHT = 35;
				  break;
				case "ERR" :
					$encodedString = '';
									
					$this->CODEWIDTH = (imagefontwidth(2) * $lencode);
					$this->HEIGHT = max( $this->HEIGHT, 36 );
				  break;
				  
			}
	
			$nb_elem = strlen($encodedString);
			$this->CODEWIDTH = max( $this->CODEWIDTH, $nb_elem );
			$this->WIDTH = max( $this->WIDTH, $this->CODEWIDTH + ($this->CALMZONE*2) );
			$this->ENCODED = $encodedString;
		
		
			/**
			* Création de l'image du code
			*/
			
			//Initialisation de l'image
			$txtPosX = $posX = intval(($this->WIDTH - $this->CODEWIDTH)/2); // position X
			$posY = 0; // position Y
			$intL = 1; // largeur de la barre
			
			// detruire eventuellement l'image existante
			if ($this->IH) imagedestroy($this->IH);
			
			$this->IH = imagecreate($this->WIDTH, $this->HEIGHT);
			
			// colors
            $color[0] = ImageColorAllocate($this->IH, 0xFF & ($this->BACKGROUND >> 0x10), 0xFF & ($this->BACKGROUND >> 0x8), 0xFF & $this->BACKGROUND);
            $color[1] = ImageColorAllocate($this->IH, 0xFF & ($this->FOREGROUND >> 0x10), 0xFF & ($this->FOREGROUND >> 0x8), 0xFF & $this->FOREGROUND);
			$color[2] = ImageColorAllocate($this->IH, 160,160,160); // greyed
			
			imagefilledrectangle($this->IH, 0, 0, $this->WIDTH, $this->HEIGHT, $color[0]);
			
			
			// Gravure du code
			for ($i = 0; $i < $nb_elem; $i++)
			{
			
				// Hauteur des barres dans l'image
				$intH = $this->HEIGHT; 
				if( $this->HR != '' )
				{
					switch ($this->TYPE)
					{
					  case "EAN" :
					  case "UPC" :
						if($i<=2 OR $i>=($nb_elem-3) OR ($i>=($nb_elem/2)-2 AND $i<=($nb_elem/2)+2)) $intH-=6; else $intH-=11;
					  break;
					  default :
						if($i>0 AND $i<($nb_elem-1)) $intH-=11;
					}
				}
				
				// Gravure des barres
				$fill_color = $this->ENCODED{$i};
				switch ($this->TYPE)
				{
				  case "POSTNET" :
					if($fill_color == "1") imagefilledrectangle($this->IH, $posX, ($posY+1), $posX+1, ($posY+20), $color[1]);
					else imagefilledrectangle($this->IH, $posX, ($posY+12), $posX+1, ($posY+20), $color[1]);
					$intL = 4 ;
				  break;
				  case "KIX" :
					if($fill_color == "0") imagefilledrectangle($this->IH, $posX, ($posY+1), $posX+1, ($posY+13), $color[1]);
					elseif($fill_color == "1") imagefilledrectangle($this->IH, $posX, ($posY+7), $posX+1, ($posY+19), $color[1]);
					elseif($fill_color == "2") imagefilledrectangle($this->IH, $posX, ($posY+7), $posX+1, ($posY+13), $color[1]);
					else imagefilledrectangle($this->IH, $posX, ($posY+1), $posX+1, ($posY+19), $color[1]);
					$intL = 4 ;
				  break;
				  case "CMC7" :
					$tmp = $this->CMC7[$fill_color];
					$coord = explode( "|", $tmp );
					
					for ($j = 0; $j < sizeof($coord); $j++)
					{
						$pts = explode( "-", $coord[$j] );
						$deb = explode( ",", $pts[0] );
						$X1 = $deb[0] + $posX ;
						$Y1 = $deb[1] + 5 ;
						$fin = explode( ",", $pts[1] );
						$X2 = $fin[0] + $posX ;
						$Y2 = $fin[1] + 5 ;
						
						imagefilledrectangle($this->IH, $X1, $Y1, $X2, $Y2, $color[1]);
					}
					$intL = 24 ;
				  break;
				  default :
					if($fill_color == "1") imagefilledrectangle($this->IH, $posX, $posY, $posX, ($posY+$intH), $color[1]);
				}
				
				//Deplacement du pointeur
				$posX += $intL;
			}
			
			// Ajout du texte
			$ifw = imagefontwidth(3);
			$ifh = imagefontheight(3) - 1;
			
			$text = ($this->HR == 'AUTO' ? $this->CODE : $this->HR);
			
			switch ($this->TYPE)
			{
			  case "ERR" :
				$ifw = imagefontwidth(3);
				imagestring($this->IH, 3, floor( (($this->WIDTH)-($ifw * 7)) / 2 ), 1, "ERROR :", $color[1]); 
				imagestring($this->IH, 2, 10, 13, $this->FULLCODE, $color[1]); 
				$ifw = imagefontwidth(1);
				imagestring($this->IH, 1, ($this->WIDTH)-($ifw * 9)-2, $this->HEIGHT - $ifh, "Pitoo.com", $color[2]); 
			  break;
			  case "EAN" :
					if ($text != '') if((strlen($this->FULLCODE) > 10) && ($this->FULLCODE{0} > 0)) imagestring($this->IH, 3, $txtPosX-7, $this->HEIGHT - $ifh, substr($this->FULLCODE,-13,1), $color[1]); 
			  case "UPC" :
				if ($text != '') 
				{
					if(strlen($this->FULLCODE) > 10) {
						imagestring($this->IH, 3, $txtPosX+4, $this->HEIGHT - $ifh, substr($this->FULLCODE,1,6), $color[1]); 
						imagestring($this->IH, 3, $txtPosX+50, $this->HEIGHT - $ifh, substr($this->FULLCODE,7,6), $color[1]); 
					} else {
						imagestring($this->IH, 3, $txtPosX+4, $this->HEIGHT - $ifh, substr($this->FULLCODE,0,4), $color[1]); 
						imagestring($this->IH, 3, $txtPosX+36, $this->HEIGHT - $ifh, substr($this->FULLCODE,4,4), $color[1]); 
					}
				}
			  break;
			  case "CMC7" :
			  break;
			  default :
				if ($text != '') imagestring($this->IH, 3, intval((($this->WIDTH)-($ifw * strlen($text)))/2)+1, $this->HEIGHT - $ifh, $this->CODE, $color[1]); 
			}
			
			// de temps a autres, ajouter pitoo.com *** Merci de ne pas supprimer cette fonction ***
			$ifw = imagefontwidth(1) * 9;
			if ((rand(0,50)<1) AND ($this->HEIGHT >= $ifw)) imagestringup($this->IH, 1, $nb_elem + 12, $this->HEIGHT - 2, "Pitoo.com", $color[2]);
			
			// impression du type de code (si demandé)
			if ($this->SHOWTYPE == 'Y')
			{
				if (($this->TYPE == "EAN") AND (strlen($this->FULLCODE) > 10) AND ($this->FULLCODE{0} > 0) AND ($text != ''))
				{
					imagestringup($this->IH, 1, 0, $this->HEIGHT - 12, $this->TYPE, $color[2]); 
				}
				elseif ($this->TYPE == "POSTNET")
				{
					imagestringup($this->IH, 1, 0, $this->HEIGHT - 2, "POST", $color[2]); 
				}
				elseif ($this->TYPE != "ERR")
				{
					imagestringup($this->IH, 1, 0, $this->HEIGHT - 2, $this->TYPE, $color[2]); 
				}
			}
		}
			
		
		/**
		* Show Image
		*/
		function showBarcodeImage()
		{
			$this->checkCode();
			$this->encode();

			if ($this->FILETYPE == 'GIF') 
			{
				Header( "Content-type: image/gif");
				imagegif($this->IH); 
			}
			elseif ($this->FILETYPE == 'JPG') 
			{
				Header( "Content-type: image/jpeg");
				imagejpeg($this->IH); 
			}
			else
			{
				Header( "Content-type: image/png");
				imagepng($this->IH); 
			}
		}
		
		/**
		* Save Image
		*/
		function writeBarcodeFile($file)
		{
			$this->checkCode();
			$this->encode();

			if ($this->FILETYPE == 'GIF')     imagegif($this->IH, $file);
			elseif ($this->FILETYPE == 'JPG') imagejpeg($this->IH, $file);
			else                              imagepng($this->IH, $file);
		}
				
	}
	//Fin de la classe
}


/**
* Compatibilité avec les versions precedentes
* 
* si appel direct de la bibliothèque, générer l'image a la volée
*/
if (strpos($_SERVER['REQUEST_URI'], 'pi_barcode.php'))
{
	extract($_GET);
	
	// ***** Création de l'objet
	$objCode = new pi_barcode() ;
	
	$type = strtoupper($type);
	
	// ***** Hauteur / Largeur
	if( isset($height) || isset($width) ) $objCode->setSize($height, $width);
	
	// ***** Autres arguments
	if( $readable == 'N' ) $objCode->setText('');
	if( $showtype == 'N' ) $objCode->hideCodeType();
	
	if( $color ) 
	{
		if( $bgcolor )     $objCode->setColors($color, $bgcolor);
		else                       $objCode->setColors($color);
	}
			
	
	$objCode -> setType($type) ;
	$objCode -> setCode($code) ;
	
	$objCode -> showBarcodeImage() ;
}
