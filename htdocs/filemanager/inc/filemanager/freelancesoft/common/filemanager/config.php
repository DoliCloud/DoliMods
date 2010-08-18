<?php

/**
 * Autor: Martin Bascal Of FreelanceSoft
 * Created on: 21/10/2007
 * Description:
 *    
 *  Config file for file manager.  
 *    
 */

define('FM_IMAGE_VIEW_IMAGE_SIZE',96);
define('FM_IMAGE_VIEW_BORDER_SIZE',15);
define('FM_DETAILED_VIEW_BAR_HEIGHT',16);
define('FM_DETAILED_VIEW_ELEMENT_HEIGHT',24);
define('FM_DETAILED_VIEW_NAME_PERCENT',40);
define('FM_DETAILED_VIEW_EXTENSION_PERCENT',10);
define('FM_DETAILED_VIEW_LAST_TIME_PERCENT',25);
define('FM_DETAILED_VIEW_SIZE_PERCENT',25);
define('FM_TREE_FOLDER_HEIGHT',16);
define('FM_LOCATION_BAR_HEIGHT',24); // greater than 4
define('FM_ICON_BAR_HEIGHT',28); // greater than 4
define('FM_ICON_BAR_ICON_SIZE',24);
define('FM_CHANGE_LOCATION_ICON_WIDTH',16);
define('FM_CHANGE_LOCATION_ICON_HEIGHT',16);
define('FM_HEIGHT',600);
define('FM_WIDTH',800);
define('FM_TREE_WIDTH',160);
define('FM_AUTOSIZE',true);
define('FM_ALERT_DIALOGS_FREE_INDEX',1);
define('FM_CONFIRMATION_DIALOGS_FREE_INDEX',1);
define('FM_WINDOWS_FREE_INDEX',1);
define('FM_ACTION_UPLOAD',0);
define('FM_ALLOW_UPLOAD',true);
define('FM_ALLOW_DELETE',true);
define('FM_ALLOW_CREATE_DIRECTORY',true);
define('FM_ALLOW_PASTE',true);
define('FM_FOLDER_ICON',IMAGE_PATH.'imagen_carpeta.png');
define('FM_FOLDER_UP_ICON',IMAGE_PATH.'imagen_arriba.png');
define('FM_FILE_ICON',IMAGE_PATH.'imagen_archivo.png');
define('FM_TREE_PLUS_ICON',IMAGE_PATH.'imagen_mas.png');
define('FM_TREE_MINUS_ICON',IMAGE_PATH.'imagen_menos.png');
define('FM_TREE_FOLDER_ICON',IMAGE_PATH.'imagen_carpeta2.png');
define('FM_SCREEN_PATH',FREELANCESOFT_FILEMANAGER_PATH_URL.'screen.php');
define('FM_CSS_IMAGE_VIEW_ELEMENT','imageViewElement');
define('FM_CSS_IMAGE_VIEW_ELEMENT_SELECTED','imageViewElementSelected');
define('FM_CSS_TREE_FOLDER','imageViewElement');
define('FM_CSS_TREE_FOLDER_SELECTED','imageViewElementSelected');
define('FM_CSS_LOCATION_BAR','locationBar');
define('FM_CSS_LOCATION_BAR_INPUT','locationBarInput');
define('FM_CSS_ICON_BAR','iconBar');
define('FM_CSS_ICON_BAR_ICON','icon');
define('FM_CSS_ICON_BAR_ICON_HOVER','iconHover');
define('FM_CSS_ICON_BAR_SEPARATOR','iconSeparator');
define('FM_CSS_DIALOGS_TEXTFIELD','dialogsTextfield');
define('FM_CSS_DIALOGS_BUTTON','dialogsButton');
define('FM_CSS_DETAILED_VIEW','detailedView');
define('FM_CSS_DETAILED_VIEW_BAR','detailedViewBar');
define('FM_CSS_DETAILED_VIEW_BAR_ELEMENT','detailedViewBarElement');
define('FM_CSS_DETAILED_VIEW_ODD','detailedViewOdd');
define('FM_CSS_DETAILED_VIEW_EVEN','detailedViewEven');
define('FM_CSS_DETAILED_VIEW_ELEMENT_SELECTED','imageViewElementSelected');
define('FM_CSS_WINDOW_SECTION_TITLE','windowSectionTitle');
define('FM_CSS_DYNAMIC_ERROR','dynamicError');
define('FM_ALERT_DIALOG_TITLE','fileManager... Mensage');
define('FM_ALERT_DIALOG_ACCEPT','Aceptar');
define('FM_CONFIRM_DIALOG_TITLE','fileManager... Confirmaci&oacute;n');
define('FM_CONFIRM_DIALOG_ACCEPT','SI');
define('FM_CONFIRM_DIALOG_CANCEL','NO');
define('FM_WINDOW_ADD_FILES_TITLE','Crear archivos');
define('FM_WINDOW_ADD_FILES_MORE','Otro...');
define('FM_WINDOW_ADD_FILES_MINUS','Eliminar...');
define('FM_TITLE_GO_TO_LOCATION','Ir.');
define('FM_TITLE_GO_UP','Subir.');
define('FM_TITLE_SHOW_TREE','Mostrar u ocultar arbol.');
define('FM_TITLE_ADD_DIRECTORY','Crear directorio.');
define('FM_TITLE_ADD_FILES','Crear archivos.');
define('FM_TITLE_REFRESH','Refrescar.');
define('FM_TITLE_RENAME','Renombrar.');
define('FM_TITLE_CUT','Cortar.');
define('FM_TITLE_COPY','Copiar.');
define('FM_TITLE_PASTE','Pegar.');
define('FM_TITLE_CHANGE_VIEW','Cambiar vista.');
define('FM_TITLE_CONFIGURE','Configurar.');
define('FM_TITLE_DELETE_FILES','Eliminar elementos seleccionados.');
define('FM_CREATE_DIRECTORY_TITLE','Crear un directorio.');
define('FM_RENAME_TITLE','Renombrar.');
define('FM_UPLOAD_SUCCESS_GO_TO','Ir a la carpeta...');
define('FM_CREATE_DIRECTORY_NAME','Nombre:');
define('FM_CREATE_DIRECTORY_BUTTON_TEXT','Crear');
define('FM_RENAME_NEW_NAME','Nuevo nombre:');
define('FM_RENAME_BUTTON_TEXT','Renombrar');
define('FM_COPY_TITLE','Copiando.');
define('FM_CONFIGURE_TITLE','Configurar.');
define('FM_DETAILED_VIEW_NAME','Nombre');
define('FM_DETAILED_VIEW_EXTENSION','Ext');
define('FM_DETAILED_VIEW_LAST_TIME','&Uacute;ltimo acceso');
define('FM_DETAILED_VIEW_SIZE','Tama&ntilde;o');
define('FM_CONFIGURE_SECTION_IMAGE','Vista de im&aacute;genes');
define('FM_CONFIGURE_SECTION_DETAILS','Vista detallada');
define('FM_CONFIGURE_THUMBS_SIZE','Tama&ntilde;o de los thumbs:');
define('FM_CONFIGURE_DETAIL_ICON','Tama&ntilde;o de l&iacute;cono en detalle:');
define('FM_CONFIGURE_PERCENT_NAME','Porcentaje nombre:');
define('FM_CONFIGURE_PERCENT_EXT','Porcentaje extensi&oacute;n:');
define('FM_CONFIGURE_PERCENT_LAST_ACCESS','Porcentaje &uacute;ltimo acceso:');
define('FM_CONFIGURE_PERCENT_SIZE','Porcentaje tama&ntilde;o:');
define('FM_CONFIGURE_ACCEPT_BUTTON','Aceptar');
define('FM_DATE_FORMAT','d/m/y H:m:s');

define('FM_MSG_FILE_UPLOAD_EXISTS','El archivo %s ya existe. Desea reemplazarlo?');
define('FM_MSG_NOT_ELEMENT_SELECTED_TO_DELETE','No hay elementos seleccionados para eliminar.');
define('FM_MSG_CONFIRM_DELETE','Est&aacute; seguro de eliminar los archivos seleccionados.');
define('FM_ERROR_RESPONSE_FILES','No se obtubieron resultados.');
define('FM_ERROR_FILE_UPLOAD','Se produjo un error creando el archivo %s.');
define('FM_ERROR_NOT_ALLOWED_UPLOAD','En esta demo no se permite crear archivos.');
define('FM_ERROR_NOT_ALLOWED_DELETE','En esta demo no se permite eliminar archivos.');
define('FM_ERROR_NOT_ALLOWED_PASTE','En esta demo no se permite copiar y pegar.');
define('FM_ERROR_DELETE','Se produjo un error eliminando archivos.');
define('FM_ERROR_NOT_ALLOWED_CREATE_DIRECTORY','En esta demo no se permite crear directorios.');
define('FM_ERROR_CREATE_DIRECTORY_EMPTY','Debe ingresar un nombre de directorio.');
define('FM_ERROR_CREATE_DIRECTORY_EXISTS','El directorio %s ya existe.');
define('FM_ERROR_CREATE_DIRECTORY','Se ha producido un error creando el directorio %s.');
define('FM_ERROR_RENAME_ONE_SELECTED','Para renombrar debe tener s&oacute;lo un elemento seleccionado.');
define('FM_ERROR_RENAME_EMPTY','Debe ingresar el nuevo nombre.');
define('FM_ERROR_RENAME_EXISTS','%s ya existe.');
define('FM_ERROR_RENAME','Se ha producido un error renombrando %s.');
define('FM_ERROR_PASTE','Imposible pegar en el directorio actual.');
define('FM_ERROR_INVALID','Inv&aacute;lido.');
define('FM_ERROR_DETAILED_PERCENT','Los porcentajes deben sumar 100.');


?>