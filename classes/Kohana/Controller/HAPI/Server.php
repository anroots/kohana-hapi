<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Controller_HAPI_Server extends Controller
{

	public function execute()
	{
		// Execute the "before action" method
		$this->before();

		// Determine the action to use
		// Defaults to the HTTP verb used
		$action = $this->request->method();

		// Action (if not default) is appended to the HTTP verb
		if ($this->request->action() !== Route::$default_action) {
			$action .= '_'.$this->$this->request->action();
		}

		// If the action doesn't exist, it's a 404
		if (! method_exists($this, $action)) {
			throw new HTTP_Exception_404(
				'The requested URL :uri was not found on this server.',
				array(':uri' => $this->request->uri())
			);
		}

		// Execute the action itself
		$this->{$action}();

		// Execute the "after action" method
		$this->after();

		// Return the response
		return $this->response;
	}
}