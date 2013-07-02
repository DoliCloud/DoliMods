<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:00
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/order-address-multishipping.tpl" */ ?>
<?php /*%%SmartyHeaderCode:102631874551c1c0e01059a0-44619379%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c29a3891d81456a8a4023d1c06b8b0538d8d5a21' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/order-address-multishipping.tpl',
      1 => 1371646829,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '102631874551c1c0e01059a0-44619379',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'opc' => 0,
    'formatedAddressFieldsValuesList' => 0,
    'addresses' => 0,
    'address' => 0,
    'address_key' => 0,
    'ignoreList' => 0,
    'id_address' => 0,
    'address_key_number' => 0,
    'address_content' => 0,
    'currencySign' => 0,
    'currencyRate' => 0,
    'currencyFormat' => 0,
    'currencyBlank' => 0,
    'type' => 0,
    'field_name' => 0,
    'pattern_name' => 0,
    'link' => 0,
    'back' => 0,
    'cart' => 0,
    'back_order_page' => 0,
    'oldMessage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e0456231_30223051',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e0456231_30223051')) {function content_51c1c0e0456231_30223051($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/function.counter.php';
if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php if ($_smarty_tpl->tpl_vars['opc']->value){?>
	<?php $_smarty_tpl->tpl_vars["back_order_page"] = new Smarty_variable("order-opc.php", null, 0);?>
<?php }else{ ?>
	<?php $_smarty_tpl->tpl_vars["back_order_page"] = new Smarty_variable("order.php", null, 0);?>
<?php }?>


<?php if (!isset($_smarty_tpl->tpl_vars['formatedAddressFieldsValuesList']->value)){?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[0] = "id_address";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[1] = "id_country";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[2] = "id_state";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[3] = "id_customer";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[4] = "id_manufacturer";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[5] = "id_supplier";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[6] = "date_add";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[7] = "date_upd";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[8] = "active";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[9] = "deleted";?>	
	
	
	<?php if (isset($_smarty_tpl->tpl_vars['addresses']->value)){?>
		<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['address']->key;
?>
			<?php echo smarty_function_counter(array('start'=>0,'skip'=>1,'assign'=>'address_key_number'),$_smarty_tpl);?>

			<?php $_smarty_tpl->tpl_vars['id_address'] = new Smarty_variable($_smarty_tpl->tpl_vars['address']->value['id_address'], null, 0);?>
			<?php  $_smarty_tpl->tpl_vars['address_content'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address_content']->_loop = false;
 $_smarty_tpl->tpl_vars['address_key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['address']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address_content']->key => $_smarty_tpl->tpl_vars['address_content']->value){
$_smarty_tpl->tpl_vars['address_content']->_loop = true;
 $_smarty_tpl->tpl_vars['address_key']->value = $_smarty_tpl->tpl_vars['address_content']->key;
?>
				<?php if (!in_array($_smarty_tpl->tpl_vars['address_key']->value,$_smarty_tpl->tpl_vars['ignoreList']->value)){?>
					<?php $_smarty_tpl->createLocalArrayVariable('formatedAddressFieldsValuesList', null, 0);
$_smarty_tpl->tpl_vars['formatedAddressFieldsValuesList']->value[$_smarty_tpl->tpl_vars['id_address']->value]['ordered_fields'][$_smarty_tpl->tpl_vars['address_key_number']->value] = $_smarty_tpl->tpl_vars['address_key']->value;?>
					<?php $_smarty_tpl->createLocalArrayVariable('formatedAddressFieldsValuesList', null, 0);
$_smarty_tpl->tpl_vars['formatedAddressFieldsValuesList']->value[$_smarty_tpl->tpl_vars['id_address']->value]['formated_fields_values'][$_smarty_tpl->tpl_vars['address_key']->value] = $_smarty_tpl->tpl_vars['address_content']->value;?>
					<?php echo smarty_function_counter(array(),$_smarty_tpl);?>

				<?php }?>
			<?php } ?>
		<?php } ?>
	<?php }?>
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
	var txtProduct = "<?php echo smartyTranslate(array('s'=>'Product','js'=>1),$_smarty_tpl);?>
";
	var txtProducts = "<?php echo smartyTranslate(array('s'=>'Products','js'=>1),$_smarty_tpl);?>
";
	var txtSelectAnAddressFirst = "<?php echo smartyTranslate(array('s'=>'Please start by selecting an address','js'=>1),$_smarty_tpl);?>
";
	<?php }?>

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
			'invoice': "<?php echo smartyTranslate(array('s'=>'Your billing address'),$_smarty_tpl);?>
",
			'delivery': "<?php echo smartyTranslate(array('s'=>'Your delivery address'),$_smarty_tpl);?>
"
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
'+id_address+'&amp;back=order?step=1<?php if ($_smarty_tpl->tpl_vars['back']->value){?>&mod=<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
<?php }?>" title="<?php echo smartyTranslate(array('s'=>'Update','js'=>1),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Update','js'=>1),$_smarty_tpl);?>
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
			vals.push(values[items[field_item]]);
		return vals.join(" ");
	}

//]]>
</script>

<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Addresses'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?>

<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?><h1><?php echo smartyTranslate(array('s'=>'Addresses'),$_smarty_tpl);?>
</h1><?php }else{ ?><h2><span>1</span> <?php echo smartyTranslate(array('s'=>'Addresses'),$_smarty_tpl);?>
</h2><?php }?>

<?php if (!$_smarty_tpl->tpl_vars['opc']->value){?>
<?php $_smarty_tpl->tpl_vars['current_step'] = new Smarty_variable('address', null, 0);?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./order-steps.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./order-address-multishipping-products.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,'multi-shipping=1');?>
" method="post">
<?php }else{ ?>
<div id="opc_account" class="opc-main-block">
	<div id="opc_account-overlay" class="opc-overlay" style="display: none;"></div>
<?php }?>
	<div class="addresses clearfix">
		<input type="hidden" name="id_address_delivery" id="id_address_delivery" value="<?php echo $_smarty_tpl->tpl_vars['cart']->value->id_address_delivery;?>
" onchange="updateAddressesDisplay();<?php if ($_smarty_tpl->tpl_vars['opc']->value){?>updateAddressSelection();<?php }?>" />
		<p id="address_invoice_form" class="select" <?php if ($_smarty_tpl->tpl_vars['cart']->value->id_address_invoice==$_smarty_tpl->tpl_vars['cart']->value->id_address_delivery){?>style="display: none;"<?php }?>>
		
		<?php if (count($_smarty_tpl->tpl_vars['addresses']->value)>=1){?>
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
			<a href="<?php ob_start();?><?php echo urlencode('&multi-shipping=1');?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo "&mod=";?><?php echo (string)$_smarty_tpl->tpl_vars['back']->value;?><?php }?><?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"back=".((string)$_smarty_tpl->tpl_vars['back_order_page']->value)."?step=1".$_tmp1.$_tmp2);?>
" title="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" class="button_large"><?php echo smartyTranslate(array('s'=>'Add a new address'),$_smarty_tpl);?>
</a>
		<?php }?>
		</p>
		<div class="clearfix">
			<ul class="address alternate_item <?php if ($_smarty_tpl->tpl_vars['cart']->value->isVirtualCart()){?>full_width<?php }?>" id="address_invoice">
			</ul>
		</div>
		<p class="address_add submit">
			<a href="<?php ob_start();?><?php echo urlencode('&multi-shipping=1');?>
<?php $_tmp3=ob_get_clean();?><?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['back']->value){?><?php echo "&mod=";?><?php echo urlencode($_smarty_tpl->tpl_vars['back']->value);?>
<?php }?><?php $_tmp4=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"back=".((string)$_smarty_tpl->tpl_vars['back_order_page']->value)."?step=1".$_tmp3.$_tmp4);?>
" title="<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
" class="button_large">&raquo; <?php echo smartyTranslate(array('s'=>'Add a new address'),$_smarty_tpl);?>
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
	<p class="cart_navigation submit">
		<input type="hidden" class="hidden" name="step" value="2" />
		<input type="hidden" name="back" value="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
" />
		<?php if ($_smarty_tpl->tpl_vars['back']->value){?>
			<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,"step=0&amp;back=".((string)$_smarty_tpl->tpl_vars['back']->value));?>
" title="<?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
" class="button">&laquo; <?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
</a>
		<?php }else{ ?>
			<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,"step=0");?>
" title="<?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
" class="button">&laquo; <?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
</a>
		<?php }?>
		<input type="submit" name="processAddress" value="<?php echo smartyTranslate(array('s'=>'Next'),$_smarty_tpl);?>
 &raquo;" class="exclusive" />
	</p>
</form>
<?php }else{ ?>
</div>
<?php }?>
<?php }} ?>