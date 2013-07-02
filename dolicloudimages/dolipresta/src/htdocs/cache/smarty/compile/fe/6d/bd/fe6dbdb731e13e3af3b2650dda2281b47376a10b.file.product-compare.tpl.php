<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:03
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/product-compare.tpl" */ ?>
<?php /*%%SmartyHeaderCode:95840269751c1c0e3b9d4f9-94402429%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fe6dbdb731e13e3af3b2650dda2281b47376a10b' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/product-compare.tpl',
      1 => 1371646833,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '95840269751c1c0e3b9d4f9-94402429',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'comparator_max_item' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e3bc3924_21719333',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e3bc3924_21719333')) {function content_51c1c0e3bc3924_21719333($_smarty_tpl) {?>

<?php if ($_smarty_tpl->tpl_vars['comparator_max_item']->value){?>
<script type="text/javascript">
// <![CDATA[
	var min_item = '<?php echo smartyTranslate(array('s'=>'Please select at least one product','js'=>1),$_smarty_tpl);?>
';
	var max_item = "<?php echo smartyTranslate(array('s'=>'You cannot add more than %d product(s) to the product comparison','sprintf'=>$_smarty_tpl->tpl_vars['comparator_max_item']->value,'js'=>1),$_smarty_tpl);?>
";
//]]>
</script>

	<form method="post" action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('products-comparison');?>
" onsubmit="true">
		<p>
		<input type="submit" id="bt_compare" class="button" value="<?php echo smartyTranslate(array('s'=>'Compare'),$_smarty_tpl);?>
" />
		<input type="hidden" name="compare_product_list" class="compare_product_list" value="" />
		</p>
	</form>
<?php }?>

<?php }} ?>