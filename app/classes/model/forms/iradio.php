<?php
class Model_Forms_Iradio extends \Model_Forms_Abstract
{
	const ARRANGE_HORIZONTAL = 'horizontal';
	const ARRANGE_VERTICAL = 'vertical';

	private $images;
	private $arrangement;

	public function __construct($config, $value, $errors)
	{
		parent::__construct($config, $value, $errors);
		$this->images = \Arr::get($config, 'images');
		$this->arrangement = \Arr::get($config, 'arrangement', self::ARRANGE_HORIZONTAL);
	}

	protected function form()
	{
		$fid = 'form_images';
		$flbl = 'images';
		$form = '';
		foreach($this->images as $image)
		{
			$fid = \Str::increment($fid);
			$flbl = \Str::increment($flbl);

			$radio = '<div class="img_left text_ac">';
			$radio .= \Html::anchor(\Asset::get_file($image['browsing'], 'img'), \Asset::img($image['thumbnail']), array('rel' => 'lightbox'));
			$radio .= '<br/><br/>';
			if (! $this->_is_multi())
			{
				$radio .= \Form::hidden($this->name, $image['value'], array('id' => $fid));	
			}
			else
			{	
				$init = $this->value === $image['value'];
				$radio .= \Form::radio($this->name, $image['value'], $init, array('id' => $fid));
			}

			if($text = \Arr::get($image, 'label', false))
			{
				$radio .= $text;
			}
			$radio .= '</div>';
			if($this->arrangement === self::ARRANGE_VERTICAL)
			{
				$form .= '<br/>';
			}
			$form .= \Form::label($radio, $flbl);
		}
		return $form;
	}

	protected function item_label()
	{
		$lbl =  \Form::label($this->label, $this->name);
		if( ! empty($this->hint))
		{
			$lbl .= '<br/>';
			$lbl .= \Form::label($this->hint, $this->name, array('class' => 'hint'));
		}
		return $lbl;
	}

	private function _is_multi()
	{
		return count($this->images) > 1;
	}
}