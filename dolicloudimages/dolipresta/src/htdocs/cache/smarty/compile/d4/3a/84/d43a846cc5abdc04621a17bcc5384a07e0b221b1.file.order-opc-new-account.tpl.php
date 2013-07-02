<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:02
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/order-opc-new-account.tpl" */ ?>
<?php /*%%SmartyHeaderCode:22352754951c1c0e2185729-37185296%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd43a846cc5abdc04621a17bcc5384a07e0b221b1' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/order-opc-new-account.tpl',
      1 => 1371646831,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22352754951c1c0e2185729-37185296',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'back' => 0,
    'HOOK_CREATE_ACCOUNT_TOP' => 0,
    'guestInformations' => 0,
    'countries' => 0,
    'country' => 0,
    'state' => 0,
    'genders' => 0,
    'gender' => 0,
    'days' => 0,
    'day' => 0,
    'months' => 0,
    'k' => 0,
    'month' => 0,
    'years' => 0,
    'year' => 0,
    'newsletter' => 0,
    'dlv_all_fields' => 0,
    'field_name' => 0,
    'v' => 0,
    'sl_country' => 0,
    'stateExist' => 0,
    'one_phone_at_least' => 0,
    'inv_all_fields' => 0,
    'HOOK_CREATE_ACCOUNT_FORM' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e27b62c8_95961002',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e27b62c8_95961002')) {function content_51c1c0e27b62c8_95961002($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?><div id="opc_new_account" class="opc-main-block">
	<div id="opc_new_account-overlay" class="opc-overlay" style="display: none;"></div>
	<h2><span>1</span> <?php echo smartyTranslate(array('s'=>'Account'),$_smarty_tpl);?>
</h2>
	<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true,null,"back=order-opc");?>
" method="post" id="login_form" class="std">
		<fieldset>
			<h3><?php echo smartyTranslate(array('s'=>'Already registered?'),$_smarty_tpl);?>
</h3>
			<p><a href="#" id="openLoginFormBlock">&raquo; <?php echo smartyTranslate(array('s'=>'Click here'),$_smarty_tpl);?>
</a></p>
			<div id="login_form_content" style="display:none;">
				<!-- Error return block -->
				<div id="opc_login_errors" class="error" style="display:none;"></div>
				<!-- END Error return block -->
				<div style="margin-left:40px;margin-bottom:5px;float:left;width:40%;">
					<label for="login_email"><?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
</label>
					<span><input type="text" id="login_email" name="email" /></span>
				</div>
				<div style="margin-left:40px;margin-bottom:5px;float:left;width:40%;">
					<label for="login_passwd"><?php echo smartyTranslate(array('s'=>'Password'),$_smarty_tpl);?>
</label>
					<span><input type="password" id="login_passwd" name="login_passwd" /></span>
					<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('password',true);?>
" class="lost_password"><?php echo smartyTranslate(array('s'=>'Forgot your password?'),$_smarty_tpl);?>
</a>
				</div>
				<p class="submit">
					<?php if (isset($_smarty_tpl->tpl_vars['back']->value)){?><input type="hidden" class="hidden" name="back" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['back']->value, 'htmlall', 'UTF-8');?>
" /><?php }?>
					<input type="submit" id="SubmitLogin" name="SubmitLogin" class="button" value="<?php echo smartyTranslate(array('s'=>'Login'),$_smarty_tpl);?>
" />
				</p>
			</div>
		</fieldset>
	</form>
	<form action="javascript:;" method="post" id="new_account_form" class="std" autocomplete="on" autofill="on">
		<fieldset>
			<h3 id="new_account_title"><?php echo smartyTranslate(array('s'=>'New Customer'),$_smarty_tpl);?>
</h3>
			<div id="opc_account_choice">
				<div class="opc_float">
					<p class="title_block"><?php echo smartyTranslate(array('s'=>'Instant Checkout'),$_smarty_tpl);?>
</p>
					<p>
						<input type="button" class="exclusive_large" id="opc_guestCheckout" value="<?php echo smartyTranslate(array('s'=>'Guest checkout'),$_smarty_tpl);?>
" />
					</p>
				</div>

				<div class="opc_float">
					<p class="title_block"><?php echo smartyTranslate(array('s'=>'Create your account today and enjoy:'),$_smarty_tpl);?>
</p>
					<ul class="bullet">
						<li><?php echo smartyTranslate(array('s'=>'Personalized and secure access'),$_smarty_tpl);?>
</li>
						<li><?php echo smartyTranslate(array('s'=>'A fast and easy check out process'),$_smarty_tpl);?>
</li>
						<li><?php echo smartyTranslate(array('s'=>'Separate billing and shipping addresses'),$_smarty_tpl);?>
</li>
					</ul>
					<p>
						<input type="button" class="button_large" id="opc_createAccount" value="<?php echo smartyTranslate(array('s'=>'Create an account'),$_smarty_tpl);?>
" />
					</p>
				</div>
				<div class="clear"></div>
			</div>
			<div id="opc_account_form">
				<?php echo $_smarty_tpl->tpl_vars['HOOK_CREATE_ACCOUNT_TOP']->value;?>

				<script type="text/javascript">
				// <![CDATA[
				idSelectedCountry = <?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['id_state']){?><?php echo intval($_smarty_tpl->tpl_vars['guestInformations']->value['id_state']);?>
<?php }else{ ?>false<?php }?>;
				<?php if (isset($_smarty_tpl->tpl_vars['countries']->value)){?>
					<?php  $_smarty_tpl->tpl_vars['country'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['country']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['country']->key => $_smarty_tpl->tpl_vars['country']->value){
$_smarty_tpl->tpl_vars['country']->_loop = true;
?>
						<?php if (isset($_smarty_tpl->tpl_vars['country']->value['states'])&&$_smarty_tpl->tpl_vars['country']->value['contains_states']){?>
							countries[<?php echo intval($_smarty_tpl->tpl_vars['country']->value['id_country']);?>
] = new Array();
							<?php  $_smarty_tpl->tpl_vars['state'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['state']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['country']->value['states']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['state']->key => $_smarty_tpl->tpl_vars['state']->value){
$_smarty_tpl->tpl_vars['state']->_loop = true;
?>
								countries[<?php echo intval($_smarty_tpl->tpl_vars['country']->value['id_country']);?>
].push({'id' : '<?php echo $_smarty_tpl->tpl_vars['state']->value['id_state'];?>
', 'name' : '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['state']->value['name'], 'htmlall', 'UTF-8');?>
'});
							<?php } ?>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['country']->value['need_identification_number']){?>
							countriesNeedIDNumber.push(<?php echo intval($_smarty_tpl->tpl_vars['country']->value['id_country']);?>
);
						<?php }?>	
						<?php if (isset($_smarty_tpl->tpl_vars['country']->value['need_zip_code'])){?>
							countriesNeedZipCode[<?php echo intval($_smarty_tpl->tpl_vars['country']->value['id_country']);?>
] = <?php echo $_smarty_tpl->tpl_vars['country']->value['need_zip_code'];?>
;
						<?php }?>
					<?php } ?>
				<?php }?>
				//]]>
				
				function vat_number()
				{
					if ($('#company').val() != '')
						$('#vat_number_block').show();
					else
						$('#vat_number_block').hide();
				}
				function vat_number_invoice()
				{
					if ($('#company_invoice').val() != '')
						$('#vat_number_block_invoice').show();
					else
						$('#vat_number_block_invoice').hide();
				}
				
				$(document).ready(function() {
					$('#company').blur(function(){
						vat_number();
					});
					$('#company_invoice').blur(function(){
						vat_number_invoice();
					});
					vat_number();
					vat_number_invoice();
				});
				
				</script>
				<!-- Error return block -->
				<div id="opc_account_errors" class="error" style="display:none;"></div>
				<!-- END Error return block -->
				<!-- Account -->
				<input type="hidden" id="is_new_customer" name="is_new_customer" value="0" />
				<input type="hidden" id="opc_id_customer" name="opc_id_customer" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['id_customer']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['id_customer'];?>
<?php }else{ ?>0<?php }?>" />
				<input type="hidden" id="opc_id_address_delivery" name="opc_id_address_delivery" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['id_address_delivery']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['id_address_delivery'];?>
<?php }else{ ?>0<?php }?>" />
				<input type="hidden" id="opc_id_address_invoice" name="opc_id_address_invoice" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['id_address_delivery']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['id_address_delivery'];?>
<?php }else{ ?>0<?php }?>" />
				<p class="required text">
					<label for="email"><?php echo smartyTranslate(array('s'=>'Email'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" id="email" name="email" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['email']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['email'];?>
<?php }?>" />
				</p>
				<p class="required password is_customer_param">
					<label for="passwd"><?php echo smartyTranslate(array('s'=>'Password'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="password" class="text" name="passwd" id="passwd" />
					<span class="form_info"><?php echo smartyTranslate(array('s'=>'(five characters min.)'),$_smarty_tpl);?>
</span>
				</p>
				<p class="radio required">
					<span><?php echo smartyTranslate(array('s'=>'Title'),$_smarty_tpl);?>
</span>
					<?php  $_smarty_tpl->tpl_vars['gender'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['gender']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['genders']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['gender']->key => $_smarty_tpl->tpl_vars['gender']->value){
$_smarty_tpl->tpl_vars['gender']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['gender']->key;
?>
						<input type="radio" name="id_gender" id="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id_gender;?>
" value="<?php echo $_smarty_tpl->tpl_vars['gender']->value->id_gender;?>
" <?php if (isset($_POST['id_gender'])&&$_POST['id_gender']==$_smarty_tpl->tpl_vars['gender']->value->id_gender){?>checked="checked"<?php }?> />
						<label for="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id_gender;?>
" class="top"><?php echo $_smarty_tpl->tpl_vars['gender']->value->name;?>
</label>
					<?php } ?>
				</p>
				<p class="required text">
					<label for="firstname"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" id="customer_firstname" name="customer_firstname" onblur="$('#firstname').val($(this).val());" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['customer_firstname']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['customer_firstname'];?>
<?php }?>" />
				</p>
				<p class="required text">
					<label for="lastname"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" id="customer_lastname" name="customer_lastname" onblur="$('#lastname').val($(this).val());" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['customer_lastname']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['customer_lastname'];?>
<?php }?>" />
				</p>
				<p class="select">
					<span><?php echo smartyTranslate(array('s'=>'Date of Birth'),$_smarty_tpl);?>
</span>
					<select id="days" name="days">
						<option value="">-</option>
						<?php  $_smarty_tpl->tpl_vars['day'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['day']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['days']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['day']->key => $_smarty_tpl->tpl_vars['day']->value){
$_smarty_tpl->tpl_vars['day']->_loop = true;
?>
							<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['day']->value, 'htmlall', 'UTF-8');?>
" <?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&($_smarty_tpl->tpl_vars['guestInformations']->value['sl_day']==$_smarty_tpl->tpl_vars['day']->value)){?> selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['day']->value, 'htmlall', 'UTF-8');?>
&nbsp;&nbsp;</option>
						<?php } ?>
					</select>
					
					<select id="months" name="months">
						<option value="">-</option>
						<?php  $_smarty_tpl->tpl_vars['month'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['month']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['months']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['month']->key => $_smarty_tpl->tpl_vars['month']->value){
$_smarty_tpl->tpl_vars['month']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['month']->key;
?>
							<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['k']->value, 'htmlall', 'UTF-8');?>
" <?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&($_smarty_tpl->tpl_vars['guestInformations']->value['sl_month']==$_smarty_tpl->tpl_vars['k']->value)){?> selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['month']->value),$_smarty_tpl);?>
&nbsp;</option>
						<?php } ?>
					</select>
					<select id="years" name="years">
						<option value="">-</option>
						<?php  $_smarty_tpl->tpl_vars['year'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['year']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['years']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['year']->key => $_smarty_tpl->tpl_vars['year']->value){
$_smarty_tpl->tpl_vars['year']->_loop = true;
?>
							<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['year']->value, 'htmlall', 'UTF-8');?>
" <?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&($_smarty_tpl->tpl_vars['guestInformations']->value['sl_year']==$_smarty_tpl->tpl_vars['year']->value)){?> selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['year']->value, 'htmlall', 'UTF-8');?>
&nbsp;&nbsp;</option>
						<?php } ?>
					</select>
				</p>
				<?php if (isset($_smarty_tpl->tpl_vars['newsletter']->value)&&$_smarty_tpl->tpl_vars['newsletter']->value){?>
				<p class="checkbox">
					<input type="checkbox" name="newsletter" id="newsletter" value="1" <?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['newsletter']){?>checked="checked"<?php }?> />
					<label for="newsletter"><?php echo smartyTranslate(array('s'=>'Sign up for our newsletter!'),$_smarty_tpl);?>
</label>
				</p>
				<p class="checkbox" >
					<input type="checkbox"name="optin" id="optin" value="1" <?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['optin']){?>checked="checked"<?php }?> />
					<label for="optin"><?php echo smartyTranslate(array('s'=>'Receive special offers from our partners!'),$_smarty_tpl);?>
</label>
				</p>
				<?php }?>
				<h3><?php echo smartyTranslate(array('s'=>'Delivery address'),$_smarty_tpl);?>
</h3>
				<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(false, null, 0);?>
				<?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dlv_all_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value){
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
?>
				<?php if ($_smarty_tpl->tpl_vars['field_name']->value=="company"){?>
				<p class="text">
					<label for="company"><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
</label>
					<input type="text" class="text" id="company" name="company" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['company']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['company'];?>
<?php }?>" />
				</p>
				<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="firstname"){?>
				<p class="required text">
					<label for="firstname"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" id="firstname" name="firstname" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['firstname']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['firstname'];?>
<?php }?>" />
				</p>
				<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="lastname"){?>
				<p class="required text">
					<label for="lastname"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" id="lastname" name="lastname" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['lastname']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['lastname'];?>
<?php }?>" />
				</p>
				<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="address1"){?>
				<p class="required text">
					<label for="address1"><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" name="address1" id="address1" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['address1']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['address1'];?>
<?php }?>" />
				</p>
				<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="address2"){?>
				<p class="text is_customer_param">
					<label for="address2"><?php echo smartyTranslate(array('s'=>'Address (Line 2)'),$_smarty_tpl);?>
</label>
					<input type="text" class="text" name="address2" id="address2" value="" />
				</p>
				<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="postcode"){?>
				<p class="required postcode text">
					<label for="postcode"><?php echo smartyTranslate(array('s'=>'Zip / Postal code'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" name="postcode" id="postcode" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['postcode']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['postcode'];?>
<?php }?>" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
				</p>
				<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="city"){?>
				<p class="required text">
					<label for="city"><?php echo smartyTranslate(array('s'=>'City'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" name="city" id="city" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['city']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['city'];?>
<?php }?>" />
					
				</p>
				<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="country"||$_smarty_tpl->tpl_vars['field_name']->value=="Country:name"){?>
				<p class="required select">
					<label for="id_country"><?php echo smartyTranslate(array('s'=>'Country'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<select name="id_country" id="id_country">
						<option value="">-</option>
						<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['id_country'];?>
" <?php if ((isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['id_country']==$_smarty_tpl->tpl_vars['v']->value['id_country'])||(!isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['sl_country']->value==$_smarty_tpl->tpl_vars['v']->value['id_country'])){?> selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['v']->value['name'], 'htmlall', 'UTF-8');?>
</option>
						<?php } ?>
					</select>
				</p>
				<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="vat_number"){?>	
				<div id="vat_number_block" style="display:none;">
					<p class="text">
						<label for="vat_number"><?php echo smartyTranslate(array('s'=>'VAT number'),$_smarty_tpl);?>
</label>
						<input type="text" class="text" name="vat_number" id="vat_number" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['vat_number']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['vat_number'];?>
<?php }?>" />
					</p>
				</div>
				<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="state"||$_smarty_tpl->tpl_vars['field_name']->value=='State:name'){?>
				<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(true, null, 0);?>
				<p class="required id_state select" style="display:none;">
					<label for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
				</p>
				<?php }?>
				<?php } ?>
				<p class="required text dni">
					<label for="dni"><?php echo smartyTranslate(array('s'=>'Identification number'),$_smarty_tpl);?>
</label>
					<input type="text" class="text" name="dni" id="dni" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['dni']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['dni'];?>
<?php }?>" />
					<span class="form_info"><?php echo smartyTranslate(array('s'=>'DNI / NIF / NIE'),$_smarty_tpl);?>
</span>
				</p>
				<?php if (!$_smarty_tpl->tpl_vars['stateExist']->value){?>
				<p class="required id_state select">
					<label for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
				</p>
				<?php }?>
				<p class="textarea is_customer_param">
					<label for="other"><?php echo smartyTranslate(array('s'=>'Additional information'),$_smarty_tpl);?>
</label>
					<textarea name="other" id="other" cols="26" rows="3"></textarea>
				</p>
				<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?>
					<p class="inline-infos required is_customer_param"><?php echo smartyTranslate(array('s'=>'You must register at least one phone number.'),$_smarty_tpl);?>
</p>
				<?php }?>								
				<p class="text is_customer_param">
					<label for="phone"><?php echo smartyTranslate(array('s'=>'Home phone'),$_smarty_tpl);?>
</label>
					<input type="text" class="text" name="phone" id="phone" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['phone']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['phone'];?>
<?php }?>" />
				</p>
				<p class="<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?>required <?php }?>text">
					<label for="phone_mobile"><?php echo smartyTranslate(array('s'=>'Mobile phone'),$_smarty_tpl);?>
<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?> <sup>*</sup><?php }?></label>
					<input type="text" class="text" name="phone_mobile" id="phone_mobile" value="" />
				</p>
				<input type="hidden" name="alias" id="alias" value="<?php echo smartyTranslate(array('s'=>'My address'),$_smarty_tpl);?>
" />

				<p class="checkbox is_customer_param">
					<input type="checkbox" name="invoice_address" id="invoice_address" />
					<label for="invoice_address"><b><?php echo smartyTranslate(array('s'=>'Please use another address for invoice'),$_smarty_tpl);?>
</b></label>
				</p>

				<div id="opc_invoice_address" class="is_customer_param">
					<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(false, null, 0);?>
					<h3><?php echo smartyTranslate(array('s'=>'Invoice address'),$_smarty_tpl);?>
</h3>
					<?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['inv_all_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value){
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['field_name']->value=="company"){?>
					<p class="text is_customer_param">
						<label for="company_invoice"><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
</label>
						<input type="text" class="text" id="company_invoice" name="company_invoice" value="" />
					</p>
					<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="vat_number"){?>
					<div id="vat_number_block_invoice" class="is_customer_param" style="display:none;">
						<p class="text">
							<label for="vat_number_invoice"><?php echo smartyTranslate(array('s'=>'VAT number'),$_smarty_tpl);?>
</label>
							<input type="text" class="text" id="vat_number_invoice" name="vat_number_invoice" value="" />
						</p>
					</div>
					<p class="required text dni_invoice">
						<label for="dni"><?php echo smartyTranslate(array('s'=>'Identification number'),$_smarty_tpl);?>
</label>
						<input type="text" class="text" name="dni_invoice" id="dni_invoice" value="<?php if (isset($_smarty_tpl->tpl_vars['guestInformations']->value)&&$_smarty_tpl->tpl_vars['guestInformations']->value['dni']){?><?php echo $_smarty_tpl->tpl_vars['guestInformations']->value['dni'];?>
<?php }?>" />
						<span class="form_info"><?php echo smartyTranslate(array('s'=>'DNI / NIF / NIE'),$_smarty_tpl);?>
</span>
					</p>
					<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="firstname"){?>
					<p class="required text">
						<label for="firstname_invoice"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<input type="text" class="text" id="firstname_invoice" name="firstname_invoice" value="" />
					</p>
					<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="lastname"){?>
					<p class="required text">
						<label for="lastname_invoice"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<input type="text" class="text" id="lastname_invoice" name="lastname_invoice" value="" />
					</p>
					<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="address1"){?>
					<p class="required text">
						<label for="address1_invoice"><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<input type="text" class="text" name="address1_invoice" id="address1_invoice" value="" />
					</p>
					<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="address2"){?>
					<p class="text is_customer_param">
						<label for="address2_invoice"><?php echo smartyTranslate(array('s'=>'Address (Line 2)'),$_smarty_tpl);?>
</label>
						<input type="text" class="text" name="address2_invoice" id="address2_invoice" value="" />
					</p>
					<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="postcode"){?>
					<p class="required postcode text">
						<label for="postcode_invoice"><?php echo smartyTranslate(array('s'=>'Zip / Postal Code'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<input type="text" class="text" name="postcode_invoice" id="postcode_invoice" value="" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					</p>
					<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="city"){?>
					<p class="required text">
						<label for="city_invoice"><?php echo smartyTranslate(array('s'=>'City'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<input type="text" class="text" name="city_invoice" id="city_invoice" value="" />
					</p>
					<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="country"||$_smarty_tpl->tpl_vars['field_name']->value=="Country:name"){?>
					<p class="required select">
						<label for="id_country_invoice"><?php echo smartyTranslate(array('s'=>'Country'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<select name="id_country_invoice" id="id_country_invoice">
							<option value="">-</option>
							<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['countries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['id_country'];?>
" <?php if (($_smarty_tpl->tpl_vars['sl_country']->value==$_smarty_tpl->tpl_vars['v']->value['id_country'])){?> selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['v']->value['name'], 'htmlall', 'UTF-8');?>
</option>
							<?php } ?>
						</select>
					</p>
					<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="state"||$_smarty_tpl->tpl_vars['field_name']->value=='State:name'){?>
					<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(true, null, 0);?>
					<p class="required id_state_invoice select" style="display:none;">
						<label for="id_state_invoice"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<select name="id_state_invoice" id="id_state_invoice">
							<option value="">-</option>
						</select>
					</p>
					<?php }?>
					<?php } ?>
					<?php if (!$_smarty_tpl->tpl_vars['stateExist']->value){?>
					<p class="required id_state_invoice select" style="display:none;">
						<label for="id_state_invoice"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<select name="id_state_invoice" id="id_state_invoice">
							<option value="">-</option>
						</select>
					</p>
					<?php }?>
					<p class="textarea is_customer_param">
						<label for="other_invoice"><?php echo smartyTranslate(array('s'=>'Additional information'),$_smarty_tpl);?>
</label>
						<textarea name="other_invoice" id="other_invoice" cols="26" rows="3"></textarea>
					</p>
					<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?>
						<p class="inline-infos required"><?php echo smartyTranslate(array('s'=>'You must register at least one phone number.'),$_smarty_tpl);?>
</p>
					<?php }?>					
					<p class="text">
						<label for="phone_invoice"><?php echo smartyTranslate(array('s'=>'Home phone'),$_smarty_tpl);?>
</label>
						<input type="text" class="text" name="phone_invoice" id="phone_invoice" value="" />
					</p>
					<p class="<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?>required <?php }?>text is_customer_param">
						<label for="phone_mobile_invoice"><?php echo smartyTranslate(array('s'=>'Mobile phone'),$_smarty_tpl);?>
<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?> <sup>*</sup><?php }?></label>
						<input type="text" class="text" name="phone_mobile_invoice" id="phone_mobile_invoice" value="" />
					</p>
					<input type="hidden" name="alias_invoice" id="alias_invoice" value="<?php echo smartyTranslate(array('s'=>'My Invoice address'),$_smarty_tpl);?>
" />
				</div>
				<?php echo $_smarty_tpl->tpl_vars['HOOK_CREATE_ACCOUNT_FORM']->value;?>

				<p class="submit">
					<input type="submit" class="exclusive button" name="submitAccount" id="submitAccount" value="<?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
" />
				</p>
				<p style="display: none;" id="opc_account_saved">
					<?php echo smartyTranslate(array('s'=>'Account information saved successfully'),$_smarty_tpl);?>

				</p>
				<p class="required opc-required" style="clear: both;">
					<sup>*</sup><?php echo smartyTranslate(array('s'=>'Required field'),$_smarty_tpl);?>

				</p>
				<!-- END Account -->
			</div>
		</fieldset>
	</form>
	<div class="clear"></div>
</div><?php }} ?>