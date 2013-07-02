<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:10
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-detail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:114158528351c1c0ea0e29c3-97012705%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cac41f26dca2557c2bb140e40170ee11e920fa49' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-detail.tpl',
      1 => 1371647171,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '114158528351c1c0ea0e29c3-97012705',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'order' => 0,
    'opc' => 0,
    'type_order' => 0,
    'link' => 0,
    'carrier' => 0,
    'shop_name' => 0,
    'invoice' => 0,
    'invoiceAllowed' => 0,
    'img_dir' => 0,
    'is_guest' => 0,
    'order_history' => 0,
    'state' => 0,
    'followup' => 0,
    'isRecyclable' => 0,
    'inv_adr_fields' => 0,
    'field_item' => 0,
    'address_invoice' => 0,
    'address_words' => 0,
    'word_item' => 0,
    'invoiceAddressFormatedValues' => 0,
    'dlv_adr_fields' => 0,
    'address_delivery' => 0,
    'deliveryAddressFormatedValues' => 0,
    'return_allowed' => 0,
    'products' => 0,
    'product' => 0,
    'discounts' => 0,
    'discount' => 0,
    'currency' => 0,
    'priceDisplay' => 0,
    'use_tax' => 0,
    'line' => 0,
    'messages' => 0,
    'message' => 0,
    'errors' => 0,
    'error' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ea7cdc16_58295550',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ea7cdc16_58295550')) {function content_51c1c0ea7cdc16_58295550($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_modifier_replace')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.replace.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'page_title', null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Order'),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'#'),$_smarty_tpl);?>
<?php echo sprintf("%06d",$_smarty_tpl->tpl_vars['order']->value->id);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate ('./page-title.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div class="ui-grid-a">
	<div class="ui-block-a">
		<a data-role="button" data-icon="arrow-l" data-theme="a" data-rel="back" href="#" title="" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Back'),$_smarty_tpl);?>
</a>
	</div>
	<div class="ui-block-b">
		<?php $_smarty_tpl->tpl_vars['type_order'] = new Smarty_variable("order", null, 0);?>
		<?php if (isset($_smarty_tpl->tpl_vars['opc']->value)&&$_smarty_tpl->tpl_vars['opc']->value){?>
			<?php $_smarty_tpl->tpl_vars['type_order'] = new Smarty_variable("order-opc", null, 0);?>
		<?php }?>
		<a data-icon="refresh" data-role="button" data-theme="e" href="<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['type_order']->value;?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
<?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink($_tmp1,true,null,"submitReorder&id_order=".$_tmp2);?>
" title="<?php echo smartyTranslate(array('s'=>'Reorder'),$_smarty_tpl);?>
" data-ajax="false">
		<?php echo smartyTranslate(array('s'=>'Reorder'),$_smarty_tpl);?>

		</a>
	</div>
</div><!-- .ui-grid-a -->

<div data-role="content" id="content">
	<h3 class="bg"><?php echo smartyTranslate(array('s'=>'Order #%s - placed on','sprintf'=>sprintf("%06d",$_smarty_tpl->tpl_vars['order']->value->id)),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['order']->value->date_add,'full'=>0),$_smarty_tpl);?>
</h3>


<ul class="info-order" data-role="listview">
	<?php if ($_smarty_tpl->tpl_vars['carrier']->value->id){?><li><strong><?php echo smartyTranslate(array('s'=>'Carrier'),$_smarty_tpl);?>
</strong> <?php if ($_smarty_tpl->tpl_vars['carrier']->value->name=="0"){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['shop_name']->value, 'htmlall', 'UTF-8');?>
<?php }else{ ?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['carrier']->value->name, 'htmlall', 'UTF-8');?>
<?php }?></li><?php }?>
	<li><strong><?php echo smartyTranslate(array('s'=>'Payment method'),$_smarty_tpl);?>
</strong> <span class="color-myaccount"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['order']->value->payment, 'htmlall', 'UTF-8');?>
</span></li>
	<?php if ($_smarty_tpl->tpl_vars['invoice']->value&&$_smarty_tpl->tpl_vars['invoiceAllowed']->value){?>
	<li>
		<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/pdf.gif" alt="" class="icon" />
		<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('pdf-invoice',true);?>
?id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
<?php if ($_smarty_tpl->tpl_vars['is_guest']->value){?>&secure_key=<?php echo $_smarty_tpl->tpl_vars['order']->value->secure_key;?>
<?php }?>" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Download your invoice as a PDF file.'),$_smarty_tpl);?>
</li>
	</li>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['order']->value->recyclable){?>
	<li><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/recyclable.gif" alt="" class="icon" />&nbsp;<?php echo smartyTranslate(array('s'=>'You have given permission to receive your order in recycled packaging.'),$_smarty_tpl);?>
</li>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['order']->value->gift){?>
		<li><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/gift.gif" alt="" class="icon" />&nbsp;<?php echo smartyTranslate(array('s'=>'You have requested gift wrapping for this order.'),$_smarty_tpl);?>
</li>
		<li><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
 <?php echo nl2br($_smarty_tpl->tpl_vars['order']->value->gift_message);?>
</li>
	<?php }?>
</ul><!-- .info-order -->

<?php if (count($_smarty_tpl->tpl_vars['order_history']->value)){?>
<h3 class="bg"><?php echo smartyTranslate(array('s'=>'Follow your order\'s status step-by-step'),$_smarty_tpl);?>
</h3>
<ul data-role="listview" >
	<?php  $_smarty_tpl->tpl_vars['state'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['state']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order_history']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['state']->key => $_smarty_tpl->tpl_vars['state']->value){
$_smarty_tpl->tpl_vars['state']->_loop = true;
?>
	<li>
		<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['state']->value['ostate_name'], 'htmlall', 'UTF-8');?>

		<span class="ui-li-aside"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['state']->value['date_add'],'full'=>1),$_smarty_tpl);?>
</span>
	</li>
	<?php } ?>
</ul>
<?php }?>



<?php if (isset($_smarty_tpl->tpl_vars['followup']->value)){?>
<p class="bold"><?php echo smartyTranslate(array('s'=>'Click the following link to track the delivery of your order'),$_smarty_tpl);?>
</p>
<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['followup']->value, 'htmlall', 'UTF-8');?>
" data-ajax="false"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['followup']->value, 'htmlall', 'UTF-8');?>
</a>
<?php }?>


<h3 class="bg"><?php echo smartyTranslate(array('s'=>'Addresses'),$_smarty_tpl);?>
</h3>
<div class="adresses_bloc clearfix">


<?php if ($_smarty_tpl->tpl_vars['invoice']->value&&$_smarty_tpl->tpl_vars['invoiceAllowed']->value){?>
<p>
	<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/pdf.gif" alt="" class="icon" />
	<?php if ($_smarty_tpl->tpl_vars['is_guest']->value){?>
		<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('pdf-invoice',true,null,"id_order=".((string)$_smarty_tpl->tpl_vars['order']->value->id)."&amp;secure_key=".((string)$_smarty_tpl->tpl_vars['order']->value)."->secure_key");?>
" ><?php echo smartyTranslate(array('s'=>'Download your invoice as a PDF file.'),$_smarty_tpl);?>
</a>
	<?php }else{ ?>
		<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('pdf-invoice',true,null,"id_order=".((string)$_smarty_tpl->tpl_vars['order']->value->id));?>
" ><?php echo smartyTranslate(array('s'=>'Download your invoice as a PDF file.'),$_smarty_tpl);?>
</a>
	<?php }?>
</p>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['order']->value->recyclable&&isset($_smarty_tpl->tpl_vars['isRecyclable']->value)&&$_smarty_tpl->tpl_vars['isRecyclable']->value){?>
<p><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/recyclable.gif" alt="" class="icon" />&nbsp;<?php echo smartyTranslate(array('s'=>'You have given permission to receive your order in recycled packaging.'),$_smarty_tpl);?>
</p>
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['order']->value->gift){?>
	<p><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/gift.gif" alt="" class="icon" />&nbsp;<?php echo smartyTranslate(array('s'=>'You have requested gift wrapping for this order.'),$_smarty_tpl);?>
</p>
	<p><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
 <?php echo nl2br($_smarty_tpl->tpl_vars['order']->value->gift_message);?>
</p>
<?php }?>


<ul data-role="listview" data-inset="true" data-dividertheme="c">
	<?php if (!$_smarty_tpl->tpl_vars['order']->value->isVirtual()){?>
	<li data-role="list-divider"><?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>
</li>
	<li>
	<?php  $_smarty_tpl->tpl_vars['field_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['inv_adr_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_item']->key => $_smarty_tpl->tpl_vars['field_item']->value){
$_smarty_tpl->tpl_vars['field_item']->_loop = true;
?>
		<?php if ($_smarty_tpl->tpl_vars['field_item']->value=="company"&&isset($_smarty_tpl->tpl_vars['address_invoice']->value->company)){?><p class="address_company"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address_invoice']->value->company, 'htmlall', 'UTF-8');?>
</p>
		<?php }elseif($_smarty_tpl->tpl_vars['field_item']->value=="address2"&&$_smarty_tpl->tpl_vars['address_invoice']->value->address2){?><p class="address_address2"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address_invoice']->value->address2, 'htmlall', 'UTF-8');?>
</p>
		<?php }elseif($_smarty_tpl->tpl_vars['field_item']->value=="phone_mobile"&&$_smarty_tpl->tpl_vars['address_invoice']->value->phone_mobile){?><p class="address_phone_mobile"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address_invoice']->value->phone_mobile, 'htmlall', 'UTF-8');?>
</p>
		<?php }else{ ?>
				<?php $_smarty_tpl->tpl_vars['address_words'] = new Smarty_variable(explode(" ",$_smarty_tpl->tpl_vars['field_item']->value), null, 0);?>
				<p><?php  $_smarty_tpl->tpl_vars['word_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['word_item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['address_words']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['word_item']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['word_item']->key => $_smarty_tpl->tpl_vars['word_item']->value){
$_smarty_tpl->tpl_vars['word_item']->_loop = true;
 $_smarty_tpl->tpl_vars['word_item']->index++;
 $_smarty_tpl->tpl_vars['word_item']->first = $_smarty_tpl->tpl_vars['word_item']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["word_loop"]['first'] = $_smarty_tpl->tpl_vars['word_item']->first;
?><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['word_loop']['first']){?> <?php }?><span class="address_<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['word_item']->value,',','');?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['invoiceAddressFormatedValues']->value[smarty_modifier_replace($_smarty_tpl->tpl_vars['word_item']->value,',','')], 'htmlall', 'UTF-8');?>
</span><?php } ?></p>
		<?php }?>
	<?php } ?>
	</li>
	<?php }?>
	<li data-role="list-divider" ><?php echo smartyTranslate(array('s'=>'Delivery'),$_smarty_tpl);?>
</li>
	<li>
	<?php  $_smarty_tpl->tpl_vars['field_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dlv_adr_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_item']->key => $_smarty_tpl->tpl_vars['field_item']->value){
$_smarty_tpl->tpl_vars['field_item']->_loop = true;
?>
		<?php if ($_smarty_tpl->tpl_vars['field_item']->value=="company"&&isset($_smarty_tpl->tpl_vars['address_delivery']->value->company)){?><p class="address_company"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address_delivery']->value->company, 'htmlall', 'UTF-8');?>
</p>
		<?php }elseif($_smarty_tpl->tpl_vars['field_item']->value=="address2"&&$_smarty_tpl->tpl_vars['address_delivery']->value->address2){?><p class="address_address2"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address_delivery']->value->address2, 'htmlall', 'UTF-8');?>
</p>
		<?php }elseif($_smarty_tpl->tpl_vars['field_item']->value=="phone_mobile"&&$_smarty_tpl->tpl_vars['address_delivery']->value->phone_mobile){?><p class="address_phone_mobile"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address_delivery']->value->phone_mobile, 'htmlall', 'UTF-8');?>
</p>
		<?php }else{ ?>
				<?php $_smarty_tpl->tpl_vars['address_words'] = new Smarty_variable(explode(" ",$_smarty_tpl->tpl_vars['field_item']->value), null, 0);?> 
				<p><?php  $_smarty_tpl->tpl_vars['word_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['word_item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['address_words']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['word_item']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['word_item']->key => $_smarty_tpl->tpl_vars['word_item']->value){
$_smarty_tpl->tpl_vars['word_item']->_loop = true;
 $_smarty_tpl->tpl_vars['word_item']->index++;
 $_smarty_tpl->tpl_vars['word_item']->first = $_smarty_tpl->tpl_vars['word_item']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["word_loop"]['first'] = $_smarty_tpl->tpl_vars['word_item']->first;
?><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['word_loop']['first']){?> <?php }?><span class="address_<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['word_item']->value,',','');?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['deliveryAddressFormatedValues']->value[smarty_modifier_replace($_smarty_tpl->tpl_vars['word_item']->value,',','')], 'htmlall', 'UTF-8');?>
</span><?php } ?></p>
		<?php }?>
	<?php } ?>
	</li>
</ul>
</div><!-- .adresses_bloc -->

<!-- order details -->
<h3 class="bg"><?php echo smartyTranslate(array('s'=>'Order details'),$_smarty_tpl);?>
</h3>



<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value){?><p><?php echo smartyTranslate(array('s'=>'If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.'),$_smarty_tpl);?>
</p><?php }?>
<?php if (!$_smarty_tpl->tpl_vars['is_guest']->value){?><form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-follow',true);?>
" method="post"><?php }?>
<ul data-role="listview" data-inset="true">
<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
	<?php if (!isset($_smarty_tpl->tpl_vars['product']->value['deleted'])){?>
		<?php $_smarty_tpl->tpl_vars['productId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_id'], null, 0);?>
		<?php $_smarty_tpl->tpl_vars['productAttributeId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_attribute_id'], null, 0);?>
		<?php if (isset($_smarty_tpl->tpl_vars['product']->value['customizedDatas'])){?>
			<?php $_smarty_tpl->tpl_vars['productQuantity'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'], null, 0);?>
		<?php }else{ ?>
			<?php $_smarty_tpl->tpl_vars['productQuantity'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_quantity'], null, 0);?>
		<?php }?>
		<?php echo $_smarty_tpl->getSubTemplate ("./order-detail-product-li.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<?php }?>
<?php } ?>

<?php  $_smarty_tpl->tpl_vars['discount'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['discount']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['discounts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['discount']->key => $_smarty_tpl->tpl_vars['discount']->value){
$_smarty_tpl->tpl_vars['discount']->_loop = true;
?>
	<li class="item">
		<h3><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['discount']->value['name'], 'htmlall', 'UTF-8');?>
</h3>
		<p><?php echo smartyTranslate(array('s'=>'Voucher'),$_smarty_tpl);?>
 <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['discount']->value['name'], 'htmlall', 'UTF-8');?>
</p>
		<p><span class="order_qte_span editable">1</span></p>
		<p>&nbsp;</p>
		<p><?php if ($_smarty_tpl->tpl_vars['discount']->value['value']!=0.00){?><?php echo smartyTranslate(array('s'=>'-'),$_smarty_tpl);?>
<?php }?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['discount']->value['value'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</p>
		<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value){?>
		<p>&nbsp;</p>
		<?php }?>
	</li>
<?php } ?>

	<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value&&$_smarty_tpl->tpl_vars['use_tax']->value){?>
		<li data-theme="b" class="item">
			<?php echo smartyTranslate(array('s'=>'Total products (tax excl.)'),$_smarty_tpl);?>
 <span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->getTotalProductsWithoutTaxes(),'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
		</li>
	<?php }?>
	<li data-theme="b" class="item">
		<?php echo smartyTranslate(array('s'=>'Total products'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['use_tax']->value){?><?php echo smartyTranslate(array('s'=>'(tax incl.)'),$_smarty_tpl);?>
<?php }?>: <span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->getTotalProductsWithTaxes(),'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
	</li>
	<?php if ($_smarty_tpl->tpl_vars['order']->value->total_discounts>0){?>
	<li data-theme="b" class="item">
		<?php echo smartyTranslate(array('s'=>'Total vouchers:'),$_smarty_tpl);?>
 <span class="price-discount"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_discounts,'currency'=>$_smarty_tpl->tpl_vars['currency']->value,'convert'=>1),$_smarty_tpl);?>
</span>
	</li>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['order']->value->total_wrapping>0){?>
	<li data-theme="b" class="item">
		<?php echo smartyTranslate(array('s'=>'Total gift wrapping cost:'),$_smarty_tpl);?>
 <span class="price-wrapping"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_wrapping,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
	</li>
	<?php }?>
	<li data-theme="b" class="item">
		<?php echo smartyTranslate(array('s'=>'Total shipping'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['use_tax']->value){?><?php echo smartyTranslate(array('s'=>'(tax incl.)'),$_smarty_tpl);?>
<?php }?>: <span class="price-shipping"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_shipping,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
	</li>
	<li data-theme="a" class="totalprice item">
		<?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
 <span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_paid,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
	</li>
</ul>
<!-- /order details -->

<?php if (count($_smarty_tpl->tpl_vars['order']->value->getShipping())>0){?>
<h3 class="bg"><?php echo smartyTranslate(array('s'=>'Carrier'),$_smarty_tpl);?>
</h3>
<ul data-role="listview" >
	<?php  $_smarty_tpl->tpl_vars['line'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['line']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getShipping(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['line']->key => $_smarty_tpl->tpl_vars['line']->value){
$_smarty_tpl->tpl_vars['line']->_loop = true;
?>
	<li>
		<h3><?php echo $_smarty_tpl->tpl_vars['line']->value['carrier_name'];?>
</h3>
		<p><strong><?php echo smartyTranslate(array('s'=>'Weight'),$_smarty_tpl);?>
</strong> <?php echo sprintf("%.3f",$_smarty_tpl->tpl_vars['line']->value['weight']);?>
 <?php echo Configuration::get('PS_WEIGHT_UNIT');?>
</p>
		<p><strong><?php echo smartyTranslate(array('s'=>'Shipping cost'),$_smarty_tpl);?>
</strong> <?php if ($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_INC')){?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['line']->value['shipping_cost_tax_incl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
<?php }else{ ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['line']->value['shipping_cost_tax_excl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
<?php }?></p>
		<p><strong><?php echo smartyTranslate(array('s'=>'Tracking number'),$_smarty_tpl);?>
</strong> <?php if ($_smarty_tpl->tpl_vars['line']->value['url']&&$_smarty_tpl->tpl_vars['line']->value['tracking_number']){?><a href="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['line']->value['url'],'@',$_smarty_tpl->tpl_vars['line']->value['tracking_number']);?>
" data-ajax="false"><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
</a><?php }elseif($_smarty_tpl->tpl_vars['line']->value['tracking_number']!=''){?><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
<?php }else{ ?>----<?php }?></p>
		<span class="ui-li-aside"><?php echo $_smarty_tpl->tpl_vars['line']->value['date_add'];?>
</span>
	</li>
	<?php } ?>
</ul>
<?php }?>


<?php if (!$_smarty_tpl->tpl_vars['is_guest']->value){?>
	<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value){?>
	<div id="returnOrderMessage">
		<h3><?php echo smartyTranslate(array('s'=>'Merchandise return'),$_smarty_tpl);?>
</h3>
		<p><?php echo smartyTranslate(array('s'=>'If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.'),$_smarty_tpl);?>
</p>
		<fieldset>
			<textarea cols="67" rows="3" name="returnText"></textarea>
		</fieldset>
		<fieldset>
			<input type="submit" data-theme="a" value="<?php echo smartyTranslate(array('s'=>'Make an RMA slip'),$_smarty_tpl);?>
" name="submitReturnMerchandise" class="button_large" />
			<input type="hidden" class="hidden" value="<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
" name="id_order" />
		</fieldset>
	</div>
	<br />
	<?php }?>
	</form>

	<?php if (count($_smarty_tpl->tpl_vars['messages']->value)){?>
	<h3><?php echo smartyTranslate(array('s'=>'Messages'),$_smarty_tpl);?>
</h3>
	<div class="table_block">
		<table class="detail_step_by_step std">
			<thead>
				<tr>
					<th class="first_item" style="width:150px;"><?php echo smartyTranslate(array('s'=>'From'),$_smarty_tpl);?>
</th>
					<th class="last_item"><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
</th>
				</tr>
			</thead>
			<tbody>
			<?php  $_smarty_tpl->tpl_vars['message'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['message']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['messages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['message']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['message']->iteration=0;
 $_smarty_tpl->tpl_vars['message']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["messageList"]['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['message']->key => $_smarty_tpl->tpl_vars['message']->value){
$_smarty_tpl->tpl_vars['message']->_loop = true;
 $_smarty_tpl->tpl_vars['message']->iteration++;
 $_smarty_tpl->tpl_vars['message']->index++;
 $_smarty_tpl->tpl_vars['message']->first = $_smarty_tpl->tpl_vars['message']->index === 0;
 $_smarty_tpl->tpl_vars['message']->last = $_smarty_tpl->tpl_vars['message']->iteration === $_smarty_tpl->tpl_vars['message']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["messageList"]['first'] = $_smarty_tpl->tpl_vars['message']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["messageList"]['index']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["messageList"]['last'] = $_smarty_tpl->tpl_vars['message']->last;
?>
				<tr class="<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['messageList']['first']){?>first_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['messageList']['last']){?>last_item<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['messageList']['index']%2){?>alternate_item<?php }else{ ?>item<?php }?>">
					<td>
						<?php if (isset($_smarty_tpl->tpl_vars['message']->value['ename'])&&$_smarty_tpl->tpl_vars['message']->value['ename']){?>
							<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['message']->value['efirstname'], 'htmlall', 'UTF-8');?>
 <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['message']->value['elastname'], 'htmlall', 'UTF-8');?>

						<?php }elseif($_smarty_tpl->tpl_vars['message']->value['clastname']){?>
							<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['message']->value['cfirstname'], 'htmlall', 'UTF-8');?>
 <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['message']->value['clastname'], 'htmlall', 'UTF-8');?>

						<?php }else{ ?>
							<b><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['shop_name']->value, 'htmlall', 'UTF-8');?>
</b>
						<?php }?>
						<br />
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['message']->value['date_add'],'full'=>1),$_smarty_tpl);?>

					</td>
					<td><?php echo nl2br($_smarty_tpl->tpl_vars['message']->value['message']);?>
</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['errors']->value)&&$_smarty_tpl->tpl_vars['errors']->value){?>
		<div class="error">
			<p><?php if (count($_smarty_tpl->tpl_vars['errors']->value)>1){?><?php echo smartyTranslate(array('s'=>'There are %d errors','sprintf'=>count($_smarty_tpl->tpl_vars['errors']->value)),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'There is %d error','sprintf'=>count($_smarty_tpl->tpl_vars['errors']->value)),$_smarty_tpl);?>
<?php }?> :</p>
			<ol>
			<?php  $_smarty_tpl->tpl_vars['error'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['error']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['errors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['error']->key => $_smarty_tpl->tpl_vars['error']->value){
$_smarty_tpl->tpl_vars['error']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['error']->key;
?>
				<li><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</li>
			<?php } ?>
			</ol>
		</div>
	<?php }?>
	
	<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-detail',true);?>
" method="post" class="std" id="sendOrderMessage">
		<h3 class="bg"><?php echo smartyTranslate(array('s'=>'Add a message'),$_smarty_tpl);?>
</h3>
		<p><?php echo smartyTranslate(array('s'=>'If you would like to add a comment about your order, please write it in the field below.'),$_smarty_tpl);?>
</p>
		<fieldset>
			<label for="id_product"><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</label>
			<select name="id_product" style="width:300px;">
				<option value="0"><?php echo smartyTranslate(array('s'=>'-- Choose --'),$_smarty_tpl);?>
</option>
				<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
</option>
				<?php } ?>
			</select>
		</fieldset>
		<fieldset>
			<textarea name="msgText"></textarea>
		</fieldset>
		<input type="hidden" name="id_order" value="<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
" />
		<input type="submit" data-role="button" data-theme="a" name="submitMessage" value="<?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
"/>
	</form>
<?php }else{ ?>
<p><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/infos.gif" alt="" class="icon" />&nbsp;<?php echo smartyTranslate(array('s'=>'You cannot return merchandise with a guest account'),$_smarty_tpl);?>
</p>
<?php }?>
</div><!-- #content -->
<?php }} ?>