<?php

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				    	// If this page is public (can be called outside logged session)
if (! defined("MAIN_LANG_DEFAULT")) define('MAIN_LANG_DEFAULT','auto');
if (! defined("MAIN_AUTHENTICATION_MODE")) define('MAIN_AUTHENTICATION_MODE','sellyoursaas');

// Load Dolibarr environment
include ('./mainmyaccount.inc.php');

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
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
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/website/class/website.class.php';

$conf->global->SYSLOG_FILE_ONEPERSESSION=1;

$welcomecid = GETPOST('welcomecid','alpha');
$mode = GETPOST('mode', 'alpha');
if (empty($mode) && empty($welcomecid)) $mode='dashboard';

$langs=new Translate('', $conf);
$langs->setDefaultLang('auto');

$langs->loadLangs(array("main","companies","bills","sellyoursaas@sellyoursaas"));


/*
 * View
 */

$form = new Form($db);

$head='<link rel="icon" href="img/favicon.ico">
<!-- Bootstrap core CSS -->
<link href="dist/css/bootstrap.min.css" rel="stylesheet">
<link href="dist/css/myaccount.css" rel="stylesheet">';
$head.="
<script>
var select2arrayoflanguage = {
	matches: function (matches) { return matches + '" .dol_escape_js($langs->transnoentitiesnoconv("Select2ResultFoundUseArrows"))."'; },
	noResults: function () { return '". dol_escape_js($langs->transnoentitiesnoconv("Select2NotFound")). "'; },
	inputTooShort: function (input) {
		var n = input.minimum;
		/*console.log(input);
		console.log(input.minimum);*/
		if (n > 1) return '". dol_escape_js($langs->transnoentitiesnoconv("Select2Enter")). "' + n + '". dol_escape_js($langs->transnoentitiesnoconv("Select2MoreCharacters")) ."';
			else return '". dol_escape_js($langs->transnoentitiesnoconv("Select2Enter")) ."' + n + '". dol_escape_js($langs->transnoentitiesnoconv("Select2MoreCharacter")) . "';
		},
	loadMore: function (pageNumber) { return '".dol_escape_js($langs->transnoentitiesnoconv("Select2LoadingMoreResults"))."'; },
	searching: function () { return '". dol_escape_js($langs->transnoentitiesnoconv("Select2SearchInProgress"))."'; }
};
</script>
";

$website = new Website($db);
$website->fetch(0, 'sellyoursaas');


llxHeader($head, $langs->trans("MyAccount"));

$linklogo = DOL_URL_ROOT.'/viewimage.php?modulepart=mycompany&file='.urlencode('/thumbs/'.$conf->global->SELLYOURSAAS_LOGO_MINI);

print '

    <nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand" href="#"><img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=mycompany&file='.urlencode('/thumbs/'.$conf->global->SELLYOURSAAS_LOGO_MINI).'" width="160px"></a>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item'.($mode == 'dashboard'?'active':'').'">
            <a class="nav-link" href="'.$_SERVER["PHP_SELF"].'?mode=dashboard">'.$langs->trans("Dashboard").'</a>
          </li>
          <li class="nav-item'.($mode == 'instances'?'active':'').'">
            <a class="nav-link" href="'.$_SERVER["PHP_SELF"].'?mode=instances">'.$langs->trans("MyInstances").'</a>
          </li>
          <li class="nav-item'.($mode == 'myaccount'?'active':'').'">
            <a class="nav-link" href="'.$_SERVER["PHP_SELF"].'?mode=myaccount">'.$langs->trans("MyAccount").'</a>
          </li>
          <li class="nav-item'.($mode == 'billing'?'active':'').'">
            <a class="nav-link" href="'.$_SERVER["PHP_SELF"].'?mode=billing">'.$langs->trans("Billing").'</a>
          </li>
          <!--
          <li class="nav-item'.($mode == 'dashboard'?'active':'').'">
            <a class="nav-link" href="#">Disabled</a>
          </li>-->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$langs->trans("Other").'</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">

	            <a class="dropdown-item" href="'.$_SERVER["PHP_SELF"].'?mode=support">'.$langs->trans("Support").'</a>
	            <a class="dropdown-item" href="https://www.dolicloud.com/en/faq" target="_new">'.$langs->trans("FAQs").'</a>
	            <a class="dropdown-item" href="'.$_SERVER["PHP_SELF"].'?mode=logout" target="_new">'.$langs->trans("Logout").'</a>

            </div>
          </li>
        </ul>
<!--
        <form class="form-inline my-2 my-md-0" action="'.$_SERVER["PHP_SELF"].'">
		<input type="hidden" name="mode" value="'.dol_escape_htmltag($mode).'">
          <input class="form-control mr-sm-2" type="text" placeholder="'.$langs->trans("Search").'">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">'.$langs->trans("Search").'</button>
        </form>
-->
      </div>
    </nav>


    <div class="container">
		<br>
';

var_dump($_SESSION["dol_loginsellyoursaas"]);
var_dump($user);

if ($welcomecid > 0)
{
	$contract = new Contrat($db);
	$contract->fetch($welcomecid);
	$contract->fetch_thirdparty();

	print '
      <div class="jumbotron">
        <div class="col-sm-8 mx-auto">


		<!-- BEGIN PAGE HEAD -->
		<div class="page-head">
		<!-- BEGIN PAGE TITLE -->
		<div class="page-title">
		<h1>'.$langs->trans("Welcome").'</h1>
		</div>
		<!-- END PAGE TITLE -->
		</div>
		<!-- END PAGE HEAD -->


		<!-- BEGIN PORTLET -->
		<div class="portletnoborder light">

		<div class="portlet-header">
		<div class="caption">
		<span class="caption-subject font-green-sharp bold uppercase">'.$langs->trans("InstallationComplete").'</span>
		</div>
		</div>';

	if (in_array($contract->thirdparty->country_code, array('aaa', 'bbb')))
	{
		print '
		<div class="portlet-body">
		<p>
		'.$langs->trans("YourCredentialToAccessYourInstanceHasBeenSentByEmail").'
		</p>

		</div>';
	}
	else
	{
	print '
		<div class="portlet-body">
		<p>
		'.$langs->trans("YouCanAccessYourInstance", $contract->array_options['options_plan']).'
		</p>
		<p class="well">
		'.$langs->trans("Url").': <a href="http://'.$contract->ref_customer.'" target="_blank">'.$contract->ref_customer.'</a>

		<br> '.$langs->trans("Username").': '.($_SESSION['initialappplogin']?$_SESSION['initialappplogin']:'NA').'
		<br> '.$langs->trans("Password").': '.($_SESSION['initialappppassword']?$_SESSION['initialappppassword']:'NA').'
		</p>
		<p>
		<a class="btn btn-primary" target="_blank" href="http://'.$contract->ref_customer.'">
		'.$langs->trans("TakeMeTo", $contract->array_options['options_plan']).'
		</a>
		</p>

		</div>';
	}

	print '
		</div> <!-- END PORTLET -->


        </div>
      </div>
	';
}
else	// Show warning
{

	print '
		<div class="note note-warning">
		<h4 class="block">'.$langs->trans("XDaysBeforeEndOfTrial", 99, 'aaa').' !</h4>
		<p>
		<a href="/customerUI/updatePaymentMethod" class="btn btn-warning">'.$langs->trans("AddAPaymentMode").'</a>
		</p>
		</div>
	';
}




if ($mode == 'dashboard')
{
	$nbofinstances = 0;
	$nboftickets = 0;

	print '
	<div class="page-content-wrapper">
			<div class="page-content">


	     <!-- BEGIN PAGE HEADER-->
	<!-- BEGIN PAGE HEAD -->
	<div class="page-head">
	  <!-- BEGIN PAGE TITLE -->
	<div class="page-title">
	  <h1>'.$langs->trans("Dashboard").'</h1>
	</div>
	<!-- END PAGE TITLE -->


	</div>
	<!-- END PAGE HEAD -->
	<!-- END PAGE HEADER-->


	    <div class="row">
	      <div class="col-md-6">

	        <div class="portlet light" id="planSection">

	          <div class="portlet-title">
	            <div class="caption">
	              <span class="caption-subject font-green-sharp bold uppercase">'.$langs->trans("MyInstances").'</span>
	            </div>
	          </div>

	          <div class="portlet-body">

	            <div class="row">

	              <div class="col-md-9">
					'.$langs->trans("NbOfInstances").'
	              </div><!-- END COL -->
	              <div class="col-md-3">
	                '.$nbofinstances.'
	              </div>
	            </div> <!-- END ROW -->

				<div class="row">
				<div class="center col-md-12"><br>
					<a href="'.$_SERVER["PHP_SELF"].'?mode=instances" class="btn default btn-xs green-stripe">
	            	'.$langs->trans("SeeDetailsAndOptions").'
	                </a>
				</div></div>

	          </div> <!-- END PORTLET-BODY -->

	        </div> <!-- END PORTLET -->

	      </div> <!-- END COL -->


	      <div class="col-md-6">
	        <div class="portlet light" id="paymentMethodSection">

	          <div class="portlet-title">
	            <div class="caption">
	              <span class="caption-subject font-green-sharp bold uppercase">'.$langs->trans("MyAccount").'</span>
	            </div>
	          </div>

	          <div class="portlet-body">
	            <p>

	                '.$langs->trans("ProfileIsComplete").'

	            </p>

				<div class="row">
				<div class="center col-md-12"><br>
					<a href="'.$_SERVER["PHP_SELF"].'?mode=myaccount" class="btn default btn-xs green-stripe">
	            	'.$langs->trans("SeeOrEditProfile").'
	                </a>
				</div></div>

	          </div> <!-- END PORTLET-BODY -->

	        </div> <!-- END PORTLET -->
	      </div><!-- END COL -->


	    </div> <!-- END ROW -->

	';

	print '
	    <div class="row">


	      <div class="col-md-6">
	        <div class="portlet light" id="paymentMethodSection">

	          <div class="portlet-title">
	            <div class="caption">
	              <span class="caption-subject font-green-sharp bold uppercase">'.$langs->trans("PaymentBalance").'</span>
	            </div>
	          </div>

	          <div class="portlet-body">
	            <p>

	                '.$langs->trans("UpToDate").'

	            </p>

				<div class="row">
				<div class="center col-md-12"><br>
					<a href="'.$_SERVER["PHP_SELF"].'?mode=billing" class="btn default btn-xs green-stripe">
	            	'.$langs->trans("SeeDetailsOfPayments").'
	                </a>
				</div></div>

	          </div> <!-- END PORTLET-BODY -->

	        </div> <!-- END PORTLET -->
	      </div><!-- END COL -->


	      <div class="col-md-6">
	        <div class="portlet light" id="paymentMethodSection">

	          <div class="portlet-title">
	            <div class="caption">
	              <span class="caption-subject font-green-sharp bold uppercase">'.$langs->trans("SupportTickets").'</span>
	            </div>
	          </div>

	          <div class="portlet-body">

	            <div class="row">

	              <div class="col-md-9">
					'.$langs->trans("NbOfTickets").'
	              </div><!-- END COL -->
	              <div class="col-md-3">
	                '.$nboftickets.'
	              </div>
	            </div> <!-- END ROW -->

				<div class="row">
				<div class="center col-md-12"><br>
					<a href="'.$_SERVER["PHP_SELF"].'?mode=support" class="btn default btn-xs green-stripe">
	            	'.$langs->trans("SeeDetailsOfTickets").'
	                </a>
				</div></div>

	          </div> <!-- END PORTLET-BODY -->

	        </div> <!-- END PORTLET -->
	      </div><!-- END COL -->

	    </div> <!-- END ROW -->
	';

	print '

		</div>


	    </div>
		</div>
	';
}

if ($mode == 'instances')
{
	print '
	<div class="page-content-wrapper">
			<div class="page-content">


	     <!-- BEGIN PAGE HEADER-->
	<!-- BEGIN PAGE HEAD -->
	<div class="page-head">
	  <!-- BEGIN PAGE TITLE -->
	<div class="page-title">
	  <h1>'.$langs->trans("MyInstances");
	  //print '<small>Review your billing history, adjust your subscription, update your payment method</small>';
	  print '</h1>
	</div>
	<!-- END PAGE TITLE -->


	</div>
	<!-- END PAGE HEAD -->
	<!-- END PAGE HEADER-->


	    <div class="row">
	      <div class="col-md-12">

			<div class="portlet light">

		      <div class="portlet-title">
		        <div class="caption">
		          <span class="caption-subject font-green-sharp bold uppercase">asoftingsas.on.dolicloud.com</span>
		          <span class="caption-helper">Dolibarr ERP-CRM</span>
		          <p style="margin-top:3px;font-size:0.8em;">
		            <span class="lowercase" style="color:green">DEPLOYED</span>
		            <small>13 déc. 2017</small>
		          </p>
		        </div>

		        <div class="tools">
		          <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
		        </div>

		      </div>


		      <div class="portlet-body" style="">

		        <div class="tabbable-custom nav-justified">
		          <ul class="nav nav-tabs nav-justified">
		            <li class="active"><a href="#tab_1_1_140211" data-toggle="tab">Domain</a></li>
		            <li><a href="#tab_1_1_240211" data-toggle="tab">App Resources</a></li>

		              <li><a href="#tab_1_1_340211" data-toggle="tab">SSH &amp; SFTP</a></li>
		              <li><a href="#tab_1_1_440211" data-toggle="tab">Database</a></li>

		            <li><a href="#tab_1_1_540211" data-toggle="tab">Danger Zone</a></li>
		          </ul>
		          <div class="tab-content">
		            <div class="tab-pane active" id="tab_1_1_140211">
		              <p>
		                </p><div class="form-group">
		                  <label>Domain</label>
		                  <input type="text" class="form-control input-xlarge" value="asoftingsas.on.dolicloud.com">
		                </div>
		                <a class="btn default change-domain-link" data-app-id="40211" data-app-ip="176.9.35.249" href="javascript:;">Change domain</a>
		              <p></p>
		            </div>
		            <div class="tab-pane" id="tab_1_1_240211">
		              <!-- STAT -->
		              <div class="row list-separated profile-stat">

		                <div class="col-md-2 col-sm-4 col-xs-6">
		                  <div class="uppercase profile-stat-title">
		                     1.00
		                  </div>
		                  <div class="uppercase profile-stat-text">
		                     Dolibarr Users
		                  </div>
		                </div>

		              </div>
		              <!-- END STAT -->
		            </div> <!-- END TABBED PANE -->


		              <div class="tab-pane" id="tab_1_1_340211">
		                <p>Secure FTP (SFTP) est un protocol simple et sécurisé pour accéder aux fichiers de votre instance (Par exemple par WinSCP ou FileZilla, des clients SFtp populaires pour Windows). Afin d accèder aux fichiers, vous aurez besoin des identifiants suivant:</p>
		                <form class="form-horizontal" role="form">
		                <div class="form-body">
		                  <div class="form-group">
		                    <label class="col-md-3 control-label">Hostname</label>
		                    <div class="col-md-9">
		                      <input type="text" class="form-control input-medium" value="asoftingsas.on.dolicloud.com">
		                    </div>
		                  </div>
		                  <div class="form-group">
		                    <label class="col-md-3 control-label">Port</label>
		                    <div class="col-md-9">
		                      <input type="text" class="form-control input-medium" value="">
		                    </div>
		                  </div>
		                  <div class="form-group">
		                    <label class="col-md-3 control-label">SFTP Username</label>
		                    <div class="col-md-9">
		                      <input type="text" class="form-control input-medium" value="ebwhlDU6CO">
		                    </div>
		                  </div>
		                  <div class="form-group">
		                    <label class="col-md-3 control-label">Password</label>
		                    <div class="col-md-9">
		                      <input type="text" class="form-control input-medium" value="LQFIhlhPG9">
		                    </div>
		                  </div>
		                </div>
		                </form>
		              </div> <!-- END TAB PANE -->

		              <div class="tab-pane" id="tab_1_1_440211">
		                <p>Vous pouvez accéder à la base de donnée avec tout logiciel d administration pour Mysql et les identifiants suivants:</p>
		                <form class="form-horizontal" role="form">
		                <div class="form-body">
		                  <div class="form-group">
		                    <label class="col-md-3 control-label">Hostname</label>
		                    <div class="col-md-9">
		                      <input type="text" class="form-control input-medium" value="176.9.35.249">
		                    </div>
		                  </div>
		                  <div class="form-group">
		                    <label class="col-md-3 control-label">Port</label>
		                    <div class="col-md-9">
		                      <input type="text" class="form-control input-medium" value="3306">
		                    </div>
		                  </div>
		                  <div class="form-group">
		                    <label class="col-md-3 control-label">Database Name</label>
		                    <div class="col-md-9">
		                      <input type="text" class="form-control input-medium" value="4xqLl6mVUcMgaav_dolibarr">
		                    </div>
		                  </div>
		                  <div class="form-group">
		                    <label class="col-md-3 control-label">Database Username</label>
		                    <div class="col-md-9">
		                      <input type="text" class="form-control input-medium" value="ebwql8hhcT">
		                    </div>
		                  </div>
		                  <div class="form-group">
		                    <label class="col-md-3 control-label">Password</label>
		                    <div class="col-md-9">
		                      <input type="text" class="form-control input-medium" value="CKWAa9GvjM">
		                    </div>
		                  </div>
		                </div>
		                </form>
		              </div> <!-- END TAB PANE -->

		            <div class="tab-pane" id="tab_1_1_540211">
		              <div class="alert alert-block alert-danger fade in">
		                <p>
		                  Please be certain. There is no undo. There is no going back!
		                </p>
		                <p>
		                  <a href="/customerUI/deleteAppInstance?appId=40211" class="btn btn-danger">Undeploy App Instance</a>
		                </p>
		              </div>
		            </div> <!-- END TAB PANE -->

		          </div> <!-- END TAB CONTENT -->
		        </div> <!-- END TABABLE CUSTOM-->

		      </div><!-- END PORTLET-BODY -->


			</div> <!-- END PORTLET -->



	      </div> <!-- END COL -->


	    </div> <!-- END ROW -->


	    </div>
		</div>
	';
}



if ($mode == 'myaccount')
{
	print '
	<div class="page-content-wrapper">
			<div class="page-content">


	     <!-- BEGIN PAGE HEADER-->
	<!-- BEGIN PAGE HEAD -->
	<div class="page-head">
	  <!-- BEGIN PAGE TITLE -->
	<div class="page-title">
	  <h1>'.$langs->trans("MyAccount").' <small>'.$langs->trans("YourPersonalInformation").'</small></h1>
	</div>
	<!-- END PAGE TITLE -->


	</div>
	<!-- END PAGE HEAD -->
	<!-- END PAGE HEADER-->


	    <div class="row">
	      <div class="col-md-6">

	        <div class="portlet light">
          <div class="portlet-title">
            <div class="caption-subject font-green-sharp bold uppercase">'.$langs->trans("Organization").'</div>
          </div>
          <div class="portlet-body">
            <form action="'.$_SERVER["PHP_SELF"].'" method="post">
			<input type="hidden" name="mode" value="'.dol_escape_htmltag($mode).'">
              <div class="form-body">

                <div class="form-group">
                  <label>'.$langs->trans("NameOfCompany").'</label>
                  <input type="text" class="form-control" placeholder="name of your organization" value="Bobolink" name="orgName">
                </div>

                <div class="form-group">
                  <label>'.$langs->trans("AddressLine").' 1</label>
                  <input type="text" class="form-control" placeholder="'.$langs->trans("HouseNumberAndStreet").'" value="" name="address.addressLine1">
                </div>
                <div class="form-group">
                  <label>'.$langs->trans("AddressLine").' 2</label>
                  <input type="text" class="form-control" value="" name="address.addressLine2">
                </div>
                <div class="form-group">
                  <label>'.$langs->trans("Town").'</label>
                  <input type="text" class="form-control" value="" name="address.city">
                </div>
                <div class="form-group">
                  <label>'.$langs->trans("Zip").'</label>
                  <input type="text" class="form-control input-small" value="" name="address.zip">
                </div>
                <div class="form-group">
                  <label>'.$langs->trans("State").'</label>
                  <input type="text" class="form-control" placeholder="'.$langs->trans("StateOrCounty").'" value="">
                </div>
                <div class="form-group">
                  <label>'.$langs->trans("Country").'</label>
';
print $form->select_country($countryselected, 'address_country', 'optionsValue="name"', 0, 'form-control minwidth300', 'code2');
print '
                </div>
                <div class="form-group">
                  <label>'.$langs->trans("VATIntra").'</label>
                  <input type="text" class="form-control input-small" value="" name="taxIdentificationNumber">
                </div>
              </div>
              <!-- END FORM BODY -->

              <div>
                <input type="submit" name="submit" value="'.$langs->trans("Save").'" class="btn green-haze btn-circle">
              </div>

            </form>
            <!-- END FORM DIV -->
          </div> <!-- END PORTLET-BODY -->
        </div>




	      </div> <!-- END COL -->

	      <div class="col-md-6">

			<div class="portlet light">
	          <div class="portlet-title">
	            <div class="caption-subject font-green-sharp bold uppercase">'.$langs->trans("YourAdminAccount").'</div>
	          </div>
	          <div class="portlet-body">
	            <form action="'.$_SERVER["PHP_SELF"].'" method="post">
				<input type="hidden" name="mode" value="'.dol_escape_htmltag($mode).'">
	              <div class="form-body">
	                <div class="row">
	                  <div class="col-md-6">
	                    <div class="form-group">
	                      <label>'.$langs->trans("Firstname").'</label>
	                      <input type="text" class="form-control" value="'.$user->firstname.'" name="firstName">
	                    </div>
	                  </div>
	                  <div class="col-md-6">
	                    <div class="form-group">
	                      <label>'.$langs->trans("Lastname").'</label>
	                      <input type="text" class="form-control" value="'.$user->lastname.'" name="lastName">
	                    </div>
	                  </div>
	                </div>
	                <div class="form-group">
	                  <label>'.$langs->trans("Email").'</label>
	                  <input type="text" class="form-control" value="'.$user->email.'" name="email">
	                </div>
	              </div>
	              <div>
	                <input type="submit" name="submit" value="'.$langs->trans("Save").'" class="btn green-haze btn-circle">
	              </div>
	            </form>
	          </div>
	        </div>


			<div class="portlet light">
	          <div class="portlet-title">
	            <div class="caption-subject font-green-sharp bold uppercase">'.$langs->trans("Password").'</div>
	          </div>
	          <div class="portlet-body">
	            <form action="'.$_SERVER["PHP_SELF"].'" method="post">
				<input type="hidden" name="mode" value="'.dol_escape_htmltag($mode).'">
	              <div class="form-body">
	                <div class="form-group">
	                  <label>'.$langs->trans("Password").'</label>
	                  <input type="password" class="form-control" name="password">
	                </div>
	                <div class="form-group">
	                  <label>'.$langs->trans("RepeatPassword").'</label>
	                  <input type="password" class="form-control" name="password2">
	                </div>
	              </div>
	              <div>
	                <input type="submit" name="submit" value="'.$langs->trans("ChangePassword").'" class="btn green-haze btn-circle">
	              </div>
	            </form>
	          </div>
	        </div>
	      </div><!-- END COL -->

	    </div> <!-- END ROW -->


	    </div>
		</div>
	';
}



if ($mode == 'billing')
{
	print '
	<div class="page-content-wrapper">
			<div class="page-content">


	     <!-- BEGIN PAGE HEADER-->
	<!-- BEGIN PAGE HEAD -->
	<div class="page-head">
	  <!-- BEGIN PAGE TITLE -->
	<div class="page-title">
	  <h1>'.$langs->trans("Billing").' <small>'.$langs->trans("BillingDesc").'</small></h1>
	</div>
	<!-- END PAGE TITLE -->


	</div>
	<!-- END PAGE HEAD -->
	<!-- END PAGE HEADER-->






	    <div class="row">
	      <div class="col-md-9">

	        <div class="portlet light" id="planSection">

	          <div class="portlet-title">
	            <div class="caption">
	              <span class="caption-subject font-green-sharp bold uppercase">'.$langs->trans("MyInstances").'</span>
	            </div>
	          </div>

	          <div class="portlet-body">


	            <div class="row">

	              <div class="col-md-9">
	                Dolibarr ERP &amp; CRM Basic <br>
	                Basic version - Support limited to migration <br>
	              </div><!-- END COL -->
	              <div class="col-md-3">
	                9,00 € / User / Month
	              </div>
	            </div> <!-- END ROW -->

	            <div class="row" style="margin-top:20px">

	              <div class="col-md-3">
	                <a href="/customerUI/changePlanForSubscription" class="btn default btn-xs green-stripe">
	                  Modifier formule
	                </a>
	              </div><!-- END COL -->


	              <div class="col-md-3">
	                <a href="/customerUI/requestAccountClosure" class="btn default btn-xs red-stripe">
	                  Fermet et détruire l instance
	                </a>
	              </div>

	            </div>



	          </div> <!-- END PORTLET-BODY -->

	        </div> <!-- END PORTLET -->



	      </div> <!-- END COL -->

	      <div class="col-md-3">
	        <div class="portlet light" id="paymentMethodSection">

	          <div class="portlet-title">
	            <div class="caption">
	              <i class="icon-credit-card font-green-sharp"></i>
	              <span class="caption-subject font-green-sharp bold uppercase">Payment Method</span>
	            </div>
	          </div>

	          <div class="portlet-body">
	            <p>

	                No payment method on file.
	                <br><br>
	                <a href="/customerUI/updatePaymentMethod" class="btn default btn-xs green-stripe">Add Payment Method</a>

	            </p>
	          </div> <!-- END PORTLET-BODY -->

	        </div> <!-- END PORTLET -->
	      </div><!-- END COL -->

	    </div> <!-- END ROW -->


	    </div>
		</div>
	';
}


if ($mode == 'support')
{
	print 'Soon, follow your support ticket here...';


}


print '
	</div>






	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<!--
	<script src="dist/js/tether.min.js"></script>
	<script src="dist/js/bootstrap.min.js"></script>
	-->

	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.13.0/umd/popper.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>

	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	<!-- <script src="/assets/js/ie10-viewport-bug-workaround.js"></script> -->

	</body>
</html>
';

$db->close();
