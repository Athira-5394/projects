<?php
abstract class Model_Forms_Abstract
{
	#入力フォームのname属性
	protected $name;
	#入力フォームの項目名
	protected $label;
	#項目名に挿入する追加文字列
	protected $hint;
	#入力フォームの下部に出力する説明文のクラス
	protected $descript;
	#入力フォームの追加属性の設定
	protected $attr;
	#入力フォームの必須かどうか
	protected $require;
	#ユーザーの入力値(htmlspecialchars適用前)
	protected $value;
	#ユーザーの入力値(htmlspecialchars適用後)
	protected $html_value;
	#入力値の検証によるエラー情報リスト
	protected $errors;

	public function __construct(array $config, $value = '', array $errors = array())
	{
		$this->value = $value;
		$this->html_value = htmlspecialchars($value);
		$this->name = \Arr::get($config, 'name', '');
		$this->label = \Arr::get($config, 'label', '');
		$this->hint = \Arr::get($config, 'hint', '');
		$this->descript = new \Model_Forms_Description(\Arr::get($config, 'description', array()));
		$this->attr = \Arr::get($config, 'attribute', array());
		$this->require = \Arr::get($config, 'require', false);
		$this->errors = $errors;
	}

	public function html()
	{
		$html = '<dl class="formitem clearfix">';
		$html .= '<dt>'.$this->item_label().'</dt>';
		$html .= '<dd>'.$this->input_form().'</dd>';
		$html .= '</dl>';
		$html .= $this->description();
		return $html;
	}

	public function q_html()
	{
		$html = '<dl class="formitem clearfix mar_b30">';
		$html .= '<dd class="pad_l30">'.$this->input_form().'</dd>';
		$html .= '</dl>';
		$html .= $this->description();
		return $html;
	}

	abstract protected function form();

	protected function input_form()
	{
		$form = '';
		if($err = \Arr::get($this->errors, $this->name, false))
		{
			$form .= html_tag('span', array('class' => 'formerror'), $err);
		}
		$form .= $this->form();
		return $form;
	}

	protected function item_label()
	{
		$lbl = ! empty(trim($this->label)) ?  \Form::label($this->label, $this->name) : '&nbsp;';
		if($this->require && $lbl !== '&nbsp;')
		{
			$lbl .= \Form::label(' *', $this->name, array('class' => 'star'));
		}
		if( ! empty($this->hint))
		{
			$lbl .= '<br/>';
			$lbl .= \Form::label($this->hint, $this->name, array('class' => 'hint'));
		}
		return $lbl;
	}

	/*
	protected function errors()
	{
		if(isset($errors[$name]) && ! empty($errors[$name]))
		{
			if(\Arr::is_multi($row, true))
			{
				if($config['type'] === 'q_textarea')
				{
					$row[1]['input'] = html_tag('span', array('class' => 'formerror'), $errors[$name]).$row[1]['input'];
				}
				else
				{
					#入力確認が存在する場合、エラーメッセージはオリジナルに表示する。
					$row[0]['input'] = html_tag('span', array('class' => 'formerror'), $errors[$name]).$row[0]['input'];
				}
			}
			else
			{
				$row['input'] = html_tag('span', array('class' => 'formerror'), $errors[$name]).$row['input'];
			}
		}
	}
	*/

	protected function description()
	{
		return $this->descript->html();
	}
}