<?php
/*
    Defines a namespace for the model.
*/
namespace App\Models;

use MongoDB\Model\CollectionInfo;

class Collection extends CollectionInfo
{
    public function getInfo()
    {
        return $this->__debugInfo();
    }
}
