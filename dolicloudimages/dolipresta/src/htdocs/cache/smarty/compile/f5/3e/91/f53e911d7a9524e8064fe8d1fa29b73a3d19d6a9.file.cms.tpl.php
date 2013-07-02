<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:08
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/cms.tpl" */ ?>
<?php /*%%SmartyHeaderCode:140497550651c1c0e885a584-63677647%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f53e911d7a9524e8064fe8d1fa29b73a3d19d6a9' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/cms.tpl',
      1 => 1371647169,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '140497550651c1c0e885a584-63677647',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cms' => 0,
    'category' => 0,
    'content_only' => 0,
    'sub_category' => 0,
    'subcategory' => 0,
    'link' => 0,
    'cms_pages' => 0,
    'cmspages' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e89691e2_95336416',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e89691e2_95336416')) {function content_51c1c0e89691e2_95336416($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'page_title', null); ob_start(); ?>
	<?php if (isset($_smarty_tpl->tpl_vars['cms']->value)&&!isset($_smarty_tpl->tpl_vars['category']->value)){?>
		<?php echo $_smarty_tpl->tpl_vars['cms']->value->meta_title;?>

	<?php }elseif(isset($_smarty_tpl->tpl_vars['category']->value)){?>
		<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['category']->value->name, 'htmlall', 'UTF-8');?>

	<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate ('./page-title.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div data-role="content" id="content">
<?php if (isset($_smarty_tpl->tpl_vars['cms']->value)&&!isset($_smarty_tpl->tpl_vars['category']->value)){?>
	<div class="rte<?php if ($_smarty_tpl->tpl_vars['content_only']->value){?> content_only<?php }?>">
		<?php echo $_smarty_tpl->tpl_vars['cms']->value->content;?>

	</div>
<?php }elseif(isset($_smarty_tpl->tpl_vars['category']->value)){?>
	<div class="block-cms">
		<?php if (isset($_smarty_tpl->tpl_vars['sub_category']->value)&!empty($_smarty_tpl->tpl_vars['sub_category']->value)){?>	
			<h3 class="bg"><?php echo smartyTranslate(array('s'=>'List of sub categories in %s:','sprintf'=>$_smarty_tpl->tpl_vars['category']->value->name),$_smarty_tpl);?>
</h3>
			<ul data-role="listview" data-inset="true">
				<?php  $_smarty_tpl->tpl_vars['subcategory'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['subcategory']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['sub_category']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['subcategory']->key => $_smarty_tpl->tpl_vars['subcategory']->value){
$_smarty_tpl->tpl_vars['subcategory']->_loop = true;
?>
					<li>
						<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getCMSCategoryLink($_smarty_tpl->tpl_vars['subcategory']->value['id_cms_category'],$_smarty_tpl->tpl_vars['subcategory']->value['link_rewrite']), 'htmlall', 'UTF-8');?>
" data-ajax="false"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['subcategory']->value['name'], 'htmlall', 'UTF-8');?>
</a>
					</li>
				<?php } ?>
			</ul>
		<?php }?>
		<?php if (isset($_smarty_tpl->tpl_vars['cms_pages']->value)&!empty($_smarty_tpl->tpl_vars['cms_pages']->value)){?>
		<h3 class="bg"><?php echo smartyTranslate(array('s'=>'List of pages in %s:','sprintf'=>$_smarty_tpl->tpl_vars['category']->value->name),$_smarty_tpl);?>
</h3>
			<ul data-role="listview" data-inset="true">
				<?php  $_smarty_tpl->tpl_vars['cmspages'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cmspages']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cms_pages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cmspages']->key => $_smarty_tpl->tpl_vars['cmspages']->value){
$_smarty_tpl->tpl_vars['cmspages']->_loop = true;
?>
					<li>
						<a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getCMSLink($_smarty_tpl->tpl_vars['cmspages']->value['id_cms'],$_smarty_tpl->tpl_vars['cmspages']->value['link_rewrite']), 'htmlall', 'UTF-8');?>
" data-ajax="false"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['cmspages']->value['meta_title'], 'htmlall', 'UTF-8');?>
</a>
					</li>
				<?php } ?>
			</ul>
		<?php }?>
	</div>
<?php }else{ ?>
	<?php echo smartyTranslate(array('s'=>'This page does not exist.'),$_smarty_tpl);?>

<?php }?>
</div><!-- #content -->
<?php }} ?>