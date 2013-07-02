var pageLayout, centerTabsLayout, centerAccordionLayout; 
function showTicketContent(){
	$('#tabs-center').tabs('select', 0);
};
function showInfoProduct(){
	$('#tabs-center').tabs('select', 0);
	$('#id_product_quantity').val(1);
};
function showInfoProductSt(){
	$('#tabs-center').tabs('select', 5);
	$('#id_product_quantity').val(1);
};
function showHistoryContent(){
	$('#tabs-center').tabs('select', 4);
};
function showPlaceContent(){
	$('#tabs-center').tabs('select', 6);
};




$(document).ready(function () {
	//$('#historyTable').tablesorter();
		//$('#quantity_wanted').keyboard();
		
		// OUTER/PAGE LAYOUT
		pageLayout = $("body").layout({ 
			west__size:				.0
		,	south__initClosed:		true
		,	north__initClosed:		true
		,	west__onresize:			function () {  }
		
		}); 

		showTicketContent();
		// LAYOUT TO CONTAIN TABS IN OUTER-CENTER PANE
		centerTabsLayout = $("body > .ui-layout-center").layout({
			closable:				false
		,	resizable:				true
		,	spacing_open:			0
		,	center__onresize:		function(){ 
										if ($("#tabs-center").tabs('option', 'selected') == 0) {
											
											
										}
									}
		});
		// TABS - CENTER
		$("#tabs-center").tabs({
			show: function (evt, ui) { 
				
			} 
		});
		//centerTabsLayout.resizeAll(); // resize layout after creating tabs


		

	
		$('#info_product').hide();
		
		
		
  });
