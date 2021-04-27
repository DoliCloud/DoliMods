<?php
/* Copyright (C) 2004-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2010	   Pierre Morin         <pierre.morin@auguria.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *	\file       htdocs/filemanager/ajaxshowpreview.php
 *  \brief      Service to return a HTML preview of a file
 *  			Call of this service is made with URL:
 * 				ajaxpreview.php?action=preview&modulepart=repfichierconcerne&file=pathrelatifdufichier
 */

if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK', '1');
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', 1); // Disables token renewal
if (! defined('NOREQUIREMENU')) define('NOREQUIREMENU', '1');
if (! defined('NOREQUIREHTML')) define('NOREQUIREHTML', '1');
if (! defined('NOREQUIREAJAX')) define('NOREQUIREAJAX', '1');

// C'est un wrapper, donc header vierge
function llxHeader()
{ }

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

dol_include_once("/filemanager/class/filemanagerroots.class.php");
include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
include_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
$id=GETPOST('id', 'int')?GETPOST('id', 'int'):(is_numeric(GETPOST('rootpath'))?GETPOST('rootpath'):'');
$action=GETPOST("action");
$original_file=GETPOST("file");
$modulepart=GETPOST("modulepart");
$urlsource=GETPOST("urlsource");
$rootpath=GETPOST("rootpath");

$langs->load("filemanager@filemanager");
$langs->load("other");

// Suppression de la chaine de caractere ../ dans $original_file
$original_file = str_replace("../", "/", $original_file);
$original_file_osencoded=dol_osencode($original_file);  // New file name encoded in OS encoding charset

// find the subdirectory name as the reference
$refname=basename(dirname($original_file)."/");

// Define root to scan
$filemanagerroots=new FilemanagerRoots($db);

if (! empty($rootpath) && is_numeric($rootpath)) {
	$result=$filemanagerroots->fetch($rootpath);
	//var_dump($filemanagerroots);
	$rootpath=$filemanagerroots->rootpath;
}

// Security checks
$accessallowed=0;
$sqlprotectagainstexternals='';
if ($modulepart) {
	// On fait une verification des droits et on definit le repertoire concerne

	// Wrapping for filemanager
	if ($modulepart == 'filemanager') {
		$dirnameslash=str_replace(array("\\","/"), "/", dirname($original_file));
		$rootpathslash=str_replace(array("\\","/"), "/", $rootpath);
		//print "x".$dirnameslash." - ".preg_quote($rootpathslash,'/');
		if (preg_match('/^'.preg_quote($rootpathslash, '/').'/', $dirnameslash)) {
			$accessallowed=1;
		}
	}
}

// Basic protection (against external users only)
if ($user->societe_id > 0) {
	if ($sqlprotectagainstexternals) {
		$resql = $db->query($sqlprotectagainstexternals);
		if ($resql) {
			$num=$db->num_rows($resql);
			$i=0;
			while ($i < $num) {
				$obj = $db->fetch_object($resql);
				if ($user->societe_id != $obj->fk_soc) {
					$accessallowed=0;
					break;
				}
				$i++;
			}
		}
	}
}

// Security:
// Limite acces si droits non corrects
if (! $accessallowed) accessforbidden();

// Security:
// On interdit les remontees de repertoire ainsi que les pipe dans
// les noms de fichiers.
if (preg_match('/\.\./', $original_file) || preg_match('/[<>|]/', $original_file)) {
	dol_syslog(__FILE__." Refused to deliver file ".$original_file);
	// Do no show plain path in shown error message
	dol_print_error(0, $langs->trans("ErrorFileNameInvalid", GETPOST("file")));
	exit;
}

// Check permissions
if (! $user->rights->filemanager->read) accessforbidden();




/*
 * Action
 */

if ($action == 'remove_file') {   // Remove a file
	clearstatcache();

	dol_syslog(__FILE__." remove $original_file $urlsource", LOG_DEBUG);

	// This test should be useless. We keep it to find bug more easily
	if (! file_exists($original_file_osencoded)) {
		dol_print_error(0, $langs->trans("ErrorFileDoesNotExists", GETPOST("file")));
		exit;
	}

	dol_delete_file($original_file);

	dol_syslog(__FILE__." back to ".urldecode($urlsource), LOG_DEBUG);

	header("Location: ".urldecode($urlsource));

	return;
}



/*
 * View
 */


// Ajout directives pour resoudre bug IE
header('Cache-Control: Public, must-revalidate');
header('Pragma: public');

$filename = basename($original_file_osencoded);
$sizeoffile = filesize($original_file_osencoded);

if (dol_is_dir($original_file)) {
	$type='directory';
} else {
	// Define mime type
	$type = 'application/octet-stream';
	if (GETPOST("type", "alphanohtml") != 'auto') $type=GETPOST("type", "alphanohtml");
	else $type=dol_mimetype($original_file, 'text/plain');
	//print 'X'.$type.'-'.$original_file;exit;
}

clearstatcache();

// Output file on browser
dol_syslog("document.php download $original_file $filename content-type=$type");

// This test if file exists should be useless. We keep it to find bug more easily
if (! file_exists($original_file_osencoded)) {
	dol_print_error(0, $langs->trans("ErrorFileDoesNotExists", $original_file));
	exit;
}

print '<!-- TYPE='.$type.' -->'."\n";
print '<!-- SIZE='.$sizeoffile.' -->'."\n";
print '<!-- Ajax page called with url '.dol_escape_htmltag($_SERVER["PHP_SELF"]).'?'.dol_escape_htmltag($_SERVER["QUERY_STRING"]).' -->'."\n";

// Les drois sont ok et fichier trouve, et fichier texte, on l'envoie
print '<b><font class="liste_titre">'.$langs->trans("Information").'</font></b><br>';
print '<hr>';

// Dir
if ($type == 'directory') {
	print '<table class="nobordernopadding">';
	print '<tr><td>'.$langs->trans("Directory").':</td><td>&nbsp; <b><span class="fmvalue">'.$original_file.'</span></b></td></tr>';

	//print $langs->trans("FullPath").': '.$original_file_osencoded.'<br>';
	//print $langs->trans("Mime-type").': '.$type.'<br>';

	$info=stat($original_file_osencoded);
	//print '<br>'."\n";
	//print $langs->trans("Owner").": ".$info['udi']."<br>\n";
	//print $langs->trans("Group").": ".$info['gdi']."<br>\n";
	//print $langs->trans("Size").": ".dol_print_size($info['size'])."<br>\n";
	print '<tr><td>'.$langs->trans("DateLastAccess").':</td><td>&nbsp; <span class="fmvalue">'.dol_print_date($info['atime'], '%Y-%m-%d %H:%M:%S')."</span></td></tr>\n";
	print '<tr><td>'.$langs->trans("DateLastChange").':</td><td>&nbsp; <span class="fmvalue">'.dol_print_date($info['mtime'], '%Y-%m-%d %H:%M:%S')."</span></td></tr>\n";
	//print $langs->trans("Ctime").": ".$info['ctime']."<br>\n";
	print '<tr><td>'.$langs->trans("Upload").':</td><td>';

	//$formfile=new FormFile($db);
	//$formfile->form_attach_new_file(DOL_URL_ROOT.'/xxx', 'none', 0, '', $user->rights->filemanager->create, 48);

	// PHP post_max_size
	$post_max_size				= ini_get('post_max_size');
	$mul_post_max_size			= substr($post_max_size, -1);
	$mul_post_max_size			= ($mul_post_max_size == 'M' ? 1048576 : ($mul_post_max_size == 'K' ? 1024 : ($mul_post_max_size == 'G' ? 1073741824 : 1)));
	$post_max_size				= $mul_post_max_size * (int) $post_max_size;
	// PHP upload_max_filesize
	$upload_max_filesize		= ini_get('upload_max_filesize');
	$mul_upload_max_filesize	= substr($upload_max_filesize, -1);
	$mul_upload_max_filesize	= ($mul_upload_max_filesize == 'M' ? 1048576 : ($mul_upload_max_filesize == 'K' ? 1024 : ($mul_upload_max_filesize == 'G' ? 1073741824 : 1)));
	$upload_max_filesize		= $mul_upload_max_filesize * (int) $upload_max_filesize;
	// Max file size
	$max_file_size 				= (($post_max_size < $upload_max_filesize) ? $post_max_size : $upload_max_filesize);

	$langs->load("errors");
	?>

	<!-- START PART FOR FILEUPLOAD -->

	<script type="text/javascript">
	var nberror=0;

	window.locale = {
		"fileUploaded": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('FileUploaded')); ?>",
		"maxFileSize": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('FileIsTooBig')); ?>",
		"minFileSize": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('FileIsTooSmall')); ?>",
		   "acceptFileTypes": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('FileTypeNotAllowed')); ?>",
		   "maxNumberOfFiles": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('MaxNumberOfFilesExceeded')); ?>",
		   "uploadedBytes": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('UploadedBytesExceedFileSize')); ?>",
		   "emptyResult": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('EmptyFileUploadResult')); ?>",
		"UnkownErrorDuringMove ErrorFileAlreadyExists": "<?php echo dol_escape_js($langs->transnoentitiesnoconv("ErrorFileAlreadyExists")); ?>",
		"ErrorFileManagerWebServerUserHasNotPermission": "<?php echo dol_escape_js($langs->transnoentitiesnoconv("ErrorFileManagerWebServerUserHasNotPermission")); ?>",
		"error": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('Error')); ?>",
		"start": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('Start')); ?>",
		"cancel": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('Cancel')); ?>",
		"destroy": "<?php echo dol_escape_js($langs->transnoentitiesnoconv('Delete')); ?>"
	};


	$(function () {
		$('#fileupload').on('submit', function (e) {
			// On empêche le navigateur de soumettre le formulaire
			e.preventDefault();

			var $form = $(this);
			var formdata = (window.FormData) ? new FormData($form[0]) : null;
			var data = (formdata !== null) ? formdata : $form.serialize();

			$.ajax({
				url: $form.attr('action'),
				type: $form.attr('method'),
				contentType: false, // obligatoire pour de l'upload
				processData: false, // obligatoire pour de l'upload
				dataType: 'json', // selon le retour attendu
				data: data,
				success: function (response) {
					// La réponse du serveur
					console.log(response);
					if (response[0].error)
					{
						mesg=response[0].error
						if (window.locale[mesg]) mesg=window.locale[mesg];
						alert(mesg);
					}
					else
					{
						loadandshowpreview('<?php echo dol_escape_js($original_file); ?>', null);
						/* alert(window.locale['fileUploaded']); */
					}
				}
			});
		});
	});

	</script>


	<form id="fileupload" method="post" action="<?php echo dol_buildpath('/filemanager/ajaxfileuploader.php', 1); ?>" enctype="multipart/form-data">
		<input type="hidden" name="upload_dir" value="<?php echo $original_file; ?>">
		<input type="file" name="files">
		<button type="submit">OK</button>
	</form>





	<?php


	print "</td></tr>\n";
	print '</table>'."\n";

	print '<br><br>';
	print '<b>'.$langs->trans("Content")."</b><br>\n";
	print '<hr><br>';

	print '<div class="filedirelem"><ul class="filedirelem">'."\n";

	// Return content of dir
	$dircontent=dol_dir_list($original_file, 'all', 0, '', '', 'name', SORT_ASC, 0);
	foreach ($dircontent as $key => $val) {
		if (dol_is_dir($val['name'])) $mimeimg='other.png';
		else $mimeimg=dol_mimetype($val['name'], 'application/octet-stream', 2);

		print '<li class="filedirelem largebutton" style="height: 100px !important;">';
		print '<br><br>';
		print '<img src="'.DOL_URL_ROOT.'/theme/common/mime/'.$mimeimg.'"><br>';
		print dol_nl2br(dol_trunc($val['name'], 24, 'wrap'), 1);
		print '</li>'."\n";
	}

	print '</ul></div>'."\n";
} else {
	print '<table class="nobordernopadding" width="100%">';
	print '<tr><td>'.$langs->trans("File").'</td><td><b><span class="fmvalue wordbreak">'.$original_file.'</span></b></td></tr>';
	print '<tr><td>'.$langs->trans("Mime-type").'</td><td><span class="fmvalue">'.$type.'</span></td>';
	print '<td align="right"><a data-ajax="false" href="'.dol_buildpath('/filemanager/document.php', 1).'?modulepart=filemanager&id='.$id.'&rootpath='.$rootpath.'&file='.urlencode($original_file).'">'.$langs->trans("Download").'</a></td>';
	print '</tr>';

	$info=stat($original_file_osencoded);
	//print '<br>'."\n";
	//print $langs->trans("Owner").": ".$info['udi']."<br>\n";
	//print $langs->trans("Group").": ".$info['gdi']."<br>\n";
	print '<tr><td>'.$langs->trans("Size").'</td><td><span class="fmvalue">'.dol_print_size($info['size'])."</span></td></tr>\n";
	print '<tr><td>'.$langs->trans("DateLastAccess").'</td><td><span class="fmvalue">'.dol_print_date($info['atime'], '%Y-%m-%d %H:%M:%S')."</span></td></tr>\n";
	print '<tr><td>'.$langs->trans("DateLastChange").'</td><td><span class="fmvalue">'.dol_print_date($info['mtime'], '%Y-%m-%d %H:%M:%S')."</span></td></tr>\n";
	//print $langs->trans("Ctime").": ".$info['ctime']."<br>\n";
	$sizearray=array();
	if (preg_match('/image/i', $type)) {
		require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
		$sizearray=dol_getImageSize($original_file_osencoded);
		print '<tr><td>'.$langs->trans("Width").'</td><td><span class="fmvalue">'.$sizearray['width'].'px</span></td></tr>';
		print '<tr><td>'.$langs->trans("Height").'</td><td><span class="fmvalue">'.$sizearray['height'].'px</span></td></tr>';
	}
	print '</table>'."\n";

	// Flush content before preview generation
	flush();    // This send all data to browser. Browser however may wait to have message complete or aborted before showing it.


	// Preview
	$preview=0;
	if (preg_match('/text/i', $type)) {
		$minmem=64;        // Minimum of memory required to use Geshi (color syntax on text files)
		$maxsize=65536;      // Max size of data to read for text preview

		// Define memmax (memory_limit in bytes)
		$memmaxorig=@ini_get("memory_limit");
		$memmax=@ini_get("memory_limit");
		if ($memmaxorig != '') {
			preg_match('/([0-9]+)([a-zA-Z]*)/i', $memmax, $reg);
			if ($reg[2]) {
				if (strtoupper($reg[2]) == 'M') $memmax=$reg[1]*1024*1024;
				if (strtoupper($reg[2]) == 'K') $memmax=$reg[1]*1024;
			}
		}
		//print "memmax php=".$memmax;

		$out='';
		$srclang=dol_mimetype($original_file, 'text/plain', 3);

		if (preg_match('/html/i', $type)) {    // If HTML file
			print '<br><br>';
			print '<b>'.$langs->trans("Preview")."</b><br>\n";
			print '<hr>';

			readfile($original_file_osencoded);
			//$out=file_get_contents($original_file_osencoded);
			//print $out;
		} else // If not a HTML file
		{
			$warn='';

			// Check if we have enough memory for Geshi
			if ($memmax < $minmem*1024*1024) {
				$warn=img_warning().' '.$langs->trans("NotEnoughMemoryForSyntaxColor");
				$warn.=' (Have '.$memmax.' - Need '.$minmem.')';
				$srclang='';    // We disable geshi
			}

			if (empty($conf->global->FILEMANAGER_DISABLE_COLORSYNTAXING)) {
				$warn=' ('.$langs->trans("ColoringDisabled").')';
				$srclang='';    // We disable geshi
			}

			// Try to detect srclang using first line
			if ($type=='text/plain' && empty($srclang)) {    // Try to enhance MIME detection with first line content
				$firstline=@file_get_contents($original_file_osencoded, false, null, 0, 32);
				if ($firstline) {
					$texts = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", strtolower($firstline));
					if (preg_match('/^#!.*\/bin\/(.*)$/', $texts[0], $reg)) {
						$converttogeshicode=array('ksh'=>'bash', 'sh'=>'bash', 'bash'=>'bash');
						$srclang=$converttogeshicode[$reg[1]]?$converttogeshicode[$reg[1]]:$reg[1];
						//print "ee".$srclang;
					}
				}
			}
			if ($srclang=='php') $srclang='php-brief';
			//if ($srclang=='perl') $srclang='';              // Perl seems to be bugged

			if (! empty($srclang)) {
				print '<br><br>';
				print '<b>'.$langs->trans("Preview")."</b> (".$srclang.")<br>\n";
				print '<hr>';

				// Translate with Geshi
				include_once 'includes/geshi/geshi.php';

				$out=file_get_contents($original_file_osencoded, false, null, 0, $maxsize);
				if (! utf8_check($out)) { $isoutf='iso'; $out=utf8_encode($out); }

				$geshi = new GeSHi($out, $srclang);
				$geshi->enable_strict_mode(true);
				$res='';
				$res=$geshi->parse_code();

				//print "zzzzzzzz";
				print $res;
			} else {
				print '<br><br>';
				print '<b>'.$langs->trans("Preview")."</b>";
				if ($warn) print ' '.$warn;
				print "<br>\n";
				print '<hr>';

				$maxlines=25;
				$i=0;$more=0;
				$handle = @fopen($original_file_osencoded, "r");
				if ($handle) {
					while (!feof($handle) && $i < $maxlines) {
						$buffer = fgets($handle, $maxsize);
						if (utf8_check($buffer)) $out.=$buffer."<br>\n";
						else $out.=utf8_encode($buffer)."<br>\n";
						$i++;
					}
					if (!feof($handle)) $more=1;
					fclose($handle);
				} else {
					print '<div class="error">'.$langs->trans("ErrorFailedToOpenFile", $original_file).'</div>';
				}

				print $out;

				print '<br>';

				if ($more) {
					print '<b>...'.$langs->trans("More").'...</b><br>'."\n";
				}
			}
		}
		$preview=1;
	}
	// Preview if image
	if (preg_match('/image/i', $type)) {
		print '<br><br>';
		print '<b>'.$langs->trans("Preview")."</b><br>\n";
		print '<hr><br>';


		print '<center><img';
		if (! empty($sizearray['width']) && $sizearray['width'] > 500) print ' width="500"';
		print ' src="'.dol_buildpath('/filemanager/viewimage.php', 1).'?modulepart=filemanager&file='.urlencode($original_file).'"></center>';
		$preview=1;
	}
	// Preview if video
	if (preg_match('/video/i', $type)) {
		$typecodec='';
		if (preg_match('/ogg/i', $type))     $typecodec=' type=\'video/ogg; codecs="theora, vorbis"\'';  // This works with HTML5 video
		//if (preg_match('/msvideo/i',$type)) $typecodec=' type=\'video/x-msvideo;\'';   // AVI
		//if (preg_match('/webm/i',$type))    $typecodec=' type=\'video/webm; codecs="vp8, vorbis"\'';
		//if (preg_match('/mp4/i',$type))     $typecodec=' type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'';

		if ($typecodec) {
			print '<br><br>';
			print '<b>'.$langs->trans("Preview")."</b><br>\n";
			print '<hr><br>';

			print '<center>';
			print '<video id="movie" style="border: 1px solid #BBB;" width="320" height="240"';
			print ' preload';
			//print ' preload="none"';
			print ' controls';
			print '>';
			print '<source src="'.dol_buildpath('/filemanager/viewimage.php', 1).'?modulepart=filemanager&file='.urlencode($original_file).'&type='.urlencode($type).'"';
			print $typecodec;
			print ' />';
			print '</video>';
			print '</center>';
			/*<video width="320" height="240"';
			print ' src="'.dol_buildpath('/filemanager/viewimage.php',1).'?modulepart=filemanager&file='.urlencode($original_file).'"';
			print '></center>';*/
			$preview=1;
		}
	}
	// No preview
	if (empty($preview)) {
		print '<br><br>';
		print '<b>'.$langs->trans("Preview")."</b><br>\n";
		print '<hr>';

		print $langs->trans("PreviewNotAvailableForThisType");
	}
}

if (is_object($db)) $db->close();

