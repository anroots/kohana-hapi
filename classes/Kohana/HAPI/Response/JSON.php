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
}