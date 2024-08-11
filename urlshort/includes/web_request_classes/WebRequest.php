<?php 
require_once("iRequest.php");

/**
 * WebRequest is the implementation of the iRequest interface. It provides methods for setting up a web request and 
 * making the web request. This class is dependant on cURL so that module must be enabled in the PHP configuration.
 * Facilities are provided for passing options directly to cURL so that way you can do things this class doesn't 
 * provide wrappers for.
 * 
 * @author OperativeThunny
 * @version 1.0
 * @date 2 April 2009
 */
class WebRequest implements iRequest
{
	protected $cHandle = null;
	protected $URL = null;
	protected $port = 80;
	protected $authMethod = "NONE";
	protected $authUser = "";
	protected $authPass = "";
	protected $httpMethod = "GET";
	protected $postParameters = "";
	protected $curlOptions = array();
	protected $requestParameters = array();
	
	/**
	 * Primary constructor. Initializes the cURL object 
	 * 
	 * @param array $curlOptions An array of options to pass to cURL. Example array: 
	 *                           $options = array(CURLOPT_URL => 'http://www.example.com/',
     *                              CURLOPT_HEADER => false
     *                           );
	 * @see http://ngicportal/sites/IT/EN/web/Manual/php/function.curl-setopt.html for the available cURL options.
	 */	
	public function __construct($url = null, $curlOptions = null)
	{
		$this->URL = $url;
		
		$this->setCurlOptions($curlOptions);
	}
	
	/**
	 * @see iRequest::execute(array $params = null) for documentation.
	 * 
	 * @param array $params An array of parameters for this web request. Example array:
	 * array( "URL" => "http://website",
     *        "port" => 8080,
     *        "authMethod" => "BASIC", // valid values are BASIC, DIGEST, ALL, or NONE
     *        "authUser" => "bob",
     *        "authPass" => "",
     *        "httpMethod" => "POST", // valid values are GET, PATCH, or POST
     *        "httpParams" => "id=10&q=search%20terms&file=books.html", 
     *        "cURLOptions" => array( CURLOPT_SSL_VERIFYHOST => 2,
     *                                CURLOPT_HEADER => false ) );
	 *
	 * @param $fakeRequest Whether or not to actually execute the request, if it is set to true, the request does 
	 *        not get executed but all the curl options do get set.
	 *
	 * @note If you specify options in the cURLOptions element that are the same as the ones specified in the other 
	 *       parameters, such as port number, the options in the cURLOptions array will be overridden.
	 * @note You can ommit parameters in the $params array if you don't need to set them.
	 * @note If you specify cURLOptions, then WebRequest::setCurlOptions will be called, replacing previously set 
	 *       cURL options.
	 * 
	 * @return the response string
	 */
	public function execute(array $params = null, $fakeRequest = false)
	{
		if($params != null)
		{
			$this->parseParamsArr($params);
		}
		
		if(!isset($this->URL) || empty($this->URL))
		{
			// We cannot procede as the URL has not been set to something useful.
			throw new InvalidArgumentException("The URL to make this web request to has not been specified.");
		}
		else
		{
			// set the cURL options and make the request
			curl_setopt($this->cHandle, CURLOPT_URL, $this->URL);
			curl_setopt($this->cHandle, CURLOPT_PORT, $this->port);
			
			// authentication mode
			switch(strtoupper($this->authMethod))
			{
				case "BASIC":
					curl_setopt($this->cHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
					$this->setCurlUserPass($this->authUser, $this->authPass);
				break;
				case "DIGEST":
					curl_setopt($this->cHandle, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
					$this->setCurlUserPass($this->authUser, $this->authPass);
				break;
				case "ALL":
					curl_setopt($this->cHandle, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);
					$this->setCurlUserPass($this->authUser, $this->authPass);
				break;
				case "NONE":
				break;
				default:
					throw new InvalidParameterException("Something other than BASIC, DIGEST, ALL, OR NONE was specified for the authMethod property.");
				break;
			}
			
			// http method:
			switch(strtoupper($this->httpMethod))
			{
				case "GET":
					curl_setopt($this->cHandle, CURLOPT_HTTPGET, true);
					curl_setopt($this->cHandle, CURLOPT_POST, false);
					break;
				case "POST":
					curl_setopt($this->cHandle, CURLOPT_HTTPGET, false);
					curl_setopt($this->cHandle, CURLOPT_POST, true);
					curl_setopt($this->cHandle, CURLOPT_POSTFIELDS, $this->postParameters);
					break;
				case "PATCH":
					curl_setopt($this->cHandle, CURLOPT_CUSTOMREQUEST, "PATCH");
					curl_setopt($this->cHandle, CURLOPT_POSTFIELDS, $this->postParameters);
					break;
				default:
					throw new InvalidParameterException("Something other than GET, PATCH, or POST was specified for the httpMethod property.");
					break;
			}
			
			if(!$fakeRequest)
			{
				// execute the request
				/*$res = curl_exec($this->cHandle);
				return $res;*/
				return $this->executeCurl();
			}
		}
	}
	
	/**
	 * This function will do nothing but execute the curl request. This will not set any parameters.
	 * This function is useful if you called WebRequest::execute(array $params = null, TRUE) indicating
	 * you did not want to actually execute the request, but just set all the curl options.
	 */
	public function executeCurl()
	{
		return curl_exec($this->cHandle);
	}
	
	/**
	 * Set the cURL options via an array. Re-initializes the cURL object with the specified parameters. 
	 * Any previous parameters set not specified in the parameter will be lost.
	 *
	 * @param array $curlParams An array of options to pass to cURL.
	 * @see WebRequest::__construct(string $url, array $curlOptions = null) for an example array.
	 * @see curl_setopt documentation for the available cURL options.
	 */
	public function setCurlOptions($curlParams)
	{
		$this->curlOptions = $curlParams;
		
		if($this->URL !== null)
			$this->cHandle = curl_init($this->URL);
		else
			$this->cHandle = curl_init();
		
		if($this->curlOptions != null && count($this->curlOptions) > 0)
			curl_setopt_array($this->cHandle, $this->curlOptions);
			
		curl_setopt($this->cHandle, CURLOPT_RETURNTRANSFER, true);
	}
	
	/**
	 * This function goes through the parameter array which is an array of parameters to set 
	 * the state of this class.
	 * 
	 * @param array $params an array of parameters to set the state of this class with.
	 * @see The comment for 
	 *      WebRequest::execute(array $params = null, $fakeRequest = false) for an example of 
	 *      the array this function expects
	 */
	protected function parseParamsArr(array $params)
	{
		$this->requestParameters = $params;
		
		// parse through the parameters and set the object's state up based on those parameters	
		if(isset($params["URL"]))
			$this->URL = $params["URL"];
		
		if(isset($params["port"]))
			$this->port = $params["port"];
		
		if(isset($params["authMethod"]))
			$this->authMethod = $params["authMethod"];
		
		if(isset($params["authUser"]))
			$this->authUser = $params["authUser"];
			
		if(isset($params["authPass"]))
			$this->authPass = $params["authPass"];
			
		if(isset($params["httpMethod"]))
			$this->httpMethod = $params["httpMethod"];
			
		if(isset($params["httpParams"]))
			$this->postParameters = $params["httpParams"];
			
		if(isset($params["cURLOptions"]))
			$this->setCurlOptions($params["cURLOptions"]);	
	}
	
	/**
	 * Sets the username and password for cURL to use for the connection.
	 *
	 * @param $user the username to set the username of the curl request to.
	 * @param $pass the password to set the apssword of the curl request to.
	 */
	protected function setCurlUserPass($user, $pass)
	{
		curl_setopt($this->cHandle, CURLOPT_USERPWD, $user . ":" . $pass);
	}
}
?>
