<?php
class Model_Forms_Upload_Image extends \Model_Forms_Abstract
{
	use \iwago\Snippet;

	protected function form()
	{
		$fileinfo = static::get_form_data('file_info');
		$uploads = '';
		foreach($fileinfo as $file)
		{
			$uploads = '<img src="data:'.$file['mimetype'].';base64,'.base64_encode(file_get_contents($file['saved_to'].$file['saved_as'])).'">';
		}
		return $uploads;
	}
}