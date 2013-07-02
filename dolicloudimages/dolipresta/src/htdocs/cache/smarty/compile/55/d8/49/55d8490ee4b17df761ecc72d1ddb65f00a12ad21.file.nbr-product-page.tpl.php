<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:59
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/nbr-product-page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:99269149051c1c0dfd92455-40033650%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '55d8490ee4b17df761ecc72d1ddb65f00a12ad21' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/nbr-product-page.tpl',
      1 => 1371646828,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '99269149051c1c0dfd92455-40033650',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'p' => 0,
    'category' => 0,
    'link' => 0,
    'manufacturer' => 0,
    'supplier' => 0,
    'nb_products' => 0,
    'products_per_page' => 0,
    'requestNb' => 0,
    'search_query' => 0,
    'tag' => 0,
    'requestKey' => 0,
    'requestValue' => 0,
    'nArray' => 0,
    'lastnValue' => 0,
    'nValue' => 0,
    'n' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e0037ee1_61915310',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e0037ee1_61915310')) {function content_51c1c0e0037ee1_61915310($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php if (isset($_smarty_tpl->tpl_vars['p']->value)&&$_smarty_tpl->tpl_vars['p']->value){?>
	<?php if (isset($_GET['id_category'])&&$_GET['id_category']&&isset($_smarty_tpl->tpl_vars['category']->value)){?>
		<?php $_smarty_tpl->tpl_vars['requestPage'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('category',$_smarty_tpl->tpl_vars['category']->value,false,false,true,false), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['requestNb'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('category',$_smarty_tpl->tpl_vars['category']->value,true,false,false,true), null, 0);?>
	<?php }elseif(isset($_GET['id_manufacturer'])&&$_GET['id_manufacturer']&&isset($_smarty_tpl->tpl_vars['manufacturer']->value)){?>
		<?php $_smarty_tpl->tpl_vars['requestPage'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('manufacturer',$_smarty_tpl->tpl_vars['manufacturer']->value,false,false,true,false), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['requestNb'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('manufacturer',$_smarty_tpl->tpl_vars['manufacturer']->value,true,false,false,true), null, 0);?>
	<?php }elseif(isset($_GET['id_supplier'])&&$_GET['id_supplier']&&isset($_smarty_tpl->tpl_vars['supplier']->value)){?>
		<?php $_smarty_tpl->tpl_vars['requestPage'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('supplier',$_smarty_tpl->tpl_vars['supplier']->value,false,false,true,false), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['requestNb'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('supplier',$_smarty_tpl->tpl_vars['supplier']->value,true,false,false,true), null, 0);?>
	<?php }else{ ?>
		<?php $_smarty_tpl->tpl_vars['requestPage'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink(false,false,false,false,true,false), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['requestNb'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink(false,false,true,false,false,true), null, 0);?>
	<?php }?>
	<!-- nbr product/page -->
	<?php if ($_smarty_tpl->tpl_vars['nb_products']->value>$_smarty_tpl->tpl_vars['products_per_page']->value){?>
		<form action="<?php if (!is_array($_smarty_tpl->tpl_vars['requestNb']->value)){?><?php echo $_smarty_tpl->tpl_vars['requestNb']->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['requestNb']->value['requestUrl'];?>
<?php }?>" method="get" class="nbrItemPage pagination">
			<p>
				<?php if (isset($_smarty_tpl->tpl_vars['search_query']->value)&&$_smarty_tpl->tpl_vars['search_query']->value){?><input type="hidden" name="search_query" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['search_query']->value, 'htmlall', 'UTF-8');?>
" /><?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['tag']->value)&&$_smarty_tpl->tpl_vars['tag']->value&&!is_array($_smarty_tpl->tpl_vars['tag']->value)){?><input type="hidden" name="tag" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['tag']->value, 'htmlall', 'UTF-8');?>
" /><?php }?>
				<label for="nb_item"><?php echo smartyTranslate(array('s'=>'Show'),$_smarty_tpl);?>
</label>
				<?php if (is_array($_smarty_tpl->tpl_vars['requestNb']->value)){?>
					<?php  $_smarty_tpl->tpl_vars['requestValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['requestValue']->_loop = false;
 $_smarty_tpl->tpl_vars['requestKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['requestNb']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['requestValue']->key => $_smarty_tpl->tpl_vars['requestValue']->value){
$_smarty_tpl->tpl_vars['requestValue']->_loop = true;
 $_smarty_tpl->tpl_vars['requestKey']->value = $_smarty_tpl->tpl_vars['requestValue']->key;
?>
						<?php if ($_smarty_tpl->tpl_vars['requestKey']->value!='requestUrl'){?>
							<input type="hidden" name="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['requestKey']->value, 'htmlall', 'UTF-8');?>
" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['requestValue']->value, 'htmlall', 'UTF-8');?>
" />
						<?php }?>
					<?php } ?>
				<?php }?>
				<select name="n" id="nb_item" onchange="this.form.submit();">
				<?php $_smarty_tpl->tpl_vars["lastnValue"] = new Smarty_variable("0", null, 0);?>
				<?php  $_smarty_tpl->tpl_vars['nValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['nValue']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['nArray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['nValue']->key => $_smarty_tpl->tpl_vars['nValue']->value){
$_smarty_tpl->tpl_vars['nValue']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['lastnValue']->value<=$_smarty_tpl->tpl_vars['nb_products']->value){?>
						<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['nValue']->value, 'htmlall', 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['n']->value==$_smarty_tpl->tpl_vars['nValue']->value){?>selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['nValue']->value, 'htmlall', 'UTF-8');?>
</option>
					<?php }?>
					<?php $_smarty_tpl->tpl_vars["lastnValue"] = new Smarty_variable($_smarty_tpl->tpl_vars['nValue']->value, null, 0);?>
				<?php } ?>
				</select>
				<span><?php echo smartyTranslate(array('s'=>'Products by page'),$_smarty_tpl);?>
</span>
			</p>
		</form>
	<?php }?>
	<!-- /nbr product/page -->
<?php }?>
<?php }} ?>