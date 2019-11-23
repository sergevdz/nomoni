<?php

use Phalcon\Mvc\Controller;

class TransactionController extends Controller
{
    public $content = ['result' => false, 'message' => ['title' => 'Error!', 'content' => 'Internal Server Error.']];

    public function getTransactions ()
    {
        $this->content['transactions'] = Types::find();
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }
    
    public function getTransaction ($id)
    {
        $this->content['transaction'] = Types::findFirst($id);
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }

    public function create ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $transaction = new Transactions();
            $transaction->setTransaction($tx);
            $transaction->amount = $request['amount'];
            $transaction->date = $request['date'];
            $transaction->concept = $request['concept'];
            $transaction->transaction = $request['transaction'];
            $transaction->type_id = $request['type_id'];

            if ($transaction->create() !== false) {
                $this->content['result'] = true;
                $this->content['message'] = Message::success('Transaction was created.');
                $tx->commit();
            } else {
                $this->content['message'] = Message::error('There was an error when trying to create the transaction.');
                $tx->rollback();
            }
            

        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        
        $this->response->setJsonContent($this->content);
    }

    public function update ($id)
    {
        $transaction = Transactions::findFirst($id);

        $request = $this->request->getPut();

        if ($transaction) {
            $transaction->amount = $request['amount'];
            $transaction->date = $request['date'];
            $transaction->concept = $request['concept'];
            $transaction->transaction = $request['transaction'];
            $transaction->type_id = $request['type_id'];


        	if ($transaction->create() !== false) {
        		$this->content['result'] = true;
        		$this->content['message'] = Message::success('Type has changed.');
        	} else {
        		$this->content['message'] = Message::error('There was an error when trying to update the transaction.');
        	}
        }
        $this->response->setJsonContent($this->content);
    }

    public function delete ($id)
    {
        $transaction = Transactions::findFirst($id);

        if ($transaction) {

            if ($transaction->delete()) {
                $this->content['result'] = true;
                $this->content['message'] = Message::success('Type has been delete.');
            } else {
                $this->content['message'] = Message::error('There was an error when trying to delete the transaction.');
            }
        }
        $this->response->setJsonContent($this->content);
    }

}
