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
	 * @param Request $request
	 * @return bool
	 * @since 1.0
	 */
	public static function is_request_valid(Request $request)
	{
		// Signature checks can be disabled
		if (!Kohana::$config->load('hapi.require_signature')) {
			return TRUE;
		}

		$public_key = $request->headers('X-Auth');
		$private_key = Arr::get(Kohana::$config->load('hapi.keys'), $public_key);

		if (! $public_key or ! $private_key) {
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
}