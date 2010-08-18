function FM_getObjNN4(obj,name){
	var x = obj.layers;
	var foundLayer;
	
	for (var i=0;i<x.length;i++)
	{
		if (x[i].id == name)
			foundLayer = x[i];
		else if (x[i].layers.length)
			var tmp = FM_getObjNN4(x[i],name);
		if (tmp) foundLayer = tmp;
	}
	return foundLayer;
}
	
function FM_getObjectById(name){
	var obj;
	
	if(document.getElementById){
		obj = document.getElementById(name);
	}
	else if(document.all)
	{
		obj = document.all[name];
	}
	else if(document.layers)
	{
		obj = FM_getObjNN4(document,name);
		obj.style = obj;
	}
	return obj;
}

function FM_showAlert(msg){
	alert_dialogs[FM_ALERT_DIALOG_INDEX][1] = msg;
	var fwindow = fwindow_Set.getFirst().toBack();
	fwindow = fwindow_Set.next();
	while(fwindow){
		fwindow.toBack();
		fwindow = fwindow_Set.next();
	}
	openAlertDialog(FM_ALERT_DIALOG_INDEX);
}

function FM_showConfirm(msg,fcn){
	// Solución temporal para que no concatene eventos
	var btns = FM_getObjectById('confirmation_dialog_buttons');
	btns.innerHTML = '<input name="Accept" type="button" value="'+FM_CONFIRM_DIALOG_ACCEPT+'" onClick="" /><input name="Cancel" type="button" value="'+FM_CONFIRM_DIALOG_CANCEL+'" onClick="" />';
	addConfirmationDialog(FM_CONFIRM_DIALOG_INDEX,FM_CONFIRM_DIALOG_TITLE,msg,fcn+';FM_hideDialog()','FM_hideDialog()');
	var fwindow = fwindow_Set.getFirst().toBack();
	fwindow = fwindow_Set.next();
	while(fwindow){
		fwindow.toBack();
		fwindow = fwindow_Set.next();
	}
	openConfirmationDialog(FM_CONFIRM_DIALOG_INDEX,'');
}

function FM_hideDialog(){
	var first = fwindow_Set.getFirst();
	var win;
	if(first){
		first.toFront();
		while(win = fwindow_Set.next()){
			win.toFront();
		}
	}
}

function FM_keyPressed(e) {
	var keys = new Object();
	keys.ctrl = false;
	keys.alt = false;
	keys.shift = false;
	if (!e) var e = window.event;
	if (parseInt(navigator.appVersion)>3){
		if (navigator.appName=="Netscape" && parseInt(navigator.appVersion)==4) {
   			// NETSCAPE 4 CODE
   			var mString =(e.modifiers+32).toString(2).substring(3,6);
   			keys.shift = (mString.charAt(0)=="1");
   			keys.ctrl = (mString.charAt(1)=="1");
   			keys.alt  = (mString.charAt(2)=="1");
   		}
   		else{
   			// NEWER BROWSERS [CROSS-PLATFORM]
   			keys.shift = e.shiftKey;
   			keys.alt  = e.altKey;
   			keys.ctrl = e.ctrlKey;
   		}
 	}
 	return keys;
 }

// *****************************************
// OBJECTS
// *****************************************
function FileDescription(fpath,fhttpPath, fisDirectory, fname, fextension, fisImage, fsize, flastTime){
	this.path = fpath;
	this.httpPath = fhttpPath;
	this.isDirectory = fisDirectory;
	this.name = fname;
	this.extension = fextension;
	this.isImage = fisImage;
	this.size = fsize;
	this.lastTime = flastTime;
	
	this.isSelected = false;
	this.index = -1;
}

function DirectoryDescription(dpath,dname,dhasDirectories){
	this.path = dpath;
	this.name = dname;
	this.hasDirectories = dhasDirectories;
}

function CopyFiles(cSourcePath,cFiles,cRemove){
	var rem = cRemove;
	this.srcPath = cSourcePath;
	var files;
	var len = cFiles.length;
	var actualIndex;
	this.destPath;
	init(cFiles);
	
	function init(cFiles){
		files = new Array();
		var i;
		for(i = 0; i < len; i++){
			files[i] = cFiles[i];
		}
	}
	
	this.remove = function(){
		return rem;
	}
	
	this.getFirst = function(){
		if(len < 1){
			return false;
		}
		else{
			actualIndex = 0;
			return(files[actualIndex++]);
		}
	}
	
	this.next = function(){
		if(actualIndex == len){
			if(this.remove()){
				files = new Array();
				len = 0;
			}
			return false;
		}
		else{
			return(files[actualIndex++]);
		}
	}
}