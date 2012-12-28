<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Controller_HAPI_Server extends Controller
{

	/**
	 * @var HAPI_Response
	 */
	public $hapi_response;

	public function execute()
	{

		$this->_set_response_encoder();

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

	public function after()
	{

		$this->response->body($this->hapi_response->encode());
		parent::after();
	}

	private function _set_response_encoder()
	{

		$prefix = 'HAPI_Response_';
		$supported_encoders = Kohana::$config->load('hapi.encoders');

		$preferred_response_mime = $this->request->headers()
			->preferred_accept($supported_encoders);

		$encoder_to_use = array_search($preferred_response_mime, $supported_encoders);

		if (! $encoder_to_use) {
			throw new HTTP_Exception_406;
		}

		if (! class_exists($prefix.$encoder_to_use)) {
			throw new Kohana_Exception('Encoder for MIME type ":type" is configured, but no class found.', [
				':type' => $preferred_response_mime
			]);
		}

		$class = new ReflectionClass($prefix.$encoder_to_use);

		if ($class->isAbstract())
		{
			throw new Kohana_Exception(
				'Cannot create instances of abstract :class',
				array(':class' => $prefix.$encoder_to_use)
			);
		}

		$this->hapi_response = $class->newInstance($this->request, $this->response, $preferred_response_mime);
	}
}