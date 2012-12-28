<?php defined('SYSPATH') or die('No direct script access.');
abstract class Kohana_HAPI_Response
{

	protected $_data;

	public $content_type;

	protected $_request;
	protected $_response;

	public function __construct(Request $request, Response $response, $content_type)
	{
		$this->_response = $response;
		$this->_request = $request;
	}


	public function content_type()
	{
			return $this->content_type;
	}

	public function data($data)
	{
		$this->_data = $data;
	}

	abstract public function encode();
}