<?php defined('SYSPATH') or die('No direct script access.');
class Kohana_HAPI_Response_HAL_XML extends Kohana_HAPI_Response_Encoder implements HAPI_Response_Encodable
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
		foreach ($this->_links as $link)
		{
			$hal->addLink($link[0], $link[1], $link[2], $link[3]);
		}
		return $hal->asXml();
	}
}