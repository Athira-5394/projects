<?php
class Model_Forms_Text extends \Model_Forms_Abstract
{
	protected function form()
	{
		return \Form::input($this->name, $this->html_value, $this->attr);
	}
}