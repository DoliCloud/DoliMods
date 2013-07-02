<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:03
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/product-list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:43374841851c1c0e3bc91c1-11990842%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '928996001a6676f9c8d2b922f13c3fd04afcd28b' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/product-list.tpl',
      1 => 1371646833,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '43374841851c1c0e3bc91c1-11990842',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'products' => 0,
    'comparator_max_item' => 0,
    'product' => 0,
    'compareProducts' => 0,
    'link' => 0,
    'homeSize' => 0,
    'PS_CATALOG_MODE' => 0,
    'restricted_country_mode' => 0,
    'priceDisplay' => 0,
    'add_prod_display' => 0,
    'static_token' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e3ecd9c1_07858780',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e3ecd9c1_07858780')) {function content_51c1c0e3ecd9c1_07858780($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php if (isset($_smarty_tpl->tpl_vars['products']->value)){?>
	<!-- Products list -->
	<ul id="product_list" class="clear">
	<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['product']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['product']->iteration=0;
 $_smarty_tpl->tpl_vars['product']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['product']->iteration++;
 $_smarty_tpl->tpl_vars['product']->index++;
 $_smarty_tpl->tpl_vars['product']->first = $_smarty_tpl->tpl_vars['product']->index === 0;
 $_smarty_tpl->tpl_vars['product']->last = $_smarty_tpl->tpl_vars['product']->iteration === $_smarty_tpl->tpl_vars['product']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['first'] = $_smarty_tpl->tpl_vars['product']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['last'] = $_smarty_tpl->tpl_vars['product']->last;
?>
		<li class="ajax_block_product <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['products']['first']){?>first_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['products']['last']){?>last_item<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['products']['index']%2){?>alternate_item<?php }else{ ?>item<?php }?> clearfix">
			<div class="left_block">
				<?php if (isset($_smarty_tpl->tpl_vars['comparator_max_item']->value)&&$_smarty_tpl->tpl_vars['comparator_max_item']->value){?>
					<p class="compare">
						<input type="checkbox" class="comparator" id="comparator_item_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
" value="comparator_item_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
" <?php if (isset($_smarty_tpl->tpl_vars['compareProducts']->value)&&in_array($_smarty_tpl->tpl_vars['product']->value['id_product'],$_smarty_tpl->tpl_vars['compareProducts']->value)){?>checked="checked"<?php }?> /> 
						<label for="comparator_item_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
"><?php echo smartyTranslate(array('s'=>'Select to compare'),$_smarty_tpl);?>
</label>
					</p>
				<?php }?>
			</div>
			<div class="center_block">
				<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['link'], 'htmlall', 'UTF-8');?>
" class="product_img_link" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['name'], 'htmlall', 'UTF-8');?>
">
					<img src="<?php echo $_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['id_image'],'home_default');?>
" alt="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['legend'], 'htmlall', 'UTF-8');?>
" <?php if (isset($_smarty_tpl->tpl_vars['homeSize']->value)){?> width="<?php echo $_smarty_tpl->tpl_vars['homeSize']->value['width'];?>
" height="<?php echo $_smarty_tpl->tpl_vars['homeSize']->value['height'];?>
"<?php }?> />
					<?php if (isset($_smarty_tpl->tpl_vars['product']->value['new'])&&$_smarty_tpl->tpl_vars['product']->value['new']==1){?><span class="new"><?php echo smartyTranslate(array('s'=>'New'),$_smarty_tpl);?>
</span><?php }?>
				</a>
				<h3><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['link'], 'htmlall', 'UTF-8');?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['name'], 'htmlall', 'UTF-8');?>
"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate(smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['name'], 'htmlall', 'UTF-8'),35,'...');?>
</a></h3>
				<p class="product_desc"><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['link'], 'htmlall', 'UTF-8');?>
" title="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate(strip_tags($_smarty_tpl->tpl_vars['product']->value['description_short']),360,'...');?>
" ><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate(strip_tags($_smarty_tpl->tpl_vars['product']->value['description_short']),360,'...');?>
</a></p>
			</div>
			<div class="right_block">
				<?php if (isset($_smarty_tpl->tpl_vars['product']->value['on_sale'])&&$_smarty_tpl->tpl_vars['product']->value['on_sale']&&isset($_smarty_tpl->tpl_vars['product']->value['show_price'])&&$_smarty_tpl->tpl_vars['product']->value['show_price']&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?><span class="on_sale"><?php echo smartyTranslate(array('s'=>'On sale!'),$_smarty_tpl);?>
</span>
				<?php }elseif(isset($_smarty_tpl->tpl_vars['product']->value['reduction'])&&$_smarty_tpl->tpl_vars['product']->value['reduction']&&isset($_smarty_tpl->tpl_vars['product']->value['show_price'])&&$_smarty_tpl->tpl_vars['product']->value['show_price']&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?><span class="discount"><?php echo smartyTranslate(array('s'=>'Reduced price!'),$_smarty_tpl);?>
</span><?php }?>
				<?php if ((!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value&&((isset($_smarty_tpl->tpl_vars['product']->value['show_price'])&&$_smarty_tpl->tpl_vars['product']->value['show_price'])||(isset($_smarty_tpl->tpl_vars['product']->value['available_for_order'])&&$_smarty_tpl->tpl_vars['product']->value['available_for_order'])))){?>
				<div class="content_price">
					<?php if (isset($_smarty_tpl->tpl_vars['product']->value['show_price'])&&$_smarty_tpl->tpl_vars['product']->value['show_price']&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)){?><span class="price" style="display: inline;"><?php if (!$_smarty_tpl->tpl_vars['priceDisplay']->value){?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value['price']),$_smarty_tpl);?>
<?php }else{ ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['product']->value['price_tax_exc']),$_smarty_tpl);?>
<?php }?></span><br /><?php }?>
					<?php if (isset($_smarty_tpl->tpl_vars['product']->value['available_for_order'])&&$_smarty_tpl->tpl_vars['product']->value['available_for_order']&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)){?><span class="availability"><?php if (($_smarty_tpl->tpl_vars['product']->value['allow_oosp']||$_smarty_tpl->tpl_vars['product']->value['quantity']>0)){?><?php echo smartyTranslate(array('s'=>'Available'),$_smarty_tpl);?>
<?php }elseif((isset($_smarty_tpl->tpl_vars['product']->value['quantity_all_versions'])&&$_smarty_tpl->tpl_vars['product']->value['quantity_all_versions']>0)){?><?php echo smartyTranslate(array('s'=>'Product available with different options'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Out of stock'),$_smarty_tpl);?>
<?php }?></span><?php }?>
				</div>
				<?php if (isset($_smarty_tpl->tpl_vars['product']->value['online_only'])&&$_smarty_tpl->tpl_vars['product']->value['online_only']){?><span class="online_only"><?php echo smartyTranslate(array('s'=>'Online only'),$_smarty_tpl);?>
</span><?php }?>
				<?php }?>
				<?php if (($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']==0||(isset($_smarty_tpl->tpl_vars['add_prod_display']->value)&&($_smarty_tpl->tpl_vars['add_prod_display']->value==1)))&&$_smarty_tpl->tpl_vars['product']->value['available_for_order']&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)&&$_smarty_tpl->tpl_vars['product']->value['minimal_quantity']<=1&&$_smarty_tpl->tpl_vars['product']->value['customizable']!=2&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
					<?php if (($_smarty_tpl->tpl_vars['product']->value['allow_oosp']||$_smarty_tpl->tpl_vars['product']->value['quantity']>0)){?>
						<?php if (isset($_smarty_tpl->tpl_vars['static_token']->value)){?>
							<a class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',false,null,"add=1&amp;id_product=".$_tmp1."&amp;token=".((string)$_smarty_tpl->tpl_vars['static_token']->value),false);?>
" title="<?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
"><span></span><?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
</a>
						<?php }else{ ?>
							<a class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',false,null,"add=1&amp;id_product=".$_tmp2,false);?>
" title="<?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
"><span></span><?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
</a>
						<?php }?>						
					<?php }else{ ?>
						<span class="exclusive"><span></span><?php echo smartyTranslate(array('s'=>'Add to cart'),$_smarty_tpl);?>
</span><br />
					<?php }?>
				<?php }?>
				<a class="button lnk_view" href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['link'], 'htmlall', 'UTF-8');?>
" title="<?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>
</a>
			</div>
		</li>
	<?php } ?>
	</ul>
	<!-- /Products list -->
<?php }?>
<?php }} ?>