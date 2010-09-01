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
 *		\version    $Id: index.php,v 1.15 2010/09/01 18:37:08 eldy Exp $
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
$openeddir = GETPOST('openeddir');

// Check permissions
if (! $user->rights->filemanager->read)
{
    accessforbidden();
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
		height:		660px;
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
            center__paneSelector:   \".ui-layout-center\"
        ,   north__paneSelector:    \".ui-layout-north\"
        ,   west__paneSelector:     \".ui-layout-west\"
		,   resizable: true
		, 	north__size:        42
		,   north__resizable:   false
		,   north__closable:    false
		,	west__size:			280
		,	west__minSize:		200
		,   west__slidable:     true
        ,   west__resizable:    true
        ,   west__togglerLength_closed: '100%'
		,	useStateCookie:		true  /* Put this to false for dev */
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


llxHeader($morehead,$langs->trans("FileManager"),'','','','',$morejs,'',0,0);

print_fiche_titre($langs->trans("FileManager"));

$form=new Form($db);

// Define root to scan
$filemanagerroots=new FilemanagerRoots($db);

if (empty($_GET["id"]))
{
    $sql = "SELECT";
    $sql.= " t.rowid";
    $sql.= " FROM ".MAIN_DB_PREFIX."filemanager_roots as t";
    $sql.= " WHERE t.entity = ".$conf->entity;

    $resql=$db->query($sql);
    $num=$db->num_rows($resql);
    if ($num ==1)
    {
        $obj=$db->fetch_object($resql);
        $_GET["id"]=$obj->rowid;
    }
}


if (! empty($_GET["id"]))
{
	$result=$filemanagerroots->fetch($_GET["id"]);
	//$filemanagerroots->rootpath="c:/ee";
    //print "xx".$filemanagerroots->rootpath."ee";
    // Add an end slash
	if (! preg_match('/[\\\\\/]$/',$filemanagerroots->rootpath)) $filemanagerroots->rootpath.='/';
    //print "xx".$filemanagerroots->rootpath."ee";
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
// ---------------------------------------------
?>
<script type="text/javascript">
<?php
if ($filemanagerroots->rootpath)
{
?>
    var fileactive='';
    var diractive='';
    var filetypeactive='';

    function newdir(dirname)
    {
    }

    function newfile(filename)
    {
    }

    function deletedir(dirname)
    {
<?php
        // New code using jQuery only
        $formconfirm= '
            var choice=\'ko\';
            jQuery("#dialog-confirm").attr("title", \''.dol_escape_js($langs->trans("DeleteDir")).'\');
            jQuery("#dialog-confirm").empty();
            jQuery("#dialog-confirm").append(\''.img_help('','').' '.dol_escape_js($langs->trans("DeleteDirName")).' <b>\'+dirname+\'</b>\');
            jQuery("#dialog-confirm").dialog({
                autoOpen: true,
                resizable: false,
                height:160,
                width:580,
                modal: true,
                closeOnEscape: false,
                close: function(event, ui) {
                     if (choice == \'ok\') { alert(\'ok\'); }
                     if (choice == \'ko\') { alert(\'ko\'); }
                  },
                buttons: {
                    \''.dol_escape_js($langs->transnoentities("Yes")).'\': function() {
                         choice=\'ok\';
                        jQuery(this).dialog(\'close\');
                    },
                    \''.dol_escape_js($langs->transnoentities("No")).'\': function() {
                         choice=\'ko\';
                        jQuery(this).dialog(\'close\');
                    }
                }
            });
        ';

        $formconfirm.= "\n";
        print $formconfirm;
?>
    }

    function deletefile(filename)
    {
    	<?php
    	        // New code using jQuery only
    	        $formconfirm= '
    	            var choice=\'ko\';
                    jQuery("#dialog-confirm").attr("title", \''.dol_escape_js($langs->trans("DeleteFile")).'\');
    	            jQuery("#dialog-confirm").empty();
    	            jQuery("#dialog-confirm").append(\''.img_help('','').' '.dol_escape_js($langs->trans("DeleteFileName")).' <b>\'+filename+\'</b>\');
    	            jQuery("#dialog-confirm").dialog({
    	                autoOpen: true,
    	                resizable: false,
    	                height:160,
    	                width:580,
    	                modal: true,
    	                closeOnEscape: false,
    	                close: function(event, ui) {
    	                     if (choice == \'ok\') { alert(\'ok\'); }
    	                     if (choice == \'ko\') { alert(\'ko\'); }
    	                  },
    	                buttons: {
    	                    \''.dol_escape_js($langs->transnoentities("Yes")).'\': function() {
    	                         choice=\'ok\';
    	                        jQuery(this).dialog(\'close\');
    	                    },
    	                    \''.dol_escape_js($langs->transnoentities("No")).'\': function() {
    	                         choice=\'ko\';
    	                        jQuery(this).dialog(\'close\');
    	                    }
    	                }
    	            });
    	        ';

    	        $formconfirm.= "\n";
    	        print $formconfirm;
    	?>
    }

    function savefile(filename)
    {
        content=jQuery('#fmeditor').val();
        // TODO Save content
        alert(content);
    }

    function loadandshowpreview(filename)
    {
        fileactive=filename;    /* Save current filename */

        /*alert('filename='+filename);*/
        jQuery('#fileview').empty();

        url='<?php echo DOL_URL_ROOT ?>/filemanager/ajaxshowpreview.php?action=preview&rootpath=<?php echo $filemanagerroots->id ?>&modulepart=filemanager&type=auto&file='+urlencode(filename);
        jQuery.get(url, function(data) {
            //alert('Load of url '+url+' was performed : '+data);
            pos=data.indexOf("TYPE=directory",0);
            //alert(pos);
            if ((pos > 0) && (pos < 20))
            {
                filetypeactive='directory';
                jQuery('.fmbuttondir').show(0);
                jQuery('.fmbuttonfile').hide(0);
            }
            else
            {
                filetypeactive='file';
                //jQuery('.fmbuttondir').addClass('hidden');
                jQuery('.fmbuttondir').hide(0);
                jQuery('.fmbuttonfile').show(0);
            }
            //filetype='dir';
            jQuery('#fileview').append(data);
        });
    }

    function loadandeditcontent()
	{
    	filename=fileactive;       /* Get current filename */

        /*alert('filename='+filename);*/
        jQuery('#fileview').empty();
        jQuery('#asavefile').show();
        jQuery('#aloadandeditcontent').hide();

        if (filename != '')
        {
    		url='<?php echo DOL_URL_ROOT ?>/filemanager/ajaxeditcontent.php?action=edit&rootpath=<?php echo $filemanagerroots->id ?>&modulepart=filemanager&type=auto&file='+urlencode(filename);
    		jQuery.get(url, function(data) {
                //alert('Load of url '+url+' was performed : '+data);
      			jQuery('#fileview').append(data);
    		});
        }
        else
        {
            jQuery('#fileview').append('<?php echo dol_escape_js($langs->trans("SelectAFile")); ?>');
        }
	}



    // Init content of tree
    // --------------------
    jQuery(document).ready( function() {
        jQuery('#filetree').fileTree({ root: '<?php echo dol_escape_js($filemanagerroots->rootpath); ?>',
                                       script: 'ajaxFileTree.php?openeddir=<?php echo urlencode($openeddir); ?>',
                                       folderEvent: 'click',
                                       multiFolder: false  },
                                      function(file) {
			loadandshowpreview(file);
		});

        jQuery("#anewdir").show();
        jQuery("#anewdir").click(function() {
        });
        jQuery("#adeletedir").hide();
        jQuery("#adeletedir").click(function() {
            deletedir();
        });
        jQuery("#anewfile").show();
        jQuery("#anewfile").click(function() {
            newfile();
        });
        jQuery("#asavefile").hide();
        jQuery("#asavefile").click(function() {
            savefile();
        });
        jQuery("#aloadandeditcontent").hide();
        jQuery("#aloadandeditcontent").click(function() {
        	loadandeditcontent();
        });
        jQuery("#adeletefile").hide();
        jQuery("#adeletefile").click(function() {
        	deletefile(fileactive);
        });
    });

<?php
}
?>
/* Hide toolbar */
jQuery(document).ready( function() {
    jQuery("#dialog-confirm").hide();
});

</script>

<?php
print '<div id="dialog-confirm" title="NOTITLE">';
print img_help('','').' NOTEXT';
print '</div>'."\n";
?>


<div id="containerlayout">
    <div class="pane ui-layout-north toolbar">
<?php
// Toolbar
print '<div class="toolbarbutton">';
print '<a href="#" id="anewdir" class="fmbuttondir" title="'.dol_escape_htmltag($langs->trans("NewDir")).'"><img border="0" width="32" height="32" src="'.DOL_URL_ROOT.'/filemanager/images/folder-new.png"></a>'."\n";
print '<a href="#" id="adeletedir" class="fmbuttondir" title="'.dol_escape_htmltag($langs->trans("DeleteDir")).'"><img border="0" width="32" height="32" src="'.DOL_URL_ROOT.'/filemanager/images/folder-delete.png"></a>'."\n";
print '<a href="#" id="anewfile" title="'.dol_escape_htmltag($langs->trans("NewFile")).'"><img border="0" width="32" height="32" src="'.DOL_URL_ROOT.'/filemanager/images/document-new.png"></a>'."\n";
print '<a href="#" id="asavefile" class="fmbuttonsave" title="'.dol_escape_htmltag($langs->trans("SaveFile")).'"><img border="0" width="32" height="32" src="'.DOL_URL_ROOT.'/filemanager/images/media-floppy.png"></a>'."\n";
print '<a href="#" id="aloadandeditcontent" class="fmbuttonfile" title="'.dol_escape_htmltag($langs->trans("Edit")).'"><img border="0" width="32" height="32" src="'.DOL_URL_ROOT.'/filemanager/images/edit-copy.png"></a>'."\n";
print '<a href="#" id="adeletefile" class="fmbuttonfile" title="'.dol_escape_htmltag($langs->trans("DeleteFile")).'"><img border="0" width="32" height="32" src="'.DOL_URL_ROOT.'/filemanager/images/document-delete.png"></a>'."\n";
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
