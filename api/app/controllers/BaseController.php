<?php 

use Phalcon\Mvc\Controller;
date_default_timezone_set('America/Mexico_City');

class BaseController extends Controller
{

	public $content;

	public $loggedUserId;

	public function onConstruct()
    {
		$this->content = [
			'result' => false,
			'message' => Message::error('Internal Server Error.')
		];

		$this->loggedUserId = Auth::getTokenData($this->config)->id ?? null;
	}
}