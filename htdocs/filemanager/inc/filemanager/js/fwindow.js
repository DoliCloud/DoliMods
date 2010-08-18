/**
 * FWindows CLASS
 * @author Fernando Hernandez - Freelance Soft
 * www.freelancesoft.com.ar
 *
 * DESCRIPCION:
 * Clase FWindow.
 *
 * Testeadas en Opera 9, Firefox 2 e IE 6, 7
 */

function FWindow(num, title, content, add_to_id, background){
   //Window configuration
   var fwindow_maximized=false;
   var fwindow_open=false; 
   var fwindow_num=num;
   var fwindow_title=title;
   var fwindow_parent_node;
   var fwindow_top_limit = 0;
   var fwindow_background=background;
   
   //for restaure window
   var fwindows_previous_width;
   var fwindows_previous_heigth;
   var fwindows_previous_top;
   var fwindows_previous_left;
   
   var parent=document.getElementById(add_to_id);
	
   window_parent_node=parent;
   
   parent.innerHTML=parent.innerHTML+
    '<div id="fwindow_'+fwindow_num+'" class="fwindow_class" onclick="toFront('+fwindow_num+')">'+
      '<div id="fwindow_'+fwindow_num+'_title" class="fwindow_title" onmousedown="moveWindows_move(\'fwindow_'+fwindow_num+'\');" onmouseup="moveWindows_leave(\'fwindow_'+fwindow_num+'\');" ondblclick="javascript:changeWindowsSize('+fwindow_num+')" >'+
      '<div class="fwindow_title_text">'+title+'</div>'+
      '<span class="fwindow_actions"> <a href="javascript:minimizeWindow('+fwindow_num+')"><img src="'+image_path+'minimize.png" border="0" alt="" /></a> <a href="javascript:changeWindowsSize('+fwindow_num+')"><img src="'+image_path+'view-fullscreen.png" border="0" alt="" /></a> <a href="javascript:closeWindow('+fwindow_num+')"><img src="'+image_path+'close.png" border="0" alt="Cerrar" /></a> '+
      '  </span> '+
      '</div> '+
      ' <div id="fwindow_'+fwindow_num+'_content" class="fwindow_content">'+
          content +
      '</div>'+
      '<div class="fwindow_resize"> '+
      ' <img src="'+image_path+'resize.png" alt="" onmousedown="moveWindows_resize(\'fwindow_'+fwindow_num+'\');" onmouseup="moveWindows_leave(\'fwindow_'+fwindow_num+'\');" /> '+
      '</div>'+
     '</div>';
   
   if(fwindow_background){
   	 parent.innerHTML=parent.innerHTML+'<div id="fwindow_background_'+fwindow_num+'" class="fwindow_background"></div>';
   }
  
   fwindows_object=document.getElementById('fwindow_'+fwindow_num);
  
   this.isMaximized=function isMaximized(){
   	  return fwindow_maximized;
   }
  
  this.haveBackground=function haveBackground(){
  	return fwindow_background;
  }
  
  this.setDimension=function setDimension(w,h){	
     fwindows_object=document.getElementById('fwindow_'+fwindow_num);
	 
	 fwindows_object.style.width=w+"px";
	 fwindows_object.style.height=h+"px";
  }
  
  this.toCenterPosition=function toCenterPosition(){
     var pos=CenterPosition();
     var x,y;
	 
	 x=pos[0]-(fwindows_object.style.width.split("p")[0] / 2);
	 y=pos[1]-(fwindows_object.style.height.split("p")[0] / 2);
		  
	 fwindows_object.style.top = y + "px";
     fwindows_object.style.left = x + "px";
  }
   
  this.getTitle=function getTitle(){
  	return fwindow_title;
  }
  
  this.getID=function getID(){
  	return fwindow_num;
  }
  
   this.setTopLimit=function setTopLimit(TopLimit){
  	 fwindow_top_limit=TopLimit;
   }
  
   this.isWindowOpen=function isWindowOpen(){
  	 return fwindow_open;
   }
  
    this.toFront=function toFront(){	
	  fwindows_object=document.getElementById('fwindow_'+fwindow_num);
	  
	  fwindows_object.style.zIndex="1";
	  fwindows_object.style.opacity= "1";
	  fwindows_object.style.filter="alpha(opacity=100)";
	} 
	
    this.toBack=function toBack(){	
	  fwindows_object=document.getElementById('fwindow_'+fwindow_num);
	  
	  fwindows_object.style.zIndex="0";
	  fwindows_object.style.opacity= "0.6";
	  fwindows_object.style.filter="alpha(opacity=60)";
	} 	
	 
     this.openWindow=function openWindow(){
	   fwindows_object=document.getElementById('fwindow_'+fwindow_num);		
	   var x,y=0;
       
	   if(!fwindow_open){ 
	      if(fwindow_background){
			document.getElementById('fwindow_background_'+fwindow_num).style.visibility="visible";
	      }     
          fwindows_object.style.visibility= "visible";
		  fwindow_open=true;
	   } else {
	      this.toFront(); 
	   }   
	 }
	 
	 this.closeWindow=function closeWindow(){
	   fwindows_object=document.getElementById('fwindow_'+fwindow_num);	 
	   
	   if(fwindow_background){
			document.getElementById('fwindow_background_'+fwindow_num).style.visibility="hidden";
	   }
	   
       fwindows_object.style.visibility= "hidden";
	   window_parent_node.removeChild(fwindows_object);
	 }
	 
	 this.minimizeWindow=function minimizeWindow(){
	   fwindows_object=document.getElementById('fwindow_'+fwindow_num);		
		
	   if(fwindow_background){
			document.getElementById('fwindow_background_'+fwindow_num).style.visibility="hidden";
	   }	
		
	   fwindow_open=false;
       fwindows_object.style.visibility= "hidden";
	 }
	 
	 this.maximizeWindow=function maximizeWindow(){
	   var max_x, max_y=0;
	   fwindows_object=document.getElementById('fwindow_'+fwindow_num);
        
	   //Save actual dimensions
	   fwindows_previous_width=fwindows_object.style.width;
	   fwindows_previous_height=fwindows_object.style.height;
	   fwindows_previous_top=fwindows_object.style.top;
	   fwindows_previous_left=fwindows_object.style.left;	
		
	   var obj=sizeWindows();
       max_x=(obj.width - 15);  // - 10px por scroll bar
	   max_y=obj.height;

	   fwindows_object.style.top = fwindow_top_limit+"px";
       fwindows_object.style.left = "0px";
	   fwindows_object.style.width = max_x + "px";
	   fwindows_object.style.height = max_y + "px";
       fwindows_object.style.visibility= "visible";	 
	   fwindow_maximized=true;  
	 }
	 
	 this.restaureWindow=function restaureWindow(){
	   var max_x, max_y=0;
	   fwindows_object=document.getElementById('fwindow_'+fwindow_num);
 
	   fwindows_object.style.width = fwindows_previous_width;
	   fwindows_object.style.height = fwindows_previous_height; 
	   fwindows_object.style.top = fwindows_previous_top;
	   fwindows_object.style.left = fwindows_previous_left;
	   
	   fwindow_maximized=false; 
	 }

//-------------------------------------------Funciones complementarias
	  function getChildElementByName(Node, Name){
   	    for( var x = 0; x < Node.childNodes.length; x++ ) {
	        if(Node.childNodes[x].name==Name){
				return Node.childNodes[x];
			}
        }
   }
    	
   function sizeWindows(){
		var myWidth = 0, myHeight = 0;
		if( typeof( window.innerWidth ) == 'number' ){
			//Non-IE
			myWidth = window.innerWidth;
  		  	myHeight = window.innerHeight;
		} 
		else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ){
			//IE 6+ in 'standards compliant mode'
 		  	myWidth = document.documentElement.clientWidth;
 		   	myHeight = document.documentElement.clientHeight;
		} 
		else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ){
			//IE 4 compatible
  		  	myWidth = document.body.clientWidth;
  		  	myHeight = document.body.clientHeight;
		}
		var obj = new Object();
		obj.width = myWidth;
		obj.height = myHeight;
		return obj;
	}
   
    function CenterPosition() {
	  var obj=sizeWindows();
	  
	  x=obj.width / 2;
	  y=obj.height / 2;
	  return [x,y];
   }
	 
} //fin de la clase FWindow