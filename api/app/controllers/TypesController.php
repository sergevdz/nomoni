<?php

use Phalcon\Mvc\Controller;

class TypesController extends BaseController
{

    function getTypes ()
    {   
        $content = $this->content;
        $content['types'] = Types::find(['order' => 'ord ASC']);
        $content['result'] = true;
        $content['message'] = [];
        $this->response->setJsonContent($content);
        $this->response->send();
    }
    
    function getType ($id)
    {
        $content = $this->content;
        $content['type'] = Types::findFirst($id);
        $content['result'] = true;
        $content['message'] = [];
        $this->response->setJsonContent($content);
        $this->response->send();
    }

    function getOptions () {
        $content = $this->content;
        $sql = "
        SELECT
            id as value,
            name as label
        FROM types
        ORDER BY id ASC;";
        $content['options'] = $this->db->query($sql)->fetchAll();
        $content['result'] = true;
        $content['message'] = [];
        $this->response->setJsonContent($content);   
        $this->response->send();
    }

    function create ()
    {
        $content = $this->content;

        try {
            $tx = $this->transactions->get();

            $request = $this->request->getPost();

            $type = new Types();
            $type->setTransaction($tx);
            $type->name = $request['name'];
            $type->icon = $request['icon'];

            if (intval($request['ord']) > 0) {
                $type->ord = $request['ord'];
            }

            if ($type->create()) {
                $content['result'] = true;
                $content['message'] = Message::success('Type was created.');
                $tx->commit();
            } else {
                $content['error'] = Helpers::getErrors($type);
                $content['message'] = Message::error('There was an error when trying to create the type.');
                $tx->rollback();
            }
        } catch (Exception $e) {
            $content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    function update ($id)
    {
        $content = $this->content;

        try {
            $tx = $this->transactions->get();

            $type = Types::findFirst($id);

            $request = $this->request->getPut();

            if ($type) {
                $type->setTransaction($tx);
                $type->name = $request['name'];
            	$type->icon = $request['icon'];
            	$type->ord = $request['ord'];

            	if ($type->update()) {
            		$content['result'] = true;
            		$content['message'] = Message::success('Type has changed.');
                    $tx->commit();
            	} else {
                    $content['error'] = Helpers::getErrors($type);
            		$content['message'] = Message::error('There was an error when trying to change the type.');
                    $tx->rollback();
            	}
            }
        } catch (Exception $e) {
            $content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    function delete ($id)
    {
        $content = $this->content;

        try {
            $tx = $this->transactions->get();

            $type = Types::findFirst($id);

            if ($type) {
                $type->setTransaction($tx);

                if ($type->delete()) {
                    $content['result'] = true;
                    $content['message'] = Message::success('Type was deleted.');
                    $tx->commit();
                } else {
                    $content['error'] = Helpers::getErrors($type);
                    $content['message'] = Message::error('There was an error when trying to delete the type.');
                    $tx->rollback();
                }
            } else {
                $content['message'] = Message::error('The type doesn\'t exists.');
            }
        } catch (Exception $e) {
            $content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }
}
