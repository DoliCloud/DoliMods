<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:13
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockbestsellers/blockbestsellers-home.tpl" */ ?>
<?php /*%%SmartyHeaderCode:44945259151c1c0edebdf67-18522925%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bd32b0bfc766ad692e7341aae8a74a1b08151495' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockbestsellers/blockbestsellers-home.tpl',
      1 => 1371647519,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '44945259151c1c0edebdf67-18522925',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'best_sellers' => 0,
    'nbLi' => 0,
    'nbItemsPerLine' => 0,
    'nbLines' => 0,
    'liHeight' => 0,
    'ulHeight' => 0,
    'product' => 0,
    'link' => 0,
    'homeSize' => 0,
    'PS_CATALOG_MODE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ee0ec5c6_67319553',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ee0ec5c6_67319553')) {function content_51c1c0ee0ec5c6_67319553($_smarty_tpl) {?><?php if (!is_callable('smarty_function_math')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/function.math.php';
if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<!-- MODULE Home Block best sellers -->
<div id="best-sellers_block_center" class="block products_block">
	<p class="title_block"><?php echo smartyTranslate(array('s'=>'Top sellers','mod'=>'blockbestsellers'),$_smarty_tpl);?>
</p>
	<?php if (isset($_smarty_tpl->tpl_vars['best_sellers']->value)&&$_smarty_tpl->tpl_vars['best_sellers']->value){?>
		<div class="block_content">
			<?php $_smarty_tpl->tpl_vars['liHeight'] = new Smarty_variable(320, null, 0);?>
			<?php $_smarty_tpl->tpl_vars['nbItemsPerLine'] = new Smarty_variable(4, null, 0);?>
			<?php $_smarty_tpl->tpl_vars['nbLi'] = new Smarty_variable(count($_smarty_tpl->tpl_vars['best_sellers']->value), null, 0);?>
			<?php echo smarty_function_math(array('equation'=>"nbLi/nbItemsPerLine",'nbLi'=>$_smarty_tpl->tpl_vars['nbLi']->value,'nbItemsPerLine'=>$_smarty_tpl->tpl_vars['nbItemsPerLine']->value,'assign'=>'nbLines'),$_smarty_tpl);?>

			<?php echo smarty_function_math(array('equation'=>"nbLines*liHeight",'nbLines'=>ceil($_smarty_tpl->tpl_vars['nbLines']->value),'liHeight'=>$_smarty_tpl->tpl_vars['liHeight']->value,'assign'=>'ulHeight'),$_smarty_tpl);?>

			<ul style="height:<?php echo $_smarty_tpl->tpl_vars['ulHeight']->value;?>
px;">
			<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['best_sellers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['product']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['product']->iteration=0;
 $_smarty_tpl->tpl_vars['product']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['total'] = $_smarty_tpl->tpl_vars['product']->total;
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
				<li style="border-bottom:0" class="ajax_block_product <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['first']){?>first_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['last']){?>last_item<?php }else{ ?>item<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['iteration']%$_smarty_tpl->tpl_vars['nbItemsPerLine']->value==0){?>last_item_of_line<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['iteration']%$_smarty_tpl->tpl_vars['nbItemsPerLine']->value==1){?>clear<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['iteration']>($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['total']-($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['total']%$_smarty_tpl->tpl_vars['nbItemsPerLine']->value))){?>last_line<?php }?>">
					<p class="s_title_block"><a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['link'];?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['product']->value['name'],32,'...'), 'htmlall', 'UTF-8');?>
"><?php echo smarty_modifier_escape($_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['product']->value['name'],27,'...'), 'htmlall', 'UTF-8');?>
</a></p>
					<div class="product_desc"><a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['link'];?>
" title="<?php echo smartyTranslate(array('s'=>'More','mod'=>'blockbestsellers'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['product']->value['description_short']),130,'...');?>
</a></div>
					<a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['link'];?>
" title="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['name'], 'html', 'UTF-8');?>
" class="product_image"><img src="<?php echo $_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['id_image'],'home_default');?>
" height="<?php echo $_smarty_tpl->tpl_vars['homeSize']->value['height'];?>
" width="<?php echo $_smarty_tpl->tpl_vars['homeSize']->value['width'];?>
" alt="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['name'], 'html', 'UTF-8');?>
" /></a>
					<div>
						<?php if (!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?><p class="price_container"><span class="price"><?php echo $_smarty_tpl->tpl_vars['product']->value['price'];?>
</span></p><?php }else{ ?><div style="height:21px;"></div><?php }?>			
						<a class="button" href="<?php echo $_smarty_tpl->tpl_vars['product']->value['link'];?>
" title="<?php echo smartyTranslate(array('s'=>'View','mod'=>'blockbestsellers'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'View','mod'=>'blockbestsellers'),$_smarty_tpl);?>
</a>
					</div>
				</li>
			<?php } ?>
			</ul>
			<p class="clearfix" style="padding: 5px;"><a style="float:right;" href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('best-sales');?>
" title="<?php echo smartyTranslate(array('s'=>'All best sellers','mod'=>'blockbestsellers'),$_smarty_tpl);?>
" class="button_large"><?php echo smartyTranslate(array('s'=>'All best sellers','mod'=>'blockbestsellers'),$_smarty_tpl);?>
</a></p>
		</div>
	<?php }else{ ?>
		<p><?php echo smartyTranslate(array('s'=>'No best sellers at this time','mod'=>'blockbestsellers'),$_smarty_tpl);?>
</p>
	<?php }?>
	<br class="clear"/>
</div>
<!-- /MODULE Home Block best sellers -->
<?php }} ?>