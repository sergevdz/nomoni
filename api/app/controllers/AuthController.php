<?php

use Phalcon\Mvc\Controller;

class AuthController extends BaseController
{
    /**
     * User Login
     *
     */
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

    /**
     * User Sign Up
     *
     */
    public function signup () {
        try {
            $request = $this->request->getPost();
            $tx = $this->transactions->get();

            $passwordIsNotEmpty = $request['password'] === null || $request['password'] === '' || empty($request['password']);
            
            if ($passwordIsNotEmpty) {
                $this->content['message'] = Message::warning('Please type a password.');
                $tx->rollback();
            }

            if ($request['password'] !== $request['confirmPassword'] && $passwordIsNotEmpty) {
                $this->content['message'] = Message::warning("The email address is already in use.");
                $tx->rollback();
            }

            if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                $this->content['message'] = Message::warning('Entered email is invalid.');
                $tx->rollback();
            }

            $user = Users::findFirst(
                [
                    'email = :email:',
                    'bind' => [
                        'email' => $request['email']
                    ]
                ]
            );

            if ($user) {
                $this->content['message'] = Message::warning('Entered email is already in use.');
                $tx->rollback();   
            }

            $user = new Users();
            $user->setTransaction($tx);
            $user->created_by = 1;
            $user->first_name = $request['first_name'];
            $user->last_name = $request['last_name'];
            $user->email = $request['email'];
            $user->password = password_hash($request['password'], PASSWORD_BCRYPT);

            if ($user->create()) {
                $this->content['result'] = true;
                $this->content['message'] = Message::success('User was created.');

                $paymentMethodsArr = [
                    'Debit Card',
                    'Credit Card',
                    'Cash'
                ];
                foreach ($paymentMethodsArr as $pm) {
                    $this->content['result'] = false;
                    $paymentMethod = new PaymentMethods();
                    $paymentMethod->setTransaction($tx);
                    $paymentMethod->user_id = $user->id;
                    $paymentMethod->name = $pm;
                    $paymentMethod->created_by = $user->id;
                    
                    if ($paymentMethod->create()) {
                        $this->content['result'] = true;
                    } else {
                        $this->content['error'] = Helpers::getErrors($paymentMethod);
                        $this->content['message'] = Message::error('There was an error when trying to create the payment method.');
                        $tx->rollback();
                    }
                }

                $categoriesArr = [
                    'Transport',
                    'Hygiene',
                    'Sports',
                    'Trips',
                    'Services',
                    'Unknow',
                    'Food',
                    'Health',
                    'Electronics',
                    'Exercise',
                    'Utilities',
                    'Cleaning',
                    'Home',
                    'Cloths',
                    'Entertainment',
                    'Gifts',
                ];
                foreach ($categories as $c) {
                    $this->content['result'] = false;
                    $category = new Categories();
                    $category->setTransaction($tx);
                    $category->user_id = $user->id;
                    $category->name = $c;
                    $category->created_by = $user->id;

                    if ($category->create()) {
                        $this->content['result'] = true;
                    } else {
                        $this->content['error'] = Helpers::getErrors($category);
                        $this->content['message'] = Message::error('There was an error when trying to create the category.');
                        $tx->rollback();
                    }
                }

                if ($this->content['result']) {
                    $this->content['message'] = Message::success('User was created.');
                    $tx->commit();
                }
            } else {
                $errorMsg = Helpers::getErrorMessage($user);
                // $this->content['message2'] = Helpers::getErrors($user);
                $this->content['message'] = Message::error($errorMsg ?? 'User could not be created.');
                $tx->rollback();
            }
        } catch (Exception $e) {
            $this->content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($this->content);
    }
}
