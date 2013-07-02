<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:57
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/access/helpers/form/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:146861775651c1c0ddc03313-09927940%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e8676682027c0f91e5f697917ff757f30f360f4' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/access/helpers/form/form.tpl',
      1 => 1371647907,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '146861775651c1c0ddc03313-09927940',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'show_toolbar' => 0,
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'profiles' => 0,
    'profile' => 0,
    'current_profile' => 0,
    'current' => 0,
    'token' => 0,
    'table' => 0,
    'submit_action' => 0,
    'form_id' => 0,
    'identifier' => 0,
    'tabs' => 0,
    'tab' => 0,
    'tabsize' => 0,
    'admin_profile' => 0,
    'access_edit' => 0,
    'accesses' => 0,
    'is_child' => 0,
    'perms' => 0,
    'perm' => 0,
    'access' => 0,
    'result_accesses' => 0,
    'child' => 0,
    'modules' => 0,
    'module' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0de196b13_40965458',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0de196b13_40965458')) {function content_51c1c0de196b13_40965458($_smarty_tpl) {?>
<script type="text/javascript">
	$(document).ready(function() {

		$('div.productTabs').find('a').each(function() {
			$(this).attr('href', '#');
		});

		$('div.productTabs a').click(function() {
			var id = $(this).attr('id');
			$('.nav-profile').removeClass('selected');
			$(this).addClass('selected');
			$('.tab-profile').hide()
			$('.'+id).show();
		});

		$('.ajaxPower').change(function(){
			var tout = $(this).attr('rel').split('||');
			var id_tab = tout[0];
			var id_profile = tout[1];
			var perm = tout[2];
			var enabled = $(this).is(':checked')? 1 : 0;
			var tabsize = tout[3];
			var tabnumber = tout[4];
			var table = 'table#table_'+id_profile;

			if (perm == 'all' && $(this).parent().parent().hasClass('parent'))
			{
				checked = enabled ? 'checked': '';
				$(this).parent().parent().parent().find('.child-'+id_tab+' input[type=checkbox]').attr('checked', checked);
				$.ajax({
					url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminAccess');?>
",
					cache: false,
					data : {
						ajaxMode : '1',
						id_tab: id_tab,
						id_profile: id_profile,
						perm: perm,
						enabled: enabled,
						submitAddAccess: '1',
						addFromParent: '1',
						action: 'updateAccess',
						ajax: '1',
						token: '<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminAccess'),$_smarty_tpl);?>
'
					},
					success : function(res,textStatus,jqXHR)
					{
						try
						{
							if (res == 'ok')
								showSuccessMessage("<?php echo smartyTranslate(array('s'=>'Update successful'),$_smarty_tpl);?>
");
							else
								showErrorMessage("<?php echo smartyTranslate(array('s'=>'Update error'),$_smarty_tpl);?>
");
						}
						catch(e)
						{
							jAlert('Technical error');
						}
					}
				});
			}
			perfect_access_js_gestion(this, perm, id_tab, tabsize, tabnumber, table);

			$.ajax({
				url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminAccess');?>
",
				cache: false,
				data : {
					ajaxMode : '1',
					id_tab: id_tab,
					id_profile: id_profile,
					perm: perm,
					enabled: enabled,
					submitAddAccess: '1',
					action: 'updateAccess',
					ajax: '1',
					token: '<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminAccess'),$_smarty_tpl);?>
'
				},
				success : function(res,textStatus,jqXHR)
				{
					try
					{
						if (res == 'ok')
							showSuccessMessage("<?php echo smartyTranslate(array('s'=>'Update successful'),$_smarty_tpl);?>
");
						else
							showErrorMessage("<?php echo smartyTranslate(array('s'=>'Update error'),$_smarty_tpl);?>
");
					}
					catch(e)
					{
						jAlert('Technical error');
					}
				}
			});
		});

		$(".changeModuleAccess").change(function(){
			var tout = $(this).attr('rel').split('||');
			var id_module = tout[0];
			var perm = tout[1];
			var id_profile = tout[2];
			var enabled = $(this).is(':checked') ? 1 : 0;
			var enabled_attr = $(this).is(':checked') ? true : false;
			var table = 'table#table_module_'+id_profile;

			if (id_module == -1)
				$(table+' .ajax-ma-'+perm).each(function(key, value) {
					$(this).attr("checked", enabled_attr);
				});
			else if (!enabled)
				$(table+' #ajax-ma-'+perm+'-master').each(function(key, value) {
					$(this).attr("checked", enabled_attr);
				});

			$.ajax({
				url: "<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminAccess');?>
",
				cache: false,
				data : {
					ajaxMode: '1',
					id_module: id_module,
					perm: perm,
					enabled: enabled,
					id_profile: id_profile,
					changeModuleAccess: '1',
					action: 'updateModuleAccess',
					ajax: '1',
					token: '<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminAccess'),$_smarty_tpl);?>
'
				},
				success : function(res,textStatus,jqXHR)
				{
					try
					{
						if (res == 'ok')
							showSuccessMessage("<?php echo smartyTranslate(array('s'=>'Update successful'),$_smarty_tpl);?>
");
						else
							showErrorMessage("<?php echo smartyTranslate(array('s'=>'Update error'),$_smarty_tpl);?>
");
					}
					catch(e)
					{
						jAlert('Technical error');
					}
				}
			});
		});

	});

</script>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

	<div class="leadin"></div>
<?php }?>

<div class="productTabs">
	<ul class="tab">
	<?php  $_smarty_tpl->tpl_vars['profile'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['profile']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['profiles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['profile']->key => $_smarty_tpl->tpl_vars['profile']->value){
$_smarty_tpl->tpl_vars['profile']->_loop = true;
?>
		<li class="tab-row">
			<a class="nav-profile <?php if ($_smarty_tpl->tpl_vars['profile']->value['id_profile']==$_smarty_tpl->tpl_vars['current_profile']->value){?>selected<?php }?>" id="profile-<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
&id_profile=<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
"><?php echo $_smarty_tpl->tpl_vars['profile']->value['name'];?>
</a>
		</li>
	<?php } ?>
	</ul>
</div>

<form id="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form" class="defaultForm" action="<?php echo $_smarty_tpl->tpl_vars['current']->value;?>
&<?php echo $_smarty_tpl->tpl_vars['submit_action']->value;?>
=1&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
" method="post" enctype="multipart/form-data">
	<?php if ($_smarty_tpl->tpl_vars['form_id']->value){?>
		<input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['identifier']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['identifier']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['form_id']->value;?>
" />
	<?php }?>

	<?php $_smarty_tpl->tpl_vars['tabsize'] = new Smarty_variable(count($_smarty_tpl->tpl_vars['tabs']->value), null, 0);?>
	<?php  $_smarty_tpl->tpl_vars['tab'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tab']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tabs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tab']->key => $_smarty_tpl->tpl_vars['tab']->value){
$_smarty_tpl->tpl_vars['tab']->_loop = true;
?>
		<?php if ($_smarty_tpl->tpl_vars['tab']->value['id_tab']>$_smarty_tpl->tpl_vars['tabsize']->value){?>
			<?php $_smarty_tpl->tpl_vars['tabsize'] = new Smarty_variable($_smarty_tpl->tpl_vars['tab']->value['id_tab'], null, 0);?>
		<?php }?>
	<?php } ?>

	<?php  $_smarty_tpl->tpl_vars['profile'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['profile']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['profiles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['profile']->key => $_smarty_tpl->tpl_vars['profile']->value){
$_smarty_tpl->tpl_vars['profile']->_loop = true;
?>

		<div class="profile-<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
 tab-profile" style="display:<?php if ($_smarty_tpl->tpl_vars['profile']->value['id_profile']==$_smarty_tpl->tpl_vars['current_profile']->value){?>block<?php }else{ ?>none<?php }?>">

			<?php if ($_smarty_tpl->tpl_vars['profile']->value['id_profile']!=$_smarty_tpl->tpl_vars['admin_profile']->value){?>
				<table class="table float" cellspacing="0" style="margin-right:50px" id="table_<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
">
					<tr>
						<th class="center">
							<?php echo smartyTranslate(array('s'=>'Menus'),$_smarty_tpl);?>

						</th>
						<th class="center">
							<input type="checkbox" name="1" id="viewall"
								<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
									rel="-1||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
||view||<?php echo $_smarty_tpl->tpl_vars['tabsize']->value;?>
||<?php echo count($_smarty_tpl->tpl_vars['tabs']->value);?>
" class="ajaxPower"
								<?php }else{ ?>
									disabled="disabled"
								<?php }?> />
							<?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>

						</th>
						<th class="center">
							<input type="checkbox" name="1" id="addall"
								<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
									rel="-1||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
||add||<?php echo $_smarty_tpl->tpl_vars['tabsize']->value;?>
||<?php echo count($_smarty_tpl->tpl_vars['tabs']->value);?>
" class="ajaxPower"
								<?php }else{ ?>
									disabled="disabled"
								<?php }?> />
							<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>

						</th>
						<th class="center">
							<input type="checkbox" name="1" id="editall"
								<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
									rel="-1||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
||edit||<?php echo $_smarty_tpl->tpl_vars['tabsize']->value;?>
||<?php echo count($_smarty_tpl->tpl_vars['tabs']->value);?>
" class="ajaxPower"
								<?php }else{ ?>
									disabled="disabled"
								<?php }?> />
							<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>

						</th>
						<th class="center">
							<input type="checkbox" name="1" id="deleteall"
								<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
									rel="-1||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
||delete||<?php echo $_smarty_tpl->tpl_vars['tabsize']->value;?>
||<?php echo count($_smarty_tpl->tpl_vars['tabs']->value);?>
" class="ajaxPower"
								<?php }else{ ?>
									disabled="disabled"
								<?php }?> />
							<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>

						</th>
						<th class="center">
							<input type="checkbox" name="1" id="allall"
								<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
									rel="-1||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
||all||<?php echo $_smarty_tpl->tpl_vars['tabsize']->value;?>
||<?php echo count($_smarty_tpl->tpl_vars['tabs']->value);?>
" class="ajaxPower"
								<?php }else{ ?>
									disabled="disabled"
								<?php }?> />
							<?php echo smartyTranslate(array('s'=>'All'),$_smarty_tpl);?>

						</th>
					</tr>
					<?php if (!count($_smarty_tpl->tpl_vars['tabs']->value)){?>
						<tr>
							<td colspan="6"><?php echo smartyTranslate(array('s'=>'No menu'),$_smarty_tpl);?>
</td>
						</tr>
					<?php }else{ ?>
						<?php  $_smarty_tpl->tpl_vars['tab'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tab']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tabs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tab']->key => $_smarty_tpl->tpl_vars['tab']->value){
$_smarty_tpl->tpl_vars['tab']->_loop = true;
?>

							<?php $_smarty_tpl->tpl_vars['access'] = new Smarty_variable($_smarty_tpl->tpl_vars['accesses']->value[$_smarty_tpl->tpl_vars['profile']->value['id_profile']], null, 0);?>

							<?php if (!$_smarty_tpl->tpl_vars['tab']->value['id_parent']||$_smarty_tpl->tpl_vars['tab']->value['id_parent']==-1){?>
								<?php $_smarty_tpl->tpl_vars['is_child'] = new Smarty_variable(false, null, 0);?>
								<?php $_smarty_tpl->tpl_vars['result_accesses'] = new Smarty_variable(0, null, 0);?>
								<tr<?php if (!$_smarty_tpl->tpl_vars['is_child']->value){?> class="parent"<?php }?>>
									<td<?php if (!$_smarty_tpl->tpl_vars['is_child']->value){?> class="bold"<?php }?>><?php if ($_smarty_tpl->tpl_vars['is_child']->value){?> &raquo; <?php }?><strong><?php echo $_smarty_tpl->tpl_vars['tab']->value['name'];?>
</strong></td>
									<?php  $_smarty_tpl->tpl_vars['perm'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['perm']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['perms']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['perm']->key => $_smarty_tpl->tpl_vars['perm']->value){
$_smarty_tpl->tpl_vars['perm']->_loop = true;
?>
										<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
											<td>
												<input type="checkbox"
													id="<?php echo $_smarty_tpl->tpl_vars['perm']->value;?>
<?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['tab']->value['id_tab']]['id_tab'];?>
"
													rel="<?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['tab']->value['id_tab']]['id_tab'];?>
||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
||<?php echo $_smarty_tpl->tpl_vars['perm']->value;?>
||<?php echo $_smarty_tpl->tpl_vars['tabsize']->value;?>
||<?php echo count($_smarty_tpl->tpl_vars['tabs']->value);?>
"
													class="ajaxPower <?php echo $_smarty_tpl->tpl_vars['perm']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['tab']->value['id_tab']]['id_tab'];?>
"
													<?php if ($_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['tab']->value['id_tab']][$_smarty_tpl->tpl_vars['perm']->value]==1){?>checked="checked"<?php }?>/>
											</td>
										<?php }else{ ?>
											<td>
												<input type="checkbox"
													disabled="disabled"
													<?php if ($_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['tab']->value['id_tab']][$_smarty_tpl->tpl_vars['perm']->value]==1){?>checked="checked"<?php }?>/>
											</td>
										<?php }?>
										<?php $_smarty_tpl->tpl_vars['result_accesses'] = new Smarty_variable($_smarty_tpl->tpl_vars['result_accesses']->value+$_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['tab']->value['id_tab']][$_smarty_tpl->tpl_vars['perm']->value], null, 0);?>
									<?php } ?>
									<td>
										<input type="checkbox"
											id='all<?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['tab']->value['id_tab']]['id_tab'];?>
'
											<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
												rel="<?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['tab']->value['id_tab']]['id_tab'];?>
||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
||all||<?php echo $_smarty_tpl->tpl_vars['tabsize']->value;?>
||<?php echo count($_smarty_tpl->tpl_vars['tabs']->value);?>
"
												class="ajaxPower all <?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['tab']->value['id_tab']]['id_tab'];?>
"
											<?php }else{ ?>
												class="all <?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['tab']->value['id_tab']]['id_tab'];?>
"
												disabled="disabled"
											<?php }?>
											<?php if ($_smarty_tpl->tpl_vars['result_accesses']->value==4){?>checked="checked"<?php }?>/>
									</td>
								</tr>

								<?php  $_smarty_tpl->tpl_vars['child'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['child']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tabs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['child']->key => $_smarty_tpl->tpl_vars['child']->value){
$_smarty_tpl->tpl_vars['child']->_loop = true;
?>
									<?php if ($_smarty_tpl->tpl_vars['child']->value['id_parent']===$_smarty_tpl->tpl_vars['tab']->value['id_tab']){?>
										<?php if (isset($_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']])){?>
											<?php $_smarty_tpl->tpl_vars['is_child'] = new Smarty_variable(true, null, 0);?>
											<?php $_smarty_tpl->tpl_vars['result_accesses'] = new Smarty_variable(0, null, 0);?>
											<tr class="child-<?php echo $_smarty_tpl->tpl_vars['child']->value['id_parent'];?>
">
												<td<?php if (!$_smarty_tpl->tpl_vars['is_child']->value){?> class="bold"<?php }?>><?php if ($_smarty_tpl->tpl_vars['is_child']->value){?> &raquo; <?php }?><strong><?php echo $_smarty_tpl->tpl_vars['child']->value['name'];?>
</strong></td>
												<?php  $_smarty_tpl->tpl_vars['perm'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['perm']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['perms']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['perm']->key => $_smarty_tpl->tpl_vars['perm']->value){
$_smarty_tpl->tpl_vars['perm']->_loop = true;
?>
													<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
														<td>
															<input type="checkbox"
																id="<?php echo $_smarty_tpl->tpl_vars['perm']->value;?>
<?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']]['id_tab'];?>
"
																rel="<?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']]['id_tab'];?>
||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
||<?php echo $_smarty_tpl->tpl_vars['perm']->value;?>
||<?php echo $_smarty_tpl->tpl_vars['tabsize']->value;?>
||<?php echo count($_smarty_tpl->tpl_vars['tabs']->value);?>
"
																class="ajaxPower <?php echo $_smarty_tpl->tpl_vars['perm']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']]['id_tab'];?>
"
																<?php if ($_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']][$_smarty_tpl->tpl_vars['perm']->value]==1){?>checked="checked"<?php }?>/>
														</td>
													<?php }else{ ?>
														<td>
															<input type="checkbox"
																disabled="disabled"
																<?php if ($_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']][$_smarty_tpl->tpl_vars['perm']->value]==1){?>checked="checked"<?php }?>/>
														</td>
													<?php }?>
													<?php $_smarty_tpl->tpl_vars['result_accesses'] = new Smarty_variable($_smarty_tpl->tpl_vars['result_accesses']->value+$_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']][$_smarty_tpl->tpl_vars['perm']->value], null, 0);?>
												<?php } ?>
												<td>
													<input type="checkbox"
														id='all<?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']]['id_tab'];?>
'
														<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
															rel="<?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']]['id_tab'];?>
||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
||all||<?php echo $_smarty_tpl->tpl_vars['tabsize']->value;?>
||<?php echo count($_smarty_tpl->tpl_vars['tabs']->value);?>
"
															class="ajaxPower all <?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']]['id_tab'];?>
"
														<?php }else{ ?>
															class="all <?php echo $_smarty_tpl->tpl_vars['access']->value[$_smarty_tpl->tpl_vars['child']->value['id_tab']]['id_tab'];?>
"
															disabled="disabled"
														<?php }?>
														<?php if ($_smarty_tpl->tpl_vars['result_accesses']->value==4){?>checked="checked"<?php }?>/>
												</td>
											</tr>
										<?php }?>
									<?php }?>
								<?php } ?>

							<?php }?>

						<?php } ?>
					<?php }?>
				</table>

				<table class="table" cellspacing="0" style="margin-left:20px" id="table_module_<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
">
					<tr>
						<th><?php echo smartyTranslate(array('s'=>'Modules'),$_smarty_tpl);?>
</th>
						<th class="center">
							<input type="checkbox"
								id="ajax-ma-view-master"
								<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
									class="changeModuleAccess" rel="-1||view||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
"
								<?php }else{ ?>
									disabled="disabled"
								<?php }?> /> <?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>

						</th>
						<th class="center">
							<input type="checkbox"
								id="ajax-ma-configure-master"
								<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
									class="changeModuleAccess" rel="-1||configure||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
"
								<?php }else{ ?>
									disabled="disabled"
								<?php }?> /> <?php echo smartyTranslate(array('s'=>'Configure'),$_smarty_tpl);?>
</th>
					</tr>
					
					<?php if (!count($_smarty_tpl->tpl_vars['modules']->value)){?>
						<tr>
							<td colspan="3"><?php echo smartyTranslate(array('s'=>'No modules are installed'),$_smarty_tpl);?>
</td>
						</tr>
					<?php }else{ ?>
						<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['modules']->value[$_smarty_tpl->tpl_vars['profile']->value['id_profile']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value){
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
							<tr>
								<td>&raquo; <?php echo $_smarty_tpl->tpl_vars['module']->value['name'];?>
</td>
								<td>
									<input type="checkbox"
										value="1"
										<?php if ($_smarty_tpl->tpl_vars['module']->value['view']==true){?>checked="checked"<?php }?>
										<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
											class="ajax-ma-view changeModuleAccess"
											rel="<?php echo $_smarty_tpl->tpl_vars['module']->value['id_module'];?>
||view||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
"
										<?php }else{ ?>
											class="ajax-ma-view"
											disabled="disabled"
										<?php }?>
									/>
								</td>
								<td>
									<input type="checkbox"
										value="1"
										<?php if ($_smarty_tpl->tpl_vars['module']->value['configure']==true){?>checked="checked"<?php }?>
										<?php if ($_smarty_tpl->tpl_vars['access_edit']->value==1){?>
											class="ajax-ma-configure changeModuleAccess"
											rel="<?php echo $_smarty_tpl->tpl_vars['module']->value['id_module'];?>
||configure||<?php echo $_smarty_tpl->tpl_vars['profile']->value['id_profile'];?>
"
										<?php }else{ ?>
											class="ajax-ma-configure"
											disabled="disabled"
										<?php }?>
									/>
								</td>
							</tr>
						<?php } ?>
					<?php }?>
				</table>
				
				<div class="clear">&nbsp;</div>

			<?php }else{ ?>
				<?php echo smartyTranslate(array('s'=>'Administrator permissions cannot be modified.'),$_smarty_tpl);?>

			<?php }?>

		</div>

	<?php } ?>
</form>
<?php }} ?>