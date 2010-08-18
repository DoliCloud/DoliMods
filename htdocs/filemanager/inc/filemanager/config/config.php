<?php

	define('ROOT', 'C:/Work/Dev/WorkspaceDolibarr/dolibarr/htdocs/filemanager/inc/filemanager/');	

	define('BASE_URL', 'http://'.$_SERVER["HTTP_HOST"].'/dolibarr/htdocs/filemanager/inc/filemanager/');
	
	define('CLASSES_PATH', ROOT."classes/");
	
	define('JS_PATH', ROOT."js/");
	
	define('JS_PATH_URL', BASE_URL."js/");
	
	define('XAJAX_PATH', ROOT."xajax/");
	
	define('XAJAX_PATH_URL', BASE_URL."xajax/");
	
	define('XAJAX_SERVER_PATH', ROOT."xajaxServer/");
	
	define('XAJAX_SERVER_PATH_URL', BASE_URL."xajaxServer/");
	
	//FREELANCESOFT UTILS
    
    define('FREELANCESOFT_FILEMANAGER_PATH', ROOT."freelancesoft/common/filemanager/");
    
    define('FREELANCESOFT_FILEMANAGER_PATH_URL', BASE_URL."freelancesoft/common/filemanager/");
	
	//Imagenes
	
	define('IMAGE_PATH', ROOT."images/"); // Direccion del File System
	
	define('IMAGE_PATH_URL', BASE_URL."images/"); // Direccion URL para enviarlas al cliente
	
	define('IMAGE_AVATAR_PATH', ROOT."images/avatars/"); // Direccion del File System
	
	define('IMAGE_AVATAR_PATH_URL', BASE_URL."images/avatars/"); // Direccion URL para enviarlas al cliente

	
	// MESSAGE MANAGER
	
	define('ERROR_UNKNOW','Error desconocido.');
	
	date_default_timezone_set('America/Argentina/Buenos_Aires');
	define('IS_DEBUG',true);
	define('LOG_FILE',ROOT."../../../../documents/log.txt");
	define('MESAGE_MANAGER_PATH', ROOT."freelancesoft/common/messageManager/");
	define('ERROR_TEMPLATE',
		'<table border="0" cellpadding="0" cellspacing="2">'.
			'<tr>'.
				'<td>'.
					'<img src="'.IMAGE_PATH_URL.'imagen_error.png"/>'.
				'</td>'.
				'<td class="errorMessage">'.
				'%s'.
				'</td>'.
			'</tr>'.
		'</table>');		
	define('MESSAGE_TEMPLATE',
		'<table border="0" cellpadding="0" cellspacing="2">'.
			'<tr>'.
				'<td>'.
					'<img src="'.IMAGE_PATH_URL.'imagen_info.png"/>'.
				'</td>'.
				'<td class="infoMessage">'.
				'%s'.
				'</td>'.
			'</tr>'.
		'</table>');
	$MESSAGEMANAGER_DEBUG_CONFIG = array(
		"FunctionalException" => array(
			"LOG" => false,
			"SHOW_ERROR" => true,
			"SHOW_MESSAGE" => false,
			"ECHO" => false,
			"EXIT" => false),
		"WarningException" => array(
			"LOG" => true,
			"SHOW_ERROR" => true,
			"SHOW_MESSAGE" => false,
			"ECHO" => false,
			"EXIT" => false),
		"Exception" => array(
			"LOG" => true,
			"SHOW_ERROR" => true,
			"SHOW_MESSAGE" => false,
			"ECHO" => true,
			"EXIT" => true));
			
	$MESSAGEMANAGER_CONFIG = array(
		"FunctionalException" => array(
			"LOG" => false,
			"SHOW_ERROR" => true,
			"SHOW_MESSAGE" => false,
			"ECHO" => false,
			"EXIT" => false),
		"WarningException" => array(
			"LOG" => true,
			"SHOW_ERROR" => false,
			"SHOW_MESSAGE" => false,
			"ECHO" => false,
			"EXIT" => false),
		"Exception" => array(
			"LOG" => true,
			"SHOW_ERROR" => true,
			"SHOW_MESSAGE" => false,
			"REPLACE_ERROR" => ERROR_UNKNOW,
			"ECHO" => false,
			"EXIT" => true));

?>