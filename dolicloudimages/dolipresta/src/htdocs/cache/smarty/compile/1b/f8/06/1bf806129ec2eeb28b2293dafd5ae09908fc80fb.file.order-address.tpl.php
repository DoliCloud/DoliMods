<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:09
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-address.tpl" */ ?>
<?php /*%%SmartyHeaderCode:185295631151c1c0e9687d85-10144047%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1bf806129ec2eeb28b2293dafd5ae09908fc80fb' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-address.tpl',
      1 => 1371647171,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '185295631151c1c0e9687d85-10144047',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'opc' => 0,
    'currencySign' => 0,
    'currencyRate' => 0,
    'currencyFormat' => 0,
    'currencyBlank' => 0,
    'back_order_page' => 0,
    'back' => 0,
    'link' => 0,
    'formatedAddressFieldsValuesList' => 0,
    'id_address' => 0,
    'type' => 0,
    'field_name' => 0,
    'pattern_name' => 0,
    'empty' => 0,
    'PS_CATALOG_MODE' => 0,
    'multi_shipping' => 0,
    'cart' => 0,
    'is_multi_address_delivery' => 0,
    'open_multishipping_fancybox' => 0,
    'addresses' => 0,
    'address' => 0,
    'oldMessage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e9a38c00_46881470',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e9a38c00_46881470')) {function content_51c1c0e9a38c00_46881470($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?><?php if ($_smarty_tpl->tpl_vars['opc']->value){?>
	<?php $_smarty_tpl->tpl_vars["back_order_page"] = new Smarty_variable("order-opc.php", null, 0);?>
<?php }else{ ?>
	<?php $_smarty_tpl->tpl_vars["back_order_page"] = new Smarty_variable("order.php", null, 0);?>
<?php }?>

<script type="text/javascript">
// <![CDATA[
	<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
	var orderProcess = 'order';
	var currencySign = '<?php echo html_entity_decode($_smarty_tpl->tpl_vars['currencySign']->value,2,"UTF-8");?>
';
	var currencyRate = '<?php echo floatval($_smarty_tpl->tpl_vars['currencyRate']->value);?>
';
	var currencyFormat = '<?php echo intval($_smarty_tpl->tpl_vars['currencyFormat']->value);?>
';
	var currencyBlank = '<?php echo intval($_smarty_tpl->tpl_vars['currencyBlank']->value);?>
';
	var txtProduct = "<?php echo smartyTranslate(array('s'=>'product','js'=>1),$_smarty_tpl);?>
";
	var txtProducts = "<?php echo smartyTranslate(array('s'=>'products','js'=>1),$_smarty_tpl);?>
";
	<?php }?>
	
	var addressMultishippingUrl = "<?php ob_start();?><?php echo urlencode('&multi-shipping=1');?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo "&mod=";?><?php echo urlencode($_smarty_tpl->tpl_vars['back']->value);?>
<?php }?><?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"back=".((string)$_smarty_tpl->tpl_vars['back_order_page']->value)."?step=1".$_tmp1.$_tmp2);?>
";
	var addressUrl = "<?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo "&mod=";?><?php echo (string)$_smarty_tpl->tpl_vars['back']->value;?><?php }?><?php $_tmp3=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"back=".((string)$_smarty_tpl->tpl_vars['back_order_page']->value)."?step=1".$_tmp3);?>
";

	var formatedAddressFieldsValuesList = new Array();

	<?php  $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['type']->_loop = false;
 $_smarty_tpl->tpl_vars['id_address'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['formatedAddressFieldsValuesList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['type']->key => $_smarty_tpl->tpl_vars['type']->value){
$_smarty_tpl->tpl_vars['type']->_loop = true;
 $_smarty_tpl->tpl_vars['id_address']->value = $_smarty_tpl->tpl_vars['type']->key;
?>
		formatedAddressFieldsValuesList[<?php echo $_smarty_tpl->tpl_vars['id_address']->value;?>
] =
		{
			'ordered_fields':[
				<?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_smarty_tpl->tpl_vars['num_field'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['type']->value['ordered_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['field_name']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value){
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
 $_smarty_tpl->tpl_vars['num_field']->value = $_smarty_tpl->tpl_vars['field_name']->key;
 $_smarty_tpl->tpl_vars['field_name']->index++;
 $_smarty_tpl->tpl_vars['field_name']->first = $_smarty_tpl->tpl_vars['field_name']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['inv_loop']['first'] = $_smarty_tpl->tpl_vars['field_name']->first;
?>
					<?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['inv_loop']['first']){?>,<?php }?>"<?php echo $_smarty_tpl->tpl_vars['field_name']->value;?>
"
				<?php } ?>
			],
			'formated_fields_values':{
					<?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_smarty_tpl->tpl_vars['pattern_name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['type']->value['formated_fields_values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['field_name']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value){
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
 $_smarty_tpl->tpl_vars['pattern_name']->value = $_smarty_tpl->tpl_vars['field_name']->key;
 $_smarty_tpl->tpl_vars['field_name']->index++;
 $_smarty_tpl->tpl_vars['field_name']->first = $_smarty_tpl->tpl_vars['field_name']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['inv_loop']['first'] = $_smarty_tpl->tpl_vars['field_name']->first;
?>
						<?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['inv_loop']['first']){?>,<?php }?>"<?php echo $_smarty_tpl->tpl_vars['pattern_name']->value;?>
":"<?php echo $_smarty_tpl->tpl_vars['field_name']->value;?>
"
					<?php } ?>
				}
		}
	<?php } ?>

	function getAddressesTitles()
	{
		return {
			'invoice': "<span style=\"font-weight:bold;font-size:1.2em;padding-bottom:5px;display:block\"><?php echo smartyTranslate(array('s'=>'Your billing address','js'=>1),$_smarty_tpl);?>
</span>",
			'delivery': "<span style=\"font-weight:bold;font-size:1.2em;padding-bottom:5px;display:block\"><?php echo smartyTranslate(array('s'=>'Your delivery address','js'=>1),$_smarty_tpl);?>
</span>"
		};

	}


	function buildAddressBlock(id_address, address_type, dest_comp)
	{
		var adr_titles_vals = getAddressesTitles();
		var li_content = formatedAddressFieldsValuesList[id_address]['formated_fields_values'];
		var ordered_fields_name = ['title'];

		ordered_fields_name = ordered_fields_name.concat(formatedAddressFieldsValuesList[id_address]['ordered_fields']);
		ordered_fields_name = ordered_fields_name.concat(['update']);

		dest_comp.html('');

		li_content['title'] = adr_titles_vals[address_type];
		li_content['update'] = '<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"id_address");?>
'+id_address+'&amp;back=<?php echo $_smarty_tpl->tpl_vars['back_order_page']->value;?>
?step=1<?php if ($_smarty_tpl->tpl_vars['back']->value){?>&mod=<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
<?php }?>" title="<?php echo smartyTranslate(array('s'=>'Update','js'=>1),$_smarty_tpl);?>
" style="font-size:.9em;font-weight:normal;padding:8px 0;display:block;text-align:right">&raquo; <?php echo smartyTranslate(array('s'=>'Update','js'=>1),$_smarty_tpl);?>
</a>';

		appendAddressList(dest_comp, li_content, ordered_fields_name);
	}

	function appendAddressList(dest_comp, values, fields_name)
	{
		for (var item in fields_name)
		{
			var name = fields_name[item];
			var value = getFieldValue(name, values);
			if (value != "")
			{
				var new_li = document.createElement('li');
				new_li.className = 'address_'+ name;
				new_li.innerHTML = getFieldValue(name, values);
				dest_comp.append(new_li);
			}
		}
	}

	function getFieldValue(field_name, values)
	{
		var reg=new RegExp("[ ]+", "g");

		var items = field_name.split(reg);
		var vals = new Array();

		for (var field_item in items)
		{
			items[field_item] = items[field_item].replace(",", "");
			vals.push(values[items[field_item]]);
		}
		return vals.join(" ");
	}

//]]>
</script>

<?php if (isset($_smarty_tpl->tpl_vars['empty']->value)){?>
<p class="warning"><?php echo smartyTranslate(array('s'=>'Your shopping cart is empty.'),$_smarty_tpl);?>
</p>
<?php }elseif($_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
<p class="warning"><?php echo smartyTranslate(array('s'=>'This store has not accepted your new order.'),$_smarty_tpl);?>
</p>
<?php }else{ ?>
	<div id="displayMobileShoppingCartTop">
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayMobileShoppingCartTop"),$_smarty_tpl);?>

	</div>
	<div data-role="content" id="content" class="cart">
		<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


		<h2><?php echo smartyTranslate(array('s'=>'Addresses'),$_smarty_tpl);?>
</h2>
		<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
			<?php $_smarty_tpl->tpl_vars['current_step'] = new Smarty_variable('address', null, 0);?>
			<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			
			<?php ob_start();?><?php echo Configuration::get('PS_ALLOW_MULTISHIPPING');?>
<?php $_tmp4=ob_get_clean();?><?php if (!$_smarty_tpl->tpl_vars['multi_shipping']->value&&$_tmp4&&!$_smarty_tpl->tpl_vars['cart']->value->isVirtualCart()){?>
				<div class="button_multishipping_mode" id="multishipping_mode_box">
					<div class="title"><?php echo smartyTranslate(array('s'=>'Multi-shipping'),$_smarty_tpl);?>
</div>
					<div class="description">
						<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,'step=1&multi-shipping=1');?>
"/>
							<?php echo smartyTranslate(array('s'=>'Specify a delivery address for each product ordered.'),$_smarty_tpl);?>

						</a>
					</div>
				</div>
			<?php }?>
		<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink($_smarty_tpl->tpl_vars['back_order_page']->value,true);?>
" method="post" data-ajax="false">
		<?php }else{ ?>
			<?php ob_start();?><?php echo Configuration::get('PS_ALLOW_MULTISHIPPING');?>
<?php $_tmp5=ob_get_clean();?><?php if ($_tmp5&&!$_smarty_tpl->tpl_vars['cart']->value->isVirtualCart()){?>
				<div class="address-form-multishipping">
					<div class="button_multishipping_mode" id="multishipping_mode_box">
						<div class="title"><?php echo smartyTranslate(array('s'=>'Multi-shipping'),$_smarty_tpl);?>
</div>
						<div class="description">
							<input type="checkbox" id="multishipping_mode_checkbox" onchange="multishippingMode(this); return false;"/><label for="multishipping_mode_checkbox"><?php echo smartyTranslate(array('s'=>'I\'d like to specify a delivery address for each product ordered.'),$_smarty_tpl);?>
</label>
						</div>
						<div class="description_off">
							<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-opc',true,null,'ajax=1&multi-shipping=1&method=multishipping');?>
" id="link_multishipping_form" title="<?php echo smartyTranslate(array('s'=>'Choose the delivery address(es)'),$_smarty_tpl);?>
">
								<?php echo smartyTranslate(array('s'=>'Specify a delivery address for each product.'),$_smarty_tpl);?>

							</a>
						</div>
					</div>
					<script type="text/javascript">
						<?php if ($_smarty_tpl->tpl_vars['is_multi_address_delivery']->value){?>
						var multishipping_mode = true;
						<?php }else{ ?>
						var multishipping_mode = false;
						<?php }?>
						var open_multishipping_fancybox = <?php echo intval($_smarty_tpl->tpl_vars['open_multishipping_fancybox']->value);?>
;
					</script>
				</div>
			<?php }?>
		<div id="opc_account" class="opc-main-block">
			<div id="opc_account-overlay" class="opc-overlay" style="display: none;"></div>
		<?php }?>
			<div class="addresses clearfix">
				<p class="address_delivery select">
					<label for="id_address_delivery"><?php if ($_smarty_tpl->tpl_vars['cart']->value->isVirtualCart()){?><?php echo smartyTranslate(array('s'=>'Choose a billing address:'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Choose a delivery address:'),$_smarty_tpl);?>
<?php }?></label>
					<select name="id_address_delivery" id="id_address_delivery" class="address_select" onchange="updateAddressesDisplay();<?php if ($_smarty_tpl->tpl_vars['opc']->value){?>updateAddressSelection();<?php }?>">
		
					<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['address']->key;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['address']->value['id_address']);?>
" <?php if ($_smarty_tpl->tpl_vars['address']->value['id_address']==$_smarty_tpl->tpl_vars['cart']->value->id_address_delivery){?>selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value['alias'], 'htmlall', 'UTF-8');?>
</option>
					<?php } ?>
					
					</select>
				</p>
				<p class="checkbox addressesAreEquals" <?php if ($_smarty_tpl->tpl_vars['cart']->value->isVirtualCart()){?>style="display:none;"<?php }?>>
					<input type="checkbox" name="same" id="addressesAreEquals" value="1" onclick="updateAddressesDisplay();<?php if ($_smarty_tpl->tpl_vars['opc']->value){?>updateAddressSelection();<?php }?>" <?php if ($_smarty_tpl->tpl_vars['cart']->value->id_address_invoice==$_smarty_tpl->tpl_vars['cart']->value->id_address_delivery||count($_smarty_tpl->tpl_vars['addresses']->value)==1){?>checked="checked"<?php }?> />
					<label for="addressesAreEquals"><?php echo smartyTranslate(array('s'=>'Use the delivery address as the billing address.'),$_smarty_tpl);?>
</label>
				</p>
		
				<p id="address_invoice_form" class="select" <?php if ($_smarty_tpl->tpl_vars['cart']->value->id_address_invoice==$_smarty_tpl->tpl_vars['cart']->value->id_address_delivery){?>style="display: none;"<?php }?>>
		
				<?php if (count($_smarty_tpl->tpl_vars['addresses']->value)>1){?>
					<label for="id_address_invoice" class="strong"><?php echo smartyTranslate(array('s'=>'Choose a billing address:'),$_smarty_tpl);?>
</label>
					<select name="id_address_invoice" id="id_address_invoice" class="address_select" onchange="updateAddressesDisplay();<?php if ($_smarty_tpl->tpl_vars['opc']->value){?>updateAddressSelection();<?php }?>">
					<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['address'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['address']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['addresses']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['step'] = ((int)-1) == 0 ? 1 : (int)-1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['name'] = 'address';
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['address']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['address']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['address']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['address']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['address']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['address']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['address']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['address']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['address']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['address']['total']);
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['addresses']->value[$_smarty_tpl->getVariable('smarty')->value['section']['address']['index']]['id_address']);?>
" <?php if ($_smarty_tpl->tpl_vars['addresses']->value[$_smarty_tpl->getVariable('smarty')->value['section']['address']['index']]['id_address']==$_smarty_tpl->tpl_vars['cart']->value->id_address_invoice&&$_smarty_tpl->tpl_vars['cart']->value->id_address_delivery!=$_smarty_tpl->tpl_vars['cart']->value->id_address_invoice){?>selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['addresses']->value[$_smarty_tpl->getVariable('smarty')->value['section']['address']['index']]['alias'], 'htmlall', 'UTF-8');?>
</option>
					<?php endfor; endif; ?>
					</select>
					<?php }else{ ?>
						<a style="margin-left: 221px;" href="<?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo "&mod=";?><?php echo (string)$_smarty_tpl->tpl_vars['back']->value;?><?php }?><?php $_tmp6=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"back=".((string)$_smarty_tpl->tpl_vars['back_order_page']->value)."?step=1&select_address=1".$_tmp6);?>
" title="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" class="button_large"><?php echo smartyTranslate(array('s'=>'Add a new address'),$_smarty_tpl);?>
</a>
					<?php }?>
				</p>
			</div>
			<div class="addresses">
				<div class="ui-grid-a same-height">
					<div class="ui-block-a">
						<div class="ui-bar ui-bar-c">
							<ul class="address item" id="address_delivery" <?php if ($_smarty_tpl->tpl_vars['cart']->value->isVirtualCart()){?>style="display:none;margin:0;padding:0;list-style:none;font-size:.9em;font-weight:normal"<?php }else{ ?>style="margin:0;padding:0;list-style:none;font-size:.9em;font-weight:normal"<?php }?>></ul>
						</div>
					</div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-c">
							<ul class="address alternate_item <?php if ($_smarty_tpl->tpl_vars['cart']->value->isVirtualCart()){?>full_width<?php }?>" id="address_invoice" style="margin:0;padding:0;list-style:none;font-size:.9em;font-weight:normal"></ul>
						</div>
					</div>
				</div>
				<p class="address_add submit">
					<a href="<?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo "&mod=";?><?php echo (string)$_smarty_tpl->tpl_vars['back']->value;?><?php }?><?php $_tmp7=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"back=".((string)$_smarty_tpl->tpl_vars['back_order_page']->value)."?step=1".$_tmp7);?>
" title="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" class="button_large" data-role="button" data-icon="plus"><?php echo smartyTranslate(array('s'=>'Add a new address'),$_smarty_tpl);?>
</a>
				</p>
				<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
				<div id="ordermsg" class="clearfix">
					<p class="txt"><?php echo smartyTranslate(array('s'=>'If you would like to add a comment about your order, please write it in the field below.'),$_smarty_tpl);?>
</p>
					<p class="textarea"><textarea cols="60" rows="3" name="message"><?php if (isset($_smarty_tpl->tpl_vars['oldMessage']->value)){?><?php echo $_smarty_tpl->tpl_vars['oldMessage']->value;?>
<?php }?></textarea></p>
				</div>
				<?php }?>
			</div>
		<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
			<fieldset class="cart_navigation submit ui-grid-a">
				<input type="hidden" class="hidden" name="step" value="2" />
				<input type="hidden" name="back" value="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
" />
				<div class="ui-block-a"><a href="<?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo "&back=";?><?php echo (string)$_smarty_tpl->tpl_vars['back']->value;?><?php }?><?php $_tmp8=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink($_smarty_tpl->tpl_vars['back_order_page']->value,true,null,"step=0".$_tmp8);?>
" title="<?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
" data-role="button" data-icon="back" data-ajax="false">&laquo; <?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
</a></div>
				<div class="ui-block-b"><input type="submit" name="processAddress" value="<?php echo smartyTranslate(array('s'=>'Next'),$_smarty_tpl);?>
" class="exclusive" data-icon="check" data-iconpos="right" data-theme="b" /></div>
			</fieldset>
		</form>
		<?php }else{ ?>
		</div>
		<?php }?>
	</div><!-- /content -->
	<div id="displayMobileShoppingCartBottom">
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayMobileShoppingCartBottom"),$_smarty_tpl);?>

	</div>
<?php }?><?php }} ?>