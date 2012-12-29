<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Todo
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Ando Roots <ando@sqroot.eu>
 * @since 1.0
 * @package Kohana/HAPI
 * @copyright (c) 2012, Ando Roots
 */
class Kohana_HAPI_Response
{

	public static function parse(Response $response)
	{
		return json_decode($response->body());
	}
}