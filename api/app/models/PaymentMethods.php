<?php

use Phalcon\Mvc\Model;

class PaymentMethods extends Model
{

    public function initialize ()
    {
        $this->setSource('payment_methods');

        $this->belongsTo('created_by', 'Users', 'id');

        $this->hasMany(
            'id',
            'Spends',
            'payment_method_id',
            [
                'foreignKey' => [
                    'message' => 'There are spends depending on this payment method.',
                ]
            ]
        );
    }
}
