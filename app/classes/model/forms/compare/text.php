<?php
class Model_Forms_Compare_Text extends Model_Forms_Text
{
	use \iwago\Snippet;

	private $confirm_name;
	private $confirm_value;
	private $confirm_html_value;

	public function __construct($config, $value, $errors)
	{
		parent::__construct($config, $value, $errors);
		$this->confirm_config();
	}

	public function html()
	{
		$html = '<dl class="formitem clearfix">';
		$html .= '<dt>'.$this->item_label().'</dt>';
		$html .= '<dd>'.$this->input_form().'</dd>';
		$html .= '</dl>';
		$html .= '<dl class="formitem clearfix">';
		$html .= '<dt>&nbsp;</dt>';
		$html .= '<dd>'.$this->confirm_form().'</dd>';
		$html .= '</dl>';
		$html .= $this->description();
		return $html;
	}

	private function confirm_form()
	{
		$desc = new \Model_Forms_Description(array('text' => 'ご確認のため'.$this->label.'の再入力をお願いします', 'attribute' => 'class="notice"'));
		$form = $desc->html();
		$form .= \Form::input($this->confirm_name, $this->confirm_html_value, $this->attr);
		return $form;
	}

	private function confirm_config()
	{
		$this->confirm_name = $this->name.'_compare';
		$this->confirm_value = static::get_form_data($this->confirm_name, '');
		$this->confirm_html_value = htmlspecialchars($this->confirm_value);
	}
}