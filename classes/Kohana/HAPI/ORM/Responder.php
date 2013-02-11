<?php defined('SYSPATH') or die('No direct script access.');
trait Kohana_HAPI_ORM_Responder
{

	public function as_hapi_data()
	{
		$hapi_model = HAPI_Model::factory($this);
		return $hapi_model->transform();
	}
}