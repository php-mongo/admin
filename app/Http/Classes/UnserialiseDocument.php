<?php


namespace App\Http\Classes;

use MongoDB\BSON\Unserializable;

class UnserialiseDocument implements Unserializable
{
    private $data = [];

    function bsonUnserialize(array $data)
    {
        // TODO: Implement bsonUnserialize() method.
        $this->data = $data;
    }
}
