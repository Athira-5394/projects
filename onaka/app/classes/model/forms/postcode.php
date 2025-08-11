<?php
class Model_Forms_Postcode extends \Model_Forms_Abstract
{

	public function html()
	{
		$html = '<dl class="formitem clearfix">';
		$html .= '<dt>'.$this->item_label().'</dt>';
		$html .= '<dd>'.$this->input_form().$this->postcode_btn().'</dd>';
		$html .= '</dl>';
		$html .= $this->description();
		return $html;
	}

	protected function form()
	{
		return \Form::input($this->name, $this->html_value, $this->attr);
	}

	private function postcode_btn()
	{
		return \Form::input('post_btn', '住所自動入力', array('type' => 'button', 'id' => 'post_btn', 'class' => 'postcode',));
	}
}