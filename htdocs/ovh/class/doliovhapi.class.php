<?php
/* Copyright (C) 2007-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file        class/doliovhapi.class.php
 * \ingroup     ovh
 * \brief       OVH API wrapper using Dolibarr's getURLContent instead of Guzzle
 */

require_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';


/**
 * OVH API wrapper using Dolibarr native HTTP functions (cURL via getURLContent)
 * Replaces the ovh/ovh SDK + GuzzleHttp dependency
 */
class DoliOvhApi
{
	/**
	 * @var array Map of endpoint names to API base URLs
	 */
	private $endpoints = array(
		'ovh-eu'        => 'https://eu.api.ovh.com/1.0',
		'ovh-ca'        => 'https://ca.api.ovh.com/1.0',
		'ovh-us'        => 'https://api.us.ovhcloud.com/1.0',
		'kimsufi-eu'    => 'https://eu.api.kimsufi.com/1.0',
		'kimsufi-ca'    => 'https://ca.api.kimsufi.com/1.0',
		'soyoustart-eu' => 'https://eu.api.soyoustart.com/1.0',
		'soyoustart-ca' => 'https://ca.api.soyoustart.com/1.0',
		'runabove-ca'   => 'https://api.runabove.com/1.0',
	);

	/**
	 * @var string API base URL for the selected endpoint
	 */
	private $endpoint = null;

	/**
	 * @var string Application key
	 */
	private $application_key = null;

	/**
	 * @var string Application secret
	 */
	private $application_secret = null;

	/**
	 * @var string Consumer key for authenticated requests
	 */
	private $consumer_key = null;

	/**
	 * @var int|null Delta between local timestamp and API server timestamp
	 */
	private $time_delta = null;


	/**
	 * Constructor
	 *
	 * @param string      $application_key    Application key from https://eu.api.ovh.com/createApp/
	 * @param string      $application_secret Application secret
	 * @param string      $api_endpoint       Endpoint name (e.g. 'ovh-eu') or full URL
	 * @param string|null $consumer_key       Consumer key (optional, needed for authenticated calls)
	 * @throws Exception if endpoint is invalid
	 */
	public function __construct($application_key, $application_secret, $api_endpoint, $consumer_key = null)
	{
		if (!isset($api_endpoint)) {
			throw new Exception("DoliOvhApi: Endpoint parameter is empty");
		}

		if (preg_match('/^https?:\/\/..*/', $api_endpoint)) {
			$this->endpoint = $api_endpoint;
		} else {
			if (!array_key_exists($api_endpoint, $this->endpoints)) {
				throw new Exception("DoliOvhApi: Unknown provided endpoint");
			}
			$this->endpoint = $this->endpoints[$api_endpoint];
		}

		$this->application_key = $application_key;
		$this->application_secret = $application_secret;
		$this->consumer_key = $consumer_key;
		$this->time_delta = null;
	}

	/**
	 * Calculate time delta between local machine and API server
	 *
	 * @return int Time delta in seconds
	 * @throws Exception on HTTP error
	 */
	private function calculateTimeDelta()
	{
		if (!isset($this->time_delta)) {
			$response = $this->rawCall('GET', '/auth/time', null, false);
			$serverTimestamp = (int) $response;
			$this->time_delta = $serverTimestamp - (int) time();
		}

		return $this->time_delta;
	}

	/**
	 * Request a consumer key from the API and get the validation URL
	 *
	 * @param  array       $accessRules  List of access rules (method + path objects)
	 * @param  string|null $redirection  URL to redirect after authentication
	 * @return array                     Array with 'consumerKey' and 'validationUrl'
	 * @throws Exception on HTTP error
	 */
	public function requestCredentials(array $accessRules, $redirection = null)
	{
		$parameters = new \StdClass();
		$parameters->accessRules = $accessRules;
		$parameters->redirection = $redirection;

		$response = $this->decodeResponse(
			$this->rawCall('POST', '/auth/credential', $parameters, true)
		);

		$this->consumer_key = $response["consumerKey"];

		return $response;
	}

	/**
	 * Sign and execute an API call using Dolibarr's getURLContent
	 *
	 * @param  string                  $method           HTTP method (GET, POST, PUT, DELETE)
	 * @param  string                  $path             API path (e.g. '/me/bill')
	 * @param  \stdClass|array|null    $content          Request body or GET query parameters
	 * @param  bool                    $is_authenticated Whether to sign the request
	 * @return string                                    Raw response body
	 * @throws Exception on HTTP or cURL error
	 */
	private function rawCall($method, $path, $content = null, $is_authenticated = true)
	{
		if ($is_authenticated) {
			if (!isset($this->application_key)) {
				throw new Exception("DoliOvhApi: Application key parameter is empty");
			}
			if (!isset($this->application_secret)) {
				throw new Exception("DoliOvhApi: Application secret parameter is empty");
			}
		}

		$url = $this->endpoint . $path;
		$body = '';

		if (isset($content) && $method == 'GET') {
			// Build query string from content for GET requests
			$query = (array) $content;
			foreach ($query as $key => $value) {
				if ($value === false) {
					$query[$key] = "false";
				} elseif ($value === true) {
					$query[$key] = "true";
				} elseif ($value === null) {
					unset($query[$key]);
				}
			}
			$queryString = http_build_query($query);
			if ($queryString) {
				$url .= (strpos($url, '?') === false ? '?' : '&') . $queryString;
			}
		} elseif (isset($content)) {
			$body = json_encode($content, JSON_UNESCAPED_SLASHES);
		}

		$headers = array(
			'Content-Type: application/json; charset=utf-8',
		);

		if ($is_authenticated) {
			$headers[] = 'X-Ovh-Application: ' . $this->application_key;

			if (!isset($this->time_delta)) {
				$this->calculateTimeDelta();
			}
			$now = time() + $this->time_delta;

			$headers[] = 'X-Ovh-Timestamp: ' . $now;

			if (isset($this->consumer_key)) {
				$toSign = $this->application_secret . '+' . $this->consumer_key . '+' . $method
					. '+' . $url . '+' . $body . '+' . $now;
				$signature = '$1$' . sha1($toSign);
				$headers[] = 'X-Ovh-Consumer: ' . $this->consumer_key;
				$headers[] = 'X-Ovh-Signature: ' . $signature;
			}
		}

		// Debug logging
		if (getDolGlobalString('OVH_DEBUG')) {
			$logfile = DOL_DATA_ROOT . '/dolibarr_ovh.log';
			$filefd = fopen($logfile, 'a+');
			if ($filefd) {
				fwrite($filefd, date('Y-m-d H:i:s') . ' ' . $method . ' ' . $url . "\n");
				fwrite($filefd, var_export($headers, true) . "\n");
				if ($body) {
					fwrite($filefd, 'BODY: ' . $body . "\n");
				}
				fclose($filefd);
				@chmod($logfile, octdec(getDolGlobalString('MAIN_UMASK', '0664')));
			}
		}

		// Map HTTP method for getURLContent
		$httpMethod = $method;
		$param = '';
		if ($method == 'POST') {
			$httpMethod = 'POSTALREADYFORMATED';
			$param = $body;
		} elseif ($method == 'PUT') {
			$httpMethod = 'PUTALREADYFORMATED';
			$param = $body;
		}

		$result = getURLContent($url, $httpMethod, $param, 1, $headers, array('https'), 0, 1);

		// Debug response logging
		if (getDolGlobalString('OVH_DEBUG')) {
			$logfile = DOL_DATA_ROOT . '/dolibarr_ovh.log';
			$filefd = fopen($logfile, 'a+');
			if ($filefd) {
				fwrite($filefd, 'RESPONSE HTTP ' . $result['http_code'] . ': ' . $result['content'] . "\n\n");
				fclose($filefd);
				@chmod($logfile, octdec(getDolGlobalString('MAIN_UMASK', '0664')));
			}
		}

		if ($result['curl_error_no']) {
			dol_syslog("DoliOvhApi::rawCall cURL error " . $result['curl_error_no'] . ': ' . $result['curl_error_msg'], LOG_ERR);
			throw new Exception('DoliOvhApi: cURL error ' . $result['curl_error_no'] . ': ' . $result['curl_error_msg']);
		}

		$httpCode = $result['http_code'];
		if ($httpCode >= 400) {
			$errorMessage = 'DoliOvhApi: HTTP error ' . $httpCode;
			$responseBody = $result['content'];
			if ($responseBody) {
				$decoded = json_decode($responseBody, true);
				if ($decoded && isset($decoded['message'])) {
					$errorMessage .= ' - ' . $decoded['message'];
				} else {
					$errorMessage .= ' - ' . $responseBody;
				}
			}
			dol_syslog("DoliOvhApi::rawCall " . $errorMessage, LOG_ERR);
			throw new Exception($errorMessage);
		}

		return $result['content'];
	}

	/**
	 * Decode a JSON response string to an array
	 *
	 * @param  string $response  Raw JSON response body
	 * @return array
	 */
	private function decodeResponse($response)
	{
		if (version_compare(PHP_VERSION, '7.2', '<')) {
			return json_decode($response, true);
		} else {
			return json_decode($response, true, 512, JSON_INVALID_UTF8_IGNORE | JSON_INVALID_UTF8_SUBSTITUTE);
		}
	}

	/**
	 * Wrap call to OVH APIs for GET requests
	 *
	 * @param  string     $path    API path
	 * @param  array|null $content Query parameters
	 * @return array
	 * @throws Exception on HTTP error
	 */
	public function get($path, $content = null)
	{
		if (preg_match('/^\/[^\/]+\.json$/', $path)) {
			// Schema description must be accessed without authentication
			return $this->decodeResponse(
				$this->rawCall("GET", $path, $content, false)
			);
		}
		return $this->decodeResponse(
			$this->rawCall("GET", $path, $content, true)
		);
	}

	/**
	 * Wrap call to OVH APIs for POST requests
	 *
	 * @param  string     $path    API path
	 * @param  array|null $content Body content
	 * @return array
	 * @throws Exception on HTTP error
	 */
	public function post($path, $content = null)
	{
		return $this->decodeResponse(
			$this->rawCall("POST", $path, $content, true)
		);
	}

	/**
	 * Wrap call to OVH APIs for PUT requests
	 *
	 * @param  string $path    API path
	 * @param  array  $content Body content
	 * @return array
	 * @throws Exception on HTTP error
	 */
	public function put($path, $content)
	{
		return $this->decodeResponse(
			$this->rawCall("PUT", $path, $content, true)
		);
	}

	/**
	 * Wrap call to OVH APIs for DELETE requests
	 *
	 * @param  string     $path    API path
	 * @param  array|null $content Body content
	 * @return array
	 * @throws Exception on HTTP error
	 */
	public function delete($path, $content = null)
	{
		return $this->decodeResponse(
			$this->rawCall("DELETE", $path, $content, true)
		);
	}

	/**
	 * Get the current consumer key
	 *
	 * @return string|null
	 */
	public function getConsumerKey()
	{
		return $this->consumer_key;
	}
}
