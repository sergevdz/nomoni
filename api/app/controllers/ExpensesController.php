<?php

use Phalcon\Mvc\Controller;

class ExpensesController extends BaseController
{
    function getAll()
    {   
        $content = $this->content;
        $content['expenses'] = Spends::find(
            [
                "user_id = {$this->loggedUserId}",
                'order' => 'date DESC', 
                'limit' => '20000'
            ]
        );
        $content['result'] = true;
        $this->response->setJsonContent($content);
        $this->response->send();
    }

    function get($id)
    {   
        $content = $this->content;
        $expense = Spends::findFirst("user_id = {$this->loggedUserId}  AND id = {$id}");
        $content['expense'] = $expense;
        $content['result'] = !empty($expense);
        $this->response->setJsonContent($content);
        $this->response->send();
    }

    function getByUser($userId)
    {   
        $content = $this->content;
        $content['expenses'] = Spends::find(
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

    function getFilteredSpends()
    {   
        $content = $this->content;
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
        
        $content['expenses'] = $spends;
        $content['count'] = Spends::find()->count();
        
        $content['result'] = true;
        $this->response->setJsonContent($content);
        $this->response->send();
    }

    function getLastFiveMonths() {
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
        $this->response->send();
    }

    function create()
    {
        $content = $this->content;

        try {
            $tx = $this->transactions->get();

            $request = $this->request->getPost();

            $spend = new Spends();
            $spend->setTransaction($tx);
            $spend->user_id = $this->loggedUserId;
            $spend->amount = $request['amount'];
            $spend->concept = $request['concept'];
            $spend->category_id = $request['category_id'];
            $date = date('Y-m-d H:i:s');

            if (isset($request['date'])) {
                $spend->date = !empty($request['date']) ? date('Y-m-d', strtotime($request['date'])) . ' ' . date('H:i:s') : $date;                
            } else {
                $spend->date = $date;
            }

            if (isset($request['type_id'])) {
                $spend->type_id = $request['type_id'];
            }

            if (isset($request['payment_method_id'])) {
                $spend->payment_method_id = $request['payment_method_id'];
            }

            if (isset($request['note'])) {
                $spend->note = $request['note'];
            }

            if ($spend->create()) {
                $content['result'] = true;
                $content['message'] = Message::success('Spend created.');
            } else {
                $content['error'] = Helpers::getErrors($spend);
                $content['message'] = Message::error("Spend can't be created.");
                $tx->rollback();
            }
        

            if ($content['result']) {
                $tx->commit();
            }
        } catch (Exception $e) {
            $content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    function update($id)
    {
        $content = $this->content;

        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $spend = Spends::findFirst($id);
            $spend->setTransaction($tx);
            // $spend->user_id = $this->loggedUserId;
            $spend->amount = $request['amount'];
            $spend->concept = $request['concept'];
            $spend->category_id = $request['category_id'];

            if (isset($request['date'])) {
                $spend->date = !empty($request['date']) ? date('Y-m-d', strtotime($request['date'])) . ' ' . date('H:i:s') : $date;                
            } else {
                $spend->date = $date;
            }

            if (isset($request['type_id'])) {
                $spend->type_id = $request['type_id'];
            }

            if (isset($request['payment_method_id'])) {
                $spend->payment_method_id = $request['payment_method_id'];
            }

            if (isset($request['note'])) {
                $spend->note = $request['note'];
            }

        	if ($spend->update()) {
        		$content['result'] = true;
        		$content['message'] = Message::success('Expense updated.');
        	} else {
                $content['error'] = Helpers::getErrors($spend);
        		$content['message'] = Message::error("Expense can't be updated.");
                $tx->rollback();
            }

            if ($content['result']) {
                $tx->commit();
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

            $spend = Spends::findFirst($id);

            if ($spend) {
                $spend->setTransaction($tx);

                if ($spend->delete()) {
                    $content['result'] = true;
                    $content['message'] = Message::success('Spend was deleted.');
                } else {
                    $content['error'] = Helpers::getErrors($spend);
                    $content['message'] = Message::error('There was an error when trying to delete the spend.');
                    $tx->rollback();
                }
                
                if ($content['result']) {
                    $tx->commit();
                }
            } else {
                $content['message'] = Message::error('The spend doesn\'t exists.');
            }
        } catch (Exception $e) {
            $content['errors'] = Message::exception($e);
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    function getDailyExpenses() {
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
        $this->response->send();
    }


    function getMonthlyExpenses() {
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
        $this->response->send();
    }

    /**
     * A list with de spends of the current month grouped by category
     */
    function getSpendGroupedByCategory()
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
        $this->content['expenses'] = $spends;
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    /**
     * A list with de spends of the current month grouped by type
     */
    function getSpendGroupedByType()
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
        $this->content['expenses'] = $spends;
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    /**
     * A list with de spends of the current month grouped by payment method
     */
    function getSpendGroupedByPaymentMethod()
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
        $this->content['expenses'] = $spends;
        $this->response->setJsonContent($this->content);
        $this->response->send();  
    }
}
