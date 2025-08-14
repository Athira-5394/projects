<?php
class Model_Forms_Enquete_Radio extends \Model_Forms_Enquete_Abstract
{
	protected function question_form($config, $value, $errors)
	{
		return new Model_Forms_Radio($config, $value, $errors);
	}
}