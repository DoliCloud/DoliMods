/* 
 * MoveWindows Functions
 * @author Fernando Hernandez - Freelance Soft
 * www.freelancesoft.com.ar
 *
 * DESCRIPCION:
 * Funciones que capturan el movimiento del mouse, utilizadas para mover las ventanas y redimensionarlas.
 *
 * Testeadas en Opera 9, Firefox 2 e IE 6, 7
 */
	var moveWindows_isNN = document.layers ? true : false;
    var moveWindows_isIE = document.all ? true : false;
    var moveWindows_mouseX;
	var moveWindows_mouseY;
	var moveWindows_dragok=false;
	var moveWindows_moving=false;
	var moveWindows_Ventana;
    var moveWindows_Action;
	var moveWindows_evt;
	var moveWindows_xClickPos=100;
	var moveWindows_TopLimit=0;
	
	function moveWindows_move(Name){
		
		moveWindows_Ventana=document.getElementById(Name);
		
		if (moveWindows_isNN)
        document.captureEvents(Event.MOUSEMOVE)
        document.onmousemove = moveWindows_handleMouseMove;
		moveWindows_dragok=true;
		moveWindows_Action="MOVE";
	}
	
	function moveWindows_handleMouseMove(evt) {	
			if(!evt) evt = window.event;	  
            moveWindows_mouseX = moveWindows_isIE ? window.event.clientX : evt.clientX;
            moveWindows_mouseY = moveWindows_isIE ? window.event.clientY : evt.clientY; 			
			
			moveWindows_evt=evt;
			
			  if(moveWindows_Action=="MOVE"){
				  moveWindows_moveWindow();
			  } else {
		          moveWindows_resizeWindow();
			  }
			
            return false;
     }
	 
	function moveWindows_moveWindow(){	
		if (moveWindows_dragok){
		  //Seteo posicion del mouse para que no tenga un movimiento brusco en el primer click sobre la ventana	
		  if(!moveWindows_moving){	   
           xLeft=moveWindows_Ventana.style.left.split("p")[0];	 
		   moveWindows_xClickPos=moveWindows_mouseX - xLeft;
		   moveWindows_moving=true;
		  }
		  
		  if(moveWindows_TopLimit<moveWindows_mouseY){
		  	moveWindows_Ventana.style.top = (moveWindows_mouseY - 10)+ "px";
		  }			 
         moveWindows_Ventana.style.left = (moveWindows_mouseX - moveWindows_xClickPos) + "px";
		 moveWindows_Ventana.style.cursor = "move";
		 window.status=moveWindows_mouseX+" "+moveWindows_Ventana.style.left+""+(moveWindows_mouseX - (moveWindows_mouseX - xLeft));
		}
	}
	
	function moveWindows_resizeWindow(){	
	    var xTop, xLeft;
		
		if (moveWindows_dragok){
		 
		 xTop=moveWindows_Ventana.style.top.split("p")[0];
		 xLeft=moveWindows_Ventana.style.left.split("p")[0];
         window.status=xTop+" "+xLeft;
		 
		 if(moveWindows_mouseX > xLeft && (moveWindows_mouseX - xLeft) > 100){
		     moveWindows_Ventana.style.width = (moveWindows_mouseX - xLeft)+ "px";
		 }
		 if(moveWindows_mouseY > xTop && (moveWindows_mouseY - xTop) > 100){			 
             moveWindows_Ventana.style.height = (moveWindows_mouseY - xTop)+ "px";
		 }
		 moveWindows_Ventana.style.cursor = "default";
		}
	}
	
	function moveWindows_leave(Name){	
	    moveWindows_Ventana=document.getElementById(Name);
	    moveWindows_moving=false;
		moveWindows_dragok=false;
		if(moveWindows_isIE){
		   window.event.stopPropagation;	
		} else {
		   moveWindows_evt.stopPropagation;
		}
        	
		moveWindows_Ventana.style.cursor = "default";
	}
	
	function moveWindows_resize(Name){	
	    moveWindows_Ventana=document.getElementById(Name);
		
		if (moveWindows_isNN)
        document.captureEvents(Event.MOUSEMOVE)
        document.onmousemove = moveWindows_handleMouseMove;
		moveWindows_dragok=true;
		moveWindows_Action="RESIZE";
	}

	function moveWindows_setTopLimit(TopLimit){
		moveWindows_TopLimit=TopLimit;
	}
	
	
	