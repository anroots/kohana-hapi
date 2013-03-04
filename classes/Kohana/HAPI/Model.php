<?php defined('SYSPATH') or die('No direct script access.');
abstract class Kohana_HAPI_Model
{

	/**
	 * @var ORM
	 */
	protected $_orm;

	/**
	 * @param ORM $orm
	 */
	public function __construct(ORM $orm)
	{
		$this->_orm = $orm;
	}


	/**
	 * @param ORM $orm
	 * @return HAPI_Model
	 */
	public static function factory(ORM $orm)
	{
		$class = get_class($orm);
		$class = preg_replace('/Model/', 'HAPI_Model', $class, 1);
		return new $class($orm);
	}

	/**
	 * @return array
	 */
	public function transform()
	{
		return $this->_orm->as_array();
	}

	/**
	 * @return array
	 */
	public function get_metadata()
	{
		$metadata = [];
		$meta_columns = ['created', 'updated', 'deleted'];
		foreach ($meta_columns as $column_name)
		{
			if (isset($this->_orm->{$column_name}))
			{
				$metadata[$column_name] = $this->_orm->{$column_name};
			}
		}
		return $metadata;
	}
}