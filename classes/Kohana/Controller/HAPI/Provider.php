<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Abstract data provider controller.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
abstract class Kohana_Controller_HAPI_Provider extends Controller
{

	/**
	 * @since 1.0
	 */
	const DEFAULT_API_VERSION = '1.0';

	/**
	 * @var HAPI_Response
	 * @since 1.0
	 */
	public $hapi_response;

	/**
	 * @var bool
	 * @since 1.0
	 */
	protected $_use_uniform_get = TRUE;

	/**
	 * @var string
	 */
	private $_version;


	public function get_version()
	{
		return $this->_version;
	}

	public function before()
	{
		parent::before();

		// Instantiate the encoder object for the response (based on the Accept header)
		$this->hapi_response = $this->_get_response_encoder();

		$this->_version = $this->_parse_version($this->hapi_response->content_type());
	}

	/**
	 * Executes the current action, considering the HTTP verb
	 *
	 * @return Response
	 * @throws HTTP_Exception_404
	 * @since 1.0
	 */
	public function execute()
	{
		// Execute the "before action" method
		$this->before();

		// Determine the Controller method to execute
		$action = $this->_determine_action();

		// Execute the method itself
		$this->{$action}();

		// Execute the "after action" method
		$this->after();

		// Return the response
		return $this->response;
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
		if ($this->request->action() !== Route::$default_action) {
			$action .= '_'.$this->request->action();
		} elseif (! $this->_use_uniform_get) {
			$action .= $this->request->param('id') === NULL ? '_all' : '_one';
		}

		// If the action doesn't exist, it's a 404
		if (! method_exists($this, $action)) {
			throw new HTTP_Exception_404(
				'The requested URL :uri was not found on this server.',
				array(':uri' => $this->request->uri())
			);
		}
		return $action;
	}

	/**
	 * @since 1.0
	 */
	public function after()
	{
		// Set the response body - ask HAPI encoder to transform its data into string
		$this->response->body($this->hapi_response->encode());
		parent::after();
	}

	/**
	 * Shorthand
	 *
	 * @since 1.0
	 * @param array $data
	 * @return Kohana_Controller_HAPI_Provider
	 */
	public function hapi(array $data)
	{
		$this->hapi_response->set_data($data);
		return $this;
	}

	/**
	 * Instantiates the HAPI response encoder.
	 *
	 * @throws Kohana_Exception
	 * @throws HTTP_Exception_406
	 * @since 1.0
	 */
	private function _get_response_encoder()
	{

		// Encoder classes are under HAPI/Response/
		$prefix = 'HAPI_Response_';

		// Config file maps supported encoder classes with MIME types
		$supported_encoders = Kohana::$config->load('hapi.encoders');

		// Get the MIME type to use as the Response Content-Type
		$preferred_response_mime = $this->request->headers()
			->preferred_accept(array_keys($supported_encoders));

		if (! $preferred_response_mime) {
			throw new HTTP_Exception_406;
		}

		// Get the class name of the encoder to use to transform data into the appropriate Content-Type
		$encoder_to_use = $supported_encoders[$preferred_response_mime];

		// Encoder class not found
		if (! class_exists($prefix.$encoder_to_use)) {
			throw new Kohana_Exception('Encoder for MIME type ":type" is configured, but no class found.', [
				':type' => $preferred_response_mime
			]);
		}

		$class = new ReflectionClass($prefix.$encoder_to_use);

		if ($class->isAbstract() || ! $class->implementsInterface('HAPI_Response_Encodable')) {
			throw new Kohana_Exception(
				'Response encoder - :class - must be a concrete class that implements HAPI_Response_Encodable',
				array(':class' => $prefix.$encoder_to_use)
			);
		}

		// Instantiate the encoder
		$hapi_response = $class->newInstance($this->request, $this->response, $preferred_response_mime);

		// Set response content type
		$this->response->headers('Content-Type', $hapi_response->content_type());

		return $hapi_response;
	}

	/**
	 * Parse the HTTP Content-Type header and return current API version
	 * Application APIs should be versioned by the following convention:
	 *
	 *     application/vnd.vendor.application-v2.0+json
	 *
	 * ...where vendor, application [second one] are placeholders
	 *
	 * @param string $content_type
	 * @return string Current API version or 1.0 as the default
	 * @since 1.0
	 */
	private function _parse_version($content_type)
	{
		preg_match('/-v(.*)\+/', $content_type, $matches);
		return count($matches) === 2 ? $matches[1] : self::DEFAULT_API_VERSION;
	}
}