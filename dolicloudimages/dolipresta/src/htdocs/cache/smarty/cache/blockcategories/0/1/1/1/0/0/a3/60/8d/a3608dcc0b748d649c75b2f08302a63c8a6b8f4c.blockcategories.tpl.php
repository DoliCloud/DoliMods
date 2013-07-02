<?php /*%%SmartyHeaderCode:142854900851c1ce5d69a9e4-91516030%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a3608dcc0b748d649c75b2f08302a63c8a6b8f4c' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockcategories/blockcategories.tpl',
      1 => 1371647520,
      2 => 'file',
    ),
    '0845f49d239c5bbc3d841bfc637e98be4b854ecb' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockcategories/category-tree-branch.tpl',
      1 => 1371647520,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '142854900851c1ce5d69a9e4-91516030',
  'variables' => 
  array (
    'isDhtml' => 0,
    'blockCategTree' => 0,
    'child' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1ce5d76ce55_28827809',
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1ce5d76ce55_28827809')) {function content_51c1ce5d76ce55_28827809($_smarty_tpl) {?>
<!-- Block categories module -->
<div id="categories_block_left" class="block">
	<p class="title_block">Catégories</p>
	<div class="block_content">
		<ul class="tree dhtml">
									
<li >
	<a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?id_category=3&amp;controller=category"  title="Il est temps, pour le meilleur lecteur de musique, de remonter sur sc&egrave;ne pour un rappel. Avec le nouvel iPod, le monde est votre sc&egrave;ne.">iPods</a>
	</li>

												
<li >
	<a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?id_category=4&amp;controller=category"  title="Tous les accessoires &agrave; la mode pour votre iPod">Accessoires</a>
	</li>

												
<li class="last">
	<a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?id_category=5&amp;controller=category"  title="Le tout dernier processeur Intel, un disque dur plus spacieux, de la m&eacute;moire &agrave; profusion et d&#039;autres nouveaut&eacute;s. Le tout, dans &agrave; peine 2,59 cm qui vous lib&egrave;rent de toute entrave. Les nouveaux portables Mac r&eacute;unissent les performances, la puissance et la connectivit&eacute; d&#039;un ordinateur de bureau. Sans la partie bureau.">Portables</a>
	</li>

							</ul>
		
		<script type="text/javascript">
		// <![CDATA[
			// we hide the tree only if JavaScript is activated
			$('div#categories_block_left ul.dhtml').hide();
		// ]]>
		</script>
	</div>
</div>
<!-- /Block categories module -->
<?php }} ?>