<?php

use Phalcon\Mvc\Model;

class PaymentMethods extends BaseModel
{

    public function initialize ()
    {
        $this->loadSource('payment_methods');

        $this->belongsTo('user_id', 'Users', 'id');

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
