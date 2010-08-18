// *****************************************
// FILE MANAGER
// *****************************************
function FileManager(idFileManager){
    var idFm = idFileManager;
    var htmlComp = FM_getObjectById(idFileManager);
    var view;
    var actualPath;
    var pathEncoded = '%2F';
    var treeView = null;
    var locationBar = null;
    var iconBar = null;
    var treeWidth = 150;
    var uploadFileIndex;
    var createDirectoryIndex;
    var renameIndex;
    var copyIndex;
    var configureIndex;
    var actualUploadFormIndex = 0;
    
    function windowSize(){
        var myWidth = 0, myHeight = 0;
        if (typeof(window.innerWidth) == 'number') {
            //Non-IE
            myWidth = window.innerWidth;
            myHeight = window.innerHeight;
        }
        else 
            if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
                //IE 6+ in 'standards compliant mode'
                myWidth = document.documentElement.clientWidth;
                myHeight = document.documentElement.clientHeight;
            }
            else 
                if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
                    //IE 4 compatible
                    myWidth = document.body.clientWidth;
                    myHeight = document.body.clientHeight;
                }
        var obj = new Object();
        obj.width = myWidth;
        obj.height = myHeight;
        return obj;
    }
    
    function privateResize(){
        var size = new Object();
        if (FM_AUTOSIZE) {
            size = windowSize();
        }
        else {
            size.width = FM_WIDTH;
            size.height = FM_HEIGHT;
        }
        var extraHeight = 0;
        if (locationBar != null) {
            extraHeight += (locationBar.height + 6);
        }
        if (iconBar != null) {
            extraHeight += (iconBar.height + 6);
        }
        htmlComp.style.width = size.width + 'px';
        htmlComp.style.height = size.height + 'px';
        var w = size.width;
        var withTree = false;
        if (treeView != null) {
            w = size.width - treeView.getWidth();
            treeView.resize(size.height, extraHeight);
            withTree = true;
        }
        view.resize(w, size.height, extraHeight, withTree);
    }
    
    this.setView = function(val){
        view = val;
    }
    
    this.setLocationBar = function(val){
        locationBar = val;
    }
    
    this.setIconBar = function(val){
        iconBar = val;
    }
    
    this.requestFiles = function(path){
        xajax_FileManager_getFiles(idFm, path);
    }
    
    this.responseFiles = function(files, path, pathEnc){
        pathEncoded = pathEnc;
        view.setFiles(path, files);
        actualPath = path;
        if (locationBar != null) {
            locationBar.setLocationString(path);
        }
    }
    
    this.responseDirectories = function(dirs, path, name){
        if (treeView != null) {
            treeView.responseDirectories(dirs, path, name);
        }
    }
    
    this.responseHasDirectories = function(value, path){
        if (treeView != null) {
            treeView.responseHasDirectories(value, path)
        }
    }
    
    this.elementClicked = function(e){
        if (!e) 
            e = event;
        view.handleClick(e);
    }
    
    this.elementDobleClicked = function(e){
        if (!e) 
            e = event;
        view.handleDobleClick(e);
    }
    
    this.treeBarMouseDown = function(e){
        if (!e) 
            e = event;
        if (treeView != null) {
            treeView.handleMouseDown(e);
        }
    }
    
    this.treeBarMouseUp = function(e){
        if (!e) 
            e = event;
        if (treeView != null) {
            treeView.handleMouseUp(e);
        }
    }
    
    this.treeBarMouseMove = function(e){
        if (!e) 
            e = event;
        if (treeView != null) {
            if (treeView.moving) {
                treeView.handleMouseMove(e);
                view.setLeft(treeView.getWidth());
                privateResize();
            }
        }
    }
    
    this.resize = function(){
        privateResize();
    }
    
    this.setTree = function(tree){
        treeView = tree;
    }
    
    this.expandOrColapse = function(idFolder){
        treeView.expandOrColapse(idFolder);
    }
    
    this.selectTreeFolder = function(idFolder){
        treeView.selectTreeFolder(idFolder);
    }
    
    this.changeLocation = function(){
        if (locationBar != null) {
            this.requestFiles(locationBar.getLocationString());
        }
    }
    
    this.goUp = function(){
        if (actualPath != '/') {
            this.requestFiles(actualPath + '../');
        }
    }
    
    this.showHideTree = function(){
        var treeDiv = FM_getObjectById(idFm + '_tree_view');
        var viewDiv = FM_getObjectById(idFm + '_view');
        if (treeView != null) {
            treeWidth = treeView.getWidth();
            FM_TREE_WIDTH = 0;
            treeDiv.innerHTML = '';
            treeDiv.style.width = '0px';
            treeView = null;
            viewDiv.style.left = '0px';
        }
        else {
            FM_TREE_WIDTH = treeWidth;
            treeDiv.style.width = FM_TREE_WIDTH + 'px';
            viewDiv.style.left = FM_TREE_WIDTH + 'px';
            this.setTree(new TreeView(idFm, FM_TREE_WIDTH, FM_HEIGHT));
        }
        privateResize();
    }
    
    this.setUploadFileIndex = function(index){
        uploadFileIndex = index;
    }
    
    this.setCreateDirectoryIndex = function(index){
        createDirectoryIndex = index;
    }
    
    this.setRenameIndex = function(index){
        renameIndex = index;
    }
    
    this.setCopyIndex = function(index){
        copyIndex = index;
    }
    
    this.setConfigureIndex = function(index){
        configureIndex = index;
    }
    
    this.openUploadFileWindow = function(){
        var fwindow = fwindow_Set.getWindow(uploadFileIndex);
        fwindow.toCenterPosition();
        openWindow(uploadFileIndex);
    }
    
    this.openCreateDirectoryWindow = function(){
        var fwindow = fwindow_Set.getWindow(createDirectoryIndex);
        fwindow.toCenterPosition();
        openWindow(createDirectoryIndex);
    }
    
    this.openRenameWindow = function(){
        var fwindow = fwindow_Set.getWindow(renameIndex);
        fwindow.toCenterPosition();
        view.setRenameInputValue();
        openWindow(renameIndex);
    }
    
    this.openCopyWindow = function(){
        var fwindow = fwindow_Set.getWindow(copyIndex);
        fwindow.toCenterPosition();
        openWindow(copyIndex);
    }
    
    this.openConfigureWindow = function(){
        var fwindow = fwindow_Set.getWindow(configureIndex);
        fwindow.toCenterPosition();
        openWindow(configureIndex);
    }
    
    this.closeCopyWindow = function(){
        closeWindow(copyIndex);
    }
	
	this.closeConfigureWindow = function(){
        closeWindow(configureIndex);
    }
    
    this.addUploadForm = function(){
        var actualIndex = (actualUploadFormIndex++);
        var htmlComp = FM_getObjectById(idFm + '_next_upload_' + actualIndex);
        var html = '<div id=\'' + idFm + '_next_upload_' + actualUploadFormIndex + '\'></div><div id=\'' + idFm + '_upload_content_' + actualIndex + '\'><iframe id=\'' + idFm + '_upload_iframe_' + actualIndex + '\' allowtransparency="true" style="border:0px;margin:0px;padding:0px;width:100%;height:50px;" scrolling="no" src="' + FM_UPLOAD_IFRAME_SRC + '?idFm=' + idFm + '&path=' + pathEncoded + '&uploadIndex=' + actualIndex + '"></iframe></div>';
        htmlComp.innerHTML = html;
    }
    
    this.removeUploadForm = function(index){
        var htmlComp = FM_getObjectById(idFm + '_upload_content_' + index);
        htmlComp.innerHTML = '';
        htmlComp.style.display = 'none';
        htmlComp.style.visibility = 'hidden';
    }
    
    this.submitUploadForm = function(index){
        var ifr = FM_getObjectById(idFm + '_upload_iframe_' + index);
        ifr.contentWindow.submitForm();
    }
    
    this.refresh = function(){
        xajax_FileManager_getFiles(idFm, actualPath);
    }
    
    this.deleteFiles = function(){
        view.deleteFiles();
    }
    
    this.deleteConfirmed = function(){
        view.deleteConfirmed();
    }
    
    this.createDirectory = function(){
        var name = FM_getObjectById(idFm + '_createDirectoryinput').value;
        xajax_FileManager_createDirectory(idFm, actualPath, name);
    }
    
    this.rename = function(){
        view.rename();
    }
    
    this.cut = function(){
        view.cut();
    }
    
    this.copy = function(){
        view.copy();
    }
    
    this.paste = function(){
        view.paste();
    }
    
    this.responseFilePasted = function(){
        view.responseFilePasted();
    }
    
    this.changeView = function(){
        var actual = '';
        if (view) {
            actual = view.getType();
        }
        if (actual == 'ImageView') {
            this.setView(new DetailedView(idFm));
        }
        else {
            this.setView(new ImageView(idFm));
        }
        this.requestFiles(actualPath);
        privateResize();
    }
    
    this.configure = function(){
        var numThumbsSize = parseInt(FM_getObjectById(idFm + '_input_thumbs').value);
        var numDetailIcon = parseInt(FM_getObjectById(idFm + '_input_detail_icon').value);
        var numPercentName = parseInt(FM_getObjectById(idFm + '_input_percent_name').value);
        var numPercentExt = parseInt(FM_getObjectById(idFm + '_input_percent_ext').value);
        var numPercentLastAcces = parseInt(FM_getObjectById(idFm + '_input_percent_last_access').value);
        var numPercentSize = parseInt(FM_getObjectById(idFm + '_input_percent_size').value);
        
        var valThumbsSize = FM_getObjectById(idFm + '_validate_thumbs');
        var valDetailIcon = FM_getObjectById(idFm + '_validate_detail_icon');
        var valPercentName = FM_getObjectById(idFm + '_validate_percent_name');
        var valPercentExt = FM_getObjectById(idFm + '_validate_percent_ext');
        var valPercentLastAcces = FM_getObjectById(idFm + '_validate_percent_last_access');
        var valPercentSize = FM_getObjectById(idFm + '_validate_percent_size');
        var valPercentTotal = FM_getObjectById(idFm + '_validate_percent_total');
        
        var isValid = true;
        if (isNaN(numThumbsSize) || numThumbsSize < 1) {
            valThumbsSize.style.display = 'block';
            isValid = false;
        }
        else {
            valThumbsSize.style.display = 'none';
        }
        if (isNaN(numDetailIcon) || numDetailIcon < 1) {
            valDetailIcon.style.display = 'block';
            isValid = false;
        }
        else {
            valDetailIcon.style.display = 'none';
        }
        if (isNaN(numPercentName) || numPercentName < 1) {
            valPercentName.style.display = 'block';
            isValid = false;
        }
        else {
            valPercentName.style.display = 'none';
        }
        if (isNaN(numPercentExt) || numPercentExt < 1) {
            valPercentExt.style.display = 'block';
            isValid = false;
        }
        else {
            valPercentExt.style.display = 'none';
        }
        if (isNaN(numPercentLastAcces) || numPercentLastAcces < 1) {
            valPercentLastAcces.style.display = 'block';
            isValid = false;
        }
        else {
            valPercentLastAcces.style.display = 'none';
        }
        if (isNaN(numPercentSize) || numPercentSize < 1) {
            valPercentSize.style.display = 'block';
            isValid = false;
        }
        else {
            valPercentSize.style.display = 'none';
        }
        if (numPercentName + numPercentLastAcces + numPercentExt + numPercentSize == 100) {
            valPercentTotal.style.display = 'none';
        }
		else{
			valPercentTotal.style.display = 'block';
            isValid = false;
		}
		
		if(isValid){
			FM_IMAGE_VIEW_IMAGE_SIZE = numThumbsSize;
			FM_DETAILED_VIEW_ELEMENT_HEIGHT = numDetailIcon;
			FM_DETAILED_VIEW_NAME_PERCENT = numPercentName;
			FM_DETAILED_VIEW_EXTENSION_PERCENT = numPercentExt;
			FM_DETAILED_VIEW_LAST_TIME_PERCENT = numPercentLastAcces;
			FM_DETAILED_VIEW_SIZE_PERCENT = numPercentSize;
			this.refresh();
			privateResize();
			this.closeConfigureWindow();
		}
        
    }
}
