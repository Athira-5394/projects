<?php
class Model_Forms_Enquete_Text extends \Model_Forms_Enquete_Abstract
{
	protected function question_form($config, $value, $errors)
	{
		return new Model_Forms_Text($config, $value, $errors);
	}
}