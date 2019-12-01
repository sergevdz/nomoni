<?php

use Phalcon\Mvc\Model;

class Categories extends BaseModel
{

    public function initialize ()
    {
        $this->loadSource('categories');

        $this->belongsTo('user_id', 'Users', 'id');

        $this->hasMany(
            'id',
            'Spends',
            'category_id',
            [
                'foreignKey' => [
                    'message' => 'There are spends depending on this category.',
                ]
            ]
        );
    }
}
