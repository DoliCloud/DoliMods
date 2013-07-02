<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:08
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/contact-form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:164794395051c1c0e896f8b9-17518975%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ab1c9b3e59f66413f59893e58f900774cbaf7495' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/contact-form.tpl',
      1 => 1371647169,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '164794395051c1c0e896f8b9-17518975',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request_uri' => 0,
    'customerThread' => 0,
    'contacts' => 0,
    'contact' => 0,
    'email' => 0,
    'PS_CATALOG_MODE' => 0,
    'isLogged' => 0,
    'orderList' => 0,
    'order' => 0,
    'orderedProductList' => 0,
    'products' => 0,
    'product' => 0,
    'message' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e8b8b017_77079885',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e8b8b017_77079885')) {function content_51c1c0e8b8b017_77079885($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'page_title', null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Contact'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate ('./page-title.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


	<div data-role="content" id="content">
		<p class="bold"><?php echo smartyTranslate(array('s'=>'For questions about an order or for more information about our products'),$_smarty_tpl);?>
.</p>
		<?php echo $_smarty_tpl->getSubTemplate ("./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<form action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['request_uri']->value, 'htmlall', 'UTF-8');?>
" method="post" class="std" enctype="multipart/form-data">
			<?php if (isset($_smarty_tpl->tpl_vars['customerThread']->value['id_contact'])){?>
				<?php  $_smarty_tpl->tpl_vars['contact'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['contact']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contacts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['contact']->key => $_smarty_tpl->tpl_vars['contact']->value){
$_smarty_tpl->tpl_vars['contact']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['contact']->value['id_contact']==$_smarty_tpl->tpl_vars['customerThread']->value['id_contact']){?>
						<input type="text" id="contact_name" name="contact_name" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['contact']->value['name'], 'htmlall', 'UTF-8');?>
" readonly="readonly" />
						<input type="hidden" name="id_contact" value="<?php echo $_smarty_tpl->tpl_vars['contact']->value['id_contact'];?>
" />
					<?php }?>
				<?php } ?>
			<?php }else{ ?>
				<select id="id_contact" name="id_contact" onchange="showElemFromSelect('id_contact', 'desc_contact')">
					<option value="0">-- <?php echo smartyTranslate(array('s'=>'Subject Heading'),$_smarty_tpl);?>
 --</option>
				<?php  $_smarty_tpl->tpl_vars['contact'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['contact']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contacts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['contact']->key => $_smarty_tpl->tpl_vars['contact']->value){
$_smarty_tpl->tpl_vars['contact']->_loop = true;
?>
					<option value="<?php echo intval($_smarty_tpl->tpl_vars['contact']->value['id_contact']);?>
" <?php if (isset($_POST['id_contact'])&&$_POST['id_contact']==$_smarty_tpl->tpl_vars['contact']->value['id_contact']){?>selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['contact']->value['name'], 'htmlall', 'UTF-8');?>
</option>
				<?php } ?>
				</select>

			<p id="desc_contact0" class="desc_contact">&nbsp;</p>
				<?php  $_smarty_tpl->tpl_vars['contact'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['contact']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contacts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['contact']->key => $_smarty_tpl->tpl_vars['contact']->value){
$_smarty_tpl->tpl_vars['contact']->_loop = true;
?>
					<p id="desc_contact<?php echo intval($_smarty_tpl->tpl_vars['contact']->value['id_contact']);?>
" class="desc_contact" style="display:none;">
						<label>&nbsp;</label><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['contact']->value['description'], 'htmlall', 'UTF-8');?>

					</p>
				<?php } ?>
			<?php }?>
			
			<fieldset>
				<?php if (isset($_smarty_tpl->tpl_vars['customerThread']->value['email'])){?>
					<input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" type="email" id="email" name="from" value="<?php echo $_smarty_tpl->tpl_vars['customerThread']->value['email'];?>
" placeholder="<?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
" readonly="readonly" />
				<?php }else{ ?>
					<input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" type="email" id="email" name="from" value="<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
" placeholder="<?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
"/>
				<?php }?>
			</fieldset>
			
			<?php if (!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
				<?php if ((!isset($_smarty_tpl->tpl_vars['customerThread']->value['id_order'])||$_smarty_tpl->tpl_vars['customerThread']->value['id_order']>0)){?>
				<fieldset>
					<?php if (!isset($_smarty_tpl->tpl_vars['customerThread']->value['id_order'])&&isset($_smarty_tpl->tpl_vars['isLogged']->value)&&$_smarty_tpl->tpl_vars['isLogged']->value==1){?>
						<select name="id_order" ><option value="0">-- <?php echo smartyTranslate(array('s'=>'Order ID'),$_smarty_tpl);?>
 --</option>
						<?php  $_smarty_tpl->tpl_vars['order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['order']->key => $_smarty_tpl->tpl_vars['order']->value){
$_smarty_tpl->tpl_vars['order']->_loop = true;
?>
							<option value="<?php echo intval($_smarty_tpl->tpl_vars['order']->value['value']);?>
" <?php if (intval($_smarty_tpl->tpl_vars['order']->value['selected'])){?>selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['order']->value['label'], 'htmlall', 'UTF-8');?>
</option>
						<?php } ?>
						</select>
					<?php }elseif(!isset($_smarty_tpl->tpl_vars['customerThread']->value['id_order'])&&!isset($_smarty_tpl->tpl_vars['isLogged']->value)){?>
						<input type="text" placeholder="<?php echo smartyTranslate(array('s'=>'Order ID'),$_smarty_tpl);?>
" name="id_order" id="id_order" value="<?php if (isset($_smarty_tpl->tpl_vars['customerThread']->value['id_order'])&&$_smarty_tpl->tpl_vars['customerThread']->value['id_order']>0){?><?php echo intval($_smarty_tpl->tpl_vars['customerThread']->value['id_order']);?>
<?php }else{ ?><?php if (isset($_POST['id_order'])){?><?php echo intval($_POST['id_order']);?>
<?php }?><?php }?>" />
					<?php }elseif($_smarty_tpl->tpl_vars['customerThread']->value['id_order']>0){?>
						<input type="text" placeholder="<?php echo smartyTranslate(array('s'=>'Order ID'),$_smarty_tpl);?>
" name="id_order" id="id_order" value="<?php echo intval($_smarty_tpl->tpl_vars['customerThread']->value['id_order']);?>
" readonly="readonly" />
					<?php }?>
				</fieldset>
				<?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['isLogged']->value)&&$_smarty_tpl->tpl_vars['isLogged']->value){?>
				<fieldset>

					<?php if (!isset($_smarty_tpl->tpl_vars['customerThread']->value['id_product'])){?>
						<?php  $_smarty_tpl->tpl_vars['products'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['products']->_loop = false;
 $_smarty_tpl->tpl_vars['id_order'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['orderedProductList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['products']->key => $_smarty_tpl->tpl_vars['products']->value){
$_smarty_tpl->tpl_vars['products']->_loop = true;
 $_smarty_tpl->tpl_vars['id_order']->value = $_smarty_tpl->tpl_vars['products']->key;
?>
							<select name="id_product"><option value="0">-- <?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
 --</option>
								<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
									<option value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['value']);?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value['label'], 'htmlall', 'UTF-8');?>
</option>
								<?php } ?>
							</select>
						<?php } ?>
					<?php }elseif($_smarty_tpl->tpl_vars['customerThread']->value['id_product']>0){?>
						<input type="text" name="id_product" id="id_product" value="<?php echo intval($_smarty_tpl->tpl_vars['customerThread']->value['id_product']);?>
" readonly="readonly" />
					<?php }?>
				</fieldset>
				<?php }?>
			<?php }?>
			
			<fieldset>
				<textarea id="message" name="message" placeholder="<?php echo smartyTranslate(array('s'=>'Your message'),$_smarty_tpl);?>
" rows="15" cols="10"><?php if (isset($_smarty_tpl->tpl_vars['message']->value)&&$_smarty_tpl->tpl_vars['message']->value!=''){?><?php echo stripslashes(smarty_modifier_escape($_smarty_tpl->tpl_vars['message']->value, 'htmlall', 'UTF-8'));?>
<?php }?></textarea>
			</fieldset>
			
			<fieldset>
				<button class="ui-btn-hidden" type="submit" aria-disabled="false" data-theme="a" name="submitMessage" id="submitMessage"><?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
</button>
			</fieldset>
		</form> 
		
		<?php echo $_smarty_tpl->getSubTemplate ('./sitemap.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</div><!-- /content --><?php }} ?>