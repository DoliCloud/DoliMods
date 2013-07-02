<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:00
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/orders/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:131038306651c1c0e0ecdea8-69029937%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c5b37a71f4225ee53ccc98bca05b44ec298e6af9' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/orders/form.tpl',
      1 => 1371647784,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '131038306651c1c0e0ecdea8-69029937',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cart' => 0,
    'current' => 0,
    'token' => 0,
    'link' => 0,
    'pic_dir' => 0,
    'defaults_order_state' => 0,
    'module' => 0,
    'id_order_state' => 0,
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'table' => 0,
    'css_files_orders' => 0,
    'css_uri' => 0,
    'media' => 0,
    'currencies' => 0,
    'currency' => 0,
    'langs' => 0,
    'lang' => 0,
    'recyclable_pack' => 0,
    'gift_wrapping' => 0,
    'payment_modules' => 0,
    'order_states' => 0,
    'order_state' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e14b7ae9_86200421',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e14b7ae9_86200421')) {function content_51c1c0e14b7ae9_86200421($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>
<script type="text/javascript">
	var id_cart = <?php echo intval($_smarty_tpl->tpl_vars['cart']->value->id);?>
;
	var id_customer = 0;
	var changed_shipping_price = false;
	var shipping_price_selected_carrier = '';
	var current_index = '<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
';
	var admin_cart_link = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
';
	var cart_quantity = new Array();
	var currencies = new Array();
	var id_currency = '';
	var id_lang = '';
	var txt_show_carts = '<?php echo smartyTranslate(array('s'=>'Show carts and orders for this customer.'),$_smarty_tpl);?>
';
	var txt_hide_carts = '<?php echo smartyTranslate(array('s'=>'Hide carts and orders for this customer.'),$_smarty_tpl);?>
';
	var defaults_order_state = new Array();
	var customization_errors = false;
	var pic_dir = '<?php echo $_smarty_tpl->tpl_vars['pic_dir']->value;?>
';
	<?php  $_smarty_tpl->tpl_vars['id_order_state'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['id_order_state']->_loop = false;
 $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['defaults_order_state']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['id_order_state']->key => $_smarty_tpl->tpl_vars['id_order_state']->value){
$_smarty_tpl->tpl_vars['id_order_state']->_loop = true;
 $_smarty_tpl->tpl_vars['module']->value = $_smarty_tpl->tpl_vars['id_order_state']->key;
?>
		defaults_order_state['<?php echo $_smarty_tpl->tpl_vars['module']->value;?>
'] = '<?php echo $_smarty_tpl->tpl_vars['id_order_state']->value;?>
';
	<?php } ?>
	$(document).ready(function() {
				
		$('#customer').typeWatch({
			captureLength: 1,
			highlight: true,
			wait: 100,
			callback: function(){ searchCustomers(); }
			});
		$('#product').typeWatch({
			captureLength: 1,
			highlight: true,
			wait: 750,
			callback: function(){ searchProducts(); }
		});
		$('#payment_module_name').change(function() {
			var id_order_state = defaults_order_state[this.value];
			if (typeof(id_order_state) == 'undefined')
				id_order_state = defaults_order_state['other'];
			$('#id_order_state').val(id_order_state);
		});
		$("#id_address_delivery").change(function() {
			updateAddresses();
		});
		$("#id_address_invoice").change(function() {
			updateAddresses();
		});
		$('#id_currency').change(function() {
			updateCurrency();
		});
		$('#id_lang').change(function(){
			updateLang();
		});
		$('#delivery_option,#carrier_recycled_package,#order_gift,#gift_message').change(function() {
			updateDeliveryOption();
		});
		$('#shipping_price').change(function() {
			if ($(this).val() != shipping_price_selected_carrier)
				changed_shipping_price = true;
		});
		$('#show_old_carts').click(function() {
			if ($('#old_carts_orders:visible').length == 0)
			{
				$(this).html(txt_hide_carts);
				$('#old_carts_orders').slideDown('slow');
			}
			else
			{
				$(this).html(txt_show_carts);
				$('#old_carts_orders').slideUp('slow');
			}
			return false;
		});
		$('#send_email_to_customer').click(function(){
			sendMailToCustomer();
			return false;
		});
		$('#show_old_carts').click();
		$('#payment_module_name').change();
		$.ajaxSetup({ type:"post" });
		$("#voucher").autocomplete('<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCartRules');?>
', {
					minChars: 3,
					max: 15,
					width: 250,
					selectFirst: false,
					scroll: false,
					dataType: "json",
					formatItem: function(data, i, max, value, term) {
						return value;
					},
					parse: function(data) {
						if (!data.found)
							$('#vouchers_err').html('<?php echo smartyTranslate(array('s'=>'No voucher was found'),$_smarty_tpl);?>
').show();
						else
							$('#vouchers_err').hide();
						var mytab = new Array();
						for (var i = 0; i < data.vouchers.length; i++)
							mytab[mytab.length] = { data: data.vouchers[i], value: data.vouchers[i].name+' - '+data.vouchers[i].description };
						return mytab;
					},
					extraParams: {
						ajax: "1",
						token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCartRules'),$_smarty_tpl);?>
",
						tab: "AdminCartRules",
						action: "searchCartRuleVouchers"
					}
				}
			)
			.result(function(event, data, formatted) {
				$('#voucher').val(data.name);
				add_cart_rule(data.id_cart_rule);
			});
		<?php if ($_smarty_tpl->tpl_vars['cart']->value->id){?>
			setupCustomer(<?php echo intval($_smarty_tpl->tpl_vars['cart']->value->id_customer);?>
);
			useCart('<?php echo intval($_smarty_tpl->tpl_vars['cart']->value->id);?>
');
		<?php }?>

		$('.delete_product').live('click', function(e) {
			e.preventDefault();
			var to_delete = $(this).attr('rel').split('_');
			deleteProduct(to_delete[1], to_delete[2], to_delete[3]);
		});
		$('.delete_discount').live('click', function(e) {
			e.preventDefault();
			deleteVoucher($(this).attr('rel'));
		});
		$('.use_cart').live('click', function(e) {
			e.preventDefault();
			useCart($(this).attr('rel'));
			return false;
		});
		$('#free_shipping').click(function() {
			var free_shipping = 0;
			if (this.checked)
				free_shipping = 1;
			$.ajax({
				type:"POST",
				url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
				async: true,
				dataType: "json",
				data : {
					ajax: "1",
					token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
					tab: "AdminCarts",
					action: "updateFreeShipping",
					id_cart: id_cart,
					id_customer: id_customer,
					'free_shipping': free_shipping
					},
				success : function(res)
				{
					displaySummary(res);
				}
			});
		});

		$('.duplicate_order').live('click', function(e) {
			e.preventDefault();
			duplicateOrder($(this).attr('rel'));
		});
		$('.cart_quantity').live('change', function(e) {
			e.preventDefault();
			if ($(this).val() != cart_quantity[$(this).attr('rel')])
			{
				var product = $(this).attr('rel').split('_');
				updateQty(product[0], product[1], product[2], $(this).val() - cart_quantity[$(this).attr('rel')]);
			}
		});
		$('.increaseqty_product, .decreaseqty_product').live('click', function(e) {
			e.preventDefault();
			var product = $(this).attr('rel').split('_');
			var sign = '';
			if ($(this).hasClass('decreaseqty_product'))
				sign = '-';
			updateQty(product[0], product[1],product[2], sign+1);
		});
		$('#id_product').live('keydown', function(e) {
			$(this).click();
			return true;
		});
		$('#id_product, .id_product_attribute').live('change', function(e) {
			e.preventDefault();
			displayQtyInStock(this.id);
		});
		$('#id_product, .id_product_attribute').live('keydown', function(e) {
			$(this).change();
			return true;
		});
		$('.product_unit_price').live('change', function(e) {
			e.preventDefault();
			var product = $(this).attr('rel').split('_');
			updateProductPrice(product[0], product[1], $(this).val());
		});
		$('#order_message').live('change', function(e) {
			e.preventDefault();
			$.ajax({
				type:"POST",
				url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
				async: true,
				dataType: "json",
				data : {
					ajax: "1",
					token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
					tab: "AdminCarts",
					action: "updateOrderMessage",
					id_cart: id_cart,
					id_customer: id_customer,
					message: $(this).val()
					},
				success : function(res)
				{
					displaySummary(res);
				}
			});
		});
		resetBind();
	});

	function resetBind()
	{
		$('.fancybox').fancybox({
			'type': 'iframe',
			'width': '60%',
			'height': '100%'
		});
		/*$("#new_address").fancybox({
			onClosed: useCart(id_cart)
		});*/
	}

	function add_cart_rule(id_cart_rule)
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "addVoucher",
				id_cart_rule: id_cart_rule,
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
				$('#voucher').val('');
				var errors = '';
				if (res.errors.length > 0)
				{
					$.each(res.errors, function() {
						errors += this+'<br/>';
					});
					$('#vouchers_err').html(errors).show();
				}
				else
					$('#vouchers_err').hide();
			}
		});
	}

	function updateProductPrice(id_product, id_product_attribute, new_price)
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "updateProductPrice",
				id_cart: id_cart,
				id_product: id_product,
				id_product_attribute: id_product_attribute,
				id_customer: id_customer,
				price: new Number(new_price.replace(",",".")).toFixed(4).toString()
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function displayQtyInStock(id)
	{
		var id_product = $('#id_product').val();
		if ($('#ipa_' + id_product + ' option').length)
			var id_product_attribute = $('#ipa_' + id_product).val();
		else
			var id_product_attribute = 0;

		$('#qty_in_stock').html(stock[id_product][id_product_attribute]);
	}

	function duplicateOrder(id_order)
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "duplicateOrder",
				id_order: id_order,
				id_customer: id_customer
				},
			success : function(res)
			{
				id_cart = res.cart.id;
				$('#id_cart').val(id_cart);
				displaySummary(res);
			}
		});
	}

	function useCart(id_new_cart)
	{
		id_cart = id_new_cart;
		$('#id_cart').val(id_cart);
		$('#id_cart').val(id_cart);
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: false,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "getSummary",
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function getSummary()
	{
		useCart(id_cart);
	}

	function deleteVoucher(id_cart_rule)
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "deleteVoucher",
				id_cart_rule: id_cart_rule,
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function deleteProduct(id_product, id_product_attribute, id_customization)
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "deleteProduct",
				id_product: id_product,
				id_product_attribute: id_product_attribute,
				id_customization: id_customization,
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function searchCustomers()
	{
		$.ajax({
			type:"POST",
			url : "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
",
				tab: "AdminOrders",
				action: "searchCustomers",
				customer_search: $('#customer').val()},
			success : function(res)
			{
				if(res.found)
				{
					var html = '<ul>';
					$.each(res.customers, function() {
						html += '<li class="customerCard"><div class="customerName"><a class="fancybox" href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomers');?>
&id_customer='+this.id_customer+'&viewcustomer&liteDisplaying=1">'+this.firstname+' '+this.lastname+'</a><span class="customerBirthday"> '+((this.birthday != '0000-00-00') ? this.birthday : '')+'</span></div>';
						html += '<div class="customerEmail"><a href="mailto:'+this.email+'">'+this.email+'</div>';
						html += '<a onclick="setupCustomer('+ this.id_customer+');return false;" href="#" class="id_customer button"><?php echo smartyTranslate(array('s'=>'Choose'),$_smarty_tpl);?>
</a></li>';
					});
					html += '</ul>';
				}
				else
					html = '<div class="warn"><?php echo smartyTranslate(array('s'=>'No customers found'),$_smarty_tpl);?>
</div>';
				$('#customers').html(html);
				resetBind();
			}
		});
	}

	function setupCustomer(idCustomer)
	{
		$('#products_part').show();
		$('#vouchers_part').show();
		$('#address_part').show();
		$('#carriers_part').show();
		$('#summary_part').show();
		var address_link = $('#new_address').attr('href');
		id_customer = idCustomer;
		id_cart = 0;
		$('#new_address').attr('href', address_link.replace(/id_customer=[0-9]+/, 'id_customer='+id_customer));
		$.ajax({
			type:"POST",
			url : "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: false,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "searchCarts",
				id_customer: id_customer,
				id_cart: id_cart
			},
			success : function(res)
			{
				if(res.found)
				{
					var html_carts = '';
					var html_orders = '';
					$.each(res.carts, function() {
						html_carts += '<tr><td>'+this.id_cart+'</td><td>'+this.date_add+'</td><td>'+this.total_price+'</td>';
						html_carts += '<td><a title="<?php echo smartyTranslate(array('s'=>'View this cart'),$_smarty_tpl);?>
" class="fancybox" href="index.php?tab=AdminCarts&id_cart='+this.id_cart+'&viewcart&token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
&liteDisplaying=1#"><img src="../img/admin/details.gif" /></a>';
						html_carts += '<a href="#" title="<?php echo smartyTranslate(array('s'=>'Use this cart'),$_smarty_tpl);?>
" class="use_cart" rel="'+this.id_cart+'"><img src="../img/admin/duplicate.png" /></a></td></tr>';
					});
					$.each(res.orders, function() {
						html_orders += '<tr><td>'+this.id_order+'</td><td>'+this.date_add+'</td><td>'+(this.nb_products ? this.nb_products : '0')+'</td><td>'+this.total_paid_real+'</span></td><td>'+this.payment+'</td><td>'+this.order_state+'</td>';
						html_orders += '<td><a title="<?php echo smartyTranslate(array('s'=>'View this order'),$_smarty_tpl);?>
" class="fancybox" href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders');?>
&id_order='+this.id_order+'&vieworder&liteDisplaying=1#"><img src="../img/admin/details.gif" /></a>';
						html_orders += '<a href="#" "title="<?php echo smartyTranslate(array('s'=>'Duplicate this order'),$_smarty_tpl);?>
" class="duplicate_order" rel="'+this.id_order+'"><img src="../img/admin/duplicate.png" /></a></td></tr>';
					});
					$('#nonOrderedCarts table tbody').html(html_carts);
					$('#lastOrders table tbody').html(html_orders);
				}
				if (res.id_cart)
				{
					id_cart = res.id_cart;
					$('#id_cart').val(id_cart);
				}
				displaySummary(res);
				resetBind();
				updateCurrencySign();
			}
		});
	}

	function updateDeliveryOptionList(delivery_option_list)
	{
		var html = '';
		if (delivery_option_list.length > 0)
		{
			$.each(delivery_option_list, function() {
				html += '<option value="'+this.key+'" '+(($('#delivery_option').val() == this.key) ? 'selected="selected"' : '')+'>'+this.name+'</option>';
			});
			$('#carrier_form').show();
			$('#delivery_option').html(html);
			$('#carriers_err').hide();
		}
		else
		{
			$('#carrier_form').hide();
			$('#carriers_err').show().html('<?php echo smartyTranslate(array('s'=>'No carrier can be applied to this order'),$_smarty_tpl);?>
');
		}
	}

	function searchProducts()
	{
		$('#products_part').show();
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
",
				tab: "AdminOrders",
				action: "searchProducts",
				id_cart: id_cart,
				id_customer: id_customer,
				id_currency: id_currency,
				product_search: $('#product').val()},
			success : function(res)
			{
				var products_found = '';
				var attributes_html = '';
				var customization_html = '';
				stock = {};

				if(res.found)
				{
					if (!customization_errors)
						$('#products_err').hide();
					else
						customization_errors = false;
					$('#products_found').show();
					products_found += '<label><?php echo smartyTranslate(array('s'=>'Product:'),$_smarty_tpl);?>
</label><select id="id_product" onclick="display_product_attributes();display_product_customizations();">';
					attributes_html += '<label><?php echo smartyTranslate(array('s'=>'Combination'),$_smarty_tpl);?>
</label>';
					$.each(res.products, function() {
						products_found += '<option '+(this.combinations.length > 0 ? 'rel="'+this.qty_in_stock+'"' : '')+' value="'+this.id_product+'">'+this.name+(this.combinations.length == 0 ? ' - '+this.formatted_price : '')+'</option>';
						attributes_html += '<select class="id_product_attribute" id="ipa_'+this.id_product+'" style="display:none;">';
						var id_product = this.id_product;
						stock[id_product] = new Array();
						if (this.customizable == '1')
						{
							customization_html += '<fieldset class="width3"><legend><?php echo smartyTranslate(array('s'=>'Customization'),$_smarty_tpl);?>
</legend><form id="customization_'+id_product+'" class="id_customization" method="post" enctype="multipart/form-data" action="'+admin_cart_link+'" style="display:none;">';
							customization_html += '<input type="hidden" name="id_product" value="'+id_product+'" />';
							customization_html += '<input type="hidden" name="id_cart" value="'+id_cart+'" />';
							customization_html += '<input type="hidden" name="action" value="updateCustomizationFields" />';
							customization_html += '<input type="hidden" name="id_customer" value="'+id_customer+'" />';
							customization_html += '<input type="hidden" name="ajax" value="1" />';
							$.each(this.customization_fields, function() {
								customization_html += '<p><label for="customization_'+id_product+'_'+this.id_customization_field+'">';
								if (this.required == 1)
									customization_html += '<sup>*</sup>';
								customization_html += this.name+'<?php echo smartyTranslate(array('s'=>':'),$_smarty_tpl);?>
</label>';
								if (this.type == 0)
									customization_html += '<input class="customization_field" type="file" name="customization_'+id_product+'_'+this.id_customization_field+'" id="customization_'+id_product+'_'+this.id_customization_field+'">';
								else if (this.type == 1)
									customization_html += '<input class="customization_field" type="text" name="customization_'+id_product+'_'+this.id_customization_field+'" id="customization_'+id_product+'_'+this.id_customization_field+'">';
								customization_html += '</p>';
							});
							customization_html += '</fieldset></form>';
						}

						$.each(this.combinations, function() {
							attributes_html += '<option rel="'+this.qty_in_stock+'" '+(this.default_on == 1 ? 'selected="selected"' : '')+' value="'+this.id_product_attribute+'">'+this.attributes+' - '+this.formatted_price+'</option>';
							stock[id_product][this.id_product_attribute] = this.qty_in_stock;
						});

						stock[this.id_product][0] = this.stock[0];
						attributes_html += '</select>';
					});
					products_found += '</select>';
					$('#products_found #product_list').html(products_found);
					$('#products_found #attributes_list').html(attributes_html);		
					$('link[rel="stylesheet"]').each(function (i, element) {
						sheet = $(element).clone();
						$('#products_found #customization_list').contents().find('head').append(sheet);
					});
					$('#products_found #customization_list').contents().find('body').html(customization_html);
					display_product_attributes();
					display_product_customizations();
					$('#id_product').change();
				}
				else
				{
					$('#products_found').hide();
					$('#products_err').html('<?php echo smartyTranslate(array('s'=>'No products found'),$_smarty_tpl);?>
');
					$('#products_err').show();
				}
				resetBind();
			}
		});
	}

	function display_product_customizations()
	{
		if ($('#products_found #customization_list').contents().find('#customization_'+$('#id_product option:selected').val()).children().length === 0)
			$('#customization_list').hide();
		else
		{
			$('#customization_list').show();
			$('#products_found #customization_list').contents().find('.id_customization').hide();
			$('#products_found #customization_list').contents().find('#customization_'+$('#id_product option:selected').val()).show();
			$('#products_found #customization_list').css('height',$('#products_found #customization_list').contents().find('#customization_'+$('#id_product option:selected').val()).height()+95+'px');
		}
	}

	function display_product_attributes()
	{
		if ($('#ipa_'+$('#id_product option:selected').val()+' option').length === 0)
			$('#attributes_list').hide();
		else
		{
			$('#attributes_list').show();
			$('.id_product_attribute').hide();
			$('#ipa_'+$('#id_product option:selected').val()).show();
		}
	}

	function updateCartProducts(products, gifts, id_address_delivery)
	{
		var cart_content = '';
		$.each(products, function() {
			var id_product = Number(this.id_product);
			var id_product_attribute = Number(this.id_product_attribute);
			cart_quantity[Number(this.id_product)+'_'+Number(this.id_product_attribute)+'_'+Number(this.id_customization)] = this.cart_quantity;
			cart_content += '<tr><td><img src="'+this.image_link+'" title="'+this.name+'" /></td><td>'+this.name+'<br />'+this.attributes_small+'</td><td>'+this.reference+'</td><td><input type="text" size="7" rel="'+this.id_product+'_'+this.id_product_attribute+'" class="product_unit_price" value="'+this.price+'" />&nbsp;<span class="currency_sign"></span></td><td>';
			cart_content += (!this.id_customization ? '<div style="float:left;"><a href="#" class="increaseqty_product" rel="'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'" ><img src="../img/admin/up.gif" /></a><br /><a href="#" class="decreaseqty_product" rel="'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'"><img src="../img/admin/down.gif" /></a></div>' : '');
			cart_content += (!this.id_customization ? '<div style="float:left;"><input type="text" rel="'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'" class="cart_quantity" size="2" value="'+this.cart_quantity+'" />' : '');
			cart_content += (!this.id_customization ? '<a href="#" class="delete_product" rel="delete_'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'" ><img src="../img/admin/delete.gif" /></a></div>' : '');
			cart_content += '</td><td>'+this.total+'&nbsp;<span class="currency_sign"></span></td></tr>';
			if (this.id_customization && this.id_customization != 0)
			{
				$.each(this.customized_datas[this.id_product][this.id_product_attribute][id_address_delivery], function() {
					var customized_desc = '';
					if(this.datas[1].length)
					{
						$.each(this.datas[1],function() {
							customized_desc += this.name+':'+this.value+'<br />';
							id_customization = this.id_customization;
						});
					}
					if(this.datas[0] && this.datas[0].length)
					{
						$.each(this.datas[0],function() {
							customized_desc += this.name+':<img src="'+pic_dir+this.value+'_small" /><br />';
							id_customization = this.id_customization;
						});
					}
			cart_content += '<tr><td></td><td>'+customized_desc+'</td><td></td><td></td><td>';
			cart_content += '<div style="float:left;"><a href="#" class="increaseqty_product" rel="'+id_product+'_'+id_product_attribute+'_'+id_customization+'" ><img src="../img/admin/up.gif" /></a><br /><a href="#" class="decreaseqty_product" rel="'+id_product+'_'+id_product_attribute+'_'+id_customization+'"><img src="../img/admin/down.gif" /></a></div>';
			cart_content += '<div style="float:left;"><input type="text" rel="'+id_product+'_'+id_product_attribute+'_'+id_customization +'" class="cart_quantity" size="2" value="'+this.quantity+'" />';
			cart_content += '<a href="#" class="delete_product" rel="delete_'+id_product+'_'+id_product_attribute+'_'+id_customization+'" ><img src="../img/admin/delete.gif" /></a>';
			cart_content += '</div></td><td></td></tr>';
				});
			}
		});

		$.each(gifts, function() {
			cart_content += '<tr><td><img src="'+this.image_link+'" title="'+this.name+'" /></td><td>'+this.name+'<br />'+this.attributes_small+'</td><td>'+this.reference+'</td>';
			cart_content += '<td><?php echo smartyTranslate(array('s'=>'Gift'),$_smarty_tpl);?>
</td><td>'+this.cart_quantity+'</td><td><?php echo smartyTranslate(array('s'=>'Gift'),$_smarty_tpl);?>
</td></tr>';
		});
		$('#customer_cart tbody').html(cart_content);
	}

	function updateCartVouchers(vouchers)
	{
		var vouchers_html = '';
		if (vouchers.length > 0)
		{
			$.each(vouchers, function() {
				vouchers_html += '<tr><td>'+this.name+'</td><td>'+this.description+'</td><td>'+this.value_real+'</td><td><a href="#" class="delete_discount" rel="'+this.id_discount+'"><img src="../img/admin/delete.gif" /></a></td></tr>';
			});
			$('#voucher_list').show();
		}
		else
			$('#voucher_list').hide();

		$('#voucher_list tbody').html(vouchers_html);
	}

	function updateCartPaymentList(payment_list)
	{
		$('#payment_list').html(payment_list);
	}

	function displaySummary(jsonSummary)
	{
		updateCartProducts(jsonSummary.summary.products, jsonSummary.summary.gift_products, jsonSummary.cart.id_address_delivery);
		updateCartVouchers(jsonSummary.summary.discounts);
		updateAddressesList(jsonSummary.addresses, jsonSummary.cart.id_address_delivery, jsonSummary.cart.id_address_invoice);

		if (!jsonSummary.summary.products.length || !jsonSummary.addresses.length || !jsonSummary.delivery_option_list)
			$('#carriers_part,#summary_part').hide();
		else
			$('#carriers_part,#summary_part').show();

		updateDeliveryOptionList(jsonSummary.delivery_option_list);

		if (jsonSummary.cart.gift == 1)
			$('#order_gift').attr('checked', true);
		else
			$('#carrier_gift').removeAttr('checked');
		if (jsonSummary.cart.recyclable == 1)
			$('#carrier_recycled_package').attr('checked', true);
		else
			$('#carrier_recycled_package').removeAttr('checked');
		if (jsonSummary.free_shipping == 1)
			$('#free_shipping').attr('checked', true);
		else
			$('#free_shipping').removeAttr('checked');

		$('#gift_message').html(jsonSummary.cart.gift_message);
		if(!changed_shipping_price)
			$('#shipping_price').html('<b>'+jsonSummary.summary.total_shipping+'</b>');
		shipping_price_selected_carrier = jsonSummary.summary.total_shipping;

		$('#total_vouchers').html(jsonSummary.summary.total_discounts_tax_exc);
		$('#total_shipping').html(jsonSummary.summary.total_shipping_tax_exc);
		$('#total_taxes').html(jsonSummary.summary.total_tax);
		$('#total_without_taxes').html(jsonSummary.summary.total_price_without_tax);
		$('#total_with_taxes').html(jsonSummary.summary.total_price);
		$('#total_products').html(jsonSummary.summary.total_products);
		id_currency = jsonSummary.cart.id_currency;
		$('#id_currency option').removeAttr('selected');
		$('#id_currency option[value="'+id_currency+'"]').attr('selected', true);
		updateCurrencySign();
		id_lang = jsonSummary.cart.id_lang;
		$('#id_lang option').removeAttr('selected');
		$('#id_lang option[value="'+id_lang+'"]').attr('selected', true);
		$('#send_email_to_customer').attr('rel', jsonSummary.link_order);
		$('#go_order_process').attr('href', jsonSummary.link_order);
		$('#order_message').val(jsonSummary.order_message);
		resetBind();
	}

	function updateQty(id_product, id_product_attribute, id_customization, qty)
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "updateQty",
				id_product: id_product,
				id_product_attribute: id_product_attribute,
				id_customization: id_customization,
				qty: qty,
				id_customer: id_customer,
				id_cart: id_cart,
				},
			success : function(res)
			{
					displaySummary(res);
					var errors = '';
					if(res.errors.length)
					{
						$.each(res.errors, function() {
							errors += this+'<br />';
						});
						$('#products_err').show();
					}
					else
						$('#products_err').hide();
					$('#products_err').html(errors);
			}
		});
	}

	function resetShippingPrice()
	{
		$('#shipping_price').val(shipping_price_selected_carrier);
		changed_shipping_price = false;
	}

	function addProduct()
	{
		var id_product = $('#id_product option:selected').val();
		$('#products_found #customization_list').contents().find('#customization_'+id_product).submit();
		if (customization_errors)
			$('#products_err').show();
		else
		{
			$('#products_err').hide();
			updateQty(id_product, $('#ipa_'+id_product+' option:selected').val(), 0, $('#qty').val());
		}
	}

	function updateCurrency()
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "updateCurrency",
				id_currency: $('#id_currency option:selected').val(),
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
					displaySummary(res);
			}
		});
	}

	function updateLang()
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "admincarts",
				action: "updateLang",
				id_lang: $('#id_lang option:selected').val(),
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
					displaySummary(res);
			}
		});
	}

	function updateDeliveryOption()
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "updateDeliveryOption",
				delivery_option: $('#delivery_option option:selected').val(),
				gift: $('#order_gift').is(':checked')?1:0,
				gift_message: $('#gift_message').val(),
				recyclable: $('#carrier_recycled_package').is(':checked')?1:0,
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function updateCurrencySign()
	{
		$('.currency_sign').html(currencies[id_currency]);
	}

	function sendMailToCustomer()
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminOrders'),$_smarty_tpl);?>
",
				tab: "AdminOrders",
				action: "sendMailValidateOrder",
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
				if (res.errors)
					$('#send_email_feedback').removeClass('conf').addClass('error');
				else
					$('#send_email_feedback').removeClass('error').addClass('conf');
				$('#send_email_feedback').html(res.result);
			}
		});
	}

	function updateAddressesList(addresses, id_address_delivery, id_address_invoice)
	{
		var addresses_delivery_options = '';
		var addresses_invoice_options = '';
		var address_invoice_detail = '';
		var address_delivery_detail = '';
		$.each(addresses, function() {
			if (this.id_address == id_address_invoice)
				address_invoice_detail = this.company+' '+this.firstname+' '+this.lastname+'<br />'+this.address1+'<br />'+this.address2+'<br />'+this.postcode+' '+this.city+' '+this.country;
			if(this.id_address == id_address_delivery)
				address_delivery_detail = this.company+' '+this.firstname+' '+this.lastname+'<br />'+this.address1+'<br />'+this.address2+'<br />'+this.postcode+' '+this.city+' '+this.country;

			addresses_delivery_options += '<option value="'+this.id_address+'" '+(this.id_address == id_address_delivery ? 'selected="selected"' : '')+'>'+this.alias+'</option>';
			addresses_invoice_options += '<option value="'+this.id_address+'" '+(this.id_address == id_address_invoice ? 'selected="selected"' : '')+'>'+this.alias+'</option>';
		});
		if (addresses.length == 0)
		{
			$('#addresses_err').show().html('<?php echo smartyTranslate(array('s'=>'You must add at least one address to process the order.'),$_smarty_tpl);?>
');
			$('#address_delivery, #address_invoice').hide();
		}
		else
		{
			$('#addresses_err').hide();
			$('#address_delivery, #address_invoice').show();
		}

		$('#id_address_delivery').html(addresses_delivery_options);
		$('#id_address_invoice').html(addresses_invoice_options);
		$('#address_delivery_detail').html(address_delivery_detail);
		$('#address_invoice_detail').html(address_invoice_detail);
	}

	function updateAddresses()
	{
		$.ajax({
			type:"POST",
			url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCarts');?>
",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCarts'),$_smarty_tpl);?>
",
				tab: "AdminCarts",
				action: "updateAddresses",
				id_customer: id_customer,
				id_cart: id_cart,
				id_address_delivery: $('#id_address_delivery option:selected').val(),
				id_address_invoice: $('#id_address_invoice option:selected').val()
				},
			success : function(res)
			{
				updateDeliveryOption();
			}
		});
	}
</script>

<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<div class="leadin"></div>

<fieldset id="customer_part">
	<legend><img src="../img/admin/tab-customers.gif" /><?php echo smartyTranslate(array('s'=>'Customer'),$_smarty_tpl);?>
</legend>
	<label><?php echo smartyTranslate(array('s'=>'Search customers'),$_smarty_tpl);?>
</label>
	<div class="margin-form">
		<input type="text" id="customer" value="" />
		<p><?php echo smartyTranslate(array('s'=>'Search a customer by tapping the first letters of his/her name'),$_smarty_tpl);?>
</p>
		<a class="fancybox button" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomers'), 'htmlall', 'UTF-8');?>
&addcustomer&liteDisplaying=1&submitFormAjax=1#">
			<img src="../img/admin/add.gif" title="new"/><span><?php echo smartyTranslate(array('s'=>'Add new customer'),$_smarty_tpl);?>
</span>
		</a>
	</div>
	<div id="customers">
	</div>
</fieldset><br />
<form action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), 'htmlall', 'UTF-8');?>
&submitAdd<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
=1" method="post" autocomplete="off">
<fieldset id="products_part" style="display:none;"><legend><img src="../img/t/AdminCatalog.gif" /><?php echo smartyTranslate(array('s'=>'Cart'),$_smarty_tpl);?>
</legend>
	<div>
		<label><?php echo smartyTranslate(array('s'=>'Search for a product'),$_smarty_tpl);?>
 </label>
		<div class="margin-form">
			<input type="hidden" value="" id="id_cart" name="id_cart" />
			<input type="text" id="product" value="" />
			<p><?php echo smartyTranslate(array('s'=>'Search a product by tapping the first letters of his/her name.'),$_smarty_tpl);?>
</p>
		</div>
		<div id="products_found">
			<div id="product_list">
			</div>
			<div id="attributes_list">
			</div>
			<iframe id="customization_list" style="border:0px;overflow:hidden;width:100%">
				<html>
				<head>
					<?php if (isset($_smarty_tpl->tpl_vars['css_files_orders']->value)){?>
						<?php  $_smarty_tpl->tpl_vars['media'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['media']->_loop = false;
 $_smarty_tpl->tpl_vars['css_uri'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['css_files_orders']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['media']->key => $_smarty_tpl->tpl_vars['media']->value){
$_smarty_tpl->tpl_vars['media']->_loop = true;
 $_smarty_tpl->tpl_vars['css_uri']->value = $_smarty_tpl->tpl_vars['media']->key;
?>
							<link href="<?php echo $_smarty_tpl->tpl_vars['css_uri']->value;?>
" rel="stylesheet" type="text/css" media="<?php echo $_smarty_tpl->tpl_vars['media']->value;?>
" />
						<?php } ?>
					<?php }?>
				</head>
				<body>
				</body>
				</html>
			</iframe>
			<p><label for="qty"><?php echo smartyTranslate(array('s'=>'Quantity:'),$_smarty_tpl);?>
</label><input type="text" name="qty" id="qty" value="1" />&nbsp;<b><?php echo smartyTranslate(array('s'=>'In stock'),$_smarty_tpl);?>
</b>&nbsp;<span id="qty_in_stock"></span></p>
			<div class="margin-form">
				<p><input type="submit" onclick="addProduct();return false;" class="button" id="submitAddProduct" value="<?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
"/></p>
			</div>
		</div>
	</div>
	<div id="products_err" class="warn" style="display:none;"></div>
	<div>
		<table cellspacing="0" cellpadding="0" class="table width5" id="customer_cart">
				<colgroup>
					<col width="50px">
					<col width="">
					<col width="90px">
					<col width="100px">
					<col width="50px">
					<col width="50px">
				</colgroup>
			<thead>
				<tr>
					<th height="39px"><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</th>
					<th><?php echo smartyTranslate(array('s'=>'Description'),$_smarty_tpl);?>
</th>
					<th><?php echo smartyTranslate(array('s'=>'Ref'),$_smarty_tpl);?>
</th>
					<th><?php echo smartyTranslate(array('s'=>'Unit price'),$_smarty_tpl);?>
</th>
					<th style="width: 80px;"><?php echo smartyTranslate(array('s'=>'Qty'),$_smarty_tpl);?>
</th>
					<th style="width: 80px;"><?php echo smartyTranslate(array('s'=>'Price'),$_smarty_tpl);?>
</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<p><b><?php echo smartyTranslate(array('s'=>'The prices are without taxes.'),$_smarty_tpl);?>
</b></p>
	</div>
	<div>
		<p><label for="id_currency"><?php echo smartyTranslate(array('s'=>'Currency'),$_smarty_tpl);?>
</label>
			<script type="text/javascript">
				<?php  $_smarty_tpl->tpl_vars['currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency']->key => $_smarty_tpl->tpl_vars['currency']->value){
$_smarty_tpl->tpl_vars['currency']->_loop = true;
?>
					currencies['<?php echo $_smarty_tpl->tpl_vars['currency']->value['id_currency'];?>
'] = '<?php echo $_smarty_tpl->tpl_vars['currency']->value['sign'];?>
';
				<?php } ?>
			</script>
			<select id="id_currency" name="id_currency">
			<?php  $_smarty_tpl->tpl_vars['currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency']->key => $_smarty_tpl->tpl_vars['currency']->value){
$_smarty_tpl->tpl_vars['currency']->_loop = true;
?>
				<option rel="<?php echo $_smarty_tpl->tpl_vars['currency']->value['iso_code'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['currency']->value['id_currency'];?>
"><?php echo $_smarty_tpl->tpl_vars['currency']->value['name'];?>
</option>
			<?php } ?>
			</select>
		</p>
		<p>
		<label for="id_lang"><?php echo smartyTranslate(array('s'=>'Language'),$_smarty_tpl);?>
</label>
		<select id="id_lang" name="id_lang">
			<?php  $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lang']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['langs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->key => $_smarty_tpl->tpl_vars['lang']->value){
$_smarty_tpl->tpl_vars['lang']->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['id_lang'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['name'];?>
</option>
			<?php } ?>
		</select>
		</p>
	</div>
	<div class="separation"></div>
	<div id="carts">
		<p><a href="#" id="show_old_carts" class="button"></a></p>
		<div id="old_carts_orders">
			<div id="nonOrderedCarts">
				<h3><?php echo smartyTranslate(array('s'=>'Carts'),$_smarty_tpl);?>
</h3>
				<table cellspacing="0" cellpadding="0" class="table  width5">
					<colgroup>
						<col width="10px">
						<col width="">
						<col width="70px">
						<col width="50px">
					</colgroup>
				<thead>
					<tr>
						<th height="39px" class="left"><?php echo smartyTranslate(array('s'=>'ID'),$_smarty_tpl);?>
</th>
						<th class="left"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
						<th class="left"><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</th>
						<th class="left"><?php echo smartyTranslate(array('s'=>'Action'),$_smarty_tpl);?>
</th>
					</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div id="lastOrders">
				<h3><?php echo smartyTranslate(array('s'=>'Orders'),$_smarty_tpl);?>
</h3>
				<table cellspacing="0" cellpadding="0" class="table  width5">
					<colgroup>
						<col width="10px">
						<col width="50px">
						<col width="">
						<col width="90px">
						<col width="100px">
						<col width="250px">
						<col width="50px">
					</colgroup>
					<thead>
						<tr>
							<th height=39px" class="left"><?php echo smartyTranslate(array('s'=>'ID'),$_smarty_tpl);?>
</th>
							<th class="left"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
							<th class="left"><?php echo smartyTranslate(array('s'=>'Products:'),$_smarty_tpl);?>
</th>
							<th class="left"><?php echo smartyTranslate(array('s'=>'Total paid'),$_smarty_tpl);?>
</th>
							<th class="left"><?php echo smartyTranslate(array('s'=>'Payment: '),$_smarty_tpl);?>
</th>
							<th class="left"><?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
</th>
							<th class="left"><?php echo smartyTranslate(array('s'=>'Action'),$_smarty_tpl);?>
</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</fieldset>
<br />
<fieldset id="vouchers_part" style="display:none;">
	<legend><img src="../img/t/AdminCartRules.gif" /><?php echo smartyTranslate(array('s'=>'Vouchers'),$_smarty_tpl);?>
</legend>
	<p>
		<label><?php echo smartyTranslate(array('s'=>'Search for a voucher:'),$_smarty_tpl);?>
 </label>
		<input type="text" id="voucher" value="" />
		<a class="fancybox button" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCartRules'), 'htmlall', 'UTF-8');?>
&addcart_rule&liteDisplaying=1&submitFormAjax=1#"><img src="../img/admin/add.gif" title="new"/><?php echo smartyTranslate(array('s'=>'Add new voucher'),$_smarty_tpl);?>
</a>
	</p>
	<div class="margin-form">
		<table cellspacing="0" cellpadding="0" class="table" id="voucher_list">
			<thead>
				<tr>
					<th class="left"><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</th>
					<th class="left"><?php echo smartyTranslate(array('s'=>'Description'),$_smarty_tpl);?>
</th>
					<th class="left"><?php echo smartyTranslate(array('s'=>'Value'),$_smarty_tpl);?>
</th>
					<th class="left"><?php echo smartyTranslate(array('s'=>'Action'),$_smarty_tpl);?>
</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<div id="vouchers_err" class="warn"></div>
</fieldset>
<br />
<fieldset id="address_part" style="display:none;">
	<legend><img src="../img/t/AdminAddresses.gif" /><?php echo smartyTranslate(array('s'=>'Addresses'),$_smarty_tpl);?>
</legend>
	<div id="addresses_err" class="warn" style="display:none;"></div>
	<div id="address_delivery">
		<h3><?php echo smartyTranslate(array('s'=>'Delivery'),$_smarty_tpl);?>
</h3>
		<select id="id_address_delivery" name="id_address_delivery">
		</select>
		<div id="address_delivery_detail">
		</div>
	</div>
	<div id="address_invoice">
		<h3><?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>
</h3>
		<select id="id_address_invoice" name="id_address_invoice">
		</select>
		<div id="address_invoice_detail">
		</div>
	</div>
<a class="fancybox button" id="new_address" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminAddresses'), 'htmlall', 'UTF-8');?>
&addaddress&id_customer=42&liteDisplaying=1&submitFormAjax=1#"><img src="../img/admin/add.gif" title="new"/><?php echo smartyTranslate(array('s'=>'Add a new address'),$_smarty_tpl);?>
</a>
</fieldset>
<br />
<fieldset id="carriers_part" style="display:none;">
	<legend><img src="../img/t/AdminCarriers.gif" /><?php echo smartyTranslate(array('s'=>'Shipping'),$_smarty_tpl);?>
</legend>
	<div id="carriers_err" style="display:none;" class="warn"></div>
	<div id="carrier_form">
		<div>
			<p>
				<label><?php echo smartyTranslate(array('s'=>'Delivery option'),$_smarty_tpl);?>
 </label>
				<select name="delivery_option" id="delivery_option">
				</select>
			</p>
			<p>
				<label for="shipping_price"><?php echo smartyTranslate(array('s'=>'Shipping price'),$_smarty_tpl);?>
</label> <span id="shipping_price"  name="shipping_price"></span>&nbsp;<span class="currency_sign"></span>&nbsp;
			</p>
			<p>
				<label for="free_shipping"><?php echo smartyTranslate(array('s'=>'Free shipping'),$_smarty_tpl);?>
</label>
				<input type="checkbox" id="free_shipping" name="free_shipping" value="1" />
			</p>
		</div>
		<div id="float:left;">
			<?php if ($_smarty_tpl->tpl_vars['recyclable_pack']->value){?>
				<p><input type="checkbox" name="carrier_recycled_package" value="1" id="carrier_recycled_package" />  <label for="carrier_recycled_package"><?php echo smartyTranslate(array('s'=>'Recycled package'),$_smarty_tpl);?>
</label></p>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['gift_wrapping']->value){?>
				<p><input type="checkbox" name="order_gift" id="order_gift" value="1" /> <label for="order_gift"><?php echo smartyTranslate(array('s'=>'Gift'),$_smarty_tpl);?>
</label></p>
				<p><label for="gift_message"><?php echo smartyTranslate(array('s'=>'Gift message'),$_smarty_tpl);?>
</label><textarea id="gift_message" cols="40" rows="4"></textarea></p>
			<?php }?>
		</div>
	</div>
</fieldset>
<br />
<fieldset id="summary_part" style="display:none;">
	<legend><img src="../img/t/AdminPayment.gif" /><?php echo smartyTranslate(array('s'=>'Summary'),$_smarty_tpl);?>
</legend>
	<div id="send_email_feedback"></div>
	<div id="cart_summary" style="clear:both;float:left;">
		<ul>
			<li><span class="total_cart"><?php echo smartyTranslate(array('s'=>'Total products'),$_smarty_tpl);?>
</span><span id="total_products"></span><span class="currency_sign"></span></li>
			<li><span class="total_cart"><?php echo smartyTranslate(array('s'=>'Total vouchers'),$_smarty_tpl);?>
</span><span id="total_vouchers"></span><span class="currency_sign"></span></li>
			<li><span class="total_cart"><?php echo smartyTranslate(array('s'=>'Total shipping'),$_smarty_tpl);?>
</span><span id="total_shipping"></span><span class="currency_sign"></span></li>
			<li><span class="total_cart"><?php echo smartyTranslate(array('s'=>'Total taxes'),$_smarty_tpl);?>
</span><span id="total_taxes"></span><span class="currency_sign"></span></li>
			<li><span class="total_cart"><?php echo smartyTranslate(array('s'=>'Total without taxes'),$_smarty_tpl);?>
</span><span id="total_without_taxes"></span><span class="currency_sign"></span></li>
			<li><span class="total_cart"><?php echo smartyTranslate(array('s'=>'Total with taxes'),$_smarty_tpl);?>
</span><span id="total_with_taxes"></span><span class="currency_sign"></span></li>
		</ul>
	</div>
	<div class="order_message_right">
		<label for="order_message"><?php echo smartyTranslate(array('s'=>'Order message'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<textarea name="order_message" id="order_message" rows="3" cols="45"></textarea>
		</div>
		<div class="margin-form">
			<a href="#" id="send_email_to_customer" class="button"><?php echo smartyTranslate(array('s'=>'Send an email to the customer with the link to process the payment.'),$_smarty_tpl);?>
</a>
		</div>
		<div class="margin-form">
			<a target="_blank" id="go_order_process" href="" class="button"><?php echo smartyTranslate(array('s'=>'Go on payment page to process the payment.'),$_smarty_tpl);?>
</a>
		</div>
		<label><?php echo smartyTranslate(array('s'=>'Payment'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<select name="payment_module_name" id="payment_module_name">
				<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" <?php if (isset($_POST['payment_module_name'])&&$_smarty_tpl->tpl_vars['module']->value->name==$_POST['payment_module_name']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['module']->value->displayName;?>
</option>
				<?php } ?>
			</select>
		</div>
		<label><?php echo smartyTranslate(array('s'=>'Order status'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<select name="id_order_state" id="id_order_state">
				<?php  $_smarty_tpl->tpl_vars['order_state'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order_state']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order_states']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['order_state']->key => $_smarty_tpl->tpl_vars['order_state']->value){
$_smarty_tpl->tpl_vars['order_state']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['order_state']->value['id_order_state'];?>
" <?php if (isset($_POST['id_order_state'])&&$_smarty_tpl->tpl_vars['order_state']->value['id_order_state']==$_POST['id_order_state']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['order_state']->value['name'];?>
</option>
				<?php } ?>
			</select>
		</div>
		<div class="margin-form">
			<input type="submit" name="submitAddOrder" class="button" value="<?php echo smartyTranslate(array('s'=>'Create the order'),$_smarty_tpl);?>
" />
		</div>
	</div>
</fieldset>
</form>
<div id="loader_container">
	<div id="loader">
	</div>
</div>

<?php }} ?>