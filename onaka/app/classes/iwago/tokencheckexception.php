<?php
namespace iwago;

class TokenCheckException extends \HttpException
{
	public function response()
	{
		\Log::warning('CSRF対策用のトークンチェックに失敗しました。');
		return new \Response(\View::forge('500'), 500);
	}
}