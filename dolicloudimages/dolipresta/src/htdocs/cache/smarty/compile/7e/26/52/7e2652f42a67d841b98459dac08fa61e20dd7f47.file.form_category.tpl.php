<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:06
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/helpers/form/form_category.tpl" */ ?>
<?php /*%%SmartyHeaderCode:144737090651c1c0e6723ae8-76864131%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7e2652f42a67d841b98459dac08fa61e20dd7f47' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/helpers/form/form_category.tpl',
      1 => 1371647812,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '144737090651c1c0e6723ae8-76864131',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'categories' => 0,
    'cat' => 0,
    'home_is_selected' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e6899469_91191846',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e6899469_91191846')) {function content_51c1c0e6899469_91191846($_smarty_tpl) {?><?php if (!is_callable('smarty_function_implode')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/function.implode.php';
?>
<?php if (count($_smarty_tpl->tpl_vars['categories']->value)&&isset($_smarty_tpl->tpl_vars['categories']->value)){?>
	<script type="text/javascript">
		var inputName = '<?php echo $_smarty_tpl->tpl_vars['categories']->value['input_name'];?>
';
		var use_radio = <?php if ($_smarty_tpl->tpl_vars['categories']->value['use_radio']){?>1<?php }else{ ?>0<?php }?>;
		var selectedCat = '<?php echo smarty_function_implode(array('value'=>$_smarty_tpl->tpl_vars['categories']->value['selected_cat']),$_smarty_tpl);?>
';
		var selectedLabel = '<?php echo $_smarty_tpl->tpl_vars['categories']->value['trads']['selected'];?>
';
		var home = '<?php echo $_smarty_tpl->tpl_vars['categories']->value['trads']['Root']['name'];?>
';
		var use_radio = <?php if ($_smarty_tpl->tpl_vars['categories']->value['use_radio']){?>1<?php }else{ ?>0<?php }?>;
		var use_context = <?php if (isset($_smarty_tpl->tpl_vars['categories']->value['use_context'])){?>1<?php }else{ ?>0<?php }?>;
		$(document).ready(function(){
			buildTreeView(use_context);
		});
	</script>

	<div class="category-filter">
		<span><a href="#" id="collapse_all" ><?php echo $_smarty_tpl->tpl_vars['categories']->value['trads']['Collapse All'];?>
</a>
		 |</span>
		 <span><a href="#" id="expand_all" ><?php echo $_smarty_tpl->tpl_vars['categories']->value['trads']['Expand All'];?>
</a>
		<?php if (!$_smarty_tpl->tpl_vars['categories']->value['use_radio']){?>
		 |</span>
		 <span></span><a href="#" id="check_all" ><?php echo $_smarty_tpl->tpl_vars['categories']->value['trads']['Check All'];?>
</a>
		 |</span>
		 <span></span><a href="#" id="uncheck_all" ><?php echo $_smarty_tpl->tpl_vars['categories']->value['trads']['Uncheck All'];?>
</a></span>
		 <?php }?>
		<?php if ($_smarty_tpl->tpl_vars['categories']->value['use_search']){?>
			<span style="margin-left:20px">
				<?php echo $_smarty_tpl->tpl_vars['categories']->value['trads']['search'];?>
 :
				<form method="post" id="filternameForm">
					<input type="text" name="search_cat" id="search_cat">
				</form>
			</span>
		<?php }?>
	</div>

	<?php $_smarty_tpl->tpl_vars['home_is_selected'] = new Smarty_variable(false, null, 0);?>

	<?php  $_smarty_tpl->tpl_vars['cat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cat']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categories']->value['selected_cat']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cat']->key => $_smarty_tpl->tpl_vars['cat']->value){
$_smarty_tpl->tpl_vars['cat']->_loop = true;
?>
		<?php if (is_array($_smarty_tpl->tpl_vars['cat']->value)){?>
			<?php if ($_smarty_tpl->tpl_vars['cat']->value['id_category']!=$_smarty_tpl->tpl_vars['categories']->value['trads']['Root']['id_category']){?>
				<input <?php if (in_array($_smarty_tpl->tpl_vars['cat']->value['id_category'],$_smarty_tpl->tpl_vars['categories']->value['disabled_categories'])){?>disabled="disabled"<?php }?> type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['categories']->value['input_name'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['cat']->value['id_category'];?>
" >
			<?php }else{ ?>
				<?php $_smarty_tpl->tpl_vars['home_is_selected'] = new Smarty_variable(true, null, 0);?>
			<?php }?>
		<?php }else{ ?>
			<?php if ($_smarty_tpl->tpl_vars['cat']->value!=$_smarty_tpl->tpl_vars['categories']->value['trads']['Root']['id_category']){?>
				<input <?php if (in_array($_smarty_tpl->tpl_vars['cat']->value,$_smarty_tpl->tpl_vars['categories']->value['disabled_categories'])){?>disabled="disabled"<?php }?> type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['categories']->value['input_name'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['cat']->value;?>
" >
			<?php }else{ ?>
				<?php $_smarty_tpl->tpl_vars['home_is_selected'] = new Smarty_variable(true, null, 0);?>
			<?php }?>
		<?php }?>
	<?php } ?>
	<ul id="categories-treeview" class="filetree">
		<li id="<?php echo $_smarty_tpl->tpl_vars['categories']->value['trads']['Root']['id_category'];?>
" class="hasChildren">
			<span class="folder">
				<?php if ($_smarty_tpl->tpl_vars['categories']->value['top_category']->id!=$_smarty_tpl->tpl_vars['categories']->value['trads']['Root']['id_category']){?>
					<input type="<?php if (!$_smarty_tpl->tpl_vars['categories']->value['use_radio']){?>checkbox<?php }else{ ?>radio<?php }?>"
							name="<?php echo $_smarty_tpl->tpl_vars['categories']->value['input_name'];?>
"
							value="<?php echo $_smarty_tpl->tpl_vars['categories']->value['trads']['Root']['id_category'];?>
"
							<?php if ($_smarty_tpl->tpl_vars['home_is_selected']->value){?>checked<?php }?>
							onclick="clickOnCategoryBox($(this));" />
						<span class="category_label"><?php echo $_smarty_tpl->tpl_vars['categories']->value['trads']['Root']['name'];?>
</span>
				<?php }else{ ?>
					&nbsp;
				<?php }?>
			</span>
			<ul>
				<li><span class="placeholder">&nbsp;</span></li>
		  	</ul>
		</li>
	</ul>
	<?php if ($_smarty_tpl->tpl_vars['categories']->value['use_radio']){?>
	<script type="text/javascript">
		searchCategory();
	</script>
	<?php }?>
<?php }?>
<?php }} ?>