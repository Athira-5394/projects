<?php
class Model_Forms_Selectbox extends \Model_Forms_Abstract
{
	protected function form()
	{
		return \Form::select($name, $html_value, $options, $attr);
	}
}