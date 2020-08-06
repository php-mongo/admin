<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      ExportDocument.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   ExportDocument.php
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
 *  See COPYRIGHT.php for copyright notices and further details.
 */

namespace App\Http\Classes;

use App\Helpers\MongoHelper;


class ExportDocument
{
    /**
     * @var $_var    string|array
     */
    private $_var;

    /**
     * @var $_params  array
     */
    private $_params;

    /**
     * @var array
     */
    private $_phpParams  = [];

    /**
     * @var array
     */
    private $_jsonParams = [];

    /**
     * @var int
     */
    private $_paramIndex = 0;

    /**
     * @var $index  string|integer
     * @return string
     */
    private function _param( $index )
    {
        return "%{MONGO_PARAM_{$index}}";
    }

    /**
     * @param $arr
     * @param string $prev
     * @return array
     */
    private function _addLabelToArray( $arr, $prev = "")
    {
        $ret        = array();
        $cutLength  = 150;
        foreach ($arr as $key => $value) {
            if (is_string($key)) {
                $newKey = $prev . ($prev === ""?"":".") . "field." . $key;
                if (is_string($value) && strlen($value) > $cutLength) {
                    $value = MongoHelper::utf8_substr($value, 0, $cutLength);
                    $value = $value . " __more.{$newKey}.more__";
                }
                $ret[$newKey . ".field"] = $value;
                if (is_array($value)) {
                    $ret[$newKey . ".field"] = $this->_addLabelToArray($value, $newKey);
                }
            }
            else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    /**
     * Export the variable to PHP
     *
     * @return string
     */
    private function _exportArray() {
        $var = $this->_formatVar($this->_var);

        $string = var_export($var, true);

        foreach ($this->_phpParams as $index => $value) {
            $this->setParam($index, $value);
        }
        return strtr($string, $this->getParams());
    }

    /**
     * @param $var
     * @return array|string
     */
    private function _formatVar($var) {
        if (is_scalar($var) || is_null($var)) {
            switch (gettype($var)) {
                case "integer":
                    if (class_exists("MongoInt32")) {
                        // producing MongoInt32 to keep type unchanged (Kyryl Bilokurov <kyryl.bilokurov@gmail.com>)
                        $this->_paramIndex ++;
                        $this->_phpParams[$this->_paramIndex] = 'new MongoInt32(' . $var . ')';
                        return $this->_param($this->_paramIndex);
                    }
                default:
                    return $var;
            }
        }
        if (is_array($var)) {
            foreach ($var as $index => $value) {
                $var[$index] = $this->_formatVar($value);
            }
            return $var;
        }
        if (is_object($var)) {
            $this->_paramIndex ++;
            switch (get_class($var)) {
                case "stdClass":
                    $this->_phpParams[$this->_paramIndex] = array();
                    return $this->_param($this->_paramIndex);

                case "MongoId":
                    $this->_phpParams[$this->_paramIndex] = 'new MongoId("' . $var->__toString() . '")';
                    return $this->_param($this->_paramIndex);

                case "MongoInt32":
                    $this->_phpParams[$this->_paramIndex] = 'new MongoInt32(' . $var->__toString() . ')';
                    return $this->_param($this->_paramIndex);

                case "MongoInt64":
                    $this->_phpParams[$this->_paramIndex] = 'new MongoInt64(' . $var->__toString() . ')';
                    return $this->_param($this->_paramIndex);

                case "MongoDate":
                    $this->_phpParams[$this->_paramIndex] = 'new MongoDate(' . $var->sec . ', ' . $var->usec . ')';
                    return $this->_param($this->_paramIndex);

                case "MongoRegex":
                    $this->_phpParams[$this->_paramIndex] = 'new MongoRegex(\'/' . $var->regex . '/' . $var->flags . '\')';
                    return $this->_param($this->_paramIndex);

                case "MongoTimestamp":
                    $this->_phpParams[$this->_paramIndex] = 'new MongoTimestamp(' . $var->sec . ', ' . $var->inc . ')';
                    return $this->_param($this->_paramIndex);

                case "MongoMinKey":
                    $this->_phpParams[$this->_paramIndex] = 'new MongoMinKey()';
                    return $this->_param($this->_paramIndex);

                case "MongoMaxKey":
                    $this->_phpParams[$this->_paramIndex] = 'new MongoMaxKey()';
                    return $this->_param($this->_paramIndex);

                case "MongoCode":
                    $this->_phpParams[$this->_paramIndex] = 'new MongoCode("' . addcslashes($var->code, '"') . '", ' . var_export($var->scope, true) . ')';
                    return $this->_param($this->_paramIndex);

                default:
                    if (method_exists($var, "__toString")) {
                        return $var->__toString();
                    }
            }
        }
        return $var;
    }

    /**
     * @return string
     */
    private function _exportJSON()
    {
        if (function_exists("json_encode")) {
            $service = "json_encode";

        } else {
            $json = new ServicesJSON();
            $service = array(&$json, 'encode');
        }
    //    $var = $this->_formatVarAsJSON($this->_var, $service);
     //   $string = call_user_func($service, $var);
        $string = call_user_func($service, $this->_var);

        //Remove "\/" escape
        $string = str_replace('\/', "/", $string);

        $params = array();
        foreach ($this->_jsonParams as $index => $value) {
            $params['"' . $this->_param($index) . '"'] = $value;
        }
        return MongoHelper::json_unicode_to_utf8( MongoHelper::json_format(strtr($string, $params)));
    }

    /**
     * @param $var
     * @param $jsonService
     * @return array|string
     */
    private function _formatVarAsJSON($var, $jsonService) {
        if (is_scalar($var) || is_null($var)) {
            switch (gettype($var)) {
                case "integer":
                    $this->_paramIndex ++;
                    $this->_jsonParams[$this->_paramIndex] = 'NumberInt(' . $var . ')';
                    return $this->_param($this->_paramIndex);
                default:
                    return $var;
            }
        }
        if (is_array($var)) {
            foreach ($var as $index => $value) {
                $var[$index] = $this->_formatVarAsJSON($value, $jsonService);
            }
            return $var;
        }
        if (is_object($var)) {
            $this->_paramIndex ++;
            switch (get_class($var)) {
                case "MongoId":
                    $this->_jsonParams[$this->_paramIndex] = 'ObjectId("' . $var->__toString() . '")';
                    return $this->_param($this->_paramIndex);

                case "MongoInt32":
                    $this->_jsonParams[$this->_paramIndex] = 'NumberInt(' . $var->__toString() . ')';
                    return $this->_param($this->_paramIndex);

                case "MongoInt64":
                    $this->_jsonParams[$this->_paramIndex] = 'NumberLong(' . $var->__toString() . ')';
                    return $this->_param($this->_paramIndex);

                case "MongoDate":
                    $timezone = @date_default_timezone_get();
                    date_default_timezone_set("UTC");
                    $this->_jsonParams[$this->_paramIndex] = "ISODate(\"" . date("Y-m-d", $var->sec) . "T" . date("H:i:s.", $var->sec) . ($var->usec/1000) . "Z\")";
                    date_default_timezone_set($timezone);
                    return $this->_param($this->_paramIndex);

                case "MongoTimestamp":
                    $this->_jsonParams[$this->_paramIndex] = call_user_func($jsonService, array(
                        "t" => $var->inc * 1000,
                        "i" => $var->sec
                    ));
                    return $this->_param($this->_paramIndex);

                case "MongoMinKey":
                    $this->_jsonParams[$this->_paramIndex] = call_user_func($jsonService, array( '$minKey' => 1 ));
                    return $this->_param($this->_paramIndex);

                case "MongoMaxKey":
                    $this->_jsonParams[$this->_paramIndex] = call_user_func($jsonService, array( '$minKey' => 1 ));
                    return $this->_param($this->_paramIndex);

                case "MongoCode":
                    $this->_jsonParams[$this->_paramIndex] = $var->__toString();
                    return $this->_param($this->_paramIndex);

                default:
                    if (method_exists($var, "__toString")) {
                        return $var->__toString();
                    }
                    return '<unknown type>';
            }
        }
    }

    /**
     * @return mixed
     */
    public function getVar()
    {
        return $this->_var;
    }

    /**
     * @param mixed $var
     */
    public function setVar( $var ): void
    {
        $this->_var = $var;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->_params;
    }

    /**
     * @param $array    array
     */
    public function setParams( array $array ): void
    {
        $this->_params = $array;
    }

    /**
     * @param $index
     * @param $value
     */
    public function setParam( $index, $value ): void
    {
        $this->_params["'" . $this->_param( $index ) . "'"] = $value;
    }

    /**
     * @param string $param
     */
    public function addParams( string $param ): void
    {
        $this->params = $param;
    }

    /**
     * ExportDocument constructor.
     */
    public function __construct()
    {
        // clear the properties
        $this->setVar(null);
        $this->setParams([]);
    }

    /**
     * Handle the export process
     *
     * @param string $type
     * @param bool $fieldLabel
     * @return string
     */
    public function export( $type = 'array', $fieldLabel = false)
    {
        if ($fieldLabel) {
            $this->setVar( $this->_addLabelToArray( $this->_var ) );
        }
        if ($type == 'array') {
            return $this->_exportArray();
        }
        return $this->_exportJSON();
    }
}
