<?php

namespace App\Exceptions;

use Exception;

class DocumentObjectException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return string
     */
    public function message()
    {
        return 'error getting document';
    }
}
