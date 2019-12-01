<?php
use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    public function loadSource (string $source)
    {
        $this->setSource($source);

        $this->belongsTo('created_by', 'SysUsers', 'id', ['alias' => 'CreatedById']);
        $this->belongsTo('modified_by', 'SysUsers', 'id', ['alias' => 'ModifiedById']);
    }

}