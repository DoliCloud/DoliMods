<?php
// Tutorial: http://25labs.com/import-gmail-or-google-contacts-using-google-contacts-data-api-3-0-and-oauth-2-0-in-php/
// Tutorial: https://developers.google.com/google-apps/contacts/v3/


$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && file_exists("../../../../../main.inc.php")) $res=@include("../../../../../main.inc.php");
if (! $res && preg_match('/\/nltechno([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
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
$url='https://accounts.google.com/o/oauth2/auth?client_id='.$client_id.'&redirect_uri='.urlencode($redirect_uri).'&scope=https://www.google.com/m8/feeds/&response_type=code';

$auth_code = GETPOST("code");



/*
 * Actions
*/

// Ask token (possible only if inside an oauth google session)
if (empty($_SESSION['google_oauth_token']) || $auth_code)		// We are not into a google session (oauth_token empty) or we come from a redirect of Google auth page
{
	if (empty($auth_code))	// If we are not coming from oauth page, we make a redirect to it
	{
		//print 'We are not coming from an oauth page and are not logged into google oauth, so we redirect to it';
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

$max_results = 10;

llxHeader();



print '<iframe src="http://www.google.com/calendar/embed?showTitle=0&amp;height=600&amp;wkst=1&amp;bgcolor=%23f4f4f4&amp;src=eldy10%40gmail.com&amp;color=%237A367A&amp;ctz=Europe%2FParis" style=" border-width:0 " width="100%" height="600" frameborder="0" scrolling="no">zob</iframe>';


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



/*


// TEST CONTACT INTERFACE
// Tutorial: http://www.ibm.com/developerworks/library/x-phpgooglecontact/index.html

$user = $conf->global->GOOGLE_LOGIN;
$pwd = $conf->global->GOOGLE_PASSWORD;
print "\n".'<br><br>Test contact interface for user='.$user.' pass='.$pwd.'<br>'."\n";

$path = dol_buildpath('/google/includes/zendgdata');
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once('Zend/Loader.php');
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_HttpClient');
Zend_Loader::loadClass('Zend_Gdata_Contacts');
Zend_Loader::loadClass('Zend_Gdata_Query');
Zend_Loader::loadClass('Zend_Gdata_Feed');
$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pwd, 'cp');


echo '<h2>List Contact (generic method)</h2>';

// LIST Using generic code
try {
	// perform login and set protocol version to 3.0
	$gdata = new Zend_Gdata($client);
	$gdata->setMajorProtocolVersion(3);

	// perform query and get result feed
	$query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/default/full');
	$feed = $gdata->getFeed($query);

	// display title and result count
	print $feed->title."<br>\n";
	print $feed->totalResults."<br>\n";

	// parse feed and extract contact information
	// into simpler objects
	$results = array();
	foreach($feed as $entry)
	{

		$xml = simplexml_load_string($entry->getXML());

		$obj = new stdClass;
		$obj->name = (string) $xml->name->fullName;

		foreach ($xml->email as $e) {
			$obj->emailAddress[] = (string) $e['address'];
		}

		foreach ($xml->phoneNumber as $p) {
			$obj->phoneNumber[] = (string) $p;
		}
		foreach ($xml->website as $w) {
			$obj->website[] = (string) $w['href'];
		}

		$results[] = $obj;
	}
} catch (Exception $e) {
	die('ERROR:' . $e->getMessage());
}


echo '<h2>List Contact (gdata_contacts method)</h2>';

// LIST Using Gdata_contacts method
$gdata = new Zend_Gdata_Contacts($client);
$gdata->setMajorProtocolVersion(3);
$gdata->setMaxResults($max_results);
//$gdata->setMaxResults(300);
$gdata->setStartIndex(0);
$feed = $gdata->getContactListFeed();
$entries = $feed->getEntry();
foreach($entries as $entry)
{
	$tmp=$entry->toArray();
	print $tmp['full_name']."<br>\n";
}

*/


/*
// CREATE GENERIC
$doc  = new DOMDocument();
try {
	// perform login and set protocol version to 3.0
	$gdata = new Zend_Gdata($client);
	$gdata->setMajorProtocolVersion(3);

	// create new entry
	$doc->formatOutput = true;
	$entry = $doc->createElement('atom:entry');
	$entry->setAttributeNS('http://www.w3.org/2000/xmlns/' ,
		'xmlns:atom', 'http://www.w3.org/2005/Atom');
	$entry->setAttributeNS('http://www.w3.org/2000/xmlns/' ,
		'xmlns:gd', 'http://schemas.google.com/g/2005');
	$doc->appendChild($entry);

	// add name element
	$name = $doc->createElement('gd:name');
	$entry->appendChild($name);
	$fullName = $doc->createElement('gd:fullName', 'Test from Dolibarr');
	$name->appendChild($fullName);

	// add email element
	$email = $doc->createElement('gd:email');
	$email->setAttribute('address' ,'testfromdolibarr@example.com');
	$email->setAttribute('rel' ,'http://schemas.google.com/g/2005#home');
	$entry->appendChild($email);

	// add org name element
	$org = $doc->createElement('gd:organization');
	$org->setAttribute('rel' ,'http://schemas.google.com/g/2005#work');
	$entry->appendChild($org);
	$orgName = $doc->createElement('gd:orgName', 'Test org from Dolibarr');
	$org->appendChild($orgName);

	// insert entry
	$entryResult = $gdata->insertEntry($doc->saveXML(),	'http://www.google.com/m8/feeds/contacts/default/full');

	echo '<h2>Add Contact</h2>';
	echo 'The href of the new entry is: ' . $entryResult->getEditLink()->href.'<br>';

} catch (Exception $e) {
	die('ERROR:' . $e->getMessage());
}


// UPDATE
try {
	echo '<h2>Update Contact</h2>';

	$gdata = new Zend_Gdata($client);
	$gdata->setMajorProtocolVersion(3);

	$query = new Zend_Gdata_Query($entryResult->getEditLink()->href);
	//$entryResult = $gdata->getEntry($query,'Zend_Gdata_Contacts_ListEntry');
	$entryResult = $gdata->getEntry($query);

	$xml = simplexml_load_string($entryResult->getXML());
	$xml->name->fullName = 'Test from Dolibarr 2';
	$xml->email['address'] = 'tesfromdolibarr2@example.com';
	$xml->organization->orgName = 'Test org from Dolibarr 2';

	$extra_header = array('If-Match'=>'*');
	$newentryResult = $gdata->updateEntry($xml->saveXML(), $entryResult->getEditLink()->href, null, $extra_header);

	echo 'The href of the new entry is: ' . $newentryResult->getEditLink()->href.'<br>';
} catch (Exception $e) {
	die('ERROR:' . $e->getMessage());
}


// DELETE
try {
	$query = new Zend_Gdata_Query($newentryResult->getEditLink()->href);
	$gdata->setMajorProtocolVersion(3);
	$entryfromid = $gdata->getEntry($query,'Zend_Gdata_Contacts_ListEntry');

	echo '<h2>Delete Contact with etag '.$entryfromid->getEtag().'</h2>';
	$extra_header = array('If-Match'=>$entryfromid->getEtag());

	$newentryResult->delete($extra_header);
	echo 'Entry deleted';
} catch (Exception $e) {
	die('ERROR:' . $e->getMessage());
}


*/

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