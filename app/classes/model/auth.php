<?php
class Model_Auth
{
	protected function __construct(){}

	public static function check()
	{
		$sid = \Session::key();
		\Log::info('session_id: '.$sid);
		if( ! $sid)
		{
			#セッションが存在しない
			\Response::redirect('error/invalid');
		}
		else if( ! static::_get_auth('account', false))
		{
			#ユーザーIDが存在しない
			\Response::redirect('error/nologin');
		}
	}

	public static function login($username, $password)
	{
		if($account = Model_Accounts::login($username, $password))
		{
			foreach($account as $k => $v)
			{
				static::_set_auth($k, $v);
			}
			\Log::info('login success');
			return true;
		}
		else
		{
			\Log::warning('login failed.');
			return false;
		}
	}

	public static function logout()
	{
		static::_delete_auth();
	}

	public static function get_account_id()
	{
		return static::_get_auth('account', '');
	}

	public static function get_user_group()
	{
		return static::_get_auth('groupId');
	}

	private static function _get_auth($key = null, $default = null)
	{
		if(is_null($key))
		{
			return \Session::get('auth', $default);
		}
		return \Session::get('auth.'.$key, $default);
	}

	private static function _set_auth($key, $value)
	{
		\Session::set('auth.'.$key, $value);
	}

	private static function _delete_auth()
	{
		\Session::destroy();
	}
}