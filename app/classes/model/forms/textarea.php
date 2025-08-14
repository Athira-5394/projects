<?php
class Model_Forms_Textarea extends \Model_Forms_Abstract
{
	protected function form()
	{
		return \Form::textarea($this->name, $this->html_value, $this->attr);
	}
}