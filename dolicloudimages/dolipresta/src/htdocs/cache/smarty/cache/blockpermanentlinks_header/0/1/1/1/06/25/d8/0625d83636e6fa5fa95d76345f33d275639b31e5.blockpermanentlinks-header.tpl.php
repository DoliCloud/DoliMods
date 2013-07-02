<?php /*%%SmartyHeaderCode:28994762351c1ce5d2dde01-45187871%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0625d83636e6fa5fa95d76345f33d275639b31e5' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/modules/blockpermanentlinks/blockpermanentlinks-header.tpl',
      1 => 1371646725,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28994762351c1ce5d2dde01-45187871',
  'variables' => 
  array (
    'link' => 0,
    'come_from' => 0,
    'meta_title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1ce5d334415_58818535',
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1ce5d334415_58818535')) {function content_51c1ce5d334415_58818535($_smarty_tpl) {?>
<!-- Block permanent links module HEADER -->
<ul id="header_links">
	<li id="header_link_contact"><a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?controller=contact" title="contact">contact</a></li>
	<li id="header_link_sitemap"><a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?controller=sitemap" title="plan du site">plan du site</a></li>
	<li id="header_link_bookmark">
		<script type="text/javascript">writeBookmarkLink('http://nbraud.nltechno.com/prestashop/htdocs/index.php', 'NOM_DE_LA_BOUTIQUE', 'favoris');</script>
	</li>
</ul>
<!-- /Block permanent links module HEADER -->
<?php }} ?>