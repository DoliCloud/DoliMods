<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:59
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/identity.tpl" */ ?>
<?php /*%%SmartyHeaderCode:53717953051c1c0df7499b2-39588659%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cf2e4ca5d8e21555e2bead89aa8d1ff0b4f91ae6' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/identity.tpl',
      1 => 1371646827,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '53717953051c1c0df7499b2-39588659',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'navigationPipe' => 0,
    'confirmation' => 0,
    'pwd_changed' => 0,
    'email' => 0,
    'genders' => 0,
    'gender' => 0,
    'days' => 0,
    'v' => 0,
    'sl_day' => 0,
    'months' => 0,
    'k' => 0,
    'sl_month' => 0,
    'years' => 0,
    'sl_year' => 0,
    'newsletter' => 0,
    'img_dir' => 0,
    'base_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0df90a050_81878418',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0df90a050_81878418')) {function content_51c1c0df90a050_81878418($_smarty_tpl) {?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
"><?php echo smartyTranslate(array('s'=>'My account'),$_smarty_tpl);?>
</a><span class="navigation-pipe"><?php echo $_smarty_tpl->tpl_vars['navigationPipe']->value;?>
</span><?php echo smartyTranslate(array('s'=>'Your personal information'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<h1><?php echo smartyTranslate(array('s'=>'Your personal information'),$_smarty_tpl);?>
</h1>

<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php if (isset($_smarty_tpl->tpl_vars['confirmation']->value)&&$_smarty_tpl->tpl_vars['confirmation']->value){?>
	<p class="success">
		<?php echo smartyTranslate(array('s'=>'Your personal information has been successfully updated.'),$_smarty_tpl);?>

		<?php if (isset($_smarty_tpl->tpl_vars['pwd_changed']->value)){?><br /><?php echo smartyTranslate(array('s'=>'Your password has been sent to your email:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['email']->value;?>
<?php }?>
	</p>
<?php }else{ ?>
	<h3><?php echo smartyTranslate(array('s'=>'Please be sure to update your personal information if it has changed.'),$_smarty_tpl);?>
</h3>
	<p class="required"><sup>*</sup><?php echo smartyTranslate(array('s'=>'Required field'),$_smarty_tpl);?>
</p>
	<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('identity',true);?>
" method="post" class="std">
		<fieldset>
			<p class="radio">
				<span><?php echo smartyTranslate(array('s'=>'Title'),$_smarty_tpl);?>
</span>
				<?php  $_smarty_tpl->tpl_vars['gender'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['gender']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['genders']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['gender']->key => $_smarty_tpl->tpl_vars['gender']->value){
$_smarty_tpl->tpl_vars['gender']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['gender']->key;
?>
					<input type="radio" name="id_gender" id="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" value="<?php echo intval($_smarty_tpl->tpl_vars['gender']->value->id);?>
" <?php if (isset($_POST['id_gender'])&&$_POST['id_gender']==$_smarty_tpl->tpl_vars['gender']->value->id){?>checked="checked"<?php }?> />
					<label for="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" class="top"><?php echo $_smarty_tpl->tpl_vars['gender']->value->name;?>
</label>
				<?php } ?>
			</p>
			<p class="required text">
				<label for="firstname"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<input type="text" id="firstname" name="firstname" value="<?php echo $_POST['firstname'];?>
" />
			</p>
			<p class="required text">
				<label for="lastname"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<input type="text" name="lastname" id="lastname" value="<?php echo $_POST['lastname'];?>
" />
			</p>
			<p class="required text">
				<label for="email"><?php echo smartyTranslate(array('s'=>'Email'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<input type="text" name="email" id="email" value="<?php echo $_POST['email'];?>
" />
			</p>
			<p class="required text">
				<label for="old_passwd"><?php echo smartyTranslate(array('s'=>'Current Password'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<input type="password" name="old_passwd" id="old_passwd" />
			</p>
			<p class="password">
				<label for="passwd"><?php echo smartyTranslate(array('s'=>'New Password'),$_smarty_tpl);?>
</label>
				<input type="password" name="passwd" id="passwd" />
			</p>
			<p class="password">
				<label for="confirmation"><?php echo smartyTranslate(array('s'=>'Confirmation'),$_smarty_tpl);?>
</label>
				<input type="password" name="confirmation" id="confirmation" />
			</p>
			<p class="select">
				<label><?php echo smartyTranslate(array('s'=>'Date of Birth'),$_smarty_tpl);?>
</label>
				<select name="days" id="days">
					<option value="">-</option>
					<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['days']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['v']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_day']->value==$_smarty_tpl->tpl_vars['v']->value)){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
&nbsp;&nbsp;</option>
					<?php } ?>
				</select>
				
				<select id="months" name="months">
					<option value="">-</option>
					<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['months']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_month']->value==$_smarty_tpl->tpl_vars['k']->value)){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['v']->value),$_smarty_tpl);?>
&nbsp;</option>
					<?php } ?>
				</select>
				<select id="years" name="years">
					<option value="">-</option>
					<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['years']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['v']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_year']->value==$_smarty_tpl->tpl_vars['v']->value)){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
&nbsp;&nbsp;</option>
					<?php } ?>
				</select>
			</p>
			<?php if ($_smarty_tpl->tpl_vars['newsletter']->value){?>
			<p class="checkbox">
				<input type="checkbox" id="newsletter" name="newsletter" value="1" <?php if (isset($_POST['newsletter'])&&$_POST['newsletter']==1){?> checked="checked"<?php }?> />
				<label for="newsletter"><?php echo smartyTranslate(array('s'=>'Sign up for our newsletter!'),$_smarty_tpl);?>
</label>
			</p>
			<p class="checkbox">
				<input type="checkbox" name="optin" id="optin" value="1" <?php if (isset($_POST['optin'])&&$_POST['optin']==1){?> checked="checked"<?php }?> />
				<label for="optin"><?php echo smartyTranslate(array('s'=>'Receive special offers from our partners!'),$_smarty_tpl);?>
</label>
			</p>
			<?php }?>
			<p class="submit">
				<input type="submit" class="button" name="submitIdentity" value="<?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
" />
			</p>
			<p id="security_informations">
				<?php echo smartyTranslate(array('s'=>'[Insert customer data privacy clause here, if applicable]'),$_smarty_tpl);?>

			</p>
		</fieldset>
	</form>
<?php }?>

<ul class="footer_links">
	<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/my-account.gif" alt="" class="icon" /></a><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
"><?php echo smartyTranslate(array('s'=>'Back to your account'),$_smarty_tpl);?>
</a></li>
	<li class="f_right"><a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/home.gif" alt="" class="icon" /> <?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a></li>
</ul>
<?php }} ?>