<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:07
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/address.tpl" */ ?>
<?php /*%%SmartyHeaderCode:141721419851c1c0e769d004-52023159%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7a20fa113b682b6e9964e13ca8df39f6be99ff23' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/address.tpl',
      1 => 1371647167,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '141721419851c1c0e769d004-52023159',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'address' => 0,
    'countries' => 0,
    'country' => 0,
    'state' => 0,
    'id_address' => 0,
    'link' => 0,
    'vat_display' => 0,
    'ordered_adr_fields' => 0,
    'field_name' => 0,
    'token' => 0,
    'countries_list' => 0,
    'vatnumber_ajax_call' => 0,
    'ajaxurl' => 0,
    'stateExist' => 0,
    'select_address' => 0,
    'back' => 0,
    'mod' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e7b993c8_65129392',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e7b993c8_65129392')) {function content_51c1c0e7b993c8_65129392($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('default', 'page_title', null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Your address'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php echo $_smarty_tpl->getSubTemplate ('./page-title.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ("./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<script type="text/javascript">
// <![CDATA[
idSelectedCountry = <?php if (isset($_POST['id_state'])){?><?php echo intval($_POST['id_state']);?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->id_state)){?><?php echo intval($_smarty_tpl->tpl_vars['address']->value->id_state);?>
<?php }else{ ?>false<?php }?><?php }?>;
countries = new Array();
countriesNeedIDNumber = new Array();
countriesNeedZipCode = new Array();
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
$(function(){
	$('.id_state option[value=<?php if (isset($_POST['id_state'])){?><?php echo $_POST['id_state'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->id_state)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->id_state, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>]').attr('selected', 'selected');
});

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

//]]>
</script>

<div data-role="content" id="content">
	<div>
		<p>
		<?php if (isset($_smarty_tpl->tpl_vars['id_address']->value)&&(isset($_POST['alias'])||isset($_smarty_tpl->tpl_vars['address']->value->alias))){?>
			<?php echo smartyTranslate(array('s'=>'Modify address'),$_smarty_tpl);?>
 
			<?php if (isset($_POST['alias'])){?>
				"<?php echo $_POST['alias'];?>
"
			<?php }else{ ?>
				<?php if (isset($_smarty_tpl->tpl_vars['address']->value->alias)){?>"<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->alias, 'htmlall', 'UTF-8');?>
"<?php }?>
			<?php }?>
		<?php }else{ ?>
			<?php echo smartyTranslate(array('s'=>'To add a new address, please fill out the form below.'),$_smarty_tpl);?>

		<?php }?>
		</p>
		
		<form action="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('address',true);?>
" method="post" id="add_address" data-ajax="false">
			<legend><h3><?php if (isset($_smarty_tpl->tpl_vars['id_address']->value)&&$_smarty_tpl->tpl_vars['id_address']->value!=0){?><?php echo smartyTranslate(array('s'=>'Your address'),$_smarty_tpl);?>
<?php }else{ ?><?php echo smartyTranslate(array('s'=>'New address'),$_smarty_tpl);?>
<?php }?></h3></legend>
			<div class="required text dni">
				<label for="dni"><?php echo smartyTranslate(array('s'=>'Identification number'),$_smarty_tpl);?>
</label>
				<input type="text" class="text" name="dni" id="dni" value="<?php if (isset($_POST['dni'])){?><?php echo $_POST['dni'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->dni)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->dni, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
				<p><?php echo smartyTranslate(array('s'=>'DNI / NIF / NIE'),$_smarty_tpl);?>
 <sup>*</sup></p>
			</div>
			<?php if ($_smarty_tpl->tpl_vars['vat_display']->value==2){?>
			<div id="vat_area">
			<?php }elseif($_smarty_tpl->tpl_vars['vat_display']->value==1){?>
			<div id="vat_area" style="display: none;">
			<?php }else{ ?>
			<div style="display: none;">
			<?php }?>
				<div id="vat_number">
					<p class="text">
						<label for="vat_number"><?php echo smartyTranslate(array('s'=>'VAT number'),$_smarty_tpl);?>
</label>
						<input type="text" class="text" name="vat_number" value="<?php if (isset($_POST['vat_number'])){?><?php echo $_POST['vat_number'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->vat_number)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->vat_number, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
					</p>
				</div>
			</div>
			<?php $_smarty_tpl->tpl_vars["stateExist"] = new Smarty_variable("false", null, 0);?>
			<?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ordered_adr_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value){
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
?>
				<?php if ($_smarty_tpl->tpl_vars['field_name']->value=='company'){?>
				<div class="text">
					<input type="hidden" name="token" value="<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
" />
					<label for="company"><?php echo smartyTranslate(array('s'=>'Company'),$_smarty_tpl);?>
</label>
					<input type="text" id="company" name="company" value="<?php if (isset($_POST['company'])){?><?php echo $_POST['company'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->company)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->company, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
				</div>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['field_name']->value=='firstname'){?>
				<div class="required text">
					<label for="firstname"><?php echo smartyTranslate(array('s'=>'First name'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" name="firstname" id="firstname" value="<?php if (isset($_POST['firstname'])){?><?php echo $_POST['firstname'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->firstname)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->firstname, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
				</div>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['field_name']->value=='lastname'){?>
				<div class="required text">
					<label for="lastname"><?php echo smartyTranslate(array('s'=>'Last name'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" id="lastname" name="lastname" value="<?php if (isset($_POST['lastname'])){?><?php echo $_POST['lastname'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->lastname)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->lastname, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
				</div>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['field_name']->value=='address1'){?>
				<div class="required text">
					<label for="address1"><?php echo smartyTranslate(array('s'=>'Address'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" id="address1" name="address1" value="<?php if (isset($_POST['address1'])){?><?php echo $_POST['address1'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->address1)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->address1, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
				</div>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['field_name']->value=='address2'){?>
				<div class="required text">
					<label for="address2"><?php echo smartyTranslate(array('s'=>'Address (Line 2)'),$_smarty_tpl);?>
</label>
					<input type="text" id="address2" name="address2" value="<?php if (isset($_POST['address2'])){?><?php echo $_POST['address2'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->address2)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->address2, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
				</div>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['field_name']->value=='postcode'){?>
				<div class="required postcode text">
					<label for="postcode"><?php echo smartyTranslate(array('s'=>'Zip / Postal Code'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" id="postcode" name="postcode" value="<?php if (isset($_POST['postcode'])){?><?php echo $_POST['postcode'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->postcode)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->postcode, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
				</div>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['field_name']->value=='city'){?>
				<div class="required text">
					<label for="city"><?php echo smartyTranslate(array('s'=>'City'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<input type="text" name="city" id="city" value="<?php if (isset($_POST['city'])){?><?php echo $_POST['city'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->city)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->city, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" maxlength="64" />
				</div>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['field_name']->value=='Country:name'||$_smarty_tpl->tpl_vars['field_name']->value=='country'){?>
				<div class="required select">
					<label for="id_country"><?php echo smartyTranslate(array('s'=>'Country'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<select id="id_country" name="id_country"><?php echo $_smarty_tpl->tpl_vars['countries_list']->value;?>
</select>
				</div>
				<?php if ($_smarty_tpl->tpl_vars['vatnumber_ajax_call']->value){?>
				<script type="text/javascript">
				var ajaxurl = '<?php echo $_smarty_tpl->tpl_vars['ajaxurl']->value;?>
';
				
						$(document).ready(function(){
							$('#id_country').change(function() {
								$.ajax({
									type: "GET",
									url: ajaxurl+"vatnumber/ajax.php?id_country="+$('#id_country').val(),
									success: function(isApplicable){
										if(isApplicable == "1")
										{
											$('#vat_area').show();
											$('#vat_number').show();
										}
										else
										{
											$('#vat_area').hide();
										}
									}
								});
							});
						});
				
				</script>
				<?php }?>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['field_name']->value=='State:name'){?>
				<?php $_smarty_tpl->tpl_vars["stateExist"] = new Smarty_variable("true", null, 0);?>
				<div class="required id_state select">
					<label for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
				</div>
				<?php }?>
			<?php } ?>
			<?php if ($_smarty_tpl->tpl_vars['stateExist']->value=="false"){?>
			<div class="required id_state select">
				<label for="id_state"><?php echo smartyTranslate(array('s'=>'State'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<select name="id_state" id="id_state">
					<option value="">-</option>
				</select>
			</div>
			<?php }?>
			<div class="textarea">
				<label for="other"><?php echo smartyTranslate(array('s'=>'Additional information'),$_smarty_tpl);?>
</label>
				<textarea id="other" name="other" cols="26" rows="3"><?php if (isset($_POST['other'])){?><?php echo $_POST['other'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->other)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->other, 'htmlall', 'UTF-8');?>
<?php }?><?php }?></textarea>
			</div>
			
			<p><?php echo smartyTranslate(array('s'=>'You must register at least one phone number.'),$_smarty_tpl);?>
 <sup class="required">*</sup></p>
			<div class="text">
				<label for="phone"><?php echo smartyTranslate(array('s'=>'Home phone'),$_smarty_tpl);?>
</label>
				<input type="text" id="phone" name="phone" value="<?php if (isset($_POST['phone'])){?><?php echo $_POST['phone'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->phone)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->phone, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
			</div>
			<div class="text">
				<label for="phone_mobile"><?php echo smartyTranslate(array('s'=>'Mobile phone'),$_smarty_tpl);?>
</label>
				<input type="text" id="phone_mobile" name="phone_mobile" value="<?php if (isset($_POST['phone_mobile'])){?><?php echo $_POST['phone_mobile'];?>
<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['address']->value->phone_mobile)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->phone_mobile, 'htmlall', 'UTF-8');?>
<?php }?><?php }?>" />
			</div>
			<p class="required text" id="adress_alias">
				<label for="alias"><?php echo smartyTranslate(array('s'=>'Please assign an address title for future reference.'),$_smarty_tpl);?>
 <sup>*</sup></label>
				<input type="text" id="alias" name="alias" value="<?php if (isset($_POST['alias'])){?><?php echo $_POST['alias'];?>
<?php }elseif(isset($_smarty_tpl->tpl_vars['address']->value->alias)){?><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['address']->value->alias, 'htmlall', 'UTF-8');?>
<?php }elseif(isset($_smarty_tpl->tpl_vars['select_address']->value)){?><?php echo smartyTranslate(array('s'=>'My address'),$_smarty_tpl);?>
<?php }?>" />
			</p>
			<div>
				<?php if (isset($_smarty_tpl->tpl_vars['id_address']->value)){?><input type="hidden" name="id_address" value="<?php echo intval($_smarty_tpl->tpl_vars['id_address']->value);?>
" /><?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['back']->value)){?><input type="hidden" name="back" value="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
" /><?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['mod']->value)){?><input type="hidden" name="mod" value="<?php echo $_smarty_tpl->tpl_vars['mod']->value;?>
" /><?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['select_address']->value)){?><input type="hidden" name="select_address" value="<?php echo intval($_smarty_tpl->tpl_vars['select_address']->value);?>
" /><?php }?>
				<button type="submit" data-theme="a" name="submitAddress" value="submit-value" id="submitAddress" ><?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
</button>
			</div>
		</form>
	</div>
	
	<?php echo $_smarty_tpl->getSubTemplate ('./sitemap.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div><!-- /content --><?php }} ?>