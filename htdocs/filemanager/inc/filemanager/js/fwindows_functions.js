/**
 * FWindows Functions
 * @author Fernando Hernandez - Freelance Soft
 * www.freelancesoft.com.ar
 *
 * DESCRIPCION:
 * Conjunto Basico de funciones para utilizar las FWindows
 *
 */
  
  var fwindow_Set=new FWindow_Set();

  function openWindow(num){
  	fwindow=fwindow_Set.getWindow(num);
	fwindow.openWindow();
	fwindow.toFront();
  }

  function closeWindow(num){
      //fwindow=fwindow_Set.getWindow(num);
	  //fwindow.closeWindow();
	  //fwindow_Set.removeWindow(num);
	  minimizeWindow(num);
   }
   
   function changeWindowsSize(num){
   	 fwindow=fwindow_Set.getWindow(num);
  	 if(fwindow.isMaximized()){
	 	fwindow.restaureWindow(); 
	 } else {
	 	fwindow.maximizeWindow();
	 }
  }
  
  function minimizeWindow(num){
      fwindow=fwindow_Set.getWindow(num);
  	  fwindow.minimizeWindow();
  }
  
  function toFront(num){  	
    fwindow=fwindow_Set.getFirst().toBack();
	
	fwindow=fwindow_Set.next();
	while(fwindow){
		fwindow.toBack();
		fwindow=fwindow_Set.next();
	}
  	(fwindow_Set.getWindow(num)).toFront();	
  }