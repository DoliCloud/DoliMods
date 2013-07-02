<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:07
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/helpers/list/list_header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12381165951c1c0e7163481-58888734%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9ac5d1ebde2921f946828db2ab6a094d2d9cff7b' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/helpers/list/list_header.tpl',
      1 => 1371647816,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12381165951c1c0e7163481-58888734',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'simple_header' => 0,
    'table' => 0,
    'is_order_position' => 0,
    'token' => 0,
    'order_way' => 0,
    'show_toolbar' => 0,
    'toolbar_btn' => 0,
    'toolbar_scroll' => 0,
    'title' => 0,
    'name_controller' => 0,
    'hookName' => 0,
    'action' => 0,
    'page' => 0,
    'total_pages' => 0,
    'pagination' => 0,
    'value' => 0,
    'selected_pagination' => 0,
    'list_total' => 0,
    'table_id' => 0,
    'table_dnd' => 0,
    'fields_display' => 0,
    'params' => 0,
    'shop_link_type' => 0,
    'has_actions' => 0,
    'has_bulk_actions' => 0,
    'currentIndex' => 0,
    'key' => 0,
    'identifier' => 0,
    'order_by' => 0,
    'row_hover' => 0,
    'option_value' => 0,
    'option_display' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e7645dd7_10539666',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e7645dd7_10539666')) {function content_51c1c0e7645dd7_10539666($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php if (!$_smarty_tpl->tpl_vars['simple_header']->value){?>

	<script type="text/javascript">
		$(document).ready(function() {
			$('table.<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
 .filter').keypress(function(event){
				formSubmit(event, 'submitFilterButton<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
')
			})
		});
	</script>
	
	<?php if ($_smarty_tpl->tpl_vars['is_order_position']->value){?>
		<script type="text/javascript" src="../js/jquery/plugins/jquery.tablednd.js"></script>
		<script type="text/javascript">
			var token = '<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
';
			var come_from = '<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
';
			var alternate = <?php if ($_smarty_tpl->tpl_vars['order_way']->value=='DESC'){?>'1'<?php }else{ ?>'0'<?php }?>;
		</script>
		<script type="text/javascript" src="../js/admin-dnd.js"></script>
	<?php }?>

	<script type="text/javascript">
		$(function() {
			if ($("table.<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
 .datepicker").length > 0)
				$("table.<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
 .datepicker").datepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd'
				});
		});
	</script>


<?php }?>

<?php if ($_smarty_tpl->tpl_vars['show_toolbar']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ("toolbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('toolbar_btn'=>$_smarty_tpl->tpl_vars['toolbar_btn']->value,'toolbar_scroll'=>$_smarty_tpl->tpl_vars['toolbar_scroll']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value), 0);?>

<?php }?>

<?php if (!$_smarty_tpl->tpl_vars['simple_header']->value){?>
	<div class="leadin"></div>
<?php }?>




<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayAdminListBefore'),$_smarty_tpl);?>

<?php if (isset($_smarty_tpl->tpl_vars['name_controller']->value)){?>
	<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo ucfirst($_smarty_tpl->tpl_vars['name_controller']->value);?>
ListBefore<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

<?php }elseif(isset($_GET['controller'])){?>
	<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo htmlentities(ucfirst($_GET['controller']));?>
ListBefore<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

<?php }?>


<?php if (!$_smarty_tpl->tpl_vars['simple_header']->value){?>
<form method="post" action="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" class="form">
	<input type="hidden" id="submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" name="submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" value="0"/>
<?php }?>
	<table class="table_grid" name="list_table">
		<?php if (!$_smarty_tpl->tpl_vars['simple_header']->value){?>
			<tr>
				<td style="vertical-align: bottom;">
					<span style="float: left;">
						<?php if ($_smarty_tpl->tpl_vars['page']->value>1){?>
							<input type="image" src="../img/admin/list-prev2.gif" onclick="getE('submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').value=1"/>&nbsp;
							<input type="image" src="../img/admin/list-prev.gif" onclick="getE('submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').value=<?php echo $_smarty_tpl->tpl_vars['page']->value-1;?>
"/>
						<?php }?>
						<?php echo smartyTranslate(array('s'=>'Page'),$_smarty_tpl);?>
 <b><?php echo $_smarty_tpl->tpl_vars['page']->value;?>
</b> / <?php echo $_smarty_tpl->tpl_vars['total_pages']->value;?>

						<?php if ($_smarty_tpl->tpl_vars['page']->value<$_smarty_tpl->tpl_vars['total_pages']->value){?>
							<input type="image" src="../img/admin/list-next.gif" onclick="getE('submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').value=<?php echo $_smarty_tpl->tpl_vars['page']->value+1;?>
"/>&nbsp;
							<input type="image" src="../img/admin/list-next2.gif" onclick="getE('submitFilter<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').value=<?php echo $_smarty_tpl->tpl_vars['total_pages']->value;?>
"/>
						<?php }?>
						| <?php echo smartyTranslate(array('s'=>'Display'),$_smarty_tpl);?>

						<select name="pagination" onchange="submit()">
							
							<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['pagination']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
								<option value="<?php echo intval($_smarty_tpl->tpl_vars['value']->value);?>
"<?php if ($_smarty_tpl->tpl_vars['selected_pagination']->value==$_smarty_tpl->tpl_vars['value']->value){?> selected="selected" <?php }elseif($_smarty_tpl->tpl_vars['selected_pagination']->value==null&&$_smarty_tpl->tpl_vars['value']->value==$_smarty_tpl->tpl_vars['pagination']->value[1]){?> selected="selected2"<?php }?>><?php echo intval($_smarty_tpl->tpl_vars['value']->value);?>
</option>
							<?php } ?>
						</select>
						/ <?php echo $_smarty_tpl->tpl_vars['list_total']->value;?>
 <?php echo smartyTranslate(array('s'=>'result(s)'),$_smarty_tpl);?>

					</span>
					<span style="float: right;">
						<input type="submit" name="submitReset<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" value="<?php echo smartyTranslate(array('s'=>'Reset'),$_smarty_tpl);?>
" class="button" />
						<input type="submit" id="submitFilterButton<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" name="submitFilter" value="<?php echo smartyTranslate(array('s'=>'Filter'),$_smarty_tpl);?>
" class="button" />
					</span>
					<span class="clear"></span>
				</td>
			</tr>
		<?php }?>
		<tr>
			<td<?php if ($_smarty_tpl->tpl_vars['simple_header']->value){?> style="border:none;"<?php }?>>
				<table
				<?php if ($_smarty_tpl->tpl_vars['table_id']->value){?> id=<?php echo $_smarty_tpl->tpl_vars['table_id']->value;?>
<?php }?>
				class="table <?php if ($_smarty_tpl->tpl_vars['table_dnd']->value){?>tableDnD<?php }?> <?php echo $_smarty_tpl->tpl_vars['table']->value;?>
"
				cellpadding="0" cellspacing="0"
				style="width: 100%; margin-bottom:10px;">
					<col width="10px" />
					<?php  $_smarty_tpl->tpl_vars['params'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['params']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['fields_display']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['params']->key => $_smarty_tpl->tpl_vars['params']->value){
$_smarty_tpl->tpl_vars['params']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['params']->key;
?>
						<col <?php if (isset($_smarty_tpl->tpl_vars['params']->value['width'])&&$_smarty_tpl->tpl_vars['params']->value['width']!='auto'){?>width="<?php echo $_smarty_tpl->tpl_vars['params']->value['width'];?>
px"<?php }?>/>
					<?php } ?>
					<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value){?>
						<col width="80px" />
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['has_actions']->value){?>
						<col width="52px" />
					<?php }?>
					<thead>
						<tr class="nodrag nodrop" style="height: 40px">
							<th class="center">
								<?php if ($_smarty_tpl->tpl_vars['has_bulk_actions']->value){?>
									<input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, '<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
Box[]', this.checked)" />
								<?php }?>
							</th>
							<?php  $_smarty_tpl->tpl_vars['params'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['params']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['fields_display']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['params']->key => $_smarty_tpl->tpl_vars['params']->value){
$_smarty_tpl->tpl_vars['params']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['params']->key;
?>
								<th <?php if (isset($_smarty_tpl->tpl_vars['params']->value['align'])){?> class="<?php echo $_smarty_tpl->tpl_vars['params']->value['align'];?>
"<?php }?>>
									<?php if (isset($_smarty_tpl->tpl_vars['params']->value['hint'])){?><span class="hint" name="help_box"><?php echo $_smarty_tpl->tpl_vars['params']->value['hint'];?>
<span class="hint-pointer">&nbsp;</span></span><?php }?>
									<span class="title_box">
										<?php echo $_smarty_tpl->tpl_vars['params']->value['title'];?>

									</span>
									<?php if ((!isset($_smarty_tpl->tpl_vars['params']->value['orderby'])||$_smarty_tpl->tpl_vars['params']->value['orderby'])&&!$_smarty_tpl->tpl_vars['simple_header']->value){?>
										<br />
										<a href="<?php echo $_smarty_tpl->tpl_vars['currentIndex']->value;?>
&<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
Orderby=<?php echo urlencode($_smarty_tpl->tpl_vars['key']->value);?>
&<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
Orderway=desc&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
<?php if (isset($_GET[$_smarty_tpl->tpl_vars['identifier']->value])){?>&<?php echo $_smarty_tpl->tpl_vars['identifier']->value;?>
=<?php echo intval($_GET[$_smarty_tpl->tpl_vars['identifier']->value]);?>
<?php }?>">
										<img border="0" src="../img/admin/down<?php if (isset($_smarty_tpl->tpl_vars['order_by']->value)&&($_smarty_tpl->tpl_vars['key']->value==$_smarty_tpl->tpl_vars['order_by']->value)&&($_smarty_tpl->tpl_vars['order_way']->value=='DESC')){?>_d<?php }?>.gif" /></a>
										<a href="<?php echo $_smarty_tpl->tpl_vars['currentIndex']->value;?>
&<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
Orderby=<?php echo urlencode($_smarty_tpl->tpl_vars['key']->value);?>
&<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
Orderway=asc&token=<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
<?php if (isset($_GET[$_smarty_tpl->tpl_vars['identifier']->value])){?>&<?php echo $_smarty_tpl->tpl_vars['identifier']->value;?>
=<?php echo intval($_GET[$_smarty_tpl->tpl_vars['identifier']->value]);?>
<?php }?>">
										<img border="0" src="../img/admin/up<?php if (isset($_smarty_tpl->tpl_vars['order_by']->value)&&($_smarty_tpl->tpl_vars['key']->value==$_smarty_tpl->tpl_vars['order_by']->value)&&($_smarty_tpl->tpl_vars['order_way']->value=='ASC')){?>_d<?php }?>.gif" /></a>
									<?php }elseif(!$_smarty_tpl->tpl_vars['simple_header']->value){?>
										<br />&nbsp;
									<?php }?>
								</th>
							<?php } ?>
							<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value){?>
								<th>
									<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value=='shop'){?>
										<?php echo smartyTranslate(array('s'=>'Shop'),$_smarty_tpl);?>

									<?php }else{ ?>
										<?php echo smartyTranslate(array('s'=>'Group shop'),$_smarty_tpl);?>

									<?php }?>
									<br />&nbsp;
								</th>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['has_actions']->value){?>
								<th class="center"><?php echo smartyTranslate(array('s'=>'Actions'),$_smarty_tpl);?>
<?php if (!$_smarty_tpl->tpl_vars['simple_header']->value){?><br />&nbsp;<?php }?></th>
							<?php }?>
						</tr>
 						<?php if (!$_smarty_tpl->tpl_vars['simple_header']->value){?>
						<tr class="nodrag nodrop filter <?php if ($_smarty_tpl->tpl_vars['row_hover']->value){?>row_hover<?php }?>" style="height: 35px;">
							<td class="center">
								<?php if ($_smarty_tpl->tpl_vars['has_bulk_actions']->value){?>
									--
								<?php }?>
							</td>

							
							<?php  $_smarty_tpl->tpl_vars['params'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['params']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['fields_display']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['params']->key => $_smarty_tpl->tpl_vars['params']->value){
$_smarty_tpl->tpl_vars['params']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['params']->key;
?>
								<td <?php if (isset($_smarty_tpl->tpl_vars['params']->value['align'])){?> class="<?php echo $_smarty_tpl->tpl_vars['params']->value['align'];?>
" <?php }?>>
									<?php if (isset($_smarty_tpl->tpl_vars['params']->value['search'])&&!$_smarty_tpl->tpl_vars['params']->value['search']){?>
										--
									<?php }else{ ?>
										<?php if ($_smarty_tpl->tpl_vars['params']->value['type']=='bool'){?>
											<select onchange="$('#submitFilterButton<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').focus();$('#submitFilterButton<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').click();" name="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
Filter_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
">
												<option value="">--</option>
												<option value="1" <?php if ($_smarty_tpl->tpl_vars['params']->value['value']==1){?> selected="selected" <?php }?>><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</option>
												<option value="0" <?php if ($_smarty_tpl->tpl_vars['params']->value['value']==0&&$_smarty_tpl->tpl_vars['params']->value['value']!=''){?> selected="selected" <?php }?>><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</option>
											</select>
										<?php }elseif($_smarty_tpl->tpl_vars['params']->value['type']=='date'||$_smarty_tpl->tpl_vars['params']->value['type']=='datetime'){?>
											<?php echo smartyTranslate(array('s'=>'From'),$_smarty_tpl);?>
 <input type="text" class="filter datepicker" id="<?php echo $_smarty_tpl->tpl_vars['params']->value['id_date'];?>
_0" name="<?php echo $_smarty_tpl->tpl_vars['params']->value['name_date'];?>
[0]" value="<?php if (isset($_smarty_tpl->tpl_vars['params']->value['value'][0])){?><?php echo $_smarty_tpl->tpl_vars['params']->value['value'][0];?>
<?php }?>"<?php if (isset($_smarty_tpl->tpl_vars['params']->value['width'])){?> style="width:70px"<?php }?>/><br />
											<?php echo smartyTranslate(array('s'=>'To'),$_smarty_tpl);?>
 <input type="text" class="filter datepicker" id="<?php echo $_smarty_tpl->tpl_vars['params']->value['id_date'];?>
_1" name="<?php echo $_smarty_tpl->tpl_vars['params']->value['name_date'];?>
[1]" value="<?php if (isset($_smarty_tpl->tpl_vars['params']->value['value'][1])){?><?php echo $_smarty_tpl->tpl_vars['params']->value['value'][1];?>
<?php }?>"<?php if (isset($_smarty_tpl->tpl_vars['params']->value['width'])){?> style="width:70px"<?php }?>/>
										<?php }elseif($_smarty_tpl->tpl_vars['params']->value['type']=='select'){?>
											<?php if (isset($_smarty_tpl->tpl_vars['params']->value['filter_key'])){?>
												<select onchange="$('#submitFilterButton<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').focus();$('#submitFilterButton<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
').click();" name="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
Filter_<?php echo $_smarty_tpl->tpl_vars['params']->value['filter_key'];?>
" <?php if (isset($_smarty_tpl->tpl_vars['params']->value['width'])){?> style="width:<?php echo $_smarty_tpl->tpl_vars['params']->value['width'];?>
px"<?php }?>>
													<option value="" <?php if ($_smarty_tpl->tpl_vars['params']->value['value']==''){?> selected="selected" <?php }?>>--</option>
													<?php if (isset($_smarty_tpl->tpl_vars['params']->value['list'])&&is_array($_smarty_tpl->tpl_vars['params']->value['list'])){?>
														<?php  $_smarty_tpl->tpl_vars['option_display'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option_display']->_loop = false;
 $_smarty_tpl->tpl_vars['option_value'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['params']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option_display']->key => $_smarty_tpl->tpl_vars['option_display']->value){
$_smarty_tpl->tpl_vars['option_display']->_loop = true;
 $_smarty_tpl->tpl_vars['option_value']->value = $_smarty_tpl->tpl_vars['option_display']->key;
?>
															<option value="<?php echo $_smarty_tpl->tpl_vars['option_value']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['option_display']->value==$_smarty_tpl->tpl_vars['params']->value['value']||$_smarty_tpl->tpl_vars['option_value']->value==$_smarty_tpl->tpl_vars['params']->value['value']){?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['option_display']->value;?>
</option>
														<?php } ?>
													<?php }?>
												</select>
											<?php }?>
										<?php }else{ ?>
											<input type="text" class="filter" name="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
Filter_<?php if (isset($_smarty_tpl->tpl_vars['params']->value['filter_key'])){?><?php echo $_smarty_tpl->tpl_vars['params']->value['filter_key'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
<?php }?>" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['params']->value['value'], 'htmlall', 'UTF-8');?>
" <?php if (isset($_smarty_tpl->tpl_vars['params']->value['width'])&&$_smarty_tpl->tpl_vars['params']->value['width']!='auto'){?> style="width:<?php echo $_smarty_tpl->tpl_vars['params']->value['width'];?>
px"<?php }else{ ?>style="width:95%"<?php }?> />
										<?php }?>
									<?php }?>
								</td>
							<?php } ?>

							<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value){?>
								<td>--</td>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['has_actions']->value){?>
								<td class="center">--</td>
							<?php }?>
							</tr>
						<?php }?>
						</thead>
<?php }} ?>