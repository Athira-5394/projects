<?php
class Model_Forms_File extends \Model_Forms_Abstract
{
	protected function form()
	{
		return \Form::file($this->name, $this->attr);
	}
}