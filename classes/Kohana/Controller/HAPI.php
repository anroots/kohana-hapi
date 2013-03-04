<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Abstract controller for the main API controller to extend.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
abstract class Kohana_Controller_HAPI extends Controller
{

	/**
	 * @var string Current API version. Might be different between Controller instances.
	 */
	protected $_api_version = '1.0';

	/**
	 * Instance of a Response_Encoder class whose purpose is to
	 * hold the response body data and transform it into the appropriate format (JSON, XML)
	 *
	 * @var HAPI_Response_Encoder
	 * @since 1.0
	 */
	public $response_encoder;


	/**
	 * @since 1.0
	 * @var bool
	 */
	protected $_include_metadata = TRUE;

	/**
	 * @var array An array of paths to return. Defaults to everything.
	 */
	protected $_paths = [];


	/**
	 * @param Request $request
	 * @param Response $response
	 * @since 1.0
	 */
	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		$this->_base_url = URL::base('http').'api/';
	}

	/**
	 * @throws HTTP_Exception_401
	 * @since 1.0
	 */
	public function before()
	{
		parent::before();

		// Check request signature
		if (Kohana::$config->load('hapi.require_signature')
			&& ! HAPI_Security::is_request_signature_valid($this->request)
		)
		{
			// Todo: change to 401
			throw new HTTP_Exception_400('Request signature was invalid');
		}

		// Login using basic auth
		if (array_key_exists('authorization', $this->request->headers()))
		{
			HAPI_Security::login($this->request->headers('authorization'));
		}

		if (Kohana::$config->load('hapi.require_login')
			&& ! HAPI_Security::is_request_authenticated($this->request)
		)
		{
			$http_401 = new HTTP_Exception_401('Login required');
			$http_401->authenticate('Login'); // Todo
			$http_401->request($this->request);
			throw $http_401;
		}

		$this->_include_metadata = Kohana::$config->load('hapi.include_metadata');

		// Instantiate the encoder object for the response (based on the Accept header)
		$this->response_encoder = $this->_get_response_encoder();


		// Set current language
		$supported_languages = Kohana::$config->load('hapi.supported_languages');
		$preferred_language = $this->request->headers()->preferred_language($supported_languages);

		if ($preferred_language)
		{
			I18n::lang($preferred_language);
		}

		$this->_paths = $this->_extract_array($this->request->query('paths'));

	}

	/**
	 * Executes the current controller.
	 *
	 * Action takes into account the HTTP verb
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
		if ($this->request->method() === HTTP_Request::PUT)
		{
			$this->{$action}($this->_extract_put_data($this->request->body()));
		} else
		{
			$this->{$action}();
		}
		// Execute the "after action" method
		$this->after();

		// Return the response
		return $this->response;
	}

	/**
	 * Extract PUT data into an array.
	 *
	 * @since 1.0
	 * @param $request_body
	 * @return array
	 */
	private function _extract_put_data($request_body)
	{
		parse_str($request_body, $put_data);
		return $put_data;
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
	 * @since 1.0
	 */
	public function after()
	{
		$this->response_encoder->filter_paths($this->_paths);

		// Set the response body - ask HAPI encoder to transform its data into string
		$this->response->body($this->response_encoder->encode());

		// For AJAX queries
		$this->response->headers('Access-Control-Allow-Origin', Kohana::$config->load('hapi.allow_origin'));

		parent::after();
	}

	/**
	 * Shorthand for setting the response data from the controller
	 *
	 * @since 1.0
	 * @param array $data
	 * @return Kohana_Controller_HAPI_Provider
	 */
	public function hapi($data)
	{
		$this->response_encoder->add_data($data);
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

		if (! $preferred_response_mime)
		{
			throw new HTTP_Exception_406;
		}

		// Get the class name of the encoder to use to transform data into the appropriate Content-Type
		$encoder_to_use = $supported_encoders[$preferred_response_mime];

		// Encoder class not found
		if (! class_exists($prefix.$encoder_to_use))
		{
			throw new Kohana_Exception('Encoder for MIME type ":type" is configured, but no class found.', [
				':type' => $preferred_response_mime
			]);
		}

		$class = new ReflectionClass($prefix.$encoder_to_use);

		if ($class->isAbstract() || ! $class->implementsInterface('HAPI_Response_Encodable'))
		{
			throw new Kohana_Exception(
				'Response encoder - :class - must be a concrete class that implements HAPI_Response_Encodable',
				array(':class' => $prefix.$encoder_to_use)
			);
		}

		// Instantiate the encoder
		$hapi_encoder = $class->newInstance($this->request, $this->response, $preferred_response_mime);

		// Set response content type
		$this->response->headers('Content-Type', $hapi_encoder->content_type());

		// Add metadata to the response such as response generation time
		if ($this->_include_metadata)
		{
			$hapi_encoder->add_data($this->get_metadata());
		}

		return $hapi_encoder;
	}

	/**
	 * @return array
	 * @since 1.0
	 */
	public function get_metadata()
	{
		$protocol = $this->request->secure() ? 'https' : 'http';
		return [
			'api_version' => $this->_api_version,
			'generated'   => time(),
			'_links'      => [
				'self' => ['href' => URL::base($protocol).$this->request->uri()]
			],
		];
	}


	private function _extract_array($keys)
	{
		if (empty($keys))
		{
			return [];
		}
		return explode(',', $keys);
	}
}