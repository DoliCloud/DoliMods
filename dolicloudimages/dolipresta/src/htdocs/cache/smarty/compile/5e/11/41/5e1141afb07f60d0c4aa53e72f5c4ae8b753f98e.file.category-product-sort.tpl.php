<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:08
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/category-product-sort.tpl" */ ?>
<?php /*%%SmartyHeaderCode:90399538251c1c0e85b5ee8-68806576%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5e1141afb07f60d0c4aa53e72f5c4ae8b753f98e' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/category-product-sort.tpl',
      1 => 1371647168,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '90399538251c1c0e85b5ee8-68806576',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'orderby' => 0,
    'orderway' => 0,
    'sort_already_display' => 0,
    'category' => 0,
    'link' => 0,
    'manufacturer' => 0,
    'supplier' => 0,
    'request' => 0,
    'container_class' => 0,
    'orderbydefault' => 0,
    'orderwaydefault' => 0,
    'PS_CATALOG_MODE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e8715605_30793835',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e8715605_30793835')) {function content_51c1c0e8715605_30793835($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>
<?php if (isset($_smarty_tpl->tpl_vars['orderby']->value)&&isset($_smarty_tpl->tpl_vars['orderway']->value)){?>
	<?php if (!isset($_smarty_tpl->tpl_vars['sort_already_display']->value)){?> 
		<?php $_smarty_tpl->tpl_vars['sort_already_display'] = new Smarty_variable('true', null, 3);
$_ptr = $_smarty_tpl->parent; while ($_ptr != null) {$_ptr->tpl_vars['sort_already_display'] = clone $_smarty_tpl->tpl_vars['sort_already_display']; $_ptr = $_ptr->parent; }
Smarty::$global_tpl_vars['sort_already_display'] = clone $_smarty_tpl->tpl_vars['sort_already_display'];?>
		<!-- Sort products -->
		<?php if (isset($_GET['id_category'])&&$_GET['id_category']){?>
			<?php $_smarty_tpl->tpl_vars['request'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('category',$_smarty_tpl->tpl_vars['category']->value,false,true), null, 0);?>
		<?php }elseif(isset($_GET['id_manufacturer'])&&$_GET['id_manufacturer']){?>
			<?php $_smarty_tpl->tpl_vars['request'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('manufacturer',$_smarty_tpl->tpl_vars['manufacturer']->value,false,true), null, 0);?>
		<?php }elseif(isset($_GET['id_supplier'])&&$_GET['id_supplier']){?>
			<?php $_smarty_tpl->tpl_vars['request'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('supplier',$_smarty_tpl->tpl_vars['supplier']->value,false,true), null, 0);?>
		<?php }else{ ?>
			<?php $_smarty_tpl->tpl_vars['request'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink(false,false,false,true), null, 0);?>
		<?php }?>
		
		<script type="text/javascript">
		//<![CDATA[
		$(document).ready(function()
		{
			$('.selectPrductSort').change(function()
			{
				var requestSortProducts = '<?php echo $_smarty_tpl->tpl_vars['request']->value;?>
';
				var splitData = $(this).val().split(':');
					document.location.href = requestSortProducts + ((requestSortProducts.indexOf('?') < 0) ? '?' : '&') + 'orderby=' + splitData[0] + '&orderway=' + splitData[1];
			});
		});
		//]]>
		</script>
	<?php }?>

	<div class="<?php echo $_smarty_tpl->tpl_vars['container_class']->value;?>
">
		<form id="productsSortForm" action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['request']->value, 'htmlall', 'UTF-8');?>
">
			<select class="selectPrductSort">
				<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['orderbydefault']->value, 'htmlall', 'UTF-8');?>
:<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['orderwaydefault']->value, 'htmlall', 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['orderby']->value==$_smarty_tpl->tpl_vars['orderbydefault']->value){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Sort by'),$_smarty_tpl);?>
</option>
				<?php if (!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
					<option value="price:asc" <?php if ($_smarty_tpl->tpl_vars['orderby']->value=='price'&&$_smarty_tpl->tpl_vars['orderway']->value=='asc'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Price: Lowest first'),$_smarty_tpl);?>
</option>
					<option value="price:desc" <?php if ($_smarty_tpl->tpl_vars['orderby']->value=='price'&&$_smarty_tpl->tpl_vars['orderway']->value=='desc'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Price: Highest first'),$_smarty_tpl);?>
</option>
				<?php }?>
				<option value="name:asc" <?php if ($_smarty_tpl->tpl_vars['orderby']->value=='name'&&$_smarty_tpl->tpl_vars['orderway']->value=='asc'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Product Name: A to Z'),$_smarty_tpl);?>
</option>
				<option value="name:desc" <?php if ($_smarty_tpl->tpl_vars['orderby']->value=='name'&&$_smarty_tpl->tpl_vars['orderway']->value=='desc'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Product Name: Z to A'),$_smarty_tpl);?>
</option>
				<?php if (!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
					<option value="quantity:desc" <?php if ($_smarty_tpl->tpl_vars['orderby']->value=='quantity'&&$_smarty_tpl->tpl_vars['orderway']->value=='desc'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'In stock'),$_smarty_tpl);?>
</option>
				<?php }?>
			</select>
		</form>
	</div> <!-- .<?php echo $_smarty_tpl->tpl_vars['container_class']->value;?>
 -->
<!-- /Sort products -->
<?php }?>
<?php }} ?>