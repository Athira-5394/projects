<?php
namespace iwago;

trait Snippet
{
	public function assoc_key_position($array, $target, $default = null, $recursive = true, $delimiter = '.')
	{
		$exists = array_key_exists($target, $array);

		if($exists)
		{
			return $target;
		}

		if ($recursive)
		{
			$routes = array();
			foreach ($array as $k => $v)
			{
				if (is_array($v))
				{
					$rk = $this->assoc_key_position($v, $target, $default, true, $delimiter);
					if ($rk !== $default)
					{
						$routes = array($k, $rk);
						break;
					}
				}
			}
			$route = count($routes) ? implode($delimiter, $routes) : false;
		}
		return $route === false ? $default : $route;
	}

	public function inquiry_display($type)
	{
		$return = '';
		switch($type)
		{
			case 'opinion':
				$return = 'ご意見・ご感想';
			break;

			case 'question':
				$return = '教えて！岩合さ～ん';
			break;

			case 'cheer':
				$return = '岩合さんへの応援メッセージ';
			break;

			case 'business':
				$return = 'PRESS関連お問い合わせ';
			break;

			default:
				$return = 'デジタル岩合';
			break;
		}
		return $return;
	}

	public static function set_system_info($contents)
	{
		\Session::set('pre_controller', static::controller());
		\Session::set('cur_controller', \Request::active()->controller);
		\Session::set('pre_contents', static::contents());
		\Session::set('cur_contents', $contents);
		\Session::set(static::contents().'.pre_action', static::action());
		\Session::set(static::contents().'.cur_action', \Request::active()->action);
	}

	public static function contents($previous = false)
	{
		$target = $previous ? 'pre_contents' : 'cur_contents';
		return \Session::get($target, '');
	}

	public static function action($previous = false)
	{
		$target = $previous ? 'pre_action' : 'cur_action';
		return \Session::get(static::contents().'.'.$target, '');
	}

	public static function controller($previous = false)
	{
		$target = $previous ? 'pre_controller' : 'cur_controller';
		return \Session::get($target, '');
	}

	public static function is_entry($previous = false)
	{
		return static::action($previous) === 'entry' ? true : false;
	}

	public static function is_confirm($previous = false)
	{
		return static::action($previous) === 'confirm' ? true : false;
	}

	public static function is_complete($previous = false)
	{
		return static::action($previous) === 'complete' ? true : false;
	}

	public static function route_check()
	{
		$pre = static::action(true);
		switch($pre)
		{
			case 'entry':
				#前回のアクションがentryならば移動先はentryまたはconfirmのみ
				return static::is_entry() || static::is_confirm();
			break;

			case 'confirm':
				#前回のアクションがconfirmならば移動先はentryまたはcompleteのみ
				return static::is_entry() || static::is_complete();
			break;

			default:
				#前回のアクションが空もしくはcompleteの場合はどこへ移動してもよい。
				return true;
			break;
		}
	}

	public static function delete_form_data()
	{
		\Session::delete(static::contents().'.form');
	}

	public static function set_form_data($key, $value)
	{
		if(is_array($value))
		{
			foreach($value as $k => $v)
			{
				if(is_array($v))
				{
					static::set_form_data($key.'.'.$k, $v);
				}
				else
				{
					\Session::set(static::contents().'.form.'.$key.'.'.$k, trim(mb_convert_kana($v, 's')));
				}
			}
		}
		else
		{
			\Session::set(static::contents().'.form.'.$key, trim(mb_convert_kana($value, 's')));
		}
	}

	public static function get_form_data($key = null, $default = null)
	{
		if(is_null($key))
		{
			return \Session::get(static::contents().'.form', $default);
		}
		return \Session::get(static::contents().'.form.'.$key, $default);
	}

	public static function check_public($start, $end)
	{
		$s = empty($start) ? '1970/01/01 00:00:01' : $start;
		$e = empty($end) ? '2038/01/01 00:00:00' : $end;
		$istart = strtotime($s);
		$iend = strtotime($e);
		return time() >= $istart && time() <= $iend;
	}

	public static function agent_data()
	{
		/*
		$tmp = array();
		foreach(\Agent::properties() as $k => $v)
		{
			$tmp[] = $k.'='.$v;
		}
		return implode(',', $tmp);
		*/
		return $_SERVER['HTTP_USER_AGENT'];
	}

	public static function file_download($url, $dir = '.', $save_base_name = '')
	{
		$tmp = file_get_contents($url);
		if (! $tmp)
		{
			#\Log::error('do not download -> '.$url);
			throw new \iwago\IwagoException('URL['.$url.']からダウンロードできませんでした。', 10001, null);
		}
		if (! is_dir($dir))
		{
			mkdir($dir);
		}
		#$dir = preg_replace('{/$}', '', $dir);
		$dir = substr($dir, -1) === '/' ? $dir : $dir.'/';
		$p = pathinfo($url);
		//\Log::debug('set file info: '.print_r($p, true));
		$local_filename = $save_base_name ? : \Arr::get($p, 'filename', 'newfuelfile');
		$local_path = \Arr::get($p, 'extension', '') ? $dir.$local_filename.'.'.\Arr::get($p, 'extension') : $dir.$local_filename;
		if (is_file($local_path))
		{
			//既にファイルが存在した場合はそれを削除する。
			unlink($local_path);
		}
		$fp = fopen($local_path, 'w');
		fwrite($fp, $tmp);
		fclose($fp);
	}

	public static function server_domain($path = '')
	{
		$base = \Uri::create();
		if(($pos = strpos($base, $_SERVER['SERVER_NAME'])) !== false)
		{
			$pos += strlen($_SERVER['SERVER_NAME']);
			$uri = substr($base, 0, $pos);
			$add = empty($path) ? '/' : '/'.$path;
			return $uri.$add;
		}
		else
		{
			return $base;
		}
	}

	public static function end_slash($dir)
	{
		return \Str::ends_with($dir, '/') ? $dir : $dir.'/';
	}

	public static function check_usage_memory()
	{
		// 512M のように M で指定されている前提なのでアレでごめんなさい
		list($max) = sscanf(ini_get('memory_limit'), '%dM');
		$peak = memory_get_peak_usage(true) / 1024 / 1024;
		$used = ((int) $max !== 0)? round((int) $peak / (int) $max * 100, 2): '--';
		\Log::debug(sprintf('Memory usage: %s %% used. (max: %sM, now: %sM)', $used, $max, $peak));
		/*
		if ($used > 85) {
			\Log::warning(sprintf('Memory peak usage warning: %s %% used. (max: %sM, now: %sM)', $used, $max, $peak));
		}
		*/
	}

	public static function model_selector($event)
	{
		$class = \Inflector::words_to_upper('model_'.$event);
		if(class_exists($class))
		{
			return new $class();
		}
		else
		{
			\Log::warning('unkwon event');
			throw new \iwago\IwagoException('unkwon event');
		}
	}
}