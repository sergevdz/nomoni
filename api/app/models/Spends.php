<?php

use Phalcon\Mvc\Model;

class Spends extends Model
{

    public function initialize ()
    {
        $this->setSource('spends');

        $this->belongsTo('created_by', 'Users', 'id');
        $this->belongsTo('type_id', 'Types', 'id');
        $this->belongsTo('category_id', 'Categories', 'id');
        $this->belongsTo('payment_method_id', 'PaymentMethods', 'id');
    }
}
