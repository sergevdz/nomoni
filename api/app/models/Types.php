<?php

use Phalcon\Mvc\Model;

class Types extends BaseModel
{

    public function initialize ()
    {
        $this->loadSource('types');

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
