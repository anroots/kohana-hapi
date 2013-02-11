<?php defined('SYSPATH') or die('No direct script access.');
interface Kohana_View_HAPI_Loadable
{

	/**
	 * @param Response $response
	 * @return View_HAPI_Loadable
	 */
	public function load(Response $response);

	/**
	 * @return View_HAPI_Loadable
	 */
	public function build();
}