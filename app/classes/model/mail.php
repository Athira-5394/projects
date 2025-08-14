<?php
class Model_Mail
{
	use \iwago\Snippet;
	//置換文字とエスケープ文字の定義。ここの値は変更しないこと。
	const REPLACE_CHAR = ':';
	const ESCAPE_CHAR = '#';

	public function __construct()
	{
		//\Config::load('email', true);
	}

	public static function forge()
	{
		return new Model_Mail();
	}

	public function run($config)
	{
		try
		{
			$subject = $this->message_replace($config['subject']);
			$msg = $this->message_replace($config['msg'], array('datetime' => date('Y/m/d H:i:s')));
			$this->_sendmail($config['to'], $config['from'], $subject, $msg, $config['cc'], $config['bcc']);
		}
		catch(\FuelException $e)
		{
			\Log::error($e->getMessage());
		}
	}

	public function user_mail($config)
	{
		$to = static::get_form_data('email');
		try
		{
			$subject = $this->message_replace($config['subject']);
			$msg = $this->message_replace($config['msg'], array('datetime' => date('Y/m/d H:i:s')));
			$this->_sendmail($to, $config['from'], $subject, $msg, $config['cc'], $config['bcc']);
		}
		catch(\FuelException $e)
		{
			\Log::error($e->getMessage());
		}
	}

	public function attached_mail($config)
	{
		$file = new Model_File();
		try
		{
			$subject = $this->message_replace($config['subject']);
			$msg = $this->message_replace($config['msg'], array('datetime' => date('Y/m/d H:i:s')));
			$attached = $file->file_save_path(\Arr::get($config, 'file_field'));
			$this->_sendmail($config['to'], $config['from'], $subject, $msg, $config['cc'], $config['bcc'], $attached);
		}
		catch(\FuelException $e)
		{
			\Log::error($e->getMessage());
		}
	}

	public function provisional_mail($to, $url, $expired, $type)
	{
		\Config::load('mailmagazine/provisional', 'mail');
		$config = \Config::get('mail.'.$type);
		$email = \Email\Email::forge();
		try
		{
			$msg = $this->message_replace($config['body'], array('url' => $url, 'expirationDate' => $expired));
			$this->_sendmail($to, $config['from'], $config['subject'], $msg);
		}
		catch(\FuelException $e)
		{
			\Log::error($e->getMessage());
		}
	}

	public function complete_mail($to, $type)
	{
		\Config::load('mailmagazine/complete', 'mail');
		$config = \Config::get('mail.'.$type);
		$this->_sendmail($to, $config['from'], $config['subject'], $config['body']);
	}

	public function task_error_mail($to, $from, $subject, $msg)
	{
		$this->_sendmail($to, $from, $subject, $msg);
	}

	public function digest_mail($to, $from, $subject, $body, $user, $password)
	{
		$msg = $this->message_replace($body, array('username' => $user, 'password' => $password));
		foreach((array)$to as $t)
		{
			$this->_sendmail($t, $from, $subject, $msg);
		}
	}

	private function _sendmail($to, $from, $subject, $msg, $cc = null, $bcc = null, $attached = null)
	{
		$email = \Email\Email::forge();
		$email->to($to);
		if(is_array($from))
		{
			foreach($from as $addr => $name)
			{
				$email->from($addr, $name);
			}
		}
		else
		{
			$email->from($from);
		}
		$email->subject($subject);
		if( ! empty($cc))
		{
			$email->cc($cc);
		}
		if( ! empty($bcc))
		{
			$email->bcc($bcc);
		}
		if( ! empty($attached))
		{
			$email->attach($attached);
		}
		$email->body(mb_convert_encoding($msg, \Config::get('email.defaults.charset'), mb_detect_encoding($msg, 'auto')));
		try
		{
			$email->send();
		}
		catch(\Email\EmailValidationFailedException $e)
		{
			// 検証に失敗したときのコード
			\Log::warning('宛先のメールアドレスが存在しません。');
			\Log::error($e->getMessage());
			throw new \FuelException('宛先のメールアドレスが存在しません。', 20000, $e);
		}
		catch(\Email\EmailSendingFailedException $e)
		{
			// 送信に失敗したときのコード
			\Log::warning('メールの送信に失敗しました。');
			\Log::error($e->getMessage());
			throw new \FuelException('メールの送信に失敗しました。', 20001, $e);
		}
	}

	private function message_replace($message, array $datas = array())
	{
		$tmp = $message;
		$len = mb_strlen($tmp);
		$offset = 0;
		$pointer = array();
		while(true)
		{
			$point = $this->search($tmp, $offset);
			if(is_null($point))
			{
				#探しだす文字列がない場合はループを抜ける
				break;
			}
			else
			{
				$pointer[] = $point;
				$offset = $point + 1;
			}
		}
		if((count($pointer) % 2) === 0)
		{
			$tokens = $this->token($tmp, $pointer);
			//\Log::debug('tokens -> '.print_r($tokens, true));
			foreach($tokens as $token)
			{
				$skey = str_replace(Model_Mail::REPLACE_CHAR, '', $token);
				if( ! empty($datas) && array_key_exists($skey, $datas))
				{
					#引数の$datasにキーが存在した場合は置換を実施する
					$tmp = str_replace($token, $datas[$skey], $tmp);
				}
				elseif(array_key_exists($skey, $this->get_form_data(null, array())))
				{
					#セッションにキーが存在した場合は置換を実施する
					$tmp = str_replace($token, $this->get_form_data($skey, ''), $tmp);
				}
			}
			#エスケープ文字を削除する。
			return preg_replace('/'.Model_Mail::ESCAPE_CHAR.'(.)?/', '$1', $tmp);
		}
		else
		{
			throw new \FuelException('パラメータ挿入の書式が間違っています。', 20002, null);
		}
	}

	private function search($msg, $offset)
	{
		$ret = mb_strpos($msg, Model_Mail::REPLACE_CHAR, $offset);
		if($ret === false)
		{
			#置換文字列が存在しない場合はnullを返す。
			return null;
		}
		elseif($ret === 0)
		{
			#発見位置が0(先頭文字)の場合はエスケープされていないのでそのまま返す
			return $ret;
		}
		elseif(mb_substr($msg, $ret - 1, 1) === Model_Mail::ESCAPE_CHAR)
		{
			#置換文字列の前にエスケープキー'#'が存在する場合は次の置換文字列を検索する。
			return $this->search($msg, $ret + 1);
		}
		else
		{
			#エスケープされていない置換文字列が存在した場合はその位置を返す。
			return $ret;
		}
	}

	private function token($msg, array $pointer)
	{
		$return = array();
		$tmp = \Arr::to_assoc($pointer);
		foreach($tmp as $start => $end)
		{
			$return[] = mb_substr($msg, $start, $end - $start + 1);
		}
		return $return;
	}
}