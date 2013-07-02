<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:58
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/contact-form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:147554878151c1c0deb8cf23-94894422%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a51c78325cc2b839b43ebc052eee1a568a7e5520' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/contact-form.tpl',
      1 => 1371646826,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '147554878151c1c0deb8cf23-94894422',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'customerThread' => 0,
    'confirmation' => 0,
    'base_dir' => 0,
    'img_dir' => 0,
    'alreadySent' => 0,
    'request_uri' => 0,
    'contacts' => 0,
    'contact' => 0,
    'email' => 0,
    'PS_CATALOG_MODE' => 0,
    'isLogged' => 0,
    'orderList' => 0,
    'order' => 0,
    'orderedProductList' => 0,
    'id_order' => 0,
    'products' => 0,
    'product' => 0,
    'fileupload' => 0,
    'message' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0dee30108_73819518',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0dee30108_73819518')) {function content_51c1c0dee30108_73819518($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Contact'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<h1><?php echo smartyTranslate(array('s'=>'Customer service'),$_smarty_tpl);?>
 - <?php if (isset($_smarty_tpl->tpl_vars['customerThread']->value)&&$_smarty_tpl->tpl_vars['customerThread']->value){?><?php echo smartyTranslate(array('s'=>'Your reply'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Contact us'),$_smarty_tpl);?>
<?php }?></h1>

<?php if (isset($_smarty_tpl->tpl_vars['confirmation']->value)){?>
	<p><?php echo smartyTranslate(array('s'=>'Your message has been successfully sent to our team.'),$_smarty_tpl);?>
</p>
	<ul class="footer_links">
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><img class="icon" alt="" src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/home.gif"/></a><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a></li>
	</ul>
<?php }elseif(isset($_smarty_tpl->tpl_vars['alreadySent']->value)){?>
	<p><?php echo smartyTranslate(array('s'=>'Your message has already been sent.'),$_smarty_tpl);?>
</p>
	<ul class="footer_links">
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><img class="icon" alt="" src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/home.gif"/></a><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a></li>
	</ul>
<?php }else{ ?>
	<p class="bold"><?php echo smartyTranslate(array('s'=>'For questions about an order or for more information about our products'),$_smarty_tpl);?>
.</p>
	<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<form action="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['request_uri']->value, 'htmlall', 'UTF-8');?>
" method="post" class="std" enctype="multipart/form-data">
		<fieldset>
			<h3><?php echo smartyTranslate(array('s'=>'send a message'),$_smarty_tpl);?>
</h3>
			<p class="select">
				<label for="id_contact"><?php echo smartyTranslate(array('s'=>'Subject Heading'),$_smarty_tpl);?>
</label>
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
			</p>
			<?php }else{ ?>
				<select id="id_contact" name="id_contact" onchange="showElemFromSelect('id_contact', 'desc_contact')">
					<option value="0"><?php echo smartyTranslate(array('s'=>'-- Choose --'),$_smarty_tpl);?>
</option>
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
			</p>
			<p id="desc_contact0" class="desc_contact">&nbsp;</p>
				<?php  $_smarty_tpl->tpl_vars['contact'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['contact']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contacts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['contact']->key => $_smarty_tpl->tpl_vars['contact']->value){
$_smarty_tpl->tpl_vars['contact']->_loop = true;
?>
					<p id="desc_contact<?php echo intval($_smarty_tpl->tpl_vars['contact']->value['id_contact']);?>
" class="desc_contact" style="display:none;">
						<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['contact']->value['description'], 'htmlall', 'UTF-8');?>

					</p>
				<?php } ?>
			<?php }?>
			<p class="text">
				<label for="email"><?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
</label>
				<?php if (isset($_smarty_tpl->tpl_vars['customerThread']->value['email'])){?>
					<input type="text" id="email" name="from" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['customerThread']->value['email'], 'htmlall', 'UTF-8');?>
" readonly="readonly" />
				<?php }else{ ?>
					<input type="text" id="email" name="from" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['email']->value, 'htmlall', 'UTF-8');?>
" />
				<?php }?>
			</p>
		<?php if (!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
			<?php if ((!isset($_smarty_tpl->tpl_vars['customerThread']->value['id_order'])||$_smarty_tpl->tpl_vars['customerThread']->value['id_order']>0)){?>
			<p class="text select">
				<label for="id_order"><?php echo smartyTranslate(array('s'=>'Order reference'),$_smarty_tpl);?>
</label>
				<?php if (!isset($_smarty_tpl->tpl_vars['customerThread']->value['id_order'])&&isset($_smarty_tpl->tpl_vars['isLogged']->value)&&$_smarty_tpl->tpl_vars['isLogged']->value==1){?>
					<select name="id_order" >
						<option value="0"><?php echo smartyTranslate(array('s'=>'-- Choose --'),$_smarty_tpl);?>
</option>
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
					<input type="text" name="id_order" id="id_order" value="<?php if (isset($_smarty_tpl->tpl_vars['customerThread']->value['id_order'])&&$_smarty_tpl->tpl_vars['customerThread']->value['id_order']>0){?><?php echo intval($_smarty_tpl->tpl_vars['customerThread']->value['id_order']);?>
<?php }else{ ?><?php if (isset($_POST['id_order'])){?><?php echo intval($_POST['id_order']);?>
<?php }?><?php }?>" />
				<?php }elseif($_smarty_tpl->tpl_vars['customerThread']->value['id_order']>0){?>
					<input type="text" name="id_order" id="id_order" value="<?php echo intval($_smarty_tpl->tpl_vars['customerThread']->value['id_order']);?>
" readonly="readonly" />
				<?php }?>
			</p>
			<?php }?>
			<?php if (isset($_smarty_tpl->tpl_vars['isLogged']->value)&&$_smarty_tpl->tpl_vars['isLogged']->value){?>
			<p class="text select">
			<label for="id_product"><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</label>
				<?php if (!isset($_smarty_tpl->tpl_vars['customerThread']->value['id_product'])){?>
				<?php  $_smarty_tpl->tpl_vars['products'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['products']->_loop = false;
 $_smarty_tpl->tpl_vars['id_order'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['orderedProductList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['products']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['products']->key => $_smarty_tpl->tpl_vars['products']->value){
$_smarty_tpl->tpl_vars['products']->_loop = true;
 $_smarty_tpl->tpl_vars['id_order']->value = $_smarty_tpl->tpl_vars['products']->key;
 $_smarty_tpl->tpl_vars['products']->index++;
 $_smarty_tpl->tpl_vars['products']->first = $_smarty_tpl->tpl_vars['products']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['first'] = $_smarty_tpl->tpl_vars['products']->first;
?>
					<select name="id_product" id="<?php echo $_smarty_tpl->tpl_vars['id_order']->value;?>
_order_products" class="product_select" style="width:300px;<?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['products']['first']){?> display:none; <?php }?>" <?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['products']['first']){?>disabled="disabled" <?php }?>>
						<option value="0"><?php echo smartyTranslate(array('s'=>'-- Choose --'),$_smarty_tpl);?>
</option>
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
			</p>
			<?php }?>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['fileupload']->value==1){?>
			<p class="text">
			<label for="fileUpload"><?php echo smartyTranslate(array('s'=>'Attach File'),$_smarty_tpl);?>
</label>
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
				<input type="file" name="fileUpload" id="fileUpload" />
			</p>
		<?php }?>
		<p class="textarea">
			<label for="message"><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
</label>
			 <textarea id="message" name="message" rows="15" cols="10"><?php if (isset($_smarty_tpl->tpl_vars['message']->value)){?><?php echo stripslashes(smarty_modifier_escape($_smarty_tpl->tpl_vars['message']->value, 'htmlall', 'UTF-8'));?>
<?php }?></textarea>
		</p>
		<p class="submit">
			<input type="submit" name="submitMessage" id="submitMessage" value="<?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
" class="button_large" onclick="$(this).hide();" />
		</p>
	</fieldset>
</form>
<?php }?>
<?php }} ?>