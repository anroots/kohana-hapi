<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Interface form ORM models that want to benefit from Controller_HAPI_ORM_Provider
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
interface Kohana_HAPI_ORM_CRUD
{

	/**
	 * Retrieve record(s) data
	 *
	 * @throws Database_Exception
	 * @return array Record data as an array
	 * @since 1.0
	 */
	public function hapi_get();

	/**
	 * Create a new record
	 *
	 * @throws Database_Exception
	 * @throws ORM_Validation_Exception
	 * @param array $post Data from $_POST
	 * @return bool|int Loaded record ORM instance
	 * @since 1.0
	 */
	public function hapi_post(array $post);

	/**
	 * Update existing record s data.
	 *
	 * @throws Database_Exception
	 * @throws ORM_Validation_Exception
	 * @param array $post Data from $_POST
	 * @return ORM Loaded record ORM instance
	 * @since 1.0
	 */
	public function hapi_put(array $post);

	/**
	 * Delete the record
	 *
	 * @throws Database_Exception
	 * @throws ORM_Validation_Exception
	 * @return bool TRUE or FALSE on success/failure
	 * @since 1.0
	 */
	public function hapi_delete();
}