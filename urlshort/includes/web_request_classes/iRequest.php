<?php
/**
 * InvalidParameterException is thrown if invalid parameters are passed to a function.
 * 
 * @author OperativeThunny
 * @version 1.0
 * @date 2 April 2009
 */
class InvalidParameterException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		parent::__construct($message, $code);
	}
}

/**
 * NotImplementedException is thrown if something is not implemented.
 * 
 * @author OperativeThunny
 * @version 1.0
 * @date 2 April 2009
 */
class NotImplementedException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		parent::__construct($message, $code);
	}
}

/**
 * UnableToPerformDutiesException is thrown if any of the PHP is unable to do it's job.
 * 
 * @author OperativeThunny
 * @version 1.0
 * @date 14 April 2009
 */
class UnableToPerformDutiesException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		parent::__construct($message, $code);
	}
}

/**
 * ImpossibleException is thrown if something impossible happens.
 * 
 * @author OperativeThunny
 * @version 1.0
 * @date 14 April 2009
 */
class ImpossibleException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		parent::__construct($message, $code);
	}
}


/**
 * iRequest defines an abstraction of a request, be it a web request, a soap request, a job promotion request...
 * 
 * @author OperativeThunny
 * @version 1.0
 * @date 2 April 2009
 */
interface iRequest
{
	/**
	 * The iRequest::execute(1) function executes the request, whatever that may mean.
	 *
	 * @param array $params	An array of parameters required to execute the particular request the client is making.
	 * @param $fakeRequest Set to true if you don't actually want to execute the request, defaults to false.
	 * @return String value with the results of the request.
	 */
	public function execute(array $params = null, $fakeRequest = false);
}
?>
