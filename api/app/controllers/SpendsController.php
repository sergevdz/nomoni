<?php

use Phalcon\Mvc\Controller;

class SpendsController extends BaseController
{

    public function getSpends()
    {   
        $this->content['spends'] = Spends::find(
            [
                'user_id = ' . $this->loggedUserId,
                'order' => 'id DESC', 
                'limit' => '20000'
            ]
        );
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }

    public function getByUser($userId)
    {   
        // HERE Sacando los gastos del usuario
        $content = $this->content;
        $content['spends'] = Spends::find(
            [
                'user_id = ' . $userId,
                'order' => 'id DESC', 
                'limit' => '20000'
            ]
        );
        $content['result'] = true;
        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function getFilteredSpends()
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
        LEFT JOIN types ON types.id = spends.type_id
        LEFT JOIN payment_methods ON payment_methods.id = spends.payment_method_id
        JOIN categories ON categories.id = spends.category_id
        WHERE spends.user_id = {$this->loggedUserId} AND spends.id > 0 {$where}
        ORDER BY spends.{$sortBy} {$descending} limit {$rowsPerPage} offset {$offset};";

        $spends = $this->db->query($sql)->fetchAll();
        
        $this->content['spends'] = $spends;
        $this->content['count'] = Spends::find()->count();
        
        $this->content['result'] = true;
        $this->response->setJsonContent($this->content);
    }

    public function getLastFiveMonths() {
        $months = [];
        $currentDate = date('Y-m');

        for ($i = 1; $i <= 5; $i++) {
            $spends = $this->db->query(
                "SELECT sum(amount) as amount FROM spends where user_id = $this->loggedUserId AND to_char(date, 'YYYY-MM') = '$currentDate';"
            )->fetchAll();
            $months[] = ['label' => $currentDate, 'value' => floatval($spends[0]['amount'])];
            $currentDate = date("Y-m", strtotime("-1 month", strtotime($currentDate)));
        }
        $this->content['months'] = $months;
        $this->response->setJsonContent($this->content);
    }
    
    public function getSpend ($id)
    {
        $spend = Spends::findFirst("user_id = {$this->loggedUserId} AND id = {$id}");
        
        $this->content['message'] = Message::warning("Spend doesn't exists.");

        if ($spend) {
            $spend->date = date('Y-m-d', strtotime($spend->date));
            $this->content['spend'] = $spend;
            $this->content['result'] = true;
        }
        $this->response->setJsonContent($this->content);
    }

    public function create()
    {
        try {
            $tx = $this->transactions->get();

            $request = $this->request->getPost();

            $spend = new Spends();
            $spend->setTransaction($tx);
            $spend->user_id = $this->loggedUserId;
            $spend->amount = $request['amount'];
            $spend->concept = $request['concept'];
            $spend->category_id = $request['category_id'];

            $spend->date = date('Y-m-d H:i:s');

            if ($request['date'] !== null && !empty($request['date'])) {
                $spend->date = date('Y-m-d', strtotime($request['date'])) . ' ' . date('H:i:s');
            }

            if (intval($request['type_id']) > 0) {
                $spend->type_id = $request['type_id'];
            }

            if (intval($request['payment_method_id']) > 0) {
                $spend->payment_method_id = $request['payment_method_id'];
            }

            $spend->note = $request['note'];

            if ($spend->create()) {
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
                $spend->user_id = $this->loggedUserId;
                $spend->amount = $request['amount'];
                $spend->concept = $request['concept'];
                $spend->category_id = $request['category_id'];

                if ($request['date'] !== null && !empty($request['date'])) {
                    $spend->date = date('Y-m-d', strtotime($request['date']));
                }

                if (intval($request['type_id']) > 0) {
                    $spend->type_id = $request['type_id'];
                }

                if (intval($request['payment_method_id']) > 0) {
                    $spend->payment_method_id = $request['payment_method_id'];
                }

                $spend->note = $request['note'];

            	if ($spend->update()) {
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

                if ($spend->delete()) {
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

    public function getDailyExpenses() {
        $today = date('Y-m-d');
        $sql = "SELECT amount FROM spends where user_id = {$this->loggedUserId} AND to_char(date, 'YYYY-MM-DD') = '$today';";
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


    public function getMonthlyExpenses() {
        $today = date('Y-m');
        $sql = "SELECT amount FROM spends where user_id = {$this->loggedUserId} AND to_char(date, 'YYYY-MM') = '$today';";
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
    public function getSpendGroupedByCategory()
    {
        $today = date('Y-m');
        $sql = "        
        SELECT
            sum(amount) AS amount,
            categories.name,
            categories.ord 
        FROM spends 
        JOIN categories ON categories.id = spends.category_id
        WHERE categories.user_id = {$this->loggedUserId} AND to_char(date, 'YYYY-MM') = '{$today}'
        GROUP BY spends.category_id, categories.name, categories.ord
        ORDER BY categories.ord;";
        $spends = $this->db->query($sql)->fetchAll();
        $this->content['spends'] = $spends;
        $this->response->setJsonContent($this->content);   
    }

    /**
     * A list with de spends of the current month grouped by type
     */
    public function getSpendGroupedByType()
    {
        $today = date('Y-m');
        $sql = "        
        SELECT
            sum(amount) AS amount,
            types.name,
            types.ord
        FROM spends 
        JOIN types ON types.id = spends.type_id
        WHERE to_char(date, 'YYYY-MM') = '{$today}'
        GROUP BY spends.type_id, types.name, types.ord
        ORDER BY types.ord;";
        $spends = $this->db->query($sql)->fetchAll();
        $this->content['spends'] = $spends;
        $this->response->setJsonContent($this->content);   
    }

    /**
     * A list with de spends of the current month grouped by payment method
     */
    public function getSpendGroupedByPaymentMethod()
    {
        $today = date('Y-m');
        $sql = "        
        SELECT
            sum(amount) AS amount,
            payment_methods.name,
            payment_methods.ord
        FROM spends 
        JOIN payment_methods ON payment_methods.id = spends.payment_method_id
        WHERE payment_methods.user_id = {$this->loggedUserId} AND to_char(date, 'YYYY-MM') = '{$today}'
        GROUP BY spends.payment_method_id, payment_methods.name, payment_methods.ord
        ORDER BY payment_methods.ord;";
        $spends = $this->db->query($sql)->fetchAll();
        $this->content['spends'] = $spends;
        $this->response->setJsonContent($this->content);   
    }
}
