// *****************************************
// IMAGE VIEW
// *****************************************
function ImageView(idFileManager){
	var idFm = idFileManager;
	var htmlComp = 	FM_getObjectById(idFileManager+'_view');
	var htmlContainerComp;
	var htmlCreated = false;
	var actualFiles = new Array();
	var selectedFiles = new Array();
	var actualPath = '';
	var extraHeight = 0;
	var copyFiles;
	
	function getHtmlForElement(fileIndex,top,left){
		var element;
		if(fileIndex > -1){
			element = actualFiles[fileIndex];
			element.index = fileIndex;
		}
		var w = FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2;
		var h = w;
		var css = element.isSelected ? FM_CSS_IMAGE_VIEW_ELEMENT_SELECTED : FM_CSS_IMAGE_VIEW_ELEMENT;
		var clickAction =  'onclick="fm_' + idFm + '.elementClicked(event);"';
		var dobleClickAction =  'ondblclick="fm_' + idFm + '.elementDobleClicked(event);"';
		var htmlCode = '<div id="' + idFm +'_image_view_element_' + fileIndex + '" style="position:relative;top:' + top + 'px;left:' + left + 'px;width:' + w + 'px;height:' + h + 'px;" class="' + css + '">'
		var imgPath = '';
		var name =  (element.extension == '' ? element.name : element.name + '.' + element.extension);
		if(element.isDirectory){
			if(name == '..'){
				imgPath = FM_SCREEN_PATH + '?i=' + FM_FOLDER_UP_ICON + '&w=' + FM_IMAGE_VIEW_IMAGE_SIZE + '&h=' + FM_IMAGE_VIEW_IMAGE_SIZE;
			}
			else{
				imgPath = FM_SCREEN_PATH + '?i=' + FM_FOLDER_ICON + '&w=' + FM_IMAGE_VIEW_IMAGE_SIZE + '&h=' + FM_IMAGE_VIEW_IMAGE_SIZE;
			}
		}
		else if(element.isImage){
			imgPath = FM_SCREEN_PATH + '?i=' + element.path + '&w=' + FM_IMAGE_VIEW_IMAGE_SIZE + '&h=' + FM_IMAGE_VIEW_IMAGE_SIZE;
		}
		else{
			imgPath = FM_SCREEN_PATH + '?i=' + FM_FILE_ICON + '&w=' + FM_IMAGE_VIEW_IMAGE_SIZE + '&h=' + FM_IMAGE_VIEW_IMAGE_SIZE;
		}
		h = w - FM_IMAGE_VIEW_BORDER_SIZE;
		htmlCode += '<div id="' + idFm + '_image_view_image_' + fileIndex + '" style="width:' + w + 'px;height:' + h + 'px;background-image:url(' + imgPath + ');background-repeat:no-repeat;background-position:center;" ' + clickAction + ' ' + dobleClickAction + '></div>';
		h = FM_IMAGE_VIEW_BORDER_SIZE;
		htmlCode += '<div id="' + idFm + '_image_view_name_' + fileIndex + '" style="width:' + w + 'px;height:' + h + 'px;overflow:hidden;" align="center">' + name + '</div>';
		htmlCode += '</div>';
		return htmlCode;
	}
	
	function getHtmlForRow(rowNum,maxIndex){
		var fileIndex = (rowNum * FM_IMAGE_VIEW_COLUMNS);
		var htmlCode = '';
		var i;
		var col = 0;
		for(i = fileIndex; ( (i < fileIndex + FM_IMAGE_VIEW_COLUMNS) && (i < maxIndex) ); i++){
			var left = (FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2) * col;
			var top = -(rowNum * ((FM_IMAGE_VIEW_COLUMNS - 1) * (FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2)) + left);
			htmlCode += getHtmlForElement(i,top,left);
			col++;
		}
		return htmlCode;
	}
	
	function createHtml(){
		var countElements = actualFiles.length;
		var countRows = Math.floor(countElements / FM_IMAGE_VIEW_COLUMNS);
		if((countElements % FM_IMAGE_VIEW_COLUMNS) > 0) countRows++;
		var i;
		var w = FM_IMAGE_VIEW_COLUMNS * (FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2);
		var h = (countRows * (FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2)) + extraHeight;
		var htmlCode = '<div id="' + idFm +'_image_view_container' + '" style="overflow:hidden;width:' + w + 'px;height:' + h + 'px;">';
		for(i = 0; i < countRows; i++){
			htmlCode += getHtmlForRow(i,countElements);
		}
		htmlCode += '</div>';
		htmlComp.innerHTML = htmlCode;
		for(i = 0; i < countElements; i++){
			actualFiles[i].htmlComp = FM_getObjectById(idFm +'_image_view_element_' + i);
			actualFiles[i].htmlComp.imageComp = FM_getObjectById(idFm +'_image_view_image_' + i);
			actualFiles[i].htmlComp.nameComp = FM_getObjectById(idFm +'_image_view_name_' + i);
			actualFiles[i].index = i;
		}
		htmlContainerComp = FM_getObjectById(idFm +'_image_view_container');
	}
	
	function actualizeHtmlElement(fileIndex,top,left){
		var element = actualFiles[fileIndex];
		var comp = element.htmlComp;
		var imageComp = comp.imageComp;
		var nameComp = comp.nameComp;
		var w = FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2;
		var h = w;
		var css = element.isSelected ? FM_CSS_IMAGE_VIEW_ELEMENT_SELECTED : FM_CSS_IMAGE_VIEW_ELEMENT;
		comp.style.left = left + 'px';
		comp.style.top = top + 'px';
		comp.style.width = w + 'px';
		comp.style.height = h + 'px';
		comp.className = css;
		h = w - FM_IMAGE_VIEW_BORDER_SIZE;
		imageComp.style.width = w + 'px';
		imageComp.style.height = h + 'px';
		h = FM_IMAGE_VIEW_BORDER_SIZE;
		nameComp.style.width = w + 'px';
		nameComp.style.height = h + 'px';
	}
	
	function actualizeHtmlRow(rowNum,maxIndex){
		var fileIndex = (rowNum * FM_IMAGE_VIEW_COLUMNS);
		var i;
		var col = 0;
		for(i = fileIndex; ( (i < fileIndex + FM_IMAGE_VIEW_COLUMNS) && (i < maxIndex) ); i++){
			var left = (FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2) * col;
			var top = -(rowNum * ((FM_IMAGE_VIEW_COLUMNS - 1) * (FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2)) + left);
			actualizeHtmlElement(i,top,left);
			col++;
		}
	}
	
	function actualizeHtml(){
		var countElements = actualFiles.length;
		var countRows = Math.floor(countElements / FM_IMAGE_VIEW_COLUMNS);
		if((countElements % FM_IMAGE_VIEW_COLUMNS) > 0) countRows++;
		var i;
		var w = FM_IMAGE_VIEW_COLUMNS * (FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2);
		var h = (countRows * (FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2)) + extraHeight;
		htmlContainerComp.style.width = w + 'px';
		htmlContainerComp.style.height = h + 'px';
		for(i = 0; i < countRows; i++){
			actualizeHtmlRow(i,countElements);
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
		FM_IMAGE_VIEW_COLUMNS = Math.floor(width / (FM_IMAGE_VIEW_IMAGE_SIZE + FM_IMAGE_VIEW_BORDER_SIZE * 2));
		if(FM_IMAGE_VIEW_COLUMNS < 1) FM_IMAGE_VIEW_COLUMNS = 1;
		actualizeView();
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
		return('ImageView');
	}
}