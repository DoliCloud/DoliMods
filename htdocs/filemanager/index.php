<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       htdocs/filemanager/index.php
 *		\ingroup    filemanager
 *		\brief      This is home page of filemanager module
 *		\version    $Id: index.php,v 1.6 2010/08/21 17:26:08 eldy Exp $
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
"/includes/jquery/js/jquery.layout-latest.js",
"/filemanager/inc/jqueryFileTree/jqueryFileTree.js",
);
$morecss=array("/filemanager/css/filemanager.css.php");
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
		, 	north__size:         42
		,   north__resizable:   false
		,   north__closable:  false
		,	west__size:			300
		,	west__minSize:		200
		,	useStateCookie:		false  /* Put this to false for dev */
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

print_fiche_titre($langs->trans("FileManager"));

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
<?php
if ($filemanagerroots->rootpath)
{
?>
    var fileactive='';

    function loadandshowpreview(filename)
    {
        fileactive=filename;    /* Save current filename */

        /*alert('filename='+filename);*/
        jQuery('#fileview').empty();

        url='<?php echo DOL_URL_ROOT ?>/filemanager/ajaxshowpreview.php?action=preview&rootpath=<?php echo $filemanagerroots->id ?>&modulepart=filemanager&type=auto&file='+urlencode(filename);
        jQuery.get(url, function(data) {
            //alert('Load of url '+url+' was performed : '+data);
            //alert('Load of url '+url+' was performed');
            jQuery('#fileview').append(data);
        });
    }

    function loadandshowcontent()
	{
    	filename=fileactive;   /* Get current filename */

        /*alert('filename='+filename);*/
		jQuery('#fileview').empty();

		url='<?php echo DOL_URL_ROOT ?>/filemanager/ajaxshowcontent.php?action=view&rootpath=<?php echo $filemanagerroots->id ?>&modulepart=filemanager&type=auto&file='+urlencode(filename);
		jQuery.get(url, function(data) {
  			//alert('Load of url '+url+' was performed : '+data);
  			//alert('Load of url '+url+' was performed');
  			jQuery('#fileview').append(data);
		});
	}

    // Init content of tree
    jQuery(document).ready( function() {
        jQuery('#filetree').fileTree({ root: '<?php echo dol_escape_js($filemanagerroots->rootpath); ?>', script: 'ajaxFileTree.php', folderEvent: 'click', multiFolder: false  }, function(file) {
			loadandshowpreview(file);
	});
});
<?php
}
?>
</script>



<div id="containerlayout">
    <div class="pane ui-layout-north filetoolbar">
<?php
// Toolbar
print '<div class="filetoolbarbutton">';
print '<a href="#" onClick="loadandshowcontent()"><img width="32" height="32" src="'.DOL_URL_ROOT.'/filemanager/images/imagen_pegar.png"></a>';
print '</div>';
?>
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
