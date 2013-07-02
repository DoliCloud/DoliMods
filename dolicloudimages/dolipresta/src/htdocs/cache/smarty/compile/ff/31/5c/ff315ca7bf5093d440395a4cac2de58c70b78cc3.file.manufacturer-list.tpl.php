<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:09
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/manufacturer-list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:123856132251c1c0e9352a69-75495605%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ff315ca7bf5093d440395a4cac2de58c70b78cc3' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/manufacturer-list.tpl',
      1 => 1371647170,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '123856132251c1c0e9352a69-75495605',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'errors' => 0,
    'nbManufacturers' => 0,
    'manufacturers' => 0,
    'manufacturer' => 0,
    'link' => 0,
    'img_manu_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e945ddf3_62448963',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e945ddf3_62448963')) {function content_51c1c0e945ddf3_62448963($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'page_title', null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Manufacturers:'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate ('./page-title.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div data-role="content" id="content">

<?php if (isset($_smarty_tpl->tpl_vars['errors']->value)&&$_smarty_tpl->tpl_vars['errors']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }else{ ?>
	<p class="nbrmanufacturer"><span class="bold"><?php if ($_smarty_tpl->tpl_vars['nbManufacturers']->value==0){?><?php echo smartyTranslate(array('s'=>'There are no manufacturers.'),$_smarty_tpl);?>
<?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['nbManufacturers']->value==1){?><?php echo smartyTranslate(array('s'=>'There is %d manufacturer.','sprintf'=>$_smarty_tpl->tpl_vars['nbManufacturers']->value),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'There are %d manufacturers.','sprintf'=>$_smarty_tpl->tpl_vars['nbManufacturers']->value),$_smarty_tpl);?>
<?php }?><?php }?></span>
	</p>

<?php if ($_smarty_tpl->tpl_vars['nbManufacturers']->value>0){?>
	<ul id="manufacturers_list" data-role="listview">
	<?php  $_smarty_tpl->tpl_vars['manufacturer'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['manufacturer']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['manufacturers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['manufacturer']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['manufacturer']->iteration=0;
 $_smarty_tpl->tpl_vars['manufacturer']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['manufacturer']->key => $_smarty_tpl->tpl_vars['manufacturer']->value){
$_smarty_tpl->tpl_vars['manufacturer']->_loop = true;
 $_smarty_tpl->tpl_vars['manufacturer']->iteration++;
 $_smarty_tpl->tpl_vars['manufacturer']->index++;
 $_smarty_tpl->tpl_vars['manufacturer']->first = $_smarty_tpl->tpl_vars['manufacturer']->index === 0;
 $_smarty_tpl->tpl_vars['manufacturer']->last = $_smarty_tpl->tpl_vars['manufacturer']->iteration === $_smarty_tpl->tpl_vars['manufacturer']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['manufacturers']['first'] = $_smarty_tpl->tpl_vars['manufacturer']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['manufacturers']['last'] = $_smarty_tpl->tpl_vars['manufacturer']->last;
?>
		<li data-corners="false" data-shadow="false" data-iconshadow="true" data-inline="false" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="clearfix <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['manufacturers']['first']){?>first_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['manufacturers']['last']){?>last_item<?php }else{ ?>item<?php }?>"> 
		<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getmanufacturerLink($_smarty_tpl->tpl_vars['manufacturer']->value['id_manufacturer'],$_smarty_tpl->tpl_vars['manufacturer']->value['link_rewrite']), 'htmlall', 'UTF-8');?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['manufacturer']->value['name'], 'htmlall', 'UTF-8');?>
" class="lnk_img" data-ajax="false"><?php }?>
			<img src="<?php echo $_smarty_tpl->tpl_vars['img_manu_dir']->value;?>
<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['manufacturer']->value['image'], 'htmlall', 'UTF-8');?>
-medium_default.jpg" alt="" width="80" />
			<h3><?php echo smarty_modifier_escape($_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['manufacturer']->value['name'],60,'...'), 'htmlall', 'UTF-8');?>
</h3>
			<p>
				<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']==1){?>
					<?php echo smartyTranslate(array('s'=>'%d product','sprintf'=>intval($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products'])),$_smarty_tpl);?>

				<?php }else{ ?>
					<?php echo smartyTranslate(array('s'=>'%d products','sprintf'=>intval($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products'])),$_smarty_tpl);?>

				<?php }?>
			</p>
		<?php if ($_smarty_tpl->tpl_vars['manufacturer']->value['nb_products']>0){?></a><?php }?>
		</li>
	<?php } ?>
	</ul>
	<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?>
<?php }?>
	<?php echo $_smarty_tpl->getSubTemplate ('./sitemap.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div><!-- #content -->
<?php }} ?>