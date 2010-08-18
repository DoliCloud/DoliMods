<?php

/**
 * Autor: Martin Bascal Of FreelanceSoft
 * Created on: 06/11/2007
 * Description:
 *    
 *    Upload a file.
 *    
 */

include_once('../../../config/config.php');
require_once(FREELANCESOFT_FILEMANAGER_PATH.'config.php');

$path = urldecode($_GET['path']);
$idFm = $_GET['idFm'];
$uploadIndex = $_GET['uploadIndex'];
$actionUpload = FM_ACTION_UPLOAD;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title></title>
<style type="text/css">
<!--
	div{
		border: 0px;
		margin: 0px;
		padding: 0px;
	}
	
	body{
		border: 0px;
		margin: 0px;
		padding-top: 0px;
		padding-bottom: 0px;
		padding-left: 4px;
		padding-right: 4px;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		background-color: #333333;
		color: #CCCCCC;
	}
	
	form{
		border: 0px;
		margin: 0px;
		padding: 0px;
	}
	
	input{
		border: 0px;
		margin: 0px;
		padding-top: 0px;
		padding-bottom: 0px;
		padding-left: 4px;
		padding-right: 4px;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 10px;
		line-height: 12px;
		height: 18px;
	}
	
	a{
		border: 0px;
		margin: 0px;
		padding-top: 0px;
		padding-bottom: 0px;
		padding-left: 4px;
		padding-right: 0px;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		color: #CCCCCC;
		text-decoration: none;
		font-weight: bold;
	}
-->
</style>

<script language="javascript" type="text/javascript">

function FM_getObjNN4(obj,name){
	var x = obj.layers;
	var foundLayer;
	
	for (var i=0;i<x.length;i++)
	{
		if (x[i].id == name)
			foundLayer = x[i];
		else if (x[i].layers.length)
			var tmp = FM_getObjNN4(x[i],name);
		if (tmp) foundLayer = tmp;
	}
	return foundLayer;
}
	
function FM_getObjectById(name){
	var obj;
	
	if(document.getElementById){
		obj = document.getElementById(name);
	}
	else if(document.all)
	{
		obj = document.all[name];
	}
	else if(document.layers)
	{
		obj = FM_getObjNN4(document,name);
		obj.style = obj;
	}
	return obj;
}

function submitForm(){
	var theForm = FM_getObjectById('uploadForm');
	theForm.submit();
}

</script>

</head>
<body <?php echo (isset($onloadFunction)? $onloadFunction : ''); ?>>

<div>Path: <?php echo $path; ?></div>
<form id="uploadForm" name="uploadForm" enctype="multipart/form-data" method="post" action="<?php echo FREELANCESOFT_FILEMANAGER_PATH_URL.'submitUpload.php'; ?>" style="float:left;">
  <input id="fileUpload" name="fileUpload" type="file" name="file" onchange="<?php echo "parent.xajax_FileManager_fileExists('{$idFm}','{$path}',this.value,{$actionUpload},{$uploadIndex})" ?>" />
  <input id="idFm" name="idFm" type="hidden" value="<?php echo $idFm; ?>" />
  <input id="path" name="path" type="hidden" value="<?php echo $path; ?>" />
  <input id="uploadIndex" name="uploadIndex" type="hidden" value="<?php echo $uploadIndex; ?>" />
</form>
<div><a href="#" onclick="<?php echo "parent.fm_{$idFm}.removeUploadForm({$uploadIndex});"; ?> return false;"><?php echo FM_WINDOW_ADD_FILES_MINUS; ?></a></div>
<div style="height:10px;border-bottom:1px dashed #000000"></div>

</body>
</html>
