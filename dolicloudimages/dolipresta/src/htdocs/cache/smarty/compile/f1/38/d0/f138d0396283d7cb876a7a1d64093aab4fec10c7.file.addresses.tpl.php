<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:07
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/addresses.tpl" */ ?>
<?php /*%%SmartyHeaderCode:34101887551c1c0e75c89c5-65593702%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f138d0396283d7cb876a7a1d64093aab4fec10c7' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/addresses.tpl',
      1 => 1371647167,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '34101887551c1c0e75c89c5-65593702',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'multipleAddresses' => 0,
    'addresses_style' => 0,
    'address' => 0,
    'pattern' => 0,
    'addressKey' => 0,
    'key' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e7694eb6_78465060',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e7694eb6_78465060')) {function content_51c1c0e7694eb6_78465060($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.replace.php';
if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'page_title', null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'My addresses'),$_smarty_tpl);?>
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
	<p><?php echo smartyTranslate(array('s'=>'Please configure your default billing and delivery addresses when placing an order. You may also add additional addresses, which can be useful for sending gifts or receiving an order at your office.'),$_smarty_tpl);?>
</p>
	<div>
		<?php if (isset($_smarty_tpl->tpl_vars['multipleAddresses']->value)&&$_smarty_tpl->tpl_vars['multipleAddresses']->value){?>
		<h3><?php echo smartyTranslate(array('s'=>'Your addresses are listed below.'),$_smarty_tpl);?>
</h3>
		<p><?php echo smartyTranslate(array('s'=>'Be sure to update your personal information if it has changed.'),$_smarty_tpl);?>
</p>
		<?php $_smarty_tpl->tpl_vars["adrs_style"] = new Smarty_variable($_smarty_tpl->tpl_vars['addresses_style']->value, null, 0);?>
		<form action="opc.html" method="post">
			<ul data-role="listview" data-theme="g">
				<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['multipleAddresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
?>
				<li>
					<a href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['address']->value['object']['id']);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"id_address=".$_tmp1);?>
" title="<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>
" data-ajax="false">
						<p class="title_block"><?php echo $_smarty_tpl->tpl_vars['address']->value['object']['alias'];?>
</p>
						<?php  $_smarty_tpl->tpl_vars['pattern'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pattern']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['address']->value['ordered']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['pattern']->key => $_smarty_tpl->tpl_vars['pattern']->value){
$_smarty_tpl->tpl_vars['pattern']->_loop = true;
?>
							<?php $_smarty_tpl->tpl_vars['addressKey'] = new Smarty_variable(explode(" ",$_smarty_tpl->tpl_vars['pattern']->value), null, 0);?>
							<?php  $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['key']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['addressKey']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['key']->key => $_smarty_tpl->tpl_vars['key']->value){
$_smarty_tpl->tpl_vars['key']->_loop = true;
?>
								<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value['formated'][smarty_modifier_replace($_smarty_tpl->tpl_vars['key']->value,',','')], 'htmlall', 'UTF-8');?>

							<?php } ?>
							<br />
						<?php } ?>
					</a>
				</li>
				<?php } ?>
			</ul>
		</form>
		<?php }else{ ?>
		<p class="warning"><?php echo smartyTranslate(array('s'=>'No addresses are available.'),$_smarty_tpl);?>
</p>
	<?php }?>
		<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true);?>
" data-role="button" data-theme="a" data-ajax="false"><?php echo smartyTranslate(array('s'=>'Add a new address'),$_smarty_tpl);?>
</a>
	</div>
	
	<?php echo $_smarty_tpl->getSubTemplate ('./sitemap.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div><!-- /content -->
<?php }} ?>