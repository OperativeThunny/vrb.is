<?php
date_default_timezone_set('EST');
require_once("CachedWebRequest.php");
require_once("iFileCachedRequest.php");

/**
 * This is the concrete class which implements the cached web request functionality via a file cache.
 *
 * @author OperativeThunny
 * @date 8 January 2014
 * @version 1.1
 */
class FileCachedWebRequest extends CachedWebRequest implements iFileCachedRequest
{
	private $cacheFile = "./CacheFile.txt";
	
	/**
	 * Primary constructor. Has all parameters from the WebRequest and CachedWebRequest and adds one parameter for the cache file.
	 */
	public function __construct($url = null, $curlOptions = null, $cacheTTL = 3600, $cacheUnits = 'S', $cacheFile = "./CacheFile.txt")
	{
		parent::__construct($url,$curlOptions,$cacheTTL,$cacheUnits);	
		
		$this->cacheFile = $cacheFile;
	}
	
	/**
	 * Sets the cache file.
	 *
	 * @param string $fileName the file name that will contain the cache.
	 */
	public function setCacheFile($fileName)
	{
		$this->cacheFile = $fileName;
	}
	
	/**
	 * Gets the cache file.
	 *
	 * @return The string representation of the file that contains the cache.
	 */
	public function getCacheFile()
	{
		return $this->cacheFile;	
	}
	
	/**
	 * Clears the cache by deleting the file containing the cache. If it cannot delete the file, it truncates the file to length 0.
	 * If this method can't do either of the two previous options, an exception is thrown.
	 *
	 * @return true on success, failure is not an option for a return value.
	 * @throws UnableToPerformDutiesException
	 */
	public function clearCache()
	{
		if(unlink($this->cacheFile))
		{
			return true;
		}
		else
		{
			$fp = fopen($this->cacheFile, "r+");

			if($fp !== false)
			{
				if(ftruncate($fp, 0))
				{
					fclose($fp);
					return true;
				}
				else
				{
					fclose($fp);
					throw new UnableToPerformDutiesException("Unable to delete cache file and unable to truncate the cache file to 0.");	
				}
			}
			else
			{
				throw new UnableToPerformDutiesException("Unable to delete cache file and open the file for truncation.");
			}
		}
		
		return false;
	}
	
	/**
	 * Returns the cache contents as a string.
	 *
	 * @return the contents of the cache file as a string, false if the cache file does not exist.
	 */
	public function getCacheContents()
	{
		if(file_exists($this->cacheFile))
			return file_get_contents($this->cacheFile, true);
		
		return false;
	}
	
	/**
	 * Determines whether or not the cache is stale. 
	 * A cache is stale if it's last modification time plus the TTL value is less than the current time.
	 *
	 * @return true if the cache exists and is stale, false if the cache exists and is not stale or false if the cache does not exist.
	 */
	public function cacheStale()
	{
		clearstatcache();
		clearstatcache();
		clearstatcache();
		
		if(file_exists($this->cacheFile))
		{
			$fileInfo = stat($this->cacheFile);
			$lastMod = $fileInfo['mtime'];
			
			if($lastMod + $this->cacheTTL < time())
			{
				return true;
			}
		}
		else
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Executes a web request with cache functionality. First checks the cache and returns the cache value. 
	 * If there is no cache or the cache is stale, executes a web request and caches the results.
	 * 
	 * @see WebRequest::execute(array $params = null, $fakeRequest = false) for more detail about the parameters
	 */
	public function execute(array $params = null, $fakeRequest = false)
	{
		if($this->cacheStale())
		{
			$response = parent::execute($params, $fakeRequest);
			
			if(file_put_contents($this->cacheFile, $response) === false)
			{
				throw new UnableToPerformDutiesException("Error: unable to write response to cache file");
			}
			
			return $response;
		}
		else
		{
			return file_get_contents($this->cacheFile);
		}
	}
}
?>
