<?php
require_once("iCachedRequest.php");

/**
 * The iFileCachedRequest interface represents a more specific abstraction of the iCachedRequest interface 
 * where requests are cached to files.
 *
 * @author OperativeThunny
 * @version 1.0
 * @date 2 April 2009
 */
interface iFileCachedRequest extends iCachedRequest
{
	/**
	 * Sets the cache file path.
	 *
	 * @param string $fileName The file path/name to use for storing the cache in. 
	 * @return True on success, false if the file exists and PHP does not have permission to use that file.
	 */
	public function setCacheFile($fileName);

	/**
	 * Gets the cache file path.
	 * 
	 * @return The file path/name of the cache file.
	 */
	public function getCacheFile();
}
?>
