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

    /**
     * User Sign Up
     *
     */
    public function signup () {
        try {
            $request = $this->request->getPost();
            $tx = $this->transactions->get();

            if ($request['password'] !== $request['password']) {
                $this->content['message'] = Message::warning('.');
                $tx->rollback();
            }

            if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                $this->content['message'] = Message::warning("Entered passwords doesn't match");
                $tx->rollback();
            }

            $user = Users::findFirst(
                [
                    'email = :email:',
                    'bind' => [
                        'email' => $email
                    ]
                ]
            );

            if ($user) {
                $this->content['message'] = Message::warning('Entered email is already in use.');
                $tx->rollback();   
            }

            $user = new Users();
            $user->setTransaction($tx);
            $user->first_name = $request['first_name'];
            $user->last_name = $request['last_name'];
            $user->email = $request['email'];
            $user->password = $request['password'];

            if ($user->create()) {
                $this->content['result'] = true;
                $this->content['message'] = Message::success('User was created.');
                $tx->commit();
            } else {
                $errorMsg = Helpers::getErrorMessage($client);
                $this->content['message'] = Message::error($errorMsg ?? 'User could not be created.');
                $tx->rollback();
            }
        } catch (Exception $e) {
            $this->content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($this->content);
    }
}
