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

	public function encode()
	{
		return json_encode($this->_data);
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

		$this->_data = array_replace($this->_data, $data);
		return $this;
	}
}