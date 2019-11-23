<?php

use Phalcon\Mvc\Model;

class Types extends Model
{

    public function initialize ()
    {
        $this->setSource('types');

        $this->belongsTo('created_by', 'Users', 'id');

        $this->hasMany(
            'id',
            'Spends',
            'type_id',
            [
                'foreignKey' => [
                    'message' => 'There are spends depending on this type.',
                ]
            ]
        );
    }
}
