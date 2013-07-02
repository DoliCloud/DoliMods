<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:03
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/pagination.tpl" */ ?>
<?php /*%%SmartyHeaderCode:63839834451c1c0e3672097-50349105%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3b920277d2334e69a90cef8a8e66ba76e5e43f65' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/pagination.tpl',
      1 => 1371646832,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '63839834451c1c0e3672097-50349105',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'no_follow' => 0,
    'p' => 0,
    'category' => 0,
    'current_url' => 0,
    'link' => 0,
    'manufacturer' => 0,
    'supplier' => 0,
    'start' => 0,
    'stop' => 0,
    'no_follow_text' => 0,
    'requestPage' => 0,
    'p_previous' => 0,
    'pages_nb' => 0,
    'p_next' => 0,
    'nb_products' => 0,
    'products_per_page' => 0,
    'requestNb' => 0,
    'search_query' => 0,
    'tag' => 0,
    'nArray' => 0,
    'lastnValue' => 0,
    'nValue' => 0,
    'n' => 0,
    'requestKey' => 0,
    'requestValue' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0e3a528c5_25005523',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0e3a528c5_25005523')) {function content_51c1c0e3a528c5_25005523($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>

<?php if (isset($_smarty_tpl->tpl_vars['no_follow']->value)&&$_smarty_tpl->tpl_vars['no_follow']->value){?>
	<?php $_smarty_tpl->tpl_vars['no_follow_text'] = new Smarty_variable('rel="nofollow"', null, 0);?>
<?php }else{ ?>
	<?php $_smarty_tpl->tpl_vars['no_follow_text'] = new Smarty_variable('', null, 0);?>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['p']->value)&&$_smarty_tpl->tpl_vars['p']->value){?>
	<?php if (isset($_GET['id_category'])&&$_GET['id_category']&&isset($_smarty_tpl->tpl_vars['category']->value)){?>
		<?php if (!isset($_smarty_tpl->tpl_vars['current_url']->value)){?>
		<?php $_smarty_tpl->tpl_vars['requestPage'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('category',$_smarty_tpl->tpl_vars['category']->value,false,false,true,false), null, 0);?>
		<?php }else{ ?>
			<?php $_smarty_tpl->tpl_vars['requestPage'] = new Smarty_variable($_smarty_tpl->tpl_vars['current_url']->value, null, 0);?>
		<?php }?>
		<?php $_smarty_tpl->tpl_vars['requestNb'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('category',$_smarty_tpl->tpl_vars['category']->value,true,false,false,true), null, 0);?>
	<?php }elseif(isset($_GET['id_manufacturer'])&&$_GET['id_manufacturer']&&isset($_smarty_tpl->tpl_vars['manufacturer']->value)){?>
		<?php $_smarty_tpl->tpl_vars['requestPage'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('manufacturer',$_smarty_tpl->tpl_vars['manufacturer']->value,false,false,true,false), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['requestNb'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('manufacturer',$_smarty_tpl->tpl_vars['manufacturer']->value,true,false,false,true), null, 0);?>
	<?php }elseif(isset($_GET['id_supplier'])&&$_GET['id_supplier']&&isset($_smarty_tpl->tpl_vars['supplier']->value)){?>
		<?php $_smarty_tpl->tpl_vars['requestPage'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('supplier',$_smarty_tpl->tpl_vars['supplier']->value,false,false,true,false), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['requestNb'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink('supplier',$_smarty_tpl->tpl_vars['supplier']->value,true,false,false,true), null, 0);?>
	<?php }else{ ?>
		<?php $_smarty_tpl->tpl_vars['requestPage'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink(false,false,false,false,true,false), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['requestNb'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getPaginationLink(false,false,true,false,false,true), null, 0);?>
	<?php }?>
	<!-- Pagination -->
	<div id="pagination" class="pagination">
	<?php if ($_smarty_tpl->tpl_vars['start']->value!=$_smarty_tpl->tpl_vars['stop']->value){?>
		<ul class="pagination">
		<?php if ($_smarty_tpl->tpl_vars['p']->value!=1){?>
			<?php $_smarty_tpl->tpl_vars['p_previous'] = new Smarty_variable($_smarty_tpl->tpl_vars['p']->value-1, null, 0);?>
			<li id="pagination_previous"><a <?php echo $_smarty_tpl->tpl_vars['no_follow_text']->value;?>
 href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,$_smarty_tpl->tpl_vars['p_previous']->value);?>
">&laquo;&nbsp;<?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
</a></li>
		<?php }else{ ?>
			<li id="pagination_previous" class="disabled"><span>&laquo;&nbsp;<?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
</span></li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['start']->value==3){?>
			<li><a <?php echo $_smarty_tpl->tpl_vars['no_follow_text']->value;?>
  href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,1);?>
">1</a></li>
			<li><a <?php echo $_smarty_tpl->tpl_vars['no_follow_text']->value;?>
  href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,2);?>
">2</a></li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['start']->value==2){?>
			<li><a <?php echo $_smarty_tpl->tpl_vars['no_follow_text']->value;?>
  href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,1);?>
">1</a></li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['start']->value>3){?>
			<li><a <?php echo $_smarty_tpl->tpl_vars['no_follow_text']->value;?>
  href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,1);?>
">1</a></li>
			<li class="truncate">...</li>
		<?php }?>
		<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['name'] = 'pagination';
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['start'] = (int)$_smarty_tpl->tpl_vars['start']->value;
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['stop']->value+1) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['step'] = ((int)1) == 0 ? 1 : (int)1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['loop'];
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['pagination']['total']);
?>
			<?php if ($_smarty_tpl->tpl_vars['p']->value==$_smarty_tpl->getVariable('smarty')->value['section']['pagination']['index']){?>
				<li class="current"><span><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['p']->value, 'htmlall', 'UTF-8');?>
</span></li>
			<?php }else{ ?>
				<li><a <?php echo $_smarty_tpl->tpl_vars['no_follow_text']->value;?>
 href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,$_smarty_tpl->getVariable('smarty')->value['section']['pagination']['index']);?>
"><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('smarty')->value['section']['pagination']['index'], 'htmlall', 'UTF-8');?>
</a></li>
			<?php }?>
		<?php endfor; endif; ?>
		<?php if ($_smarty_tpl->tpl_vars['pages_nb']->value>$_smarty_tpl->tpl_vars['stop']->value+2){?>
			<li class="truncate">...</li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,$_smarty_tpl->tpl_vars['pages_nb']->value);?>
"><?php echo intval($_smarty_tpl->tpl_vars['pages_nb']->value);?>
</a></li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['pages_nb']->value==$_smarty_tpl->tpl_vars['stop']->value+1){?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,$_smarty_tpl->tpl_vars['pages_nb']->value);?>
"><?php echo intval($_smarty_tpl->tpl_vars['pages_nb']->value);?>
</a></li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['pages_nb']->value==$_smarty_tpl->tpl_vars['stop']->value+2){?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,$_smarty_tpl->tpl_vars['pages_nb']->value-1);?>
"><?php echo $_smarty_tpl->tpl_vars['pages_nb']->value-intval(1);?>
</a></li>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,$_smarty_tpl->tpl_vars['pages_nb']->value);?>
"><?php echo intval($_smarty_tpl->tpl_vars['pages_nb']->value);?>
</a></li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['pages_nb']->value>1&&$_smarty_tpl->tpl_vars['p']->value!=$_smarty_tpl->tpl_vars['pages_nb']->value){?>
			<?php $_smarty_tpl->tpl_vars['p_next'] = new Smarty_variable($_smarty_tpl->tpl_vars['p']->value+1, null, 0);?>
			<li id="pagination_next"><a <?php echo $_smarty_tpl->tpl_vars['no_follow_text']->value;?>
 href="<?php echo $_smarty_tpl->tpl_vars['link']->value->goPage($_smarty_tpl->tpl_vars['requestPage']->value,$_smarty_tpl->tpl_vars['p_next']->value);?>
"><?php echo smartyTranslate(array('s'=>'Next'),$_smarty_tpl);?>
&nbsp;&raquo;</a></li>
		<?php }else{ ?>
			<li id="pagination_next" class="disabled"><span><?php echo smartyTranslate(array('s'=>'Next'),$_smarty_tpl);?>
&nbsp;&raquo;</span></li>
		<?php }?>
		</ul>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['nb_products']->value>$_smarty_tpl->tpl_vars['products_per_page']->value){?>
		<form action="<?php if (!is_array($_smarty_tpl->tpl_vars['requestNb']->value)){?><?php echo $_smarty_tpl->tpl_vars['requestNb']->value;?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['requestNb']->value['requestUrl'];?>
<?php }?>" method="get" class="pagination">
			<p>
				<?php if (isset($_smarty_tpl->tpl_vars['search_query']->value)&&$_smarty_tpl->tpl_vars['search_query']->value){?><input type="hidden" name="search_query" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['search_query']->value, 'htmlall', 'UTF-8');?>
" /><?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['tag']->value)&&$_smarty_tpl->tpl_vars['tag']->value&&!is_array($_smarty_tpl->tpl_vars['tag']->value)){?><input type="hidden" name="tag" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['tag']->value, 'htmlall', 'UTF-8');?>
" /><?php }?>
				<input type="submit" class="button_mini" value="<?php echo smartyTranslate(array('s'=>'OK'),$_smarty_tpl);?>
" />
				<label for="nb_item"><?php echo smartyTranslate(array('s'=>'items:'),$_smarty_tpl);?>
</label>
				<select name="n" id="nb_item">
				<?php $_smarty_tpl->tpl_vars["lastnValue"] = new Smarty_variable("0", null, 0);?>
				<?php  $_smarty_tpl->tpl_vars['nValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['nValue']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['nArray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['nValue']->key => $_smarty_tpl->tpl_vars['nValue']->value){
$_smarty_tpl->tpl_vars['nValue']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['lastnValue']->value<=$_smarty_tpl->tpl_vars['nb_products']->value){?>
						<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['nValue']->value, 'htmlall', 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['n']->value==$_smarty_tpl->tpl_vars['nValue']->value){?>selected="selected"<?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['nValue']->value, 'htmlall', 'UTF-8');?>
</option>
					<?php }?>
					<?php $_smarty_tpl->tpl_vars["lastnValue"] = new Smarty_variable($_smarty_tpl->tpl_vars['nValue']->value, null, 0);?>
				<?php } ?>
				</select>
				<?php if (is_array($_smarty_tpl->tpl_vars['requestNb']->value)){?>
					<?php  $_smarty_tpl->tpl_vars['requestValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['requestValue']->_loop = false;
 $_smarty_tpl->tpl_vars['requestKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['requestNb']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['requestValue']->key => $_smarty_tpl->tpl_vars['requestValue']->value){
$_smarty_tpl->tpl_vars['requestValue']->_loop = true;
 $_smarty_tpl->tpl_vars['requestKey']->value = $_smarty_tpl->tpl_vars['requestValue']->key;
?>
						<?php if ($_smarty_tpl->tpl_vars['requestKey']->value!='requestUrl'){?>
							<input type="hidden" name="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['requestKey']->value, 'htmlall', 'UTF-8');?>
" value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['requestValue']->value, 'htmlall', 'UTF-8');?>
" />
						<?php }?>
					<?php } ?>
				<?php }?>
			</p>
		</form>
	<?php }?>
	</div>
	<!-- /Pagination -->
<?php }?>
<?php }} ?>