<?php
class Model_Forms_Description extends \Model_Forms_Abstract
{
	private $text;

	public function __construct($config)
	{
		$this->text = \Arr::get($config, 'text', '');
		$this->attr = \Arr::get($config, 'attribute', array());
	}

	protected function form(){}

	public function html()
	{
		return empty($this->text) ? '' : html_tag('p', $this->attr, $this->text);
	}
}