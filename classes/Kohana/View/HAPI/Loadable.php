<?php defined('SYSPATH') or die('No direct script access.');
interface Kohana_View_HAPI_Loadable
{

	public function load(Response $response);
}