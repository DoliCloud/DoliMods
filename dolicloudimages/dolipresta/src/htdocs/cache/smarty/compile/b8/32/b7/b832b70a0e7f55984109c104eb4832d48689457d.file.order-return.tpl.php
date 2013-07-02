<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:03
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/order-return.tpl" */ ?>
<?php /*%%SmartyHeaderCode:174775303051c1c0e310d614-11010621%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b832b70a0e7f55984109c104eb4832d48689457d' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/order-return.tpl',
      1 => 1371646831,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '174775303051c1c0e310d614-11010621',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'orderRet' => 0,
    'order' => 0,
    'nbdaysreturn' => 0,
    'state_name' => 0,
    'products' => 0,
    'returnedCustomizations' => 0,
    'customization' => 0,
    'product' => 0,
    'productId' => 0,
    'productAttributeId' => 0,
    'customizationId' => 0,
    'customizedDatas' => 0,
    'type' => 0,
    'datas' => 0,
    'pic_dir' => 0,
    'data' => 0,
    'customizationFieldName' => 0,
    'quantityDisplayed' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e334f9b2_88059006',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e334f9b2_88059006')) {function content_51c1c0e334f9b2_88059006($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_function_counter')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/function.counter.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php if (isset($_smarty_tpl->tpl_vars['orderRet']->value)){?>
	<p class="title_block"><?php echo smartyTranslate(array('s'=>'RE#'),$_smarty_tpl);?>
<span class="color-myaccount"><?php echo sprintf("%06d",$_smarty_tpl->tpl_vars['orderRet']->value->id);?>
</span> <?php echo smartyTranslate(array('s'=>'on'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['order']->value->date_add,'full'=>0),$_smarty_tpl);?>
</p>
	<div>
		<p class="bold"><?php echo smartyTranslate(array('s'=>'We have logged your return request.'),$_smarty_tpl);?>
</p>
		<p><?php echo smartyTranslate(array('s'=>'Your package must be returned to us within'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['nbdaysreturn']->value;?>
 <?php echo smartyTranslate(array('s'=>'days of receiving your order.'),$_smarty_tpl);?>
</p>
		<p><?php echo smartyTranslate(array('s'=>'The current status of your merchandise return is:'),$_smarty_tpl);?>
 <span class="bold"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['state_name']->value, 'htmlall', 'UTF-8');?>
</span></p>
		<p><?php echo smartyTranslate(array('s'=>'List of items to be returned:'),$_smarty_tpl);?>
</p>
	</div>
	<div id="order-detail-content" class="table_block">
		<table class="std">
			<thead>
				<tr>
					<th class="first_item"><?php echo smartyTranslate(array('s'=>'Reference'),$_smarty_tpl);?>
</th>
					<th class="item"><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</th>
					<th class="last_item"><?php echo smartyTranslate(array('s'=>'Quantity'),$_smarty_tpl);?>
</th>
				</tr>
			</thead>
			<tbody>
			<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['product']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['product']->index++;
 $_smarty_tpl->tpl_vars['product']->first = $_smarty_tpl->tpl_vars['product']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['first'] = $_smarty_tpl->tpl_vars['product']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']++;
?>

				<?php $_smarty_tpl->tpl_vars['quantityDisplayed'] = new Smarty_variable(0, null, 0);?>
				<?php  $_smarty_tpl->tpl_vars['customization'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customization']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['returnedCustomizations']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['customization']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['customization']->key => $_smarty_tpl->tpl_vars['customization']->value){
$_smarty_tpl->tpl_vars['customization']->_loop = true;
 $_smarty_tpl->tpl_vars['customization']->index++;
 $_smarty_tpl->tpl_vars['customization']->first = $_smarty_tpl->tpl_vars['customization']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['first'] = $_smarty_tpl->tpl_vars['customization']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']++;
?>
					<?php if ($_smarty_tpl->tpl_vars['customization']->value['product_id']==$_smarty_tpl->tpl_vars['product']->value['product_id']){?>
						<tr class="<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['products']['first']){?>first_item<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['products']['index']%2){?>alternate_item<?php }else{ ?>item<?php }?>">
							<td><?php if ($_smarty_tpl->tpl_vars['customization']->value['reference']){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['customization']->value['reference'], 'htmlall', 'UTF-8');?>
<?php }else{ ?>--<?php }?></td>
							<td class="bold"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['customization']->value['name'], 'htmlall', 'UTF-8');?>
</td>
							<td><span class="order_qte_span editable"><?php echo intval($_smarty_tpl->tpl_vars['customization']->value['product_quantity']);?>
</span></td>
						</tr>
						<?php $_smarty_tpl->tpl_vars['productId'] = new Smarty_variable($_smarty_tpl->tpl_vars['customization']->value['product_id'], null, 0);?>
						<?php $_smarty_tpl->tpl_vars['productAttributeId'] = new Smarty_variable($_smarty_tpl->tpl_vars['customization']->value['product_attribute_id'], null, 0);?>
						<?php $_smarty_tpl->tpl_vars['customizationId'] = new Smarty_variable($_smarty_tpl->tpl_vars['customization']->value['id_customization'], null, 0);?>
						<?php  $_smarty_tpl->tpl_vars['datas'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['datas']->_loop = false;
 $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value][$_smarty_tpl->tpl_vars['customizationId']->value]['datas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['datas']->key => $_smarty_tpl->tpl_vars['datas']->value){
$_smarty_tpl->tpl_vars['datas']->_loop = true;
 $_smarty_tpl->tpl_vars['type']->value = $_smarty_tpl->tpl_vars['datas']->key;
?>
							<tr class="alternate_item">
								<td colspan="3">
									<?php if ($_smarty_tpl->tpl_vars['type']->value==@constant('_CUSTOMIZE_FILE_')){?>
									<ul class="customizationUploaded">
										<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
											<li><img src="<?php echo $_smarty_tpl->tpl_vars['pic_dir']->value;?>
<?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
_small" alt="" class="customizationUploaded" /></li>
										<?php } ?>
									</ul>
									<?php }elseif($_smarty_tpl->tpl_vars['type']->value==@constant('_CUSTOMIZE_TEXTFIELD_')){?>
									<ul class="typedText"><?php echo smarty_function_counter(array('start'=>0,'print'=>false),$_smarty_tpl);?>

										<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value){
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
											<?php $_smarty_tpl->tpl_vars['customizationFieldName'] = new Smarty_variable(("Text #").($_smarty_tpl->tpl_vars['data']->value['id_customization_field']), null, 0);?>
											<li><?php echo smartyTranslate(array('s'=>'%s:','sprintf'=>(($tmp = @$_smarty_tpl->tpl_vars['data']->value['name'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['customizationFieldName']->value : $tmp)),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
</li>
										<?php } ?>
									</ul>
									<?php }?>
								</td>
							</tr>
						<?php } ?>
						<?php $_smarty_tpl->tpl_vars['quantityDisplayed'] = new Smarty_variable($_smarty_tpl->tpl_vars['quantityDisplayed']->value+$_smarty_tpl->tpl_vars['customization']->value['product_quantity'], null, 0);?>
					<?php }?>
				<?php } ?>

				<?php if ($_smarty_tpl->tpl_vars['product']->value['product_quantity']>$_smarty_tpl->tpl_vars['quantityDisplayed']->value){?>
					<tr class="<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['products']['first']){?>first_item<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['products']['index']%2){?>alternate_item<?php }else{ ?>item<?php }?>">
						<td><?php if ($_smarty_tpl->tpl_vars['product']->value['product_reference']){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['product_reference'], 'htmlall', 'UTF-8');?>
<?php }else{ ?>--<?php }?></td>
						<td class="bold"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['product_name'], 'htmlall', 'UTF-8');?>
</td>
						<td><span class="order_qte_span editable"><?php echo intval($_smarty_tpl->tpl_vars['product']->value['product_quantity']);?>
</span></td>
					</tr>
				<?php }?>
			<?php } ?>
			</tbody>
		</table>
	</div>

	<?php if ($_smarty_tpl->tpl_vars['orderRet']->value->state==2){?>
	<p class="bold"><?php echo smartyTranslate(array('s'=>'Reminder:'),$_smarty_tpl);?>
</p>
	<div>
		- <?php echo smartyTranslate(array('s'=>'All merchandise must be returned in its original packaging and in its original state.'),$_smarty_tpl);?>

		<br />- <?php echo smartyTranslate(array('s'=>'Please print out the'),$_smarty_tpl);?>
 <a href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['orderRet']->value->id);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('pdf-order-return',true,null,"id_order_return=".$_tmp1);?>
"><?php echo smartyTranslate(array('s'=>'PDF return slip'),$_smarty_tpl);?>
</a> <?php echo smartyTranslate(array('s'=>'and include it with your package.'),$_smarty_tpl);?>

		<br />- <?php echo smartyTranslate(array('s'=>'Please see the PDF return slip'),$_smarty_tpl);?>
 (<a href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['orderRet']->value->id);?>
<?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('pdf-order-return',true,null,"id_order_return=".$_tmp2);?>
"><?php echo smartyTranslate(array('s'=>'for the correct address'),$_smarty_tpl);?>
</a>)
		<br /><br />
		<?php echo smartyTranslate(array('s'=>'When we receive your package, we will notify you by email. We will then begin processing order reimbursement.'),$_smarty_tpl);?>

		<br /><br /><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true);?>
"><?php echo smartyTranslate(array('s'=>'Please let us know if you have any questions.'),$_smarty_tpl);?>
</a>
		<br />
		<p class="bold"><?php echo smartyTranslate(array('s'=>'If the conditions of return listed above are not respected, we reserve the right to refuse your package and/or reimbursement.'),$_smarty_tpl);?>
</p>
	</div>
	<?php }elseif($_smarty_tpl->tpl_vars['orderRet']->value->state==1){?>
		<p class="bold"><?php echo smartyTranslate(array('s'=>'You must wait for confirmation before returning any merchandise.'),$_smarty_tpl);?>
</p>
	<?php }?>
<?php }?>

<?php }} ?>