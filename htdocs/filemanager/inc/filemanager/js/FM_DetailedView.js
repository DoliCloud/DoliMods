// *****************************************
// DETAILED VIEW
// *****************************************
function DetailedView(idFileManager){
	var idFm = idFileManager;
	var htmlComp = 	FM_getObjectById(idFileManager+'_view');
	var htmlCreated = false;
	var actualFiles = new Array();
	var selectedFiles = new Array();
	var actualPath = '';
	var extraHeight = 0;
	var copyFiles;
	
	function round(num){
		return(Math.round(num * 100) / 100);
	}
	
	function getHtmlForElement(fileIndex){
		var element;
		if(fileIndex > -1){
			element = actualFiles[fileIndex];
			element.index = fileIndex;
		}
		var css = '';
		if(element.isSelected){
			css = FM_CSS_DETAILED_VIEW_ELEMENT_SELECTED;
		}
		else{
			css = (fileIndex % 2 == 0 ? FM_CSS_DETAILED_VIEW_ODD : FM_CSS_DETAILED_VIEW_EVEN);
		}
		var h = FM_DETAILED_VIEW_ELEMENT_HEIGHT;
		var imgPath = '';
		if(element.isDirectory){
			if(element.name == '..'){
				imgPath = FM_SCREEN_PATH + '?i=' + FM_FOLDER_UP_ICON + '&w=' + (h-4) + '&h=' + (h-4);
			}
			else{
				imgPath = FM_SCREEN_PATH + '?i=' + FM_FOLDER_ICON + '&w=' + (h-4) + '&h=' + (h-4);
			}
		}
		else if(element.isImage){
			imgPath = FM_SCREEN_PATH + '?i=' + element.path + '&w=' + (h-4) + '&h=' + (h-4);
		}
		else{
			imgPath = FM_SCREEN_PATH + '?i=' + FM_FILE_ICON + '&w=' + (h-4) + '&h=' + (h-4);
		}
		var clickAction =  'onclick="fm_' + idFm + '.elementClicked(event);"';
		var dobleClickAction =  'ondblclick="fm_' + idFm + '.elementDobleClicked(event);"';
		htmlCode = '<div  id="' + idFm +'_detailed_view_element_' + fileIndex + '" style="overflow:hidden;width:100%;height:' + h + 'px;line-height:' + h + 'px;" class="' + css + '">'
		htmlCode += '<div id="' + idFm + '_detailed_view_name_' + fileIndex + '" style="width:' + FM_DETAILED_VIEW_NAME_PERCENT + '%;float:left;"><div id="' + idFm + '_detailed_view_name_image_' + fileIndex + '" style="width:' + h + 'px;height:' + h + 'px;background-image:url(' + imgPath + ');background-repeat:no-repeat;background-position:left;float:left;" ' + clickAction + ' ' + dobleClickAction + '></div><div id="' + idFm + '_detailed_view_name_name_' + fileIndex + '" style="float:left;" ' + clickAction + ' ' + dobleClickAction + '>' + element.name + '</div></div>';
		htmlCode += '<div id="' + idFm + '_detailed_view_ext_' + fileIndex + '" style="width:' + FM_DETAILED_VIEW_EXTENSION_PERCENT + '%;height:' + h + 'px;float:left;" ' + clickAction + ' ' + dobleClickAction + '>' + element.extension + '</div>';
		htmlCode += '<div id="' + idFm + '_detailed_view_last_time_' + fileIndex + '" style="width:' + FM_DETAILED_VIEW_LAST_TIME_PERCENT + '%;height:' + h + 'px;float:left;" ' + clickAction + ' ' + dobleClickAction + '>' + element.lastTime + '</div>';
		htmlCode += '<div id="' + idFm + '_detailed_view_size_' + fileIndex + '" style="width:' + FM_DETAILED_VIEW_SIZE_PERCENT + '%;height:' + h + 'px;float:left;" ' + clickAction + ' ' + dobleClickAction + '>' + round(element.size / 1024) + ' Kb</div>';
		htmlCode += '</div>';
		return htmlCode;
	}
	
	function createHtml(){
		var countElements = actualFiles.length;
		var h = FM_DETAILED_VIEW_BAR_HEIGHT;
		var htmlCode = '<div class="' + FM_CSS_DETAILED_VIEW + '">';
		htmlCode += '<div id="' + idFm +'_detailed_view_bar' + '" style="overflow:hidden;width:100%;height:' + h + 'px;" class="' + FM_CSS_DETAILED_VIEW_BAR + '">';
		htmlCode += '<div id="' + idFm +'_detailed_view_bar_name' + '" style="overflow:hidden;width:' + FM_DETAILED_VIEW_NAME_PERCENT + '%;height:' + h + 'px;float:left;" class="' + FM_CSS_DETAILED_VIEW_BAR_ELEMENT + '">' + FM_DETAILED_VIEW_NAME + '</div>';
		htmlCode += '<div id="' + idFm +'_detailed_view_bar_ext' + '" style="overflow:hidden;width:' + FM_DETAILED_VIEW_EXTENSION_PERCENT + '%;height:' + h + 'px;float:left;" class="' + FM_CSS_DETAILED_VIEW_BAR_ELEMENT + '">' + FM_DETAILED_VIEW_EXTENSION + '</div>';
		htmlCode += '<div id="' + idFm +'_detailed_view_bar_last_time' + '" style="overflow:hidden;width:' + FM_DETAILED_VIEW_LAST_TIME_PERCENT + '%;height:' + h + 'px;float:left;" class="' + FM_CSS_DETAILED_VIEW_BAR_ELEMENT + '">' + FM_DETAILED_VIEW_LAST_TIME + '</div>';
		htmlCode += '<div id="' + idFm +'_detailed_view_bar_size' + '" style="overflow:hidden;width:' + FM_DETAILED_VIEW_SIZE_PERCENT + '%;height:' + h + 'px;float:left;" class="' + FM_CSS_DETAILED_VIEW_BAR_ELEMENT + '">' + FM_DETAILED_VIEW_SIZE + '</div>';
		htmlCode += '</div>';
		var i;
		for(i = 0; i < countElements; i++){
			htmlCode += getHtmlForElement(i);
		}
		htmlCode += '</div>';
		htmlComp.innerHTML = htmlCode;
		for(i = 0; i < countElements; i++){
			actualFiles[i].htmlComp = FM_getObjectById(idFm + '_detailed_view_element_' + i);
		}
		htmlContainerComp = FM_getObjectById(idFm +'_image_view_container');
	}
	
	function actualizeHtml(){
		var countElements = actualFiles.length;
		for(i = 0; i < countElements; i++){
			var element = actualFiles[i];
			var css = '';
			if(element.isSelected){
				css = FM_CSS_DETAILED_VIEW_ELEMENT_SELECTED;
			}
			else{
				css = (i % 2 == 0 ? FM_CSS_DETAILED_VIEW_ODD : FM_CSS_DETAILED_VIEW_EVEN);
			}
			element.htmlComp.className = css;
		}
	}
	
	function actualizeView(){
		if(htmlCreated){
			actualizeHtml();
		}
		else{
			createHtml();
		}
	}
	
	function getFirstSelected(){
		var i;
		for(i = 0; i < actualFiles.length; i++){
			if(actualFiles[i].isSelected){
				return (actualFiles[i]);
			}
		}
		return null;
	}
	
	function selectWithCtrl(element){
		var i;
		var removeI = 0;
		var arrAux = new Array();
		if(element.isSelected){
			for(i = 0; i < selectedFiles.length; i++){
				if(selectedFiles[i] == element){
					removeI = 1;
				}
				else{
					arrAux[i-removeI] = selectedFiles[i];
				}
			}
			element.isSelected = false;
			selectedFiles = arrAux;
		}
		else{
			element.isSelected = true;
			selectedFiles[selectedFiles.length++] = element;
		}
	}
	
	function selectWithShift(element){
		var firstSelected = getFirstSelected();
		var i;
		if(firstSelected == null){
			for(i = 0; i < selectedFiles.length; i++){
				selectedFiles[i].isSelected = false;
			}
			element.isSelected = true;
			selectedFiles = new Array(element);
		}
		else{
			for(i = 0; i < selectedFiles.length; i++){
				selectedFiles[i].isSelected = false;
			}
			var minIndex = (firstSelected.index < element.index ? firstSelected.index : element.index);
			var maxIndex = (firstSelected.index > element.index ? firstSelected.index : element.index);
			var actualIndex = minIndex;
			var cantSelected = maxIndex - minIndex + 1;
			selectedFiles = new Array();
			for(i = 0; i < cantSelected; i++){
				var actualElement = actualFiles[actualIndex++];
				actualElement.isSelected = true;
				selectedFiles[i] = actualElement;
			}
		}
	}
	
	function select(element,e){
		if(element.name != '..'){
			var keys = FM_keyPressed(e);
			if(keys.ctrl){
				selectWithCtrl(element);
			}
			else if(keys.shift){
				selectWithShift(element);
			}
			else{
				var i;
				for(i = 0; i < selectedFiles.length; i++){
					selectedFiles[i].isSelected = false;
				}
				element.isSelected = true;
				selectedFiles = new Array(element);
			}
		}
	}
	
	this.setLeft = function(l){
		htmlComp.style.left = l + 'px';
	}
	
	this.setFiles = function(path,files){
		actualFiles = files;
		selectedFiles = new Array();
		actualPath = path;
		htmlCreated = false;
		actualizeView();
		htmlCreated = true;
	}
	
	this.handleClick = function(e){
		var targ;
		if (!e) var e = window.event;
		if (e.target) targ = e.target;
		else if (e.srcElement) targ = e.srcElement;
		if (targ.nodeType == 3) // defeat Safari bug
			targ = targ.parentNode;
		var identifier = targ.id;
		var fileIndex = parseInt(identifier.substring(identifier.lastIndexOf('_')+1));
		if(isNaN(fileIndex)){
			fileIndex = parseInt(identifier.substr(identifier.lastIndexOf('_')+1));
		}
		var element = actualFiles[fileIndex];
		select(element,e);
		actualizeView();
	}
	
	this.handleDobleClick = function(e){
		var targ;
		if (!e) var e = window.event;
		if (e.target) targ = e.target;
		else if (e.srcElement) targ = e.srcElement;
		if (targ.nodeType == 3) // defeat Safari bug
			targ = targ.parentNode;
		var identifier = targ.id;
		var fileIndex = parseInt(identifier.substring(identifier.lastIndexOf('_')+1));
		if(isNaN(fileIndex)){
			fileIndex = parseInt(identifier.substr(identifier.lastIndexOf('_')+1));
		}
		var element = actualFiles[fileIndex];
		if(element.isDirectory){
			eval('fm_' + idFm + '.requestFiles(\'' + actualPath + element.name + '/\');');
		}
		else{
			window.open(element.httpPath);
		}
	}
	
	this.resize = function(width,height,cextraHeight,withTree){
		extraHeight = cextraHeight;
		if(width < 0) width = 10;
		htmlComp.style.width = width + 'px';
		htmlComp.style.height = (height - extraHeight) + 'px';
		if(withTree){
			htmlComp.style.top = '-' + (height - extraHeight) + 'px';
		}
	}
	
	this.deleteFiles = function(){
		if((selectedFiles.length < 1) || (selectedFiles.length == 1 && selectedFiles[0].name == '..')){
			FM_showAlert(FM_MSG_NOT_ELEMENT_SELECTED_TO_DELETE);
		}
		else{
			FM_showConfirm(FM_MSG_CONFIRM_DELETE,'fm_'+idFm+'.deleteConfirmed();');
		}
	}
	
	this.deleteConfirmed = function(){
		var call = 'xajax_FileManager_delete(\''+idFm+'\', \''+actualPath+'\', ';
		var i;
		var count = selectedFiles.length;
		for(i = 0; i < count; i++){
			var element = selectedFiles[i];
			var extension = (element.extension != '' ? ('.' + element.extension) : '');
			var param = ('\'' + element.name + extension + '\'');
			if(i == count-1){
				call += (param + ');');
			}
			else{
				call += (param + ', ');
			}
		}
		eval(call);
	}
	
	this.setRenameInputValue = function(){
		if(selectedFiles.length == 1){
			var renameInput = FM_getObjectById(idFm+'_renameinput');
			renameInput.value = selectedFiles[0].name;
		}
	}
	
	this.rename = function(){
		if(selectedFiles.length != 1){
			eval('fm_'+idFm+'.openRenameWindow(); FM_showAlert(FM_ERROR_RENAME_ONE_SELECTED);');
		}
		else{
			var newName = FM_getObjectById(idFm+'_renameinput').value;
			if(newName == ''){
				eval('fm_'+idFm+'.openRenameWindow(); FM_showAlert(FM_ERROR_RENAME_EMPTY);');
			}
			else{
				var element = selectedFiles[0];
				var ext = (element.extension == '' ? '' : ('.' + element.extension));
				var name = element.name + ext;
				newName += ext;
				xajax_FileManager_rename(idFm,actualPath,name,newName);
			}
		}
	}
	
	this.cut = function(){
		copyFiles = new CopyFiles(actualPath,selectedFiles,true);
	}
	
	this.copy = function(){
		copyFiles = new CopyFiles(actualPath,selectedFiles,false);
	}
	
	this.paste = function(){
		if(copyFiles){
			var first = copyFiles.getFirst();
			if(first){
				var txtDiv = FM_getObjectById(idFm+'_copy_file');
				var name = first.name;
				var ext = (first.extension == '' ? '' : ('.' + first.extension));
				txtDiv.innerHTML = name + ext;
				eval('fm_'+idFm+'.openCopyWindow();');
				copyFiles.destPath = actualPath;
				xajax_FileManager_paste(idFm,copyFiles.srcPath,copyFiles.destPath,name,ext,copyFiles.remove());
			}
		}
	}
	
	this.responseFilePasted = function(){
		if(copyFiles){
			var next = copyFiles.next();
			if(next){
				var txtDiv = FM_getObjectById(idFm+'_copy_file');
				var name = next.name;
				var ext = (next.extension == '' ? '' : ('.' + next.extension));
				txtDiv.innerHTML = name + ext;
				xajax_FileManager_paste(idFm,copyFiles.srcPath,copyFiles.destPath,name,ext,copyFiles.remove());
			}
			else{
				eval('fm_'+idFm+'.refresh();');
				eval('fm_'+idFm+'.closeCopyWindow();');
			}
		}
	}
	
	this.getType = function(){
		return('DetailedView');
	}
}