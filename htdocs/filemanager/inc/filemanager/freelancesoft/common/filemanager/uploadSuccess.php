<?php

/**
 * Autor: Martin Bascal Of FreelanceSoft
 * Created on: 06/11/2007
 * Description:
 *    
 *    Upload a success.
 *    
 */

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

</head>
<body>

<div>Path: <?php echo $newPath.$name; ?></div>
<div><a href="#" onclick="<?php echo "parent.fm_{$idFm}.requestFiles('{$newPath}');"; ?> return false;"><?php echo FM_UPLOAD_SUCCESS_GO_TO; ?></a></div>
<div style="height:10px;border-bottom:1px dashed #000000"></div>

</body>
</html>