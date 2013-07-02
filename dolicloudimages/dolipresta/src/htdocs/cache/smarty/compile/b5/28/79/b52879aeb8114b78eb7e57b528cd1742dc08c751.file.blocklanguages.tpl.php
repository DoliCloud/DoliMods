<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 17:29:33
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/modules/blocklanguages/blocklanguages.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18389482951c1ce5d181794-01826627%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b52879aeb8114b78eb7e57b528cd1742dc08c751' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/modules/blocklanguages/blocklanguages.tpl',
      1 => 1371646711,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18389482951c1ce5d181794-01826627',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'languages' => 0,
    'language' => 0,
    'lang_iso' => 0,
    'img_lang_dir' => 0,
    'indice_lang' => 0,
    'lang_rewrite_urls' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1ce5d25bc44_64003174',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1ce5d25bc44_64003174')) {function content_51c1ce5d25bc44_64003174($_smarty_tpl) {?>

<!-- Block languages module -->
<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1){?>
<div id="languages_block_top">
	<div id="countries">
	
<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>
	<?php if ($_smarty_tpl->tpl_vars['language']->value['iso_code']==$_smarty_tpl->tpl_vars['lang_iso']->value){?>
		<p class="selected_language">
			<img src="<?php echo $_smarty_tpl->tpl_vars['img_lang_dir']->value;?>
<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
.jpg" alt="<?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>
" width="16" height="11" />
		</p>
	<?php }?>
<?php } ?>
		<ul id="first-languages" class="countries_ul">
		<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>
			<li <?php if ($_smarty_tpl->tpl_vars['language']->value['iso_code']==$_smarty_tpl->tpl_vars['lang_iso']->value){?>class="selected_language"<?php }?>>
			<?php if ($_smarty_tpl->tpl_vars['language']->value['iso_code']!=$_smarty_tpl->tpl_vars['lang_iso']->value){?>
				<?php $_smarty_tpl->tpl_vars['indice_lang'] = new Smarty_variable($_smarty_tpl->tpl_vars['language']->value['id_lang'], null, 0);?>
				<?php if (isset($_smarty_tpl->tpl_vars['lang_rewrite_urls']->value[$_smarty_tpl->tpl_vars['indice_lang']->value])){?>
					<a href="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['lang_rewrite_urls']->value[$_smarty_tpl->tpl_vars['indice_lang']->value], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" title="<?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
">
				<?php }else{ ?>
					<a href="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getLanguageLink($_smarty_tpl->tpl_vars['language']->value['id_lang']), ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" title="<?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
">

				<?php }?>
			<?php }?>
					<img src="<?php echo $_smarty_tpl->tpl_vars['img_lang_dir']->value;?>
<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
.jpg" alt="<?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>
" width="16" height="11" />
			<?php if ($_smarty_tpl->tpl_vars['language']->value['iso_code']!=$_smarty_tpl->tpl_vars['lang_iso']->value){?>
				</a>
			<?php }?>
			</li>
		<?php } ?>
		</ul>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function () {
	$("#countries").mouseover(function(){
		$(this).addClass("countries_hover");
		$(".countries_ul").addClass("countries_ul_hover");
	});
	$("#countries").mouseout(function(){
		$(this).removeClass("countries_hover");
		$(".countries_ul").removeClass("countries_ul_hover");
	});

});
</script>
<?php }?>
<!-- /Block languages module -->
<?php }} ?>