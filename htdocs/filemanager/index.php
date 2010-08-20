<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *   	\file       dev/skeletons/skeleton_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *		\version    $Id: index.php,v 1.2 2010/08/20 16:51:58 eldy Exp $
 *		\author		Put author name here
 *		\remarks	Put here some comments
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');	// If there is no menu to show
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');	// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');		// If this page is public (can be called outside logged session)
if (! defined("DISABLE_PROTOTYPE"))      define("DISABLE_PROTOTYPE",'1');		// If this page is public (can be called outside logged session)
if (! defined("DISABLE_SCRIPTACULOUS"))  define("DISABLE_SCRIPTACULOUS",'1');		// If this page is public (can be called outside logged session)
if (! defined("DISABLE_PWC"))            define("DISABLE_PWC",'1');		// If this page is public (can be called outside logged session)


require_once("../filemanager/pre.inc.php");
if (file_exists("./class/filemanagerroots.class.php")) require_once("./class/filemanagerroots.class.php");
else if (file_exists(DOL_DOCUMENT_ROOT."/filemanager/class/filemanagerroots.class.php")) require_once(DOL_DOCUMENT_ROOT."/filemanager/class/filemanagerroots.class.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("ecm");
$langs->load("other");

// Get parameters
$myparam = isset($_GET["myparam"])?$_GET["myparam"]:'';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/








/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

$morejs=array(
"/includes/jquery/jquery.min.js",
"/includes/jquery/jquery-ui-latest.js",
"/includes/jquery/js/jquery.layout-latest.js",
"/filemanager/inc/jqueryFileTree/jqueryFileTree.js",
);
$morecss=array("/filemanager/css/jqueryFileTree.css","/filemanager/css/jqueryLayout.css");
$morehead="<style type=\"text/css\">
html, body {
		width:		100%;
		height:		100%;
		padding:	0;
		margin:		0;
		overflow:	auto; /* when page gets too small */
	}
	#containerlayout {
		background:	#999;
		height:		700px;
		margin:		0 auto;
		width:		100%;
/*		max-width:	900px; */
		min-width:	700px;
		_width:		700px; /* min-width for IE6 */
	}
	.pane {
		display:	none; /* will appear when layout inits */
	}
</style>
<SCRIPT type=\"text/javascript\">
	jQuery(document).ready(function () {
		jQuery('#containerlayout').layout({
			resizable: true
		, 	north__size:         30
		,   north__resizable:   false
		,	west__size:			300
		,	west__minSize:		200
		,	useStateCookie:		false
        ,   west__resizable:    true
			});
	});
</SCRIPT>";

//		,	north__slidable:		false	// OVERRIDE the pane-default of 'slidable=true'
//		,	north__togglerLength_closed: '100%'	// toggle-button is full-width of resizer-bar
//		,	north__spacing_closed:	20		// big resizer-bar when open (zero height)
//		,	west__spacing_open:	0		// no resizer-bar when open (zero height)
//		,	closable:				true	// pane can open & close
//		,	resizable:				true	// when open, pane can be resized
//		,	slidable:				true	// when closed, pane can 'slide' open over other panes - closes on mouse-out


llxHeader($morehead,'MyPageName','','','','',$morejs,$morecss,0,0);

$form=new Form($db);

// Define root to scan
$filemanagerroots=new FilemanagerRoots($db);

if (! empty($_GET["id"]))
{
	$result=$filemanagerroots->fetch($_GET["id"]);
	if (! preg_match('|[\//]$|',$filemanagerroots->rootpath)) $filemanagerroots->rootpath.='/';
}


if (empty($_GET["id"]))
{
	// No root selected
	print $langs->trans("PleaseSelectARoot")."<br>\n";
}
else
{
	print $langs->trans("RootFileManager").': <b>'.$filemanagerroots->rootlabel.'</b> ('.$filemanagerroots->rootpath.')<br>'."\n";
}
print "<br>\n";



// Javascript part
?>
<script type="text/javascript">

function urlencode(s) {
	return s.replace(/\+/gi,'%2B');
}

<?php
if ($filemanagerroots->rootpath)
{
?>
jQuery(document).ready( function() {

	function loadanshowfile(filename)
	{
		/*alert('filename='+filename);*/
		jQuery('#fileview').empty();

		url='<?php echo DOL_URL_ROOT ?>/filemanager/ajaxfilemanager.php?action=download&rootpath=<?php echo $filemanagerroots->id ?>&modulepart=filemanager&type=auto&file='+urlencode(filename);
		jQuery.get(url, function(data) {
  			//alert('Load of url '+url+' was performed : '+data);
  			//alert('Load of url '+url+' was performed');
  			jQuery('#fileview').append(data);
		});
	}

	jQuery('#filetree').fileTree({ root: '<?php echo dol_escape_js($filemanagerroots->rootpath); ?>', script: 'jqueryFileTree.php', folderEvent: 'click', multiFolder: false  }, function(file) {
			loadanshowfile(file);
	});
});
<?php
}
?>
</script>



<div id="containerlayout">
    <div class="pane ui-layout-north">


    </div>

	<div class="pane ui-layout-west">
<?php

// Show filemanager tree
print '<div id="filetree" class="filetree">';
print '</div>';

?>

	</div>

	<div class="pane ui-layout-center">

<?php
print '<div id="fileview" class="fileview">';

if ($filemanagerroots->id) print $langs->trans("SelectAFile");

print '</div>';
?>

	</div>
<!--	<div class="pane ui-layout-east"></div> -->

<!--	<div class="pane ui-layout-south"></div> -->
</div>


<?php
llxFooter();
?>
