<?php
/*
    Defines a namespace for the controller.
*/
namespace App\Models;

use MongoDB\Model\DatabaseInfo;

class Db extends DatabaseInfo
{
    public function getInfo()
    {
        return $this->__debugInfo();
    }
}
