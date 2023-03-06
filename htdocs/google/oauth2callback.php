<?php
// Tutorial: http://25labs.com/import-gmail-or-google-contacts-using-google-contacts-data-api-3-0-and-oauth-2-0-in-php/
// Tutorial: https://developers.google.com/google-apps/contacts/v3/


// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/geturl.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/lib/json.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

require_once DOL_DOCUMENT_ROOT.'/includes/OAuth/bootstrap.php';
use OAuth\Common\Storage\DoliStorage;
use OAuth\Common\Consumer\Credentials;

dol_include_once("/google/lib/google.lib.php");


// You must allow Dolibarr to login to


$client_id = getDolGlobalString('OAUTH_GOOGLE-CONTACT_ID');
$client_secret = getDolGlobalString('OAUTH_GOOGLE-CONTACT_SECRET');
$client_login = getDolGlobalString('OAUTH_GOOGLE-CONTACT_LOGIN');
$shortscope = getDolGlobalString('OAUTH_GOOGLE-CONTACT_SCOPE');
$redirect_uri=dol_buildpath('/google/oauth2callback.php', ((float) DOL_VERSION >= 4.0)?3:2);




$action = GETPOST('action');
$code = GETPOST('code');
$state = GETPOST('state');
$scope = GETPOST('scope');
$backtourl = GETPOST('backtourl') ? GETPOST('backtourl') : $_SESSION['backtourlsavedbeforeoauthjump'];
$servicename = GETPOSTISSET('servicename') ? GETPOST('servicename') : $_SESSION['servicenamesavedbeforeoauthjump'];
$mesgs = '';



$serviceFactory = new \OAuth\ServiceFactory();
$httpClient = new \OAuth\Common\Http\Client\CurlClient();
$serviceFactory->setHttpClient($httpClient);

// Setup the credentials for the requests
$keyforparamid = 'OAUTH_GOOGLE-CONTACT_ID';
$keyforparamsecret = 'OAUTH_GOOGLE-CONTACT_SECRET';
$credentials = new Credentials(
	$client_id,
	$client_secret,
	$redirect_uri
);

if (empty($state)) {
	print 'Error, parameter state is not defined';
	exit;
}

$storage = new DoliStorage($db, $conf, $servicename);
$apiService = $serviceFactory->createService('Google', $credentials, $storage, explode(',', $shortscope));
$apiService->setAccessType('offline');

/*
* Actions
*/


if ($action == 'delete') {
	$langs->load("oauth");

	$storage->clearToken('Google');
	unset($_SESSION['google_web_token_'.$conf->entity]);
	setEventMessages($langs->trans('TokenDeleted'), null, 'mesgs');
	header('Location: '.$backtourl);
	exit();
} elseif (!$code) {	// If we are coming from Google contact admin page, we must ask a token
	$_SESSION['backtourlsavedbeforeoauthjump'] = $backtourl;
	$_SESSION['servicenamesavedbeforeoauthjump'] = $servicename;
	$_SESSION['shortscopesavedbeforeoauthjump'] = $shortscope;
	$_SESSION['oauthstateanticsrf'] = $state;

	$apiService->setApprouvalPrompt('force');

	if ($state) {
		$url = $apiService->getAuthorizationUri(array('state' => $state));
	} else {
		$url = $apiService->getAuthorizationUri(); // Parameter state will be randomly generated
	}

	header("Location: ".$url);
	exit();
}	else {
	// Save token into database
	$langs->load("oauth");

	if (isset($_SESSION['oauthstateanticsrf']) && $state != $_SESSION['oauthstateanticsrf']) {
		print 'Value for state = '.dol_escape_htmltag($state).' differs from value in $_SESSION["oauthstateanticsrf"]. Code is refused.';
		unset($_SESSION['oauthstateanticsrf']);

	} else {
		try {
			// Save token into database using DoliStorage
			$tokenobj = $apiService->requestAccessToken($code, $state);

			$_SESSION['google_web_token_'.$conf->entity] = array('access_token' => $tokenobj->getAccessToken(),
			'expires_in' => ($tokenobj->getEndOfLife() - time()),
			'created' => ($tokenobj->getEndOfLife() - 3600)
		);

			setEventMessages($langs->trans('NewTokenStored'), null, 'mesgs');

			// Redirect to original page
			header('Location: '.$backtourl);
			exit();

		}	catch (Exception $e) {
			$error++;
			$mesgs.= $e->getMessage();
		}
	}
}




// After this, should never be used, except for test purpose or if error
// ---------------------------------------------------------------------

/*
 * View
 */

$google_web_token = $_SESSION['google_web_token_'.$conf->entity];

$max_results = 10;

llxHeader();

print $mesgs;


// Get contacts using oauth
/*
 print 'This page is a test page show information on your Google account using Oauth login. It is not used by module.<br>';
print '<a href="'.$url.'">Click here to authenticate using oauth</a><br><br>';

//var_dump($_GET);
//var_dump($_POST);

print 'We are into an oauth authenticated session oauth_token='.$google_web_token.'<br>';

// Ask API
$url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results='.$max_results.'&oauth_token='.$google_web_token;
$xmlresponse =  curl_file_get_contents($url);
if((strlen(stristr($xmlresponse,'Authorization required'))>0) && (strlen(stristr($xmlresponse,'Error '))>0)) //At times you get Authorization error from Google.
{
echo "<h2>OOPS !! Something went wrong. Please try reloading the page.</h2>";
exit();
}

echo "<h3>Addresses:</h3>";
//var_dump($xmlresponse);

//$xml =  new SimpleXMLElement($xmlresponse);
//$xml->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
//$result = $xml->xpath('//gd:email');

//foreach ($result as $title) { echo $title->attributes()->address . "<br>"; }
*/


// Create a contact using oauth
/*$urltocreate='https://www.google.com/m8/feeds/contacts/testapi@testapi.com/full&oauth_token='.$google_web_token;
 $curl = curl_init();
curl_setopt($curl,CURLOPT_URL,$urltocreate);
curl_setopt($curl,CURLOPT_POST,0);
//curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
//turning off the server and peer verification(TrustManager Concept).
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
$result = curl_exec($curl);
curl_close($curl);
var_dump($result);
*/


llxFooter();

$db->close();
