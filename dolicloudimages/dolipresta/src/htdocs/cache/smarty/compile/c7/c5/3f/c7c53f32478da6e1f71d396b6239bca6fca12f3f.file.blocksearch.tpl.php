<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:15
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blocksearch/blocksearch.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15182569551c1c0ef874712-47860798%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c7c53f32478da6e1f71d396b6239bca6fca12f3f' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blocksearch/blocksearch.tpl',
      1 => 1371647526,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15182569551c1c0ef874712-47860798',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'ENT_QUOTES' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ef8b7956_54903223',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ef8b7956_54903223')) {function content_51c1c0ef8b7956_54903223($_smarty_tpl) {?>

<!-- Block search module -->
<div id="search_block_left" class="block exclusive">
	<p class="title_block"><?php echo smartyTranslate(array('s'=>'Search','mod'=>'blocksearch'),$_smarty_tpl);?>
</p>
	<form method="get" action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('search',true);?>
" id="searchbox">
		<p class="block_content">
			<label for="search_query_block"><?php echo smartyTranslate(array('s'=>'Enter a product name','mod'=>'blocksearch'),$_smarty_tpl);?>
</label>
			<input type="hidden" name="orderby" value="position" />
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="orderway" value="desc" />
			<input class="search_query" type="text" id="search_query_block" name="search_query" value="<?php if (isset($_GET['search_query'])){?><?php echo stripslashes(htmlentities($_GET['search_query'],$_smarty_tpl->tpl_vars['ENT_QUOTES']->value,'utf-8'));?>
<?php }?>" />
			<input type="submit" id="search_button" class="button_mini" value="<?php echo smartyTranslate(array('s'=>'go','mod'=>'blocksearch'),$_smarty_tpl);?>
" />
		</p>
	</form>
</div>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['self']->value)."/blocksearch-instantsearch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- /Block search module -->
<?php }} ?>