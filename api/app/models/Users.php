<?php

use Phalcon\Mvc\Model;

class Users extends BaseModel
{

    public function initialize ()
    {
        $this->loadSource('users');
	    
	    $this->hasMany(
	        'id',
	        'Spends',
	        'user_id',
	        [
	            'foreignKey' => [
	                'message' => 'There are spends depending on this user.',
	            ]
	        ]
	    );

	    $this->hasMany(
	        'id',
	        'PaymentMethods',
	        'user_id',
	        [
	            'foreignKey' => [
	                'message' => 'There are types depending on this user.',
	            ]
	        ]
	    );

	    $this->hasMany(
	        'id',
	        'Categories',
	        'user_id',
	        [
	            'foreignKey' => [
	                'message' => 'There are types depending on this user.',
	            ]
	        ]
	    );
    }

}