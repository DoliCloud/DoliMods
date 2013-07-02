<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:13
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockbestsellers/blockbestsellers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:70304844751c1c0eddee905-31852420%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fe75ac0249a639915f5f0694ab162c3bfa703a2c' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockbestsellers/blockbestsellers.tpl',
      1 => 1371647519,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '70304844751c1c0eddee905-31852420',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'best_sellers' => 0,
    'product' => 0,
    'smallSize' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0edeb7822_26993626',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0edeb7822_26993626')) {function content_51c1c0edeb7822_26993626($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<!-- MODULE Block best sellers -->
<div id="best-sellers_block_right" class="block products_block">
	<p class="title_block"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('best-sales');?>
"><?php echo smartyTranslate(array('s'=>'Top sellers','mod'=>'blockbestsellers'),$_smarty_tpl);?>
</a></p>
	<div class="block_content">
	<?php if (count($_smarty_tpl->tpl_vars['best_sellers']->value)>0){?>
		<ul class="product_images">
			<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['best_sellers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['product']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['product']->iteration=0;
 $_smarty_tpl->tpl_vars['product']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['product']->iteration++;
 $_smarty_tpl->tpl_vars['product']->index++;
 $_smarty_tpl->tpl_vars['product']->first = $_smarty_tpl->tpl_vars['product']->index === 0;
 $_smarty_tpl->tpl_vars['product']->last = $_smarty_tpl->tpl_vars['product']->iteration === $_smarty_tpl->tpl_vars['product']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['first'] = $_smarty_tpl->tpl_vars['product']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['iteration']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['last'] = $_smarty_tpl->tpl_vars['product']->last;
?>
			<li class="<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['first']){?>first_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['last']){?>last_item<?php }else{ ?>item<?php }?> clearfix">
				<a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['link'];?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['legend'], 'htmlall', 'UTF-8');?>
" class="content_img clearfix">
					<span class="number"><?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['iteration'];?>
</span>
					<img src="<?php echo $_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['id_image'],'small_default');?>
" height="<?php echo $_smarty_tpl->tpl_vars['smallSize']->value['height'];?>
" width="<?php echo $_smarty_tpl->tpl_vars['smallSize']->value['width'];?>
" alt="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['legend'], 'htmlall', 'UTF-8');?>
" />
				
				</a>
				<p><a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['link'];?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['legend'], 'htmlall', 'UTF-8');?>
">
					<?php echo smarty_modifier_escape(strip_tags($_smarty_tpl->tpl_vars['product']->value['name']), 'htmlall', 'UTF-8');?>
<br />
					<span class="price"><?php echo $_smarty_tpl->tpl_vars['product']->value['price'];?>
</span>
				</a></p>
			</li>
		<?php } ?>
		</ul>
		<p class="lnk"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('best-sales');?>
" title="<?php echo smartyTranslate(array('s'=>'All best sellers','mod'=>'blockbestsellers'),$_smarty_tpl);?>
" class="button_large">&raquo; <?php echo smartyTranslate(array('s'=>'All best sellers','mod'=>'blockbestsellers'),$_smarty_tpl);?>
</a></p>
	<?php }else{ ?>
		<p><?php echo smartyTranslate(array('s'=>'No best sellers at this time','mod'=>'blockbestsellers'),$_smarty_tpl);?>
</p>
	<?php }?>
	</div>
</div>
<!-- /MODULE Block best sellers -->
<?php }} ?>