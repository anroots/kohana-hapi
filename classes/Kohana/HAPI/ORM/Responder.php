<?php defined('SYSPATH') or die('No direct script access.');
trait Kohana_HAPI_ORM_Responder
{

	/**
	 * @return array
	 */
	public function as_hapi_data()
	{
		$hapi_model = HAPI_Model::factory($this);
		return $hapi_model->transform();
	}

	/**
	 * Wrapper for ordering results.
	 * Can be overriden for more complex queries
	 *
	 * @param string $key
	 * @param string $direction
	 * @return ORM
	 */
	public function hapi_order_by($key, $direction = 'asc')
	{
		return $this->order_by($key, $direction);
	}

	/**
	 * @param string $query_string
	 * @return ORM
	 */
	public function search($query_string)
	{
		return $this->where('name', 'LIKE', "%$query_string%");
	}
}