<?php

use Phalcon\Mvc\Controller;

class UsersController extends BaseController
{

    public function getProfile()
    {
        $content = $this->content;

        $content['user'] = null;
        
        $user = Users::findFirst($this->loggedUserId)->toArray();
        
        if ($user) {
            unset($user['password'], $user['password_token']);
            
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/uploads/profile/';
            
            if (!file_exists($uploadDir . $user['photo'])) {
                $user['photo'] = 'default-user.png';
            }

            $content['user'] = $user;
            $content['result'] = true;
        } else {
            $content['message'] = Message::warning("User doesn't exists.");
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }
}
