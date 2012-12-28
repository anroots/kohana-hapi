<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
abstract class Kohana_HAPI_Response
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
	 * @since 1.0
	 */
	public function set_data(array $data)
	{
		$this->_data = $data;
	}

	/**
	 * @return string
	 * @since 1.0
	 */
	abstract public function encode();
}