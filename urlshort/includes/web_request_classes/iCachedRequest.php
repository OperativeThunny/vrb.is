<?php
require_once("iRequest.php");

/**
 * The iCachedRequest interface defines an abstraction of a cached request, such as a cached web 
 * request where if a cache does not exist, a web request is made and the results of that request
 * is cached, for example, to a file. Subsequent calls of the cached request will pull the data
 * from the cache instead of the online source.
 *
 * @author OperativeThunny
 * @version 1.0
 * @date 2 April 2009
 */
interface iCachedRequest extends iRequest
{
	/**
	 * This function sets the Time To Live (TTL) for the cache. This defines the duration of time 
	 * before a cache is considered stale and needs to be refreshed.
	 *
	 * @param int $ttlValue is the TTL value you wish to specify.
	 * @param string $ttlUnits the units the TTL value is in, can be (S)econds, (Mi)nutes, (H)ours, 
	 *               (D)ays, or (Mo)nths. Defaults to seconds. Valid values: "S", "Mi", "H", "D", "Mo" 
	 *               or you can spell out the word. Case is not important.
	 * 
	 * @return True on success, failure is not an option.
	 * @throws InvalidParameterException if one of the passed parameters is invalid.
	 */
	public function setCacheTTL($ttlValue, $ttlUnits = 'S');

	/**
	 * Clears the cache. Next request execution after this is called will obviously need to refresh the cache. 
	 *
	 * @return True on success, False on failure.
	 */
	public function clearCache();
	
	/**
	 * This function obtains the cache data and gives it back to the calling client.
	 * 
	 * @return The cache contents as a string.
	 */
	public function getCacheContents();
	
	/**
	 * Determines whether or not the cache is stale. 
	 *
	 * @returns true if the cache exists and is stale, false if the cache exists and is not stale or false if the cache does not exist.
	 */
	public function cacheStale();
}
?>
