<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:08
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/category-product-list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:126098509651c1c0e840e4a4-18975204%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ca5916a35153d825931aec57aca016e8d8121417' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/category-product-list.tpl',
      1 => 1371647168,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '126098509651c1c0e840e4a4-18975204',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'products' => 0,
    'product' => 0,
    'link' => 0,
    'PS_CATALOG_MODE' => 0,
    'restricted_country_mode' => 0,
    'priceDisplay' => 0,
    'info3_class' => 0,
    'info3_str' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e85afcd7_28942518',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e85afcd7_28942518')) {function content_51c1c0e85afcd7_28942518($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php if (isset($_smarty_tpl->tpl_vars['products']->value)){?>
	<ul data-role="listview" id="category-list" class="ui-listview ui-grid-a">
	<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']++;
?>
		<li class="ui-block-<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['products']['index']%2){?>b<?php }else{ ?>a<?php }?> product-list-row">
			<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['link'], 'htmlall', 'UTF-8');?>
" data-ajax="false">
				<div class="product_img_wrapper"><img class="ui-li-thumb" src="<?php echo $_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['id_image'],'home_default');?>
" alt="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['legend'], 'htmlall', 'UTF-8');?>
" /></div>
				<h3 class="ui-li-heading"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['name'], 'htmlall', 'UTF-8');?>
</h3>
				<?php if ((!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value&&((isset($_smarty_tpl->tpl_vars['product']->value['show_price'])&&$_smarty_tpl->tpl_vars['product']->value['show_price'])||(isset($_smarty_tpl->tpl_vars['product']->value['available_for_order'])&&$_smarty_tpl->tpl_vars['product']->value['available_for_order'])))){?>
					<p class="ui-li-price">
					<?php if (isset($_smarty_tpl->tpl_vars['product']->value['show_price'])&&$_smarty_tpl->tpl_vars['product']->value['show_price']&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)){?>
						<?php if (!$_smarty_tpl->tpl_vars['priceDisplay']->value){?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value['price']),$_smarty_tpl);?>
<?php }else{ ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value['price_tax_exc']),$_smarty_tpl);?>
<?php }?>
					<?php }?>
					</p>
					<?php $_smarty_tpl->tpl_vars['info3_str'] = new Smarty_variable('&nbsp;', null, 0);?>
					<?php $_smarty_tpl->tpl_vars['info3_class'] = new Smarty_variable('on_sale', null, 0);?>
					<?php if (isset($_smarty_tpl->tpl_vars['product']->value['on_sale'])&&$_smarty_tpl->tpl_vars['product']->value['on_sale']&&isset($_smarty_tpl->tpl_vars['product']->value['show_price'])&&$_smarty_tpl->tpl_vars['product']->value['show_price']&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
						<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'info3_str', null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'On sale!'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
					<?php }elseif(isset($_smarty_tpl->tpl_vars['product']->value['reduction'])&&$_smarty_tpl->tpl_vars['product']->value['reduction']&&isset($_smarty_tpl->tpl_vars['product']->value['show_price'])&&$_smarty_tpl->tpl_vars['product']->value['show_price']&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
						<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'info3_str', null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Reduced price!'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
						<?php $_smarty_tpl->tpl_vars['info3_class'] = new Smarty_variable('discount', null, 0);?>
					<?php }?>
					<p class="ui-li-price-info <?php echo $_smarty_tpl->tpl_vars['info3_class']->value;?>
"><span><?php echo $_smarty_tpl->tpl_vars['info3_str']->value;?>
</span></p>
					<p class="availability">
					<?php if (isset($_smarty_tpl->tpl_vars['product']->value['available_for_order'])&&$_smarty_tpl->tpl_vars['product']->value['available_for_order']&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)){?>
						<?php if (($_smarty_tpl->tpl_vars['product']->value['allow_oosp']||$_smarty_tpl->tpl_vars['product']->value['quantity']>0)){?><?php echo smartyTranslate(array('s'=>'Available'),$_smarty_tpl);?>
<?php }elseif((isset($_smarty_tpl->tpl_vars['product']->value['quantity_all_versions'])&&$_smarty_tpl->tpl_vars['product']->value['quantity_all_versions']>0)){?><?php echo smartyTranslate(array('s'=>'Product available with different options'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Out of stock'),$_smarty_tpl);?>
<?php }?>
					<?php }else{ ?>
						&nbsp;
					<?php }?>
					</p>
					
					<?php if (isset($_smarty_tpl->tpl_vars['product']->value['online_only'])&&$_smarty_tpl->tpl_vars['product']->value['online_only']){?>
						<p class="online_only"><?php echo smartyTranslate(array('s'=>'Online only!'),$_smarty_tpl);?>
</p>
					<?php }?>
				<?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['product']->value['new'])&&$_smarty_tpl->tpl_vars['product']->value['new']==1){?><p class="new"><?php echo smartyTranslate(array('s'=>'New'),$_smarty_tpl);?>
</p><?php }?>
			</a>
		</li>
	<?php } ?>
	</ul><!-- #category-list -->
<?php }?>
<?php }} ?>