<?php defined('SYSPATH') or die('No direct script access.');
/**
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
abstract class Kohana_Controller_HAPI_ORM_Provider extends Kohana_Controller_HAPI_Provider
{

	/**
	 * @var string
	 * @since 1.0
	 */
	protected $_orm_name;

	/**
	 * @var ORM
	 */
	protected $_orm;

	public function before()
	{
		parent::before();

		if ($this->_orm_name === NULL) {
			$this->_orm_name = $this->_get_orm_name();
		}

		if ($this->_orm === NULL && class_exists('Model_'.$this->_orm_name)) {
			$this->_orm = ORM::factory($this->_orm_name, $this->request->param('id'));
		}

	}

	/**
	 * @return string
	 * @since 1.0
	 * Todo: Support nested files (uppercase)
	 */
	protected function _get_orm_name()
	{
		$controller_class_name = get_called_class();
		$controller_file_name = explode('_', $controller_class_name);
		return ucfirst(Inflector::singular(end($controller_file_name)));
	}
}