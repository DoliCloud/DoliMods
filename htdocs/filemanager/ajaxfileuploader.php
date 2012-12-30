<?php
/* Copyright (C) 2011-2012	Regis Houssin		<regis@dolibarr.fr>
 * Copyright (C) 2011		Laurent Destailleur	<eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *       \file       htdocs/core/ajax/fileupload.php
 *       \brief      File to return Ajax response on file upload
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1'); // If there is no menu to show
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1'); // If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');       // If this page is public (can be called outside logged session)


$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
include_once(DOL_DOCUMENT_ROOT.'/core/lib/json.lib.php');
include_once(DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php');
if (! $res) die("Include of main fails");

//print_r($_POST);
//print_r($_GET);
$upload_dir=GETPOST('upload_dir');

header('Content-type: application/json');
header('Pragma: no-cache');
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');



switch ($_SERVER['REQUEST_METHOD']) {
	case 'OPTIONS':
		break;
    case 'HEAD':
    case 'GET':
        break;
    case 'POST':
    	if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
			// TODO delete file
    		echo dol_json_encode(array('success'=>1));
        } else {
			$result=dol_move_uploaded_file($_FILES['files']['tmp_name'][0], $upload_dir."/".dol_unescapefile($_FILES['files']['name'][0]), 0, 0, $_FILES['userfile']['error']);

        	$file1 = new stdClass();
        	//$file1->url='http://uuu';
        	//$file1->thumbnail_url='http://ttt';
        	$file1->name=$_FILES['files']['name'][0];
        	$file1->type=dol_mimetype($file1->name);
        	$file1->size=dol_filesize($pathoffile);
        	//$file1->delete_url='ddd';
        	//$file1->delete_type='DELETE';
        	if (is_numeric($result) && $result > 0) {
        		// ok
        	}
        	else
        	{
        		if ($result == -3)
        		{
        			// Test permission
        			if (! is_writable($upload_dir)) $file1->error='ErrorWebServerUserHasNotPermission';
        			else $file1->error='FailedToWriteFileToTargetDir';
        		}
        		else $file1->error='UnkownErrorDuringMove '.$result;
        	}

 	        // This json return format is ok with current version of jquery fileupload
 	        // 	 echo '[{"url":"http://jquery-file-upload.appspot.com/AMIfv95fcu6lggyE5W9TYM78sKEwUny89rCzk6fhT0B_rkp1cJPseoxG3-8eQ5GVYR_bsFUerIWsEWMx0kQ2aNNgg-xsh7_6gWv92YMk6wMl7Gs4fe72RzcCuu4I0JVrTaA9DTi8vGEOrX4PEAR8bfAzyxGZ26P1eA/Julien-Lavergne_reference_medium.jpg","thumbnail_url":"http://lh5.ggpht.com/cTSujaK0enHNYJCIxRnrClnu43eQcDcipY7adGKIJUgZjpPeOSVvuH5De50wGTawLS-thCx6bN0ulyqd4gu7wk1kwINBY4s=s80","name":"Julien-Lavergne_reference_medium.jpg","type":"image/jpeg","size":8738,"delete_url":"http://jquery-file-upload.appspot.com/AMIfv95fcu6lggyE5W9TYM78sKEwUny89rCzk6fhT0B_rkp1cJPseoxG3-8eQ5GVYR_bsFUerIWsEWMx0kQ2aNNgg-xsh7_6gWv92YMk6wMl7Gs4fe72RzcCuu4I0JVrTaA9DTi8vGEOrX4PEAR8bfAzyxGZ26P1eA/Julien-Lavergne_reference_medium.jpg?delete=true","delete_type":"DELETE"}]';
 	        //   echo dol_json_encode(array($file1));
 	        // This json return format should be ok with more recent version:
 	        // 	 echo '{"files":[{"url":"http://jquery-file-upload.appspot.com/AMIfv95fcu6lggyE5W9TYM78sKEwUny89rCzk6fhT0B_rkp1cJPseoxG3-8eQ5GVYR_bsFUerIWsEWMx0kQ2aNNgg-xsh7_6gWv92YMk6wMl7Gs4fe72RzcCuu4I0JVrTaA9DTi8vGEOrX4PEAR8bfAzyxGZ26P1eA/Julien-Lavergne_reference_medium.jpg","thumbnail_url":"http://lh5.ggpht.com/cTSujaK0enHNYJCIxRnrClnu43eQcDcipY7adGKIJUgZjpPeOSVvuH5De50wGTawLS-thCx6bN0ulyqd4gu7wk1kwINBY4s=s80","name":"Julien-Lavergne_reference_medium.jpg","type":"image/jpeg","size":8738,"delete_url":"http://jquery-file-upload.appspot.com/AMIfv95fcu6lggyE5W9TYM78sKEwUny89rCzk6fhT0B_rkp1cJPseoxG3-8eQ5GVYR_bsFUerIWsEWMx0kQ2aNNgg-xsh7_6gWv92YMk6wMl7Gs4fe72RzcCuu4I0JVrTaA9DTi8vGEOrX4PEAR8bfAzyxGZ26P1eA/Julien-Lavergne_reference_medium.jpg?delete=true","delete_type":"DELETE"}]}';
 	    	//   echo dol_json_encode(array('files'=>array($file1)));
 	        echo dol_json_encode(array(0=>$file1));
        }
        break;
    case 'DELETE':
		// TODO delete file
    	echo dol_json_encode(array('success'=>1));
        break;
    default:
        header('HTTP/1.0 405 Method Not Allowed');
        exit;
}

$db->close();

?>