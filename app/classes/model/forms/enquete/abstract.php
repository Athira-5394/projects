<?php
abstract class Model_Forms_Enquete_Abstract
{
	#アンケート設問
	protected $question;
	#アンケート用の入力フォーム
	protected $q_form;

	public function __construct($config, $value, $errors)
	{
		$this->question = \Arr::get($config, 'question', '');
		$this->q_form = $this->question_form($config, $value, $errors);
	}

	abstract protected function question_form($config, $value, $errors);

	public function html()
	{
		$html = '<dl class="formitem clearfix">';
		$html .= '<dd>'.$this->question.'</dd>';
		$html .= '</dl>';
		$html .= $this->q_form->q_html();
		return $html;
	}
}