<?php

/**
 * Autor: Martin Bascal Of FreelanceSoft
 * Created on: 19/10/2007
 * Description:
 *    
 *    Represents a filemanager.
 *    
 */

require_once(FREELANCESOFT_FILEMANAGER_PATH.'config.php');
require_once(FREELANCESOFT_FILEMANAGER_PATH.'Icon.php');
require_once(XAJAX_SERVER_PATH.'FileManager.php');

define('FM_ALERT_DIALOG_INDEX',FM_ALERT_DIALOGS_FREE_INDEX);
define('FM_CONFIRM_DIALOG_INDEX',FM_CONFIRMATION_DIALOGS_FREE_INDEX);
define('FM_UPLOAD_DIALOG_INDEX',FM_WINDOWS_FREE_INDEX);
define('FM_CREATE_DIRECTORY_INDEX',FM_WINDOWS_FREE_INDEX+1);
define('FM_RENAME_INDEX',FM_WINDOWS_FREE_INDEX+2);
define('FM_COPY_INDEX',FM_WINDOWS_FREE_INDEX+3);
define('FM_CONFIGURE_INDEX',FM_WINDOWS_FREE_INDEX+4);
define('FM_TEMPLATE_LOCATION_BAR',
	'	<div class="'.FM_CSS_LOCATION_BAR.'" align="center" style="border:0px;margin:0px;padding:4px;width:100%%;">'."\n".
	'		<form id="%s_form_locationbar" name="%s_form_locationbar" onsubmit="fm_%s.changeLocation();return false;" method="post" action="" style="border:0px;margin:0px;padding:0px;overflow:hidden;height:'.(FM_LOCATION_BAR_HEIGHT-4).'px;width:100%%;">'."\n".
	'			<table border="0" cellspacing="0" cellpadding="0" width="100%%">'."\n".
	'				<tr>'."\n".
	'					<td align="left" width="95%%">'."\n".
	'						<input id="%s_input_locationbar" class="'.FM_CSS_LOCATION_BAR_INPUT.'" name="" type="text" />'."\n".
	'					</td>'."\n".
	'					<td align="center" width="5%%">'."\n".
	'						<input name="" type="image" width="'.FM_CHANGE_LOCATION_ICON_WIDTH.'" height="'.FM_CHANGE_LOCATION_ICON_HEIGHT.'" src="'.IMAGE_PATH_URL.'imagen_ir.png" title="'.FM_TITLE_GO_TO_LOCATION.'" />'."\n".
	'					</td>'."\n".
	'				</tr>'."\n".
	'			</table>'."\n".
	'		</form>'."\n".
	'	</div>'."\n");
define('FM_TEMPLATE_CREATE_DIRECTORY',
	'<table width="100%%" height="100%%" border="0" cellspacing="0" cellpadding="0" onsubmit="fm_%s.createDirectory(); closeWindow('.FM_CREATE_DIRECTORY_INDEX.'); return false;">'.
		'<tr>'.
			'<td align="center" valign="middle">'.
				'<form id="%s_createDirectoryForm" name="%s_createDirectoryForm" method="post" action="" style="border:0px;margin:0px;padding:0px;">'.
					'<div>'.FM_CREATE_DIRECTORY_NAME.' <input id="%s_createDirectoryinput" name="%s_createDirectoryinput"  type="text" class="'.FM_CSS_DIALOGS_TEXTFIELD.'"/></div>'.
					'<div style="height:15px;"></div>'.
					'<div><input type="submit" value="'.FM_CREATE_DIRECTORY_BUTTON_TEXT.'" class="'.FM_CSS_DIALOGS_BUTTON.'" /></div>'.
				'</form>'.
			'</td>'.
		'</tr>'.
	'</table>');
define('FM_TEMPLATE_RENAME',
	'<table width="100%%" height="100%%" border="0" cellspacing="0" cellpadding="0">'.
		'<tr>'.
			'<td align="center" valign="middle">'.
				'<form id="%s_renameForm" name="%s_renameForm" method="post" action="" style="border:0px;margin:0px;padding:0px;" onsubmit="closeWindow('.FM_RENAME_INDEX.'); fm_%s.rename(); return false;">'.
					'<div>'.FM_RENAME_NEW_NAME.' <input id="%s_renameinput" name="%s_renameinput"  type="text" class="'.FM_CSS_DIALOGS_TEXTFIELD.'"/></div>'.
					'<div style="height:15px;"></div>'.
					'<div><input type="submit" value="'.FM_RENAME_BUTTON_TEXT.'" class="'.FM_CSS_DIALOGS_BUTTON.'" /></div>'.
				'</form>'.
			'</td>'.
		'</tr>'.
	'</table>');
define('FM_TEMPLATE_COPY',
	'<table width="100%%" height="100%%" border="0" cellspacing="0" cellpadding="0">'.
		'<tr>'.
			'<td align="center" valign="middle">'.
				'<div id=\"%s_copy_file\"></div>'.
			'</td>'.
		'</tr>'.
	'</table>');
define('FM_TEMPLATE_CONFIGURE',
	'<form id="%s_form_configure" name="%s_form_configure" method="post" action="" style="border:0px;margin:0px;padding:0px;" onsubmit="fm_%s.configure(); return false;">'.
	  '<table width="100%%" height="100%%" border="0" cellspacing="0" cellpadding="0" class="createDirectory">'.
			'<tr>'.
				'<td align="center" valign="middle">'.
					'<table width="100%%" border="0" cellspacing="2" cellpadding="2">'.
						'<tr>'.
							'<td width="100%%" align="center" colspan="2" class="'.FM_CSS_WINDOW_SECTION_TITLE.'">'.FM_CONFIGURE_SECTION_IMAGE.'</td>'.
						'</tr>'.
						'<tr>'.
							'<td width="50%%" align="right">'.FM_CONFIGURE_THUMBS_SIZE.'</td>'.
							'<td width="50%%" align="left">'.
								'<input id="%s_input_thumbs" name="%s_input_thumbs"  type="text" class="'.FM_CSS_DIALOGS_TEXTFIELD.'" value="'.FM_IMAGE_VIEW_IMAGE_SIZE.'"/>'.
								'<div id="%s_validate_thumbs" name="%s_validate_thumbs" class="'.FM_CSS_DYNAMIC_ERROR.'" style="display:none;">'.FM_ERROR_INVALID.'</div>'.
							'</td>'.
						'</tr>'.
						'<tr>'.
							'<td width="100%%" align="center" colspan="2" class="'.FM_CSS_WINDOW_SECTION_TITLE.'">'.FM_CONFIGURE_SECTION_DETAILS.'</td>'.
						'</tr>'.
						'<tr>'.
							'<td width="50%%" align="right">'.FM_CONFIGURE_DETAIL_ICON.'</td>'.
							'<td width="50%%" align="left">'.
								'<input id="%s_input_detail_icon" name="%s_input_detail_icon"  type="text" class="'.FM_CSS_DIALOGS_TEXTFIELD.'" value="'.FM_DETAILED_VIEW_ELEMENT_HEIGHT.'"/>'.
								'<div id="%s_validate_detail_icon" name="%s_validate_detail_icon" class="'.FM_CSS_DYNAMIC_ERROR.'" style="display:none;">'.FM_ERROR_INVALID.'</div>'.
							'</td>'.
						'</tr>'.
						'<tr>'.
							'<td width="50%%" align="right">'.FM_CONFIGURE_PERCENT_NAME.'</td>'.
							'<td width="50%%" align="left">'.
								'<input id="%s_input_percent_name" name="%s_input_percent_name"  type="text" class="'.FM_CSS_DIALOGS_TEXTFIELD.'" value="'.FM_DETAILED_VIEW_NAME_PERCENT.'"/>'.
								'<div id="%s_validate_percent_name" name="%s_validate_percent_name" class="'.FM_CSS_DYNAMIC_ERROR.'" style="display:none;">'.FM_ERROR_INVALID.'</div>'.
							'</td>'.
						'</tr>'.
						'<tr>'.
							'<td width="50%%" align="right">'.FM_CONFIGURE_PERCENT_EXT.'</td>'.
							'<td width="50%%" align="left">'.
								'<input id="%s_input_percent_ext" name="%s_input_percent_ext"  type="text" class="'.FM_CSS_DIALOGS_TEXTFIELD.'" value="'.FM_DETAILED_VIEW_EXTENSION_PERCENT.'"/>'.
								'<div id="%s_validate_percent_ext" name="%s_validate_percent_ext" class="'.FM_CSS_DYNAMIC_ERROR.'" style="display:none;">'.FM_ERROR_INVALID.'</div>'.
							'</td>'.
						'</tr>'.
						'<tr>'.
							'<td width="50%%" align="right">'.FM_CONFIGURE_PERCENT_LAST_ACCESS.'</td>'.
							'<td width="50%%" align="left">'.
								'<input id="%s_input_percent_last_access" name="%s_input_percent_last_access"  type="text" class="'.FM_CSS_DIALOGS_TEXTFIELD.'" value="'.FM_DETAILED_VIEW_LAST_TIME_PERCENT.'"/>'.
								'<div id="%s_validate_percent_last_access" name="%s_validate_percent_last_access" class="'.FM_CSS_DYNAMIC_ERROR.'" style="display:none;">'.FM_ERROR_INVALID.'</div>'.
							'</td>'.
						'</tr>'.
						'<tr>'.
							'<td width="50%%" align="right">'.FM_CONFIGURE_PERCENT_SIZE.'</td>'.
							'<td width="50%%" align="left">'.
								'<input id="%s_input_percent_size" name="%s_input_percent_size"  type="text" class="'.FM_CSS_DIALOGS_TEXTFIELD.'" value="'.FM_DETAILED_VIEW_SIZE_PERCENT.'"/>'.
								'<div id="%s_validate_percent_size" name="%s_validate_percent_size" class="'.FM_CSS_DYNAMIC_ERROR.'" style="display:none;">'.FM_ERROR_INVALID.'</div>'.
							'</td>'.
						'</tr>'.
						'<tr>'.
							'<td width="100%%" align="center" colspan="2">'.
								'<div id="%s_validate_percent_total" name="%s_validate_percent_total" class="'.FM_CSS_DYNAMIC_ERROR.'" style="display:none;">'.FM_ERROR_DETAILED_PERCENT.'</div>'.
							'</td>'.
						'</tr>'.
					'</table>'.
						'<div>'.
							'<input type="submit" value="'.FM_CONFIGURE_ACCEPT_BUTTON.'" class="'.FM_CSS_DIALOGS_BUTTON.'" />'.
						'</div>'.
				'</td>'.
			'</tr>'.
		'</table>'.
	'</form>');

if(!isset($_SESSION)) session_start();

class FileManager{
	private $_id;
	private $_root;
	private $_view;
	private $_showTree;
	private $_showLocationBar;
	private $_showIconBar;
	private $_icons;
		
	
	/**
	 * Image view.
	 *
	 * @var string
	 */
	const VIEW_IMAGE = 'ImageView';
	const VIEW_DETAILED = 'DetailedView';
	
	private function getWinsowsJs(){
		$contentCreateDirectory = sprintf(FM_TEMPLATE_CREATE_DIRECTORY,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id);
		$contentRename = sprintf(FM_TEMPLATE_RENAME,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id);
		$contentCopy = sprintf(FM_TEMPLATE_COPY,$this->_id);
		$contentConfigure = sprintf(FM_TEMPLATE_CONFIGURE,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,
			$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,
			$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,$this->_id,
			$this->_id,$this->_id,$this->_id,$this->_id);
		return
		"		addAlertDialog(".FM_ALERT_DIALOG_INDEX.", '".FM_ALERT_DIALOG_TITLE."', '');\n".
		"		fwindow_Set.addWindow(".FM_UPLOAD_DIALOG_INDEX.",new FWindow(".FM_UPLOAD_DIALOG_INDEX.", '".FM_WINDOW_ADD_FILES_TITLE."','<div><a href=\"#\" onclick=\"fm_{$this->_id}.addUploadForm();return false;\" class=\"linkUpload\" >".FM_WINDOW_ADD_FILES_MORE."</a></div><div style=\"height:10px;border-bottom:1px dashed #000000\"></div><div id=\'{$this->_id}_next_upload_0\'></div>','fwindows_div', true));\n".
		"		var uploadWindow{$this->_id} = fwindow_Set.getWindow(".FM_UPLOAD_DIALOG_INDEX.");\n". 
	   	"		uploadWindow{$this->_id}.setDimension(400,300);\n".
	   	"		uploadWindow{$this->_id}.toCenterPosition();\n".
	   	"		fm_{$this->_id}.setUploadFileIndex(".FM_UPLOAD_DIALOG_INDEX.");\n".
		"		fm_{$this->_id}.addUploadForm();\n".
		"		fwindow_Set.addWindow(".FM_CREATE_DIRECTORY_INDEX.",new FWindow(".FM_CREATE_DIRECTORY_INDEX.", '".FM_CREATE_DIRECTORY_TITLE."','{$contentCreateDirectory}','fwindows_div', true));\n".
		"		var createDirectoryWindow{$this->_id} = fwindow_Set.getWindow(".FM_CREATE_DIRECTORY_INDEX.");\n". 
	   	"		createDirectoryWindow{$this->_id}.setDimension(400,200);\n".
	   	"		createDirectoryWindow{$this->_id}.toCenterPosition();\n".
	   	"		fm_{$this->_id}.setCreateDirectoryIndex(".FM_CREATE_DIRECTORY_INDEX.");\n".
		"		fwindow_Set.addWindow(".FM_RENAME_INDEX.",new FWindow(".FM_RENAME_INDEX.", '".FM_RENAME_TITLE."','{$contentRename}','fwindows_div', true));\n".
		"		var renameWindow{$this->_id} = fwindow_Set.getWindow(".FM_RENAME_INDEX.");\n". 
	   	"		renameWindow{$this->_id}.setDimension(400,200);\n".
	   	"		renameWindow{$this->_id}.toCenterPosition();\n".
	   	"		fm_{$this->_id}.setRenameIndex(".FM_RENAME_INDEX.");\n".
		"		fwindow_Set.addWindow(".FM_COPY_INDEX.",new FWindow(".FM_COPY_INDEX.", '".FM_COPY_TITLE."','{$contentCopy}','fwindows_div', true));\n".
		"		var copyWindow{$this->_id} = fwindow_Set.getWindow(".FM_COPY_INDEX.");\n". 
	   	"		copyWindow{$this->_id}.setDimension(400,100);\n".
	   	"		copyWindow{$this->_id}.toCenterPosition();\n".
	   	"		fm_{$this->_id}.setCopyIndex(".FM_COPY_INDEX.");\n".
		"		fwindow_Set.addWindow(".FM_CONFIGURE_INDEX.",new FWindow(".FM_CONFIGURE_INDEX.", '".FM_CONFIGURE_TITLE."','{$contentConfigure}','fwindows_div', true));\n".
		"		var configureWindow{$this->_id} = fwindow_Set.getWindow(".FM_CONFIGURE_INDEX.");\n". 
	   	"		configureWindow{$this->_id}.setDimension(500,450);\n".
	   	"		configureWindow{$this->_id}.toCenterPosition();\n".
	   	"		fm_{$this->_id}.setConfigureIndex(".FM_CONFIGURE_INDEX.");\n";
	}
	
	/**
	 * Gets Id.
	 *
	 * @return string
	 */
	public function getId(){
		return $this->_id;
	}
	
	/**
	 * Sets Id.
	 *
	 * @param string $value
	 */
	public function setId($value){
		$this->_id = $value;
	}
	
	/**
	 * Gets show tree.
	 *
	 * @return bool
	 */
	public function isShowTree(){
		return $this->_showTree;
	}
	
	/**
	 * Sets show tree.
	 *
	 * @param bool $value
	 */
	public function showTree($value){
		$this->_showTree = $value;
	}
	
	/**
	 * Gets show location bar.
	 *
	 * @return bool
	 */
	public function isShowLocationBar(){
		return $this->_showLocationBar;
	}
	
	/**
	 * Sets show location bar.
	 *
	 * @param bool $value
	 */
	public function showLocationBar($value){
		$this->_showLocationBar = $value;
	}
	
	/**
	 * Gets show icon bar.
	 *
	 * @return bool
	 */
	public function isShowIconBar(){
		return $this->_showIconBar;
	}
	
	/**
	 * Sets show icon bar.
	 *
	 * @param bool $value
	 */
	public function showIconBar($value){
		$this->_showIconBar = $value;
	}
	
	/**
	 * Gets root.
	 *
	 * @return string
	 */
	public function getRoot(){
		return $this->_root;
	}
	
	/**
	 * Sets root.
	 *
	 * @param string $value
	 */
	public function setRoot($value){
		$this->_root = $value;
	}
	
	/**
	 * Gets view.
	 *
	 * @return string
	 */
	public function getView(){
		return $this->_view;
	}
	
	/**
	 * Sets view.
	 *
	 * @param string $value
	 */
	public function setView($value){
		$this->_view = $value;
	}
	
	public function getJS(){
		$FM_AUTOSIZE = FM_AUTOSIZE ? 'true' : 'false';
		$FM_WIDTH = FM_WIDTH;
		$FM_HEIGHT = FM_HEIGHT;
		$FM_IMAGE_VIEW_IMAGE_SIZE = FM_IMAGE_VIEW_IMAGE_SIZE;
		$FM_IMAGE_VIEW_BORDER_SIZE = FM_IMAGE_VIEW_BORDER_SIZE;
		$FM_TREE_FOLDER_HEIGHT = FM_TREE_FOLDER_HEIGHT;
		$FM_FOLDER_ICON = FM_FOLDER_ICON;
		$FM_FOLDER_UP_ICON = FM_FOLDER_UP_ICON;
		$FM_FILE_ICON = FM_FILE_ICON;
		$FM_TREE_PLUS_ICON = FM_TREE_PLUS_ICON;
		$FM_TREE_MINUS_ICON = FM_TREE_MINUS_ICON;
		$FM_TREE_FOLDER_ICON = FM_TREE_FOLDER_ICON;
		$FM_SCREEN_PATH = FM_SCREEN_PATH;
		$FM_CSS_IMAGE_VIEW_ELEMENT = FM_CSS_IMAGE_VIEW_ELEMENT;
		$FM_CSS_IMAGE_VIEW_ELEMENT_SELECTED = FM_CSS_IMAGE_VIEW_ELEMENT_SELECTED;
		$FM_CSS_TREE_FOLDER = FM_CSS_TREE_FOLDER;
		$FM_CSS_TREE_FOLDER_SELECTED = FM_CSS_TREE_FOLDER_SELECTED;
		$FM_CSS_DETAILED_VIEW = FM_CSS_DETAILED_VIEW;
		$FM_CSS_DETAILED_VIEW_BAR = FM_CSS_DETAILED_VIEW_BAR;
		$FM_CSS_DETAILED_VIEW_BAR_ELEMENT = FM_CSS_DETAILED_VIEW_BAR_ELEMENT;
		$FM_CSS_DETAILED_VIEW_ODD = FM_CSS_DETAILED_VIEW_ODD;
		$FM_CSS_DETAILED_VIEW_EVEN = FM_CSS_DETAILED_VIEW_EVEN;
		$FM_CSS_DETAILED_VIEW_ELEMENT_SELECTED = FM_CSS_DETAILED_VIEW_ELEMENT_SELECTED;
		$FM_ALERT_DIALOG_INDEX = FM_ALERT_DIALOG_INDEX;
		$FM_CONFIRM_DIALOG_INDEX = FM_CONFIRM_DIALOG_INDEX;
		$FM_CONFIRM_DIALOG_TITLE = FM_CONFIRM_DIALOG_TITLE;
		$FM_CONFIRM_DIALOG_ACCEPT = FM_CONFIRM_DIALOG_ACCEPT;
		$FM_CONFIRM_DIALOG_CANCEL = FM_CONFIRM_DIALOG_CANCEL;
		$FM_MSG_NOT_ELEMENT_SELECTED_TO_DELETE = FM_MSG_NOT_ELEMENT_SELECTED_TO_DELETE;
		$FM_MSG_CONFIRM_DELETE = FM_MSG_CONFIRM_DELETE;
		$FM_ERROR_RENAME_ONE_SELECTED = FM_ERROR_RENAME_ONE_SELECTED;
		$FM_ERROR_RENAME_EMPTY = FM_ERROR_RENAME_EMPTY;
		$FM_DETAILED_VIEW_BAR_HEIGHT = FM_DETAILED_VIEW_BAR_HEIGHT;
		$FM_DETAILED_VIEW_ELEMENT_HEIGHT = FM_DETAILED_VIEW_ELEMENT_HEIGHT;
		$FM_DETAILED_VIEW_NAME_PERCENT = FM_DETAILED_VIEW_NAME_PERCENT;
		$FM_DETAILED_VIEW_EXTENSION_PERCENT = FM_DETAILED_VIEW_EXTENSION_PERCENT;
		$FM_DETAILED_VIEW_LAST_TIME_PERCENT = FM_DETAILED_VIEW_LAST_TIME_PERCENT;
		$FM_DETAILED_VIEW_SIZE_PERCENT = FM_DETAILED_VIEW_SIZE_PERCENT;
		$FM_DETAILED_VIEW_NAME = FM_DETAILED_VIEW_NAME;
		$FM_DETAILED_VIEW_EXTENSION = FM_DETAILED_VIEW_EXTENSION;
		$FM_DETAILED_VIEW_LAST_TIME = FM_DETAILED_VIEW_LAST_TIME;
		$FM_DETAILED_VIEW_SIZE = FM_DETAILED_VIEW_SIZE;
		$resize = "		fm_{$this->_id}.resize();\n";
		$setTree = $this->isShowTree() ? "		fm_{$this->_id}.setTree(new TreeView('{$this->_id}',".FM_TREE_WIDTH.",".FM_HEIGHT."));\n" : '';
		$h = FM_LOCATION_BAR_HEIGHT;
		$setLocationBar = $this->isShowLocationBar() ? "		fm_{$this->_id}.setLocationBar(new LocationBar('{$this->_id}_input_locationbar',{$h}));\n" : '';
		$h = FM_ICON_BAR_HEIGHT;
		$setIconBar = $this->isShowIconBar() ? "		fm_{$this->_id}.setIconBar(new IconBar('{$this->_id}',{$h}));\n" : '';
		$uploadSrc = FREELANCESOFT_FILEMANAGER_PATH_URL.'uploadForm.php';
		$js =
		'<script type="text/javascript" language="javascript">'."\n".
		"	var fm_{$this->_id};\n".
		"	var FM_AUTOSIZE = {$FM_AUTOSIZE};\n".
		"	var FM_WIDTH = {$FM_WIDTH};\n".
		"	var FM_HEIGHT = {$FM_HEIGHT};\n".
		"	var FM_IMAGE_VIEW_COLUMNS = 4;\n".
		"	var FM_IMAGE_VIEW_IMAGE_SIZE = {$FM_IMAGE_VIEW_IMAGE_SIZE};\n".
		"	var FM_IMAGE_VIEW_BORDER_SIZE = {$FM_IMAGE_VIEW_BORDER_SIZE};\n".
		"	var FM_TREE_FOLDER_HEIGHT = {$FM_TREE_FOLDER_HEIGHT};\n".
		"	var FM_FOLDER_ICON = '{$FM_FOLDER_ICON}';\n".
		"	var FM_FOLDER_UP_ICON = '{$FM_FOLDER_UP_ICON}';\n".
		"	var FM_FILE_ICON = '{$FM_FILE_ICON}';\n".
		"	var FM_TREE_PLUS_ICON = '{$FM_TREE_PLUS_ICON}';\n".
		"	var FM_TREE_MINUS_ICON = '{$FM_TREE_MINUS_ICON}';\n".
		"	var FM_TREE_FOLDER_ICON = '{$FM_TREE_FOLDER_ICON}';\n".
		"	var FM_SCREEN_PATH = '{$FM_SCREEN_PATH}';\n".
		"	var FM_CSS_IMAGE_VIEW_ELEMENT = '{$FM_CSS_IMAGE_VIEW_ELEMENT}';\n".
		"	var FM_CSS_IMAGE_VIEW_ELEMENT_SELECTED = '{$FM_CSS_IMAGE_VIEW_ELEMENT_SELECTED}';\n".
		"	var FM_CSS_TREE_FOLDER = '{$FM_CSS_TREE_FOLDER}';\n".
		"	var FM_CSS_TREE_FOLDER_SELECTED = '{$FM_CSS_TREE_FOLDER_SELECTED}';\n".
		"	var FM_CSS_DETAILED_VIEW = '{$FM_CSS_DETAILED_VIEW}';\n".
		"	var FM_CSS_DETAILED_VIEW_BAR = '{$FM_CSS_DETAILED_VIEW_BAR}';\n".
		"	var FM_CSS_DETAILED_VIEW_BAR_ELEMENT = '{$FM_CSS_DETAILED_VIEW_BAR_ELEMENT}';\n".
		"	var FM_CSS_DETAILED_VIEW_ODD = '{$FM_CSS_DETAILED_VIEW_ODD}';\n".
		"	var FM_CSS_DETAILED_VIEW_EVEN = '{$FM_CSS_DETAILED_VIEW_EVEN}';\n".
		"	var FM_CSS_DETAILED_VIEW_ELEMENT_SELECTED = '{$FM_CSS_DETAILED_VIEW_ELEMENT_SELECTED}';\n".
		"	var FM_UPLOAD_IFRAME_SRC = '{$uploadSrc}';\n".
		"	var FM_ALERT_DIALOG_INDEX = {$FM_ALERT_DIALOG_INDEX};\n".
		"	var FM_CONFIRM_DIALOG_INDEX = {$FM_CONFIRM_DIALOG_INDEX};\n".
		"	var FM_CONFIRM_DIALOG_TITLE = '{$FM_CONFIRM_DIALOG_TITLE}';\n".
		"	var FM_CONFIRM_DIALOG_ACCEPT = '{$FM_CONFIRM_DIALOG_ACCEPT}';\n".
		"	var FM_CONFIRM_DIALOG_CANCEL = '{$FM_CONFIRM_DIALOG_CANCEL}';\n".
		"	var FM_MSG_NOT_ELEMENT_SELECTED_TO_DELETE = '{$FM_MSG_NOT_ELEMENT_SELECTED_TO_DELETE}';\n".
		"	var FM_MSG_NOT_ELEMENT_SELECTED_TO_DELETE = '{$FM_MSG_NOT_ELEMENT_SELECTED_TO_DELETE}';\n".
		"	var FM_MSG_CONFIRM_DELETE = '{$FM_MSG_CONFIRM_DELETE}';\n".
		"	var FM_ERROR_RENAME_ONE_SELECTED = '{$FM_ERROR_RENAME_ONE_SELECTED}';\n".
		"	var FM_ERROR_RENAME_EMPTY = '{$FM_ERROR_RENAME_EMPTY}';\n".
		"	var FM_DETAILED_VIEW_BAR_HEIGHT = {$FM_DETAILED_VIEW_BAR_HEIGHT};\n".
		"	var FM_DETAILED_VIEW_ELEMENT_HEIGHT = {$FM_DETAILED_VIEW_ELEMENT_HEIGHT};\n".
		"	var FM_DETAILED_VIEW_NAME_PERCENT = {$FM_DETAILED_VIEW_NAME_PERCENT};\n".
		"	var FM_DETAILED_VIEW_EXTENSION_PERCENT = {$FM_DETAILED_VIEW_EXTENSION_PERCENT};\n".
		"	var FM_DETAILED_VIEW_LAST_TIME_PERCENT = {$FM_DETAILED_VIEW_LAST_TIME_PERCENT};\n".
		"	var FM_DETAILED_VIEW_SIZE_PERCENT = {$FM_DETAILED_VIEW_SIZE_PERCENT};\n".
		"	var FM_DETAILED_VIEW_NAME = '{$FM_DETAILED_VIEW_NAME}';\n".
		"	var FM_DETAILED_VIEW_EXTENSION = '{$FM_DETAILED_VIEW_EXTENSION}';\n".
		"	var FM_DETAILED_VIEW_LAST_TIME = '{$FM_DETAILED_VIEW_LAST_TIME}';\n".
		"	var FM_DETAILED_VIEW_SIZE = '{$FM_DETAILED_VIEW_SIZE}';\n".
		"	function FM_OnLoad_{$this->_id}(){\n".
		"		fm_{$this->_id} = new FileManager('{$this->_id}');\n".
		"		fm_{$this->_id}.setView(new {$this->_view}('{$this->_id}'));\n".
		"		fm_{$this->_id}.requestFiles('/');\n".
				$setTree.
				$setLocationBar.
				$setIconBar.
				$resize.
				$this->getWinsowsJs().
		"	}\n".
		'</script>'."\n";
		return $js;
	}
	
	/**
	 * Gets html
	 *
	 * @return string
	 */
	public function getHtml(){
		$icons = Icon::getIcons($this->getId(),$this->_icons);
		$locationBar = $this->isShowLocationBar() ? sprintf(FM_TEMPLATE_LOCATION_BAR,$this->_id,$this->_id,$this->_id,$this->_id) : '';
		$html =
		'<div id="'.$this->_id.'" style="overflow:hidden;width:'.FM_WIDTH.'px;height:'.FM_HEIGHT.'px">'."\n".
		'	<div id="'.$this->_id.'_icon_bar" class="'.FM_CSS_ICON_BAR.'" style="overflow:hidden;padding:4px;vertical-align:middle;width:100%;height:'.FM_ICON_BAR_HEIGHT.'px">'."\n".
			$icons.
		'	</div>'."\n".
			$locationBar.
		'	<div id="'.$this->_id.'_tree_view" style="position:relative;top:0px;overflow:hidden;width:'.FM_TREE_WIDTH.'px;height:'.(FM_HEIGHT-FM_LOCATION_BAR_HEIGHT).'px">'."\n".
		'	</div>'."\n".
		'	<div id="'.$this->_id.'_view" style="position:relative;top:-'.(FM_HEIGHT-FM_LOCATION_BAR_HEIGHT).'px;left:'.FM_TREE_WIDTH.'px;overflow:scroll;width:'.(FM_WIDTH-FM_TREE_WIDTH).'px;height:'.(FM_HEIGHT-FM_LOCATION_BAR_HEIGHT).'px">'."\n".
		'	</div>'."\n".
		'</div>'."\n";
		return $html;
	}
	
	/**
	 * Gets a resize function
	 *
	 * @return string
	 */
	public function getResizeFunction(){
		return "fm_{$this->_id}.resize();";
	}
	
	/**
	 * Gets a javascript function for body onload.
	 *
	 * @return string
	 */
	public function getOnloadFunction(){
		return "FM_OnLoad_{$this->_id}();";
	}
	
	/**
	 * Print a javascript function for body onload.
	 *
	 * @return string
	 */
	public function printOnloadFunction(){
		echo $this->getOnloadFunction();
	}
	
	/**
	 * Prints html
	 *
	 */
	public function printHtml(){
		echo $this->getHtml();
	}
	
	/**
	 * Prints a resize function
	 *
	 */
	public function printResizeFunction(){
		echo $this->getResizeFunction();
	}
	
	/**
	 * Constructor.
	 *
	 * @param string $id
	 * @param string $root
	 * @param array(int) $icons
	 * @param string $view
	 * @param bool $showTree
	 * @param bool $showLocationBar
	 * @return FileManager
	 */
	public function FileManager($id,$root,$icons = array(),$view = FileManager::VIEW_IMAGE,$showTree = true,$showLocationBar = true){
		$this->_id = $id;
		$this->_root = $root;
		$this->_view = $view;
		$this->_icons = $icons;
		$this->showTree($showTree);
		$this->showLocationBar($showLocationBar);
		$this->showIconBar(isset($icons[0]));
		$_SESSION[$id] = $this;
	}
	
}

?>