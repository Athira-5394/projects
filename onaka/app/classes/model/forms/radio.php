<?php
class Model_Forms_Radio extends \Model_Forms_Abstract
{
	const ARRANGE_HORIZONTAL = 'horizontal';
	const ARRANGE_VERTICAL = 'vertical';

	private $options;
	private $arrangement;

	public function __construct($config, $value, $errors)
	{
		parent::__construct($config, $value, $errors);
		$this->options = \Arr::get($config, 'options');
		$this->arrangement = \Arr::get($config, 'arrangement', self::ARRANGE_VERTICAL);
	}

	protected function form()
	{
		$fid = 'form_'.$this->name.'_options';
		$flbl = $this->name.'_options';
		$form = '';
		foreach($this->options as $option)
		{
			$fid = \Str::increment($fid);
			$flbl = \Str::increment($flbl);
			$init = ! $this->_is_multi() || $this->value === $option['value'];
			$form .= \Form::radio($this->name, $option['value'], $init, array('id' => $fid));
			$form .= \Form::label($option['label'], $flbl);
			if($this->arrangement === self::ARRANGE_VERTICAL)
			{
				$form .= '<br/>';
			}
		}
		return $form;
	}

	private function _is_multi()
	{
		return count($this->options) > 1;
	}
}