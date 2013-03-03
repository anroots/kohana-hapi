<?php defined('SYSPATH') or die('No direct script access.');
/**
 * HTTP Exception override. Needs APPPATH/HTTP/Exception.php to extend this
 *
 * Tries to conform to api-problem content type
 *
 *     throw HTTP_Exception::factory(500,'Please specify the filters')->set_description_url('http://mylink.com/85');
 *
 * @link http://www.mwop.net/blog/2013-02-13-restful-apis-with-zf2-part-2.html
 */
class HAPI_HAPI_HTTP_Exception extends Kohana_HTTP_Exception
{

	/**
	 * @var string Link to an URL where one can get additional information about the particular problem. Required.
	 */
	protected $_described_by;

	/**
	 * @param string $url
	 * @return $this
	 */
	public function set_description_url($url)
	{
		$this->_described_by = $url;
		return $this;
	}

	/**
	 * Shorthand for throwing new HTTP exceptions
	 *
	 * @param int $code HTTP status code
	 * @param string $described_by a URL to a document describing the error condition (required)
	 * @param string $title a brief title for the error condition (required)
	 * @param null|array $variables I18n replacements
	 * @return $this
	 */
	public static function problem($code, $described_by, $title, array $variables = NULL)
	{
		return static::factory($code, $title, $variables)->set_description_url($described_by);
	}

	/**
	 * Generate a Response for the current Exception
	 *
	 * @uses   Kohana_Exception::response()
	 * @return Response
	 */
	public function get_response()
	{
		$response = static::response($this);

		$response_data = [
			'describedBy' => $this->_described_by,
			'httpStatus'  => $response->status(),
			'title'       => $response->body()
		];
		return $response->body(json_encode($response_data));
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


			// Set the response body
			$response->body($message);
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