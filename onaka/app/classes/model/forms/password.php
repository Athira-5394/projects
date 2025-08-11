<?php
class Model_Forms_Password extends \Model_Forms_Abstract
{
	protected function form()
	{
		return \Form::password($this->name, $this->html_value, $this->attr);
	}
}