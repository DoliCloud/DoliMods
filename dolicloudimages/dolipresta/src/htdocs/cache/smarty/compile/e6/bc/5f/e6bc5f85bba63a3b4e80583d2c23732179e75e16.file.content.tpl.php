<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:04
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/shipping/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:136938378351c1c0e4846e15-05484660%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e6bc5f85bba63a3b4e80583d2c23732179e75e16' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/shipping/content.tpl',
      1 => 1371647795,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '136938378351c1c0e4846e15-05484660',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
    'action_fees' => 0,
    'carriers' => 0,
    'carrier' => 0,
    'id_carrier' => 0,
    'carrierSelected' => 0,
    'ranges' => 0,
    'range' => 0,
    'suffix' => 0,
    'zones' => 0,
    'currency' => 0,
    'rangeIdentifier' => 0,
    'zone' => 0,
    'deliveryArray' => 0,
    'price' => 0,
    'table' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e49fc777_35837805',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e49fc777_35837805')) {function content_51c1c0e49fc777_35837805($_smarty_tpl) {?>

<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

<br /><br />
<h2><?php echo smartyTranslate(array('s'=>'Fees by carrier, geographical zone and ranges'),$_smarty_tpl);?>
</h2>
<form action="<?php echo $_smarty_tpl->tpl_vars['action_fees']->value;?>
" id="fees" name="fees" method="post">
	<fieldset>
		<legend><img src="../img/admin/delivery.gif" /><?php echo smartyTranslate(array('s'=>'Fees'),$_smarty_tpl);?>
</legend>
		<?php if (empty($_smarty_tpl->tpl_vars['carriers']->value)){?>
			<?php echo smartyTranslate(array('s'=>'If you only have free carriers, there\'s no need to configure delivery prices.'),$_smarty_tpl);?>

		<?php }else{ ?>
			<b><?php echo smartyTranslate(array('s'=>'Carrier:'),$_smarty_tpl);?>
 </b>
			<select name="id_carrier2" onchange="$('#fees').attr('action', $('#fees').attr('action')+'&id_carrier='+$(this).attr('value')+'#fees'); $('#fees').submit();">
				<?php  $_smarty_tpl->tpl_vars['carrier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['carrier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['carriers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['carrier']->key => $_smarty_tpl->tpl_vars['carrier']->value){
$_smarty_tpl->tpl_vars['carrier']->_loop = true;
?>
					<option value="<?php echo intval($_smarty_tpl->tpl_vars['carrier']->value['id_carrier']);?>
" <?php if ($_smarty_tpl->tpl_vars['carrier']->value['id_carrier']==$_smarty_tpl->tpl_vars['id_carrier']->value){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['carrier']->value['name'];?>
</option>
				<?php } ?>
			</select><br />

			<table class="table space" cellpadding="0" cellspacing="0">
				<tr>
					<th><?php echo smartyTranslate(array('s'=>'Zone / Range'),$_smarty_tpl);?>
</th>
					<?php if (!$_smarty_tpl->tpl_vars['carrierSelected']->value->is_free){?>
						<?php  $_smarty_tpl->tpl_vars['range'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['range']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ranges']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['range']->key => $_smarty_tpl->tpl_vars['range']->value){
$_smarty_tpl->tpl_vars['range']->_loop = true;
?>
							<th style="font-size: 11px;"><?php echo floatval($_smarty_tpl->tpl_vars['range']->value['delimiter1']);?>
<?php echo $_smarty_tpl->tpl_vars['suffix']->value;?>
 <?php echo smartyTranslate(array('s'=>'to'),$_smarty_tpl);?>
 <?php echo floatval($_smarty_tpl->tpl_vars['range']->value['delimiter2']);?>
<?php echo $_smarty_tpl->tpl_vars['suffix']->value;?>
</th>
						<?php } ?>
					<?php }?>
				</tr>
				<?php if (sizeof($_smarty_tpl->tpl_vars['ranges']->value)&&!$_smarty_tpl->tpl_vars['carrierSelected']->value->is_free){?>
					<?php if (sizeof($_smarty_tpl->tpl_vars['zones']->value)>1){?>
						<tr>
							<th style="height: 30px;"><?php echo smartyTranslate(array('s'=>'All'),$_smarty_tpl);?>
</th>
							<?php  $_smarty_tpl->tpl_vars['range'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['range']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ranges']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['range']->key => $_smarty_tpl->tpl_vars['range']->value){
$_smarty_tpl->tpl_vars['range']->_loop = true;
?>
								<td class="center">
									<?php echo $_smarty_tpl->tpl_vars['currency']->value->getSign('left');?>

									<input type="text" id="fees_all_<?php echo $_smarty_tpl->tpl_vars['range']->value[$_smarty_tpl->tpl_vars['rangeIdentifier']->value];?>
" onchange="this.value = this.value.replace(/,/g, '.');" onkeyup="if ((event.keyCode||event.which) != 9){ spreadFees(<?php echo $_smarty_tpl->tpl_vars['range']->value[$_smarty_tpl->tpl_vars['rangeIdentifier']->value];?>
)}" style="width: 45px;" />
									<?php echo $_smarty_tpl->tpl_vars['currency']->value->getSign('right');?>
 <?php echo smartyTranslate(array('s'=>'(tax excl.)'),$_smarty_tpl);?>

								</td>
							<?php } ?>
						</tr>
					<?php }?>
		
					<?php  $_smarty_tpl->tpl_vars['zone'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['zone']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['zones']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['zone']->key => $_smarty_tpl->tpl_vars['zone']->value){
$_smarty_tpl->tpl_vars['zone']->_loop = true;
?>
						<tr>
							<th style="height: 30px;"><?php echo $_smarty_tpl->tpl_vars['zone']->value['name'];?>
</th>
							<?php  $_smarty_tpl->tpl_vars['range'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['range']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ranges']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['range']->key => $_smarty_tpl->tpl_vars['range']->value){
$_smarty_tpl->tpl_vars['range']->_loop = true;
?>
								<?php if (isset($_smarty_tpl->tpl_vars['deliveryArray']->value[$_smarty_tpl->tpl_vars['zone']->value['id_zone']][$_smarty_tpl->tpl_vars['id_carrier']->value][$_smarty_tpl->tpl_vars['range']->value[$_smarty_tpl->tpl_vars['rangeIdentifier']->value]])){?>
									<?php $_smarty_tpl->tpl_vars['price'] = new Smarty_variable($_smarty_tpl->tpl_vars['deliveryArray']->value[$_smarty_tpl->tpl_vars['zone']->value['id_zone']][$_smarty_tpl->tpl_vars['id_carrier']->value][$_smarty_tpl->tpl_vars['range']->value[$_smarty_tpl->tpl_vars['rangeIdentifier']->value]], null, 0);?>
								<?php }else{ ?>
									<?php $_smarty_tpl->tpl_vars['price'] = new Smarty_variable('0.00', null, 0);?>
								<?php }?>
								<td class="center">
									<?php echo $_smarty_tpl->tpl_vars['currency']->value->getSign('left');?>

									<input 
										type="text" 
										class="fees_<?php echo $_smarty_tpl->tpl_vars['range']->value[$_smarty_tpl->tpl_vars['rangeIdentifier']->value];?>
" 
										onchange="this.value = this.value.replace(/,/g, '.');" name="fees_<?php echo $_smarty_tpl->tpl_vars['zone']->value['id_zone'];?>
_<?php echo $_smarty_tpl->tpl_vars['range']->value[$_smarty_tpl->tpl_vars['rangeIdentifier']->value];?>
" onkeyup="clearAllFees(<?php echo $_smarty_tpl->tpl_vars['range']->value[$_smarty_tpl->tpl_vars['rangeIdentifier']->value];?>
)" 
										value="<?php echo sprintf("%.6f",$_smarty_tpl->tpl_vars['price']->value);?>
"
										style="width: 45px;" 
									/>
									<?php echo $_smarty_tpl->tpl_vars['currency']->value->getSign('right');?>
 <?php echo smartyTranslate(array('s'=>'(tax excl.)'),$_smarty_tpl);?>

								</td>
							<?php } ?>
						</tr>
					<?php } ?>
				<?php }?>
				<tr>
					<td colspan="<?php echo sizeof($_smarty_tpl->tpl_vars['ranges']->value)+1;?>
" class="center" style="border-bottom: none; height: 40px;">
						<input type="hidden" name="submitFees<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" value="1" />
					<?php if (sizeof($_smarty_tpl->tpl_vars['ranges']->value)&&!$_smarty_tpl->tpl_vars['carrierSelected']->value->is_free){?>
						<input type="submit" value="<?php echo smartyTranslate(array('s'=>'   Save   '),$_smarty_tpl);?>
" class="button" />
					<?php }elseif($_smarty_tpl->tpl_vars['carrierSelected']->value->is_free){?>
						<?php echo smartyTranslate(array('s'=>'This is a free carrier'),$_smarty_tpl);?>

					<?php }else{ ?>
						<?php echo smartyTranslate(array('s'=>'No ranges is set for this carrier'),$_smarty_tpl);?>

					<?php }?>
					</td>
				</tr>
			</table>
		<?php }?>
		<input type="hidden" name="id_carrier" value="<?php echo $_smarty_tpl->tpl_vars['id_carrier']->value;?>
" />
	</fieldset>
</form>
<?php }} ?>