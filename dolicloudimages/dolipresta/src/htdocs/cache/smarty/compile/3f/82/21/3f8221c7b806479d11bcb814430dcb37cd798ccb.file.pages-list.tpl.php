<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:11
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/pages-list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:49409480351c1c0eb89f2e0-18971645%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3f8221c7b806479d11bcb814430dcb37cd798ccb' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/pages-list.tpl',
      1 => 1371647175,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '49409480351c1c0eb89f2e0-18971645',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'controller_name' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0eb9036c1_78423921',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0eb9036c1_78423921')) {function content_51c1c0eb9036c1_78423921($_smarty_tpl) {?>

		<hr/>
		<h2 class="site_map"><?php echo smartyTranslate(array('s'=>'Sitemap'),$_smarty_tpl);?>
</h2>
		<ul data-role="listview" data-inset="true" id="category">
			<?php if ($_smarty_tpl->tpl_vars['controller_name']->value!='index'){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('index',true);?>
" data-ajax="false">Accueil</a></li><?php }?>
			
			
			
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getCategoryLink(3,false);?>
" data-ajax="false">IPod</a></li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getCategoryLink(4,false);?>
" data-ajax="false">Accessoires</a></li>
			
			
			<?php if ($_smarty_tpl->tpl_vars['controller_name']->value!='my-account'){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
" data-ajax="false"><?php echo smartyTranslate(array('s'=>'My account'),$_smarty_tpl);?>
</a></li><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['controller_name']->value!='contact'){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true);?>
" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Contact'),$_smarty_tpl);?>
</a></li><?php }?>
		</ul>
<?php }} ?>