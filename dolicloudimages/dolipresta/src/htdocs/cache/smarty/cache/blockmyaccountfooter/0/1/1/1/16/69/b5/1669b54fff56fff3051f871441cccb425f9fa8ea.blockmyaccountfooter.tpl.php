<?php /*%%SmartyHeaderCode:885575451c1ce5dd258b3-70779428%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1669b54fff56fff3051f871441cccb425f9fa8ea' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/blockmyaccountfooter/blockmyaccountfooter.tpl',
      1 => 1371647524,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '885575451c1ce5dd258b3-70779428',
  'variables' => 
  array (
    'link' => 0,
    'returnAllowed' => 0,
    'voucherAllowed' => 0,
    'HOOK_BLOCK_MY_ACCOUNT' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1ce5dde83f1_23091864',
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1ce5dde83f1_23091864')) {function content_51c1ce5dde83f1_23091864($_smarty_tpl) {?>
<!-- Block myaccount module -->
<div class="block myaccount">
	<p class="title_block"><a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?controller=my-account" title="Gérer mon compte client" rel="nofollow">Mon compte</a></p>
	<div class="block_content">
		<ul class="bullet">
			<li><a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?controller=history" title="Voir mes commandes" rel="nofollow">Mes commandes</a></li>
						<li><a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?controller=order-slip" title="Voir mes avoirs" rel="nofollow">Mes avoirs</a></li>
			<li><a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?controller=addresses" title="Voir mes adresses" rel="nofollow">Mes adresses</a></li>
			<li><a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?controller=identity" title="Gérer mes informations personnelles" rel="nofollow">Mes informations personnelles</a></li>
						
<li class="favoriteproducts">
	<a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?fc=module&amp;module=favoriteproducts&amp;controller=account" title="Mes produits favoris">
				Mes produits favoris
	</a>
</li>

		</ul>
		<p class="logout"><a href="http://nbraud.nltechno.com/prestashop/htdocs/index.php?mylogout" title="Se déconnecter" rel="nofollow">Sign out</a></p>
	</div>
</div>
<!-- /Block myaccount module -->
<?php }} ?>