<?php /*%%SmartyHeaderCode:89118840851c1ce5d340fe4-83548297%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '308b189ecec743006f747910d6c2691a390f2df5' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/modules/blocksearch/blocksearch-top.tpl',
      1 => 1371646729,
      2 => 'file',
    ),
    '8bec9acfa72bf78a92f3258c7d0d1d5202961fa7' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/modules/blocksearch/blocksearch-instantsearch.tpl',
      1 => 1371646729,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '89118840851c1ce5d340fe4-83548297',
  'variables' => 
  array (
    'hook_mobile' => 0,
    'link' => 0,
    'ENT_QUOTES' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1ce5d44d139_23310719',
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1ce5d44d139_23310719')) {function content_51c1ce5d44d139_23310719($_smarty_tpl) {?><!-- block seach mobile -->
<!-- Block search module TOP -->
<div id="search_block_top">

	<form method="get" action="http://nbraud.nltechno.com/prestashop/htdocs/index.php?controller=search" id="searchbox">
		<p>
			<label for="search_query_top"><!-- image on background --></label>
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="orderby" value="position" />
			<input type="hidden" name="orderway" value="desc" />
			<input class="search_query" type="text" id="search_query_top" name="search_query" value="" />
			<input type="submit" name="submit_search" value="Rechercher" class="button" />
	</p>
	</form>
</div>
	<script type="text/javascript">
	// <![CDATA[
		$('document').ready( function() {
			$("#search_query_top")
				.autocomplete(
					'http://nbraud.nltechno.com/prestashop/htdocs/index.php?controller=search', {
						minChars: 3,
						max: 10,
						width: 500,
						selectFirst: false,
						scroll: false,
						dataType: "json",
						formatItem: function(data, i, max, value, term) {
							return value;
						},
						parse: function(data) {
							var mytab = new Array();
							for (var i = 0; i < data.length; i++)
								mytab[mytab.length] = { data: data[i], value: data[i].cname + ' > ' + data[i].pname };
							return mytab;
						},
						extraParams: {
							ajaxSearch: 1,
							id_lang: 1
						}
					}
				)
				.result(function(event, data, formatted) {
					$('#search_query_top').val(data.pname);
					document.location.href = data.product_link;
				})
		});
	// ]]>
	</script>

<!-- /Block search module TOP -->
<?php }} ?>