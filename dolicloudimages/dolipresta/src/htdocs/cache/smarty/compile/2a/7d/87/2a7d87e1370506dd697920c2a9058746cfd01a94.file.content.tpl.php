<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:59
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/login/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:118785135051c1c0dfddd620-15644942%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2a7d87e1370506dd697920c2a9058746cfd01a94' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/login/content.tpl',
      1 => 1371647776,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '118785135051c1c0dfddd620-15644942',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'errors' => 0,
    'nbErrors' => 0,
    'error' => 0,
    'warningSslMessage' => 0,
    'shop_name' => 0,
    'wrong_folder_name' => 0,
    'wrong_install_name' => 0,
    'email' => 0,
    'password' => 0,
    'redirect' => 0,
    'randomNb' => 0,
    'adminUrl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0dfef4357_61532250',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0dfef4357_61532250')) {function content_51c1c0dfef4357_61532250($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.date_format.php';
?>
			<script type="text/javascript">
				var there_are = '<?php echo smartyTranslate(array('s'=>'There are'),$_smarty_tpl);?>
';
				var there_is = '<?php echo smartyTranslate(array('s'=>'There is'),$_smarty_tpl);?>
';
				var label_errors = '<?php echo smartyTranslate(array('s'=>'errors'),$_smarty_tpl);?>
';
				var label_error = '<?php echo smartyTranslate(array('s'=>'error'),$_smarty_tpl);?>
';
			</script>
			<div id="container">	
				<div id="error" <?php if (!isset($_smarty_tpl->tpl_vars['errors']->value)){?>class="hide"<?php }?>>
<?php if (isset($_smarty_tpl->tpl_vars['errors']->value)){?>
					<h3><?php if ($_smarty_tpl->tpl_vars['nbErrors']->value>1){?><?php echo smartyTranslate(array('s'=>'There are %d errors.','sprintf'=>$_smarty_tpl->tpl_vars['nbErrors']->value),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'There is %d error.','sprintf'=>$_smarty_tpl->tpl_vars['nbErrors']->value),$_smarty_tpl);?>
<?php }?></h3>
					<ol style="margin: 0 0 0 20px;">
					<?php  $_smarty_tpl->tpl_vars["error"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["error"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['errors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["error"]->key => $_smarty_tpl->tpl_vars["error"]->value){
$_smarty_tpl->tpl_vars["error"]->_loop = true;
?>
						<li><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</li>
					<?php } ?>
					</ol>
<?php }?>
				</div>
				<br />
<?php if (isset($_smarty_tpl->tpl_vars['warningSslMessage']->value)){?>
				<div class="warn"><?php echo $_smarty_tpl->tpl_vars['warningSslMessage']->value;?>
</div>
<?php }?>
				<div id="login">
					<h1><?php echo $_smarty_tpl->tpl_vars['shop_name']->value;?>
</h1>
<?php if (!isset($_smarty_tpl->tpl_vars['wrong_folder_name']->value)&&!isset($_smarty_tpl->tpl_vars['wrong_install_name']->value)){?>
						<form action="#" id="login_form" method="post">
							<div class="field">
								<label for="email"><?php echo smartyTranslate(array('s'=>'Email address:'),$_smarty_tpl);?>
</label>
								<input type="text" id="email" name="email" class="input email_field" value="<?php if (isset($_smarty_tpl->tpl_vars['email']->value)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['email']->value, 'htmlall', 'UTF-8');?>
<?php }?>" />
							</div>					
							<div class="field">
								<label for="passwd"><?php echo smartyTranslate(array('s'=>'Password:'),$_smarty_tpl);?>
</label>
								<input id="passwd" type="password" name="passwd" class="input password_field" value="<?php if (isset($_smarty_tpl->tpl_vars['password']->value)){?><?php echo $_smarty_tpl->tpl_vars['password']->value;?>
<?php }?>"/>
							</div>					
							<div class="field">
								<input type="submit" name="submitLogin" value="<?php echo smartyTranslate(array('s'=>'Log in'),$_smarty_tpl);?>
" class="button fl margin-right-5" />					
								<p class="fl no-margin hide ajax-loader">
									<img src="../img/loader.gif" alt="" />
								</p>					
								<p class="fr no-margin">
									<a href="#" class="show-forgot-password"><?php echo smartyTranslate(array('s'=>'Lost password?'),$_smarty_tpl);?>
</a>
								</p>
								<div class="clear"></div>
							</div>
							<input type="hidden" name="redirect" id="redirect" value="<?php echo $_smarty_tpl->tpl_vars['redirect']->value;?>
"/>
						</form>					
						<form action="#" id="forgot_password_form" method="post" class="hide">
							<h2 class="no-margin"><?php echo smartyTranslate(array('s'=>'Forgot your password?'),$_smarty_tpl);?>
</h2>
							<p class="bold"><?php echo smartyTranslate(array('s'=>'In order to receive your access code by email, please enter the address you provided during the registration process.'),$_smarty_tpl);?>
</p>					
							<div class="field">
								<label><?php echo smartyTranslate(array('s'=>'Email address:'),$_smarty_tpl);?>
</label>
								<input type="text" name="email_forgot" id="email_forgot" class="input email_field" />
							</div>					
							<div class="field">
								<input type="submit" name="submit" value="<?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
" class="button fl margin-right-5" />					
								<p class="fl no-margin hide ajax-loader">
									<img src="../img/loader.gif" alt=""  />
								</p>					
								<p class="fr no-margin">
									<a href="#" class="show-login-form"><?php echo smartyTranslate(array('s'=>'Back to login'),$_smarty_tpl);?>
</a>
								</p>
								<div class="clear"></div>
							</div>
						</form>
<?php }else{ ?>
						<div class="padding-30">
							<p><?php echo smartyTranslate(array('s'=>'For security reasons, you cannot connect to the Back Office until after you have:'),$_smarty_tpl);?>
</p>
							<ul>
								<?php if (isset($_smarty_tpl->tpl_vars['wrong_install_name']->value)&&$_smarty_tpl->tpl_vars['wrong_install_name']->value==true){?><li><?php echo smartyTranslate(array('s'=>'deleted the /install folder'),$_smarty_tpl);?>
</li><?php }?>
								<?php if (isset($_smarty_tpl->tpl_vars['wrong_folder_name']->value)&&$_smarty_tpl->tpl_vars['wrong_folder_name']->value==true){?><li><?php echo smartyTranslate(array('s'=>'renamed the /admin folder (e.g. %s)','sprintf'=>$_smarty_tpl->tpl_vars['randomNb']->value),$_smarty_tpl);?>
</li><?php }?>
							</ul>
							<p><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['adminUrl']->value, 'htmlall', 'UTF-8');?>
"><?php echo smartyTranslate(array('s'=>'Please then access this page by the new URL (e.g. %s)','sprintf'=>$_smarty_tpl->tpl_vars['adminUrl']->value),$_smarty_tpl);?>
</a></p>
						</div>
<?php }?>
				</div>
				<h2><a href="http://www.prestashop.com">&copy; 2005 - <?php echo smarty_modifier_date_format(time(),"%Y");?>
 Copyright by PrestaShop. all rights reserved.</a></h2>
			</div><?php }} ?>