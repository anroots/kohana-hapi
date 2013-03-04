<?php defined('SYSPATH') or die('No direct script access.');
/**
 * All response encoders must implement this
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
interface Kohana_HAPI_Response_Encodable
{

	/**
	 * @return string
	 * @since 1.0
	 */
	public function content_type();

	/**
	 * Sets the response data, overriding the previous value
	 *
	 * @param array $data
	 * @since 1.0
	 * @return HAPI_Response_Encodable
	 */
	public function set_data($data);

	/**
	 * Append to the response data
	 *
	 * @since 1.0
	 * @param $data
	 * @return HAPI_Response_Encodable
	 */
	public function add_data($data);

	/**
	 * @return string
	 * @since 1.0
	 */
	public function encode();

	/**
	 * Add a link to the resource, identified by $rel, located at $uri, with an
	 * optional $title
	 *
	 * @param string $rel
	 * @param string $uri
	 * @param string $title
	 * @param array $attributes Other attributes, as defined by HAL spec and RFC 5988
	 */
	public function add_link($rel, $uri, $title = null, array $attributes = array());
}