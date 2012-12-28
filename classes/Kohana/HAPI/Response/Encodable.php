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
	 * @param array $data
	 * @since 1.0
	 */
	public function set_data(array $data);

	/**
	 * @return string
	 * @since 1.0
	 */
	public function encode();
}