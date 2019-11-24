<?php 

use Phalcon\Mvc\Controller;

class BaseController extends Controller
{

	public $content;

	public function onConstruct ()
    {
		$this->content = [
			'result' => false,
			'message' => Message::error('Internal Server Error.')
		];
	}
}