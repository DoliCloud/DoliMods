<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:13
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/sitemap.tpl" */ ?>
<?php /*%%SmartyHeaderCode:174271457451c1c0ed9506d7-25497686%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e3bd2c78fdbea38c15b2c6c10dbc087996b61a83' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/sitemap.tpl',
      1 => 1371647181,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '174271457451c1c0ed9506d7-25497686',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'categoriesTree' => 0,
    'i' => 0,
    'child' => 0,
    'controller_name' => 0,
    'link' => 0,
    'PS_CATALOG_MODE' => 0,
    'display_manufacturer_link' => 0,
    'PS_DISPLAY_SUPPLIERS' => 0,
    'display_supplier_link' => 0,
    'voucherAllowed' => 0,
    'categoriescmsTree' => 0,
    'cms' => 0,
    'display_store' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0edb7e443_65324811',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0edb7e443_65324811')) {function content_51c1c0edb7e443_65324811($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>
<div id="hook_mobile_top_site_map">
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayMobileTopSiteMap"),$_smarty_tpl);?>

</div>
<hr/>
<?php if (isset($_smarty_tpl->tpl_vars['categoriesTree']->value['children'])){?>
	<h2><?php echo smartyTranslate(array('s'=>'Our offers'),$_smarty_tpl);?>
</h2>

	<ul data-role="listview" data-inset="true">
		<?php $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['i']->step = 1;$_smarty_tpl->tpl_vars['i']->total = (int)ceil(($_smarty_tpl->tpl_vars['i']->step > 0 ? 4+1 - (0) : 0-(4)+1)/abs($_smarty_tpl->tpl_vars['i']->step));
if ($_smarty_tpl->tpl_vars['i']->total > 0){
for ($_smarty_tpl->tpl_vars['i']->value = 0, $_smarty_tpl->tpl_vars['i']->iteration = 1;$_smarty_tpl->tpl_vars['i']->iteration <= $_smarty_tpl->tpl_vars['i']->total;$_smarty_tpl->tpl_vars['i']->value += $_smarty_tpl->tpl_vars['i']->step, $_smarty_tpl->tpl_vars['i']->iteration++){
$_smarty_tpl->tpl_vars['i']->first = $_smarty_tpl->tpl_vars['i']->iteration == 1;$_smarty_tpl->tpl_vars['i']->last = $_smarty_tpl->tpl_vars['i']->iteration == $_smarty_tpl->tpl_vars['i']->total;?>
			<?php if (isset($_smarty_tpl->tpl_vars['categoriesTree']->value['children'][$_smarty_tpl->tpl_vars['i']->value])){?>
				<?php if (isset($_smarty_tpl->tpl_vars['categoriesTree']->value['children'][$_smarty_tpl->tpl_vars['i']->value]['children'])&&(count($_smarty_tpl->tpl_vars['categoriesTree']->value['children'][$_smarty_tpl->tpl_vars['i']->value]['children'])>0)){?>
						<?php echo $_smarty_tpl->getSubTemplate ("./category-tree-branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('node'=>$_smarty_tpl->tpl_vars['categoriesTree']->value['children'][$_smarty_tpl->tpl_vars['i']->value]), 0);?>

				<?php }else{ ?>
				<li data-icon="arrow-d">
					<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['categoriesTree']->value['children'][$_smarty_tpl->tpl_vars['i']->value]['link'], 'htmlall', 'UTF-8');?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['categoriesTree']->value['children'][$_smarty_tpl->tpl_vars['i']->value]['desc'], 'htmlall', 'UTF-8');?>
">
						<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['categoriesTree']->value['children'][$_smarty_tpl->tpl_vars['i']->value]['name'], 'htmlall', 'UTF-8');?>

					</a>
				</li>
				<?php }?>
			<?php }?>
		<?php }} ?>
		<li>
			<?php echo smartyTranslate(array('s'=>'All categories'),$_smarty_tpl);?>

			<ul data-role="listview" data-inset="true">
				<?php  $_smarty_tpl->tpl_vars['child'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['child']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categoriesTree']->value['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['child']->key => $_smarty_tpl->tpl_vars['child']->value){
$_smarty_tpl->tpl_vars['child']->_loop = true;
?>
					<?php echo $_smarty_tpl->getSubTemplate ("./category-tree-branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('node'=>$_smarty_tpl->tpl_vars['child']->value,'last'=>'true'), 0);?>

				<?php } ?>
			</ul>
		</li>
	</ul>
<?php }?>

<hr/>
<h2><?php echo smartyTranslate(array('s'=>'Sitemap'),$_smarty_tpl);?>
</h2>
<ul data-role="listview" data-inset="true" id="category">
	<?php if ($_smarty_tpl->tpl_vars['controller_name']->value!='index'){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('index',true);?>
"><?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a></li><?php }?>
	<li><?php echo smartyTranslate(array('s'=>'Our offers'),$_smarty_tpl);?>

		<ul data-role="listview" data-inset="true">
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('new-products');?>
" title="<?php echo smartyTranslate(array('s'=>'New products'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'New products'),$_smarty_tpl);?>
</a></li>
			<?php if (!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('prices-drop');?>
" title="<?php echo smartyTranslate(array('s'=>'Price drop'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Price drop'),$_smarty_tpl);?>
</a></li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('best-sales',true);?>
" title="<?php echo smartyTranslate(array('s'=>'Best sellers'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Best sellers'),$_smarty_tpl);?>
</a></li>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['display_manufacturer_link']->value||$_smarty_tpl->tpl_vars['PS_DISPLAY_SUPPLIERS']->value){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('manufacturer');?>
"><?php echo smartyTranslate(array('s'=>'Manufacturers:'),$_smarty_tpl);?>
</a></li><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['display_supplier_link']->value||$_smarty_tpl->tpl_vars['PS_DISPLAY_SUPPLIERS']->value){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('supplier');?>
"><?php echo smartyTranslate(array('s'=>'Suppliers:'),$_smarty_tpl);?>
</a></li><?php }?>
		</ul>
	</li>
	<li><?php echo smartyTranslate(array('s'=>'Your Account'),$_smarty_tpl);?>

		<ul data-role="listview" data-inset="true">
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
"><?php echo smartyTranslate(array('s'=>'Your Account'),$_smarty_tpl);?>
</a></li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('identity',true);?>
"><?php echo smartyTranslate(array('s'=>'Personal information'),$_smarty_tpl);?>
</a></li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('addresses',true);?>
"><?php echo smartyTranslate(array('s'=>'Addresses'),$_smarty_tpl);?>
</a></li>
			<?php if ($_smarty_tpl->tpl_vars['voucherAllowed']->value){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('discount',true);?>
"><?php echo smartyTranslate(array('s'=>'Discounts'),$_smarty_tpl);?>
</a></li><?php }?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('history',true);?>
"><?php echo smartyTranslate(array('s'=>'Order history'),$_smarty_tpl);?>
</a></li>
		</ul>
	</li>
	<li><?php echo smartyTranslate(array('s'=>'Pages'),$_smarty_tpl);?>

		<ul data-role="listview" data-inset="true">
			<?php if (isset($_smarty_tpl->tpl_vars['categoriescmsTree']->value['children'])){?>
				<?php  $_smarty_tpl->tpl_vars['child'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['child']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categoriescmsTree']->value['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['child']->key => $_smarty_tpl->tpl_vars['child']->value){
$_smarty_tpl->tpl_vars['child']->_loop = true;
?>
					<?php if ((isset($_smarty_tpl->tpl_vars['child']->value['children'])&&count($_smarty_tpl->tpl_vars['child']->value['children'])>0)||count($_smarty_tpl->tpl_vars['child']->value['cms'])>0){?>
						<?php echo $_smarty_tpl->getSubTemplate ("./category-cms-tree-branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('node'=>$_smarty_tpl->tpl_vars['child']->value), 0);?>

					<?php }?>
				<?php } ?>
			<?php }?>
			<?php  $_smarty_tpl->tpl_vars['cms'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cms']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categoriescmsTree']->value['cms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cms']->key => $_smarty_tpl->tpl_vars['cms']->value){
$_smarty_tpl->tpl_vars['cms']->_loop = true;
?>
				<li><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['cms']->value['link'], 'htmlall', 'UTF-8');?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['cms']->value['meta_title'], 'htmlall', 'UTF-8');?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['cms']->value['meta_title'], 'htmlall', 'UTF-8');?>
</a></li>
			<?php } ?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true);?>
" title="<?php echo smartyTranslate(array('s'=>'Contact'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Contact'),$_smarty_tpl);?>
</a></li>
			<?php if ($_smarty_tpl->tpl_vars['display_store']->value){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('stores');?>
" title="<?php echo smartyTranslate(array('s'=>'Our stores'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Our stores'),$_smarty_tpl);?>
</a></li><?php }?>
		</ul>
	</li>
</ul>
<?php }} ?>