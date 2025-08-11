<?php
class Model_Forms_Select_Radio extends Model_Forms_Abstract
{
	use \iwago\Snippet;

	protected function form()
	{
		return \Form::label(static::get_form_data('_radio_opts.'.$this->name.'.'.static::get_form_data($this->name)));
	}
}
