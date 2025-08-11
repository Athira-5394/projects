<?php
class Controller_Mailmagazine_Captcha extends \Controller_Rest
{
	public function get_index()
	{
		return Captcha::forge('simplecaptcha')->image();
	}
}