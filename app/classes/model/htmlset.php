<?php
class Model_Htmlset
{
	public static function create(array $config, $value = '', $errors = array())
	{
		\Config::load('input_forms', 'forms');
		$class = \Config::get('forms.'.$config['type'], false);
		if($class && class_exists($class))
		{
			return new $class($config, $value, $errors);
		}
		else
		{
			throw new \iwago\IwagoException('不正なフォームの種類('.$class.')');
		}
	}
}
