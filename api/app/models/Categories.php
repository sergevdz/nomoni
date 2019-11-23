<?php

use Phalcon\Mvc\Model;

class Categories extends Model
{

    public function initialize ()
    {
        $this->setSource('categories');

        $this->belongsTo('created_by', 'Users', 'id');

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
