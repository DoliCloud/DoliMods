<?php

require_once(MESAGE_MANAGER_PATH."Log.php");

function manageErrors($num_err, $cadena_err, $archivo_err, $linea_err)
{
	/*
	 * Si ocurren errores antes de que el script es ejecutado (p.ej. cuando se cargan archivos desde una 
	 * página web) el gestor de errores personalizado no puede ser llamado ya que no está registrado en ese 
	 * momento.
	 */
    switch ($num_err) {
    case E_USER_ERROR:
    	MessageManager::handleException( new Exception(
    		"ERROR FATAL en la línea {$linea_err} en el archivo {$archivo_err}: {$cadena_err}",$num_err));
        break;
    case E_USER_WARNING:
        MessageManager::handleException( new WarningException(
    		"ADVERTENCIA: {$cadena_err}",$num_err));
        break;

    case E_USER_NOTICE:
        MessageManager::handleException( new WarningException(
    		"NOTICIA: {$cadena_err}",$num_err));
        break;

    default:
    	// Si dejamos esto se garcha por cualquier cosa
		$error = "ERROR DESCONOCIDO: {$cadena_err}";
		if(isset($linea_err) && isset($archivo_err)){
			$error .= " en la línea {$linea_err} en el archivo {$archivo_err}.";
		}
        MessageManager::handleException( new WarningException($error,$num_err));
        break;
    }
    return true;
}

function manageExceptions($ex) {
  MessageManager::handleException($ex);;
}

set_error_handler("manageErrors");
set_exception_handler('manageExceptions');

class MessageManager{
	
	private static $error = NULL;
	private static $message = NULL;
	
	/**
	 * Maneja una excepción según la configuración definida
	 *
	 * @param Exception $ex
	 */
	static function handleException($ex){
		global $MESSAGEMANAGER_DEBUG_CONFIG;
		global $MESSAGEMANAGER_CONFIG;
		if(IS_DEBUG){
			$config = $MESSAGEMANAGER_DEBUG_CONFIG;
		}
		else{
			$config = $MESSAGEMANAGER_CONFIG;
		}
		if(!isset($config)){
			throw new Exception('Configuración no definida.');
		}
		$config = isset($config[get_class($ex)]) ? $config[get_class($ex)] : NULL;
		if(!isset($config) || !($ex instanceof Exception)){
			Log::log($ex,false);
		}
		else{
			$log = (isset($config['LOG']) && $config['LOG']);
			$showError = (isset($config['SHOW_ERROR']) && $config['SHOW_ERROR']);
			$showMessage = (isset($config['SHOW_MESSAGE']) && $config['SHOW_MESSAGE']);
			$replaceError = ($showError && isset($config['REPLACE_ERROR']));
			$replaceMessage = ($showMessage && isset($config['REPLACE_MESSAGE']));
			$echo = (isset($config['ECHO']) && $config['ECHO']);
			$exit = (isset($config['EXIT']) && $config['EXIT']);
			if($replaceError){
				self::$error = $config['REPLACE_ERROR'];
			}
			elseif($showError){
				self::$error = $ex->getMessage();
			}
			if($replaceMessage){
				self::$message = $config['REPLACE_MESSAGE'];
			}
			elseif($showMessage){
				self::$message = $ex->getMessage();
			}
			if($log){
				Log::log($ex,true);
			}
			if($echo){
				echo $ex->getMessage();
			}
			if($exit){
				echo 'Error inesperado...';
				exit();
			}
		}
	}
	
	/**
	 * Actualiza el mensaje de error, no hace log.
	 *
	 * @param string $val
	 */
	static function setError($val){
		self::$error = $val;
	}
	
	/**
	 * Actualiza el mensaje de advertencia, no hace log.
	 *
	 * @param string $val
	 */
	static function setMessage($val){
		self::$message = $val;
	}
	
	/**
	 * Obtiene el mensaje de error.
	 *
	 * @return string
	 */
	static function getError(){
		return self::$error;
	}
	
	/**
	 * Obtiene el mensaje de advertencia.
	 *
	 * @return string
	 */
	static function getMessage(){
		return self::$message;
	}
	
	/**
	 * Obtiene Html con error y/o mensaje, y actualiza para que no se vuelvan a mostrar.
	 * 
	 * @return string
	 */
	static function getHtml(){
		if(!defined('ERROR_TEMPLATE')){
			throw new Exception('No está definido el tamplate para errores.');
		}
		if(!defined('MESSAGE_TEMPLATE')){
			throw new Exception('No está definido el tamplate para mensajes.');
		}
		$html = '';
		if(self::$error != NULL){
			$html .= sprintf(ERROR_TEMPLATE,self::$error);
			self::$error = NULL;
		}
		if(self::$message != NULL){
			$html .= sprintf(MESSAGE_TEMPLATE,self::$message);
			self::$message = NULL;
		}
		return $html;
	}
	
	/**
	 * Imprime Html con error y/o mensaje, y actualiza para que no se vuelvan a mostrar.
	 * 
	 */
	static function printHtml(){
		echo self::getHtml();
	}
}
?>