// *****************************************
// LOCATION BAR
// *****************************************
function LocationBar(idInputLocation,height){
	this.height = height;
	this.htmlInput = FM_getObjectById(idInputLocation);
	
	this.setLocationString = function(val){
		this.htmlInput.value = val;
	}
	
	this.getLocationString = function(){
		return(this.htmlInput.value);
	}
}