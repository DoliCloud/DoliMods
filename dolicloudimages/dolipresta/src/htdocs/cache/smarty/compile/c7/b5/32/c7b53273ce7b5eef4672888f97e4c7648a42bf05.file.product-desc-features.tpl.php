<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:11
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/product-desc-features.tpl" */ ?>
<?php /*%%SmartyHeaderCode:113089483051c1c0ebd97501-20617524%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c7b53273ce7b5eef4672888f97e4c7648a42bf05' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/product-desc-features.tpl',
      1 => 1371647177,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '113089483051c1c0ebd97501-20617524',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'features' => 0,
    'feature' => 0,
    'attachments' => 0,
    'attachment' => 0,
    'link' => 0,
    'accessories' => 0,
    'accessory' => 0,
    'accessoryLink' => 0,
    'mediumSize' => 0,
    'restricted_country_mode' => 0,
    'PS_CATALOG_MODE' => 0,
    'priceDisplay' => 0,
    'static_token' => 0,
    'btn_class' => 0,
    'btn_href' => 0,
    'btn_more' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ec092606_68779519',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ec092606_68779519')) {function content_51c1c0ec092606_68779519($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?><div data-role="content" id="more_info_block" class="clearfix">
	<div data-role="collapsible-set" data-content-theme="a">
		
		<!-- full description -->
		<?php if (isset($_smarty_tpl->tpl_vars['product']->value)&&$_smarty_tpl->tpl_vars['product']->value->description){?>
		<div data-role="collapsible" data-theme="a" data-content-theme="a">
			<h3><?php echo smartyTranslate(array('s'=>'More info'),$_smarty_tpl);?>
</h3>
			<div><?php echo $_smarty_tpl->tpl_vars['product']->value->description;?>
</div>
		</div>
		<?php }?>
		
		<!-- product's features -->
		<?php if (isset($_smarty_tpl->tpl_vars['features']->value)&&$_smarty_tpl->tpl_vars['features']->value&&count($_smarty_tpl->tpl_vars['features']->value)>0){?>
		<div data-role="collapsible" data-theme="a" data-content-theme="a">
			<h3><?php echo smartyTranslate(array('s'=>'Data sheet'),$_smarty_tpl);?>
</h3>
			<ul>
				<?php  $_smarty_tpl->tpl_vars['feature'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['feature']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['features']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['feature']->key => $_smarty_tpl->tpl_vars['feature']->value){
$_smarty_tpl->tpl_vars['feature']->_loop = true;
?>
				<?php if (isset($_smarty_tpl->tpl_vars['feature']->value['value'])){?>
					<li><span><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['feature']->value['name'], 'htmlall', 'UTF-8');?>
</span> <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['feature']->value['value'], 'htmlall', 'UTF-8');?>
</li>
				<?php }?>
			<?php } ?>
			</ul>
		</div>
		<?php }?>
		
		<!-- attachments -->
		<?php if (isset($_smarty_tpl->tpl_vars['attachments']->value)&&$_smarty_tpl->tpl_vars['attachments']->value){?>
		<div data-role="collapsible" data-theme="a" data-content-theme="a">
			<h3><?php echo smartyTranslate(array('s'=>'Download'),$_smarty_tpl);?>
</h3>
			<ul>
				<?php  $_smarty_tpl->tpl_vars['attachment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attachment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['attachments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attachment']->key => $_smarty_tpl->tpl_vars['attachment']->value){
$_smarty_tpl->tpl_vars['attachment']->_loop = true;
?>
					<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('attachment',true,null,"id_attachment=".((string)$_smarty_tpl->tpl_vars['attachment']->value['id_attachment']));?>
" data-ajax="false"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['attachment']->value['name'], 'htmlall', 'UTF-8');?>
</a><br /><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['attachment']->value['description'], 'htmlall', 'UTF-8');?>
</li>
				<?php } ?>
			</ul>
		</div>
		<?php }?>
		
		<!-- accessories -->
		<?php if (isset($_smarty_tpl->tpl_vars['accessories']->value)&&$_smarty_tpl->tpl_vars['accessories']->value){?>
		<div data-role="collapsible" data-theme="a" data-content-theme="a" class="accessories_block">
			<h3><?php echo smartyTranslate(array('s'=>'Accessories'),$_smarty_tpl);?>
</h3>
			<ul>
				<?php  $_smarty_tpl->tpl_vars['accessory'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['accessory']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['accessories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['accessory']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['accessory']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['accessory']->key => $_smarty_tpl->tpl_vars['accessory']->value){
$_smarty_tpl->tpl_vars['accessory']->_loop = true;
 $_smarty_tpl->tpl_vars['accessory']->iteration++;
 $_smarty_tpl->tpl_vars['accessory']->last = $_smarty_tpl->tpl_vars['accessory']->iteration === $_smarty_tpl->tpl_vars['accessory']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['accessories_list']['last'] = $_smarty_tpl->tpl_vars['accessory']->last;
?>
					<?php $_smarty_tpl->tpl_vars['accessoryLink'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['accessory']->value['id_product'],$_smarty_tpl->tpl_vars['accessory']->value['link_rewrite'],$_smarty_tpl->tpl_vars['accessory']->value['category']), null, 0);?>
					<li class="ajax_block_product <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['accessories_list']['last']){?>last_item<?php }else{ ?>item<?php }?> product_accessories_description clearfix">
						<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['accessoryLink']->value, 'htmlall', 'UTF-8');?>
" data-ajax="false">
							<div class="clearfix" >
								<div class="col-left" style="width:<?php echo $_smarty_tpl->tpl_vars['mediumSize']->value['width']+10;?>
px;">
									<img src="<?php echo $_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['accessory']->value['link_rewrite'],$_smarty_tpl->tpl_vars['accessory']->value['id_image'],'medium_default');?>
" alt="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['accessory']->value['legend'], 'htmlall', 'UTF-8');?>
" width="<?php echo $_smarty_tpl->tpl_vars['mediumSize']->value['width'];?>
" height="<?php echo $_smarty_tpl->tpl_vars['mediumSize']->value['height'];?>
" />
								</div><!-- .col-left -->
								<div class="col-right">
									<div class="inner">
										<p class="s_title_block"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['accessory']->value['name'], 'htmlall', 'UTF-8');?>
</p>
										<p><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['accessory']->value['description_short']),70,'...');?>
</p>
									</div>
								</div>
							</div>
						</a>
						<?php if ($_smarty_tpl->tpl_vars['accessory']->value['show_price']&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
							<div class="price">
								<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value!=1){?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPrice'][0][0]->displayWtPrice(array('p'=>$_smarty_tpl->tpl_vars['accessory']->value['price']),$_smarty_tpl);?>
<?php }else{ ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPrice'][0][0]->displayWtPrice(array('p'=>$_smarty_tpl->tpl_vars['accessory']->value['price_tax_exc']),$_smarty_tpl);?>
<?php }?>
							</div>
						<?php }?>
						<div class="btn-row">
							<a class="" data-theme="a" data-role="button" data-mini="true" data-inline="true" data-icon="arrow-r" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['accessoryLink']->value, 'htmlall', 'UTF-8');?>
" title="<?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>
" data-ajax="false"><?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>
</a>
							<?php $_smarty_tpl->tpl_vars["btn_more"] = new Smarty_variable('', null, 0);?>
							<?php $_smarty_tpl->tpl_vars["btn_href"] = new Smarty_variable('', null, 0);?>
							<?php $_smarty_tpl->tpl_vars["btn_class"] = new Smarty_variable('', null, 0);?>
							<?php if (($_smarty_tpl->tpl_vars['accessory']->value['allow_oosp']||$_smarty_tpl->tpl_vars['accessory']->value['quantity']>0)&&$_smarty_tpl->tpl_vars['accessory']->value['available_for_order']&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
								<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['accessory']->value['id_product']);?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars["btn_href"] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',true,null,"qty=1&amp;id_product=".$_tmp1."&amp;token=".((string)$_smarty_tpl->tpl_vars['static_token']->value)."&amp;add"), null, 0);?>
							<?php }else{ ?>
								<?php $_smarty_tpl->tpl_vars["btn_class"] = new Smarty_variable("disabled", null, 0);?>
								<?php $_smarty_tpl->_capture_stack[0][] = array('default', "btn_more", null); ob_start(); ?><span class="availability"><?php if ((isset($_smarty_tpl->tpl_vars['accessory']->value['quantity_all_versions'])&&$_smarty_tpl->tpl_vars['accessory']->value['quantity_all_versions']>0)){?><?php echo smartyTranslate(array('s'=>'Product available with different options'),$_smarty_tpl);?>
<?php }else{ ?><?php if (!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?><?php echo smartyTranslate(array('s'=>'Out of stock'),$_smarty_tpl);?>
<?php }?><?php }?></span><?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
							<?php }?>
							<a class="<?php echo $_smarty_tpl->tpl_vars['btn_class']->value;?>
" data-role="button" data-inline="true" data-theme="e" data-icon="plus" data-mini="true" class="exclusive button ajax_add_to_cart_button" href="<?php echo $_smarty_tpl->tpl_vars['btn_href']->value;?>
" rel="ajax_id_product_<?php echo intval($_smarty_tpl->tpl_vars['accessory']->value['id_product']);?>
" title="<?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
</a>
							<?php echo $_smarty_tpl->tpl_vars['btn_more']->value;?>

						</div><!-- .btn-row -->
					</li>
				<?php } ?>
			</ul>
		</div>
		<?php }?>
	</div><!-- role:collapsible-set-->
</div><!-- #more_info_block -->
<?php }} ?>