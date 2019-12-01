<?php

use Phalcon\Mvc\Model;

class Spends extends BaseModel
{

    public function initialize ()
    {
        $this->loadSource('spends');

        $this->belongsTo('user_id', 'Users', 'id');
        $this->belongsTo('type_id', 'Types', 'id');
        $this->belongsTo('category_id', 'Categories', 'id');
        $this->belongsTo('payment_method_id', 'PaymentMethods', 'id');
    }
}
