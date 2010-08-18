<?php
class Log{

	/**
	 * Escribe en el log la excepción.
	 *
	 * @param Exception $ex
	 * @param bool $controled controlada por configuración
	 */
	static function log($ex,$controled){
		$nombre_archivo = LOG_FILE;
		if (!$gestor = fopen($nombre_archivo, 'a')){
			throw new Exception("No se puede ABRIR el archivo $nombre_archivo.");
		}

		// Escribir $contenido a nuestro arcivo abierto.
		$contenido = '/*************************' . "\r\n";
		$contenido .= date('d/m/Y H:i:s') . "\r\n";
		if(!$controled){
			$contenido .= "ERROR NO CONTROLADO POR CONFIGURACIÓN !!! \r\n";
		}
		$contenido .= '/-------------------------' . "\r\n";
		$class = get_class($ex);
		$contenido .= "    TIPO: {$class} \r\n";
		if($ex instanceof Exception){
			$contenido .= "    CODIGO: {$ex->getCode()} \r\n";
			$contenido .= "    MENSAJE: {$ex->getMessage()} \r\n";
			$contenido .= "    ARCHIVO: {$ex->getFile()} \r\n";
			$contenido .= "    LINEA: {$ex->getLine()} \r\n";
			$contenido .= "    TRACE:\r\n";
			$count = 0;
			foreach ($ex->getTrace() as $trace) {
				$contenido .= "      #{$count}:\r\n";
				foreach ($trace as $key => $value) {
					if(is_array($value)){
						$contenido .= "        {$key} => ( ";
						foreach ($value as $argValue){
							$contenido .= "value ";//"{$argValue} "; A veces hay recursividad...
						}
						$contenido .= ")\r\n";
					}
					else{
						$contenido .= "        {$key} => {$value}\r\n";
					}
				}
				$count++;
			}
		}
		else{
			$var = var_export($ex,true);
			$contenido .= "    VAR_DUMP: {$var} \r\n";
		}
		$contenido .= '/*************************' . "\r\n" . "\r\n";
		if (fwrite($gestor, $contenido) === FALSE){
			throw new Exception("No se puede ESCRIBIR en el archivo $nombre_archivo.");
		}
		fclose($gestor);
	}

	function __construct(){}
}
?>