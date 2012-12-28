<?php defined('SYSPATH') or die('No direct script access.');
class Kohana_HAPI_Response_JSON extends HAPI_Response
{

	public function encode()
	{
		return json_encode($this->_data);
	}
}