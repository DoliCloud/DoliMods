<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:06
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/sitemap.tpl" */ ?>
<?php /*%%SmartyHeaderCode:101349330051c1c0e6d92320-99748746%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bff770a71469d83a716e0a8c564e6c5d89736c82' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/sitemap.tpl',
      1 => 1371646836,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '101349330051c1c0e6d92320-99748746',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'PS_CATALOG_MODE' => 0,
    'display_manufacturer_link' => 0,
    'PS_DISPLAY_SUPPLIERS' => 0,
    'display_supplier_link' => 0,
    'logged' => 0,
    'voucherAllowed' => 0,
    'base_dir_ssl' => 0,
    'categoriesTree' => 0,
    'child' => 0,
    'categoriescmsTree' => 0,
    'cms' => 0,
    'display_store' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e70add36_10194439',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e70add36_10194439')) {function content_51c1c0e70add36_10194439($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Sitemap'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<h1><?php echo smartyTranslate(array('s'=>'Sitemap'),$_smarty_tpl);?>
</h1>
<div id="sitemap_content" class="clearfix">
	<div class="sitemap_block">
		<h3><?php echo smartyTranslate(array('s'=>'Our offers'),$_smarty_tpl);?>
</h3>
		<ul>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('new-products');?>
" title="<?php echo smartyTranslate(array('s'=>'View a new product'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'New products'),$_smarty_tpl);?>
</a></li>
			<?php if (!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('best-sales');?>
" title="<?php echo smartyTranslate(array('s'=>'View top-selling products'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Best sellers'),$_smarty_tpl);?>
</a></li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('prices-drop');?>
" title="<?php echo smartyTranslate(array('s'=>'View products with a price drop'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Price drop'),$_smarty_tpl);?>
</a></li>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['display_manufacturer_link']->value||$_smarty_tpl->tpl_vars['PS_DISPLAY_SUPPLIERS']->value){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('manufacturer');?>
" title="<?php echo smartyTranslate(array('s'=>'View a list of manufacturers'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Manufacturers:'),$_smarty_tpl);?>
</a></li><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['display_supplier_link']->value||$_smarty_tpl->tpl_vars['PS_DISPLAY_SUPPLIERS']->value){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('supplier');?>
" title="<?php echo smartyTranslate(array('s'=>'View a list of suppliers'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Suppliers:'),$_smarty_tpl);?>
</a></li><?php }?>
		</ul>
	</div>
	<div class="sitemap_block">
		<h3><?php echo smartyTranslate(array('s'=>'Your Account'),$_smarty_tpl);?>
</h3>
		<ul>
		<?php if ($_smarty_tpl->tpl_vars['logged']->value){?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
" title="<?php echo smartyTranslate(array('s'=>'Manage your customer account'),$_smarty_tpl);?>
" rel="nofollow"><?php echo smartyTranslate(array('s'=>'Your Account'),$_smarty_tpl);?>
</a></li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('identity',true);?>
" title="<?php echo smartyTranslate(array('s'=>'Manage your personal information'),$_smarty_tpl);?>
" rel="nofollow"><?php echo smartyTranslate(array('s'=>'Personal information'),$_smarty_tpl);?>
</a></li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('addresses',true);?>
" title="<?php echo smartyTranslate(array('s'=>'View a list of my addresses'),$_smarty_tpl);?>
" rel="nofollow"><?php echo smartyTranslate(array('s'=>'Addresses'),$_smarty_tpl);?>
</a></li>
			<?php if ($_smarty_tpl->tpl_vars['voucherAllowed']->value){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('discount',true);?>
" title="<?php echo smartyTranslate(array('s'=>'View a list of my discounts'),$_smarty_tpl);?>
" rel="nofollow"><?php echo smartyTranslate(array('s'=>'Discounts'),$_smarty_tpl);?>
</a></li><?php }?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('history',true);?>
" title="<?php echo smartyTranslate(array('s'=>'View a list of my orders'),$_smarty_tpl);?>
" rel="nofollow"><?php echo smartyTranslate(array('s'=>'Order history'),$_smarty_tpl);?>
</a></li>
		<?php }else{ ?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true);?>
" title="<?php echo smartyTranslate(array('s'=>'Authentication'),$_smarty_tpl);?>
" rel="nofollow"><?php echo smartyTranslate(array('s'=>'Authentication'),$_smarty_tpl);?>
</a></li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true);?>
" title="<?php echo smartyTranslate(array('s'=>'Create new account'),$_smarty_tpl);?>
"rel="nofollow"><?php echo smartyTranslate(array('s'=>'Create new account'),$_smarty_tpl);?>
</a></li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['logged']->value){?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('index');?>
?mylogout" title="<?php echo smartyTranslate(array('s'=>'Log out'),$_smarty_tpl);?>
" rel="nofollow"><?php echo smartyTranslate(array('s'=>'Log out'),$_smarty_tpl);?>
</a></li>
		<?php }?>
		</ul>
	</div>
	<br class="clear" />
</div>
<div id="listpage_content">
	<div class="categTree">
		<h3><?php echo smartyTranslate(array('s'=>'Categories'),$_smarty_tpl);?>
</h3>
		<div class="tree_top"><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir_ssl']->value;?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['categoriesTree']->value['name'], 'htmlall', 'UTF-8');?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['categoriesTree']->value['name'], 'htmlall', 'UTF-8');?>
</a></div>
		<ul class="tree">
		<?php if (isset($_smarty_tpl->tpl_vars['categoriesTree']->value['children'])){?>
			<?php  $_smarty_tpl->tpl_vars['child'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['child']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categoriesTree']->value['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['child']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['child']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['child']->key => $_smarty_tpl->tpl_vars['child']->value){
$_smarty_tpl->tpl_vars['child']->_loop = true;
 $_smarty_tpl->tpl_vars['child']->iteration++;
 $_smarty_tpl->tpl_vars['child']->last = $_smarty_tpl->tpl_vars['child']->iteration === $_smarty_tpl->tpl_vars['child']->total;
?>
				<?php if ($_smarty_tpl->tpl_vars['child']->last){?>
					<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./category-tree-branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('node'=>$_smarty_tpl->tpl_vars['child']->value,'last'=>'true'), 0);?>

				<?php }else{ ?>
					<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./category-tree-branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('node'=>$_smarty_tpl->tpl_vars['child']->value), 0);?>

				<?php }?>
			<?php } ?>
		<?php }?>
		</ul>
	</div>
	<div class="categTree">
		<h3><?php echo smartyTranslate(array('s'=>'Pages'),$_smarty_tpl);?>
</h3>
		<div class="tree_top"><a href="<?php echo $_smarty_tpl->tpl_vars['categoriescmsTree']->value['link'];?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['categoriescmsTree']->value['name'], 'htmlall', 'UTF-8');?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['categoriescmsTree']->value['name'], 'htmlall', 'UTF-8');?>
</a></div>
		<ul class="tree">
			<?php if (isset($_smarty_tpl->tpl_vars['categoriescmsTree']->value['children'])){?>
				<?php  $_smarty_tpl->tpl_vars['child'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['child']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categoriescmsTree']->value['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['child']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['child']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['child']->key => $_smarty_tpl->tpl_vars['child']->value){
$_smarty_tpl->tpl_vars['child']->_loop = true;
 $_smarty_tpl->tpl_vars['child']->iteration++;
 $_smarty_tpl->tpl_vars['child']->last = $_smarty_tpl->tpl_vars['child']->iteration === $_smarty_tpl->tpl_vars['child']->total;
?>
					<?php if ((isset($_smarty_tpl->tpl_vars['child']->value['children'])&&count($_smarty_tpl->tpl_vars['child']->value['children'])>0)||count($_smarty_tpl->tpl_vars['child']->value['cms'])>0){?>
						<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./category-cms-tree-branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('node'=>$_smarty_tpl->tpl_vars['child']->value), 0);?>

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
			<?php if ($_smarty_tpl->tpl_vars['display_store']->value){?><li class="last"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('stores');?>
" title="<?php echo smartyTranslate(array('s'=>'List of our stores'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Our stores'),$_smarty_tpl);?>
</a></li><?php }?>
		</ul>
	</div>
</div>
<?php }} ?>