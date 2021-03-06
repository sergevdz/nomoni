<?php

use Phalcon\Mvc\Controller;

class TypesController extends Controller
{
    public $content = ['result' => false, 'message' => ['title' => 'Error!', 'content' => 'Internal Server Error.']];

    public function getTypes ()
    {   
        $this->content['types'] = Types::find(['order' => 'ord ASC']);
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }
    
    public function getType ($id)
    {
        $this->content['type'] = Types::findFirst($id);
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }

    public function getOptions () {
        $sql = "SELECT id, name, icon FROM types ORDER BY id ASC;";
        $types = $this->db->query($sql)->fetchAll();
        
        $options = [];
        foreach ($types as $type) {
            $options[] = [
                'value' => $type['id'],
                'label' => $type['name'],
                'icon' => $type['icon']
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

            $type = new Types();
            $type->setTransaction($tx);
            $type->name = $request['name'];
            $type->icon = $request['icon'];
            $type->ord = $request['ord'];

            if ($type->create() !== false) {
                $this->content['result'] = true;
                $this->content['message'] = Message::success('Type was created.');
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($type);
                $this->content['message'] = Message::error('There was an error when trying to create the type.');
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

            $type = Types::findFirst($id);

            $request = $this->request->getPut();

            if ($type) {
                $type->setTransaction($tx);
                $type->name = $request['name'];
            	$type->icon = $request['icon'];
            	$type->ord = $request['ord'];

            	if ($type->update() !== false) {
            		$this->content['result'] = true;
            		$this->content['message'] = Message::success('Type has changed.');
                    $tx->commit();
            	} else {
                    $this->content['error'] = Helpers::getErrors($type);
            		$this->content['message'] = Message::error('There was an error when trying to change the type.');
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

            $type = Types::findFirst($id);

            if ($type) {
                $type->setTransaction($tx);

                if ($type->delete() !== false) {
                    $this->content['result'] = true;
                    $this->content['message'] = Message::success('Type was deleted.');
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($type);
                    $this->content['message'] = Message::error('There was an error when trying to delete the type.');
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = Message::error('The type doesn\'t exists.');
            }
        } catch (Exception $e) {
            $this->content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($this->content);
    }
}
