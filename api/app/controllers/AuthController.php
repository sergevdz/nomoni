<?php

use Phalcon\Mvc\Controller;

class AuthController extends BaseController
{

    public function login ()
    {
        try {
            $request = $this->request->getPost();

            if (isset($request['email']) && isset($request['password'])) {
                $userId = Auth::getUserId($request['email'], $request['password']);
                
                if ($userId > -1) {
                    $data = ['id' => $userId];
                    $jwt = Auth::createToken($this->request->getHttpHost(), $data, $this->config->jwtkey);
                    $this->content['jwt'] = $jwt;
                    $this->content['result'] = true;
                } else {
                    $this->content['message'] = Message::warning('Email or password doesn\'t match with any user.');
                }
            } else {
                $this->content['message'] = Message::warning('Please write the email and password\'s account.');
            }
        } catch (Exception $e) {
            $this->content['errors'] = Message::exception($e);
        }
        
        $this->response->setJsonContent($this->content);
    }
}
