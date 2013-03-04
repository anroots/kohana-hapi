<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @link http://stateless.co/hal_specification.html
 */
class Kohana_HAPI_Response_HAL_JSON extends Kohana_HAPI_Response_Encoder implements HAPI_Response_Encodable
{
	/**
	 * Encode the response data
	 *
	 * @since 1.0
	 * @return string Encoded data
	 */
	public function encode()
	{
		$hal = new \Nocarrier\Hal(Request::current()->uri(), $this->_data);
		return $hal->asJson();
	}
}