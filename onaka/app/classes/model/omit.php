<?php
class Model_Omit extends \Orm\Model
{
	protected static $_properties = array(
			'id',
			'mail',
			'addDate',
	);

	protected static $_table_name = 'omit_mailing_lists';

	protected static $_primary_key = array('id');

	protected static $_connection = 'default';

	public static function delete_omit($email)
	{
		$query = self::query()->where('mail', $email);
		return $query->delete();
	}

	public static function entry_omit($email)
	{
		$omit = Model_Omit::forge();
		$omit->mail = $mail;
		$omit->addDate = date('Y-m-d H:i:s');
		return $omit->save();
	}
}