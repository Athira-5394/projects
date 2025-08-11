<?php
namespace iwago;

class IwagoException extends \HttpException
{
	use \iwago\Snippet;

	public function response()
	{
		\Log::warning($this->getMessage());
		static::delete_form_data();
		return new \Response(\View::forge('500'), 500);
	}
}
