<?php


namespace App\Http\Classes;

if (class_exists('PEAR_Error')) {

    /**
     * Class ServicesJSONError
     * @package App\Http\Classes
     */
    class ServicesJSONError extends PEAR_Error
    {
        /**
         * @param string $message
         * @param null $code
         * @param null $mode
         * @param null $options
         * @param null $userinfo
         */
        function ServicesJSONError($message = 'unknown error', $code = null, $mode = null, $options = null, $userinfo = null)
        {
            parent::PEAR_Error($message, $code, $mode, $options, $userinfo);
        }
    }

} else {

    /**
     * Class ServicesJSONError
     * @package App\Http\Classes
     */
    class ServicesJSONError
    {
        /**
         * @param string $message
         * @param null $code
         * @param null $mode
         * @param null $options
         * @param null $userinfo
         */
        function ServicesJSONError($message = 'unknown error', $code = null, $mode = null, $options = null, $userinfo = null)
        {

        }
    }
}
