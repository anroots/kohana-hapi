<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
class Kohana_HAPI_Request extends Request
{

	/**
	 * @var array
	 * @since 1.0
	 */
	public $hapi_profile_settings;

	/**
	 * Load HAPI profile settings
	 *
	 * @since 1.0
	 * @param string $profile_name
	 * @return Kohana_HAPI_Request
	 */
	public function load_config($profile_name = 'default')
	{
		$profile_settings = Kohana::$config->load('hapi.request.profiles.'.$profile_name);
		$this->hapi_profile_settings = Arr::extract($profile_settings, ['public_key', 'private_key']);
		return $this;
	}

	/**
	 * Change the request method to PUT and add body data.
	 *
	 * @since 1.0
	 * @param array|null $put_data Array of data to include as the request body
	 * @return mixed
	 */
	public function put(array $put_data = NULL)
	{

		// Use data already saved to POST
		if ($put_data === NULL)
		{
			$put_data = $this->post();
		}

		return $this->method(HTTP_Request::PUT)
			->headers('Content-Type', 'application/x-www-form-urlencoded')
			->body(http_build_query($put_data));
	}

	/**
	 * @param string $uri
	 * @param array $client_params
	 * @param bool $allow_external
	 * @param array $injected_routes
	 * @internal param \HTTP_Cache $cache
	 * @return \HAPI_Request|\Request|void
	 */
	public static function factory($uri = '', $client_params = array(), $allow_external = TRUE, $injected_routes = array())
	{
		// If this is the initial request
		if (! Request::$initial)
		{
			if (isset($_SERVER['SERVER_PROTOCOL']))
			{
				$protocol = $_SERVER['SERVER_PROTOCOL'];
			} else
			{
				$protocol = HTTP::$protocol;
			}

			if (isset($_SERVER['REQUEST_METHOD']))
			{
				// Use the server request method
				$method = $_SERVER['REQUEST_METHOD'];
			} else
			{
				// Default to GET requests
				$method = HTTP_Request::GET;
			}

			if (! empty($_SERVER['HTTPS']) AND filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN))
			{
				// This request is secure
				$secure = TRUE;
			}

			if (isset($_SERVER['HTTP_REFERER']))
			{
				// There is a referrer for this request
				$referrer = $_SERVER['HTTP_REFERER'];
			}

			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				// Browser type
				Request::$user_agent = $_SERVER['HTTP_USER_AGENT'];
			}

			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
			{
				// Typically used to denote AJAX requests
				$requested_with = $_SERVER['HTTP_X_REQUESTED_WITH'];
			}

			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
				AND isset($_SERVER['REMOTE_ADDR'])
					AND in_array($_SERVER['REMOTE_ADDR'], Request::$trusted_proxies)
			)
			{
				// Use the forwarded IP address, typically set when the
				// client is using a proxy server.
				// Format: "X-Forwarded-For: client1, proxy1, proxy2"
				$client_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

				Request::$client_ip = array_shift($client_ips);

				unset($client_ips);
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])
				AND isset($_SERVER['REMOTE_ADDR'])
					AND in_array($_SERVER['REMOTE_ADDR'], Request::$trusted_proxies)
			)
			{
				// Use the forwarded IP address, typically set when the
				// client is using a proxy server.
				$client_ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);

				Request::$client_ip = array_shift($client_ips);

				unset($client_ips);
			} elseif (isset($_SERVER['REMOTE_ADDR']))
			{
				// The remote IP address
				Request::$client_ip = $_SERVER['REMOTE_ADDR'];
			}

			if ($method !== HTTP_Request::GET)
			{
				// Ensure the raw body is saved for future use
				$body = file_get_contents('php://input');
			}

			$cookies = array();

			if (($cookie_keys = array_keys($_COOKIE)))
			{
				foreach ($cookie_keys as $key)
				{
					$cookies[$key] = Cookie::get($key);
				}
			}

			// Create the instance singleton
			Request::$initial = $request = new Request($uri, $client_params, $allow_external, $injected_routes);

			// Store global GET and POST data in the initial request only
			$request->protocol($protocol)
				->query($_GET)
				->post($_POST);

			if (isset($secure))
			{
				// Set the request security
				$request->secure($secure);
			}

			if (isset($method))
			{
				// Set the request method
				$request->method($method);
			}

			if (isset($referrer))
			{
				// Set the referrer
				$request->referrer($referrer);
			}

			if (isset($requested_with))
			{
				// Apply the requested with variable
				$request->requested_with($requested_with);
			}

			if (isset($body))
			{
				// Set the request body (probably a PUT type)
				$request->body($body);
			}

			if (isset($cookies))
			{
				$request->cookie($cookies);
			}
		} else
		{
			$request = new HAPI_Request($uri, $client_params, $allow_external, $injected_routes);
		}
		return $request;
	}

	/**
	 * Sign and execute the request
	 *
	 * @return Response
	 */
	public function execute()
	{
		// Timestamp for avoiding identical signatures
		$this->query('ts', (string) time());

		if ($this->hapi_profile_settings === NULL)
		{
			$this->load_config();
		}

		// Add signature to the request
		$signature = HAPI_Security::calculate_hmac($this, $this->hapi_profile_settings['private_key']);
		$this->headers('X-Auth', $this->hapi_profile_settings['public_key']);
		$this->headers('X-Auth-Hash', $signature);

		return parent::execute();
	}

}