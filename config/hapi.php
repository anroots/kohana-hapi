<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
return [

	/**
	 * The development mode enables automatic login.
	 *
	 * Pass `_hapi_login` to the HAPI as a GET variable and set it to the ID of a user in your users table.
	 * The HAPI logs uses Auth::instance()->force_login to login that user.
	 * Works in Kohana::$environment = Kohana::DEVELOPMENT only
	 *
	 * @example http://sqroot.eu/api/test?_hapi_login=1
	 * @var bool
	 */
	'development_mode'    => TRUE,

	/**
	 * API version to use when the Accept header does not contain a version
	 */
	'default_version'     => '1.0',
	/**
	 * Require valid user before processing API requests
	 */
	'require_login'       => TRUE,
	/**
	 * Set to FALSE to disable signature checks.
	 * This allows processing of unsigned requests, useful in development
	 * mode (when using external client to manually build queries)
	 *
	 * @since 1.0
	 */
	'require_signature'   => Kohana::$environment === Kohana::PRODUCTION,
	/**
	 * Automatically include request meta-data in the HAPI response
	 * Example: response generation time
	 *
	 * @since 1.0
	 */
	'include_metadata'    => TRUE,
	/**
	 * @since 1.0
	 */
	'allow_origin'        => 'http://fanapp.local/',
	/**
	 * A list of language codes that are supported by the API
	 * @example ['en', 'et']
	 */
	'supported_languages' => [],
	/**
	 * List of supported HAPI response encoders.
	 *
	 * Keys are HTTP MIME types, values class names in HAPI/Response directory
	 *
	 * @since 1.0
	 */
	'encoders'            => [
		'application/json'               => 'JSON',
		'application/vnd.no99-v1.0+json' => 'JSON'
	],
	/**
	 * Public key => Private key
	 *
	 * @since 1.0
	 */
	'keys'                => [
		'FYjrCuOj5nORuRZXTe70P4mYjnFoTjgG' => 'l6M5LLJpAEL9F1tuE5mDEyODTNK6P2kS'
	],
	/**
	 * @since 1.0
	 */
	'request'             => [
		/**
		 * @since 1.0
		 */
		'profiles' => [
			'default' => [
				'public_key'  => 'FYjrCuOj5nORuRZXTe70P4mYjnFoTjgG',
				'private_key' => 'l6M5LLJpAEL9F1tuE5mDEyODTNK6P2kS'
			],
			'test'    => [
				'public_key'  => 'FYjrCuOj5nORuRZXTe70P4mYjnfrTjgG',
				'private_key' => '36M5LLJpAEL9F1tdc5mDEyODTNK6P2kS'
			]
		]
	]
];