<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:57
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/toolbar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:99593625451c1c0ddac3965-93692971%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cf26179ad2e335b1573c66c097776f59412802de' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/toolbar.tpl',
      1 => 1371647305,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '99593625451c1c0ddac3965-93692971',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'toolbar_scroll' => 0,
    'toolbar_btn' => 0,
    'table' => 0,
    'btn' => 0,
    'k' => 0,
    'tab_modules_open' => 0,
    'tab_modules_list' => 0,
    'admin_module_ajax_url' => 0,
    'back_tab_modules_list' => 0,
    'title' => 0,
    'key' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ddbedeb1_33745809',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ddbedeb1_33745809')) {function content_51c1c0ddbedeb1_33745809($_smarty_tpl) {?>

<div class="toolbar-placeholder">
	<div class="toolbarBox <?php if ($_smarty_tpl->tpl_vars['toolbar_scroll']->value){?>toolbarHead<?php }?>">
		
			<ul class="cc_button">
				<?php  $_smarty_tpl->tpl_vars['btn'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['btn']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['toolbar_btn']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['btn']->key => $_smarty_tpl->tpl_vars['btn']->value){
$_smarty_tpl->tpl_vars['btn']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['btn']->key;
?>
					<li>
						<a id="desc-<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
-<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['imgclass'])){?><?php echo $_smarty_tpl->tpl_vars['btn']->value['imgclass'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
<?php }?>" class="toolbar_btn" <?php if (isset($_smarty_tpl->tpl_vars['btn']->value['href'])){?>href="<?php echo $_smarty_tpl->tpl_vars['btn']->value['href'];?>
"<?php }?> title="<?php echo $_smarty_tpl->tpl_vars['btn']->value['desc'];?>
" <?php if (isset($_smarty_tpl->tpl_vars['btn']->value['target'])&&$_smarty_tpl->tpl_vars['btn']->value['target']){?>target="_blank"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['btn']->value['js'])&&$_smarty_tpl->tpl_vars['btn']->value['js']){?>onclick="<?php echo $_smarty_tpl->tpl_vars['btn']->value['js'];?>
"<?php }?>>
							<span class="process-icon-<?php if (isset($_smarty_tpl->tpl_vars['btn']->value['imgclass'])){?><?php echo $_smarty_tpl->tpl_vars['btn']->value['imgclass'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
<?php }?> <?php if (isset($_smarty_tpl->tpl_vars['btn']->value['class'])){?><?php echo $_smarty_tpl->tpl_vars['btn']->value['class'];?>
<?php }?>" ></span>
							<div <?php if (isset($_smarty_tpl->tpl_vars['btn']->value['force_desc'])&&$_smarty_tpl->tpl_vars['btn']->value['force_desc']==true){?> class="locked" <?php }?>><?php echo $_smarty_tpl->tpl_vars['btn']->value['desc'];?>
</div>
						</a>
						<?php if ($_smarty_tpl->tpl_vars['k']->value=='modules-list'){?>
							<div id="modules_list_container" style="display:none">
							<div style="float:right;margin:5px">
								<a href="#" onclick="$('#modules_list_container').slideUp();return false;"><img alt="X" src="../img/admin/close.png"></a>
							</div>
							<div id="modules_list_loader"><img src="../img/loader.gif" alt="" border="0"></div>
							<div id="modules_list_container_tab" style="display:none;"></div>
							</div>
						<?php }?>
					</li>
				<?php } ?>
			</ul>

			<script language="javascript" type="text/javascript">
			//<![CDATA[
				var submited = false
				var modules_list_loaded = false;
				$(function() {
					//get reference on save link
					btn_save = $('span[class~="process-icon-save"]').parent();

					//get reference on form submit button
					btn_submit = $('#<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form_submit_btn');

					if (btn_save.length > 0 && btn_submit.length > 0)
					{
						//get reference on save and stay link
						btn_save_and_stay = $('span[class~="process-icon-save-and-stay"]').parent();

						//get reference on current save link label
						lbl_save = $('#desc-<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
-save div');

						//override save link label with submit button value
						if (btn_submit.val().length > 0)
							lbl_save.html(btn_submit.attr("value"));

						if (btn_save_and_stay.length > 0)
						{

							//get reference on current save link label
							lbl_save_and_stay = $('#desc-<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
-save-and-stay div');

							//override save and stay link label with submit button value
							if (btn_submit.val().length > 0 && lbl_save_and_stay && !lbl_save_and_stay.hasClass('locked'))
							{
								lbl_save_and_stay.html(btn_submit.val() + " <?php echo smartyTranslate(array('s'=>'and stay'),$_smarty_tpl);?>
 ");
							}

						}

						//hide standard submit button
						btn_submit.hide();
						//bind enter key press to validate form
						$('#<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form').keypress(function (e) {
							if (e.which == 13 && e.target.localName != 'textarea')
								$('#desc-<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
-save').click();
						});
						//submit the form
						
							btn_save.click(function() {
								// Avoid double click
								if (submited)
									return false;
								submited = true;
								
								//add hidden input to emulate submit button click when posting the form -> field name posted
								btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'" value="1" />');

								$('#<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form').submit();
								return false;
							});

							if (btn_save_and_stay)
							{
								btn_save_and_stay.click(function() {
									//add hidden input to emulate submit button click when posting the form -> field name posted
									btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'AndStay" value="1" />');

									$('#<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form').submit();
									return false;
								});
							}
						
					}
					<?php if (isset($_smarty_tpl->tpl_vars['tab_modules_open']->value)){?>
						if (<?php echo $_smarty_tpl->tpl_vars['tab_modules_open']->value;?>
)
							openModulesList();
					<?php }?>
				});
				<?php if (isset($_smarty_tpl->tpl_vars['tab_modules_list']->value)){?>
				$('.process-icon-modules-list').parent('a').unbind().bind('click', function (){
					openModulesList();
				});
				
				function openModulesList()
				{
					$('#modules_list_container').slideDown();
					if (!modules_list_loaded)
					{
						$.ajax({
							type: "POST",
							url : '<?php echo $_smarty_tpl->tpl_vars['admin_module_ajax_url']->value;?>
',
							async: true,
							data : {
								ajax : "1",
								controller : "AdminModules",
								action : "getTabModulesList",
								tab_modules_list : '<?php echo $_smarty_tpl->tpl_vars['tab_modules_list']->value;?>
',
								back_tab_modules_list : '<?php echo $_smarty_tpl->tpl_vars['back_tab_modules_list']->value;?>
'
							},
							success : function(data)
							{
								$('#modules_list_container_tab').html(data).slideDown();
								$('#modules_list_loader').hide();
								modules_list_loaded = true;
							}
						});
					}
					else
					{
						$('#modules_list_container_tab').slideDown();
						$('#modules_list_loader').hide();
					}
					return false;
				}
				<?php }?>
			//]]>
			</script>
		
		<div class="pageTitle">
			<h3>
				<span id="current_obj" style="font-weight: normal;">
					<?php if ($_smarty_tpl->tpl_vars['title']->value){?>
						<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['title']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['item']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['item']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
 $_smarty_tpl->tpl_vars['item']->iteration++;
 $_smarty_tpl->tpl_vars['item']->last = $_smarty_tpl->tpl_vars['item']->iteration === $_smarty_tpl->tpl_vars['item']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['title']['last'] = $_smarty_tpl->tpl_vars['item']->last;
?>
							
							<span class="breadcrumb item-<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
 "><?php echo preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['item']->value);?>

								<?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['title']['last']){?>
									<img alt="&gt;" style="margin-right:5px" src="../img/admin/separator_breadcrumb.png" />
								<?php }?>
							</span>
						<?php } ?>
					<?php }else{ ?>
						&nbsp;
					<?php }?>
				</span>
				
			</h3>
		</div>
	</div>
</div>
<?php }} ?>