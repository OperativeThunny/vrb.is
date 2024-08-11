<?php
require_once("WebRequest.php");
require_once("iCachedRequest.php");

/**
 * CachedWebRequest class is an abstract class which represents the concept of a web request which gets cached and on
 * subsequent calls to execute, the request is retrieved from the cache instead of executed again.
 *
 * This class is abstract because there are more than one type of cache and an abstract class is better to provide 
 * abstraction. For example, you could have a cache which lives in a file, or in a database. 
 *
 * @author OperativeThunny
 * @date 14 April 2009
 * @version 1.0
 */
abstract class CachedWebRequest extends WebRequest implements iCachedRequest
{
	protected $cacheTTL = 3600; // the TTL will always be stored in seconds. 
	protected $cacheUnits = "S"; // since the TTL will always be stored in seconds, 
                               // the cache units will always be "S" because when the cacheTTL is set it gets converted to seconds.
	
	/**
	 * Primary constructor. Takes parameters from WebRequest::__construct(2) and adds two parameters from iCachedRequest.
	 */
	public function __construct($url = null, $curlOptions = null, $cacheTTL = 3600, $cacheUnits = 'S')
	{
			parent::__construct($url, $curlOptions);
			
			$this->setCacheTTL($cacheTTL, $cacheUnits);
	}
	
	/**
	 * This function sets the Time To Live (TTL) for the cache. This defines the duration of time 
	 * before a cache is considered stale and needs to be refreshed. This function will convert the 
	 * parameter ttl value into seconds and the value that gets stored will be in seconds.
	 *
	 * @param int $ttlValue is the TTL value you wish to specify.
	 * @param string $ttlUnits the units the TTL value is in, can be (S)econds, (Mi)nutes, (H)ours, 
	 *               (D)ays, or (Mo)nths. Defaults to seconds. Valid values: "S", "Mi", "H", "D", "Mo" 
	 *               or you can spell out the word. Case is not important.
	 * 
	 * @return True on success, failure is not an option.
	 * @throws InvalidParameterException if one of the passed parameters is invalid.
	 * @throws ImpossibleException if something impossible happens.
	 */
	public function setCacheTTL($ttlValue, $ttlUnits = null)
	{
		$ttlUnits = strtoupper($ttlUnits);
		
		if($ttlUnits == 'S' || $ttlUnits == 'MI' || $ttlUnits == 'H' || $ttlUnits == 'D' || $ttlUnits == 'MO')
		{
			if($ttlUnits == 'S')
			{
				$this->cacheUnits = $ttlUnits;
				$this->cacheTTL = $ttlValue;
			}
			else
			{
				// need to convert to seconds.
				$curTime = time();
				
				switch($ttlUnits)
				{
					case "S":
						$myUnits = "seconds";
					break;
					case "MI":
						$myUnits = "minutes";
					break;
					case "H":
						$myUnits = "hours";
					break;
					case "D":
						$myUnits = "days";
					break;
					case "MO":
						$myUnits = "months";
					break;
					default:
						// this code branch is impossible to get to. 
						// the code execution will never get here.
						throw new ImpossibleException("An act of God has occured and we have reached a branch of code that is impossible to get to.");
					break;
				}
				
				$this->cacheUnits = "S";
				$this->cacheTTL = strtotime("+" . $ttlValue . " " . $myUnits, $curTime) - $curTime;
			}
		}
		else
		{
			throw new InvalidParameterException('TTL Units parameter is invalid! Valid values are "S", "Mi", "H", "D", and "Mo"');
		}

		return true;
	}
	
	abstract public function clearCache();
	
	abstract public function getCacheContents();
}
?>
