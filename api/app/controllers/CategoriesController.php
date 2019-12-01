<?php

use Phalcon\Mvc\Controller;

class CategoriesController extends BaseController
{

    public function getCategories ()
    {   
        $this->content['categories'] = Categories::find(["user_id = {$this->loggedUserId}", 'order' => 'ord ASC']);
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }
    
    public function getCategory ($id)
    {
        $this->content['category'] = Categories::findFirst("user = {$this->loggedUserId} AND id = {$id}");
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }

    public function getOptions () {
        $sql = "SELECT id, name FROM categories WHERE user_id = {$this->loggedUserId} ORDER BY id ASC;";
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

            $category = new Categories();
            $category->setTransaction($tx);
            $category->user_id = $this->loggedUserId;
            $category->name = $request['name'];
            $category->icon = $request['icon'];
            $category->ord = $request['ord'];

            if ($category->create()) {
                $this->content['result'] = true;
                $this->content['message'] = Message::success('Category was created.');
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($category);
                $this->content['message'] = Message::error('There was an error when trying to create the category.');
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

            $category = Categories::findFirst($id);

            $request = $this->request->getPut();

            if ($category) {
                $category->setTransaction($tx);
                $category->user_id = $this->loggedUserId;
                $category->name = $request['name'];
            	$category->icon = $request['icon'];
            	$category->ord = $request['ord'];

            	if ($category->update()) {
            		$this->content['result'] = true;
            		$this->content['message'] = Message::success('Category has changed.');
                    $tx->commit();
            	} else {
                    $this->content['error'] = Helpers::getErrors($category);
            		$this->content['message'] = Message::error('There was an error when trying to change the category.');
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

            $category = Categories::findFirst($id);

            if ($category) {
                $category->setTransaction($tx);

                if ($category->delete()) {
                    $this->content['result'] = true;
                    $this->content['message'] = Message::success('Category was deleted.');
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($category);
                    $this->content['message'] = Message::error('There was an error when trying to delete the category.');
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = Message::error('The category doesn\'t exists.');
            }
        } catch (Exception $e) {
            $this->content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($this->content);
    }
}
