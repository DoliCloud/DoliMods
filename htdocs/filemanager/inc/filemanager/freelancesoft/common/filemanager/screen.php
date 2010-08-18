<?php if ($_GET) {

//nombre de la imagen, la obtengo de la base de datos
$nombre = urldecode($_GET['i']);
if (is_file($nombre)) {
	$datos = getimagesize($nombre);
	if($datos[2]==1){$img = imagecreatefromgif($nombre);} 
	if($datos[2]==2){$img = imagecreatefromjpeg($nombre);} 
	if($datos[2]==3){$img = imagecreatefrompng($nombre);}
	
	$widthOld = $datos[0];
	$heigthOld = $datos[1];
	$maxWidth = isset($_GET['w']) ? (int)$_GET['w'] : 128;
	$maxHeight = isset($_GET['h']) ? (int)$_GET['h'] : 128;
	
	$limitByWidth = false;
	if($widthOld > $heigthOld) $limitByWidth = true;
	if($limitByWidth){
		$newWidth = $maxWidth;
		$ratio = ($widthOld / $maxWidth);
		$newHeight = ($heigthOld / $ratio);
	}
	else{
		$newHeight = $maxHeight;
		$ratio = ($heigthOld / $maxHeight);
		$newWidth = ($widthOld / $ratio);
	}

	$thumb = imagecreatetruecolor($newWidth,$newHeight);
	
	if($datos[2]==1){
		imagecolortransparent($thumb, imagecolorallocate($thumb, 0, 0, 0) );
    	imagealphablending($thumb, true);
	}
	elseif($datos[2]==3){
		imagecolortransparent($thumb, imagecolorallocate($thumb, 0, 0, 0) );
    	imagealphablending($thumb, false);
    	imagesavealpha($thumb, true);
	}
	
	imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newWidth, $newHeight, $widthOld, $heigthOld);	
	if($datos[2]==1){header("Content-type: image/gif"); imagegif($thumb);} 
	if($datos[2]==2){header("Content-type: image/jpeg");imagejpeg($thumb);} 
	if($datos[2]==3){header("Content-type: image/png");imagepng($thumb); } 
	imagedestroy($thumb); 
	}
}
?>