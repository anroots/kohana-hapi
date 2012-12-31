<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
class Kohana_HAPI_Response_JSON extends Kohana_HAPI_Response_Encoder implements HAPI_Response_Encodable
{

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
	 * Encode the response data
	 *
	 * @since 1.0
	 * @return string Encoded data
	 */
	public function encode()
	{
		return json_encode($this->sort_response($this->_data));
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
}