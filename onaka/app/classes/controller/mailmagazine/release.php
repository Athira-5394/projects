<?php
class Controller_Mailmagazine_Release extends \Controller_Template
{
	use \iwago\Snippet;

	public $template = 'main_template';

	public function before()
	{
		parent::before();
		static::set_system_info('mail_release');
		foreach(\Input::post() as $k => $v)
		{
			$this->set_form_data($k, $v);
		}
	}

	public function action_provisional_release()
	{

		$captcha = Captcha::forge('simplecaptcha')->html();
		$captcha->set('captcha_error', \Session::get_flash('form_error.simplecaptcha', ''));
		$data = array(
				'action' 	=> \Uri::create('mailmagazine/release/provisional_complete'),
				'err_email' => \Session::get_flash('form_error.email', ''),
				'email'  	=> $this->get_form_data('email', ''),
				'captcha'	=> $captcha,
				'init'		=> $this->get_form_data('policy', ''),
		);
		#main_templateのtitleタグに設定タイトルを挿入
		$this->_set_breadcrumb();
		$this->template->set('body', \View::forge('mailmagazine/release/provisional_release', $data));
	}

	public function action_provisional_complete()
	{
		if( ! \Security::check_token())
		{
			throw new \iwago\TokenCheckException;
		}
		$val = $this->validation_release();
		if( ! $val->run())
		{
			$errors = $val->error();
			foreach(array_keys($errors) as $key)
			{
				#\Log::warning($key.' error '.$val->error($key)->get_message());
				\Session::set_flash('form_error.'.$key, $val->error($key)->get_message());
			}
			\Response::redirect('mailmagazine/release/provisional_release');
		}
		$email = $this->get_form_data('email');
		try
		{
			if( ! Model_Mailinglists::exists($email))
			{
				\Log::warning('入力されたメールアドレスがDBに存在しない。'.$email);
				\Session::set_flash('form_error.email', 'メールアドレスを見直してください');
				\Response::redirect('mailmagazine/release/provisional_release');
			}
			//ワンタイムURLを生成
			$expired = '';
			$url = Model_Onetimeurl::create_onetime_url($email, Model_Onetimeurl::DELETE, $expired);
			//ユーザーにワンタイムURLを通知
			Model_Mail::forge()->provisional_mail($email, $url, $expired, 'release');
			#main_templateのtitleタグに設定タイトルを挿入
			$this->_set_breadcrumb();
			//仮解除完了画面へ遷移
			$this->template->set('body', \View::forge('mailmagazine/release/provisional_complete'));
			//仮解除完了画面の出力後にセッション情報を破棄
			$this->delete_form_data();
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
				'err_msg'		=> \Session::get_flash('form_error.email', ''),
				'complete_url'	=> \Uri::create('mailmagazine/release/constancy_complete'),
		);
		try
		{
			#正しいURLからのリクエストであるか
			if($mail = Model_Onetimeurl::verify_release_url($keyword))
			{
				#既に会員解除済みか
				if( ! Model_Mailinglists::exists($mail))
				{
					#既に登録済みの場合はエラーメッセージを出力
					$data['err_msg'] = '既に解除済みです';
				}
				else
				{
					#確認画面に登録するメールアドレスを表示
					$data['email'] = $mail;
					#登録するメールアドレスとキーワードをセッションに追加
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
			$this->template->set('body', \View::forge('mailmagazine/release/constancy_confirm', $data));

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
			if($mail = Model_Onetimeurl::verify_release_url($this->get_form_data('keyword')))
			{
				#既に会員解除済みか
				if( ! Model_Mailinglists::exists($mail))
				{
					#既に解除済みの場合はエラーメッセージを出力
					$msg = '既に解除済みです';
					\Session::set_flash('form_error.mail', $msg);
					\Response::redirect('mailmagazine/release/constancy_confirm');
				}
				else
				{
					#本登録を実施
					Model_Mailinglists::delete($mail);
					#メール配信除外に存在する場合は削除
					Model_Omit::delete_omit($mail);
					#ワンタイムURLを削除
					Model_Onetimeurl::delete_onetime_url($mail, Model_Onetimeurl::DELETE, $this->get_form_data('keyword'));
					#main_templateのtitleタグに設定タイトルを挿入
					$this->_set_breadcrumb();
					#完了画面を表示
					$this->template->set('body', \View::forge('mailmagazine/release/constancy_complete'));
				}
			}
			else
			{
				#無効なリクエスト
				$msg = 'たいへん申し訳ありませんが、使用されたURLは無効になっております';
				\Session::set_flash('form_error.email', $msg);
				\Response::redirect('mailmagazine/release/constancy_confirm/'.$this->get_form_data('keyword'));
			}

		}
		catch(\Database_Exception $e)
		{
			\Log::error($e->getTraceAsString());
			throw new \iwago\IwagoException($e->getMessage());
		}
	}

	private function validation_release()
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
		$title = \Lang::get('magazine.release.'.static::action());
		#main_templateのtitleタグに設定タイトルを挿入
		$this->template->set('breadcrumb', $title);
	}
}