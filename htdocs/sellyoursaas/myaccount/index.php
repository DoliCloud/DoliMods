<?php

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				    // If this page is public (can be called outside logged session)


include ('./common.inc.php');

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';


/*
 * View
 */

llxHeader();

/*
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<!--<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css">-->
<link rel="stylesheet" href="/assets/global/plugins/font-awesome/css/font-awesome.min-33d76662a3bc0a7da379d5998ffa0927.css"/>
<link rel="stylesheet" href="/assets/global/plugins/simple-line-icons/simple-line-icons.min-881ba469e6b4988981bb0469258fecc4.css"/>
<link rel="stylesheet" href="/assets/global/plugins/bootstrap/css/bootstrap.min-f44841c36db82944dc3d2f2f947539b7.css"/>
<link rel="stylesheet" href="/assets/global/plugins/uniform/css/uniform.default-b36287a9a7c9b97815bc9e4c0bd9f5a7.css"/>
<link rel="stylesheet" href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min-ba5e3a0950e13427714742901ae33832.css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="/assets/global/plugins/select2/select2-8219bb2353df99c411fb22c1fc695e58.css"/>
<!-- BEGIN THEME STYLES -->
<link rel="stylesheet" id="style_components" href="/assets/global/css/components-rounded-26764a21ab1726b9e095eb7a6d256f74.css"/>
<link rel="stylesheet" href="/assets/global/css/plugins-61a0911b4d4ee273dfa7344919b59c8c.css"/>
<link rel="stylesheet" href="/assets/admin/layout4/css/layout-e9c543e96fd005f6f83f95bef811e2ca.css"/>
<link rel="stylesheet" id="style_color" href="/assets/admin/layout4/css/themes/light-ca6c72aa1e0f0afa9e98f5a849ad9b75.css"/>
<link rel="stylesheet" href="/assets/admin/layout4/css/custom-dfa3435129e28534079725c820e3dc1c.css"/>
<!-- BEGIN PAGE LEVEL STYLES -->
<!-- may need to move these styles so they are not in the global layout -->
<link rel="stylesheet" href="/assets/global/plugins/icheck/skins/all-7a5ba28444075ac7268f028c3901c97f.css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- CUSTOM STYLE -->
<link rel="stylesheet" href="/assets/custom-6a4cd672859629b69b7455fa78a41f6d.css"/>

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="/assets/global/plugins/respond.min-6c16b279230d42df71b12ce701c12601.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/excanvas.min-6bdfe35ac8a675dbfa2282b6e1ec08a0.js" type="text/javascript" ></script>
<![endif]-->
<script src="/assets/global/plugins/jquery.min-ddaf60ab0024dd5dea8eb84479d133b3.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/jquery-migrate.min-9d0c21ba9c368042c3ce18201c638aef.js" type="text/javascript" ></script>
<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="/assets/global/plugins/jquery-ui/jquery-ui.min-82d8b2305a91d4b1be36d5a62923b14b.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/bootstrap/js/bootstrap.min-1f7e5ddea6c801f5345ad851606a5492.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min-394737b25e7ae1573de1b27129c7004b.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min-a830f0d792d2d4a48f648385eb832759.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/jquery.blockui.min-a41bb18056bf38a2ede8f5711281d910.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/jquery.cokie.min-1fe12842c869ff4e3d2070941d50cc00.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/uniform/jquery.uniform.min-854266869b8a30559dd43b21c157ce3b.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min-613fd75f1fc429aa4311f6eef8385181.js" type="text/javascript" ></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="/assets/global/plugins/select2/select2.min-00f8c7ecce1c496afe9d9fecfd133dd6.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min-613fd75f1fc429aa4311f6eef8385181.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/amcharts/amcharts/amcharts-e947734e5654742f91815c8d1695173c.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/amcharts/amcharts/serial-173beb72499374cf7a26fbab48b82c2f.js" type="text/javascript" ></script>
<script src="/assets/global/plugins/amcharts/amcharts/pie-a69d55d1fb0833b457c5617c6e3d9647.js" type="text/javascript" ></script>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="/assets/global/scripts/metronic-bb1e8b7737ef1fe670947989823b54ce.js" type="text/javascript" ></script>
<script src="/assets/admin/layout4/scripts/layout-a7065c9428f20e4bfb7edc8a97352285.js" type="text/javascript" ></script>
<script src="/assets/admin/layout4/scripts/demo-109dda2d09bc3dfcaea180b8a7ebc03b.js" type="text/javascript" ></script>
<script src="/assets/admin/pages/scripts/charts-amcharts-d8213f5db306fa6502475a2954def65c.js" type="text/javascript" ></script>

<!-- END PAGE LEVEL SCRIPTS -->


<meta name="layout" content="customerUI"/>


</head>
<!-- END HEAD -->
*/


print '
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-header-fixed page-sidebar-closed-hide-logo">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
<!-- BEGIN HEADER INNER -->
<div class="page-header-inner">
<!-- BEGIN LOGO -->
<div class="page-logo">
<a href="/customerUI/home">
<img width="200" src="/image/ebcccc95cca729596e56c8a61f8ef9b35118ca85" id="logo" />
</a>
<div class="menu-toggler sidebar-toggler">
<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
</div>
</div>
<!-- END LOGO -->
<!-- BEGIN RESPONSIVE MENU TOGGLER -->
<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
</a>
<!-- END RESPONSIVE MENU TOGGLER -->
<!-- BEGIN PAGE TOP -->
<div class="page-top">
<!-- BEGIN TOP NAVIGATION MENU -->
<div class="top-menu">
<ul class="nav navbar-nav pull-right">
<li class="separator hide">
</li>
<!-- BEGIN USER LOGIN DROPDOWN -->
<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
<li class="dropdown dropdown-user dropdown-dark">
<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
<span class="username username-hide-on-mobile">admin@nltechno.com</span>
<!-- DOC: Do not remove below empty space(&nbsp;) as its purposely used -->
<img src="/assets/admin/layout/img/avatar-d4326bad16607e040dc05ee3ab773192.png" alt="avatar" class="img-circle"/>
</a>
<ul class="dropdown-menu dropdown-menu-default">
<li>
<a href="/customerUI/myAccount?customer=37843">
<i class="icon-briefcase"></i> My Account
</a>
</li>
<li>
<a href="/logout/index">
<i class="icon-key"></i> Sign Out
</a>
</li>
</ul>
</li>
<!-- END USER LOGIN DROPDOWN -->
</ul>
</div>
<!-- END TOP NAVIGATION MENU -->
</div>
<!-- END PAGE TOP -->
</div>
<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
<!-- BEGIN SIDEBAR -->
<div class="page-sidebar-wrapper">
<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
<div class="page-sidebar navbar-collapse collapse">
<!-- BEGIN SIDEBAR MENU -->
<!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
<!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
<!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
<!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
<!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
<ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
<li>
<a href="/customerUI/apps">
<i class="icon-rocket"></i>
<span class="title">Apps</span>
</a>
</li>
<li>
<a href="/customerUI/myAccount">
<i class="icon-briefcase"></i>
<span class="title">My Account</span>
</a>
</li>
<li>
<a href="/customerUI/billingOverview">
<i class="icon-wallet"></i>
<span class="title">Billing &amp; Plans</span>
</a>
</li>
</ul>
<!-- END SIDEBAR MENU -->
</div>
</div>
<!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
<div class="page-content">


<!-- BEGIN PAGE HEADER-->
<!-- BEGIN PAGE HEAD -->
<div class="page-head">
<!-- BEGIN PAGE TITLE -->
<div class="page-title">
<h1>Welcome <small>Welcome abord!</small></h1>
</div>
<!-- END PAGE TITLE -->


</div>
<!-- END PAGE HEAD -->
<!-- END PAGE HEADER-->




<div class="portlet light">
<div class="portlet-header">
<div class="caption">
<span class="caption-subject font-green-sharp bold uppercase">Installation Complete!</span>
</div>
</div>
<div class="portlet-body">
<p>
You can access your new Dolibarr ERP-CRM using the following information.
</p>
<p class="well">
Url: <a href="http://fdfdfd.on.dolicloud.com" target="_blank">http://fdfdfd.on.dolicloud.com</a>

<br /> Username: admin
<br /> Password: bigone-10
</p>
<p>
<a class="btn btn-primary" target="_blank" href="http://fdfdfd.on.dolicloud.com/">
Take me to my new Dolibarr ERP-CRM
</a>
</p>

</div>
</div> <!-- END PORTLET -->

</div>
</div>
<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<script>
jQuery(document).ready(function() {
	Metronic.init(); // init metronic core components
	Layout.init(); // init current layout
	Demo.init(); // init demo features
});
	$(document).ready(function() {
		$(\'a[href="\' + this.location.pathname + \'"]\').parent().addClass(\'active open\');
	});
		</script>

		<!-- END JAVASCRIPTS -->
';


llxFooter();
$db->close();
