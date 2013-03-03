<?php defined('SYSPATH') or die('No direct script access.');
class HAPI_HAPI_HTTP_Exception extends Kohana_HTTP_Exception
{

	/**
	 * Generate a Response for the current Exception
	 *
	 * @uses   Kohana_Exception::response()
	 * @return Response
	 */
	public function get_response()
	{
		return static::response($this);
	}

	/**
	 * Get a Response object representing the exception
	 *
	 * @uses    Kohana_Exception::text
	 * @param   Exception  $e
	 * @return  Response
	 */
	public static function response(Exception $e)
	{
		// Log the exception
		Kohana_Exception::log($e);

		try
		{
			// Get the exception information
			$code = $e->getCode();
			$message = $e->getMessage();

			if (! headers_sent())
			{
				// Make sure the proper http header is sent
				$http_header_status = ($e instanceof HTTP_Exception) ? $code : 500;
			}


			if ($e instanceof ErrorException && isset(Kohana_Exception::$php_errors[$code]))
			{
				// Use the human-readable error name
				$code = Kohana_Exception::$php_errors[$code];
			}

			// Prepare the response object.
			$response = Response::factory();

			// Set the response status
			$response->status(($e instanceof HTTP_Exception) ? $e->getCode() : 500);

			// Set the response headers
			$response->headers('Content-Type', 'application/api-problem+json; charset='.Kohana::$charset);

			$response_data = [
				'describedBy' => 'http://en.wikipedia.org/wiki/List_of_HTTP_status_codes', // Todo
				'httpStatus'  => $code,
				'title'       => $message
			];

			// Set the response body
			$response->body(json_encode($response_data));
		} catch (Exception $e)
		{
			/**
			 * Things are going badly for us, Lets try to keep things under control by
			 * generating a simpler response object.
			 */
			$response = Response::factory();
			$response->status(500);
			$response->headers('Content-Type', 'text/plain');
			$response->body(Kohana_Exception::text($e));
		}

		return $response;
	}
}