<?php
class Model_Forms_Label extends \Model_Forms_Abstract
{
	protected function form()
	{
		return \Form::label(nl2br($this->html_value), $this->name, $this->attr);
	}
}