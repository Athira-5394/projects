<?php
namespace iwago;

class Rules
{
	use \iwago\Snippet;

	public static function _validation_captcha()
	{
		return \Captcha::forge('simplecaptcha')->check();
	}

	public static function _validation_photo_select($val, $values)
	{
		if( ! empty($val) && array_key_exists($val, $values))
		{
			return true;
		}
		return false;
	}

	public static function _validation_present_select($val, $values)
	{
		if( ! empty($val) && array_key_exists($val, $values))
		{
			return true;
		}
		return false;
	}

	public static function _validation_radio_select($val, $values)
	{
		if( ! empty($val) && array_key_exists($val, $values))
		{
			return true;
		}
		return false;
	}

	public static function _validation_telephone($val)
	{
		return preg_match('/^[0-9][0-9\-]*$/', $val) === 1;
	}

	public static function _validation_postcode($val)
	{
		return preg_match('/^[0-9]{7}$/', $val) === 1;
	}
	public static function _validation_file_upload()
	{
		\Upload::process();
		if(\Upload::is_valid())
		{
			\Upload::save();
			static::set_form_data('file_info', \Upload::get_files());
			return true;
		}
		else
		{
			foreach(\Upload::get_errors() as $file)
			{
				if($file['error'] == 1)
				{
					$err_msg = '';
					foreach($file['errors'] as $error)
					{
						switch($error['error'])
						{
							case \Upload::UPLOAD_ERR_MAX_SIZE:
								\Log::warning('最大値より大きいファイルをアップロード');
								$err_msg = 'ファイルサイズが上限を超えております';
							break;

							case \Upload::UPLOAD_ERR_NO_FILE:
								\Log::warning('ファイルを選択していない');
								$err_msg = ':label を選んでください';
							break;

							default:
								\Log::warning($error['message']);
								$err_msg = 'ファイルのアップロードに失敗しました。';
							break;
						}

					}
					\Validation::active()->set_message('file_upload', $err_msg);
				}
			}
			return false;
		}
	}
}