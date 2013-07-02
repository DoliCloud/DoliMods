<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:10
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-follow.tpl" */ ?>
<?php /*%%SmartyHeaderCode:51734217451c1c0ea7d4ab1-04667795%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b52412541413820b910a69c9b39021b18bab321' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/order-follow.tpl',
      1 => 1371647171,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '51734217451c1c0ea7d4ab1-04667795',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'errorQuantity' => 0,
    'errorMsg' => 0,
    'errorDetail1' => 0,
    'errorDetail2' => 0,
    'errorNotReturnable' => 0,
    'ordersReturn' => 0,
    'return' => 0,
    'img_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ea988f21_14788112',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ea988f21_14788112')) {function content_51c1c0ea988f21_14788112($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'page_title', null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Return Merchandise Authorization (RMA)'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate ('./page-title.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div data-role="content" id="content">
	<a data-role="button" data-icon="arrow-l" data-theme="a" data-mini="true" data-inline="true" href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
" data-ajax="false"><?php echo smartyTranslate(array('s'=>'My account'),$_smarty_tpl);?>
</a>

	<?php if (isset($_smarty_tpl->tpl_vars['errorQuantity']->value)&&$_smarty_tpl->tpl_vars['errorQuantity']->value){?><p class="error"><?php echo smartyTranslate(array('s'=>'You do not have enough products to request an additional merchandise return.'),$_smarty_tpl);?>
</p><?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['errorMsg']->value)&&$_smarty_tpl->tpl_vars['errorMsg']->value){?><p class="error"><?php echo smartyTranslate(array('s'=>'Please provide an explanation for your RMA.'),$_smarty_tpl);?>
</p><?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['errorDetail1']->value)&&$_smarty_tpl->tpl_vars['errorDetail1']->value){?><p class="error"><?php echo smartyTranslate(array('s'=>'Please check at least one product you would like to return.'),$_smarty_tpl);?>
</p><?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['errorDetail2']->value)&&$_smarty_tpl->tpl_vars['errorDetail2']->value){?><p class="error"><?php echo smartyTranslate(array('s'=>'For each product you wish to add, please specify the desired quantity.'),$_smarty_tpl);?>
</p><?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['errorNotReturnable']->value)&&$_smarty_tpl->tpl_vars['errorNotReturnable']->value){?><p class="error"><?php echo smartyTranslate(array('s'=>'This order cannot be returned.'),$_smarty_tpl);?>
</p><?php }?>
	
	<p><?php echo smartyTranslate(array('s'=>'Here is a list of pending merchandise returns'),$_smarty_tpl);?>
.</p>
	<div class="block-center" id="block-history">
		<?php if ($_smarty_tpl->tpl_vars['ordersReturn']->value&&count($_smarty_tpl->tpl_vars['ordersReturn']->value)){?>
		<table id="order-list" class="std">
			<thead>
				<tr>
					<th class="first_item"><?php echo smartyTranslate(array('s'=>'Return'),$_smarty_tpl);?>
</th>
					<th class="item"><?php echo smartyTranslate(array('s'=>'Order'),$_smarty_tpl);?>
</th>
					<th class="item"><?php echo smartyTranslate(array('s'=>'Package status'),$_smarty_tpl);?>
</th>
					<th class="item"><?php echo smartyTranslate(array('s'=>'Date issued'),$_smarty_tpl);?>
</th>
					<th class="last_item"><?php echo smartyTranslate(array('s'=>'Return slip'),$_smarty_tpl);?>
</th>
				</tr>
			</thead>
			<tbody>
			<?php  $_smarty_tpl->tpl_vars['return'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['return']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ordersReturn']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['return']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['return']->iteration=0;
 $_smarty_tpl->tpl_vars['return']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['return']->key => $_smarty_tpl->tpl_vars['return']->value){
$_smarty_tpl->tpl_vars['return']->_loop = true;
 $_smarty_tpl->tpl_vars['return']->iteration++;
 $_smarty_tpl->tpl_vars['return']->index++;
 $_smarty_tpl->tpl_vars['return']->first = $_smarty_tpl->tpl_vars['return']->index === 0;
 $_smarty_tpl->tpl_vars['return']->last = $_smarty_tpl->tpl_vars['return']->iteration === $_smarty_tpl->tpl_vars['return']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['first'] = $_smarty_tpl->tpl_vars['return']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['index']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['last'] = $_smarty_tpl->tpl_vars['return']->last;
?>
				<tr class="<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['first']){?>first_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['last']){?>last_item<?php }else{ ?>item<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['index']%2){?>alternate_item<?php }?>">
					<td class="bold"><a class="color-myaccount" href="javascript:showOrder(0, <?php echo intval($_smarty_tpl->tpl_vars['return']->value['id_order_return']);?>
, '<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-return');?>
');" data-ajax="false"><?php echo smartyTranslate(array('s'=>'#'),$_smarty_tpl);?>
<?php echo sprintf("%06d",$_smarty_tpl->tpl_vars['return']->value['id_order_return']);?>
</a></td>
					<td class="history_method"><a class="color-myaccount" href="javascript:showOrder(1, <?php echo intval($_smarty_tpl->tpl_vars['return']->value['id_order']);?>
, '<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-detail');?>
');" data-ajax="false"><?php echo smartyTranslate(array('s'=>'#'),$_smarty_tpl);?>
<?php echo sprintf("%06d",$_smarty_tpl->tpl_vars['return']->value['id_order']);?>
</a></td>
					<td class="history_method"><span class="bold"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['return']->value['state_name'], 'htmlall', 'UTF-8');?>
</span></td>
					<td class="bold"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['return']->value['date_add'],'full'=>0),$_smarty_tpl);?>
</td>
					<td class="history_invoice">
					<?php if ($_smarty_tpl->tpl_vars['return']->value['state']==2){?>
						<a href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['return']->value['id_order_return']);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('pdf-order-return',true,null,"id_order_return=".$_tmp1);?>
" title="<?php echo smartyTranslate(array('s'=>'Order return'),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'#'),$_smarty_tpl);?>
<?php echo sprintf("%06d",$_smarty_tpl->tpl_vars['return']->value['id_order_return']);?>
" data-ajax="false"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/pdf.gif" alt="<?php echo smartyTranslate(array('s'=>'Order return'),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'#'),$_smarty_tpl);?>
<?php echo sprintf("%06d",$_smarty_tpl->tpl_vars['return']->value['id_order_return']);?>
" class="icon" /></a>
						<a href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['return']->value['id_order_return']);?>
<?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('pdf-order-return',true,null,"id_order_return=".$_tmp2);?>
" title="<?php echo smartyTranslate(array('s'=>'Order return'),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'#'),$_smarty_tpl);?>
<?php echo sprintf("%06d",$_smarty_tpl->tpl_vars['return']->value['id_order_return']);?>
" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Print out'),$_smarty_tpl);?>
</a>
					<?php }else{ ?>
						--
					<?php }?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<div id="block-order-detail" class="hidden">&nbsp;</div>
		<?php }else{ ?>
			<p class="warning"><?php echo smartyTranslate(array('s'=>'You have no merchandise return authorizations.'),$_smarty_tpl);?>
</p>
		<?php }?>
	</div>
</div><!-- /content -->
<?php }} ?>