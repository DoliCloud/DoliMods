<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:14
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blocklayered/blocklayered.tpl" */ ?>
<?php /*%%SmartyHeaderCode:148905927051c1c0eea177c8-83436916%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f71673dc1ea3d6aa2066b9cc8f4e247d84a38311' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blocklayered/blocklayered.tpl',
      1 => 1371647522,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '148905927051c1c0eea177c8-83436916',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'nbr_filterBlocks' => 0,
    'current_friendly_url' => 0,
    'param_product_url' => 0,
    'selected_filters' => 0,
    'n_filters' => 0,
    'filter_values' => 0,
    'filters' => 0,
    'filter' => 0,
    'filter_type' => 0,
    'id_value' => 0,
    'filter_key' => 0,
    'filter_value' => 0,
    'value' => 0,
    'hide_0_values' => 0,
    'layered_show_qties' => 0,
    'values' => 0,
    'id_category_layered' => 0,
    'img_ps_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ef33fa93_34060985',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ef33fa93_34060985')) {function content_51c1c0ef33fa93_34060985($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<!-- Block layered navigation module -->
<?php if ($_smarty_tpl->tpl_vars['nbr_filterBlocks']->value!=0){?>
<script type="text/javascript">
current_friendly_url = '#<?php echo $_smarty_tpl->tpl_vars['current_friendly_url']->value;?>
';
<?php if (version_compare(@constant('_PS_VERSION_'),'1.5','>')){?>
param_product_url = '#<?php echo $_smarty_tpl->tpl_vars['param_product_url']->value;?>
';
<?php }else{ ?>
param_product_url = '';
<?php }?>
</script>
<div id="layered_block_left" class="block">
	<p class="title_block"><?php echo smartyTranslate(array('s'=>'Catalog','mod'=>'blocklayered'),$_smarty_tpl);?>
</p>
	<div class="block_content">
		<form action="#" id="layered_form">
			<div>
				<?php if (isset($_smarty_tpl->tpl_vars['selected_filters']->value)&&$_smarty_tpl->tpl_vars['n_filters']->value>0){?>
				<div id="enabled_filters">
					<span class="layered_subtitle" style="float: none;"><?php echo smartyTranslate(array('s'=>'Enabled filters:','mod'=>'blocklayered'),$_smarty_tpl);?>
</span>
					<ul>
					<?php  $_smarty_tpl->tpl_vars['filter_values'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['filter_values']->_loop = false;
 $_smarty_tpl->tpl_vars['filter_type'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['selected_filters']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['filter_values']->key => $_smarty_tpl->tpl_vars['filter_values']->value){
$_smarty_tpl->tpl_vars['filter_values']->_loop = true;
 $_smarty_tpl->tpl_vars['filter_type']->value = $_smarty_tpl->tpl_vars['filter_values']->key;
?>
						<?php  $_smarty_tpl->tpl_vars['filter_value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['filter_value']->_loop = false;
 $_smarty_tpl->tpl_vars['filter_key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['filter_values']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['filter_value']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['filter_value']->key => $_smarty_tpl->tpl_vars['filter_value']->value){
$_smarty_tpl->tpl_vars['filter_value']->_loop = true;
 $_smarty_tpl->tpl_vars['filter_key']->value = $_smarty_tpl->tpl_vars['filter_value']->key;
 $_smarty_tpl->tpl_vars['filter_value']->index++;
 $_smarty_tpl->tpl_vars['filter_value']->first = $_smarty_tpl->tpl_vars['filter_value']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['f_values']['first'] = $_smarty_tpl->tpl_vars['filter_value']->first;
?>
							<?php  $_smarty_tpl->tpl_vars['filter'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['filter']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['filters']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['filter']->key => $_smarty_tpl->tpl_vars['filter']->value){
$_smarty_tpl->tpl_vars['filter']->_loop = true;
?>
								<?php if ($_smarty_tpl->tpl_vars['filter']->value['type']==$_smarty_tpl->tpl_vars['filter_type']->value&&isset($_smarty_tpl->tpl_vars['filter']->value['values'])){?>
									<?php if (isset($_smarty_tpl->tpl_vars['filter']->value['slider'])){?>
										<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['f_values']['first']){?>
											<li>
												<a href="#" rel="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_slider" title="<?php echo smartyTranslate(array('s'=>'Cancel','mod'=>'blocklayered'),$_smarty_tpl);?>
">x</a>
												<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['filter']->value['name'], 'html', 'UTF-8');?>
<?php echo smartyTranslate(array('s'=>':','mod'=>'blocklayered'),$_smarty_tpl);?>

												<?php if ($_smarty_tpl->tpl_vars['filter']->value['format']){?>
													<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['filter']->value['values'][0]),$_smarty_tpl);?>
 - 
													<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['filter']->value['values'][1]),$_smarty_tpl);?>

												<?php }else{ ?>
													<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['filter']->value['values'][0], 'html', 'UTF-8');?>
<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['filter']->value['unit'], 'html', 'UTF-8');?>
 - 
													<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['filter']->value['values'][1], 'html', 'UTF-8');?>
<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['filter']->value['unit'], 'html', 'UTF-8');?>

												<?php }?>
											</li>
										<?php }?>
									<?php }else{ ?>
										<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['id_value'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['filter']->value['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['id_value']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
											<?php if ($_smarty_tpl->tpl_vars['id_value']->value==$_smarty_tpl->tpl_vars['filter_key']->value&&!is_numeric($_smarty_tpl->tpl_vars['filter_value']->value)&&($_smarty_tpl->tpl_vars['filter']->value['type']=='id_attribute_group'||$_smarty_tpl->tpl_vars['filter']->value['type']=='id_feature')||$_smarty_tpl->tpl_vars['id_value']->value==$_smarty_tpl->tpl_vars['filter_value']->value&&$_smarty_tpl->tpl_vars['filter']->value['type']!='id_attribute_group'&&$_smarty_tpl->tpl_vars['filter']->value['type']!='id_feature'){?>
												<li>
													<a href="#" rel="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" title="<?php echo smartyTranslate(array('s'=>'Cancel','mod'=>'blocklayered'),$_smarty_tpl);?>
">x</a>
													<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['filter']->value['name'], 'html', 'UTF-8');?>
<?php echo smartyTranslate(array('s'=>':','mod'=>'blocklayered'),$_smarty_tpl);?>
 <?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['value']->value['name'], 'html', 'UTF-8');?>

												</li>
											<?php }?>
										<?php } ?>
									<?php }?>
								<?php }?>
							<?php } ?>
						<?php } ?>
					<?php } ?>
					</ul>
				</div>
				<?php }?>
				<?php  $_smarty_tpl->tpl_vars['filter'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['filter']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['filters']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['filter']->key => $_smarty_tpl->tpl_vars['filter']->value){
$_smarty_tpl->tpl_vars['filter']->_loop = true;
?>
					<?php if (isset($_smarty_tpl->tpl_vars['filter']->value['values'])){?>
						<?php if (isset($_smarty_tpl->tpl_vars['filter']->value['slider'])){?>
						<div class="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
" style="display: none;">
						<?php }else{ ?>
						<div>
						<?php }?>
						<span class="layered_subtitle"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['filter']->value['name'], 'html', 'UTF-8');?>
</span>
						<span class="layered_close"><a href="#" rel="ul_layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
">v</a></span>
						<div class="clear"></div>
						<ul id="ul_layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
">
						<?php if (!isset($_smarty_tpl->tpl_vars['filter']->value['slider'])){?>
							<?php if ($_smarty_tpl->tpl_vars['filter']->value['filter_type']==0){?>
								<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['id_value'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['filter']->value['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['fe']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['id_value']->value = $_smarty_tpl->tpl_vars['value']->key;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['fe']['index']++;
?>
									<?php if ($_smarty_tpl->tpl_vars['value']->value['nbr']||!$_smarty_tpl->tpl_vars['hide_0_values']->value){?>
									<li class="nomargin <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['fe']['index']>=$_smarty_tpl->tpl_vars['filter']->value['filter_show_limit']){?>hiddable<?php }?>">
										<?php if (isset($_smarty_tpl->tpl_vars['filter']->value['is_color_group'])&&$_smarty_tpl->tpl_vars['filter']->value['is_color_group']){?>
											<input class="color-option <?php if (isset($_smarty_tpl->tpl_vars['value']->value['checked'])&&$_smarty_tpl->tpl_vars['value']->value['checked']){?>on<?php }?> <?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?>disable<?php }?>" type="button" name="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" rel="<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
" id="layered_id_attribute_group_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" <?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?>disabled="disabled"<?php }?> style="background: <?php if (isset($_smarty_tpl->tpl_vars['value']->value['color'])){?><?php if (file_exists((@constant('_PS_ROOT_DIR_')).("/img/co/".((string)$_smarty_tpl->tpl_vars['id_value']->value).".jpg"))){?>url(img/co/<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
.jpg)<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['value']->value['color'];?>
<?php }?><?php }else{ ?>#CCC<?php }?>;" />
											<?php if (isset($_smarty_tpl->tpl_vars['value']->value['checked'])&&$_smarty_tpl->tpl_vars['value']->value['checked']){?><input type="hidden" name="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" /><?php }?>
										<?php }else{ ?>
											<input type="checkbox" class="checkbox" name="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" id="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
<?php if ($_smarty_tpl->tpl_vars['id_value']->value||$_smarty_tpl->tpl_vars['filter']->value['type']=='quantity'){?>_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
<?php }?>" value="<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
<?php if ($_smarty_tpl->tpl_vars['filter']->value['id_key']){?>_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
<?php }?>"<?php if (isset($_smarty_tpl->tpl_vars['value']->value['checked'])){?> checked="checked"<?php }?><?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?> disabled="disabled"<?php }?> /> 
										<?php }?>
										<label for="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
"<?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?> class="disabled"<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['filter']->value['is_color_group'])&&$_smarty_tpl->tpl_vars['filter']->value['is_color_group']){?> name="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" class="layered_color" rel="<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
"<?php }?><?php }?>>
											<?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?>
											<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['value']->value['name'], 'html', 'UTF-8');?>
<?php if ($_smarty_tpl->tpl_vars['layered_show_qties']->value){?><span> (<?php echo $_smarty_tpl->tpl_vars['value']->value['nbr'];?>
)</span><?php }?>
											<?php }else{ ?>
											<a href="<?php echo $_smarty_tpl->tpl_vars['value']->value['link'];?>
" rel="<?php echo $_smarty_tpl->tpl_vars['value']->value['rel'];?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['value']->value['name'], 'html', 'UTF-8');?>
<?php if ($_smarty_tpl->tpl_vars['layered_show_qties']->value){?><span> (<?php echo $_smarty_tpl->tpl_vars['value']->value['nbr'];?>
)</span><?php }?></a>
											<?php }?>
										</label>
									</li>
									<?php }?>
								<?php } ?>
							<?php }else{ ?>
								<?php if ($_smarty_tpl->tpl_vars['filter']->value['filter_type']==1){?>
								<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['id_value'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['filter']->value['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['fe']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['id_value']->value = $_smarty_tpl->tpl_vars['value']->key;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['fe']['index']++;
?>
									<?php if ($_smarty_tpl->tpl_vars['value']->value['nbr']||!$_smarty_tpl->tpl_vars['hide_0_values']->value){?>
									<li class="nomargin <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['fe']['index']>=$_smarty_tpl->tpl_vars['filter']->value['filter_show_limit']){?>hiddable<?php }?>">
										<?php if (isset($_smarty_tpl->tpl_vars['filter']->value['is_color_group'])&&$_smarty_tpl->tpl_vars['filter']->value['is_color_group']){?>
											<input class="radio color-option <?php if (isset($_smarty_tpl->tpl_vars['value']->value['checked'])&&$_smarty_tpl->tpl_vars['value']->value['checked']){?>on<?php }?> <?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?>disable<?php }?>" type="button" name="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" rel="<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
" id="layered_id_attribute_group_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" <?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?>disabled="disabled"<?php }?> style="background: <?php if (isset($_smarty_tpl->tpl_vars['value']->value['color'])){?><?php if (file_exists((@constant('_PS_ROOT_DIR_')).("/img/co/".((string)$_smarty_tpl->tpl_vars['id_value']->value).".jpg"))){?>url(img/co/<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
.jpg)<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['value']->value['color'];?>
<?php }?><?php }else{ ?>#CCC<?php }?>;"/>
											<?php if (isset($_smarty_tpl->tpl_vars['value']->value['checked'])&&$_smarty_tpl->tpl_vars['value']->value['checked']){?><input type="hidden" name="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" /><?php }?>
										<?php }else{ ?>
											<input type="radio" class="radio layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" name="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
<?php if ($_smarty_tpl->tpl_vars['filter']->value['id_key']){?>_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
<?php }else{ ?>_1<?php }?>" id="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
<?php if ($_smarty_tpl->tpl_vars['id_value']->value||$_smarty_tpl->tpl_vars['filter']->value['type']=='quantity'){?>_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
<?php if ($_smarty_tpl->tpl_vars['filter']->value['id_key']){?>_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
<?php }?><?php }?>" value="<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
<?php if ($_smarty_tpl->tpl_vars['filter']->value['id_key']){?>_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
<?php }?>"<?php if (isset($_smarty_tpl->tpl_vars['value']->value['checked'])){?> checked="checked"<?php }?><?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?> disabled="disabled"<?php }?> /> 
										<?php }?>
										<label for="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
"<?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?> class="disabled"<?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['filter']->value['is_color_group'])&&$_smarty_tpl->tpl_vars['filter']->value['is_color_group']){?> name="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" class="layered_color" rel="<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
"<?php }?><?php }?>>
											<?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?>
												<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['value']->value['name'], 'html', 'UTF-8');?>
<?php if ($_smarty_tpl->tpl_vars['layered_show_qties']->value){?><span> (<?php echo $_smarty_tpl->tpl_vars['value']->value['nbr'];?>
)</span><?php }?></a>
											<?php }else{ ?>
												<a href="<?php echo $_smarty_tpl->tpl_vars['value']->value['link'];?>
" rel="<?php echo $_smarty_tpl->tpl_vars['value']->value['rel'];?>
"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['value']->value['name'], 'html', 'UTF-8');?>
<?php if ($_smarty_tpl->tpl_vars['layered_show_qties']->value){?><span> (<?php echo $_smarty_tpl->tpl_vars['value']->value['nbr'];?>
)</span><?php }?></a>
											<?php }?>
										</label>
									</li>
									<?php }?>
								<?php } ?>
								<?php }else{ ?>
									<select class="select" <?php if ($_smarty_tpl->tpl_vars['filter']->value['filter_show_limit']>1){?>multiple="multiple" size="<?php echo $_smarty_tpl->tpl_vars['filter']->value['filter_show_limit'];?>
"<?php }?>>
										<option value=""><?php echo smartyTranslate(array('s'=>'No filters','mod'=>'blocklayered'),$_smarty_tpl);?>
</option>
										<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['id_value'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['filter']->value['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['id_value']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
										<?php if ($_smarty_tpl->tpl_vars['value']->value['nbr']||!$_smarty_tpl->tpl_vars['hide_0_values']->value){?>
											<option style="color: <?php if (isset($_smarty_tpl->tpl_vars['value']->value['color'])){?><?php echo $_smarty_tpl->tpl_vars['value']->value['color'];?>
<?php }?>" id="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type_lite'];?>
<?php if ($_smarty_tpl->tpl_vars['id_value']->value||$_smarty_tpl->tpl_vars['filter']->value['type']=='quantity'){?>_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
<?php }?>" value="<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
" <?php if (isset($_smarty_tpl->tpl_vars['value']->value['checked'])&&$_smarty_tpl->tpl_vars['value']->value['checked']){?>selected="selected"<?php }?> <?php if (!$_smarty_tpl->tpl_vars['value']->value['nbr']){?>disabled="disabled"<?php }?>>
												<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['value']->value['name'], 'html', 'UTF-8');?>
<?php if ($_smarty_tpl->tpl_vars['layered_show_qties']->value){?><span> (<?php echo $_smarty_tpl->tpl_vars['value']->value['nbr'];?>
)</span><?php }?></a>
											</option>
										<?php }?>
										<?php } ?>
									</select>
								<?php }?>
							<?php }?>
						<?php }else{ ?>
							<?php if ($_smarty_tpl->tpl_vars['filter']->value['filter_type']==0){?>
								<label for="<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
"><?php echo smartyTranslate(array('s'=>'Range:','mod'=>'blocklayered'),$_smarty_tpl);?>
</label> <span id="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range"></span>
								<div class="layered_slider_container">
									<div class="layered_slider" id="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_slider"></div>
								</div>
								<script type="text/javascript">
								
									var filterRange = <?php echo $_smarty_tpl->tpl_vars['filter']->value['max'];?>
-<?php echo $_smarty_tpl->tpl_vars['filter']->value['min'];?>
;
									var step = filterRange / 100;
									if (step > 1)
										step = parseInt(step);
									addSlider('<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
',{
										range: true,
										step: step,
										min: <?php echo $_smarty_tpl->tpl_vars['filter']->value['min'];?>
,
										max: <?php echo $_smarty_tpl->tpl_vars['filter']->value['max'];?>
,
										values: [ <?php echo $_smarty_tpl->tpl_vars['filter']->value['values'][0];?>
, <?php echo $_smarty_tpl->tpl_vars['filter']->value['values'][1];?>
],
										slide: function( event, ui ) {
											stopAjaxQuery();
											
											<?php if ($_smarty_tpl->tpl_vars['filter']->value['format']<5){?>
											
												from = blocklayeredFormatCurrency(ui.values[0], <?php echo $_smarty_tpl->tpl_vars['filter']->value['format'];?>
, '<?php echo $_smarty_tpl->tpl_vars['filter']->value['unit'];?>
');
												to = blocklayeredFormatCurrency(ui.values[1], <?php echo $_smarty_tpl->tpl_vars['filter']->value['format'];?>
, '<?php echo $_smarty_tpl->tpl_vars['filter']->value['unit'];?>
');
											
											<?php }else{ ?>
											
												from = ui.values[0]+'<?php echo $_smarty_tpl->tpl_vars['filter']->value['unit'];?>
';
												to = ui.values[1]+'<?php echo $_smarty_tpl->tpl_vars['filter']->value['unit'];?>
';
											
											<?php }?>
											
											$('#layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range').html(from+' - '+to);
										},
										stop: function () {
											reloadContent();
										}
									}, '<?php echo $_smarty_tpl->tpl_vars['filter']->value['unit'];?>
', <?php echo $_smarty_tpl->tpl_vars['filter']->value['format'];?>
);
								
								</script>
							<?php }else{ ?>
								<?php if ($_smarty_tpl->tpl_vars['filter']->value['filter_type']==1){?>
								<li class="nomargin">
									<?php echo smartyTranslate(array('s'=>'From','mod'=>'blocklayered'),$_smarty_tpl);?>
 <input class="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range layered_input_range_min layered_input_range" id="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range_min" type="text" value="<?php echo $_smarty_tpl->tpl_vars['filter']->value['values'][0];?>
"/>
									<span class="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range_unit"><?php echo $_smarty_tpl->tpl_vars['filter']->value['unit'];?>
</span>
									<?php echo smartyTranslate(array('s'=>'to','mod'=>'blocklayered'),$_smarty_tpl);?>
 <input class="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range layered_input_range_max layered_input_range" id="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range_max" type="text" value="<?php echo $_smarty_tpl->tpl_vars['filter']->value['values'][1];?>
"/>
									<span class="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range_unit"><?php echo $_smarty_tpl->tpl_vars['filter']->value['unit'];?>
</span>
									<span class="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_format" style="display:none;"><?php echo $_smarty_tpl->tpl_vars['filter']->value['format'];?>
</span>
									<script type="text/javascript">
									
										$('#layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range_min').attr('limitValue', <?php echo $_smarty_tpl->tpl_vars['filter']->value['min'];?>
);
										$('#layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range_max').attr('limitValue', <?php echo $_smarty_tpl->tpl_vars['filter']->value['max'];?>
);
									
									</script>
								</li>
								<?php }else{ ?>
								<?php  $_smarty_tpl->tpl_vars['values'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['values']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['filter']->value['list_of_values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['values']->key => $_smarty_tpl->tpl_vars['values']->value){
$_smarty_tpl->tpl_vars['values']->_loop = true;
?>
									<li class="nomargin <?php if ($_smarty_tpl->tpl_vars['filter']->value['values'][1]==$_smarty_tpl->tpl_vars['values']->value[1]&&$_smarty_tpl->tpl_vars['filter']->value['values'][0]==$_smarty_tpl->tpl_vars['values']->value[0]){?>layered_list_selected<?php }?> layered_list" onclick="$('#layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range_min').val(<?php echo $_smarty_tpl->tpl_vars['values']->value[0];?>
);$('#layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range_max').val(<?php echo $_smarty_tpl->tpl_vars['values']->value[1];?>
);reloadContent();">
										- <?php echo smartyTranslate(array('s'=>'From','mod'=>'blocklayered'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['values']->value[0];?>
 <?php echo $_smarty_tpl->tpl_vars['filter']->value['unit'];?>
 <?php echo smartyTranslate(array('s'=>'to','mod'=>'blocklayered'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['values']->value[1];?>
 <?php echo $_smarty_tpl->tpl_vars['filter']->value['unit'];?>

									</li>
								<?php } ?>
								<li style="display: none;">
									<input class="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range" id="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range_min" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['filter']->value['values'][0];?>
"/>
									<input class="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range" id="layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
_range_max" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['filter']->value['values'][1];?>
"/>
								</li>
								<?php }?>
							<?php }?>
						<?php }?>
						<?php if (count($_smarty_tpl->tpl_vars['filter']->value['values'])>$_smarty_tpl->tpl_vars['filter']->value['filter_show_limit']&&$_smarty_tpl->tpl_vars['filter']->value['filter_show_limit']>0&&$_smarty_tpl->tpl_vars['filter']->value['filter_type']!=2){?>
							<span class="hide-action more"><?php echo smartyTranslate(array('s'=>'Show more','mod'=>'blocklayered'),$_smarty_tpl);?>
</span>
							<span class="hide-action less"><?php echo smartyTranslate(array('s'=>'Show less','mod'=>'blocklayered'),$_smarty_tpl);?>
</span>
						<?php }?>
						</ul>
					</div>
					<script type="text/javascript">
					
						$('.layered_<?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
').show();
					
					</script>
					<?php }?>
				<?php } ?>
			</div>
			<input type="hidden" name="id_category_layered" value="<?php echo $_smarty_tpl->tpl_vars['id_category_layered']->value;?>
" />
			<?php  $_smarty_tpl->tpl_vars['filter'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['filter']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['filters']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['filter']->key => $_smarty_tpl->tpl_vars['filter']->value){
$_smarty_tpl->tpl_vars['filter']->_loop = true;
?>
				<?php if ($_smarty_tpl->tpl_vars['filter']->value['type_lite']=='id_attribute_group'&&isset($_smarty_tpl->tpl_vars['filter']->value['is_color_group'])&&$_smarty_tpl->tpl_vars['filter']->value['is_color_group']&&$_smarty_tpl->tpl_vars['filter']->value['filter_type']!=2){?>
					<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['id_value'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['filter']->value['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['id_value']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
						<?php if (isset($_smarty_tpl->tpl_vars['value']->value['checked'])){?>
							<input type="hidden" name="layered_id_attribute_group_<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['id_value']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['filter']->value['id_key'];?>
" />
						<?php }?>
					<?php } ?>
				<?php }?>
			<?php } ?>
		</form>
	</div>
	<div id="layered_ajax_loader" style="display: none;">
		<p><img src="<?php echo $_smarty_tpl->tpl_vars['img_ps_dir']->value;?>
loader.gif" alt="" /><br /><?php echo smartyTranslate(array('s'=>'Loading...','mod'=>'blocklayered'),$_smarty_tpl);?>
</p>
	</div>
</div>
<?php }else{ ?>
<div id="layered_block_left" class="block">
	<div class="block_content">
		<form action="#" id="layered_form">
			<input type="hidden" name="id_category_layered" value="<?php echo $_smarty_tpl->tpl_vars['id_category_layered']->value;?>
" />
		</form>
	</div>
	<div style="display: none;">
		<p style=""><img src="<?php echo $_smarty_tpl->tpl_vars['img_ps_dir']->value;?>
loader.gif" alt="" /><br /><?php echo smartyTranslate(array('s'=>'Loading...','mod'=>'blocklayered'),$_smarty_tpl);?>
</p>
	</div>
</div>
<?php }?>
<!-- /Block layered navigation module -->
<?php }} ?>