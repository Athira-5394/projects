<?php
class Controller_Mailmagazine_Entry extends \Controller_Template
{
	use \iwago\Snippet;

	public $template = 'main_template';

	public function before()
	{
		parent::before();
		static::set_system_info('mail_entry');
		foreach(\Input::post() as $k => $v)
		{
			$this->set_form_data($k, $v);
		}
	}

	public function action_provisional_entry()
	{
		$captcha = Captcha::forge('simplecaptcha')->html();
		$captcha->set('captcha_error', \Session::get_flash('form_error.simplecaptcha', ''));
		$data = array(
			'action' 	=> \Uri::create('mailmagazine/entry/provisional_complete'),
			'err_email' => \Session::get_flash('form_error.email', ''),
			'email'  	=> $this->get_form_data('email', ''),
			'captcha'	=> $captcha,
			'init'		=> $this->get_form_data('policy', ''),
		);
		#main_templateのtitleタグに設定タイトルを挿入
		$this->_set_breadcrumb();
		$this->template->set('body', \View::forge('mailmagazine/entry/provisional_entry', $data));
	}

	public function action_provisional_complete()
	{
		if( ! \Security::check_token())
		{
			throw new \iwago\TokenCheckException;
		}
		$val = $this->validation_entry();
		if( ! $val->run())
		{
			$errors = $val->error();
			foreach(array_keys($errors) as $key)
			{
				#\Log::warning($key.' error '.$val->error($key)->get_message());
				\Session::set_flash('form_error.'.$key, $val->error($key)->get_message());
			}
			\Response::redirect('mailmagazine/entry/provisional_entry');
		}
		$email = $this->get_form_data('email');
		try
		{
			if(Model_Mailinglists::exists($email))
			{
				\Log::debug('既に登録されているメールアドレス。'.$email);
				#main_templateのtitleタグに設定タイトルを挿入
				$this->_set_breadcrumb();
				static::delete_form_data();
				$this->template->set('body', \View::forge('share/entry_exists', array('title' => 'メルマガ会員仮登録')));
			}
			else
			{
				//仮登録を実施
				Model_Provisional::set_provisional($email);
				//ワンタイムURLを生成
				$expired = '';
				$url = Model_Onetimeurl::create_onetime_url($email, Model_Onetimeurl::CREATE, $expired);
				//ユーザーにワンタイムURLを通知
				Model_Mail::forge()->provisional_mail($email, $url, $expired, 'entry');
				#main_templateのtitleタグに設定タイトルを挿入
				$this->_set_breadcrumb();
				//仮登録完了画面へ遷移
				$this->template->set('body', \View::forge('mailmagazine/entry/provisional_complete'));
				//仮登録画面の出力後にセッション情報を破棄
				$this->delete_form_data();
			}
		}
		catch(\Database_Exception $e)
		{
			\Log::error($e->getTraceAsString());
			throw new \iwago\IwagoException($e->getMessage());
		}
	}

	public function action_constancy_confirm($keyword)
	{
		$data = array(
			'err_msg'		=> \Session::get_flash('form_error.mail', ''),
			'complete_url'	=> \Uri::create('mailmagazine/entry/constancy_complete'),
		);
		#正しいURLからのリクエストであるか
		try
		{
			if($mail = Model_Onetimeurl::verify_entry_url($keyword))
			{
				#既に会員登録済みか
				if(Model_Mailinglists::exists($mail))
				{
					#既に登録済みの場合はエラーメッセージを出力
					$data['err_msg'] = '既に登録済みです';
				}
				else
				{
					#確認画面に登録するメールアドレスを表示
					$data['email'] = $mail;
					#登録するキーワードをセッションに追加
					$this->set_form_data('keyword', $keyword);
				}
			}
			else
			{
				#無効なリクエスト
				$data['err_msg'] = 'たいへん申し訳ありませんが、使用されたURLは無効になっております';
			}
			#main_templateのtitleタグに設定タイトルを挿入
			$this->_set_breadcrumb();
			$this->template->set('body', \View::forge('mailmagazine/entry/constancy_confirm', $data));
		}
		catch(\Database_Exception $e)
		{
			\Log::error($e->getTraceAsString());
			throw new \iwago\IwagoException($e->getMessage());
		}
	}

	public function action_constancy_complete()
	{
		try
		{
			#正しいURLからのリクエストであるか
			if($mail = Model_Onetimeurl::verify_entry_url($this->get_form_data('keyword')))
			{
				#既に会員登録済みか
				if(Model_Mailinglists::exists($mail))
				{
				#既に登録済みの場合はエラーメッセージを出力
					$msg = '既に登録済みです';
					\Session::set_flash('form_error.mail', $msg);
					\Response::redirect('mailmagazine/entry/constancy_confirm/'.$this->get_form_data('keyword'));
				}
				else
				{
					#仮登録から削除
					Model_Provisional::delete_provisional($mail);
					#本登録を実施
					Model_Mailinglists::entry($mail);
					#ワンタイムURLを削除
					Model_Onetimeurl::delete_onetime_url($mail, Model_Onetimeurl::CREATE, $this->get_form_data('keyword'));
					#ユーザーに登録完了のメール
					Model_Mail::forge()->complete_mail($mail, 'entry');
					#main_templateのtitleタグに設定タイトルを挿入
					$this->_set_breadcrumb();
					#完了画面を表示
					$this->template->set('body', \View::forge('mailmagazine/entry/constancy_complete'));
				}
			}
			else
			{
				#無効なリクエスト
				$msg = 'たいへん申し訳ありませんが、使用されたURLは無効になっております';
				\Session::set_flash('form_error.mail', $msg);
				\Response::redirect('mailmagazine/entry/constancy_confirm/'.$this->get_form_data('keyword'));
			}
		}
		catch(\Database_Exception $e)
		{
			\Log::error($e->getTraceAsString());
			throw new \iwago\IwagoException($e->getMessage());
		}
	}

	private function validation_entry()
	{
		$val = \Validation::forge();
		$val->add_callable('\iwago\Rules');
		$val->add('email', 'メールアドレス')
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('max_length', 255);
		$val->add('simplecaptcha', '文字認証')
			->add_rule('required')
			->add_rule('captcha');
		return $val;
	}

	private function _set_breadcrumb()
	{
		\Lang::load('iwago/mailmagazine', 'magazine');
		$title = \Lang::get('magazine.entry.'.static::action());
		#main_templateのtitleタグに設定タイトルを挿入
		$this->template->set('breadcrumb', $title);
	}
}
