<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:59
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/history.tpl" */ ?>
<?php /*%%SmartyHeaderCode:70014949851c1c0df532560-51055548%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eabb8c1093a0db50999d598340a0c97ab4493f52' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/history.tpl',
      1 => 1371646827,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '70014949851c1c0df532560-51055548',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'navigationPipe' => 0,
    'slowValidation' => 0,
    'orders' => 0,
    'order' => 0,
    'img_dir' => 0,
    'invoiceAllowed' => 0,
    'opc' => 0,
    'base_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0df7437e5_98940250',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0df7437e5_98940250')) {function content_51c1c0df7437e5_98940250($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
"><?php echo smartyTranslate(array('s'=>'My account'),$_smarty_tpl);?>
</a><span class="navigation-pipe"><?php echo $_smarty_tpl->tpl_vars['navigationPipe']->value;?>
</span><?php echo smartyTranslate(array('s'=>'Order history'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<h1><?php echo smartyTranslate(array('s'=>'Order history'),$_smarty_tpl);?>
</h1>
<p><?php echo smartyTranslate(array('s'=>'Here are the orders you\'ve placed since your account was created.'),$_smarty_tpl);?>
</p>

<?php if ($_smarty_tpl->tpl_vars['slowValidation']->value){?><p class="warning"><?php echo smartyTranslate(array('s'=>'If you have just placed an order, it may take a few minutes for it to be validated. Please refresh this page if your order is missing.'),$_smarty_tpl);?>
</p><?php }?>

<div class="block-center" id="block-history">
	<?php if ($_smarty_tpl->tpl_vars['orders']->value&&count($_smarty_tpl->tpl_vars['orders']->value)){?>
	<table id="order-list" class="std">
		<thead>
			<tr>
				<th class="first_item"><?php echo smartyTranslate(array('s'=>'Order reference'),$_smarty_tpl);?>
</th>
				<th class="item"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
				<th class="item"><?php echo smartyTranslate(array('s'=>'Total price'),$_smarty_tpl);?>
</th>
				<th class="item"><?php echo smartyTranslate(array('s'=>'Payment: '),$_smarty_tpl);?>
</th>
				<th class="item"><?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
</th>
				<th class="item"><?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>
</th>
				<th class="last_item" style="width:65px">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php  $_smarty_tpl->tpl_vars['order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orders']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['order']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['order']->iteration=0;
 $_smarty_tpl->tpl_vars['order']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['order']->key => $_smarty_tpl->tpl_vars['order']->value){
$_smarty_tpl->tpl_vars['order']->_loop = true;
 $_smarty_tpl->tpl_vars['order']->iteration++;
 $_smarty_tpl->tpl_vars['order']->index++;
 $_smarty_tpl->tpl_vars['order']->first = $_smarty_tpl->tpl_vars['order']->index === 0;
 $_smarty_tpl->tpl_vars['order']->last = $_smarty_tpl->tpl_vars['order']->iteration === $_smarty_tpl->tpl_vars['order']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['first'] = $_smarty_tpl->tpl_vars['order']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['index']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['last'] = $_smarty_tpl->tpl_vars['order']->last;
?>
			<tr class="<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['first']){?>first_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['last']){?>last_item<?php }else{ ?>item<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['index']%2){?>alternate_item<?php }?>">
				<td class="history_link bold">
					<?php if (isset($_smarty_tpl->tpl_vars['order']->value['invoice'])&&$_smarty_tpl->tpl_vars['order']->value['invoice']&&isset($_smarty_tpl->tpl_vars['order']->value['virtual'])&&$_smarty_tpl->tpl_vars['order']->value['virtual']){?><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/download_product.gif" class="icon" alt="<?php echo smartyTranslate(array('s'=>'Products to download'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Products to download'),$_smarty_tpl);?>
" /><?php }?>
					<a class="color-myaccount" href="javascript:showOrder(1, <?php echo intval($_smarty_tpl->tpl_vars['order']->value['id_order']);?>
, '<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-detail',true);?>
');"><?php echo Order::getUniqReferenceOf($_smarty_tpl->tpl_vars['order']->value['id_order']);?>
</a>
				</td>
				<td class="history_date bold"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['order']->value['date_add'],'full'=>0),$_smarty_tpl);?>
</td>
				<td class="history_price"><span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order']->value['total_paid'],'currency'=>$_smarty_tpl->tpl_vars['order']->value['id_currency'],'no_utf8'=>false,'convert'=>false),$_smarty_tpl);?>
</span></td>
				<td class="history_method"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['order']->value['payment'], 'htmlall', 'UTF-8');?>
</td>
				<td class="history_state"><?php if (isset($_smarty_tpl->tpl_vars['order']->value['order_state'])){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['order']->value['order_state'], 'htmlall', 'UTF-8');?>
<?php }?></td>
				<td class="history_invoice">
				<?php if ((isset($_smarty_tpl->tpl_vars['order']->value['invoice'])&&$_smarty_tpl->tpl_vars['order']->value['invoice']&&isset($_smarty_tpl->tpl_vars['order']->value['invoice_number'])&&$_smarty_tpl->tpl_vars['order']->value['invoice_number'])&&isset($_smarty_tpl->tpl_vars['invoiceAllowed']->value)&&$_smarty_tpl->tpl_vars['invoiceAllowed']->value==true){?>
					<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('pdf-invoice',true,null,"id_order=".((string)$_smarty_tpl->tpl_vars['order']->value['id_order']));?>
" title="<?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>
" class="_blank"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/pdf.gif" alt="<?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>
" class="icon" /></a>
					<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('pdf-invoice',true,null,"id_order=".((string)$_smarty_tpl->tpl_vars['order']->value['id_order']));?>
" title="<?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>
" class="_blank"><?php echo smartyTranslate(array('s'=>'PDF'),$_smarty_tpl);?>
</a>
				<?php }else{ ?>-<?php }?>
				</td>
				<td class="history_detail">
					<a class="color-myaccount" href="javascript:showOrder(1, <?php echo intval($_smarty_tpl->tpl_vars['order']->value['id_order']);?>
, '<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-detail',true);?>
');"><?php echo smartyTranslate(array('s'=>'details'),$_smarty_tpl);?>
</a>
					<?php if (isset($_smarty_tpl->tpl_vars['opc']->value)&&$_smarty_tpl->tpl_vars['opc']->value){?>
					<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-opc',true,null,"submitReorder&id_order=".((string)$_smarty_tpl->tpl_vars['order']->value['id_order']));?>
" title="<?php echo smartyTranslate(array('s'=>'Reorder'),$_smarty_tpl);?>
">
					<?php }else{ ?>
					<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,"submitReorder&id_order=".((string)$_smarty_tpl->tpl_vars['order']->value['id_order']));?>
" title="<?php echo smartyTranslate(array('s'=>'Reorder'),$_smarty_tpl);?>
">
					<?php }?>
						<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
arrow_rotate_anticlockwise.png" alt="<?php echo smartyTranslate(array('s'=>'Reorder'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Reorder'),$_smarty_tpl);?>
" class="icon" />
					</a>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<div id="block-order-detail" class="hidden">&nbsp;</div>
	<?php }else{ ?>
		<p class="warning"><?php echo smartyTranslate(array('s'=>'You have not placed any orders.'),$_smarty_tpl);?>
</p>
	<?php }?>
</div>

<ul class="footer_links clearfix">
	<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/my-account.gif" alt="" class="icon" /> <?php echo smartyTranslate(array('s'=>'Back to Your Account'),$_smarty_tpl);?>
</a></li>
	<li class="f_right"><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/home.gif" alt="" class="icon" /> <?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a></li>
</ul>
<?php }} ?>