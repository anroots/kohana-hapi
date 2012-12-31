<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Adds ORM model loading support to API provider controllers
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
abstract class Kohana_Controller_HAPI_ORM extends Controller_HAPI
{

	/**
	 * TRUE - use only get() method for GET requests
	 * FALSE - use get_one() when param ID is give, get_all() when to ID is given
	 *
	 * @var bool
	 * @since 1.0
	 */
	protected $_use_uniform_get = TRUE;

	/**
	 * @var string The name of the ORM model
	 * @since 1.0
	 */
	protected $_orm_name;

	/**
	 * @var ORM Instance of the (loaded) ORM model
	 */
	protected $_orm;

	public function before()
	{
		parent::before();

		if ($this->_orm_name === NULL)
		{
			$this->_orm_name = $this->_get_orm_name();
		}

		if ($this->_orm === NULL && class_exists('Model_'.$this->_orm_name))
		{
			$this->_orm = ORM::factory($this->_orm_name, $this->request->param('id'));
		}
	}

	/**
	 * Determine the Controller method to execute based
	 * on the HTTP method
	 *
	 * @return string
	 * @throws HTTP_Exception_404
	 * @since 1.0
	 */
	protected function _determine_action()
	{
		// Defaults method is the HTTP verb
		$action = strtolower($this->request->method());

		// Action (if not default) is appended to the HTTP verb
		if ($this->request->action() !== Route::$default_action)
		{
			$action .= '_'.$this->request->action();
		} elseif (! $this->_use_uniform_get and $this->request->method() === HTTP_Request::GET)
		{
			$action .= $this->request->param('id') === NULL ? '_all' : '_one';
		}

		// If the action doesn't exist, it's a 404
		if (! method_exists($this, $action))
		{
			throw new HTTP_Exception_404(
				'The requested URL :uri was not found on this server.',
				array(':uri' => $this->request->uri())
			);
		}
		return $action;
	}

	/**
	 * Extract the name of the matching ORM model from the controller class
	 *
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