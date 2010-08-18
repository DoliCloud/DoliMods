<?php

/**
 * Autor: Martin Bascal Of FreelanceSoft
 * Created on: 07/11/2007
 * Description:
 *    
 *    Upload a file.
 *    
 */

include_once('../../../config/config.php');
require_once(CLASSES_PATH."exceptions.php");
require_once(MESAGE_MANAGER_PATH."MessageManager.php");
require_once(FREELANCESOFT_FILEMANAGER_PATH.'FileManager.php');
require_once(FREELANCESOFT_FILEMANAGER_PATH.'FileHelper.php');
if(!isset($_SESSION)) session_start();

$idFm = $_POST['idFm'];
$path = $_POST['path'];
$uploadIndex = $_POST['uploadIndex'];
$_GET['idFm'] = $idFm;
$_GET['path'] = $path;
$_GET['uploadIndex'] = $uploadIndex;
$file = $_FILES['fileUpload'];
$name = $file['name'];

if(FM_ALLOW_UPLOAD){
	if($file['error'] == UPLOAD_ERR_OK){
		$fileManager = $_SESSION[$idFm];
		FileHelper::filterPath($fileManager->getRoot(),$path,$realPath,$newPath);
		$tmp_name = $file["tmp_name"];
		$pathToFile = $realPath.'/'.$name;
		if(move_uploaded_file($tmp_name, $pathToFile)){
			include(FREELANCESOFT_FILEMANAGER_PATH.'uploadSuccess.php');
		}
		else{
			$msg = sprintf(FM_ERROR_FILE_UPLOAD,$name);
			$onloadFunction = "onload=\"parent.FM_showAlert('{$msg}');\"";
			include(FREELANCESOFT_FILEMANAGER_PATH.'uploadForm.php');
		}
	}
	else{
		$msg = sprintf(FM_ERROR_FILE_UPLOAD,$name);
		$onloadFunction = "onload=\"parent.FM_showAlert('{$msg}');\"";
		include(FREELANCESOFT_FILEMANAGER_PATH.'uploadForm.php');
	}
}
else{
	$msg = FM_ERROR_NOT_ALLOWED_UPLOAD;
	$onloadFunction = "onload=\"parent.FM_showAlert('{$msg}');\"";
	include(FREELANCESOFT_FILEMANAGER_PATH.'uploadForm.php');
}

?>