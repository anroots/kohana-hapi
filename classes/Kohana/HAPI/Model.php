<?php defined('SYSPATH') or die('No direct script access.');
abstract class Kohana_HAPI_Model
{

	protected $_orm;

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
}