<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:57
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/authentication.tpl" */ ?>
<?php /*%%SmartyHeaderCode:197038824751c1c0ddb93a23-63710815%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '908534e010017cd068f2c622950f00969247b679' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/authentication.tpl',
      1 => 1371646825,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '197038824751c1c0ddb93a23-63710815',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'countries' => 0,
    'country' => 0,
    'state' => 0,
    'address' => 0,
    'email_create' => 0,
    'back' => 0,
    'authentification_error' => 0,
    'account_error' => 0,
    'v' => 0,
    'link' => 0,
    'inOrderProcess' => 0,
    'PS_GUEST_CHECKOUT_ENABLED' => 0,
    'genders' => 0,
    'gender' => 0,
    'days' => 0,
    'day' => 0,
    'sl_day' => 0,
    'months' => 0,
    'k' => 0,
    'sl_month' => 0,
    'month' => 0,
    'years' => 0,
    'year' => 0,
    'sl_year' => 0,
    'newsletter' => 0,
    'dlv_all_fields' => 0,
    'field_name' => 0,
    'sl_country' => 0,
    'one_phone_at_least' => 0,
    'stateExist' => 0,
    'HOOK_CREATE_ACCOUNT_FORM' => 0,
    'HOOK_CREATE_ACCOUNT_TOP' => 0,
    'b2b_enable' => 0,
    'PS_REGISTRATION_PROCESS_TYPE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0de58f846_98627681',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0de58f846_98627681')) {function content_51c1c0de58f846_98627681($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Login'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<script type="text/javascript">
// <![CDATA[
var idSelectedCountry = <?php if (isset($_POST['id_state'])){?><?php echo intval($_POST['id_state']);?>
<?php }else{ ?>false<?php }?>;
var countries = new Array();
var countriesNeedIDNumber = new Array();
var countriesNeedZipCode = new Array(); 
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
].push({'id' : '<?php echo intval($_smarty_tpl->tpl_vars['state']->value['id_state']);?>
', 'name' : '<?php echo addslashes($_smarty_tpl->tpl_vars['state']->value['name']);?>
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
$(function(){
	$('.id_state option[value=<?php if (isset($_POST['id_state'])){?><?php echo intval($_POST['id_state']);?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value)){?><?php echo intval($_smarty_tpl->tpl_vars['address']->value->id_state);?>
<?php }?><?php }?>]').attr('selected', true);
});
//]]>

$(document).ready(function() {
	$('#company').blur(function(){
		vat_number();
	});
	vat_number();
	function vat_number()
	{
		if ($('#company').val() != '')
			$('#vat_number').show();
		else
			$('#vat_number').hide();
	}
});

</script>

<h1><?php if (!isset($_smarty_tpl->tpl_vars['email_create']->value)){?><?php echo smartyTranslate(array('s'=>'Log in'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'Create an account'),$_smarty_tpl);?>
<?php }?></h1>
<?php if (!isset($_smarty_tpl->tpl_vars['back']->value)||$_smarty_tpl->tpl_vars['back']->value!='my-account'){?><?php $_smarty_tpl->tpl_vars['current_step'] = new Smarty_variable('login', null, 0);?><?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./order-steps.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }?> 
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(false, null, 0);?>
<?php if (!isset($_smarty_tpl->tpl_vars['email_create']->value)){?>
	<script type="text/javascript">
	
	$(document).ready(function(){
		// Retrocompatibility with 1.4
		if (typeof baseUri === "undefined" && typeof baseDir !== "undefined")
		baseUri = baseDir;
		$('#create-account_form').submit(function(){
			submitFunction();
			return false;
		});
	});
	function submitFunction()
	{
		$('#create_account_error').html('').hide();
		//send the ajax request to the server
		$.ajax({
			type: 'POST',
			url: baseUri,
			async: true,
			cache: false,
			dataType : "json",
			data: {
				controller: 'authentication',
				SubmitCreate: 1,
				ajax: true,
				email_create: $('#email_create').val(),
				back: $('input[name=back]').val(),
				token: token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(error in jsonData.errors)
						//IE6 bug fix
						if(error != 'indexOf')
							errors += '<li>'+jsonData.errors[error]+'</li>';
					$('#create_account_error').html('<ol>'+errors+'</ol>').show();
				}
				else
				{
					// adding a div to display a transition
					$('#center_column').html('<div id="noSlide">'+$('#center_column').html()+'</div>');
					$('#noSlide').fadeOut('slow', function(){
						$('#noSlide').html(jsonData.page);
						// update the state (when this file is called from AJAX you still need to update the state)									
						bindStateInputAndUpdate();
						$(this).fadeIn('slow', function(){
							document.location = '#account-creation';
						});
					});
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert("TECHNICAL ERROR: unable to load form.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}
	
	</script>
	<!--<?php if (isset($_smarty_tpl->tpl_vars['authentification_error']->value)){?>
	<div class="error">
		<?php ob_start();?><?php echo count($_smarty_tpl->tpl_vars['authentification_error']->value);?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1==1){?>
			<p><?php echo smartyTranslate(array('s'=>'There\'s at least one error'),$_smarty_tpl);?>
 :</p>
			<?php }else{ ?>
			<p><?php echo smartyTranslate(array('s'=>'There are %s errors','sprintf'=>array(count($_smarty_tpl->tpl_vars['account_error']->value))),$_smarty_tpl);?>
 :</p>
		<?php }?>
		<ol>
			<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['authentification_error']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
				<li><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</li>
			<?php } ?>
		</ol>
	</div>
	<?php }?>-->
	<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true);?>
" method="post" id="create-account_form" class="std">
		<fieldset>
			<h3><?php echo smartyTranslate(array('s'=>'Create an account'),$_smarty_tpl);?>
</h3>
			<div class="form_content clearfix">
				<p class="title_block"><?php echo smartyTranslate(array('s'=>'Please enter your email address to create an account.'),$_smarty_tpl);?>
.</p>
				<div class="error" id="create_account_error" style="display:none"></div>
				<p class="text">
					<label for="email_create"><?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
</label>
					<span><input type="text" id="email_create" name="email_create" value="<?php if (isset($_POST['email_create'])){?><?php echo stripslashes($_POST['email_create']);?>
<?php }?>" class="account_input" /></span>
				</p>
				<p class="submit">
					<?php if (isset($_smarty_tpl->tpl_vars['back']->value)){?><input type="hidden" class="hidden" name="back" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['back']->value, 'htmlall', 'UTF-8');?>
" /><?php }?>
					<input type="submit" id="SubmitCreate" name="SubmitCreate" class="button_large" value="<?php echo smartyTranslate(array('s'=>'Create an account'),$_smarty_tpl);?>
" />
					<input type="hidden" class="hidden" name="SubmitCreate" value="<?php echo smartyTranslate(array('s'=>'Create an account'),$_smarty_tpl);?>
" />
				</p>
			</div>
		</fieldset>
	</form>

	<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true);?>
" method="post" id="login_form" class="std">
		<fieldset>
			<h3><?php echo smartyTranslate(array('s'=>'Already registered?'),$_smarty_tpl);?>
</h3>
			<div class="form_content clearfix">
				<p class="text">
					<label for="email"><?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
</label>
					<span><input type="text" id="email" name="email" value="<?php if (isset($_POST['email'])){?><?php echo stripslashes($_POST['email']);?>
<?php }?>" class="account_input" /></span>
				</p>
				<p class="text">
					<label for="passwd"><?php echo smartyTranslate(array('s'=>'Password'),$_smarty_tpl);?>
</label>
					<span><input type="password" id="passwd" name="passwd" value="<?php if (isset($_POST['passwd'])){?><?php echo stripslashes($_POST['passwd']);?>
<?php }?>" class="account_input" /></span>
				</p>
				<p class="lost_password"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('password');?>
"><?php echo smartyTranslate(array('s'=>'Forgot your password?'),$_smarty_tpl);?>
</a></p>
				<p class="submit">
					<?php if (isset($_smarty_tpl->tpl_vars['back']->value)){?><input type="hidden" class="hidden" name="back" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['back']->value, 'htmlall', 'UTF-8');?>
" /><?php }?>
					<input type="submit" id="SubmitLogin" name="SubmitLogin" class="button" value="<?php echo smartyTranslate(array('s'=>'Log in'),$_smarty_tpl);?>
" />
				</p>
			</div>
		</fieldset>
	</form>

	<?php if (isset($_smarty_tpl->tpl_vars['inOrderProcess']->value)&&$_smarty_tpl->tpl_vars['inOrderProcess']->value&&$_smarty_tpl->tpl_vars['PS_GUEST_CHECKOUT_ENABLED']->value){?>
	<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true,null,"back=".((string)$_smarty_tpl->tpl_vars['back']->value));?>
" method="post" id="new_account_form" class="std clearfix">
		<fieldset>
			<h3><?php echo smartyTranslate(array('s'=>'Instant checkout'),$_smarty_tpl);?>
</h3>
			<div id="opc_account_form" style="display: block; ">
				<!-- Account -->
				<p class="required text">
					<label for="guest_email"><?php echo smartyTranslate(array('s'=>'Email address'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" id="guest_email" name="guest_email" value="<?php if (isset($_POST['guest_email'])){?><?php echo $_POST['guest_email'];?>
<?php }?>" />
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
						<input type="radio" name="id_gender" id="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" value="<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
"<?php if (isset($_POST['id_gender'])&&$_POST['id_gender']==$_smarty_tpl->tpl_vars['gender']->value->id){?> checked="checked"<?php }?> />
						<label for="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" class="top"><?php echo $_smarty_tpl->tpl_vars['gender']->value->name;?>
</label>
					<?php } ?>
				</p>
				<p class="required text">
					<label for="firstname"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" id="firstname" name="firstname" onblur="$('#customer_firstname').val($(this).val());" value="<?php if (isset($_POST['firstname'])){?><?php echo $_POST['firstname'];?>
<?php }?>" />
					<input type="hidden" class="text" id="customer_firstname" name="customer_firstname" value="<?php if (isset($_POST['firstname'])){?><?php echo $_POST['firstname'];?>
<?php }?>" />
				</p>
				<p class="required text">
					<label for="lastname"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" id="lastname" name="lastname" onblur="$('#customer_lastname').val($(this).val());" value="<?php if (isset($_POST['lastname'])){?><?php echo $_POST['lastname'];?>
<?php }?>" />
					<input type="hidden" class="text" id="customer_lastname" name="customer_lastname" value="<?php if (isset($_POST['lastname'])){?><?php echo $_POST['lastname'];?>
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
							<option value="<?php echo $_smarty_tpl->tpl_vars['day']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_day']->value==$_smarty_tpl->tpl_vars['day']->value)){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['day']->value;?>
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
							<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_month']->value==$_smarty_tpl->tpl_vars['k']->value)){?> selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['month']->value),$_smarty_tpl);?>
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
							<option value="<?php echo $_smarty_tpl->tpl_vars['year']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_year']->value==$_smarty_tpl->tpl_vars['year']->value)){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['year']->value;?>
&nbsp;&nbsp;</option>
						<?php } ?>
					</select>
				</p>
				<?php if (isset($_smarty_tpl->tpl_vars['newsletter']->value)&&$_smarty_tpl->tpl_vars['newsletter']->value){?>
					<p class="checkbox">
						<input type="checkbox" name="newsletter" id="newsletter" value="1" <?php if (isset($_POST['newsletter'])&&$_POST['newsletter']=='1'){?>checked="checked"<?php }?> />
						<label for="newsletter"><?php echo smartyTranslate(array('s'=>'Sign up for our newsletter!'),$_smarty_tpl);?>
</label>
					</p>
					<p class="checkbox">
						<input type="checkbox" name="optin" id="optin" value="1" <?php if (isset($_POST['optin'])&&$_POST['optin']=='1'){?>checked="checked"<?php }?> />
						<label for="optin"><?php echo smartyTranslate(array('s'=>'Receive special offers from our partners!'),$_smarty_tpl);?>
</label>
					</p>
				<?php }?>
				<h3><?php echo smartyTranslate(array('s'=>'Delivery address'),$_smarty_tpl);?>
</h3>
				<?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dlv_all_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value){
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['field_name']->value=="company"){?>
						<p class="text">
							<label for="company"><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
</label>
							<input type="text" class="text" id="company" name="company" value="<?php if (isset($_POST['company'])){?><?php echo $_POST['company'];?>
<?php }?>" />
						</p>
						<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="vat_number"){?>
						<div id="vat_number" style="display:none;">
							<p class="text">
								<label for="vat_number"><?php echo smartyTranslate(array('s'=>'VAT number'),$_smarty_tpl);?>
</label>
								<input type="text" class="text" name="vat_number" value="<?php if (isset($_POST['vat_number'])){?><?php echo $_POST['vat_number'];?>
<?php }?>" />
							</p>
						</div>
						<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="address1"){?>
						<p class="required text">
							<label for="address1"><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="text" name="address1" id="address1" value="<?php if (isset($_POST['address1'])){?><?php echo $_POST['address1'];?>
<?php }?>" />
						</p>
						<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="postcode"){?>
						<p class="required postcode text">
							<label for="postcode"><?php echo smartyTranslate(array('s'=>'Zip / Postal Code'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="text" name="postcode" id="postcode" value="<?php if (isset($_POST['postcode'])){?><?php echo $_POST['postcode'];?>
<?php }?>" onblur="$('#postcode').val($('#postcode').val().toUpperCase());" />
						</p>
						<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="city"){?>
						<p class="required text">
							<label for="city"><?php echo smartyTranslate(array('s'=>'City'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<input type="text" class="text" name="city" id="city" value="<?php if (isset($_POST['city'])){?><?php echo $_POST['city'];?>
<?php }?>" />
						</p>
						<!--
							   if customer hasn't update his layout address, country has to be verified
							   but it's deprecated
						   -->
						<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="Country:name"||$_smarty_tpl->tpl_vars['field_name']->value=="country"){?>
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
" <?php if (($_smarty_tpl->tpl_vars['sl_country']->value==$_smarty_tpl->tpl_vars['v']->value['id_country'])){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value['name'];?>
</option>
								<?php } ?>
							</select>
						</p>
						<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="State:name"){?>
						<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(true, null, 0);?>

						<p class="required id_state select">
							<label for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
							<select name="id_state" id="id_state">
								<option value="">-</option>
							</select>
						</p>
						<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="phone"){?>
						<p class="<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?>required <?php }?>text">
							<label for="phone"><?php echo smartyTranslate(array('s'=>'Phone'),$_smarty_tpl);?>
<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?> <sup>*</sup><?php }?></label>
							<input type="text" class="text" name="phone" id="phone" value="<?php if (isset($_POST['phone'])){?><?php echo $_POST['phone'];?>
<?php }?>"/>
						</p>
					<?php }?>
				<?php } ?>
				<?php if ($_smarty_tpl->tpl_vars['stateExist']->value==false){?>
					<p class="required id_state select">
						<label for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
						<select name="id_state" id="id_state">
							<option value="">-</option>
						</select>
					</p>
				<?php }?>
				<input type="hidden" name="alias" id="alias" value="<?php echo smartyTranslate(array('s'=>'My address'),$_smarty_tpl);?>
" />
				<input type="hidden" name="is_new_customer" id="is_new_customer" value="0" />
				<!-- END Account -->
			</div>
		</fieldset>
		<fieldset class="account_creation dni">
			<h3><?php echo smartyTranslate(array('s'=>'Tax identification'),$_smarty_tpl);?>
</h3>
			<p class="required text">
				<label for="dni"><?php echo smartyTranslate(array('s'=>'Identification number'),$_smarty_tpl);?>
</label>
				<input type="text" class="text" name="dni" id="dni" value="<?php if (isset($_POST['dni'])){?><?php echo $_POST['dni'];?>
<?php }?>" />
				<span class="form_info"><?php echo smartyTranslate(array('s'=>'DNI / NIF / NIE'),$_smarty_tpl);?>
</span>
			</p>
		</fieldset>
		<?php echo $_smarty_tpl->tpl_vars['HOOK_CREATE_ACCOUNT_FORM']->value;?>

		<p class="cart_navigation required submit">
			<span><sup>*</sup><?php echo smartyTranslate(array('s'=>'Required field'),$_smarty_tpl);?>
</span>
			<input type="hidden" name="display_guest_checkout" value="1" />
			<input type="submit" class="exclusive" name="submitGuestAccount" id="submitGuestAccount" value="<?php echo smartyTranslate(array('s'=>'Continue'),$_smarty_tpl);?>
" />
		</p>
	</form>
	<?php }?>
<?php }else{ ?>
	<!--<?php if (isset($_smarty_tpl->tpl_vars['account_error']->value)){?>
	<div class="error">
		<?php ob_start();?><?php echo count($_smarty_tpl->tpl_vars['account_error']->value);?>
<?php $_tmp2=ob_get_clean();?><?php if ($_tmp2==1){?>
			<p><?php echo smartyTranslate(array('s'=>'There\'s at least one error'),$_smarty_tpl);?>
 :</p>
			<?php }else{ ?>
			<p><?php echo smartyTranslate(array('s'=>'There are %s errors','sprintf'=>array(count($_smarty_tpl->tpl_vars['account_error']->value))),$_smarty_tpl);?>
 :</p>
		<?php }?>
		<ol>
			<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['account_error']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
				<li><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</li>
			<?php } ?>
		</ol>
	</div>
	<?php }?>-->
<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('authentication',true);?>
" method="post" id="account-creation_form" class="std">
	<?php echo $_smarty_tpl->tpl_vars['HOOK_CREATE_ACCOUNT_TOP']->value;?>

	<fieldset class="account_creation">
		<h3><?php echo smartyTranslate(array('s'=>'Your personal information'),$_smarty_tpl);?>
</h3>
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
				<input type="radio" name="id_gender" id="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" value="<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" <?php if (isset($_POST['id_gender'])&&$_POST['id_gender']==$_smarty_tpl->tpl_vars['gender']->value->id){?>checked="checked"<?php }?> />
				<label for="id_gender<?php echo $_smarty_tpl->tpl_vars['gender']->value->id;?>
" class="top"><?php echo $_smarty_tpl->tpl_vars['gender']->value->name;?>
</label>
			<?php } ?>
		</p>
		<p class="required text">
			<label for="customer_firstname"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
			<input onkeyup="$('#firstname').val(this.value);" type="text" class="text" id="customer_firstname" name="customer_firstname" value="<?php if (isset($_POST['customer_firstname'])){?><?php echo $_POST['customer_firstname'];?>
<?php }?>" />
		</p>
		<p class="required text">
			<label for="customer_lastname"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
			<input onkeyup="$('#lastname').val(this.value);" type="text" class="text" id="customer_lastname" name="customer_lastname" value="<?php if (isset($_POST['customer_lastname'])){?><?php echo $_POST['customer_lastname'];?>
<?php }?>" />
		</p>
		<p class="required text">
			<label for="email"><?php echo smartyTranslate(array('s'=>'Email'),$_smarty_tpl);?>
 <sup>*</sup></label>
			<input type="text" class="text" id="email" name="email" value="<?php if (isset($_POST['email'])){?><?php echo $_POST['email'];?>
<?php }?>" />
		</p>
		<p class="required password">
			<label for="passwd"><?php echo smartyTranslate(array('s'=>'Password'),$_smarty_tpl);?>
 <sup>*</sup></label>
			<input type="password" class="text" name="passwd" id="passwd" />
			<span class="form_info"><?php echo smartyTranslate(array('s'=>'(Five characters minimum)'),$_smarty_tpl);?>
</span>
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
					<option value="<?php echo $_smarty_tpl->tpl_vars['day']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_day']->value==$_smarty_tpl->tpl_vars['day']->value)){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['day']->value;?>
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
					<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_month']->value==$_smarty_tpl->tpl_vars['k']->value)){?> selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['month']->value),$_smarty_tpl);?>
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
					<option value="<?php echo $_smarty_tpl->tpl_vars['year']->value;?>
" <?php if (($_smarty_tpl->tpl_vars['sl_year']->value==$_smarty_tpl->tpl_vars['year']->value)){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['year']->value;?>
&nbsp;&nbsp;</option>
				<?php } ?>
			</select>
		</p>
		<?php if ($_smarty_tpl->tpl_vars['newsletter']->value){?>
		<p class="checkbox" >
			<input type="checkbox" name="newsletter" id="newsletter" value="1" <?php if (isset($_POST['newsletter'])&&$_POST['newsletter']==1){?> checked="checked"<?php }?> />
			<label for="newsletter"><?php echo smartyTranslate(array('s'=>'Sign up for our newsletter!'),$_smarty_tpl);?>
</label>
		</p>
		<p class="checkbox" >
			<input type="checkbox"name="optin" id="optin" value="1" <?php if (isset($_POST['optin'])&&$_POST['optin']==1){?> checked="checked"<?php }?> />
			<label for="optin"><?php echo smartyTranslate(array('s'=>'Receive special offers from our partners!'),$_smarty_tpl);?>
</label>
		</p>
		<?php }?>
	</fieldset>
	<?php if ($_smarty_tpl->tpl_vars['b2b_enable']->value){?>
	<fieldset class="account_creation">
		<h3><?php echo smartyTranslate(array('s'=>'Your company information'),$_smarty_tpl);?>
</h3>
		<p class="text">
			<label for=""><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
</label>
			<input type="text" class="text" id="company" name="company" value="<?php if (isset($_POST['company'])){?><?php echo $_POST['company'];?>
<?php }?>" />
		</p>
		<p class="text">
			<label for="siret"><?php echo smartyTranslate(array('s'=>'SIRET'),$_smarty_tpl);?>
</label>
			<input type="text" class="text" id="siret" name="siret" value="<?php if (isset($_POST['siret'])){?><?php echo $_POST['siret'];?>
<?php }?>" />
		</p>
		<p class="text">
			<label for="ape"><?php echo smartyTranslate(array('s'=>'APE'),$_smarty_tpl);?>
</label>
			<input type="text" class="text" id="ape" name="ape" value="<?php if (isset($_POST['ape'])){?><?php echo $_POST['ape'];?>
<?php }?>" />
		</p>
		<p class="text">
			<label for="website"><?php echo smartyTranslate(array('s'=>'Website'),$_smarty_tpl);?>
</label>
			<input type="text" class="text" id="website" name="website" value="<?php if (isset($_POST['website'])){?><?php echo $_POST['website'];?>
<?php }?>" />
		</p>
	</fieldset>
	<?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['PS_REGISTRATION_PROCESS_TYPE']->value)&&$_smarty_tpl->tpl_vars['PS_REGISTRATION_PROCESS_TYPE']->value){?>
	<fieldset class="account_creation">
		<h3><?php echo smartyTranslate(array('s'=>'Your address'),$_smarty_tpl);?>
</h3>
		<?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dlv_all_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value){
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
?>
			<?php if ($_smarty_tpl->tpl_vars['field_name']->value=="company"){?>
				<p class="text">
					<label for="company"><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
</label>
					<input type="text" class="text" id="company" name="company" value="<?php if (isset($_POST['company'])){?><?php echo $_POST['company'];?>
<?php }?>" />
				</p>
			<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="vat_number"){?>
				<div id="vat_number" style="display:none;">
					<p class="text">
						<label for="vat_number"><?php echo smartyTranslate(array('s'=>'VAT number'),$_smarty_tpl);?>
</label>
						<input type="text" class="text" name="vat_number" value="<?php if (isset($_POST['vat_number'])){?><?php echo $_POST['vat_number'];?>
<?php }?>" />
					</p>
				</div>
			<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="firstname"){?>
				<p class="required text">
					<label for="firstname"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" id="firstname" name="firstname" value="<?php if (isset($_POST['firstname'])){?><?php echo $_POST['firstname'];?>
<?php }?>" />
				</p>
			<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="lastname"){?>
				<p class="required text">
					<label for="lastname"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" id="lastname" name="lastname" value="<?php if (isset($_POST['lastname'])){?><?php echo $_POST['lastname'];?>
<?php }?>" />
				</p>
			<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="address1"){?>
				<p class="required text">
					<label for="address1"><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" name="address1" id="address1" value="<?php if (isset($_POST['address1'])){?><?php echo $_POST['address1'];?>
<?php }?>" />
					<span class="inline-infos"><?php echo smartyTranslate(array('s'=>'Street address, P.O. Box, Company name, etc.'),$_smarty_tpl);?>
</span>
				</p>
			<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="address2"){?>
				<p class="text">
					<label for="address2"><?php echo smartyTranslate(array('s'=>'Address (Line 2)'),$_smarty_tpl);?>
</label>
					<input type="text" class="text" name="address2" id="address2" value="<?php if (isset($_POST['address2'])){?><?php echo $_POST['address2'];?>
<?php }?>" />
					<span class="inline-infos"><?php echo smartyTranslate(array('s'=>'Apartment, suite, unit, building, floor, etc...'),$_smarty_tpl);?>
</span>
				</p>
			<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="postcode"){?>
				<p class="required postcode text">
					<label for="postcode"><?php echo smartyTranslate(array('s'=>'Zip / Postal Code'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" name="postcode" id="postcode" value="<?php if (isset($_POST['postcode'])){?><?php echo $_POST['postcode'];?>
<?php }?>" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
				</p>
			<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="city"){?>
				<p class="required text">
					<label for="city"><?php echo smartyTranslate(array('s'=>'City'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" class="text" name="city" id="city" value="<?php if (isset($_POST['city'])){?><?php echo $_POST['city'];?>
<?php }?>" />
				</p>
				<!--
					if customer hasn't update his layout address, country has to be verified
					but it's deprecated
				-->
			<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="Country:name"||$_smarty_tpl->tpl_vars['field_name']->value=="country"){?>
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
" <?php if (($_smarty_tpl->tpl_vars['sl_country']->value==$_smarty_tpl->tpl_vars['v']->value['id_country'])){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value['name'];?>
</option>
						<?php } ?>
					</select>
				</p>
			<?php }elseif($_smarty_tpl->tpl_vars['field_name']->value=="State:name"||$_smarty_tpl->tpl_vars['field_name']->value=='state'){?>
				<?php $_smarty_tpl->tpl_vars['stateExist'] = new Smarty_variable(true, null, 0);?>
				<p class="required id_state select">
					<label for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
				</p>
			<?php }?>
		<?php } ?>
		<?php if ($_smarty_tpl->tpl_vars['stateExist']->value==false){?>
			<p class="required id_state select">
				<label for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<select name="id_state" id="id_state">
					<option value="">-</option>
				</select>
			</p>
		<?php }?>
		<p class="textarea">
			<label for="other"><?php echo smartyTranslate(array('s'=>'Additional information'),$_smarty_tpl);?>
</label>
			<textarea name="other" id="other" cols="26" rows="3"><?php if (isset($_POST['other'])){?><?php echo $_POST['other'];?>
<?php }?></textarea>
		</p>
		<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?>
			<p class="inline-infos"><?php echo smartyTranslate(array('s'=>'You must register at least one phone number.'),$_smarty_tpl);?>
</p>
		<?php }?>
		<p class="text">
			<label for="phone"><?php echo smartyTranslate(array('s'=>'Home phone'),$_smarty_tpl);?>
</label>
			<input type="text" class="text" name="phone" id="phone" value="<?php if (isset($_POST['phone'])){?><?php echo $_POST['phone'];?>
<?php }?>" />
		</p>
		<p class="<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?>required <?php }?> text">
			<label for="phone_mobile"><?php echo smartyTranslate(array('s'=>'Mobile phone'),$_smarty_tpl);?>
<?php if (isset($_smarty_tpl->tpl_vars['one_phone_at_least']->value)&&$_smarty_tpl->tpl_vars['one_phone_at_least']->value){?> <sup>*</sup><?php }?></label>
			<input type="text" class="text" name="phone_mobile" id="phone_mobile" value="<?php if (isset($_POST['phone_mobile'])){?><?php echo $_POST['phone_mobile'];?>
<?php }?>" />
		</p>
		<p class="required text" id="address_alias">
			<label for="alias"><?php echo smartyTranslate(array('s'=>'Assign an address alias for future reference.'),$_smarty_tpl);?>
 <sup>*</sup></label>
			<input type="text" class="text" name="alias" id="alias" value="<?php if (isset($_POST['alias'])){?><?php echo $_POST['alias'];?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'My address'),$_smarty_tpl);?>
<?php }?>" />
		</p>
	</fieldset>
	<fieldset class="account_creation dni">
		<h3><?php echo smartyTranslate(array('s'=>'Tax identification'),$_smarty_tpl);?>
</h3>
		<p class="required text">
			<label for="dni"><?php echo smartyTranslate(array('s'=>'Identification number'),$_smarty_tpl);?>
 <sup>*</sup></label>
			<input type="text" class="text" name="dni" id="dni" value="<?php if (isset($_POST['dni'])){?><?php echo $_POST['dni'];?>
<?php }?>" />
			<span class="form_info"><?php echo smartyTranslate(array('s'=>'DNI / NIF / NIE'),$_smarty_tpl);?>
</span>
		</p>
	</fieldset>
	<?php }?>
	<?php echo $_smarty_tpl->tpl_vars['HOOK_CREATE_ACCOUNT_FORM']->value;?>

	<p class="cart_navigation required submit">
		<input type="hidden" name="email_create" value="1" />
		<input type="hidden" name="is_new_customer" value="1" />
		<?php if (isset($_smarty_tpl->tpl_vars['back']->value)){?><input type="hidden" class="hidden" name="back" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['back']->value, 'htmlall', 'UTF-8');?>
" /><?php }?>
		<input type="submit" name="submitAccount" id="submitAccount" value="<?php echo smartyTranslate(array('s'=>'Register'),$_smarty_tpl);?>
" class="exclusive" />
		<span><sup>*</sup><?php echo smartyTranslate(array('s'=>'Required field'),$_smarty_tpl);?>
</span>
	</p>
</form>
<?php }?><?php }} ?>