// *****************************************
// TREE VIEW
// *****************************************
function TreeView(idFileManager,w,h){
	var idFm = idFileManager;
	var width = w;
	var height = h;
	var htmlComp = 	FM_getObjectById(idFileManager+'_tree_view');
	var htmlTreeComp;
	var htmlBarComp;
	var widthBar = 6;
	var minWidthBar = 50;
	this.moving = false;
	var mX1;
	var mX2;
	var selectedFolder = null;
	var extraHeight = 0;	
	defineHtml();

	function defineHtml(){
		var htmlCode = '';
		var mouseDownAction = 'onmousedown="fm_' + idFm + '.treeBarMouseDown(event);" ';
		var mouseUpAction = 'onmouseup="fm_' + idFm + '.treeBarMouseUp(event);" ';
		htmlCode += '<div id="' + idFileManager + '_tree" style="width:' + (width - widthBar) + 'px;height:' + (height - extraHeight) + 'px;overflow:auto;"></div>';
		htmlCode += '<div id="' + idFileManager + '_tree_bar" style="position:relative;top:-' + (height - extraHeight)  + 'px;left:' + (width - widthBar) + 'px;width:' + widthBar + 'px;height:' + (height - extraHeight)  + 'px;background-color:#999999;cursor:e-resize;" ' + mouseDownAction + mouseUpAction + '></div>';
		htmlComp.innerHTML = htmlCode;
		htmlTreeComp = 	FM_getObjectById(idFileManager+'_tree');
		htmlBarComp = 	FM_getObjectById(idFileManager+'_tree_bar');
		xajax_FileManager_hasDirectories(idFm,'/');
	}
	
	function mouseX(evt) {
		if (evt.pageX) return evt.pageX;
		else if (evt.clientX)
	   		return evt.clientX + (document.documentElement.scrollLeft ?	document.documentElement.scrollLeft : document.body.scrollLeft);
		else return null;
	}
	
	function setHeight(h){
		height = h;
		htmlComp.style.height = (h - extraHeight) + 'px';
		htmlTreeComp.style.height = (h - extraHeight) + 'px';
		htmlBarComp.style.height = (h - extraHeight) + 'px';
		htmlBarComp.style.top = '-' + (h - extraHeight) + 'px';
	}
	
	function setWidth(w){
		htmlComp.style.width = w + 'px';
		htmlTreeComp.style.width = (w - widthBar) + 'px';
		htmlBarComp.style.left = (w - widthBar) + 'px';
		width = w;
	}
	
	this.getWidth = function(){
		return(width);
	}
	
	function getIdSufix(folderPath,folderName){
		return(folderPath);
	}
	
	function translatePath(path,folderName){
		if(folderName == '/'){
			return('/');
		}
		else if(path == '/'){
			return('/' + folderName);
		}
		else{
			return(path + '/' + folderName);
		}
	}
	
	function getExpandIconHtml(colapsed,hasSubdirectories,folderPath,folderName){
		var imageUrl;
		var imagePath;
		var imageAttr;
		var actionClick;
		var idElement = idFm+'_tree_element_'+getIdSufix(translatePath(folderPath,folderName),folderName);
		if(colapsed){
			imageUrl = FM_TREE_PLUS_ICON;
		}
		else{
			imageUrl = FM_TREE_MINUS_ICON;
		}
		if(hasSubdirectories){
			imagePath = FM_SCREEN_PATH + '?i=' + imageUrl + '&w=' + FM_TREE_FOLDER_HEIGHT + '&h=' + FM_TREE_FOLDER_HEIGHT;
			imageAttr = 'background-image:url(' + imagePath + ');';
			actionClick = 'onclick="fm_'+idFm+'.expandOrColapse(\''+idElement+'\');" ';
		}
		else{
			imagePath = '';
			imageAttr = '';
		}
		return('<div style="' + imageAttr + 'background-position:center;background-repeat:no-repeat;width:' + FM_TREE_FOLDER_HEIGHT + 'px;height:' + FM_TREE_FOLDER_HEIGHT + 'px;cursor:pointer;" ' + actionClick + '></div>');
	}
	
	function getFolderLineHtml(folderName,idElement,idFolder){
		var imagePath = FM_SCREEN_PATH + '?i=' + FM_TREE_FOLDER_ICON + '&w=' + FM_TREE_FOLDER_HEIGHT + '&h=' + FM_TREE_FOLDER_HEIGHT;
		var imageAttr = 'background-image:url(' + imagePath + ');';
		var clickAction = 'onclick="fm_'+idFm+'.selectTreeFolder(\''+idElement+'\');" ';
		return('<div id="'+idFolder+'" '+ clickAction + 'class="' + FM_CSS_TREE_FOLDER + '" style="' + imageAttr + 'background-position:left;background-repeat:no-repeat;padding-left:' + (FM_TREE_FOLDER_HEIGHT+4) + 'px;cursor:default;font-family:Arial, Helvetica, sans-serif;font-size:' + (FM_TREE_FOLDER_HEIGHT-4) + 'px;line-height:' + FM_TREE_FOLDER_HEIGHT + 'px;width:100%;height:' + FM_TREE_FOLDER_HEIGHT + 'px;">' + folderName + '</div>');
	}
	
	function getFolderHtml(folderName,colapsed,hasSubdirectories,folderPath,idExpander,idFolder,idElement,idContainer){
		var htmlCode = '<table id="'+idElement+'" border="0" cellspacing="0" cellpadding="0">';
		htmlCode += '<tr>';
		htmlCode += '<td nowrap="nowrap" width="' + FM_TREE_FOLDER_HEIGHT + 'px" style="vertical-align:top;">';
		
		htmlCode += '<div id="'+idExpander+'" style="width:' + FM_TREE_FOLDER_HEIGHT + 'px;height:' + FM_TREE_FOLDER_HEIGHT + 'px;">';
		htmlCode += getExpandIconHtml(colapsed,hasSubdirectories,folderPath,folderName);
		htmlCode += '</div>';
		
		htmlCode += '</td>';
		htmlCode += '<td>';
		
		htmlCode += getFolderLineHtml(folderName,idElement,idFolder);
		
		htmlCode += '<div id="'+idContainer+'">';	
		htmlCode += '</div>';
		
		htmlCode += '</td>';
		
		htmlCode += '</td>';
		htmlCode += '</tr>';
		htmlCode += '</table>';
		return(htmlCode);
	}
	
	function setFolderHtml(folderName,colapsed,hasSubdirectories,folderPath,htmlComp){
		var idSufix = getIdSufix(folderPath,folderName);
		var idExpander = idFm+'_tree_expander_'+idSufix;
		var idFolder = idFm+'_tree_folder_'+idSufix;
		var idElement = idFm+'_tree_element_'+idSufix;
		var idContainer = idFm+'_tree_container_'+idSufix;
		htmlComp.innerHTML = getFolderHtml(folderName,colapsed,hasSubdirectories,folderPath,idExpander,idFolder,idElement,idContainer);
		var element = FM_getObjectById(idElement);
		element.expander = FM_getObjectById(idExpander);
		element.colapsed = colapsed;
		element.container = FM_getObjectById(idContainer);
		element.folder = FM_getObjectById(idFolder);
		element.folderName = folderName;
		element.folderPath = folderPath;
		element.hasDirectories = hasSubdirectories;
	}
	
	function addNodes(element,dirs,path){
		var idSufix;
		var idExpander;
		var idFolder;
		var idElement;
		var idContainer;
		var arrayElement = new Array();
		var arrayExpander = new Array();
		var arrayContainer = new Array();
		var arrayFolder = new Array();
		var arrayFolderName = new Array();
		var hasDirs = new Array();
		var htmlCode = '';
		var dir;
		var i = 0;
		for(i = 0; i < dirs.length; i++){
			dir = dirs[i];
			idSufix = getIdSufix(translatePath(path,dir.name),dir.name);
			idExpander = idFm+'_tree_expander_'+idSufix;
			idFolder = idFm+'_tree_folder_'+idSufix;
			idElement = idFm+'_tree_element_'+idSufix;
			idContainer = idFm+'_tree_container_'+idSufix;
			htmlCode += getFolderHtml(dir.name,true,dir.hasDirectories,path,idExpander,idFolder,idElement,idContainer);
			arrayElement[i] = idElement;
			arrayExpander[i] = idExpander;
			arrayContainer[i] = idContainer;
			arrayFolder[i] = idFolder;
			arrayFolderName[i] = dir.name;
			hasDirs[i] = dir.hasDirectories;
		}
		element.container.innerHTML = htmlCode;
		for(i = 0; i < dirs.length; i++){
			var el = FM_getObjectById(arrayElement[i]);
			el.expander = FM_getObjectById(arrayExpander[i]);
			el.colapsed = true;
			el.container = FM_getObjectById(arrayContainer[i]);
			el.folder = FM_getObjectById(arrayFolder[i]);
			el.folderName = arrayFolderName[i];
			el.folderPath = path;
			el.hasDirectories = hasDirs[i];
		}
	}
	
	function getWidth(){
		return(width);
	}
	
	function expand(element){
		xajax_FileManager_getDirectories(idFm,translatePath(element.folderPath,element.folderName), element.folderName);
	}
	
	function colapse(element){
		element.colapsed = true;
		if(!element.hasDirectories){
			element.expander.innerHTML = '';
		}
		else{
			element.expander.innerHTML = getExpandIconHtml(true,true,element.folderPath,element.folderName);
		}
		element.container.innerHTML = '';
	}
	
	this.handleMouseDown = function(e){
		if (!e) var e = window.event;
		this.moving = true;
        eval('document.onmousemove = fm_'+idFm+'.treeBarMouseMove;');
		mX1 = mouseX(e);
	}
	
	this.handleMouseUp = function(e){
		if (!e) var e = window.event;
		if(this.moving){
			mX2 = mouseX(e);
			var w = width + (mX2 - mX1);
			if(w < minWidthBar) this.moving = false;
			else{
				setWidth(w);
				mX1 = mX2;
			}
			
		}
		this.moving = false;
	}
	
	this.handleMouseMove = function(e){
		if (!e) var e = window.event;
		if(this.moving){
			mX2 = mouseX(e);
			var w = width + (mX2 - mX1);
			if(w < minWidthBar) this.moving = false;
			else{
				setWidth(w);
				mX1 = mX2;
			}
		}
	}
	
	this.getWidth = function(){
		return width;
	}
	
	this.resize = function(h,cextraHeight){
		extraHeight = cextraHeight;
		setHeight(h);
	}
	
	this.responseHasDirectories = function(value,path){
		if(path == '/'){
			setFolderHtml('/',true,value,'/',htmlTreeComp);
		}
	}
	
	this.responseDirectories = function(dirs,path,name){
		var idSufix = getIdSufix(path,name);
		var idElement = idFm+'_tree_element_'+idSufix;
		var element = FM_getObjectById(idElement);
		addNodes(element,dirs,path);
		element.colapsed = false;
		element.expander.innerHTML = getExpandIconHtml(false,element.hasDirectories,element.folderPath,element.folderName);
	}
	
	this.expandOrColapse = function(idElement){
		var element = FM_getObjectById(idElement);
		if(element.colapsed){
			expand(element);
		}
		else{
			colapse(element);
		}
	}
	
	this.selectTreeFolder = function(idElement){
		var element = FM_getObjectById(idElement);
		if(selectedFolder != null){
			selectedFolder.folder.className = FM_CSS_TREE_FOLDER;
		}
		selectedFolder = element;
		selectedFolder.folder.className = FM_CSS_TREE_FOLDER_SELECTED;
		var newPath = translatePath(selectedFolder.folderPath,selectedFolder.folderName);
		eval('fm_'+idFm+'.requestFiles(\''+newPath+'\');');
	}
}