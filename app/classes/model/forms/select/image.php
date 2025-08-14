<?php
class Model_Forms_Select_Image extends Model_Forms_Abstract
{
	use \iwago\Snippet;

	protected function form()
	{
		return \Asset::img(static::get_form_data('_image_paths.'.$this->name.'.'.static::get_form_data($this->name)));
	}
}
