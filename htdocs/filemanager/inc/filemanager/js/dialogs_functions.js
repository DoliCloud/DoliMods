/**
 * Dialogs Functions
 * @author Fernando Hernandez - Freelance Soft
 * www.freelancesoft.com.ar
 *
 * DESCRIPCION:
 * Conjunto de funciones para alert y confirmation dialogs.
 *
 */
    var confirmation_dialogs = new Array();
    var alert_dialogs = new Array(); 
	
	function addConfirmationDialog(num, title, text, func_accept, func_cancel){
       confirmation_dialogs[num]=new Array(title,text,func_accept,func_cancel);
	}
	
	function addAlertDialog(num, title, text){		
       alert_dialogs[num]=new Array(title,text);
	}
	 
    function openConfirmationDialog(num, name){
	   var Dialog=document.getElementById('confirmation_dialog');
	   var Background=document.getElementById('confirmation_dialog_background');
	   var x,y=0;
	   
	   document.getElementById('confirmation_dialog_title').innerHTML=confirmation_dialogs[num][0];
	   
	   if(name!=''){
		 document.getElementById('confirmation_dialog_text').innerHTML=confirmation_dialogs[num][1].replace("%s",name);
	   } else {
	     document.getElementById('confirmation_dialog_text').innerHTML=confirmation_dialogs[num][1];
	   }
	   var buttons = document.getElementById('confirmation_dialog_buttons');
       
	   add_event(getChildElementByName(buttons, 'Accept'),"click", function onclick_confirmation_dialog_accept(e){
	   	                                                                closeConfirmationDialog(num);
                                                                        eval(confirmation_dialogs[num][2]);
                                                                     });
	   add_event(getChildElementByName(buttons, 'Cancel'),"click", function onclick_confirmation_dialog_cancel(e){
	   	                                                                closeConfirmationDialog(num);
                                                                        eval(confirmation_dialogs[num][3]);
                                                                     }); 
	   
	  // alert(getChildElementByName(buttons, 'Accept').onclick); 
	   var pos=CenterPosition();
       x=pos[0]-200;
	   y=pos[1]-200;

	   Dialog.style.top = y + "px";
       Dialog.style.left = x + "px";
	   Background.style.visibility= "visible";
       Dialog.style.visibility= "visible";
	   
	 }
	 
	function closeConfirmationDialog(num){
	   var Dialog=document.getElementById('confirmation_dialog');	   
       var Background=document.getElementById('confirmation_dialog_background');
	   
       Dialog.style.visibility= "hidden";
	   Background.style.visibility= "hidden";
	 }
	 
	 function openAlertDialog(num){
	   var Dialog=document.getElementById('alert_dialog');
	   var Background=document.getElementById('alert_dialog_background');
	   var x,y=0;
	   
	   document.getElementById('alert_dialog_title').innerHTML=alert_dialogs[num][0];
	   document.getElementById('alert_dialog_text').innerHTML=alert_dialogs[num][1];

	   var pos=CenterPosition();
       x=pos[0]-200;
	   y=pos[1]-200;

	   Dialog.style.top = y + "px";
       Dialog.style.left = x + "px";
	   Background.style.visibility= "visible";
       Dialog.style.visibility= "visible";
	   
	 }
	 
	function closeAlertDialog(){
	   var Dialog=document.getElementById('alert_dialog');	   
       var Background=document.getElementById('alert_dialog_background');
	   
       Dialog.style.visibility= "hidden";
	   Background.style.visibility= "hidden";
	 }
	 
	 //FUNCIONES COMPLEMENTARIAS
	 
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
   
   function add_event(element, evento, funcion) {
      if (element.attachEvent) {
          element.attachEvent("on"+evento, funcion);
      } else {      
		 element.addEventListener(evento, funcion, false);
      }
     }