<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       htdocs/filemanager/index.php
 *		\ingroup    filemanager
 *		\brief      This is home page of filemanager module
 */

if (! defined('REQUIRE_JQUERY_LAYOUT'))     define('REQUIRE_JQUERY_LAYOUT', '1');
if (! defined('REQUIRE_JQUERY_FILEUPLOAD')) define('REQUIRE_JQUERY_FILEUPLOAD', '1');


/**
 * llxHeader
 *
 * @param 	string		$head			Head
 * @param 	string		$title			Title
 * @param 	string		$help_url		Help url
 * @param 	string		$target			Target
 * @param 	int			$disablejs		Disablejs
 * @param 	int			$disablehead	Disablehead
 * @param 	array		$arrayofjs		Array of js
 * @param 	array		$arrayofcss		Array of css
 * @param	string		$morequerystring	Query string to add to the link "print" to get same parameters (use only if autodetect fails)
 * @return	void
 */
function llxHeader($head = '', $title = '', $help_url = '', $target = '', $disablejs = 0, $disablehead = 0, $arrayofjs = '', $arrayofcss = '', $morequerystring = '')
{
	global $db, $user, $conf, $langs;

	top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss);	// Show html headers

	print '<body id="mainbody">' . "\n";

	// top menu and left menu area
	if (empty($conf->global->MAIN_HIDE_TOP_MENU)) {
		top_menu($head, $title, $target, $disablejs, $disablehead, $arrayofjs, $arrayofcss, $morequerystring);
	}


	require_once DOL_DOCUMENT_ROOT.'/core/class/menu.class.php';
	$menu=new Menu();

	$numr=0;

	// Entry for each bank config
	$sql = "SELECT rowid, rootlabel, rootpath";
	$sql.= " FROM ".MAIN_DB_PREFIX."filemanager_roots";
	$sql.= " WHERE entity = ".$conf->entity;
	$sql.= " ORDER BY position";

	$resql = $db->query($sql);
	if ($resql) {
		$numr = $db->num_rows($resql);
		$i = 0;

		if ($numr == 0) {
			$langs->load("errors");
			$menu->add('#', $langs->trans('ErrorModuleSetupNotComplete'), 0, 0);
		}

		while ($i < $numr) {
			$objp = $db->fetch_object($resql);
			$menu->add('/filemanager/index.php?leftmenu=filemanager&id='.$objp->rowid, $objp->rootlabel, 0, 1);
			$i++;
		}
	} else {
		dol_print_error($db);
	}
	$db->free($resql);


	if (empty($conf->global->MAIN_HIDE_LEFT_MENU)) {
		left_menu('', $help_url, '', $menu->liste, 1, $title);
	}

	main_area($title);
}


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

include_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
include_once DOL_DOCUMENT_ROOT."/core/lib/security2.lib.php";
dol_include_once("/filemanager/class/filemanagerroots.class.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("ecm");
$langs->load("other");

// Get parameters
$myparam=GETPOST("myparam");
$openeddir=GETPOST('openeddir');
$id=GETPOST('id', 'int');

// Check permissions
if (! $user->rights->filemanager->read) {
	accessforbidden();
}


/*
 * Actions
 */

if (GETPOST('action')=='deletefile') {
	if (empty($user->rights->filemanager->create)) {
		setEventMessages($langs->trans("NotEnoughPermissions"), null, 'errors');
	} else {
		$filetodelete=GETPOST('file');
		if (! dol_is_file($filetodelete)) {
			$langs->load("errors");
			setEventMessages($langs->trans("ErrorFileNotFound", $filtetodelete), null, 'errors');
		} else {
			$langs->load("other");
			$result=dol_delete_file($filetodelete, 0, 1);
			if ($result) setEventMessages($langs->trans("FileWasRemoved", $filetodelete), null, 'mesgs');
			else {
				$langs->load("errors");
				setEventMessages($langs->trans("ErrorFailToDeleteFile", $filetodelete), null, 'errors');
			}
		}
	}
}

if (GETPOST('action')=='deletedir') {
	if (empty($user->rights->filemanager->create)) {
		setEventMessages($langs->trans("NotEnoughPermissions"), null, 'errors');
	} else {
		$dirtodelete=GETPOST('dir');
		if (! dol_is_dir($dirtodelete)) {
			$langs->load("errors");
			setEventMessages($langs->trans("ErrorDirNotFound", $dirtodelete), null, 'errors');
		} else {
			$result=dol_delete_dir($dirtodelete, 1);
			if ($result) setEventMessages($langs->trans("DirWasRemoved", $dirtodelete), null, 'mesgs');
			else {
				$langs->load("errors");
				setEventMessages($langs->trans("ErrorFailToDeleteDir", $dirtodelete), null, 'errors');
			}
		}
	}
}



/*
 * view
 */

$maxheightwin=(isset($_SESSION["dol_screenheight"]) && $_SESSION["dol_screenheight"] > 500)?($_SESSION["dol_screenheight"]-166):660;

$morecss=array();
$morejs=array(
"/filemanager/includes/jquery/plugins/layout/jquery.layout.js",
"/filemanager/includes/jqueryFileTree/jqueryFileTree.js",
);

$morehead="<style type=\"text/css\">
	#containerlayout {
		height:		".$maxheightwin."px;
		margin:		0 auto;
		width:		100%;
		min-width:	700px;
		_width:		700px; /* min-width for IE6 */
	}
</style>
<SCRIPT type=\"text/javascript\">
	jQuery(document).ready(function () {
		jQuery('#containerlayout').layout({
            name: \"ecmlayout\"
        ,   paneClass:    \"ecm-layout-pane\"
        ,   resizerClass: \"ecm-layout-resizer\"
        ,   togglerClass: \"ecm-layout-toggler\"
		,   center__paneSelector:   \"#ecm-layout-center\"
        ,   north__paneSelector:    \"#ecm-layout-north\"
        ,   west__paneSelector:     \"#ecm-layout-west\"
		,   resizable: true
		, 	north__size:        38
		,   north__resizable:   false
		,   north__closable:    false
		,	west__size:			280
		,	west__minSize:		200
		,   west__slidable:     true
        ,   west__resizable:    true
        ,   west__togglerLength_closed: '100%'
		,	useStateCookie:		true
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


llxHeader($morehead, $langs->trans("FileManager"), '', '', '', '', $morejs, $morecss, 0, 0);

print_fiche_titre($langs->trans("FileManager"));

$form=new Form($db);
// Define root to scan
$filemanagerroots=new FilemanagerRoots($db);

if (empty($id)) {
	$sql = "SELECT";
	$sql.= " t.rowid";
	$sql.= " FROM ".MAIN_DB_PREFIX."filemanager_roots as t";
	$sql.= " WHERE t.entity = ".$conf->entity;

	$resql=$db->query($sql);
	$num=$db->num_rows($resql);
	if ($num ==1) {
		$obj=$db->fetch_object($resql);
		$_GET["id"]=$obj->rowid;
	}
}


if (! empty($id)) {
	$result=$filemanagerroots->fetch($id);
	//$filemanagerroots->rootpath="c:/ee";
	//print "xx".$filemanagerroots->rootpath."ee";
	// Add an end slash
	if (! preg_match('/[\\\\\/]$/', $filemanagerroots->rootpath)) $filemanagerroots->rootpath.='/';
	//print "xx".$filemanagerroots->rootpath."ee";
}


if (empty($id)) {
	// No root selected
	print $langs->trans("PleaseSelectARoot")."<br>\n";
} else {
	print '<span class="opacitymedium">'.$langs->trans("RootFileManager").':</span> <b>'.$filemanagerroots->rootlabel.'</b> ('.$filemanagerroots->rootpath.')<br>'."\n";
}
//print "<br>\n";


print '<div id="mesg" style="margin-bottom: 6px;">'.dol_escape_htmltag($mesg).'</div>';
//setEventMessages($mesg, null);	$mesg contains a div class="ok" so setEventMessages does not work

// Javascript part
// ---------------------------------------------
?>
<script type="text/javascript">
<?php
if ($filemanagerroots->rootpath) {
	?>

	var filediractive='<?php echo $filemanagerroots->rootpath; ?>';
	var filetypeactive='';

	function newdir()
	{
		dirname=filediractive;
		<?php
		// New code using jQuery only
		$formconfirm= '
            var choice=\'ko\';
            jQuery("#dialog-confirm").attr("title", \''.dol_escape_js($langs->transnoentities("NewDir")).'\');
            jQuery("#dialog-confirm").empty();
            jQuery("#dialog-confirm").append(\''.img_help('', '').' '.dol_escape_js($langs->transnoentities("AddDirName")).' <input type="text" id="confirmdirname" name="dirname" value="\'+dirname+\'newdir">\');
            jQuery("#dialog-confirm").append(\'<br>'.dol_escape_js($langs->transnoentities("ServerMustHavePermission", dol_getwebuser('user'), dol_getwebuser('group'))).'\');
            jQuery("#dialog-confirm").dialog({
                autoOpen: true,
                resizable: false,
                height:220,
                width:580,
                modal: true,
                closeOnEscape: false,
                close: function(event, ui) {
                         if (choice == \'ok\') {
                            /* location.href=\''.$_SERVER["PHP_SELF"].'?action=adddir&token='.newToken().'&id='.$id.'&dir=\'+jQuery("#confirmdirname").val(); */
                            url=\''.dol_buildpath('/filemanager/ajaxfileactions.php', 1).'?action=newdir&rootpath='.$filemanagerroots->id.'&modulepart=filemanager&type=auto&file=\'+urlencode(jQuery("#confirmdirname").val());
                            console.log(\'url=\'+url);
                            jQuery.post(url,
                                function(data) {
                                jQuery(\'#mesg\').show();
                                jQuery(\'#mesg\').replaceWith(\'<div id="mesg">\'+data+\'</div>\');
                                }
                            );
                         }
                         if (choice == \'ko\') { }
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

	function newfile()
	{
		//if (filetypeactive == 'directory')
		//{
			filename=filediractive;
			<?php
					// New code using jQuery only
					$formconfirm= '
    		            var choice=\'ko\';
    		            jQuery("#dialog-confirm").attr("title", \''.dol_escape_js($langs->transnoentities("NewFile")).'\');
    		            jQuery("#dialog-confirm").empty();
    		            jQuery("#dialog-confirm").append(\''.img_help('', '').' '.dol_escape_js($langs->transnoentities("AddFileName")).' <input type="text" id="confirmfilename" name="filename" value="\'+filename+\'newfile.txt">\');
    		            jQuery("#dialog-confirm").append(\'<br>'.dol_escape_js($langs->transnoentities("ServerMustHavePermission", dol_getwebuser('user'), dol_getwebuser('group'))).'\');
    		            jQuery("#dialog-confirm").dialog({
    		                autoOpen: true,
    		                resizable: false,
    		                height:220,
    		                width:580,
    		                modal: true,
    		                closeOnEscape: false,
    		                close: function(event, ui) {
    		                         if (choice == \'ok\') {
    		                            /* location.href=\''.$_SERVER["PHP_SELF"].'?action=addfile&token='.newToken().'&id='.$id.'&dir=\'+jQuery("#confirmfilename").val(); */
    		                            url=\''.dol_buildpath('/filemanager/ajaxfileactions.php', 1).'?action=newfile&rootpath='.$filemanagerroots->id.'&modulepart=filemanager&type=auto&file=\'+urlencode(jQuery("#confirmfilename").val());
    		                            console.log(\'url=\'+url);
    		                            jQuery.post(url,
    		                                function(data) {
    		                                jQuery(\'#mesg\').show();
    		                                jQuery(\'#mesg\').replaceWith(\'<div id="mesg">\'+data+\'</div>\');
    		                                }
    		                            );
    		                         }
    		                         if (choice == \'ko\') { }
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
		//}
	}

	// js function to ask confirm to delete dir
	function deletedir()
	{
		if (filetypeactive == 'directory')
		{
			dirname=filediractive;
		<?php
		// New code using jQuery only
		$formconfirm= '
            var choice=\'ko\';
            jQuery("#dialog-confirm").attr("title", \''.dol_escape_js($langs->transnoentities("DeleteDir")).'\');
            jQuery("#dialog-confirm").empty();
            jQuery("#dialog-confirm").append(\''.img_help('', '').' '.dol_escape_js($langs->transnoentities("DeleteDirName")).' <b>\'+dirname+\'</b>\');
            jQuery("#dialog-confirm").append(\'<br>'.dol_escape_js($langs->transnoentities("ServerMustHavePermission", dol_getwebuser('user'), dol_getwebuser('group'))).'\');
            jQuery("#dialog-confirm").dialog({
                autoOpen: true,
                resizable: false,
                height:220,
                width:580,
                modal: true,
                closeOnEscape: false,
                close: function(event, ui) {
                         if (choice == \'ok\') {
                            location.href=\''.$_SERVER["PHP_SELF"].'?action=deletedir&token='.newToken().'&id='.$id.'&dir=\'+urlencode(dirname);
                         }
                         if (choice == \'ko\') { }
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
	}

	function deletefile()
	{
		if (filetypeactive == 'file')
		{
			filename=filediractive;
		<?php
			// New code using jQuery only
			$formconfirm= '
	            var choice=\'ko\';
                jQuery("#dialog-confirm").attr("title", \''.dol_escape_js($langs->transnoentities("DeleteFile")).'\');
	            jQuery("#dialog-confirm").empty();
	            jQuery("#dialog-confirm").append(\''.img_help('', '').' '.dol_escape_js($langs->transnoentities("DeleteFileName")).' <b>\'+filename+\'</b>\');
	            jQuery("#dialog-confirm").append(\'<br>'.dol_escape_js($langs->transnoentities("ServerMustHavePermission", dol_getwebuser('user'), dol_getwebuser('group'))).'\');
	            jQuery("#dialog-confirm").dialog({
	                autoOpen: true,
	                resizable: false,
	                height:220,
	                width:580,
	                modal: true,
	                closeOnEscape: false,
	                close: function(event, ui) {
	                     if (choice == \'ok\') {
                            location.href=\''.$_SERVER["PHP_SELF"].'?action=deletefile&token='.newToken().'&id='.((int) $id).'&file=\'+urlencode(filename);
	                     }
	                     if (choice == \'ko\') { }
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
	}

	function savefile()
	{
		if (filetypeactive == 'file')
		{
			filename=filediractive;
			content=jQuery('#fmeditor').val();
			textformat=jQuery('#textformat').val();
			if (content)
			{
				// TODO Save content
				//alert(content);
				url='<?php echo dol_buildpath('/filemanager/ajaxfileactions.php', 1); ?>?action=save&token=<?php echo newToken(); ?>&rootpath=<?php echo $filemanagerroots->id ?>&modulepart=filemanager&type=auto&file='+urlencode(filename);
				// jQuery.post("test.php", $("#testform").serialize());
				jQuery.post(url, { action: 'save', str: content, sizeofcontent: content.length, textformat: textformat },
					function(data) {
					jQuery('#mesg').show();
					jQuery('#mesg').replaceWith('<div id="mesg">'+data+'</div>');
					}
				);
			}
		}
	}

	function loadandeditcontent()
	{
		if (filetypeactive == 'file')
		{
			filename=filediractive;       /* Get current filename */

			/*alert('filename='+filename);*/
			jQuery('#fileview').empty();
			jQuery('#asavefile').attr('href','#').animate({ opacity: 1 }, "fast");
			jQuery('#aloadandeditcontent').removeAttr('href').animate({ opacity: 0.2 }, "fast");

			if (filename != '')
			{
				url='<?php  echo dol_buildpath('/filemanager/ajaxfileactions.php', 1);  ?>?action=edit&token=<?php echo newToken(); ?>&rootpath=<?php echo $filemanagerroots->id ?>&modulepart=filemanager&type=auto&file='+urlencode(filename);
				jQuery.get(url, function(data) {
					// alert('Load of url '+url+' was performed : '+data);
					  jQuery('#fileview').append(data);
				});
			}
			else
			{
				jQuery('#fileview').append('<?php echo dol_escape_js($langs->trans("SelectAFile")); ?>');
			}
		}
	}


	function loadandshowpreview(filedirname, element)
	{
		//alert('filename='+filename);
		//console.log(element);
		jQuery('#fileview').empty();

		url='<?php echo dol_buildpath('/filemanager/ajaxshowpreview.php', 1); ?>?action=preview&rootpath=<?php echo $filemanagerroots->id ?>&modulepart=filemanager&type=auto&file='+urlencode(filedirname);

		jQuery.get(url, function(data) {
			//alert('Load of url '+url+' was performed : '+data);
			pos=data.indexOf("TYPE=directory",0);
			//alert(pos);
			if ((pos > 0) && (pos < 20))
			{
				filediractive=filedirname;    // Save current dirname
				filetypeactive='directory';
				jQuery('.fmbuttondir').attr('href','#').animate({ opacity: 1 }, "fast");
				jQuery('.fmbuttonfile').removeAttr('href').animate({ opacity: 0.2 }, "fast");
			}
			else
			{
				filediractive=filedirname;    // Save current dirname
				filetypeactive='file';
				jQuery('.fmbuttondir').removeAttr('href').animate({ opacity: 0.2 }, "fast");
				jQuery('#asavefile').removeAttr('href').animate({ opacity: 0.2 }, "fast");
				jQuery('.fmbuttonfile').attr('href','#').animate({ opacity: 1 }, "fast");
			}
			//filetype='dir';
			jQuery('#fileview').append(data);
		});
	}
	<?php
}
?>

	// Init content of tree
	// --------------------
	jQuery(document).ready( function() {
		<?php if ($filemanagerroots->rootpath) { ?>
			jQuery('#filetree').fileTree(
						{ root: '<?php echo dol_escape_js($filemanagerroots->rootpath); ?>',
						script: 'ajaxFileTree.php?openeddir=<?php echo urlencode($openeddir); ?>',		// Called when we click onto a dir (loadandshowpreview is on the onClick of the a tag of dir)
						folderEvent: 'click',
						multiFolder: false },
						function(file) {																// Called when we click onto a file
							console.log("We click on a file");
						}
			);
		<?php } ?>

		<?php if (! $filemanagerroots->rootpath) { ?>
		jQuery("#anewdir").removeAttr('href').animate({ opacity: 0.2 }, "fast");
		<?php } else { ?>
		jQuery("#anewdir").attr('href','#').animate({ opacity: 1 }, "fast");
		<?php } ?>
		<?php if (! $filemanagerroots->rootpath) { ?>
		jQuery("#anewfile").removeAttr('href').animate({ opacity: 0.2 }, "fast");
		<?php } else { ?>
		jQuery("#anewfile").attr('href','#').animate({ opacity: 1 }, "fast");
		<?php } ?>
		jQuery("#adeletedir").removeAttr('href').animate({ opacity: 0.2 }, "fast");
		jQuery("#asavefile").removeAttr('href').animate({ opacity: 0.2 }, "fast");
		jQuery("#aloadandeditcontent").removeAttr('href').animate({ opacity: 0.2 }, "fast");
		jQuery("#adeletefile").removeAttr('href').animate({ opacity: 0.2 }, "fast");
		<?php
		if ($user->rights->filemanager->create) {
			?>
		jQuery("#anewdir").click(function() {
			console.log("anewdir click");
			//res=confirm('<?php echo dol_escape_js($langs->transnoentitiesnoconv("CreateNewDir")); ?>: '+filediractive+'/newdir');
			   //if (res) newdir('newdir');
			   newdir();
		});
		jQuery("#adeletedir").click(function() {
			console.log("adeletedir click");
			deletedir();
		});
		jQuery("#anewfile").click(function() {
			console.log("anewfiler click");
			newfile();
		});
		jQuery("#asavefile").click(function() {
			console.log("asavefile click");
			savefile();
		});
		jQuery("#aloadandeditcontent").click(function() {
			console.log("aloadandeditcontent click");
			loadandeditcontent();
		});
		jQuery("#adeletefile").click(function() {
			console.log("adeletefile click");
			deletefile();
		});
			<?php
		}
		?>
	});


/* Hide toolbar */
jQuery(document).ready( function() {
	jQuery("#dialog-confirm").hide();
});

</script>

<?php
print '<div id="dialog-confirm" title="NOTITLE">';
print img_help('', '').' NOTEXT';
print '</div>'."\n";
?>


<div id="containerlayout"> <!-- begin div id="containerlayout" -->
	<div id="ecm-layout-north" class="hidden toolbar largebutton">
<?php
// Toolbar
print '<div class="toolbarbutton">';
print '<a href="#" id="anewdir" disabled="disabled" class="toolbarbutton fmbuttondir" title="'.dol_escape_htmltag($langs->transnoentities("NewDir")).'"><img border="0" class="toolbarbutton" src="'.dol_buildpath('/filemanager/images/folder-new.png', 1).'"></a>'."\n";
print '<a href="#" id="adeletedir" class="toolbarbutton fmbuttondir" title="'.dol_escape_htmltag($langs->transnoentities("DeleteDir")).'"><img border="0" class="toolbarbutton" src="'.dol_buildpath('/filemanager/images/folder-delete.png', 1).'"></a>'."\n";
print '<a href="#" id="anewfile" class="toolbarbutton fmbuttondir" title="'.dol_escape_htmltag($langs->transnoentities("NewFile")).'"><img border="0" class="toolbarbutton" src="'.dol_buildpath('/filemanager/images/document-new.png', 1).'"></a>'."\n";
print '<a href="#" id="asavefile" class="toolbarbutton fmbuttonsave" title="'.dol_escape_htmltag($langs->transnoentities("Save")).'"><img border="0" class="toolbarbutton" src="'.dol_buildpath('/filemanager/images/media-floppy.png', 1).'"></a>'."\n";
print '<a href="#" id="aloadandeditcontent" class="toolbarbutton fmbuttonfile" title="'.dol_escape_htmltag($langs->transnoentities("Edit")).'"><img border="0" class="toolbarbutton" src="'.dol_buildpath('/filemanager/images/edit-copy.png', 1).'"></a>'."\n";
print '<a href="#" id="adeletefile" class="toolbarbutton fmbuttonfile" title="'.dol_escape_htmltag($langs->transnoentities("DeleteFile")).'"><img border="0" class="toolbarbutton" src="'.dol_buildpath('/filemanager/images/document-delete.png', 1).'"></a>'."\n";
print '</div>';
?>
	</div>

	<div id="ecm-layout-west" class="hidden">
<?php

// Show filemanager tree
print '<div id="filetree" class="filetree">';
print '</div>';

?>
	</div>

	<div id="ecm-layout-center" class="hidden">
<?php
print '<div id="fileview" class="fileview">';

if ($filemanagerroots->id) print $langs->trans("SelectAFile");

print '</div>';
?>
	</div>

</div>


<?php
llxFooter();

if (is_object($db)) $db->close();

