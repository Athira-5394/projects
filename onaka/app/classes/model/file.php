<?php
class Model_File
{
	use \iwago\Snippet;

	protected $file_info = null;

	public function __construct()
	{
		$this->file_info = array();
		foreach($this->get_form_data('file_info', array()) as $file)
		{
			$this->file_info[$file['field']] = $file;
		}
	}

	public static function forge()
	{
		return new Model_File();
	}

	public function run($config)
	{
		if( ! is_dir($config['save_to']))
		{
			mkdir($config['save_to'], 0755, true);
		}
		$i = 0;
		foreach($this->file_info as $file)
		{
			$src = $file['saved_to'].$file['saved_as'];
			$dst = $config['save_to'].$file['basename'].$config['surfix'];
			\Log::debug('src: '.$src.'\ndst: '.$dst);
			if(is_file($src) && copy($src, $dst))
			{
				#一時ファイルを削除するかどうか。
				if($config['temporary_remove'])
				{
					unlink($src);
				}
				$this->set_form_data('file_info.'.$i.'.real_save_path', $dst);
			}
			else
			{
				\Log::error('ファイルのコピーに失敗しました。');
				throw new \HttpServerErrorException();
			}
			$i++;
		}
	}

	public function event($config)
	{
		if( ! is_dir($config['save_to']))
		{
			mkdir($config['save_to'], 0755, true);
		}
		$i = 0;
		$base = static::contents();
		foreach($this->file_info as $file)
		{
			$src = $file['saved_to'].$file['saved_as'];
			$dst_fname = $base.$config['surfix'].\Str::random('numeric', 3).'.'.$file['extension'];
			$dst = $config['save_to'].$dst_fname;
			\Log::debug('src: '.$src);
			\Log::debug('dst: '.$dst);
			if(is_file($src) && copy($src, $dst))
			{
				#一時ファイルを削除するかどうか。
				if($config['temporary_remove'])
				{
					unlink($src);
				}
				$this->set_form_data('file_info.'.$i.'.real_save_path', $dst);
				$this->set_form_data('file_info.'.$i.'.real_save_fname', $dst_fname);
			}
			else
			{
				\Log::error('ファイルのコピーに失敗しました。');
				throw new \HttpServerErrorException();
			}
			$i++;
		}
	}


	/**
	 * 一時的にアップロードしたファイルを削除する
	 */
	public function delete_temporay_file()
	{
		foreach($this->file_info as $file)
		{
			if(isset($file['saved_to']) && isset($file['saved_as']) && is_file($file['saved_to'].$file['saved_as']))
			{
				unlink($file['saved_to'].$file['saved_as']);
			}
		}
	}

	public function file_name($field)
	{
		return $this->_get_file_info($field, 'name');
	}

	public function file_extention($field)
	{
		return $this->_get_file_info($field, 'extension');
	}

	public function attached_file_name($field)
	{
		return $this->_get_file_info($field, 'real_save_fname');
	}

	public function file_save_path($field)
	{
		return $this->_get_file_info($field, 'real_save_path');
	}

	private function _get_file_info($field, $key)
	{
		if( ! isset($this->file_info[$field]) || ! is_array($this->file_info[$field]))
		{
			return '';
		}
		return \Arr::get($this->file_info[$field], $key, '');
	}
}