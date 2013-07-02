<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:15
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockmanufacturer/blockmanufacturer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:168546363451c1c0ef3a4fa5-06022044%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b7943261c49f43ced9c85c1b539e2ebafebf2f92' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockmanufacturer/blockmanufacturer.tpl',
      1 => 1371647523,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '168546363451c1c0ef3a4fa5-06022044',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'display_link_manufacturer' => 0,
    'link' => 0,
    'manufacturers' => 0,
    'text_list' => 0,
    'text_list_nb' => 0,
    'manufacturer' => 0,
    'form_list' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ef4816f9_45306262',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ef4816f9_45306262')) {function content_51c1c0ef4816f9_45306262($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<!-- Block manufacturers module -->
<div id="manufacturers_block_left" class="block blockmanufacturer">
	<p class="title_block"><?php if ($_smarty_tpl->tpl_vars['display_link_manufacturer']->value){?><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('manufacturer');?>
" title="<?php echo smartyTranslate(array('s'=>'Manufacturers','mod'=>'blockmanufacturer'),$_smarty_tpl);?>
"><?php }?><?php echo smartyTranslate(array('s'=>'Manufacturers','mod'=>'blockmanufacturer'),$_smarty_tpl);?>
<?php if ($_smarty_tpl->tpl_vars['display_link_manufacturer']->value){?></a><?php }?></p>
	<div class="block_content">
<?php if ($_smarty_tpl->tpl_vars['manufacturers']->value){?>
	<?php if ($_smarty_tpl->tpl_vars['text_list']->value){?>
	<ul class="bullet">
	<?php  $_smarty_tpl->tpl_vars['manufacturer'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['manufacturer']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['manufacturers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['manufacturer']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['manufacturer']->iteration=0;
 $_smarty_tpl->tpl_vars['manufacturer']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['manufacturer_list']['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars['manufacturer']->key => $_smarty_tpl->tpl_vars['manufacturer']->value){
$_smarty_tpl->tpl_vars['manufacturer']->_loop = true;
 $_smarty_tpl->tpl_vars['manufacturer']->iteration++;
 $_smarty_tpl->tpl_vars['manufacturer']->index++;
 $_smarty_tpl->tpl_vars['manufacturer']->first = $_smarty_tpl->tpl_vars['manufacturer']->index === 0;
 $_smarty_tpl->tpl_vars['manufacturer']->last = $_smarty_tpl->tpl_vars['manufacturer']->iteration === $_smarty_tpl->tpl_vars['manufacturer']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['manufacturer_list']['first'] = $_smarty_tpl->tpl_vars['manufacturer']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['manufacturer_list']['iteration']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['manufacturer_list']['last'] = $_smarty_tpl->tpl_vars['manufacturer']->last;
?>
		<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['manufacturer_list']['iteration']<=$_smarty_tpl->tpl_vars['text_list_nb']->value){?>
		<li class="<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['manufacturer_list']['last']){?>last_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['manufacturer_list']['first']){?>first_item<?php }else{ ?>item<?php }?>"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getmanufacturerLink($_smarty_tpl->tpl_vars['manufacturer']->value['id_manufacturer'],$_smarty_tpl->tpl_vars['manufacturer']->value['link_rewrite']);?>
" title="<?php echo smartyTranslate(array('s'=>'More about','mod'=>'blockmanufacturer'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['name'];?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['manufacturer']->value['name'], 'htmlall', 'UTF-8');?>
</a></li>
		<?php }?>
	<?php } ?>
	</ul>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['form_list']->value){?>
		<form action="<?php echo smarty_modifier_escape($_SERVER['SCRIPT_NAME'], 'htmlall', 'UTF-8');?>
" method="get">
			<p>
				<select id="manufacturer_list" onchange="autoUrl('manufacturer_list', '');">
					<option value="0"><?php echo smartyTranslate(array('s'=>'All manufacturers','mod'=>'blockmanufacturer'),$_smarty_tpl);?>
</option>
				<?php  $_smarty_tpl->tpl_vars['manufacturer'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['manufacturer']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['manufacturers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['manufacturer']->key => $_smarty_tpl->tpl_vars['manufacturer']->value){
$_smarty_tpl->tpl_vars['manufacturer']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['link']->value->getmanufacturerLink($_smarty_tpl->tpl_vars['manufacturer']->value['id_manufacturer'],$_smarty_tpl->tpl_vars['manufacturer']->value['link_rewrite']);?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['manufacturer']->value['name'], 'htmlall', 'UTF-8');?>
</option>
				<?php } ?>
				</select>
			</p>
		</form>
	<?php }?>
<?php }else{ ?>
	<p><?php echo smartyTranslate(array('s'=>'No manufacturer','mod'=>'blockmanufacturer'),$_smarty_tpl);?>
</p>
<?php }?>
	</div>
</div>
<!-- /Block manufacturers module -->
<?php }} ?>