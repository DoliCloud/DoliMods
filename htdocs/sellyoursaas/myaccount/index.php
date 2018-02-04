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
dol_include_once('/sellyoursaas/class/packages.class.php');

$conf->global->SYSLOG_FILE_ONEPERSESSION=1;

$welcomecid = GETPOST('welcomecid','alpha');
$mode = GETPOST('mode', 'alpha');
if (empty($mode) && empty($welcomecid)) $mode='dashboard';

$langs=new Translate('', $conf);
$langs->setDefaultLang('auto');

$langs->loadLangs(array("main","companies","bills","sellyoursaas@sellyoursaas"));




/*
 * Action
 */

if ($mode == 'logout')
{
	session_destroy();
	header("Location: /index.php");
	exit;
}


/*
 * View
 */

$form = new Form($db);


$socid = $_SESSION['dol_loginsellyoursaas'];

$listofcontractid = array();
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
$documentstatic=new Contrat($db);
$documentstaticline=new ContratLigne($db);
$sql = 'SELECT c.rowid as rowid';
$sql.= ' FROM '.MAIN_DB_PREFIX.'contrat as c LEFT JOIN '.MAIN_DB_PREFIX.'contrat_extrafields as ce ON ce.fk_object = c.rowid, '.MAIN_DB_PREFIX.'contratdet as d, '.MAIN_DB_PREFIX.'societe as s';
$sql.= " WHERE c.fk_soc = s.rowid AND s.rowid = ".$socid;
$sql.= " AND d.fk_contrat = c.rowid";
$sql.= " AND c.entity = ".$conf->entity;
$sql.= " AND ce.deployment_status IN ('processing', 'done')";

$resql=$db->query($sql);
if ($resql)
{
	$num_rows = $db->num_rows($resql);
	$i = 0;
	while ($i < $num_rows)
	{
		$obj = $db->fetch_object($resql);
		if ($obj)
		{
			$contract=new Contrat($db);
			$contract->fetch($obj->rowid);
			$listofcontractid[$obj->rowid]=$contract;
		}
		$i++;
	}
}
else
{
	setEventMessages($db->lasterror(), null, 'errors');
}
if ($welcomecid > 0)
{
	$contract=new Contrat($db);
	$contract->fetch($welcomecid);
	$listofcontractid[$welcomecid]=$contract;
}
//var_dump($listofcontractid);


$head='<link rel="icon" href="img/favicon.ico">
<!-- Bootstrap core CSS -->
<!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.css" rel="stylesheet">-->
<link href="dist/css/bootstrap.css" rel="stylesheet">
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


//$website = new Website($db);
//$website->fetch(0, 'sellyoursaas');


llxHeader($head, $langs->trans("MyAccount"));

$linklogo = DOL_URL_ROOT.'/viewimage.php?modulepart=mycompany&file='.urlencode('/thumbs/'.$conf->global->SELLYOURSAAS_LOGO_MINI);

print '
    <nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbars" aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand" href="#"><img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=mycompany&file='.urlencode('/thumbs/'.$conf->global->SELLYOURSAAS_LOGO_MINI).'" height="48px"></a>

      <div class="collapse navbar-collapse" id="navbars">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item'.($mode == 'dashboard'?' active':'').'">
            <a class="nav-link" href="'.$_SERVER["PHP_SELF"].'?mode=dashboard"><i class="fa fa-tachometer"></i> '.$langs->trans("Dashboard").'</a>
          </li>
          <li class="nav-item'.($mode == 'instances'?' active':'').'">
            <a class="nav-link" href="'.$_SERVER["PHP_SELF"].'?mode=instances"><i class="fa fa-server"></i> '.$langs->trans("MyInstances").'</a>
          </li>
          <li class="nav-item'.($mode == 'billing'?' active':'').'">
            <a class="nav-link" href="'.$_SERVER["PHP_SELF"].'?mode=billing"><i class="fa fa-usd"></i> '.$langs->trans("Billing").'</a>
          </li>

          <li class="nav-item'.($mode == 'support'?' active':'').' dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-gear"></i> '.$langs->trans("Other").'</a>
            <ul class="dropdown-menu">
	            <li><a class="dropdown-item" href="'.$_SERVER["PHP_SELF"].'?mode=support">'.$langs->trans("Support").'</a></li>
                <li class="dropdown-divider"></li>
	            <li><a class="dropdown-item" href="https://www.dolicloud.com/en/faq" target="_newfaq">'.$langs->trans("FAQs").'</a></li>
            </ul>
          </li>

          <li class="nav-item'.($mode == 'myaccount'?' active':'').' dropdown">
             <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-user"></i> '.$langs->trans("MyAccount").'</a>
             <ul class="dropdown-menu">
                 <li><a class="dropdown-item" href="'.$_SERVER["PHP_SELF"].'?mode=myaccount"><i class="fa fa-user"></i> '.$langs->trans("MyAccount").'</a></li>
                 <li class="dropdown-divider"></li>
                 <li><a class="dropdown-item" href="'.$_SERVER["PHP_SELF"].'?mode=logout"><i class="fa fa-sign-out"></i> '.$langs->trans("Logout").'</a></li>
             </ul>
           </li>

        </ul>

		<!--
        <form class="form-inline my-2 my-md-0" action="'.$_SERVER["PHP_SELF"].'">
		<input type="hidden" name="mode" value="'.dol_escape_htmltag($mode).'">
          <input class="form-control mr-sm-2" type="text" placeholder="'.$langs->trans("Search").'">
          <button class="btn-transparent nav-link" type="submit"><i class="fa fa-search"></i></button>
        </form>
		-->

      </div>
    </nav>
';


print '
    <div class="container">
		<br>
';


//var_dump($_SESSION["dol_loginsellyoursaas"]);
//var_dump($user);


// Special case - when coming from a specific contract id $welcomid
if ($welcomecid > 0)
{
	$contract = $listofcontractid[$welcomecid];
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


if (! empty($conf->global->SELLYOURSAAS_ANNOUNCE))	// Show warning
{

	print '
		<div class="note note-warning">
		<h4 class="block">'.$langs->trans($conf->global->SELLYOURSAAS_ANNOUNCE).'</h4>
		</div>
	';
}


if (1 == 1)	// Show warning
{
	foreach ($listofcontractid as $contractid => $contract)
	{
		if ($contract->array_options['options_date_endfreeperiod'] > 0)
		{
			$dateendfreeperiod = $contract->array_options['options_date_endfreeperiod'];
			if (! is_numeric($dateendfreeperiod)) $dateendfreeperiod = dol_stringtotime($dateendfreeperiod);
			$delaybeforeendoftrial = ($dateendfreeperiod - dol_now());

			// TODO Test if a payment method exists = a recurring invoice exists.


			if ($delaybeforeendoftrial > 0)
			{
				$delayindays = round($delaybeforeendoftrial / 3600 / 24);

				$firstline = reset($contract->lines);
				print '
					<div class="note note-warning">
					<h4 class="block">'.$langs->trans("XDaysBeforeEndOfTrial", abs($delayindays), $contract->ref_customer).' !</h4>
					<p>
					<a href="/customerUI/updatePaymentMethod" class="btn btn-warning">'.$langs->trans("AddAPaymentMode").'</a>
					</p>
					</div>
				';
			}
			else
			{
				$delayindays = round($delaybeforeendoftrial / 3600 / 24);

				$firstline = reset($contract->lines);
				print '
					<div class="note note-warning">
					<h4 class="block">'.$langs->trans("XDaysAfterEndOfTrial", abs($delayindays), $contract->ref_customer).' !</h4>
					<p>
					<a href="/customerUI/updatePaymentMethod" class="btn btn-warning">'.$langs->trans("AddAPaymentModeToRestoreInstance").'</a>
					</p>
					</div>
				';
			}
		}
	}
}



if ($mode == 'dashboard')
{
	$nbofinstances = count($listofcontractid);
	$nboftickets = $langs->trans("SoonAvailable");

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
	                <h2>'.$nbofinstances.'</h2>
	              </div>
	            </div> <!-- END ROW -->

				<div class="row">
				<div class="center col-md-12">
					<br>
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
				<div class="row">
				<div class="col-md-12">
	                '.$langs->trans("ProfileIsComplete").'
	            </div>
				</div>

				<div class="row">
				<div class="center col-md-12">
					<br>
					<a href="'.$_SERVER["PHP_SELF"].'?mode=myaccount" class="btn default btn-xs green-stripe">
	            	'.$langs->trans("SeeOrEditProfile").'
	                </a>
				</div>
				</div>

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
				<div class="row">
				<div class="col-md-12">
	                '.$langs->trans("UpToDate").'
				</div>
	            </div>
				<div class="row">
				<div class="center col-md-12">
					<br>
					<a href="'.$_SERVER["PHP_SELF"].'?mode=billing" class="btn default btn-xs green-stripe">
	            	'.$langs->trans("SeeDetailsOfPayments").'
	                </a>
				</div>
				</div>

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
				<div class="center col-md-12">
					<br>
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
	  <h1>'.$langs->trans("MyInstances").'</h1>
	</div>
	<!-- END PAGE TITLE -->


	</div>
	<!-- END PAGE HEAD -->
	<!-- END PAGE HEADER-->';

	foreach ($listofcontractid as $id => $contract)
	{
		$planref = $contract->array_options['options_plan'];
		$statuslabel = $contract->array_options['options_deployment_status'];
		$instancename = preg_replace('/\..*$/', '', $contract->ref_customer);

		$package = new Packages($db);
		$package->fetch(0, $planref);

		print '
		    <div class="row">
		      <div class="col-md-12">

				<div class="portlet light">

			      <div class="portlet-title">
			        <div class="caption">
			          <span class="caption-subject font-green-sharp bold uppercase">'.$instancename.'</span>
			          <span class="caption-helper"> - '.$package->label.'</span>
			          <p style="margin-top:3px;font-size:0.8em;">
			            <span class="caption-helper">'.$langs->trans("ID").' : '.$contract->ref.'</span><br>
			            <span class="caption-helper">'.$langs->trans("Status").' : <span class="bold uppercase" style="color:green">'.$statuslabel.'</span></span><br>
			            <span>';
		if ($contract->array_options['options_deployment_status'] == 'processing')
		{
			print $langs->trans("DateStart").' : <span class="bold">'.dol_print_date($contract->array_options['options_deployment_date_start'], 'dayhour').'</span>';
		}
		if ($contract->array_options['options_deployment_status'] == 'deployed')
		{
			print $langs->trans("Date").' : <span class="bold">'.dol_print_date($contract->array_options['options_deployment_end_start'], 'dayhour').'</span>';
		}

		print          '</span>
			          </p>
			        </div>

			        <div class="tools">
			          <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
			        </div>

			      </div>


			      <div class="portlet-body" style="">

			        <div class="tabbable-custom nav-justified">
			          <ul class="nav nav-tabs nav-justified">
			            <li><a href="#tab_domain_'.$contract->id.'" data-toggle="tab">Domain</a></li>
			            <li><a href="#tab_resource_'.$contract->id.'" data-toggle="tab">App Resources</a></li>
			            <li><a href="#tab_ssh_'.$contract->id.'" data-toggle="tab">'.$langs->trans("SSH").' / '.$langs->trans("SFTP").'</a></li>
			            <li><a href="#tab_db_'.$contract->id.'" data-toggle="tab">'.$langs->trans("Database").'</a></li>
			            <li><a href="#tab_danger_'.$contract->id.'" data-toggle="tab">'.$langs->trans("DangerZone").'</a></li>
			          </ul>

			          <div class="tab-content">
			            <div class="tab-pane active" id="tab_domain_'.$contract->id.'">
			              <div class="form-group">
			                  '.$langs->trans("URL").' <input type="text" class="" value="'.$contract->ref_customer.'">
			              </div>
			                <!--<a class="btn default change-domain-link" data-app-id="40211" data-app-ip="176.9.35.249" href="javascript:;">Change domain</a>-->
			            </div>
			            <div class="tab-pane" id="tab_resource_'.$contract->id.'">
			              <!-- STAT -->
			              <div class="">
			                  <div class="uppercase profile-stat-text">'.$langs->trans("Users").'</div>
			                  <div class="uppercase profile-stat-title">
			                     1.00
			                  </div>
			              </div>
			              <!-- END STAT -->
			            </div> <!-- END TABBED PANE -->


			              <div class="tab-pane" id="tab_ssh_'.$contract->id.'">
			                <p>Secure FTP (SFTP) est un protocol simple et sécurisé pour accéder aux fichiers de votre instance (Par exemple par WinSCP ou FileZilla, des clients SFtp populaires pour Windows). Afin d accèder aux fichiers, vous aurez besoin des identifiants suivant:</p>
			                <form class="form-horizontal" role="form">
			                <div class="form-body">
			                  <div class="form-group">
			                    <label class="col-md-3 control-label">'.$langs->trans("Hostname").'</label>
			                    <div class="col-md-9">
			                      <input type="text" class="form-control input-medium" value="'.$contract->array_options['options_hostname_os'].'">
			                    </div>
			                  </div>
			                  <div class="form-group">
			                    <label class="col-md-3 control-label">'.$langs->trans("Port").'</label>
			                    <div class="col-md-9">
			                      <input type="text" class="form-control input-medium" value="'.($contract->array_options['options_port_os']?$contract->array_options['options_port_os']:22).'">
			                    </div>
			                  </div>
			                  <div class="form-group">
			                    <label class="col-md-3 control-label">'.$langs->trans("SFTP Username").'</label>
			                    <div class="col-md-9">
			                      <input type="text" class="form-control input-medium" value="'.$contract->array_options['options_username_os'].'">
			                    </div>
			                  </div>
			                  <div class="form-group">
			                    <label class="col-md-3 control-label">'.$langs->trans("Password").'</label>
			                    <div class="col-md-9">
			                      <input type="text" class="form-control input-medium" value="'.$contract->array_options['options_password_os'].'">
			                    </div>
			                  </div>
			                </div>
			                </form>
			              </div> <!-- END TAB PANE -->

			              <div class="tab-pane" id="tab_db_'.$contract->id.'">
			                <p>Vous pouvez accéder à la base de donnée avec tout logiciel d administration pour Mysql et les identifiants suivants:</p>
			                <form class="form-horizontal" role="form">
			                <div class="form-body">
			                  <div class="form-group">
			                    <label class="col-md-3 control-label">'.$langs->trans("Hostname").'</label>
			                    <div class="col-md-9">
			                      <input type="text" class="form-control input-medium" value="'.$contract->array_options['options_database_db'].'">
			                    </div>
			                  </div>
			                  <div class="form-group">
			                    <label class="col-md-3 control-label">'.$langs->trans("Port").'</label>
			                    <div class="col-md-9">
			                      <input type="text" class="form-control input-medium" value="'.$contract->array_options['options_port_db'].'">
			                    </div>
			                  </div>
			                  <div class="form-group">
			                    <label class="col-md-3 control-label">'.$langs->trans("DatabaseName").'</label>
			                    <div class="col-md-9">
			                      <input type="text" class="form-control input-medium" value="'.$contract->array_options['options_database_db'].'">
			                    </div>
			                  </div>
			                  <div class="form-group">
			                    <label class="col-md-3 control-label">'.$langs->trans("DatabaseLogin").'</label>
			                    <div class="col-md-9">
			                      <input type="text" class="form-control input-medium" value="'.$contract->array_options['options_username_db'].'">
			                    </div>
			                  </div>
			                  <div class="form-group">
			                    <label class="col-md-3 control-label">'.$langs->trans("Password").'</label>
			                    <div class="col-md-9">
			                      <input type="text" class="form-control input-medium" value="'.$contract->array_options['options_password_db'].'">
			                    </div>
			                  </div>
			                </div>
			                </form>
			              </div> <!-- END TAB PANE -->

			            <div class="tab-pane" id="tab_danger_'.$contract->id.'">
			              <div class="">
			                <div>
			                    '.$langs->trans("PleaseBeSure").'
								<br><br>
			                  <a href="/customerUI/deleteAppInstance?appId=40211" class="btn btn-danger">'.$langs->trans("UndeployInstance").'</a>
			                </div>
			              </div>
			            </div> <!-- END TAB PANE -->

			          </div> <!-- END TAB CONTENT -->
			        </div> <!-- END TABABLE CUSTOM-->

			      </div><!-- END PORTLET-BODY -->


				</div> <!-- END PORTLET -->



		      </div> <!-- END COL -->


		    </div> <!-- END ROW -->
		';
	}

	print '
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
	<script src="dist/js/tether.min.js"></script>
	<script src="dist/js/popper.min.js"></script>
	<script src="dist/js/bootstrap.min.js"></script>
	<!--
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.13.0/umd/popper.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
	-->

	</body>
</html>
';

llxFooter();

$db->close();
