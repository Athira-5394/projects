<?php
class Model_Onetimeurl extends \Orm\Model
{
	const CREATE = 'C';
	const DELETE = 'D';
	//有効期間の時間(単位は分)
	private static $expiration = 30;

	protected static $_properties = array(
			'id',
			'type',
			'mailId',
			'url',
			'expirationDate',
	);

	protected static $_table_name = 'one_time_urls';

	protected static $_primary_key = array('id');

	protected static $_connection = 'default';

	public static function create_onetime_url($email, $type, &$expired)
	{
		\Log::debug('email -> '.$email);
		$obj = self::forge();
		$obj->type = $type;
		$obj->mailId = $email;
		$obj->url = static::_create_url($type);
		$time = time() + (static::$expiration * 60);
		$obj->expirationDate = date('Y-m-d H:i:s', $time);
		$expired = $obj->expirationDate;
		$obj->save();
		return $obj->url;
	}

	public static function delete_onetime_url($email, $type, $token)
	{
		$url = static::_create_url($type, $token);
		$obj = self::find('all', array(
							'where' => array(
								array('mailId', '=', $email),
								array('type', '=', $type),
								array('url', '=', $url),
							),
		));
		\Log::debug('email -> '.$email);
		\Log::debug('type -> '.$type);
		\Log::debug('token -> '.$token);
		foreach($obj as $row)
		{
			$row->delete();
		}
	}

	public static function verify_entry_url($keyword)
	{
		$url = static::_create_url(self::CREATE, $keyword);
		$query = \DB::select(array('A.mailId', 'mail'))->distinct()->from(array('one_time_urls', 'A'));
		$query->join(array('provisional_mailing_lists', 'B'), 'LEFT OUTER');
		$query->on('A.mailId', '=', 'B.mail');
		$query->where('A.url', '=', $url);
		$query->where('A.type', '=', self::CREATE);
		$query->where('B.mail', '!=', null);
		$query->where(\DB::expr('now()'), '<=', \DB::expr('`A`.`expirationDate`'));
		$result = $query->as_assoc()->execute();
		\Log::debug(\DB::last_query());
		if(count($result) === 1)
		{
			return $result[0]['mail'];
		}
		else
		{
			\Log::warning('有効な仮登録データが存在しない');
			return false;
		}
	}

	public static function verify_release_url($keyword)
	{
		$url = static::_create_url(self::DELETE, $keyword);
		$query = \DB::select(array('mailId', 'mail'))->distinct()->from('one_time_urls');
		$query->where('url', '=', $url);
		$query->where('type', '=', self::DELETE);
		$query->where(\DB::expr('now()'), '<=', \DB::expr('`expirationDate`'));
		$result = $query->as_assoc()->execute();
		\Log::debug(\DB::last_query());
		#取得件数が1件である
		if(count($result) === 1)
		{
			return $result[0]['mail'];
		}
		else
		{
			\Log::warning('有効な仮解除データが存在しない');
			return false;
		}

	}

	public static function unnecessary_record_delete()
	{
		//一日前のレコードは全削除
		try
		{
			$obj = self::query()->where(\DB::expr('curdate()'), '>', \DB::expr('cast(expirationDate as date)'));
			return $obj->delete();
		}
		catch(\Exception $e)
		{
			\Log::error('working failed.'.$e->getMessage());
			throw new \FuelException('onetime url failed.', 101, $e);
		}
	}

	private static function _create_url($type, $token = '')
	{
		$base = '';
		switch($type)
		{
			case self::CREATE:
				$base = '/mailmagazine/entry/constancy_confirm/';
			break;

			case self::DELETE:
				$base = '/mailmagazine/release/constancy_confirm/';
			break;

			default:
				throw new HttpServerErrorException();
			break;
		}
		if(empty($token))
		{
			$token = \Crypt::encode(\Str::random('sha1').date('YmdHis'));
		}
		return \Uri::create($base.$token);
	}

}
