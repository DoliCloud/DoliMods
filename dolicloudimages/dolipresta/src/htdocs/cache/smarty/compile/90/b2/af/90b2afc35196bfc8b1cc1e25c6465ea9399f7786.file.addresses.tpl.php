<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:57
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/addresses.tpl" */ ?>
<?php /*%%SmartyHeaderCode:171844539151c1c0dd926c63-27063267%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '90b2afc35196bfc8b1cc1e25c6465ea9399f7786' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/addresses.tpl',
      1 => 1371646824,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '171844539151c1c0dd926c63-27063267',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'multipleAddresses' => 0,
    'addresses' => 0,
    'address' => 0,
    'address_key' => 0,
    'ignoreList' => 0,
    'address_number' => 0,
    'address_key_number' => 0,
    'address_content' => 0,
    'addresses_style' => 0,
    'link' => 0,
    'navigationPipe' => 0,
    'pattern' => 0,
    'addressKey' => 0,
    'key' => 0,
    'img_dir' => 0,
    'base_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ddb8d444_62537141',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ddb8d444_62537141')) {function content_51c1c0ddb8d444_62537141($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/function.counter.php';
if (!is_callable('smarty_modifier_replace')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.replace.php';
if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>





<?php if (!isset($_smarty_tpl->tpl_vars['multipleAddresses']->value)){?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[0] = "id_address";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[1] = "id_country";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[2] = "id_state";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[3] = "id_customer";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[4] = "id_manufacturer";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[5] = "id_supplier";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[6] = "date_add";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[7] = "date_upd";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[8] = "active";?>
	<?php $_smarty_tpl->createLocalArrayVariable('ignoreList', null, 0);
$_smarty_tpl->tpl_vars['ignoreList']->value[9] = "deleted";?>

	
	<?php if (isset($_smarty_tpl->tpl_vars['addresses']->value)){?>
		<?php $_smarty_tpl->tpl_vars['address_number'] = new Smarty_variable(0, null, 0);?>
		<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['address']->key;
?>
			<?php echo smarty_function_counter(array('start'=>0,'skip'=>1,'assign'=>'address_key_number'),$_smarty_tpl);?>

			<?php  $_smarty_tpl->tpl_vars['address_content'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address_content']->_loop = false;
 $_smarty_tpl->tpl_vars['address_key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['address']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address_content']->key => $_smarty_tpl->tpl_vars['address_content']->value){
$_smarty_tpl->tpl_vars['address_content']->_loop = true;
 $_smarty_tpl->tpl_vars['address_key']->value = $_smarty_tpl->tpl_vars['address_content']->key;
?>
				<?php if (!in_array($_smarty_tpl->tpl_vars['address_key']->value,$_smarty_tpl->tpl_vars['ignoreList']->value)){?>
					<?php $_smarty_tpl->createLocalArrayVariable('multipleAddresses', null, 0);
$_smarty_tpl->tpl_vars['multipleAddresses']->value[$_smarty_tpl->tpl_vars['address_number']->value]['ordered'][$_smarty_tpl->tpl_vars['address_key_number']->value] = $_smarty_tpl->tpl_vars['address_key']->value;?>
					<?php $_smarty_tpl->createLocalArrayVariable('multipleAddresses', null, 0);
$_smarty_tpl->tpl_vars['multipleAddresses']->value[$_smarty_tpl->tpl_vars['address_number']->value]['formated'][$_smarty_tpl->tpl_vars['address_key']->value] = $_smarty_tpl->tpl_vars['address_content']->value;?>
					<?php echo smarty_function_counter(array(),$_smarty_tpl);?>

				<?php }?>
			<?php } ?>
		<?php $_smarty_tpl->createLocalArrayVariable('multipleAddresses', null, 0);
$_smarty_tpl->tpl_vars['multipleAddresses']->value[$_smarty_tpl->tpl_vars['address_number']->value]['object'] = $_smarty_tpl->tpl_vars['address']->value;?>
		<?php $_smarty_tpl->tpl_vars['address_number'] = new Smarty_variable($_smarty_tpl->tpl_vars['address_number']->value+1, null, 0);?>
		<?php } ?>
	<?php }?>
<?php }?>



<?php if (!isset($_smarty_tpl->tpl_vars['addresses_style']->value)){?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['company'] = 'address_company';?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['vat_number'] = 'address_company';?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['firstname'] = 'address_name';?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['lastname'] = 'address_name';?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['address1'] = 'address_address1';?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['address2'] = 'address_address2';?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['city'] = 'address_city';?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['country'] = 'address_country';?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['phone'] = 'address_phone';?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['phone_mobile'] = 'address_phone_mobile';?>
	<?php $_smarty_tpl->createLocalArrayVariable('addresses_style', null, 0);
$_smarty_tpl->tpl_vars['addresses_style']->value['alias'] = 'address_title';?>
<?php }?>

<script type="text/javascript">
//<![CDATA[
	
	$(document).ready(function()
	{
			resizeAddressesBox();
	});
	
//]]>
</script>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
"><?php echo smartyTranslate(array('s'=>'My account'),$_smarty_tpl);?>
</a><span class="navigation-pipe"><?php echo $_smarty_tpl->tpl_vars['navigationPipe']->value;?>
</span><?php echo smartyTranslate(array('s'=>'My addresses'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<h1><?php echo smartyTranslate(array('s'=>'My addresses'),$_smarty_tpl);?>
</h1>
<p><?php echo smartyTranslate(array('s'=>'Please configure your default billing and delivery addresses when placing an order. You may also add additional addresses, which can be useful for sending gifts or receiving an order at your office.'),$_smarty_tpl);?>
</p>

<?php if (isset($_smarty_tpl->tpl_vars['multipleAddresses']->value)&&$_smarty_tpl->tpl_vars['multipleAddresses']->value){?>
<div class="addresses">
	<h3><?php echo smartyTranslate(array('s'=>'Your addresses are listed below.'),$_smarty_tpl);?>
</h3>
	<p><?php echo smartyTranslate(array('s'=>'Be sure to update your personal information if it has changed.'),$_smarty_tpl);?>
</p>
	<?php $_smarty_tpl->tpl_vars["adrs_style"] = new Smarty_variable($_smarty_tpl->tpl_vars['addresses_style']->value, null, 0);?>
	<div class="bloc_adresses clearfix">
	<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['multipleAddresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['address']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['address']->iteration=0;
 $_smarty_tpl->tpl_vars['address']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value){
$_smarty_tpl->tpl_vars['address']->_loop = true;
 $_smarty_tpl->tpl_vars['address']->iteration++;
 $_smarty_tpl->tpl_vars['address']->index++;
 $_smarty_tpl->tpl_vars['address']->first = $_smarty_tpl->tpl_vars['address']->index === 0;
 $_smarty_tpl->tpl_vars['address']->last = $_smarty_tpl->tpl_vars['address']->iteration === $_smarty_tpl->tpl_vars['address']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['first'] = $_smarty_tpl->tpl_vars['address']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['index']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['myLoop']['last'] = $_smarty_tpl->tpl_vars['address']->last;
?>
		<ul class="address <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['last']){?>last_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['first']){?>first_item<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['myLoop']['index']%2){?>alternate_item<?php }else{ ?>item<?php }?>">
			<li class="address_title"><?php echo $_smarty_tpl->tpl_vars['address']->value['object']['alias'];?>
</li>
			<?php  $_smarty_tpl->tpl_vars['pattern'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pattern']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['address']->value['ordered']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['pattern']->key => $_smarty_tpl->tpl_vars['pattern']->value){
$_smarty_tpl->tpl_vars['pattern']->_loop = true;
?>
				<?php $_smarty_tpl->tpl_vars['addressKey'] = new Smarty_variable(explode(" ",$_smarty_tpl->tpl_vars['pattern']->value), null, 0);?>
				<li>
				<?php  $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['key']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['addressKey']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['key']->key => $_smarty_tpl->tpl_vars['key']->value){
$_smarty_tpl->tpl_vars['key']->_loop = true;
?>
					<span<?php if (isset($_smarty_tpl->tpl_vars['addresses_style']->value[$_smarty_tpl->tpl_vars['key']->value])){?> class="<?php echo $_smarty_tpl->tpl_vars['addresses_style']->value[$_smarty_tpl->tpl_vars['key']->value];?>
"<?php }?>>
						<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value['formated'][smarty_modifier_replace($_smarty_tpl->tpl_vars['key']->value,',','')], 'htmlall', 'UTF-8');?>

					</span>
				<?php } ?>
				</li>
			<?php } ?>
			<li class="address_update"><a href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['address']->value['object']['id']);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"id_address=".$_tmp1);?>
" title="<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>
">&raquo; <?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>
</a></li>
			<li class="address_delete"><a href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['address']->value['object']['id']);?>
<?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true,null,"id_address=".$_tmp2."&delete");?>
" onclick="return confirm('<?php echo smartyTranslate(array('s'=>'Are you sure?','js'=>1),$_smarty_tpl);?>
');" title="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
">&raquo; <?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
</a></li>
		</ul>
	<?php } ?>
	</div>
	<p class="clear" />
</div>
<?php }else{ ?>
	<p class="warning"><?php echo smartyTranslate(array('s'=>'No addresses are available.'),$_smarty_tpl);?>
&nbsp;<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true);?>
"><?php echo smartyTranslate(array('s'=>'Add a new address'),$_smarty_tpl);?>
</a></p>
<?php }?>

<div class="clear address_add"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true);?>
" title="<?php echo smartyTranslate(array('s'=>'Add an address'),$_smarty_tpl);?>
" class="button_large"><?php echo smartyTranslate(array('s'=>'Add an address'),$_smarty_tpl);?>
</a></div>

<ul class="footer_links">
	<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/my-account.gif" alt="" class="icon" /> <?php echo smartyTranslate(array('s'=>'Back to your account'),$_smarty_tpl);?>
</a></li>
	<li class="f_right"><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/home.gif" alt="" class="icon" /> <?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a></li>
</ul>
<?php }} ?>