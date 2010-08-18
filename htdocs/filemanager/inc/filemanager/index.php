<?php
include_once('config/config.php');
require_once(CLASSES_PATH."exceptions.php");
require_once(MESAGE_MANAGER_PATH."MessageManager.php");
require_once(FREELANCESOFT_FILEMANAGER_PATH.'FileManager.php');
define('FM_ROOT',ROOT.'files'); // NOT use last '/'

$fileManager = new FileManager('fileManagerId',FM_ROOT,
	array(Icon::UP_LEVEL_ICON,
		Icon::SHOW_TREE,
		Icon::REFRESH,
		Icon::SEPARATOR,
		Icon::ADD_DIRECTORY,
		Icon::ADD_FILE,
		Icon::DELETE_FILES,
		Icon::RENAME,
		Icon::SEPARATOR,
		Icon::CUT,
		Icon::COPY,
		Icon::PASTE,
		Icon::SEPARATOR,
		Icon::CHANGE_VIEW,
		Icon::CONFIGURE
		));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Demo FreelanceSoft filemanager</title>

<link rel="stylesheet"
      type="text/css"
      href="style/filemanager.css"/>
      
<script language="javascript" type="text/javascript">
<!--  
   var image_path='<?php echo IMAGE_PATH_URL; ?>';
-->
</script>
<script src="js/moveWindows.js" type="text/javascript"></script>
<script src="js/dialogs_functions.js" type="text/javascript"></script>
<script src="js/fwindow_set.js" type="text/javascript"></script>
<script src="js/fwindows_functions.js" type="text/javascript"></script>
<script src="js/fwindow.js" type="text/javascript"></script>

<?php FileManager_printJS(); ?>

</head>

<body style="overflow:hidden;" onload="<?php $fileManager->printOnloadFunction(); ?>" onresize="<?php $fileManager->printResizeFunction(); ?>">

<?php MessageManager::printHtml(); ?>

<?php $fileManager->printHtml(); ?>

<!-- BEGIN FWINDOWS -->
<div id="alert_dialog_background"></div>
<div id="confirmation_dialog_background"></div>
<div style="position:absolute;left:0px;top:0px;">
<div id="alert_dialog">
  <div id="alert_dialog_head" onmousedown="moveWindows_move('alert_dialog');" onmouseup="moveWindows_leave('alert_dialog');" >
    <div id="alert_dialog_title"></div>
    <div id="alert_dialog_actions"><a href="javascript:closeAlertDialog();FM_hideDialog();"><img src="<?php echo IMAGE_PATH_URL; ?>close.png" border="0" alt="Close" /></a></div>
  </div>
  <div id="alert_dialog_text"></div>
  <div id="alert_dialog_buttons">
    <input name="Accept" type="button" value="<?php echo FM_ALERT_DIALOG_ACCEPT; ?>" onclick="javascript:closeAlertDialog();FM_hideDialog();" />
  </div>
</div>
<div id="confirmation_dialog">
  <div id="confirmation_dialog_head" onMouseDown="moveWindows_move('confirmation_dialog');" onMouseUp="moveWindows_leave('confirmation_dialog');" >
    <div id="confirmation_dialog_title"></div>
    <div id="confirmation_dialog_actions"><a href="javascript:closeConfirmationDialog();FM_hideDialog();"><img src="<?php echo IMAGE_PATH_URL; ?>close.png" alt="Close" /></a></div>
  </div>
  <div id="confirmation_dialog_text"></div>
  <div id="confirmation_dialog_buttons">
    <input name="Accept" type="button" value="<?PHP echo FM_CONFIRM_DIALOG_ACCEPT; ?>" onClick="" />
    <input name="Cancel" type="button" value="<?PHP echo FM_CONFIRM_DIALOG_CANCEL; ?>" onClick="" />
  </div>
</div>
</div>
<div id="fwindows_div"></div>
<!-- END FWINDOWS -->

</body>
</html>