$( '#myPage' ).live( 'pageinit',function(event){
alert( 'This page was just enhanced by jQuery Mobile!' );
});
var Ticket = jQuery.Class({

	init: function() 
	{
		this.id = 0;
		this.payment_type = 0;
		this.type = 0;
		this.discount_percent = 0;
		this.discount_qty = 0;
		this.lines = new Array();
		this.oldproducts = new Array();
		this.total = 0;
		this.customerpay = 0;
		this.difpayment = 0;
		this.customerId = 0;
		this.employeeId = 0;
		this.idsource = 0;
		this.selectedLine = 0;
		this.id_place = 0;
		this.state = 1; // 0=Draft, 1=To Invoice , 2=Invoiced, 3=No invoiceble
	},
	setButtonState:function(hasTicket)
	{
		if(!hasTicket)
		{
			$('#btnReturnTicket').hide();
			$('#btnSaveTicket').hide();
			$('#btnAddDiscount').hide();
			$('#btnOkTicket').hide();
		}
		else
		{
		
			$('#btnReturnTicket').hide();
			if(_TPV.ticketState==0 && _TPV.ticket.type!=1)
			{
				$('#btnSaveTicket').show();
				$('#btnAddDiscount').show();
			}
			$('#btnOkTicket').show();
		}
		
	},
	checkApplyQuantity:function(idProduct,cant){
		var lineproduct = null;
		if(typeof _TPV.ticket.oldproducts!='undefined' && _TPV.ticket.oldproducts.length>0)
		{
			for(var i=0;i<_TPV.ticket.oldproducts.length;i++)
			{
				if(_TPV.ticket.oldproducts[i]['idProduct']==idProduct){
					lineproduct = _TPV.ticket.oldproducts[i];
					break;
				}
			}
			if(cant>=lineproduct.cant)
				return false;
		}
		return true;
	},
	checkExistReturnProduct:function(idProduct){
		
		if(typeof _TPV.ticket.oldproducts!='undefined' && _TPV.ticket.oldproducts.length>0)
		{
			for(var i=0;i<_TPV.ticket.oldproducts.length;i++)
			{
				if(_TPV.ticket.oldproducts[i]['idProduct']==idProduct){
					return true;
				}
			}
		}
		return false;
	},
	newTicket: function(){
		this.init();
		_TPV.ticketState=0;
		//this.setButtonState(false);
		
		$('#ticketCart li').remove();
		//$('#totalDiscount').html(displayPrice(0));
		//$('#totalTicket').html(displayPrice(0));
		
	},
	newTicketPlace: function(id_place){
		this.newTicket();
		this.id_place = id_place;
		$.mobile.changePage( "#categorypage", { transition: "slideup"} );
		//$.mobile.changePage( "#cart", { transition: "slideup"} );
	},

	setLine : function(idProduct, line){
		this.lines.push(line);
		if(this.lines.length==1)
			this.setButtonState(true);
	},
	getLine : function(idProduct){
		for (var i in this.lines)
		{
			if(this.lines[i]['idProduct']==idProduct)
				return this.lines[i];
		}
		return null;
	},
	getTotal : function(){
		
		return this.total.toFixed(2);;
	},
	calculeDiscountTotal: function()
	{
		
		discount = 0;
		if(this.discount_percent!=null && this.discount_percent!=0)
			discount = this.total*(this.discount_percent/100)
			this.total = this.total*(1-(this.discount_percent/100));
		if(this.discount_qty!=null && this.discount_qty!=0){
			discount = this.discount_qty;
			this.total = this.total-this.discount_qty;
		}
		var pricediscount = new Number(discount);
		pricediscount = pricediscount.toFixed(2);
		$('#totalDiscount').html(pricediscount);
		var total = new Number(this.total);
		total = total.toFixed(2);
		$('#totalTicket').html(displayPrice(total));
	},
	calculeTotal : function(){
		var sum = 0;
		for (var i in this.lines)
		{
			sum = parseFloat(sum) + parseFloat(this.lines[i].total);
		}
		this.total = sum.toFixed(2);
		this.calculeDiscountTotal();
		
	},
	addProductLine: function()
	{
		if(!this.getLine(_TPV.activeIdProduct))
		{
			this.addLine(_TPV.activeIdProduct, true);
		}
		var cant = parseInt($('#id_product_quantity').val());
		if(cant>1)
		{
			cant = cant-1;
			this.getLine(_TPV.activeIdProduct).cant = this.getLine(_TPV.activeIdProduct).cant + cant;
			this.getLine(_TPV.activeIdProduct).setQuantity(this.getLine(_TPV.activeIdProduct).cant);
			this.getLine(_TPV.activeIdProduct).showTotal();	
		}
		showTicketContent();
	},
	addManualProduct: function(line)
	{
		var id = line['idProduct'];
		var disc = line['discount'];
		var qty = line['cant'];
		
		if(typeof id!= 'undefined' && id!=0)
		{
			_TPV.activeIdProduct = id;
			_TPV.ticket.addLine(id);
			if(typeof qty!= 'undefined' && qty!=1)
			{
				
				qty = qty-1;
				this.getLine(_TPV.activeIdProduct).cant = this.getLine(_TPV.activeIdProduct).cant + qty;
				this.getLine(_TPV.activeIdProduct).setQuantity(this.getLine(_TPV.activeIdProduct).cant);
				
			}
			if(typeof disc!= 'undefined' && disc!=0)
				this.getLine(_TPV.activeIdProduct).setDiscount(disc);
			this.getLine(_TPV.activeIdProduct).showTotal();
		}
		this.getLine(_TPV.activeIdProduct).note = line['note'];
	},
	
	addLine: function(idProduct, add)
	{
		if(_TPV.ticketState==1)
			return;
		if(_TPV.infoProduct==0 || typeof add!='undefined')
		{
		
			if(this.getLine(idProduct)!=undefined)
			{
				this.getLine(idProduct).cant = this.getLine(idProduct).cant + 1;
				this.getLine(idProduct).setQuantity(this.getLine(idProduct).cant);
				this.getLine(idProduct).showTotal();		
			}
			else
			{
				var line = new TicketLine();
				line.setLineByIdProducts(idProduct);
				this.total = this.total + line.price_ttc;
				//$('#totalTicket').html(displayPrice(this.total));
				this.setLine(idProduct,line);
				//$(line.getHtml()).insertAfter('#ticketCart');
				$('#ticketCart').append(line.getHtml());
				 //$("#ticketCart:jqmData(role='listview')").listview("refresh");
			
			}
		} 
		else
		{
			
			showInfoProduct();
		}
		_TPV.ticket.calculeTotal();
		_TPV.addInfoProduct(idProduct);
		
	},
	editTicketLine : function(idProduct)
	{
		$('#saveTicketLine').unbind().click(function(){
			if(_TPV.ticket.checkApplyQuantity($('#idProduct').val(),$('#line_quantity').val()))
			{
				var line = _TPV.ticket.getLine(idProduct);
				line.setQuantity($('#line_quantity').val());
				line.note = $('#line_note').val();
				line.showTotal();
				$.mobile.changePage( "#cart", { transition: "slideup"} );
			}
		});
		$('#deleteLine').unbind().click(function(){
			_TPV.ticket.deleteLine($('#idProduct').val())
		});
		$('#idProduct').val(idProduct);
		var line = _TPV.ticket.getLine(idProduct);
		$('#productLabel').html(line.label);
		$('#line_quantity').val(line.cant);
		$('#our_price_display').val(line.price_ttc);
		$('#line_note').val(line.note);
		
		
		$.mobile.changePage( "#productEdit", { transition: "slideup"} );	
	},
	addTicketLine : function(idProduct)
	{
		if(this.id_place==0)
             	{
                    $.mobile.changePage( "#services", { transition: "slideup"} );
                    return;
            	 }
		$('#saveTicketLine').unbind().click(function(){
				
				var line = _TPV.ticket.getLine(idProduct);
				if(line!=null)
				{
					line.setQuantity($('#line_quantity').val());
					line.note = $('#line_note').val();
					line.showTotal();
				}
				else
				{
					_TPV.ticket.addLine(idProduct);
					var line = _TPV.ticket.getLine(idProduct);
					line.setQuantity($('#line_quantity').val());
					line.note = $('#line_note').val();
					line.showTotal();
				}
				 
				$.mobile.changePage( "#cart", { transition: "slideup"} );
				$("#ticketCart:jqmData(role='listview')").listview("refresh");
			
		});
		 
		$('#deleteLine').unbind().click(function(){
			_TPV.ticket.deleteLine($('#idProduct').val())
		});
		
		
		
		$('#idProduct').val(idProduct);
		$.mobile.changePage( "#productEdit", { transition: "slideup"} );	
		var line = _TPV.ticket.getLine(idProduct);
		if(line!=null)
		{
			$('#productLabel').html(line.label);
			$('#line_quantity').val(line.cant);
			$('#our_price_display').html(displayPrice(line.price_ttc));
			$('#line_note').val(line.note);
			$('#bigpic').attr("src",line.image);
		}
		else
		{
			
			var info = new Object();
			info['product']=idProduct;
			if(_TPV.ticket.customerId != 0){
				info['customer'] = _TPV.ticket.customerId;
			}
			else
				{
				info['customer'] = _TPV.customerId;
				}
			var result = ajaxDataSend('getProduct',info);
			if(result.length>0)
				_TPV.products[idProduct]= result[0];
			
			var product = _TPV.products[idProduct];
		
			$('#productLabel').html(product.label);
			$('#line_quantity').val(1);
			$('#our_price_display').html(displayPrice(product.price_ttc));
			$('#line_note').val(product.note);
			$('#short_description_content').html(product.description);
			$('#bigpic').attr("src",product.image);
			
		}	
	},
	deleteLine: function(idProduct)
	{
		var success = ajaxSend('deleteLine');
		$('#ticketLine'+ idProduct).remove();
		this.total = this.total - this.getLine(idProduct).total;
		$('#totalTicket').html(displayPrice(this.total));
		this.lines = removeKey(this.lines,idProduct);
		$("#ticketCart:jqmData(role='listview')").listview("refresh");
		
	},
	cancelTicket: function()
	{
		var success = ajaxSend('cancelTicket');
		$('#ticketCart li').remove();
	},
	saveTicket: function()
	{
		// Set State to draft
		_TPV.ticket.state=0;
		_TPV.ticket.employeeId=_TPV.employeeId;
		var result = ajaxDataSend('saveTicket',_TPV.ticket);
		$('#ticketCart li').remove();
		_TPV.getPlaces();
		$.mobile.changePage( "#services", { transition: "slideup"} );

	},
	editInfoTicket: function()
	{
		// Set State to draft
		$('#ticketnotes').val(_TPV.ticket.note);
		
		//$.mobile.changePage( "#cart", { transition: "slideup"} );

	},
	saveInfoTicket: function()
	{
		// Set State to draft
		_TPV.ticket.note=$('#ticketnotes').val();
		
		$.mobile.changePage( "#cart", { transition: "slideup"} );

	},
	
	
	okTicket: function()
	{	
		$('#id_btn_add_ticket').hide();
		_TPV.ticket.employeeId=_TPV.employeeId;
		//_TPV.ticket.customerId=_TPV.customerId;
		if(_TPV.ticket.type==1)
		{
			
			var sendTicket = _TPV.ticket;
			var result = ajaxDataSend('saveTicket',sendTicket);
			if(!result)
				return;
		   _TPV.printing('ticket',result);
			_TPV.ticket.newTicket();
			return;
		}
		$('#pay_client_id').val('');
		$('.payment_options .payment_return').html('');
		
		$('.payment_options .payment_total').html(this.total);
		$('#payment_options').hide();
		$('#payType').dialog({width:400});
		$('#id_btn_add_ticket').unbind('click');
		
		$('#id_btn_add_ticket').click(function(){
			_TPV.ticket.state=1;
		//	_TPV.ticket.employeeId=_TPV.employeeId;
			var sendTicket = _TPV.ticket;
			var result = ajaxDataSend('saveTicket',sendTicket);
			
			$('#payType').dialog( "close" );
			if(!result)
				return;
			//if(result)
		   _TPV.printing('ticket',result);

			_TPV.ticket.newTicket();
		});			
	},
	showAddCustomer: function(customer)
	{
		$('#idClient').dialog({width:440});
		$('#id_btn_add_customer').unbind('click');
		$('#id_btn_add_customer').click(function(){
			var customer = new Customer();
			customer.nom = $('#id_customer_name').val();
			customer.prenom = $('#id_customer_lastname').val();
			customer.address = $('#id_customer_address').val();
			customer.idprof1 = $('#id_customer_cif').val();
			customer.tel = $('#id_customer_phone').val();
			customer.email = $('#id_customer_email').val();
			var result = ajaxDataSend('addCustomer',customer);
			$('#idClient').dialog('close');
			//if(result.length>0)
				//_TPV.ticket.id= result[0];
		});
		
	},
	addTicketCustomer: function(idcustomer,name)
	{
		_TPV.ticket.customerId = idcustomer;
		$('#infoCustomer').html(name);
		showTicketContent();
	},
	showAddProduct: function(customer)
	{
		var product = new Product();
		$('#id_product_name').val('');
		$('#id_product_ref').val('');
		$('#id_product_price').val('');
		$('#idPanelProduct').dialog({height:300,width:440});
		$('.tax_types').removeClass('btnon');
		$('.tax_types').unbind('click');
		$('.tax_types').click(function(){
			$('.tax_types').removeClass('btnon');
			$(this).addClass('btnon');
			product.tax = $(this).find('a:first').attr('id').substring(14);
		})
		$('#id_btn_add_product').unbind('click');
		$('#id_btn_add_product').click(function(){
			
			product.label = $('#id_product_name').val();
			product.ref = $('#id_product_ref').val();
			product.price_ttc = $('#id_product_price').val();
			var result = ajaxDataSend('addNewProduct',product);
			$('#idPanelProduct').dialog('close');
			if(result)
				_TPV.getDataCategories(0);
			
		});
		
	},
	addDiscount: function()
	{
		$('#ticket_discount_perc').val('');
		$('#ticket_discount_qty').val('');
		$('#idDiscount').dialog({width:450});
		$('#id_btn_add_discount').unbind('click');
		$('#id_btn_add_discount').click(function(){
			_TPV.ticket.discount_percent = $('#ticket_discount_perc').val();
			_TPV.ticket.discount_qty = $('#ticket_discount_qty').val();
			_TPV.ticket.calculeTotal();
			$('#idDiscount').dialog( "close" );
		});			
	},
	setPaymentType : function(idType){
		this.payment_type=idType;
		$('#payment_options').show();
		$('#payment_options').find('.payment_options').hide();
		$('#payment_'+idType).show();
		//Initialize Values
		$('.payment_total').html(displayPrice(_TPV.ticket.total));
	},
	showZoomProducts:function()
	{
		$('#idProducts').append($('#products').html());
		$('#idProducts').dialog({width:640});
	},
	showManualProducts:function()
	{
		$('#idManualProducts').dialog({width:640});
	}
});

// CLASS TICKET LINE **********************************************************************
var TicketLine = jQuery.Class({
	init: function()
	{
		this.id = 0;
		this.idProduct = 0;
		this.ref = 0;
		this.label = '';
		this.description = '';
		this.discount = 0;
		this.cant = 1;
		this.price = 0;
		this.price_ttc=0;
		this.total = 0;
		this.idTicket = 0;
		this.localtax1_tx = 0;
		this.localtax2_tx = 0;
		this.tva_tx = 0;
		this.note = '';
	},
	getHtml:function(){
		return '<li data-icon="delete" data-theme="c"  id="ticketLine'+this.idProduct+'">'
		+'<a onclick="_TPV.ticket.editTicketLine('+this.idProduct+');" data-prefetch="" data-transition="none"  class="ticketLine"><p><strong>'+this.label+'</strong></p><span class="ui-li-count cant">'+this.cant+'</span></a>'
		+'<a class="deleteLine" onclick="_TPV.ticket.deleteLine('+this.idProduct+');"  data-transition="none" data-icon="delete" >Eliminar</a></li>';
							
		//return '<tr id="ticketLine'+this.idProduct+'"><td class="idCol">'+this.idProduct+'</td><td class="description">'+this.label+'</td><td class="discount">'+this.discount+'%</td><td class="price">'+displayPrice(this.price_ttc)+'</td><td class="cant">'+this.cant+'</td><td class="total">'+displayPrice(this.price_ttc*this.cant)+'</td><td class="colActions"><a class="action edit" onclick="_TPV.ticket.editTicketLine('+this.idProduct+');"><a/><a class="action delete" onclick="_TPV.ticket.deleteLine('+this.idProduct+');"><a/><a class="action info" onclick="_TPV.addInfoProduct('+this.idProduct+');showInfoProduct();"><a/></td></tr>';
	},
	setLineByIdProducts:function(idProduct){
		
		var info = new Object();
		info['product']=idProduct;
		if(_TPV.ticket.customerId != 0){
			info['customer'] = _TPV.ticket.customerId;
		}
		else
			{
			info['customer'] = _TPV.customerId;
			}
		var result = ajaxDataSend('getProduct',info);
		if(result.length>0)
			_TPV.products[idProduct]= result[0];
				
		var product = _TPV.products[idProduct];
		this.idProduct = idProduct;
		this.ref = 0;
		this.label = product.label;
		this.description = product.description;
		this.price_ttc = Math.round(product.price_ttc*100)/100;
		this.price = Math.round(product.price*100)/100;
		this.localtax1_tx = product.localtax1_tx;
		this.localtax2_tx = product.localtax2_tx;
		this.tva_tx = product.tva_tx;
		this.total = this.price_ttc*this.cant;
		this.idTicket = _TPV.ticket.id;
	},
	setLineByIdLine:function(idProduct){
		if(typeof _TPV.ticket.oldproducts=='undefined'){
			return;	
		}
		var lines = _TPV.ticket.oldproducts;
		var line = null;
		for(var i=0;i<lines.length;i++)
		{
			if(lines[i]['idProduct']==idProduct){
				line = lines[i];
				break;
			}
		}
		if(!line)
			return 1;
		this.idProduct = idProduct;
		this.ref = 0;
		this.label = line.label;
		this.discount = line.discount;
		this.description = line.description;
		this.price_ttc = line.price_ttx;
		this.price = line.price;
		this.localtax1_tx = line.localtax1_tx;
		this.localtax2_tx = line.localtax2_tx;
		this.tva_tx = line.tva_tx;
		this.total = this.price_ttc*this.cant;
		return 0;
	},
	setQuantity : function(cant){
		number = parseFloat(cant);
		// Add Quantity
		this.cant = number;
		$('#ticketLine'+this.idProduct).find('.cant').html(number);	
	},
	setDiscount : function(discount){
		quantitydiscount = parseFloat(discount); 
		// Add Discount
		this.discount = quantitydiscount;
		$('#ticketLine'+this.idProduct).find('.discount').html(quantitydiscount+'%');
	},
	setTotal : function(total){
		// Add Total
		this.total = total;
		$('#ticketLine'+this.idProduct).find('.total').html(displayPrice(total));
	},
	showTotal : function(){
		this.total = this.cant * this.price_ttc;
		this.total = this.total - this.total*(this.discount/100);
		this.total = this.total.toFixed(2);
		//$('#ticketLine'+this.idProduct).find('.total').html(displayPrice(this.total));
		_TPV.ticket.calculeTotal();
	}
});


// CLASS CUSTOMER *******************************************************************
var Customer = jQuery.Class({
	init: function()
	{
		this.id = 0;
		this.nom = '';
		this.prenom = '';
		this.idprof1 = '';
		this.address = '';
		this.cp = '';
		this.ville = '';	
		this.tel = '';
		this.email = '';
	}
	
});

// CLASS PRODUCT ********************************************************
var Product = jQuery.Class({
	init: function()
	{
		this.id = 0;
		this.label = '';
		this.price_ttc = 0;
		this.ref = '';
		this.tax = 0;
		
	}
	
});
//CLASS PRODUCT ********************************************************
var Cash = jQuery.Class({
	init: function()
	{
		this.moneyincash = 0;
		this.type = 1;
		this.printer = 1;
		this.employeeId = 0;

	}
	
});


// CLASS TPV  ***********************************************************
var TPV = jQuery.Class({
	
	init: function()
	{
		this.categories = new Array();
		this.products = new Array();
		this.ticket = new Ticket();
		this.activeIdProduct = 0;
		this.employeeId = 0;
		this.barcode = 0;
		this.infoProduct = 0;
		this.defaultConfig = new Array();
		this.ticketState = 0; // 0 => Normal, 1 => Blocked to add products, 2 => Return products  
		this.cash = new Cash();
	},
	
	setButtonEvents:function()
	{
		$('#btnNewTicket').click(function() {
			_TPV.ticket.newTicket();
		});
		
		$('#btnOkTicket').click(function() {
			_TPV.ticket.okTicket();
		});
		$('#btnHistory').click(function() {
			_TPV.getHistory();
		});
		$('#btnSaveTicket').click(function() {
			_TPV.ticket.saveTicket();
		});
		$('#btnSaveInfoTicket').click(function() {
			_TPV.ticket.saveInfoTicket();
		});
		$('#btnCancelTicket').click(function() {
			_TPV.ticket.cancelTicket();
		});
		$('#btnReturnTicket').click(function() {
			_TPV.ticketState=2;
			_TPV.ticket.setButtonState(false);
			var id = _TPV.ticket.idsource;
			var discount_percent = _TPV.ticket.discount_percent;
			var discount_qty = _TPV.ticket.discount_qty;
			var lines = _TPV.ticket.oldproducts;
			_TPV.ticket.newTicket();
			_TPV.ticket.idsource = id;
			_TPV.ticket.oldproducts = lines;
			_TPV.ticket.discount_percent = discount_percent;
			_TPV.ticket.discount_qty = discount_qty;
			_TPV.ticket.type=1;
		});
		$('#btnViewTicket').click(function() {
			_TPV.ticket.viewTicket();
		});
		
		$('#btnAddCustomer').click(function() {
			_TPV.ticket.showAddCustomer();
		});
		$('#btnAddDiscount').click(function() {
			_TPV.ticket.addDiscount();
		});
		$('#btnAddProduct').click(function() {
			_TPV.ticket.showAddProduct();
		});
		$('#btnShowManualProducts').click(function() {
			_TPV.ticket.showManualProducts();
		});
		$('#btnLogout').click(function() {
			window.location.href = "./disconect.php";
		});
		$('#btnZoomCategories').click(function() {
			_TPV.ticket.showZoomProducts();
		});	
		$('#btnAddProductCart').click(function() {
			_TPV.ticket.addProductLine();
		});
		// Add manual referece
		/*$('#btnAddRefManual').click(function() {
			$('#refmanual').val(1);
			_TPV.ticket.addManualProduct($('#refmanual').val(),$('#qtymanual').val());
		});*/
		// Filter Product Search Events
		$('#id_product_search').live("keypress", function(e) {
	        if (e.keyCode == 13 || e.which == 13) {
	        	var result = ajaxDataSend('searchProducts',$(this).val());
	        	$("#id_selectProduct option").remove();
	        	 $('#id_selectProduct').append(
		        	        $('<option></option>').val(0).html('Productos '+ result.length)
		         );
	        	$.each(result, function(id, item) {
	        	    $('#id_selectProduct').append(
	        	        $('<option></option>').val(item['id']).html(item['label'])
	        	    );
	        	});
	        	if(_TPV.barcode==1)
	        	{
	        		$('#id_product_search').val('');
	        		
	        	}
	        	if(_TPV.barcode==1 && result.length==1)
	        	{
	        		
	        		_TPV.ticket.addLine(result[0]['id']);	
	        		$('#id_product_search').focus();
	        	}    		
	        }
	    });
		// Filter Sotck products Search Events
		$('#id_stock_search').live("keypress", function(e) {
	        if (e.keyCode == 13 || e.which == 13) {
	        	var result = ajaxDataSend('searchStocks',$('#id_stock_search').val());
	        	$("#storeTable tr.data").remove();
	        	$.each(result, function(id, item) {
	        	    $('#storeTable').append('<tr class="data"><td>'+item['id']+'</td><td>'+item['ref']+'</td><td>'+item['label']+'</td><td>'+item['stock']+'</td><td>'+item['warehouse']+'</td><td><a class="accion addline" onclick="_TPV.ticket.addLine('+item['id']+');"><a/></td></tr>');
	        	});
	        }
	    });
		// Filter Cusotmer Search
		$('#id_customer_search').live("keypress", function(e) {
	        if (e.keyCode == 13 || e.which == 13) {
	        	var result = ajaxDataSend('searchCustomer',$('#id_customer_search').val());
	        	$("#customerTable tr.data").remove();

	        	$.each(result, function(id, item) {
	        	    $('#customerTable').append('<tr class="data"><td class="itemId">'+item['id']+'</td><td class="itemDni">'+item['profid1']+'</td><td class="itemName">'+item['nom']+'</td><td class="itemAddress">'+item['address']+'</td><td class="itemPhone">'+item['tel']+'</td><td class="action add"><a class="action addcustomer" onclick="_TPV.ticket.addTicketCustomer('+item['id']+',\''+item['nom']+'\');"><a/></td></tr>');
	        	});
	        }
	    });
		
		$('#tabHistory').click(function(){
			_TPV.searchByRef();	
		});
		// Filter Reference Search 
		$('#id_ref_search').live("keypress", function(e) {
	        if (e.keyCode == 13 || e.which == 13) {
	        	_TPV.searchByRef();
	        	/*var result = ajaxDataSend('getHistory',$('#id_ref_search').val());
	        	$("#historyTable tr.data").remove();
	        	
	        	
	        	$.each(result, function(id, item) {
	        		var edit = false;
	        		if(item['statut']==0)
		        		edit = true;
	        		var date = '-';
	        		if(item['date_close'].length>0 && item['date_close']!='')
	        			date = item['date_close'];
	        		else if(item['date_creation'].length>0 && item['date_creation']!='')
	        			date = item['date_creation'];
	        	    $('#historyTable').append('<tr class="data state'+item['statut']+'"><td>'+item['ticketnumber']+'</td><td>'+date+'</td><td>'+item['terminal']+'</td><td>'+item['seller']+'</td><td>'+item['client']+'</td><td>'+displayPrice(item['amount'])+'</td><td class="colActions"><a class="action edit" onclick="_TPV.getTicket('+item['id']+','+edit+');"><img src="img/edit.png" width="32" height="32" /><a/></tr>');
	        	});*/
	        }
	    });
		$('#id_selectProduct').change(function() {
			if($(this).val()!=0)
				_TPV.ticket.addLine($(this).val());		
		});
		$('.payment_types').each(function(){
				$(this).click(function() {
					$('#id_btn_add_ticket').show();
					$('.payment_types').removeClass('btnon');
					$(this).addClass('btnon');
					_TPV.ticket.setPaymentType($(this).find('a:first').attr('id').substring(7));
				});
		});
		$('#pay_client_id').blur(function(){
			_TPV.ticket.customerpay = $('#pay_client_id').val();
			_TPV.ticket.difpayment = _TPV.ticket.total-_TPV.ticket.customerpay;
			$('.payment_return').html(displayPrice(_TPV.ticket.difpayment));
		});
		$('#id_btn_tpvtactil').click(function(){
			if($(this).hasClass('on')){
				$(this).removeClass('on');
				$(this).addClass('off');
				_TPV.tpvTactil(false);
			}
			else{
				$(this).addClass('on');
				$(this).removeClass('off');
				_TPV.tpvTactil(true);
			}
		});
		$('#id_btn_barcode').click(function(){
			if($(this).hasClass('on')){
				$(this).removeClass('on');
				$(this).addClass('off');
				_TPV.barcode=0;
			}
			else if($(this).hasClass('off')){
				$(this).addClass('on');
				$(this).removeClass('off');
				_TPV.barcode=1;
			}
		});
		
		
		$('#id_btn_infoproduct').click(function(){
			if($(this).hasClass('on')){
				$(this).removeClass('on');
				$(this).addClass('off');
				_TPV.showInfoProduct(false);
			}
			else{
				$(this).addClass('on');
				$(this).removeClass('off');
				_TPV.showInfoProduct(true);
			}
		});
		$('#id_btn_closecash').click(function(){
			var money = ajaxDataSend('getMoneyCash',null);
			$('#id_terminal_cash').val(displayPrice(money));
			$('#id_money_cash').val('');
			$('#idCloseCash').dialog({width:440});
			$('#id_btn_close_cash').unbind('click');
			
			$('#id_btn_close_cash').click(function(){
				
				if($('#id_money_cash').val())
					_TPV.cash.moneyincash = $('#id_money_cash').val();
				_TPV.cash.employeeId = _TPV.employeeId;
				var result = ajaxDataSend('closeCash',_TPV.cash);
				$('#idCloseCash').dialog('close');
				if(!result)
					return;
				if(result && _TPV.cash.printer==1)
					_TPV.printing('closecash',result);
				if(_TPV.cash.type==1)
					$('#btnLogout').click();
			
			});
		});
		$('.close_types').click(function(){
			$('.close_types').removeClass('btnon');
			$(this).addClass('btnon');
			_TPV.cash.type = $(this).find('a:first').attr('id').substring(9);
		});
		$('.print_close_types').click(function(){
			$('.print_close_types').removeClass('btnon');
			$(this).addClass('btnon');
			_TPV.cash.printer = $(this).find('a:first').attr('id').substring(14);
		});
		$('.type_discount').click(function(){
			$('.type_discount').removeClass('btnon');
			$(this).addClass('btnon');
			if($(this).find('a:first').attr('id')=='btnTypeDiscount0')
			{
				$('#typeDiscount0').show();
				$('#typeDiscount1').hide();
				$('#typeDiscount1').val(0);
			}
			else
			{
				$('#typeDiscount1').show();
				$('#typeDiscount0').hide();
				$('#typeDiscount0').val(0);
			}
		});
		
		$('#id_btn_employee').click(function(){
			
			
			$('#idEmployee').dialog({width:400});
			$('#idEmployee a').unbind('click');
			
			$('#idEmployee a').click(function(){
				
				_TPV.employeeId = $(this).attr('id').substring(12);
				$('#id_user_name').html($(this).html());
				$('#idEmployee').dialog('close');
			
			});
		});
		
		
		
	},
	getTicket: function(idTicket,edit)
	{	
		if(typeof idTicket!='undefined')
		{
			var result = ajaxDataSend('getTicket',idTicket);
			$.each(result, function(id, item) {
        	    _TPV.ticket.init();
        	    _TPV.ticket.id = item['id'];
        	    _TPV.ticket.payment_type = item['payment_type'];
        	    _TPV.ticket.type = item['type'];
        	    if(typeof item['discount_percent']!='undefined')
        	    	_TPV.ticket.discount_percent = item['discount_percent'];
        	    else
        	    	_TPV.ticket.discount_percent = 0;
        	    if(typeof item['discount_qty']!='undefined')
               	    _TPV.ticket.discount_qty =item['discount_qty'];
        	    else
        	    	_TPV.ticket.discount_qty =0;
        	    _TPV.ticket.customerpay = item['customerpay'];
        	    _TPV.ticket.difpayment = item['difpayment'];
        	    _TPV.ticket.customerId = item['customerId'];
        	    _TPV.ticket.state = item['state'];
        	    _TPV.ticket.id_place = item['id_place'];
        	    _TPV.ticket.note = item['note'];
        	  //  _TPV.ticket.idsource = idTicket;
        	   // _TPV.ticket.oldproducts = item['lines'];
        	    
        	    $('#ticketCart li').remove();
        	    var total = 0;
        	    $.each(item['lines'], function(idline, line) 
        	    {
        	    	_TPV.ticket.addManualProduct(line);
        	    });
        	        	  
        	    $.mobile.changePage( "#cart", { transition: "slideup"} );
        	    $("#ticketCart:jqmData(role='listview')").listview("refresh");
        	   
        	});
		}	
	},
	
	getDataCategories: function(category)
	{

		this.getCategories(category);
	},
	getCategories: function(category)
	{	
		var categories = this.categories;
		var list = '';
		$('#categories').html('');
		
		if(typeof category!='undefined' && category!=0)
		{
			if(typeof categories[category]['parent']!='undefined')
			{
				$("#categories:jqmData(role='listview')").append('<li data-theme="e"><a onclick="_TPV.getCategories('+categories[category]['parent']+')" href="#"><==</a></li>');
			}
		}
			
		$.getJSON('./ajax_pos.php?action=getCategories&parentcategory='+ category, function(data)
		{
													
			$.each( data, function( i, item ) {
				if(categories[item.id]==undefined){
					categories[item.id]= item;
					categories[item.id]['parent'] = category;
				}
				list += '<li data-theme="e"><a onclick="_TPV.getCategories('+item.id+')" href="#">';
				list += item.label;
				list += '</a></li>';
			});
			$("#categories:jqmData(role='listview')").append(list).listview("refresh");
			
		});
	//	$("#categories:jqmData(role='listview')");
			
		this.getProducts(category);
		$("#categories:jqmData(role='listview')").listview("refresh"); 
		$.mobile.changePage( "#categorypage", { transition: "slideup"} );
		
	},
	getProducts: function(category)
	{
		var products = this.products;
		var categories = this.categories;
		if(typeof category!='undefined')
		{
			var addProducts = false;
			if(categories[category]!=undefined)
			{
				if(typeof categories[category]['products']!='undefined' && categories[category]['products'].length>0)
				{
					categoryProducts = this.categories[category]['products'];
					
					//$("#categories:jqmData(role='listview')").html('');
					$.each(categoryProducts, function(key, val) {
						product = products[val];
						$("#categories:jqmData(role='listview')").append('<li><a onclick="_TPV.ticket.addTicketLine('+product.id+')" href="#">'+product.label+'</a></li>');
						
					});
					$("#categories:jqmData(role='listview')").listview("refresh"); 
					//$.mobile.changePage( "#productpage", { transition: "slideup"} );
					return true;
				} 
				else 
				{
					addProducts = true;
					categories[category]['products'] = new Array();
				}
			}
			/*if(category!=0){
				$('#returnCategory').click(function()
						{
					_TPV.getCategories(categories[category]['parent']);
						});
			}*/
			$.getJSON('./ajax_pos.php?action=getMoreProducts&category='+ category+'&pag=-1', function(data) {
											
												var list = '';
												$.each( data, function( i, item ) {
													if(products[item.id]==undefined)
														products[item.id]= item;
													if(addProducts){
														var arrayItem = categories[category]['products'].length;
														categories[category]['products'][arrayItem] = item.id;
													}
												    list += '<li><a onclick="_TPV.ticket.addTicketLine('+item.id+')" href="#">';
												    list += item.label;
												    list += '</a></li>';
												});
												$("#categories:jqmData(role='listview')").append(list).listview("refresh"); 
											
											});
											
		} 
		else
		{
			//$('#categories').empty();	
		}
		$('#returnCategory').unbind().click(function(){
			_TPV.getCategories(category);
		});
		//$.mobile.changePage("#productpage", { transition: "slideup"} );
	},
	getPlaces: function()
	{
		$("#listplaces").html('');
		$.getJSON('./ajax_pos.php?action=getPlaces', function(data) {
											
												var list = '';
												$.each( data, function( i, item ) 
												{
													var placename = "$('#placeName').html('"+item.name+"');";
													if(item.fk_ticket>0)
													{
														list += '<li data-icon="false" data-theme="e"><a onclick="_TPV.getTicket('+item.fk_ticket+',true);'+placename+'" href="#">';
													}
													else
													{
														list += '<li data-icon="false"><a onclick="_TPV.ticket.newTicketPlace('+item.id+');'+placename+'")" href="#">';
													}
												    //list += '<li><a onclick="_TPV.ticket.getTicket('+item.rowid+')" href="#">';
												    list += item.name;
												    list += '</a></li>';
												});
												$("#listplaces:jqmData(role='listview')").append(list).listview("refresh"); 
											
											});
											
		//$.mobile.changePage( "#productpage", { transition: "slideup"} );
	},
	
    
	
	showInfo: function(error)
	{
		$('#errorText').html(error);
		$('#idPanelError').dialog({width:500,height:200});
		setTimeout(function(){$('#idPanelError').dialog("close")},3000);
	},
	addInfoProduct: function(idProduct)
	{
		$('#info_product').show();
		this.activeIdProduct = idProduct;
		product = this.products[idProduct];
		$('#info_product').find('h1').html(product.label);
		$('#info_product').find('#short_description_content').html(product.description);
		var price = new Number(product.price_ttc);
		price = price.toFixed(2);
		$('#info_product').find('#our_price_display').html(price);	
		$('#info_product').find('#bigpic').attr({src:product.image});
		$('#info_product').find('#hiddenIdProduct').val(idProduct);
	},
	loadConfig: function(){
		
		var result = ajaxDataSend('getConfig',null);
		if(result)
		{
			this.defaultConfig = result;
			$('#id_user_name').html(result['user']['name']);
			$('#id_user_terminal').html(result['terminal']['name']);
			$('#infoCustomer').html(result['customer']['name']);
			_TPV.customerId = result['customer']['id'];
			_TPV.employeeId = result['user']['id'];
			
		}
		return;
	},
	showInfoProduct: function(on){
		
		if(!on){
			_TPV.infoProduct = 0;
			
		} else {
			_TPV.infoProduct = 1;
		}
		return;
	},
	tpvTactil: function(on){
		
		if(!on){
			//$('.quertyKeyboard').keyboard('option', 'openOn', '');
			$('[type=text]').each(function(){
				$(this).getkeyboard().destroy();
			});
			return;
		}
		else
		{
			$('[type=text]:not(.numKeyboard)').keyboard({
				layout:'qwerty',
				usePreview:false , 
				autoAccept : true,
				accepted : function(e, keyboard, el){
				
			  }	
			});
			$('.numKeyboard').keyboard({ 
				//layout:'num',
				layout: 'custom',
				usePreview:false,
				customLayout: {
					'default' : [
						
						'7 8 9',
						'4 5 6',
						'1 2 3',
						'0 . {sign}',
						'{bksp} {a} {c}'
					]
				},
				accepted : function(e, el){ 
					
				}
			});
			return;
		}
		
	}
});

$(function(){
	
			_TPV.setButtonEvents();
			
			//_TPV.tpvTactil(true);
			
		});
$(document).ready(function() {
	_TPV.loadConfig();
	_TPV.getPlaces();
	//_TPV.ticket.setButtonState(false);
	});
var _TPV = new TPV();
_TPV.getDataCategories(0);
_TPV.getPlaces();

function removeKey(arrayName,key)
{
 var x;
 var tmpArray = new Array();
 for (var i=0;i<arrayName.length;i++)
 {
	 if(arrayName[i]['idProduct']!=key) { tmpArray.push(arrayName[i]); }
 }
 return tmpArray;
}

function ajaxSend(action)
{
	var result;
	$.ajax({
			type: "POST",
			url: './ajax_pos.php',
			data: 'action='+ action,
			async : false,
			success: function(msg)
			{
				result = msg;
			}
		});
	return result;
}
function displayPrice(pr)
{
	//return (Math.round(pr*100/5)*5/100).toFixed(2);
	var precision = 2;
	if(typeof _TPV.defaultConfig['decrange']['tot']!='undefined')
			precision = _TPV.defaultConfig['decrange']['tot'];
	return parseFloat(pr).toFixed(precision);
	
}

function ajaxDataSend(action,data)
{
	var result;

	var DTO = {'data': data};

	var data = JSON.stringify(DTO);
	$.ajax({
		type: "POST",
	  traditional: true,
	  cache:false,
		url: './ajax_pos.php?action='+action,
		contentType: "application/json;charset=utf-8",
		dataType: "json", 
		async: false,
		processData:false,
		data: data,
		success: function(msg)
		{
			result = msg;
		}
	});
	
	if(result!=null && typeof result!='undefined'){
		if(typeof result['error']!='undefined' && typeof result['error']['desc']!='undefined' && result['error']['desc']!='') // desc,value
		{
			_TPV.showInfo(result['error']['desc']);
			if(typeof result['error']['value']!='undefined' && parseInt(result['error']['value'])==1)
				return false;
		}
		if(typeof result['error']!='undefined' && typeof result['error']['value']!='undefined' && result['error']['value']==0) // desc,value
		{
			
			if(typeof result['data']!='undefined')
				return result['data']; 
		}
		//_TPV.showInfo('Error de ejecucion del codigo javascript');
	}
	return result;
}
