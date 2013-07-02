<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:07
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/store_infos.tpl" */ ?>
<?php /*%%SmartyHeaderCode:142099813251c1c0e7203d20-49009646%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7ca6a957092d1d6a0c0597263ab083735c54a4a3' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/store_infos.tpl',
      1 => 1371646836,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '142099813251c1c0e7203d20-49009646',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'days_datas' => 0,
    'one_day' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e7223d23_87776653',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e7223d23_87776653')) {function content_51c1c0e7223d23_87776653($_smarty_tpl) {?>

<br />
<br />
<span id="store_hours"><?php echo smartyTranslate(array('s'=>'working hours'),$_smarty_tpl);?>
</span>
<table style="font-size: 9px;">
	<?php  $_smarty_tpl->tpl_vars['one_day'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['one_day']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['days_datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['one_day']->key => $_smarty_tpl->tpl_vars['one_day']->value){
$_smarty_tpl->tpl_vars['one_day']->_loop = true;
?>
	<tr>
		<td style="width: 70px;"><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['one_day']->value['day']),$_smarty_tpl);?>
</td><td><?php echo $_smarty_tpl->tpl_vars['one_day']->value['hours'];?>
</td>
	</tr>
	<?php } ?>
</table>
<?php }} ?>