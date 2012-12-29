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
	 * List of supported HAPI response encoders.
	 *
	 * Keys are HTTP MIME types, values class names in HAPI/Response directory
	 *
	 * @since 1.0
	 */
	'encoders' => [
		'application/json' => 'JSON',
		'application/vnd.no99-v1.0+json' => 'JSON'
	]
];