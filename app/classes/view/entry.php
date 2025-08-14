<?php
abstract class View_Entry extends \ViewModel
{
	use \iwago\Snippet;

	/**
	 * コンテンツ要素の設定を読み込みそのデータ返すメソッド。
	 */
	abstract protected function config_load();

	/**
	 * 画面遷移のためのリンク(ボタン)要素を設定するメソッド。
	 */
	abstract protected function set_link();

	/**
	 * 画面構成でデフォルト以外に必要なデータを追加するメソッド。
	 * 必要に応じてオーバーライドすること。
	 */
	protected function optional_info()
	{
		$this->init = static::get_form_data('policy', false);
	}

	/**
	 * 画面構成でデフォルト以外に必要なJSとCSSを追加するメソッド。
	 * 必要に応じてオーバーライドすること。
	 */
	protected function set_js_and_css($config)
	{
		$this->css = \Arr::get($config, 'css', array());
		$this->js = \Arr::get($config, 'js', array());
	}

	public function view()
	{
		$config = $this->config_load();
		$items = \Arr::get($config, 'item', array());
		$forms = array();
		$errors = array();
		foreach($items as $item)
		{
			$value = static::get_form_data($item['name'], false) ? : '';
			$err_msg = \Session::get_flash('form_error.'.$item['name'], false);
			$comp_flg = false;
			if($err_msg)
			{
				$errors[$item['name']] = $err_msg;
			}
			$forms[] = \Model_Htmlset::create($item, $value, $errors);
		}
		$this->title = \Arr::get($config, 'title');
		$this->readtext = \Arr::get($config, 'readtext');
		$this->set_js_and_css($config);
		$this->set_link();
		$this->optional_info();
		$this->set('forms', $forms, false);
	}

}