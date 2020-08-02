<?php

use Phalcon\Mvc\Controller;

class AuthController extends BaseController
{
    public function login()
    {
        $content = $this->content;

        try {
            $request = $this->request->getPost();

            if (isset($request['email']) && isset($request['password'])) {
                $userId = Auth::getUserId($request['email'], $request['password']);
                
                if ($userId > -1) {
                    $data = ['id' => $userId];
                    $content['jwt'] = Auth::createToken($this->request->getHttpHost(), $data, $this->config->jwtkey);;
                    $content['result'] = true;
                } else {
                    $content['message'] = Message::warning("Email or password doesn't match with any user.");
                }
            } else {
                $content['message'] = Message::warning("Please write the email and password's account.");
            }
        } catch (PDOException $e) {
            $content['errors'] = [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                // 'trace' => $e->getTraceAsString()
            ];
        } catch (Exception $e) {
            $content['errors'] = Message::exception($e);
        }
        
        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function signup() {
        $content = $this->content;

        try {
            $request = $this->request->getPost();
            $tx = $this->transactions->get();

            $passwordIsNotEmpty = $request['password'] === null || $request['password'] === '' || empty($request['password']);
            
            if ($passwordIsNotEmpty) {
                $content['message'] = Message::warning('Please type a password.');
                $tx->rollback();
            }

            if ($request['password'] !== $request['confirmPassword'] && $passwordIsNotEmpty) {
                $content['message'] = Message::warning("The email address is already in use.");
                $tx->rollback();
            }

            if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                $content['message'] = Message::warning('Entered email is invalid.');
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
                $content['message'] = Message::warning('Entered email is already in use.');
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
                $content['result'] = true;
                $content['message'] = Message::success('User was created.');

                $paymentMethodsArr = [
                    'Debit Card',
                    'Credit Card',
                    'Cash'
                ];
                foreach ($paymentMethodsArr as $pm) {
                    $content['result'] = false;
                    $paymentMethod = new PaymentMethods();
                    $paymentMethod->setTransaction($tx);
                    $paymentMethod->user_id = $user->id;
                    $paymentMethod->name = $pm;
                    $paymentMethod->created_by = $user->id;
                    
                    if ($paymentMethod->create()) {
                        $content['result'] = true;
                    } else {
                        $content['error'] = Helpers::getErrors($paymentMethod);
                        $content['message'] = Message::error('There was an error when trying to create the payment method.');
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
                foreach ($categoriesArr as $c) {
                    $content['result'] = false;
                    $category = new Categories();
                    $category->setTransaction($tx);
                    $category->user_id = $user->id;
                    $category->name = $c;
                    $category->created_by = $user->id;

                    if ($category->create()) {
                        $content['result'] = true;
                    } else {
                        $content['error'] = Helpers::getErrors($category);
                        $content['message'] = Message::error('There was an error when trying to create the category.');
                        $tx->rollback();
                    }
                }

                if ($content['result']) {
                    $content['message'] = Message::success('User was created.');
                    $tx->commit();
                }
            } else {
                $errorMsg = Helpers::getErrorMessage($user);
                $content['message'] = Message::error($errorMsg ?? 'User could not be created.');
                $tx->rollback();
            }
        } catch (Exception $e) {
            $content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }
}
