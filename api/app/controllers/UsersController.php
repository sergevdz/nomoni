<?php

use Phalcon\Mvc\Controller;

class UsersController extends BaseController
{
    /**
     * Get profile data
     *
     */
    public function profile ()
    {
        $validUser = Auth::getUserData($this->config);
        $this->content['user'] = null;
        
        if ($validUser !== null) {
            $user = Users::findFirst($validUser->id);
            
            if ($user) {
                unset($user->password, $user->password_token);
                
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/uploads/profile/';
                
                if (!file_exists($uploadDir . $user->photo)) {
                    $user->photo = 'default-user.png';
                }

                $this->content['user'] = $user;
                $this->content['result'] = true;
            }
        } else {
            $this->content['message'] = Message::warning("User doesn't exists.");
        }

        $this->response->setJsonContent($this->content);
    }
}
