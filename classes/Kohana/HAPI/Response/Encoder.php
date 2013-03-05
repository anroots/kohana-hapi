<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
class Kohana_HAPI_Response_Encoder implements HAPI_Response_Encodable
{

	/**
	 * @var array Output data that will be sent as HTTP body
	 * @since 1.0
	 */
	protected $_data;

	/**
	 * @var string HTTP Content-Type header value
	 * @since 1.0
	 */
	protected $_content_type;

	/**
	 * @var Request
	 * @since 1.0
	 */
	protected $_request;

	/**
	 * @var Response
	 * @since 1.0
	 */
	protected $_response;

	/**
	 * @var array
	 */
	protected $_links = [];

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param string $content_type
	 * @since 1.0
	 */
	public function __construct(Request $request, Response $response, $content_type)
	{
		$this->_response = $response;
		$this->_request = $request;
		$this->_content_type = $content_type;
	}

	/**
	 * @return string
	 * @since 1.0
	 */
	public function content_type()
	{
		return $this->_content_type;
	}

	/**
	 * @param array $data
	 * @return \Kohana_HAPI_Response_Encoder
	 * @since 1.0
	 */
	public function set_data($data)
	{
		$this->_data = $data;
		return $this;
	}

	/**
	 * @return string
	 * @since 1.0
	 */
	public function encode()
	{
		return $this->_data;
	}

	/**
	 * Filter the data by a list of array paths.
	 * All items not specified in the array of paths will be excluded from the response.
	 * The response is a single-level array with the paths as the keys.
	 *
	 * @see Arr::path
	 * @param array $paths
	 * @return \Kohana_HAPI_Response_Encoder
	 */
	public function filter_paths(array $paths)
	{
		if (empty($paths))
		{
			return $this;
		}

		$filtered_data = [];
		foreach ($paths as $path)
		{
			$filtered_data[$path] = Arr::path($this->_data, $path);
		}
		$this->_data = $filtered_data;
		return $this;
	}

	/**
	 * Implement response data sorting function.
	 * Override and return the data unchanged to disable
	 *
	 * @param $response_data
	 * @return bool
	 */
	public function sort_response($response_data)
	{
		asort($response_data);
		return $response_data;
	}

	/**
	 * @param $data
	 * @return Kohana_HAPI_Response_Encoder
	 */
	public function add_data($data)
	{
		if ($this->_data === NULL)
		{
			return $this->set_data($data);
		}

		$this->_data = array_replace_recursive($this->_data, $data);
		return $this;
	}

	/**
	 * @param $rel
	 * @param $uri
	 * @param null $title
	 * @param array $attributes
	 */
	public function add_link($rel, $uri, $title = NULL, array $attributes = [])
	{
		$this->_links[] = [$rel, $uri, $title, $attributes];
	}
}