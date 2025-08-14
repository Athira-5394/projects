<?php
class Model_Mailinglists extends \Model
{

    private static $heads = array('mail', 'apday', 'upday', 'ST');

	public static function exists($email)
	{
		$query = \DB::select('id')->from('mailing_lists')
						 		  ->where('mail', '=', $email)
						 		  ->execute();
		\Log::debug(\DB::last_query());
		\Log::debug('count => '.count($query));
		return count($query) > 0;
	}

	public static function search($emails, $delflg)
	{
		return self::_select($emails, $delflg);
	}

	public static function csv($emails, $delflg)
	{
		$result = self::_select($emails, $delflg);
        $csv_data[] = $result['thead'];
		foreach($result['tbody'] as $row)
		{
			$csv_data[] = $row;
		}
		
		
		$csv = \Format::forge($csv_data)->to_csv();
		return mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');
	}

	public static function entry($email)
	{
		$query = \DB::insert('mailing_lists')->set(array(
															'mail' => $email,
															'addDate' => date('Y-m-d H:i:s'),
		))->execute();
	}

	public static function delete($email)
	{
		$query = \DB::delete('mailing_lists')->where('mail', '=', $email)->execute();
		\Log::debug('実行結果: '.$query);
	}

	private static function _select($emails, $delflg)
	{
		$query = \DB::select(array('A.mail', 'mail'), array('A.addDate', 'apday'), array('B.addDate', 'upday'))->from(array('mailing_lists', 'A'));
		$query->join(array('omit_mailing_lists', 'B'), 'LEFT OUTER');
		$query->on('A.mail', '=', 'B.mail');
		#解除済みを含めない場合はupdayがnullの条件を追加する
		if( ! $delflg)
		{
			\Log::debug('delflg on');
			$query->where('B.addDate', '=', null);
		}
		if( ! empty($emails))
		{
			$tmp = explode(',', $emails);
			$query->where_open();
			foreach($tmp as $v)
			{
				$str = str_replace(array('\\', '%', '_'), array('\\\\', '\%', '\_'), trim($v));
				$query->or_where('A.mail', 'like', '%'.$str.'%');
			}
			$query->where_close();
		}
		$query->order_by('mail');
		$result = $query->as_assoc()->execute();
		\Log::debug('execute query -> '.\DB::last_query());
		$tbody = array();
		foreach($result as $row)
		{
			$row['status'] = empty($row['upday']) ? '' : '停止中';
			$tbody[] = $row;
		}
		return array(
			'condition' => static::_view_conditions($emails, $delflg),
			'thead' => static::$heads,
			'tbody' => $tbody,
		);
	}

	private static function _view_conditions($emails, $delflg)
	{
		$condition = '';
		if( ! empty($emails))
		{
			$condition .= 'メールアドレス：';
			if(mb_strlen($emails) > 30)
			{
				$condition .= mb_substr($emails, 0, 29).'...';
			}
			else
			{
				$condition .= $emails;
			}
			$condition .= '／';
		}
		$condition .= 'ステータス：';
		if($delflg)
		{
			$condition .= '配信エラーアドレスも含む';
		}
		else
		{
			$condition .= '配信エラーアドレスは含まない';
		}
		return $condition;
	}
}
