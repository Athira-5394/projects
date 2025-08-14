<?php
class Model_Validation
{
	use \iwago\Snippet;

	public function __construct(){}

	public function inquiry($forms = array())
	{
		$val = \Validation::forge();
		$val->add_callable('\iwago\Rules');
		foreach($forms as $form)
		{
			if(array_key_exists('validation', $form) && ! empty($form['validation']))
			{
				$filedset = $val->add($form['name'], $form['label']);
				foreach($form['validation'] as $vtype)
				{
					$this->_select_validator($filedset, $vtype, $form);
				}
			}
		}
		return $val;
	}

	private function _select_validator(&$fieldset, $type, $form)
	{
		switch($type)
		{
			case 'require':
				$fieldset->add_rule('required');
			break;

			case 'maxlength':
				$key = $this->assoc_key_position($form, 'maxlength', false);
				if($key)
				{
					$max = \Arr::get($form, $key);
					$fieldset->add_rule('max_length', $max);
				}
			break;

			case 'email':
				$fieldset->add_rule('valid_email');
			break;

			case 'numeric':
				$fieldset->add_rule('valid_string', array('numeric'));
			break;

			case 'postcode':
				$fieldset->add_rule('postcode');
			break;

			case 'telephone':
				$fieldset->add_rule('telephone');
			break;

			case 'compare':
				$fieldset->add_rule('match_value', static::get_form_data($form['name'].'_compare'), true);
			break;

			case 'radio_select':
				$values = \Arr::pluck($form['options'], 'label', 'value');
				$fieldset->add_rule('radio_select', $values);
				static::set_form_data('_radio_opts.'.$form['name'], $values);
			break;

			case 'photo_select':
				$values = \Arr::pluck($form['images'], 'thumbnail', 'value');
				$fieldset->add_rule('photo_select', $values);
				static::set_form_data('_image_paths.'.$form['name'], $values);
			break;

			case 'present_select':
				$values = \Arr::pluck($form['images'], 'thumbnail', 'value');
				$fieldset->add_rule('present_select', $values);
				static::set_form_data('_image_paths.'.$form['name'], $values);
			break;

			case 'file_upload':
				$fieldset->add_rule('file_upload');
			break;

			default:
				\Log::warning('未実装のデータチェックが指定された。['.$type.']');
				#throw new \HttpServerErrorException;
			break;
		}
	}
}