<?php

/**
 * method for executing a GET request
 */
function http_get($url, 
                  $port = 443)
{
	require_once("web_request_classes/FileCachedWebRequest.php");
	
	$curlOpts = array(
		// CURLOPT_SSL_VERIFYPEER => true,
		// CURLOPT_SSL_VERIFYHOST => true,
		// CURLOPT_SSLCERT => CLIENT_CERT, 
		// CURLOPT_SSLKEY => CLIENT_KEY,
		// CURLOPT_SSLKEYPASSWD => CLIENT_KEY_PASSWORD,
		CURLOPT_FOLLOWLOCATION => true//, // to follow all the authentication redirects
		// CURLOPT_COOKIEJAR => COOKIE_JAR,
		// CURLOPT_COOKIE => SHIB_HTTP_GET_COOKIE,
		// CURLOPT_COOKIEFILE => COOKIE_JAR,
		// CURLOPT_VERBOSE => SHIB_HTTP_GET_VERBOSE, // tells curl to output verbose information to the error log (stderr)
		// CURLOPT_CONNECTTIMEOUT => SHIB_HTTP_GET_TIMEOUT);
	);
	
	$requestParameters = array( 
		"URL" => $url,
		"port" => $port,
		"httpMethod" => "GET",
		"cURLOptions" => $curlOpts
	);
	
	$reqObj = new WebRequest($url, $curlOpts);
	return $reqObj->execute($requestParameters, false);
}

/**
 * method for executing a POST request
 */
function http_patch($url, 
                  $port = 443, 
				  $data = "",
				  $contentType = null,
				  )
{
	require_once("web_request_classes/FileCachedWebRequest.php");
	
	$curlOpts = array(
		// CURLOPT_SSL_VERIFYPEER => false,
		// CURLOPT_SSL_VERIFYHOST => false,
		// CURLOPT_SSLCERT => CLIENT_CERT, 
		// CURLOPT_SSLKEY => CLIENT_KEY,
		// CURLOPT_SSLKEYPASSWD => CLIENT_KEY_PASSWORD,
		CURLOPT_FOLLOWLOCATION => true//, 
		// CURLOPT_COOKIEJAR => COOKIE_JAR,
		// CURLOPT_COOKIE => SHIB_HTTP_GET_COOKIE,
		// CURLOPT_COOKIEFILE => COOKIE_JAR,
		// CURLOPT_VERBOSE => SHIB_HTTP_GET_VERBOSE, // tells curl to output verbose information to the error log (stderr)
		// CURLOPT_CONNECTTIMEOUT => SHIB_HTTP_GET_TIMEOUT
	);
	
	if($contentType != null)
	{
		// $curlOpts[CURLOPT_HTTPHEADER] = array('Content-Type' => $contentType, 'content-type' => $contentType);
		$curlOpts[CURLOPT_HTTPHEADER] = array('Content-Type: '.$contentType, 'content-type: '. $contentType);
	}
	
	$requestParameters = array( 
		"URL" => $url,
		"port" => $port,
		"httpMethod" => "PATCH",
		"httpParams" => $data,
		"cURLOptions" => $curlOpts
	);
	
	$reqObj = new WebRequest($url, null);
	
	return $reqObj->execute($requestParameters, false);
}

