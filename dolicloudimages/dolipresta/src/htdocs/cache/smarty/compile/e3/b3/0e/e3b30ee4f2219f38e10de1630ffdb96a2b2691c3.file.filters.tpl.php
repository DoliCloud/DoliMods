<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:00
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/modules/filters.tpl" */ ?>
<?php /*%%SmartyHeaderCode:32305473251c1c0e025b6a1-05009301%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e3b30ee4f2219f38e10de1630ffdb96a2b2691c3' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/modules/filters.tpl',
      1 => 1371647779,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '32305473251c1c0e025b6a1-05009301',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'add_permission' => 0,
    'logged_on_addons' => 0,
    'username_addons' => 0,
    'check_url_fopen' => 0,
    'check_openssl' => 0,
    'showTypeModules' => 0,
    'list_modules_authors' => 0,
    'module_author' => 0,
    'status' => 0,
    'showInstalledModules' => 0,
    'showEnabledModules' => 0,
    'showCountryModules' => 0,
    'nameCountryDefault' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e03faeb3_63369924',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e03faeb3_63369924')) {function content_51c1c0e03faeb3_63369924($_smarty_tpl) {?>

<?php if ($_smarty_tpl->tpl_vars['add_permission']->value=='1'){?>
	<?php if (isset($_smarty_tpl->tpl_vars['logged_on_addons']->value)){?>
			<!--start addons login-->
			<div class="filter-module" id="addons_login_div">
				<p><?php echo smartyTranslate(array('s'=>'You are logged into PrestaShop Addons.'),$_smarty_tpl);?>
</p>
				<div style="float:right">				
					<label><img src="themes/default/img/module-profile.png" /> <?php echo smartyTranslate(array('s'=>'Welcome'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['username_addons']->value;?>
</label>
					<label>|</label>
					<label><a href="#" id="addons_logout_button"><img src="themes/default/img/module-logout.png" /> <?php echo smartyTranslate(array('s'=>'Log out from PrestaShop Addons.'),$_smarty_tpl);?>
</a></label>
				</div>
			</div>
			<!--end addons login-->
	<?php }else{ ?>
		<?php if ($_smarty_tpl->tpl_vars['check_url_fopen']->value=='ko'||$_smarty_tpl->tpl_vars['check_openssl']->value=='ko'){?>
			<div class="warn">
				<b><?php echo smartyTranslate(array('s'=>'If you want to be able to fully use the AdminModules panel and have free modules available, you should enable the following configuration on your server:'),$_smarty_tpl);?>
</b><br />
				<?php if ($_smarty_tpl->tpl_vars['check_url_fopen']->value=='ko'){?>- <?php echo smartyTranslate(array('s'=>'Enable allow_url_fopen'),$_smarty_tpl);?>
<br /><?php }?>
				<?php if ($_smarty_tpl->tpl_vars['check_openssl']->value=='ko'){?>- <?php echo smartyTranslate(array('s'=>'Enable php openSSL extension'),$_smarty_tpl);?>
<br /><?php }?>
			</div>
		<?php }else{ ?>
			<!--start addons login-->
			<div class="filter-module" id="addons_login_div">
				<p><?php echo smartyTranslate(array('s'=>'Do you have a %s account?','sprintf'=>'<a href="http://addons.prestashop.com/">PrestaShop Addons</a>'),$_smarty_tpl);?>
</p>
				<form id="addons_login_form" method="post">
					<label><?php echo smartyTranslate(array('s'=>'Addons Login'),$_smarty_tpl);?>
 :</label> <input type="text" value="" id="username_addons" autocomplete="off" class="ac_input">
					<label><?php echo smartyTranslate(array('s'=>'Password Addons'),$_smarty_tpl);?>
 :</label> <input type="password" value="" id="password_addons" autocomplete="off" class="ac_input">
					<input type="submit" class="button" id="addons_login_button" value="<?php echo smartyTranslate(array('s'=>'Log in'),$_smarty_tpl);?>
">
					<br /><span id="addons_loading" style="color:red"></span>
				</form>
			</div>
			<!--end addons login-->
		<?php }?>
	<?php }?>
	<div class="clear">&nbsp;</div>
<?php }?>

<!--start filter module-->
<style>.ac_results { border:1px solid #C2C4D9; }</style>
<div class="filter-module">
	<form id="filternameForm" method="post">
		<input type="text" value="" name="filtername" autocomplete="off" class="ac_input">
		<input type="submit" class="button" value="<?php echo smartyTranslate(array('s'=>'Search'),$_smarty_tpl);?>
">
	</form>
	<form method="post">
		<div class="select-filter">
			<label class="search-filter"><?php echo smartyTranslate(array('s'=>'Sort by'),$_smarty_tpl);?>
:</label>

				<select name="module_type" id="module_type_filter" <?php if ($_smarty_tpl->tpl_vars['showTypeModules']->value!='allModules'&&$_smarty_tpl->tpl_vars['showTypeModules']->value!=''){?>style="background-color:#49B2FF;color:white;"<?php }?>>
					<option value="allModules" <?php if ($_smarty_tpl->tpl_vars['showTypeModules']->value=='allModules'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'All Modules'),$_smarty_tpl);?>
</option>
					<option value="nativeModules" <?php if ($_smarty_tpl->tpl_vars['showTypeModules']->value=='nativeModules'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Free Modules'),$_smarty_tpl);?>
</option>
					<option value="partnerModules" <?php if ($_smarty_tpl->tpl_vars['showTypeModules']->value=='partnerModules'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Partner Modules (Free)'),$_smarty_tpl);?>
</option>
					<option value="mustHaveModules" <?php if ($_smarty_tpl->tpl_vars['showTypeModules']->value=='mustHaveModules'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Must Have'),$_smarty_tpl);?>
</option>
					<?php if (isset($_smarty_tpl->tpl_vars['logged_on_addons']->value)){?><option value="addonsModules" <?php if ($_smarty_tpl->tpl_vars['showTypeModules']->value=='addonsModules'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Modules purchased on Addons'),$_smarty_tpl);?>
</option><?php }?>
					<optgroup label="<?php echo smartyTranslate(array('s'=>'Authors'),$_smarty_tpl);?>
">
						<?php  $_smarty_tpl->tpl_vars['status'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['status']->_loop = false;
 $_smarty_tpl->tpl_vars['module_author'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list_modules_authors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['status']->key => $_smarty_tpl->tpl_vars['status']->value){
$_smarty_tpl->tpl_vars['status']->_loop = true;
 $_smarty_tpl->tpl_vars['module_author']->value = $_smarty_tpl->tpl_vars['status']->key;
?>
							<option value="authorModules[<?php echo $_smarty_tpl->tpl_vars['module_author']->value;?>
]" <?php if ($_smarty_tpl->tpl_vars['status']->value=="selected"){?>selected<?php }?>><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['module_author']->value,20,'...');?>
</option>
						<?php } ?>
					</optgroup>
					<option value="otherModules" <?php if ($_smarty_tpl->tpl_vars['showTypeModules']->value=='otherModules'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Other Modules'),$_smarty_tpl);?>
</option>
				</select>
				&nbsp;
				<select name="module_install" id="module_install_filter" <?php if ($_smarty_tpl->tpl_vars['showInstalledModules']->value!='installedUninstalled'&&$_smarty_tpl->tpl_vars['showInstalledModules']->value!=''){?>style="background-color:#49B2FF;color:white;"<?php }?>>
					<option value="installedUninstalled" <?php if ($_smarty_tpl->tpl_vars['showInstalledModules']->value=='installedUninstalled'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Installed & Not Installed'),$_smarty_tpl);?>
</option>
					<option value="installed" <?php if ($_smarty_tpl->tpl_vars['showInstalledModules']->value=='installed'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Installed Modules'),$_smarty_tpl);?>
</option>
					<option value="uninstalled" <?php if ($_smarty_tpl->tpl_vars['showInstalledModules']->value=='uninstalled'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Modules Not Installed '),$_smarty_tpl);?>
</option>
				</select>
				&nbsp;
				<select name="module_status" id="module_status_filter" <?php if ($_smarty_tpl->tpl_vars['showEnabledModules']->value!='enabledDisabled'&&$_smarty_tpl->tpl_vars['showEnabledModules']->value!=''){?>style="background-color:#49B2FF;color:white;"<?php }?>>
					<option value="enabledDisabled" <?php if ($_smarty_tpl->tpl_vars['showEnabledModules']->value=='enabledDisabled'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Enabled & Disabled'),$_smarty_tpl);?>
</option>
					<option value="enabled" <?php if ($_smarty_tpl->tpl_vars['showEnabledModules']->value=='enabled'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Enabled Modules'),$_smarty_tpl);?>
</option>
					<option value="disabled" <?php if ($_smarty_tpl->tpl_vars['showEnabledModules']->value=='disabled'){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Disabled Modules'),$_smarty_tpl);?>
</option>
				</select>
				&nbsp;
				<select name="country_module_value" id="country_module_value_filter" <?php if ($_smarty_tpl->tpl_vars['showCountryModules']->value==1){?>style="background-color:#49B2FF;color:white;"<?php }?>>
					<option value="0" ><?php echo smartyTranslate(array('s'=>'All countries'),$_smarty_tpl);?>
</option>
					<option value="1" <?php if ($_smarty_tpl->tpl_vars['showCountryModules']->value==1){?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Current country:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['nameCountryDefault']->value;?>
</option>
				</select>
		</div>
		<div class="button-filter">
			<input type="submit" value="<?php echo smartyTranslate(array('s'=>'Reset'),$_smarty_tpl);?>
" name="resetFilterModules" class="button" />
			<input type="submit" value="<?php echo smartyTranslate(array('s'=>'Filter'),$_smarty_tpl);?>
" id="filterModulesButton" name="filterModules" class="button" />
		</div>
	</form>
</div>
<!--end filter module-->
<?php }} ?>