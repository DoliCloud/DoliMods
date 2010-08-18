<?php

/**
 * Autor: Martin Bascal Of FreelanceSoft
 * Created on: 19/10/2007
 * Description:
 *    
 *    Server Side functions of FileManager.
 *    TODO: escapar a las comillas al devolver strings javascript.
 */

require_once(pathinfo(__FILE__,PATHINFO_DIRNAME).'/includeConfig.php');
require_once(CLASSES_PATH."exceptions.php");
require_once(MESAGE_MANAGER_PATH."MessageManager.php");
require_once(XAJAX_PATH."xajax.inc.php");
require_once(FREELANCESOFT_FILEMANAGER_PATH.'FileManager.php');
require_once(FREELANCESOFT_FILEMANAGER_PATH.'FileHelper.php');
if(!isset($_SESSION)) session_start();

/**
 * Obtain the Javascript for xajax requests.
 * 
 * @return string
 */
function FileManager_getJS(){
	global $xajax;
	global $_AJAX_GRID_PATH;
	$js = $xajax->getJavascript(XAJAX_PATH_URL)."\n";
	$js .=
	'<script type="text/javascript" language="javascript" src="'.JS_PATH_URL."FM_commons.js".'">'."\n".
	'</script>'."\n".
	'<script type="text/javascript" language="javascript" src="'.JS_PATH_URL."FM_ImageView.js".'">'."\n".
	'</script>'."\n".
	'<script type="text/javascript" language="javascript" src="'.JS_PATH_URL."FM_DetailedView.js".'">'."\n".
	'</script>'."\n".
	'<script type="text/javascript" language="javascript" src="'.JS_PATH_URL."FM_LocationBar.js".'">'."\n".
	'</script>'."\n".
	'<script type="text/javascript" language="javascript" src="'.JS_PATH_URL."FM_TreeView.js".'">'."\n".
	'</script>'."\n".
	'<script type="text/javascript" language="javascript" src="'.JS_PATH_URL."FM_IconBar.js".'">'."\n".
	'</script>'."\n".
	'<script type="text/javascript" language="javascript" src="'.JS_PATH_URL."FM_FileManager.js".'">'."\n".
	'</script>'."\n";
	foreach ($_SESSION as $sessionVar) {
		if(get_class($sessionVar) == 'FileManager'){
			$js .= $sessionVar->getJS();
		}
	}
	return $js;
}

/**
 * Print the Javascript for xajax requests.
 * 
 * @return string
 */
function FileManager_printJS(){
	echo FileManager_getJS();
}

/**
 * Server function for list files.
 *
 * @param string $idFileManager
 * @param string $path
 * @return xajaxResponse
 */
function FileManager_getFiles($idFileManager,$path){
	$objResponse = new xajaxResponse();
	try{
		$fileManager = $_SESSION[$idFileManager];
		FileHelper::filterPath($fileManager->getRoot(),$path,$realPath,$newPath);
//print "zz".$fileManager->getRoot()."-".$path."-".$realPath."-".$newPath;
		$files = FileHelper::getFiles($realPath);
		$path = $newPath;
		$pathEncoded = urlencode($newPath);
		$error = true;
		$array = '';
		if($files){
			$elements = '';
			$count = count($files);
			$i = 1;
			foreach ($files as $file) {
				$isDir = ($file->isDirectory ? 'true' : 'false');
				$isImg = ($file->isImage ? 'true' : 'false');
				$filePath = urlencode($file->path);
				$last = date(FM_DATE_FORMAT,$file->lastTime);
				$element =
				"new FileDescription('{$filePath}', ".
					"'{$file->httpPath}', ".
					"{$isDir}, ".
					"'{$file->name}', ".
					"'{$file->extension}', ".
					"{$isImg}, ".
					"{$file->size}, ".
					"'{$last}')";
				if($count !== $i){
					$element .= ',';
				}
				$elements .= $element;
				$i++;
			}
			if($newPath != '/'){
				$element =
				"new FileDescription('', ".
					"'', ".
					"true, ".
					"'..', ".
					"'', ".
					"false, ".
					"0, ".
					"'')";
				$elements = ($element . ($count > 0 ? ',' : '') . $elements);
			}
			$array .= "new Array( {$elements} )";
			$error = false;
		}
		elseif(is_array($files)){
			$error = false;
			if($newPath == '/'){
				$array = 'new Array()';
			}
			else{
				$array = 'new Array('."new FileDescription('', ".
					"'', ".
					"true, ".
					"'..', ".
					"'', ".
					"false, ".
					"0, ".
					"'')".')';
			}
		}
		if($error){
			$objResponse->addScriptCall("FM_showAlert('b".FM_ERROR_RESPONSE_FILES."');");
		}
		else{
			$objResponse->addScriptCall("fm_{$idFileManager}.responseFiles({$array}, '{$path}', '{$pathEncoded}');");
		}
	}
	catch(Exception $ex){
		MessageManager::handleException($ex);
		if(IS_DEBUG){
			$objResponse->addAlert("Error: {$ex->getMessage()}");
		}
	}
	return $objResponse;
}

/**
 * Server function for has directories.
 *
 * @param string $idFileManager
 * @param string $path
 * @return xajaxResponse
 */
function FileManager_hasDirectories($idFileManager,$path){
	$objResponse = new xajaxResponse();
	try{
		$fileManager = $_SESSION[$idFileManager];
		FileHelper::filterPath($fileManager->getRoot(),$path,$realPath,$newPath);
		$has = FileHelper::getFiles($realPath) ? 'true' : 'false';
		$path = $newPath;
		$objResponse->addScriptCall("fm_{$idFileManager}.responseHasDirectories({$has}, '{$path}');");
	}
	catch(Exception $ex){
		MessageManager::handleException($ex);
		if(IS_DEBUG){
			$objResponse->addAlert("Error: {$ex->getMessage()}");
		}
	}
	return $objResponse;
}

/**
 * Server side function for get directories
 *
 * @param string $idFileManager
 * @param string $path
 * @return xajaxResponse
 */
function FileManager_getDirectories($idFileManager,$path,$folderName){
	$objResponse = new xajaxResponse();
	try{
		$fileManager = $_SESSION[$idFileManager];
		FileHelper::filterPath($fileManager->getRoot(),$path,$realPath,$newPath);
		$dirs = FileHelper::getDirectories($realPath);
		$error = true;
		$array = '';
		if($dirs){
			$elements = '';
			$count = count($dirs);
			$i = 1;
			foreach ($dirs as $dir) {
				$hasDirs = $dir->hasDirectories ? 'true' : 'false';
				$element = "new DirectoryDescription(".
					"'',". // Set if you need "'{$dir->path}',".
					"'{$dir->name}',".
					"{$hasDirs})";
				if($count !== $i){
					$element .= ',';
				}
				$elements .= $element;
				$i++;
			}
			$array .= "new Array( {$elements} )";
			$error = false;
		}
		else{
			$error = true;
		}
		if($error){
			$objResponse->addScriptCall("FM_showAlert('a".FM_ERROR_RESPONSE_FILES."');");
		}
		else{
			$objResponse->addScriptCall("fm_{$idFileManager}.responseDirectories({$array}, '{$path}','{$folderName}');");
		}
	}
	catch(Exception $ex){
		MessageManager::handleException($ex);
		if(IS_DEBUG){
			$objResponse->addAlert("Error: {$ex->getMessage()}");
		}
	}
	return $objResponse;
}

/**
 * Server side function for file exists
 *
 * @param string $idFileManager
 * @param string $path
 * @param string $fileName
 * @param int $action
 * @param int $indexUpload
 * @return xajaxResponse
 */
function FileManager_fileExists($idFileManager,$path,$fileName,$action,$indexUpload){
	$objResponse = new xajaxResponse();
	try{
		$fileManager = $_SESSION[$idFileManager];
		FileHelper::filterPath($fileManager->getRoot(),$path,$realPath,$newPath);
		$array = split("/",$fileName);
		$realFileName = $array[count($array)-1];
		$exists = FileHelper::fileExists($realPath.'/'.$realFileName);
		if($action == FM_ACTION_UPLOAD){
			if($exists){
				$msg = sprintf(FM_MSG_FILE_UPLOAD_EXISTS,$realFileName);
				$objResponse->addScriptCall("FM_showConfirm('{$msg}','fm_{$idFileManager}.submitUploadForm({$indexUpload})');");
			}
			else
			{
				$objResponse->addScriptCall("fm_{$idFileManager}.submitUploadForm({$indexUpload});");
			}
		}
	}
	catch(Exception $ex){
		MessageManager::handleException($ex);
		if(IS_DEBUG){
			$objResponse->addAlert("Error: {$ex->getMessage()}");
		}
	}
	return $objResponse;
}

/**
 * Server side function for delete file or directory
 *
 * @return xajaxResponse
 */
function FileManager_delete(){
	$objResponse = new xajaxResponse();
	try{
		$numArgs = func_num_args();
		if($numArgs > 2){
			if(FM_ALLOW_DELETE){
				$idFileManager = func_get_arg(0);
				$path = func_get_arg(1);
				$fileManager = $_SESSION[$idFileManager];
				FileHelper::filterPath($fileManager->getRoot(),$path,$realPath,$newPath);
				$error = false;
				for($i = 2; $i < $numArgs; $i++){
					$fileToDelete = func_get_arg($i);
					if($fileToDelete != '..'){
						$pathFileToDelete = $realPath.'/'.$fileToDelete;
						$error = ( $error || !FileHelper::delete($pathFileToDelete));
					}
				}
				if($error){
					$objResponse->addScriptCall("FM_showAlert('".FM_ERROR_DELETE."');");
				}
				else{
					$objResponse->addScriptCall("fm_{$idFileManager}.refresh();");
				}
			}
			else{
				$objResponse->addScriptCall("FM_showAlert('".FM_ERROR_NOT_ALLOWED_DELETE."');");
			}
		}
		else{
			throw new Exception('Cantidad de argumentos inválida para FileManager_delete');
		}
	}
	catch(Exception $ex){
		MessageManager::handleException($ex);
		if(IS_DEBUG){
			$objResponse->addAlert("Error: {$ex->getMessage()}");
		}
	}
	return $objResponse;
}

/**
 * Server side function for file exists
 *
 * @param string $idFileManager
 * @param string $path
 * @param string $fileName
 * @param int $action
 * @param int $indexUpload
 * @return xajaxResponse
 */
function FileManager_createDirectory($idFileManager,$path,$directoryName){
	$objResponse = new xajaxResponse();
	try{
		$fileManager = $_SESSION[$idFileManager];
		FileHelper::filterPath($fileManager->getRoot(),$path,$realPath,$newPath);
		$pathCreate = $realPath.'/'.$directoryName;
		if(!FM_ALLOW_CREATE_DIRECTORY){
			$objResponse->addScriptCall("FM_showAlert('".FM_ERROR_NOT_ALLOWED_CREATE_DIRECTORY."');");
		}
		elseif($directoryName == ''){
			$objResponse->addScriptCall("fm_{$idFileManager}.openCreateDirectoryWindow();".
				"FM_showAlert('".FM_ERROR_CREATE_DIRECTORY_EMPTY."');");
		}
		elseif(FileHelper::directoryExists($pathCreate)){
			$objResponse->addScriptCall("fm_{$idFileManager}.openCreateDirectoryWindow();".
				"FM_showAlert('".sprintf(FM_ERROR_CREATE_DIRECTORY_EXISTS,$directoryName)."');");
		}
		elseif(!FileHelper::createDirectory($pathCreate)){
			$objResponse->addScriptCall("fm_{$idFileManager}.openCreateDirectoryWindow();".
				"FM_showAlert('".sprintf(FM_ERROR_CREATE_DIRECTORY,$directoryName)."');");
		}
		else{
			$objResponse->addScriptCall("fm_{$idFileManager}.refresh();");
		}
	}
	catch(Exception $ex){
		MessageManager::handleException($ex);
		if(IS_DEBUG){
			$objResponse->addAlert("Error: {$ex->getMessage()}");
		}
	}
	return $objResponse;
}

/**
 * Server side function for rename.
 *
 * @param string $idFileManager
 * @param string $path
 * @param string $name
 * @param string $newName
 * @return xajaxResponse
 */
function FileManager_rename($idFileManager,$path,$name,$newName){
	$objResponse = new xajaxResponse();
	try{
		$fileManager = $_SESSION[$idFileManager];
		FileHelper::filterPath($fileManager->getRoot(),$path,$realPath,$newPath);
		$realName = $realPath.'/'.$name;
		$realNewName = $realPath.'/'.$newName;
		if(FileHelper::fileExists($realNewName)){
			$objResponse->addScriptCall("fm_{$idFileManager}.openRenameWindow();".
				"FM_showAlert('".sprintf(FM_ERROR_RENAME_EXISTS,$newName)."');");
		}
		elseif(!FileHelper::rename($realName,$realNewName)){
			$objResponse->addScriptCall("fm_{$idFileManager}.openRenameWindow();".
				"FM_showAlert('".sprintf(FM_ERROR_RENAME,$newName)."');");
		}
		else{
			$objResponse->addScriptCall("fm_{$idFileManager}.refresh();");
		}
	}
	catch(Exception $ex){
		MessageManager::handleException($ex);
		if(IS_DEBUG){
			$objResponse->addAlert("Error: {$ex->getMessage()}");
		}
	}
	return $objResponse;
}

/**
 * Server side function for cut, copy and paste
 *
 * @param string $idFileManager
 * @param string $pathSrc
 * @param string $pathDest
 * @param string $name
 * @param string $extension
 * @param string $remove
 * @return xajaxResponse
 */
function FileManager_paste($idFileManager,$pathSrc,$pathDest,$name,$extension,$remove){
	$objResponse = new xajaxResponse();
	try{
		$fileManager = $_SESSION[$idFileManager];
		FileHelper::filterPath($fileManager->getRoot(),$pathSrc,$realPathSrc,$newPathSrc);
		FileHelper::filterPath($fileManager->getRoot(),$pathDest,$realPathDest,$newPathDest);
		$relativePathSrc = $newPathSrc.$name.$extension;
		$relativePathDest = $newPathDest.FileHelper::generateName($realPathDest,$name,$extension);
		$remove = ($remove == 'true' ? true : false);
		$lenSrc = strlen($relativePathSrc);
		if(!FM_ALLOW_PASTE){
			$objResponse->addScriptCall("FM_showAlert('".FM_ERROR_NOT_ALLOWED_PASTE."');");
		}
		elseif(substr($relativePathDest,0,$lenSrc) == $relativePathSrc && ($lenSrc == strlen($relativePathDest) || $relativePathDest[$lenSrc] == '/')){
			$objResponse->addScriptCall("FM_showAlert('".FM_ERROR_PASTE."'); ".
				"fm_{$idFileManager}.closeCopyWindow();");
		}
		else{
			FileHelper::copyRec($relativePathSrc,$relativePathDest,$fileManager->getRoot(),$remove);
			$objResponse->addScriptCall("fm_{$idFileManager}.responseFilePasted();");		
		}
	}
	catch(Exception $ex){
		MessageManager::handleException($ex);
		if(IS_DEBUG){
			$objResponse->addAlert("Error: {$ex->getMessage()}");
		}
	}
	return $objResponse;
}

if((!isset($xajax)) || (!$xajax instanceof xajax)){
	$xajax = new xajax(XAJAX_SERVER_PATH_URL."FileManager.php");
}

$xajax->registerFunction("FileManager_getFiles");
$xajax->registerFunction("FileManager_hasDirectories");
$xajax->registerFunction("FileManager_getDirectories");
$xajax->registerFunction("FileManager_fileExists");
$xajax->registerFunction("FileManager_delete");
$xajax->registerFunction("FileManager_createDirectory");
$xajax->registerFunction("FileManager_rename");
$xajax->registerFunction("FileManager_paste");
$xajax->processRequests();

?>