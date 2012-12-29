<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Placeholders for ORM models
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
trait HAPI_ORM_Trait
{

	/**
	 * Retrieve record(s) data
	 *
	 *
	 * @throws HTTP_Exception_405
	 * @return array Record data as an array
	 * @since 1.0
	 */
	public function hapi_get()
	{
		throw new HTTP_Exception_405('Not implemented');
	}

	/**
	 * Create a new record
	 *
	 * @param array $post Data from $_POST
	 * @throws HTTP_Exception_405
	 * @since 1.0
	 */
	public function hapi_post(array $post)
	{
		throw new HTTP_Exception_405('Not implemented');
	}

	/**
	 * Update existing record s data.
	 *
	 * @param array $post Data from $_POST
	 * @throws HTTP_Exception_405
	 * @since 1.0
	 */
	public function hapi_put(array $post)
	{
		throw new HTTP_Exception_405('Not implemented');
	}

	/**
	 * Delete the record
	 *
	 *
	 * @throws HTTP_Exception_405
	 * @since 1.0
	 */
	public function hapi_delete()
	{
		throw new HTTP_Exception_405('Not implemented');
	}
}