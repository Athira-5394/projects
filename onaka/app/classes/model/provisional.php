<?php
class Model_Provisional extends \Orm\Model
{
	use \iwago\Snippet;

	protected static $_properties = array(
			'id',
			'mail',
			'addDate',
	);

	protected static $_table_name = 'provisional_mailing_lists';

	protected static $_primary_key = array('id');

	protected static $_connection = 'default';

	public static function set_provisional($email)
	{
		$obj = self::forge();
		$obj->mail = $email;
		$obj->addDate = date('Y-m-d H:i:s');
		$obj->save();
	}

	public static function delete_provisional($email)
	{
		$obj = self::find('all', array(
							'where'	=> array(
									array('mail', '=', $email),
							),
		));
		foreach($obj as $row)
		{
			#該当ユーザーの仮登録情報は全て削除する
			$row->delete();
		}
	}

	public static function unnecessary_record_delete()
	{
		try
		{
			//一日前のレコードは全削除
			$obj = self::query()->where(\DB::expr('curdate()'), '>', \DB::expr('cast(addDate as date)'));
			return $obj->delete();
		}
		catch (\Exception $e)
		{
			\Log::error('working failed.'.$e->getMessage());
			throw new \FuelException('provisional mailing list failed.', 100, $e);
		}
	}
}
