<?php

use Phalcon\Mvc\Controller;

class PaymentMethodsController extends BaseController
{

    public function getPaymentMethods ()
    {   
        $this->content['paymentMethods'] = PaymentMethods::find(["user_id = {$this->loggedUserId}", 'order' => 'ord ASC']);
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }
    
    public function getPaymentMethod ($id)
    {
        $this->content['paymentMethod'] = PaymentMethods::findFirst("user_id = {$this->loggedUserId} AND id = $id");
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }

    public function getOptions () {
        $sql = "SELECT id, name FROM payment_methods WHERE user_id = {$this->loggedUserId} ORDER BY id ASC;";
        $types = $this->db->query($sql)->fetchAll();
        
        $options = [];
        foreach ($types as $type) {
            $options[] = [
                'value' => $type['id'],
                'label' => $type['name']
            ];
        }
        $this->content['options'] = $options;
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);   
    }

    public function create ()
    {
        try {
            $tx = $this->transactions->get();

            $request = $this->request->getPost();

            $paymentMethod = new PaymentMethods();
            $paymentMethod->setTransaction($tx);
            $paymentMethod->user_id = $this->loggedUserId;
            $paymentMethod->name = $request['name'];
            $paymentMethod->icon = $request['icon'];
            $paymentMethod->ord = $request['ord'];

            if ($paymentMethod->create()) {
                $this->content['result'] = true;
                $this->content['message'] = Message::success('Payment method was created.');
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($paymentMethod);
                $this->content['message'] = Message::error('There was an error when trying to create the payment method.');
                $tx->rollback();
            }
        } catch (Exception $e) {
            $this->content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($this->content);
    }

    public function update ($id)
    {
        try {
            $tx = $this->transactions->get();

            $paymentMethod = PaymentMethods::findFirst($id);

            $request = $this->request->getPut();

            if ($paymentMethod) {
                $paymentMethod->setTransaction($tx);
                $paymentMethod->user_id = $this->loggedUserId;
                $paymentMethod->name = $request['name'];
                $paymentMethod->icon = $request['icon'];
                $paymentMethod->ord = $request['ord'];

                if ($paymentMethod->update()) {
                    $this->content['result'] = true;
                    $this->content['message'] = Message::success('Payment method has changed.');
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($paymentMethod);
                    $this->content['message'] = Message::error('There was an error when trying to change the payment method.');
                    $tx->rollback();
                }
            }
        } catch (Exception $e) {
            $this->content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($this->content);
    }

    public function delete ($id)
    {
         try {
            $tx = $this->transactions->get();

            $paymentMethod = PaymentMethods::findFirst($id);

            if ($paymentMethod) {
                $paymentMethod->setTransaction($tx);

                if ($paymentMethod->delete()) {
                    $this->content['result'] = true;
                    $this->content['message'] = Message::success('Payment method was deleted.');
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($paymentMethod);
                    $this->content['message'] = Message::error('There was an error when trying to delete the payment method.');
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = Message::error('The payment method doesn\'t exists.');
            }
        } catch (Exception $e) {
            $this->content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($this->content);
    }
}
