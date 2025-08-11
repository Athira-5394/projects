<?php
class Controller_Admin extends \Controller_Template
{
	use \iwago\Snippet;

	public $template = 'main_template';

	public function before()
	{
		parent::before();
		static::set_system_info('admin');
		foreach(\Input::post() as $k => $v)
		{
			$this->set_form_data($k, $v);
		}
	}

	public function action_login()
	{
		$init = array(
			'action'  => \Uri::create('admin/auth'),
			'err_msg' => \Session::get_flash('admin.err_msg'),
			'account' => $this->get_form_data('account', ''),
		);
		static::delete_form_data();
		#main_templateのtitleタグに設定タイトルを挿入
		$this->_set_breadcrumb();
		$this->template->set('body', \View::forge('admin/index', $init));
	}

	public function action_auth()
	{
		if( ! \Security::check_token())
		{
			throw new \iwago\IwagoException('CSRF対策用のトークンチェックに失敗しました。');
		}
		if(Model_Auth::login($this->get_form_data('account'), $this->get_form_data('password')))
		{
			\Response::redirect('admin/menu');
		}
		else
		{
			\Session::set_flash('err_msg', 'アカウント名とパスワードを確認の上、再度入力してください。');
			\Response::redirect('admin/login');
		}
	}

	public function action_menu()
	{
		Model_Auth::check();
		static::delete_form_data();
		$links = array(
			'maillist'	=> \Uri::create('admin/maillist'),
			'logout'	=> \Uri::create('admin/logout'),
		);
		#main_templateのtitleタグに設定タイトルを挿入
		$this->_set_breadcrumb();
		$this->template->set('body', \View::forge('admin/menu', $links));
	}

	public function action_logout()
	{
		Model_Auth::logout();
		\Response::redirect('admin/login');
	}

	public function action_maillist()
	{
		Model_Auth::check();
		$data = array(
				'search_url'	=> \Uri::create('admin/msearch'),
				'backlink'		=> \Uri::create('admin/menu'),
				'csv_url' 		=> \Uri::create('admin/csvoutput'),
		);
		#main_templateのtitleタグに設定タイトルを挿入
		$this->_set_breadcrumb();
		$this->template->set('body', \View::forge('admin/maillist', $data));
	}

	public function action_msearch()
	{
		Model_Auth::check();
		$emails = $this->get_form_data('emails', '');
		$delflg = $this->get_form_data('delflg') === 'true' ? true : false;
		$list_data = Model_Mailinglists::search($emails, $delflg);
		\Session::set_flash('emails', $emails);
		\Session::set_flash('delflg', $delflg);
		//\Log::debug(print_r($list_data, true));
		return new \Response(\View::forge('share/list', $list_data));
	}

	public function action_csvoutput()
	{
		Model_Auth::check();
		\Log::warning('['.\Model_Auth::get_account_id().'] execute mailing_list csv output.');
		$this->template = null;
		$emails = $this->get_form_data('emails', '');
		$delflg = $this->get_form_data('delflg') === 'true' ? true : false;
		$data = \Model_Mailinglists::csv($emails, $delflg);
		$response = new \Response($data);
		$response->set_header('Content-Type', 'application/csv');
		$response->set_header('Content-Disposition', 'attachment; filename="mailing_lists.csv"');
		return $response;
	}

	private function _set_breadcrumb()
	{
		\Lang::load('iwago/admin', 'admin');
		$title = \Lang::get('admin.'.static::action());
		#main_templateのtitleタグに設定タイトルを挿入
		$this->template->set('breadcrumb', $title);
	}
}
