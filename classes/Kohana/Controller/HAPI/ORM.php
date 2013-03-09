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
	 * Number of results to return for GET queries when no limit is specified
	 */
	const DEFAULT_ITEMS_PER_PAGE = 50;

	/**
	 * Page sizes can not exceed this value
	 */
	const MAX_ITEMS_PER_PAGE = 200;


	/**
	 * @var string Default sort direction from (asc|desc)
	 */
	public $_direction = 'asc';

	/**
	 * @var string Column name to order results by
	 * @see HAPI_ORM_Responder::hapi_order_by
	 */
	public $_orderby;

	/**
	 * TRUE - use only get() method for GET requests
	 * FALSE - use get_one() when param ID is given, get_all() when no ID is given
	 *
	 * @var bool
	 * @since 1.0
	 */
	protected $_use_uniform_get = FALSE;

	/**
	 * @var string The name of the ORM model
	 * @since 1.0
	 */
	protected $_orm_name;

	/**
	 * @var ORM Instance of the (loaded) ORM model
	 */
	protected $_orm;

	/**
	 * @var int Default number of rows to show per page
	 */
	protected $_per_page;

	public function before()
	{
		parent::before();

		// Try to get the matching ORM model name
		if ($this->_orm_name === NULL)
		{
			$this->_orm_name = $this->_get_orm_name();
		}

		// Try to instantiate and load the ORM model
		if ($this->_orm === NULL && class_exists('Model_'.$this->_orm_name))
		{
			$this->_orm = ORM::factory($this->_orm_name, $this->request->param('id'));
		}
	}

	
	public function get_all()
	{

		$search_query = $this->request->query('q');
		if ($search_query)
		{
			$this->_orm->search($search_query);
			$this->hapi(['filters' => ['query' => $search_query]]);
		}

		$this->_per_page = (int) $this->request->query('items_per_page');

		if ($this->_per_page < 1 || $this->_per_page > self::MAX_ITEMS_PER_PAGE)
		{
			$this->_per_page = self::DEFAULT_ITEMS_PER_PAGE;
		}

		if ($this->request->query('direction'))
		{
			$this->_direction = strtolower($this->request->query('direction')) === 'desc' ? 'desc' : 'asc';
		}

		if ($this->_orderby = $this->request->query('orderby'))
		{
			$this->_direction = $this->request->query('direction');
			$this->_orm->hapi_order_by($this->_orderby, $this->_direction);
		}

		$total_rows_query = clone $this->_orm;
		$total_rows = $total_rows_query->count_all();

		$pagination = Pagination::factory(
			[
				'total_items'    => $total_rows,
				'items_per_page' => $this->_per_page,
				'current_page'   => ['page' => $this->request->query('p') ? $this->request->query('p') : 1]
			]
		);

		$this->_orm->offset($pagination->offset)
			->limit($pagination->items_per_page);


		$this->response_encoder->add_link(
			'next',
			URL::full_base(
				$this->request->uri().URL::query(['p' => $pagination->next_page])
			)
		);

		$this->response_encoder->add_link(
			'prev',
			URL::full_base(
				$this->request->uri().URL::query
					(
						['p' => $pagination->previous_page]
					)
			)
		);


		$this->hapi(
			[
				'pagination' => [
					'total_items'    => $total_rows,
					'items_per_page' => $this->_per_page,
					'total_pages'    => $pagination->total_pages,
					'current_page'   => $pagination->current_page
				],
				'filters'    => [
					'orderby' => [
						'direction' => $this->_direction,
						'key'       => $this->_orderby
					]
				]
			]
		);
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