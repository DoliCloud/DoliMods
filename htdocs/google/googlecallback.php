<?php
// Tutorial: http://25labs.com/import-gmail-or-google-contacts-using-google-contacts-data-api-3-0-and-oauth-2-0-in-php/

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../dolibarr/htdocs/main.inc.php");     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include("../../../../../dolibarr/htdocs/main.inc.php");   // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');
dol_include_once("/google/lib/google.lib.php");

// You must allow Dolibarr to login to
$client_id='258042696143.apps.googleusercontent.com';
$client_secret='HdmLOMStzB9MBbAjCr87gz27';
//$redirect_uri='http://localhost/dolibarrnew/custom/google/googlecallback.php?action=xxx'; // Does not work. Must be exact url
$redirect_uri='http://localhost/dolibarrnew/custom/google/googlecallback.php';
$max_results = 10;

$auth_code = GETPOST("code");

$url='https://accounts.google.com/o/oauth2/auth?client_id='.$client_id.'&redirect_uri='.urlencode($redirect_uri).'&scope=https://www.google.com/m8/feeds/&response_type=code';


/*
 * Actions
 */

// Ask token (possible only if inside an oauth google session)
if ($auth_code || empty($_SESSION['google_oauth_token']))		// We come from a redirect of Google auth page or oauth_token empty
{
	if (empty($auth_code))	// If we are not coming from oauth page, we make a redirect to it
	{
		//print 'We are not coming from an oauth page, we redirect to it';
		header("Location: ".$url);
		exit;
	}

	$fields=array(
	'code'=>  urlencode($auth_code),
	'client_id'=>  urlencode($client_id),
	'client_secret'=>  urlencode($client_secret),
	'redirect_uri'=>  urlencode($redirect_uri),
	'grant_type'=>  urlencode('authorization_code')
	);
	$post = '';
	foreach($fields as $key=>$value) {
		$post .= $key.'='.$value.'&';
	}
	$post = rtrim($post,'&');

	$curl = curl_init();
	curl_setopt($curl,CURLOPT_URL,'https://accounts.google.com/o/oauth2/token');
	curl_setopt($curl,CURLOPT_POST,5);
	curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
	$result = curl_exec($curl);
	curl_close($curl);

	$response =  json_decode($result);

	$_SESSION['google_oauth_token']=$response->access_token;
}


$oauth_token = $_SESSION['google_oauth_token'];





/*
 * View
 */

llxHeader();



// Get contacts using oauth
/*
print 'This page is a test page show information on your Google account using Oauth login. It is not used by module.<br>';
print '<a href="'.$url.'">Click here to authenticate using oauth</a><br><br>';

//var_dump($_GET);
//var_dump($_POST);

print 'We are into an oauth authenticated session oauth_token='.$oauth_token.'<br>';

// Ask API
$url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results='.$max_results.'&oauth_token='.$oauth_token;
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



print "\n".'<br><br>Test contact interface<br>'."\n";

$path = dol_buildpath('/google/includes/zendgdata');
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

/**
 * @see Zend_Loader
 */
require_once('Zend/Loader.php');
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_HttpClient');
Zend_Loader::loadClass('Zend_Gdata_Contacts');
Zend_Loader::loadClass('Zend_Gdata_Query');
$user = $conf->global->GOOGLE_LOGIN;
$pwd = $conf->global->GOOGLE_PASSWORD;
$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pwd, 'cp');
$gdata = new Zend_Gdata_Contacts($client);
$gdata->setMaxResults($max_results);
$gdata->setStartIndex(0);
$feed = $gdata->getContactListFeed();
$entries = $feed->getEntry();
foreach($entries as $entry)
{
	var_dump($entry->toArray());
	print "-------------<br><br>\n";
}


//$client->setHeaders('If-Match: *');
//$gdata = new Zend_Gdata($client);
//$gdata->setMajorProtocolVersion(3);
//$gdata->delete($id);


// Get from an id
$query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/eldy10%40gmail.com/base/68d4e5083cc819');
$entry = $gdata->getEntry($query,'Zend_Gdata_Contacts_ListEntry');
var_dump($entry->toArray());


// Create a contact using oauth
/*$urltocreate='https://www.google.com/m8/feeds/contacts/testapi@testapi.com/full&oauth_token='.$oauth_token;
$curl = curl_init();
curl_setopt($curl,CURLOPT_URL,$urltocreate);
curl_setopt($curl,CURLOPT_POST,0);
//curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
$result = curl_exec($curl);
curl_close($curl);
var_dump($result);
*/


llxFooter();

$db->close();



function curl_file_get_contents($url)
{
	$curl = curl_init();
	$userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

	curl_setopt($curl,CURLOPT_URL,$url);	//The URL to fetch. This can also be set when initializing a session with curl_init().
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);	//TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
	curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,5);	//The number of seconds to wait while trying to connect.

	curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);	//The contents of the "User-Agent: " header to be used in a HTTP request.
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);	//To follow any "Location: " header that the server sends as part of the HTTP header.
	curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);	//To automatically set the Referer: field in requests where it follows a Location: redirect.
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);	//The maximum number of seconds to allow cURL functions to execute.
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);	//To stop cURL from verifying the peer's certificate.

	$contents = curl_exec($curl);
	curl_close($curl);
	return $contents;
}

?>