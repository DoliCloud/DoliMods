/**
 * FWindows CLASS
 * @author Fernando Hernandez - Freelance Soft
 * www.freelancesoft.com.ar
 *
 * DESCRIPCION:
 * Clase FWindow_Set.
 * Conjunto de ventanas FWindow.
 *
 */

function FWindow_Set(){
   var FWindows=new Array();
   var Max=20;
   var pos=1;
   
   for(i=1; i<=Max; i++){
	  FWindows[i]=null;
   }
   
   /* Retorna la primera ventana existente en el conjunto. En caso de no existir retorna FALSO
    */
   this.getFirst=function getFirst(){
   	 var find=false;
	 pos = 1;
	 
	 while(pos<=Max && !find){
	 	find=(FWindows[pos]!=null);
		if(!find) {pos++;}
	 }
	 if(find){
	 	return FWindows[pos];
	 } else {
	 	return false;
	 }	 
   }
   
    
   /* Retorna la proxima ventana a la posicion (pos) en el conjunto. En caso de no existir retorna FALSO
    */
   this.next=function next(){
   	 var find=false;
	 pos++;
	 
	 while(pos<=Max && !find){
	 	find=(FWindows[pos]!=null);
		if(!find) {pos++;}
	 }
	 if(find){
	 	return FWindows[pos];
	 } else {
	 	return false;
	 }
   }
   
   /* Retorna la primera posicion libre en el conjunto, si no encuentra una, aumenta el tamaño del conjunto
    * retornando la ultima posicion del mismo.
    */
   this.freePos=function freePos(){
   	  var find=false;
	  var i=1;
	  
   	  while(i<=Max && !find){
	  	if(FWindows[i]==null){
			find=true;			
		} else { i++; }		
	  }
	  if(!find){
	  	Max++;
		FWindows[Max]=null;
	  }
	  return i;   
   }
   
   /* Inserta una ventana en la posicion (i)
    */
   this.addWindow=function addWindow(i,fwindow){
		FWindows[i]=fwindow; 
   }
   
   /* Retorna la ventana de la posicion (i).
    */
   this.getWindow=function getWindow(i){
   	 return FWindows[i];
   }
 
   /* Elimina del conjunto la ventana (i).
    */ 
   this.removeWindow=function removeWindow(i){
   	FWindows[i]=null;
   }
	 
}