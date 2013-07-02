<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:31:59
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/home/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:22283720351c1c0df909f97-03368257%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd4cc16b8082915be5a0564262b86fcd26dd73383' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/admin/themes/default/template/controllers/home/content.tpl',
      1 => 1371647772,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22283720351c1c0df909f97-03368257',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'upgrade' => 0,
    'isoUser' => 0,
    'employee' => 0,
    'protocol' => 0,
    'token' => 0,
    'quick_links' => 0,
    'k' => 0,
    'link' => 0,
    'tips_optimization' => 0,
    'monthly_statistics' => 0,
    'customers_service' => 0,
    'stats_sales' => 0,
    'last_orders' => 0,
    'refresh_check_version' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0dfa25dd2_33186389',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0dfa25dd2_33186389')) {function content_51c1c0dfa25dd2_33186389($_smarty_tpl) {?>

	<div class="pageTitleHome">
		<span><h3><?php echo smartyTranslate(array('s'=>'Dashboard'),$_smarty_tpl);?>
</h3></span>
	</div>
<div id="dashboard">
<div id="homepage">


	<div id="column_left">
		<?php if ($_smarty_tpl->tpl_vars['upgrade']->value){?>
		<div id="blockNewVersionCheck">
		<?php if ($_smarty_tpl->tpl_vars['upgrade']->value->need_upgrade){?>
			<div class="warning warn" style="margin-bottom:10px;"><h3><?php echo smartyTranslate(array('s'=>'A new version of PrestaShop is available.'),$_smarty_tpl);?>
 : <a style="text-decoration: underline;" href="<?php echo $_smarty_tpl->tpl_vars['upgrade']->value->link;?>
" target="_blank"><?php echo smartyTranslate(array('s'=>'Download'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['upgrade']->value->version_name;?>
</a> !</h3></div>
		<?php }?>
		</div>
	<?php }else{ ?>
		<p><?php echo smartyTranslate(array('s'=>'Update notifications are unavailable'),$_smarty_tpl);?>
</p>
		<p>&nbsp;</p>
		<p><?php echo smartyTranslate(array('s'=>'To receive PrestaShop update warnings, you need to activate you account. '),$_smarty_tpl);?>
 <b>allow_url_fopen</b> [<a href="http://www.php.net/manual/<?php echo $_smarty_tpl->tpl_vars['isoUser']->value;?>
/ref.filesystem.php"><?php echo smartyTranslate(array('s'=>'more info on php.net'),$_smarty_tpl);?>
</a>]</p>
		<p><?php echo smartyTranslate(array('s'=>'If you don\'t know how to do this, please contact your hosting provider!'),$_smarty_tpl);?>
</p><br />
	<?php }?>

<?php if ($_smarty_tpl->tpl_vars['employee']->value->bo_show_screencast){?>
<div id="adminpresentation" style="display:block">
<h2><?php echo smartyTranslate(array('s'=>'Video'),$_smarty_tpl);?>
</h2>
		<div id="video">
			<a href="<?php echo $_smarty_tpl->tpl_vars['protocol']->value;?>
://screencasts.prestashop.com/v1.5/screencast.php?iso_lang=<?php echo $_smarty_tpl->tpl_vars['isoUser']->value;?>
" id="screencast_fancybox"><img height="128" width="220" src="../img/admin/preview_fr.jpg" /><span class="mask-player"></span></a>
		</div>
			<div id="video-content">
			<p><?php echo smartyTranslate(array('s'=>'Take part in the e-commerce adventure with PrestaShop, the best open-source shopping-cart solution on the planet. With more than 310 native features, PrestaShop comes fully equipped to help create a world of opportunity without limits. '),$_smarty_tpl);?>
</p>
			</div>
	<div id="footer_iframe_home">
		<!--<a href="#"><?php echo smartyTranslate(array('s'=>'View more video tutorials.'),$_smarty_tpl);?>
</a>-->
		<input type="checkbox" id="screencast_dont_show_again">
		<label for="screencast_dont_show_again"><?php echo smartyTranslate(array('s'=>'Do not show me this again.'),$_smarty_tpl);?>
</label>
	</div>
				<div class="separation"></div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#screencast_dont_show_again').click(function() {
		if ($(this).is(':checked'))
		{
			$.ajax({
				type : 'POST',
				data : {
					ajax : '1',
					controller : 'AdminHome',
					token : '<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
',
					id_employee : '<?php echo $_smarty_tpl->tpl_vars['employee']->value->id;?>
',
					action : 'hideScreencast'
				},
				url: 'ajax-tab.php',
				dataType : 'json',
				success: function(data) {
					if (!data)
						jAlert("TECHNICAL ERROR - no return status found");
					else if (data.status != "ok")
						jAlert("TECHNICAL ERROR: "+data.msg);

					$('#adminpresentation').slideUp('slow');
					
				},
				error: function(data, textStatus, errorThrown)
				{
					jAlert("TECHNICAL ERROR: "+data);
				}
			});
		}
	});
});
</script>
<?php }?>

<h2><?php echo smartyTranslate(array('s'=>'Quick links'),$_smarty_tpl);?>
</h2>
		<ul class="F_list clearfix">
		<?php  $_smarty_tpl->tpl_vars['link'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['link']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['quick_links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['link']->key => $_smarty_tpl->tpl_vars['link']->value){
$_smarty_tpl->tpl_vars['link']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['link']->key;
?>
		<li id="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
_block">
			<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value['href'];?>
">
				<h4><?php echo $_smarty_tpl->tpl_vars['link']->value['title'];?>
</h4>
				<p><?php echo $_smarty_tpl->tpl_vars['link']->value['description'];?>
</p>
			</a>
		</li>
		<?php } ?>
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayAdminHomeQuickLinks"),$_smarty_tpl);?>

		</ul>

	<div id="partner_preactivation">
		<p class="center"><img src="../img/loader.gif" alt="" /></p>
	</div>

	<div class="separation"></div>


	<?php echo $_smarty_tpl->tpl_vars['tips_optimization']->value;?>

	<div id="discover_prestashop"><p class="center"><img src="../img/loader.gif" alt="" /><?php echo smartyTranslate(array('s'=>'Loading...'),$_smarty_tpl);?>
</p></div>

	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayAdminHomeInfos"),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayBackOfficeHome"),$_smarty_tpl);?>
 

</div>


	<div id="column_right">
	<h2><?php echo smartyTranslate(array('s'=>'Your Information'),$_smarty_tpl);?>
</h2>
		<?php echo $_smarty_tpl->tpl_vars['monthly_statistics']->value;?>

		<?php echo $_smarty_tpl->tpl_vars['customers_service']->value;?>

		<?php echo $_smarty_tpl->tpl_vars['stats_sales']->value;?>

		<?php echo $_smarty_tpl->tpl_vars['last_orders']->value;?>

		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayAdminHomeStatistics"),$_smarty_tpl);?>

	</div>

</div>
	<div class="clear">&nbsp;</div>
	
	</div>

<script type="text/javascript">
$(document).ready(function() {
	if (<?php echo $_smarty_tpl->tpl_vars['refresh_check_version']->value;?>
)
	{
		$('#blockNewVersionCheck').hide();
		$.ajax({
			type : 'POST',
			data : {
				ajax : '1',
				controller : 'AdminHome',
				token : '<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
',
				id_employee : '<?php echo $_smarty_tpl->tpl_vars['employee']->value->id;?>
',
				action : 'refreshCheckVersion'
			},
			url: 'ajax-tab.php',
			dataType : 'json',
			success: function(data) {
				if (!data)
					jAlert("TECHNICAL ERROR - no return status found");
				else if (data.status != "ok")
					jAlert("TECHNICAL ERROR: "+data.msg);
				if(data.upgrade.need_upgrade)
				{
					$('#blockNewVersionCheck').children("a").attr('href',data.upgrade.link);
					$('#blockNewVersionCheck').children("a").html(data.upgrade.link+"pouet");
					$('#blockNewVersionCheck').fadeIn('slow');
				}

				
			},
			error: function(data, textStatus, errorThrown)
			{
				jAlert("TECHNICAL ERROR: "+data);
			}
		});
	}
	$.ajax({
		url: "ajax-tab.php",
		type: "POST",
		data:{
			token: "<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
",
			ajax: "1",
			controller : "AdminHome",
			action: "getAdminHomeElement"
		},
		dataType: "json",
		success: function(json) {
		<?php if ($_smarty_tpl->tpl_vars['employee']->value->bo_show_screencast){?>
			if (json.screencast != 'NOK')
				$('#adminpresentation').fadeIn('slow');
			else
				$('#adminpresentation').fadeOut('slow');
		<?php }?>
			$('#partner_preactivation').fadeOut('slow', function() {
				if (json.partner_preactivation != 'NOK')
					$('#partner_preactivation').html(json.partner_preactivation);
				else
					$('#partner_preactivation').html('');
				$('#partner_preactivation').fadeIn('slow');
			});

			$('#discover_prestashop').fadeOut('slow', function() {
				if (json.discover_prestashop != 'NOK')
					$('#discover_prestashop').replaceWith(json.discover_prestashop);
				else
					$('#discover_prestashop').html('');
				$('#discover_prestashop').fadeIn('slow');
			});
		},
		error: function(XMLHttpRequest, textStatus, errorThrown)
		{
			// don't show/hide screencast if it's deactivated
			<?php if ($_smarty_tpl->tpl_vars['employee']->value->bo_show_screencast){?>
			$('#adminpresentation').fadeOut('slow');
			<?php }?>
			$('#partner_preactivation').fadeOut('slow');
			$('#discover_prestashop').fadeOut('slow');
		}
	});
	$('#screencast_fancybox').bind('click', function(event)
	{
		$.fancybox(
			this.href,
			{
				'width'				: 	660,
				'height'			: 	384,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type' 				: 'iframe',
				'scrolling'			: 'no',
				'onComplete'		: function()
					{
						// Rewrite some css properties of Fancybox
						$('#fancybox-wrap').css('width', '');
						$('#fancybox-content').css('background-color', '');
						$('#fancybox-content').css( 'border', '');
					}
			});

		event.preventDefault();
	});
});
</script>
<?php }} ?>