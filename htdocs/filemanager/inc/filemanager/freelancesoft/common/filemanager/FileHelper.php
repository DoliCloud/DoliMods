<?php

/**
 * Autor: Martin Bascal Of FreelanceSoft
 * Created on: 20/10/2007
 * Description:
 *    
 *   A file helper. 
 *    
 */

class FileDescription{
	public $path;
	public $httpPath;
	public $isDirectory;
	public $name;
	public $extension;
	public $isImage;
	public $size;
	public $lastTime;
	
	public function FileDescription(){
		
	}
}

class DirectoryDescription{
	public $isDirectory = true;
	public $extension = '';
	public $path;
	public $name;
	public $hasDirectories;
	public $lastTime;
	
	public function DirectoryDescription(){
		
	}
}

class FileHelper{
	
	private static function isImage($ext){
		$ext = strtoupper($ext);
		return $ext == 'TIFF' || $ext == 'GIF' || $ext == 'PNG' || $ext == 'JPG' || $ext == 'BMP' ||
				$ext == 'PCX' || $ext == 'EPS' || $ext == 'PICT';
	}
	
	private static function fileInfoCmp($val1,$val2){
		if($val1->isDirectory && !$val2->isDirectory){
			return -1;
		}
		elseif(!$val1->isDirectory && $val2->isDirectory){
			return 1;
		}
		else{
			return strcmp($val1->name.'.'.$val1->extension,$val2->name.'.'.$val2->extension);
		}
	}
	
	private static function deleteDirectory($dir){
		$handle = opendir($dir);
		while (FALSE !== ($item = readdir($handle))){
			if($item != '.' && $item != '..'){
				$path = $dir.'/'.$item;
				if(is_dir($path)){
					FileHelper::deleteDirectory($path);
				}
				else{
					unlink($path);
				}
			}
		}
		closedir($handle);
		if(!rmdir($dir)){
			return false;
		}
		return true;
	}
	
	private static function deleteFile($file){
		return unlink($file);
	}
	
	public static function filterPath($root,$path,&$realPath,&$newPath){
		$realPath = realpath($root.$path);
/*		if($realPath){
			$l = strlen($root);
			$realRoot = substr($realPath,0,$l);
			if($root == $realRoot){
				$newPath = substr($realPath,$l).'/';
			}
			else{
				$realPath = '';
				$newPath = '/';
			}
		}
		else{
			$realPath = '';
			$newPath = '/';
		}
*/
	}
	
	private static function getFileNumber(&$fileName,&$number){
		$number = 1;
		$len = strlen($fileName);
		if( $len != 0 && $fileName[$len-1] == ')'){
			$i = $len-2;
			while($i != 0 && $fileName[$i] != '('){
				$i--;
			}
			if($i > 0){
				$num = intval(substr($fileName,$i+1,$len-$i-1));
				if(is_int($num) && $num > 0){
					$number = $num+1;
					$fileName = substr($fileName,0,$i);
				}
			}
		}
	}
	
	public static function getFiles($dir){
		if(substr($dir,-1) == '/'){
			$dir = substr($dir,0,-1);
		}
		if(!file_exists($dir) || !is_dir($dir)){
			return false;
		}
		if(!is_readable($dir)){
			return false;
		}

		$dirList = opendir($dir);
		if(!$dirList){
			return false;
		}
		$files = array();
		$dirs = array();
		while(($file = readdir($dirList)) !== false){
			$fileDescription = new FileDescription();
			if($file != '.' && $file != '..'){
				$fileDescription->path = $dir.'/'.$file;
				$fileDescription->isDirectory = is_dir($fileDescription->path);
				$fileDescription->name = substr($file,0,strcspn($file,'.'));
				$ext = substr(strrchr($file, "."),1);
				$fileDescription->extension = $ext ? $ext : '';
				$fileDescription->isImage = FileHelper::isImage($ext);
				$fileDescription->httpPath = str_replace(ROOT,BASE_URL,$fileDescription->path);
				$fileDescription->size = filesize($fileDescription->path);
				$fileDescription->lastTime = fileatime($fileDescription->path);
				$files[] = $fileDescription;
			}
		}
		closedir($dirList);
		usort($files,'FileHelper::fileInfoCmp');
		return $files;
	}
	
	public static function hasDirectories($dir){
		if(substr($dir,-1) == '/'){
			$dir = substr($dir,0,-1);
		}
		if(!file_exists($dir) || !is_dir($dir)){
			return false;
		}
		if(!is_readable($dir)){
			return false;
		}
		$dirList = opendir($dir);
		if(!$dirList){
			return false;
		}
		while(($file = readdir($dirList)) !== false){
			if($file != '.' && $file != '..'){
				$path = $dir.'/'.$file;
				if(is_dir($path)){
					return true;
				}
			}
		}
		return false;
	}
	
	public static function getDirectories($dir){
		if(substr($dir,-1) == '/'){
			$dir = substr($dir,0,-1);
		}
		if(!file_exists($dir) || !is_dir($dir)){
			return false;
		}
		if(!is_readable($dir)){
			return false;
		}
		$dirList = opendir($dir);
		if(!$dirList){
			return false;
		}
		$dirs = array();
		$i = 0;
		while(($file = readdir($dirList)) !== false){
			$absolutePath = $dir.'/'.$file;
			if($file != '.' && $file != '..' && is_dir($absolutePath)){
				$dirDescription = new DirectoryDescription();
				$dirDescription->path = $dir;
				$dirDescription->name = $file;
				$dirDescription->hasDirectories = FileHelper::hasDirectories($absolutePath);
				$dirDescription->lastTime;
				$dirs[$i++] = $dirDescription;
			}
		}
		closedir($dirList);
		usort($dirs,'FileHelper::fileInfoCmp');
		return $dirs;
	}
	
	public static function fileExists($dir){
		return file_exists($dir);	
	}
	
	public static function directoryExists($dir){
		return file_exists($dir) && is_dir($dir);	
	}
	
	public static function createDirectory($dir){
		return mkdir($dir);
	}
	
	public static function delete($file){
		if(substr($file,-1) == '/'){
			$file = substr($file,0,-1);
		}
		if(!file_exists($file) || !is_readable($file)){
			return false;
		}
		elseif(is_dir($file)){
			return FileHelper::deleteDirectory($file);
		}
		else{
			return FileHelper::deleteFile($file);
		}
	}
	
	public static function rename($old,$new){
		return rename($old,$new);
	}
	
	public static function generateName($path,$name,$ext){
		$completePath = $path.'/'.$name.$ext;
		if(!file_exists($completePath)){
			return $name.$ext;
		}
		FileHelper::getFileNumber($name,$i);
		while(true){
			$completePath = "{$path}/{$name}({$i}){$ext}";
			if(!file_exists($completePath)){
				return "{$name}({$i}){$ext}";
			}
			$i++;
		}
	}
	
	public static function copyRec($src,$dest,$base,$remove){
		$completePathSrc = $base.$src;
		$completePathDest = $base.$dest;
		if(!is_dir($completePathSrc)){
			if($remove){
				rename($completePathSrc,$completePathDest);
			}
			else{
				copy($completePathSrc,$completePathDest);
			}
		}
		else{
			$handle = opendir($completePathSrc);
			mkdir($completePathDest);
			while($file = readdir($handle)){
				if($file!="." && $file!=".."){
					FileHelper::copyRec($src.'/'.$file,$dest.'/'.$file,$base,$remove);
				}
			}
			closedir($handle);
			if($remove){
				rmdir($completePathSrc);
			}
		}
		return true; 
	}
}

?>