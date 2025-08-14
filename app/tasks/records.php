<?php
namespace Fuel\Tasks;

class Records
{
	public static function run()
	{
		\Config::load('batch', 'task');
		$conf =	\Config::get('task.records');
		try
		{
			$obj = \Model_Provisional::unnecessary_record_delete();
			$obj = \Model_Onetimeurl::unnecessary_record_delete();
		}
		catch (\FuelException $e)
		{
			$msg = 'Table: ';
			$pre = $e->getPrevious();
			switch($e->getCode())
			{
				case 100:
					$msg .= 'provisional_mailing_lists'.chr(13).chr(10).chr(13).chr(10);
				break;

				case 101:
					$msg .= 'one_time_urls'.chr(13).chr(10).chr(13).chr(10);
				break;

				default:
					\Log::error('unkwon error.'.$e->getMessage());
					exit;
				break;
			}
			$msg .= 'message: '.$pre->getMessage().chr(13).chr(10).chr(13).chr(10);
			$msg .= 'stack trace: '.chr(13).chr(10).$pre->getTraceAsString();
			\Model_Mail::forge()->task_error_mail($conf['to'], $conf['from'], $conf['subject'], $msg);
		}
	}
}