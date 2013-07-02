<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:59
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:59561721351c1c0df0cef11-80365123%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3f87805d0ae7e4da3be8abee8be0b022cd9a15bc' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/footer.tpl',
      1 => 1371646826,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '59561721351c1c0df0cef11-80365123',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content_only' => 0,
    'HOOK_RIGHT_COLUMN' => 0,
    'HOOK_FOOTER' => 0,
    'PS_ALLOW_MOBILE_DEVICE' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0df0f9bb6_21172988',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0df0f9bb6_21172988')) {function content_51c1c0df0f9bb6_21172988($_smarty_tpl) {?>

		<?php if (!$_smarty_tpl->tpl_vars['content_only']->value){?>
				</div>

<!-- Right -->
				<div id="right_column" class="column grid_2 omega">
					<?php echo $_smarty_tpl->tpl_vars['HOOK_RIGHT_COLUMN']->value;?>

				</div>
			</div>

<!-- Footer -->
			<div id="footer" class="grid_9 alpha omega clearfix">
				<?php echo $_smarty_tpl->tpl_vars['HOOK_FOOTER']->value;?>

				<?php if ($_smarty_tpl->tpl_vars['PS_ALLOW_MOBILE_DEVICE']->value){?>
					<p class="center clearBoth"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('index',true);?>
?mobile_theme_ok"><?php echo smartyTranslate(array('s'=>'Browse the mobile site'),$_smarty_tpl);?>
</a></p>
				<?php }?>
			</div>
		</div>
	<?php }?>
	</body>
</html>
<?php }} ?>