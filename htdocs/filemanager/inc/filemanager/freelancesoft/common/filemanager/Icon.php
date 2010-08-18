<?php

/**
 * Autor: Martin Bascal Of FreelanceSoft
 * Created on: 01/11/2007
 * Description:
 *    
 *    Helps to render icons in icon bar.
 *    
 */

define('FM_ICON_TEMPLATE',
		'<div style="width:'.FM_ICON_BAR_ICON_SIZE.'px;'.
				'height:'.FM_ICON_BAR_ICON_SIZE.'px;'.
				'background-image:url('.FM_SCREEN_PATH.'?i=%s&w='.FM_ICON_BAR_ICON_SIZE.'&h='.FM_ICON_BAR_ICON_SIZE.');'.
				'background-repeat:no-repeat;background-position:center;float:left;" '.
			'onclick="%s" '.
			'title="%s"'.
			'class="'.FM_CSS_ICON_BAR_ICON.'" '.
			'onmouseout="this.className=\''.FM_CSS_ICON_BAR_ICON.'\'" '.
			'onmouseover="this.className=\''.FM_CSS_ICON_BAR_ICON_HOVER.'\'" >'.
		'</div>');

class Icon{
	const UP_LEVEL_ICON = 0;
	const SHOW_TREE = 1;
	const SEPARATOR = 2;
	const ADD_FILE = 3;
	const REFRESH = 4;
	const DELETE_FILES = 5;
	const ADD_DIRECTORY = 6;
	const RENAME = 7;
	const CUT = 8;
	const COPY = 9;
	const PASTE = 10;
	const CHANGE_VIEW = 11;
	const CONFIGURE = 12;
	
	private static function getIcon($type,$idFileManager){
		$image = '';
		$action = '';
		$title = '';
		if($type == Icon::SEPARATOR){
			return '<div class="'.FM_CSS_ICON_BAR_SEPARATOR.'" style="float:left;height:'.FM_ICON_BAR_ICON_SIZE.'px;"></div>';
		}
		elseif($type == Icon::UP_LEVEL_ICON){
			$image = IMAGE_PATH.'imagen_arriba.png';
			$action = 'fm_'.$idFileManager.'.goUp();';
			$title = FM_TITLE_GO_UP;
		}
		elseif($type == Icon::SHOW_TREE){
			$image = IMAGE_PATH.'imagen_tree.png';
			$action = 'fm_'.$idFileManager.'.showHideTree();';
			$title = FM_TITLE_SHOW_TREE;
		}
		elseif($type == Icon::ADD_FILE){
			$image = IMAGE_PATH.'imagen_agregarArchivo.png';
			$action = 'fm_'.$idFileManager.'.openUploadFileWindow();';
			$title = FM_TITLE_ADD_FILES;
		}
		elseif($type == Icon::REFRESH){
			$image = IMAGE_PATH.'imagen_refrescar.png';
			$action = 'fm_'.$idFileManager.'.refresh();';
			$title = FM_TITLE_REFRESH;
		}
		elseif($type == Icon::DELETE_FILES){
			$image = IMAGE_PATH.'imagen_eliminarArchivos.png';
			$action = 'fm_'.$idFileManager.'.deleteFiles();';
			$title = FM_TITLE_DELETE_FILES;
		}
		elseif($type == Icon::ADD_DIRECTORY){
			$image = IMAGE_PATH.'imagen_agregarDirectorio.png';
			$action = 'fm_'.$idFileManager.'.openCreateDirectoryWindow();';
			$title = FM_TITLE_ADD_DIRECTORY;
		}
		elseif($type == Icon::RENAME){
			$image = IMAGE_PATH.'imagen_renombrar.png';
			$action = 'fm_'.$idFileManager.'.openRenameWindow();';
			$title = FM_TITLE_RENAME;
		}
		elseif($type == Icon::CUT){
			$image = IMAGE_PATH.'imagen_cortar.png';
			$action = 'fm_'.$idFileManager.'.cut();';
			$title = FM_TITLE_CUT;
		}
		elseif($type == Icon::COPY){
			$image = IMAGE_PATH.'imagen_copiar.png';
			$action = 'fm_'.$idFileManager.'.copy();';
			$title = FM_TITLE_COPY;
		}
		elseif($type == Icon::PASTE){
			$image = IMAGE_PATH.'imagen_pegar.png';
			$action = 'fm_'.$idFileManager.'.paste();';
			$title = FM_TITLE_PASTE;
		}
		elseif($type == Icon::CHANGE_VIEW){
			$image = IMAGE_PATH.'imagen_cambiarVista.png';
			$action = 'fm_'.$idFileManager.'.changeView();';
			$title = FM_TITLE_CHANGE_VIEW;
		}
		elseif($type == Icon::CONFIGURE){
			$image = IMAGE_PATH.'imagen_configurar.png';
			$action = 'fm_'.$idFileManager.'.openConfigureWindow();';
			$title = FM_TITLE_CONFIGURE;
		}
		return sprintf(FM_ICON_TEMPLATE,$image,$action,$title);
	}
	
	/**
	 * Gets html that represents icons by type.
	 *
	 * @param string $idFileManager
	 * @param array(int) $types
	 */
	public static function getIcons($idFileManager,$types){
		$ret = '';
		foreach ($types as $type) {
			$ret .= Icon::getIcon($type,$idFileManager);
		}
		return $ret;
	}
}

?>