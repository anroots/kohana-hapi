<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Indicate that a ViewModel class is capable of loading HAPI responses.
 * Used in Mustache-enabled views.
 */
interface Kohana_View_HAPI_Loadable
{

	/**
	 * Load API response.
	 *
	 * Typically just a setter.
	 *
	 * @param Response $response
	 * @return View_HAPI_Loadable
	 */
	public function load(Response $response);

	/**
	 * Handle, transform and set view properties from the loaded HAPI response.
	 *
	 * @return View_HAPI_Loadable
	 */
	public function build();
}