<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:58
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/cart_rules/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:113982127951c1c0dee29272-03070282%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '540a4891feebf269f6652cc341bdcb10ccce8f45' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/cart_rules/form.tpl',
      1 => 1371647765,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '113982127951c1c0dee29272-03070282',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'currentIndex' => 0,
    'currentToken' => 0,
    'currentObject' => 0,
    'table' => 0,
    'product_rule_groups_counter' => 0,
    'languages' => 0,
    'k' => 0,
    'language' => 0,
    'id_lang_default' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0deefb121_96469034',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0deefb121_96469034')) {function content_51c1c0deefb121_96469034($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<div class="leadin"></div>

<div>
 	<div class="productTabs">
		<ul class="tab">
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_informations" href="javascript:displayCartRuleTab('informations');"><?php echo smartyTranslate(array('s'=>'Information'),$_smarty_tpl);?>
</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_conditions" href="javascript:displayCartRuleTab('conditions');"><?php echo smartyTranslate(array('s'=>'Conditions'),$_smarty_tpl);?>
</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_actions" href="javascript:displayCartRuleTab('actions');"><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
</a>
			</li>
		</ul>
	</div>
</div>
<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentToken']->value, ENT_QUOTES, 'UTF-8', true);?>
&addcart_rule" id="cart_rule_form" method="post">
	<?php if ($_smarty_tpl->tpl_vars['currentObject']->value->id){?><input type="hidden" name="id_cart_rule" value="<?php echo intval($_smarty_tpl->tpl_vars['currentObject']->value->id);?>
" /><?php }?>
	<input type="hidden" id="currentFormTab" name="currentFormTab" value="informations" />
	<div id="cart_rule_informations" class="cart_rule_tab">
		<h4><?php echo smartyTranslate(array('s'=>'Cart-rule information'),$_smarty_tpl);?>
</h4>
		<div class="separation"></div>
		<?php echo $_smarty_tpl->getSubTemplate ('controllers/cart_rules/informations.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</div>
	<div id="cart_rule_conditions" class="cart_rule_tab">
		<h4><?php echo smartyTranslate(array('s'=>'Cart-rule conditions'),$_smarty_tpl);?>
</h4>
		<div class="separation"></div>
		<?php echo $_smarty_tpl->getSubTemplate ('controllers/cart_rules/conditions.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</div>
	<div id="cart_rule_actions" class="cart_rule_tab">
		<h4><?php echo smartyTranslate(array('s'=>'Cart-rule actions'),$_smarty_tpl);?>
</h4>
		<div class="separation"></div>
		<?php echo $_smarty_tpl->getSubTemplate ('controllers/cart_rules/actions.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</div>
	<div class="separation"></div>
	<div style="text-align:center">
		<input type="submit" value="<?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
" class="button" name="submitAddcart_rule" id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['table']->value, ENT_QUOTES, 'UTF-8', true);?>
_form_submit_btn" />
		<!--<input type="submit" value="<?php echo smartyTranslate(array('s'=>'Save and stay'),$_smarty_tpl);?>
" class="button" name="submitAddcart_ruleAndStay" id="" />-->
	</div>
</form>
<script type="text/javascript">
	var product_rule_groups_counter = <?php if (isset($_smarty_tpl->tpl_vars['product_rule_groups_counter']->value)){?><?php echo intval($_smarty_tpl->tpl_vars['product_rule_groups_counter']->value);?>
<?php }else{ ?>0<?php }?>;
	var product_rule_counters = new Array();
	var currentToken = '<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['currentToken']->value);?>
';
	var currentFormTab = '<?php if (isset($_POST['currentFormTab'])){?><?php echo preg_replace("%(?<!\\\\)'%", "\'",$_POST['currentFormTab']);?>
<?php }else{ ?>informations<?php }?>';
	
	var languages = new Array();
	<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>
		languages[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
] = {
			id_lang: <?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
,
			iso_code: '<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['language']->value['iso_code']);?>
',
			name: '<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['language']->value['name']);?>
'
		};
	<?php } ?>
	displayFlags(languages, <?php echo $_smarty_tpl->tpl_vars['id_lang_default']->value;?>
);
</script>
<script type="text/javascript" src="themes/default/template/controllers/cart_rules/form.js"></script><?php }} ?>