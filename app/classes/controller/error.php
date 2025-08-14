<?php
class Controller_Error extends \Controller
{
	use \iwago\Snippet;

	public function action_404()
	{
		\Response::redirect(static::server_domain('notfound.html'));
	}

	public function action_500()
	{
		\Response::redirect(static::server_domain('serror.html'));
	}
}
