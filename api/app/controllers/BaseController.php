<?php 

use Phalcon\Mvc\Controller;

class BaseController extends Controller
{

	public $content;

	public $loggedUserId;

	public function onConstruct ()
    {
		$this->content = [
			'result' => false,
			'message' => Message::error('Internal Server Error.')
		];

		$this->loggedUserId = Auth::getUserData($this->config)->id ?? null;
	}
}