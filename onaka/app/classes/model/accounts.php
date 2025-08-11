<?php
class Model_Accounts extends \Orm\Model
{
	protected static $_properties = array(
			'account',
			'password',
			'name',
			'groupId',
			'expirationDate',
			'notes',
			'addDate',
			'addMethod',
			'changeDate',
			'changeMethod',
	);

	protected static $_table_name = 'admin_accounts';

	protected static $_primary_key = array('account');

	protected static $_connection = 'default';

	public static function login($username, $password)
	{
		$rs = self::find($username);
		#ユーザーの存在チェック
		if(empty($rs))
		{
			\Log::info('user not found.');
			return false;
		}
		#ログイン成功のチェック
		if(strtoupper(hash('sha512', $password)) === strtoupper($rs['password']))
		{
			return array(
					'account' 			=> $rs['account'],
					'name' 				=> $rs['name'],
					'groupId' 			=> $rs['groupId'],
					'expirationDate' 	=> $rs['expirationDate'],
			);
		}
		else
		{
			#パスワード誤り
			\Log::info('password missmatch.');
			return false;
		}
	}

}