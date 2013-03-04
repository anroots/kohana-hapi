<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
class Kohana_HAPI_Security
{

	/**
	 * Login using HTTP Basic Auth. `username` and `api_token` columns are used for authentication.
	 *
	 * @param string $authorization_string HTTP 'Authorization' header value
	 * @return bool|Model_User Logged in user or false
	 */
	public static function login($authorization_string)
	{
		// Basic ZGhhcmE6ddVzdA==
		$tokens = Arr::get(explode(' ', $authorization_string), 1);

		// ZGhhcmE6ddVzdA==
		$tokens = base64_decode($tokens);

		// user:pass
		$tokens = explode(':', $tokens);
		$username = Arr::get($tokens, 0);
		$api_token = Arr::get($tokens, 1);

		if (empty($username) || empty($api_token))
		{
			return FALSE;
		}

		$user = ORM::factory('User', ['username' => $username, 'api_token' => $api_token]);
		if (! $user->loaded())
		{
			return FALSE;
		}

		Auth::instance()->force_login($user);

		// Hack to access the user object within the context of the current Request (without redirect)
		Session::instance()->set(Kohana::$config->load('auth.session_key'), $user);
		return $user;
	}

	/**
	 * Check that we have an active Auth session
	 *
	 * @param Request $request
	 * @return bool
	 */
	public static function is_request_authenticated(Request $request)
	{
		// User is authenticated automatically on most AJAX calls.
		// Session cookie is transmitted with the request
		$user = Auth::instance()->get_user();
		return $user !== NULL && $user->loaded();
	}

	/**
	 * @param Request $request
	 * @return bool
	 * @since 1.0
	 */
	public static function is_request_signature_valid(Request $request)
	{

		$public_key = $request->headers('X-Auth');
		$private_key = Arr::get(Kohana::$config->load('hapi.keys'), $public_key);

		if (! $public_key or ! $private_key)
		{
			return FALSE;
		}

		$provided_request_signature = $request->headers('X-Auth-Hash');
		$expected_request_signature = self::calculate_hmac($request, $private_key);


		return $expected_request_signature === $provided_request_signature;
	}


	/**
	 * Calculate the signature of a request.
	 * Should be called just before the request is sent.
	 *
	 * @param Request $request
	 * @param $private_key
	 * @return string Calculated HMAC
	 */
	public static function calculate_hmac(Request $request, $private_key)
	{
		// Consolidate data that's not in the main route (params)
		$query = array_change_key_case($request->query());
		$post = array_change_key_case($request->post());

		// Sort alphabetically
		ksort($query);
		ksort($post);

		$data_to_sign = [
			'method' => $request->method(),
			'uri'    => $request->uri(),
			'post'   => $post,
			'query'  => $query,
		];

		// Calculate the signature
		return hash_hmac('sha256', json_encode($data_to_sign), $private_key);
	}

	/**
	 * Send a www-authenticate response
	 *
	 * @param string $message
	 * @throws HTTP_Exception_401
	 */
	public static function require_auth($message = 'Authenticate!')
	{
		$http_401 = new HTTP_Exception_401($message);
		$http_401->authenticate('Basic realm="'.Kohana::$config->load("hapi.realm").'"');
		throw $http_401;
	}
}