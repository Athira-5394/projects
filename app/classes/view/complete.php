<?php
abstract class View_Complete extends \ViewModel
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
	protected function optional_info() {}

	public function view()
	{
		$config = $this->config_load();
		$this->title = \Arr::get($config, 'title');
		$this->readtext = \Arr::get($config, 'readtext');
		$this->css = \Arr::get($config, 'css', array());
		$this->js = \Arr::get($config, 'js', array());
		$this->set_link();
		$this->optional_info();
	}
}