<?php

use Phalcon\Mvc\Controller;

class SpendsController extends Controller
{
    public $content = ['result' => false, 'message' => ['title' => 'Error!', 'content' => 'Internal Server Error.']];

    public function getSpends ()
    {   
        $this->content['spends'] = Spends::find(['order' => 'id DESC', 'limit' => '20000']);
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }

    public function getFilteredSpends ()
    {   
        $request = $this->request->getPost();
        
        $descending = $request['descending'] === 'true' ? 'DESC' : 'ASC';
        $page = intval($request['page']);
        $rowsPerPage = intval($request['rowsPerPage']);
        $sortBy = ($request['sortBy'] !== null && $request['sortBy'] !== '') ? $request['sortBy'] : 'date';
        $filter = ($request['filter'] !== null && $request['filter'] !== '') ? $request['filter'] : '';
        $offset = ($page - 1) * $rowsPerPage;
        
        if ($rowsPerPage === 0) {
            $rowsPerPage = 'null';
        }

        if ($sortBy === 'format_date') {
            $sortBy = 'date';
        }

        $where = '';

        if ($filter) {
            $where = " AND (
                cast(spends.id as text) LIKE '%{$filter}%' OR 
                cast(spends.amount as text) LIKE '%{$filter}%' OR 
                cast(spends.date as text) LIKE '%{$filter}%' OR 
                cast(spends.concept as text) LIKE '%{$filter}%' OR
                cast(types.name as text) LIKE '%{$filter}%' OR
                cast(payment_methods.name as text) LIKE '%{$filter}%' OR
                cast(categories.name as text) LIKE '%{$filter}%'
            ) ";
        }

        $sql = "
        SELECT
            spends.*,
            to_char(spends.date, 'YYYY-MM-DD') as format_date,
            types.name AS type,
            payment_methods.name AS payment_method,
            categories.name AS category
        FROM spends
        JOIN types ON types.id = spends.type_id
        JOIN payment_methods ON payment_methods.id = spends.payment_method_id
        JOIN categories ON categories.id = spends.category_id
        WHERE spends.id > 0 {$where}
        ORDER BY spends.{$sortBy} {$descending} limit {$rowsPerPage} offset {$offset};";
        $this->content['sql'] = $sql;
        $spends = $this->db->query($sql)->fetchAll();
        
        $this->content['spends'] = $spends;
        $this->content['count'] = Spends::find()->count();
        
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }

    public function getLastFiveMonths () {
        $months = [];
        $currentDate = date('Y-m');

        for ($i = 1; $i <= 5; $i++) {
            $spends = $this->db->query(
                "SELECT sum(amount) as amount FROM spends where to_char(date, 'YYYY-MM') = '$currentDate';"
            )->fetchAll();
            $months[] = ['label' => $currentDate, 'value' => floatval($spends[0]['amount'])];
            $currentDate = date("Y-m", strtotime("-1 month", strtotime($currentDate)));
        }
        $this->content['months'] = $months;
        $this->response->setJsonContent($this->content);
    }
    
    public function getSpend ($id)
    {
        $spend = Spends::findFirst($id);
        
        $this->content['message'] = Message::warning("Spend doesn't exists.");

        if ($spend) {
            $spend->date = date('Y-m-d', strtotime($spend->date));
            $this->content['spend'] = $spend;
            $this->content['result'] = true;
        }
        $this->response->setJsonContent($this->content);
    }

    public function create ()
    {
        try {
            $tx = $this->transactions->get();

            $request = $this->request->getPost();

            $spend = new Spends();
            $spend->setTransaction($tx);
            $spend->amount = $request['amount'];
            $spend->date = $request['date'];
            $spend->concept = $request['concept'];
            $spend->description = $request['description'];
            $spend->type_id = $request['type_id'];
            $spend->category_id = $request['category_id'];
            $spend->payment_method_id = $request['payment_method_id'];

            if ($spend->create() !== false) {
                $this->content['result'] = true;
                $this->content['message'] = Message::success('Spend was created.');
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($spend);
                $this->content['message'] = Message::error('There was an error when trying to create the spend.');
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

            $spend = Spends::findFirst($id);

            $request = $this->request->getPut();

            if ($spend) {
                $spend->setTransaction($tx);
                $spend->amount = $request['amount'];
	            $spend->date = $request['date'];
	            $spend->concept = $request['concept'];
	            $spend->description = $request['description'];
	            $spend->type_id = $request['type_id'];
	            $spend->category_id = $request['category_id'];
	            $spend->payment_method_id = $request['payment_method_id'];

            	if ($spend->update() !== false) {
            		$this->content['result'] = true;
            		$this->content['message'] = Message::success('Spend has changed.');
                    $tx->commit();
            	} else {
                    $this->content['error'] = Helpers::getErrors($spend);
            		$this->content['message'] = Message::error('There was an error when trying to change the spend.');
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

            $spend = Spends::findFirst($id);

            if ($spend) {
                $spend->setTransaction($tx);

                if ($spend->delete() !== false) {
                    $this->content['result'] = true;
                    $this->content['message'] = Message::success('Spend was deleted.');
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($spend);
                    $this->content['message'] = Message::error('There was an error when trying to delete the spend.');
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = Message::error('The spend doesn\'t exists.');
            }
        } catch (Exception $e) {
            $this->content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($this->content);
    }

    public function getDailyExpenses () {
        $today = date('Y-m-d');
        $sql = "SELECT amount FROM spends where to_char(date, 'YYYY-MM-DD') = '$today';";
        $spends = $this->db->query($sql)->fetchAll();
        $dailyAmount = 0;
        
        if (count($spends) > 0) {
            foreach ($spends as $sp) {
                $dailyAmount += $sp['amount'];
            }
        }

        $this->content['dailyAmount'] = $dailyAmount;
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }


    public function getMonthlyExpenses () {
        $today = date('Y-m');
        $sql = "SELECT amount FROM spends where to_char(date, 'YYYY-MM') = '$today';";
        $spends = $this->db->query($sql)->fetchAll();
        $dailyAmount = 0;
        
        if (count($spends) > 0) {
            foreach ($spends as $sp) {
                $dailyAmount += $sp['amount'];
            }
        }

        $this->content['monthlyAmount'] = $dailyAmount;
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }

    /**
     * A list with de spends of the current month grouped by category
     */
    public function getSpendGroupedByCategory ()
    {
        $today = date('Y-m');
        $sql = "        
        SELECT
            sum(amount) AS amount,
            categories.name,
            categories.ord from spends 
        JOIN categories ON categories.id = spends.category_id
        WHERE to_char(date, 'YYYY-MM') = '$today'
        GROUP BY spends.category_id, categories.name, categories.ord
        ORDER BY categories.ord;";
        $spends = $this->db->query($sql)->fetchAll();
        $this->content['spends'] = $spends;
        $this->response->setJsonContent($this->content);   
    }

    /**
     * A list with de spends of the current month grouped by type
     */
    public function getSpendGroupedByType ()
    {
        $today = date('Y-m');
        $sql = "        
        SELECT
            sum(amount) AS amount,
            types.name,
            types.ord from spends 
        JOIN types ON types.id = spends.type_id
        WHERE to_char(date, 'YYYY-MM') = '$today'
        GROUP BY spends.type_id, types.name, types.ord
        ORDER BY types.ord;";
        $spends = $this->db->query($sql)->fetchAll();
        $this->content['spends'] = $spends;
        $this->response->setJsonContent($this->content);   
    }

    /**
     * A list with de spends of the current month grouped by payment method
     */
    public function getSpendGroupedByPaymentMethod ()
    {
        $today = date('Y-m');
        $sql = "        
        SELECT
            sum(amount) AS amount,
            payment_methods.name,
            payment_methods.ord from spends 
        JOIN payment_methods ON payment_methods.id = spends.payment_method_id
        WHERE to_char(date, 'YYYY-MM') = '$today'
        GROUP BY spends.payment_method_id, payment_methods.name, payment_methods.ord
        ORDER BY payment_methods.ord;";
        $spends = $this->db->query($sql)->fetchAll();
        $this->content['spends'] = $spends;
        $this->response->setJsonContent($this->content);   
    }
}
