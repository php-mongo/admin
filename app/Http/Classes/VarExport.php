<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      VarExport.php 1001 29/8/20, 3:34 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   VarExport.php
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

/**
 * My name for your space
 */
namespace App\Http\Classes;

use App\Helpers\MongoHelper;
use MongoDB\Database;

/**
 * Class VarExport
 * ToDo: !! this class need serious attention !!
 * We need to implement more accurate export data type detections
 *
 * @package App\Http\Classes
 */
class VarExport
{
    /**
     * @var Database
     */
    private $db;

    /**
     * @var string|object
     */
    private $var;

    private $phpParams  = array();

    private $jsonParams = array();

    private $paramIndex = 0;

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

    private function _param($index)
    {
        return "%{MONGO_PARAM_{$index}}";
    }

    private function _exportPHP()
    {
        $var    = $this->_formatVar($this->var);
        $string = var_export($var, true);
        $params = array();
        foreach ( $this->phpParams as $index => $value) {
            $params["'" . $this->_param($index) . "'"] = $value;
        }
        return strtr($string, $params);
    }

    private function _exportJSON()
    {
        $service = "json_encode";
        $var     = $this->_formatVarAsJSON($this->var, $service);
        $string  = call_user_func($service, $var);

        // Remove "\/" escape
        $string = str_replace('\/', "/", $string);

        $params = array();
        foreach ($this->jsonParams as $index => $value) {
            $params['"' . $this->_param($index) . '"'] = $value;
        }
        return MongoHelper::json_unicode_to_utf8($this->json_format(strtr($string, $params)));
    }

    private function _exportDocument()
    {
        $service = "json_encode";
        $vars    = $this->var->getArrayCopy();
        $output  = '';

        foreach ($vars as $key => $row) {
            $var     = $this->_formatVarAsJSON(array($key => $row), $service);

            $string  = call_user_func($service, $var);
            // Remove "\/" escape
            $string = str_replace('\/', "/", $string);

            $params = array();
            foreach ($this->jsonParams as $index => $value) {
                $params['"' . $this->_param($index) . '"'] = $value;
            }

            $output .= MongoHelper::json_unicode_to_utf8($this->json_format(strtr($string, $params)));
        }
    }

    private function _formatVarAsJSON($var, $jsonService,  $key = false)
    {
        if (is_scalar($var) || is_null($var)) {
            switch (gettype($var)) {
                case "integer":
                    $this->paramIndex ++;
                    $this->jsonParams[$this->paramIndex] = 'NumberInt(' . $var . ')';
                    return $this->_param($this->paramIndex);
                default:
                    //return $var;
                    return '';
            }
        }
        if (is_array($var)) {
            foreach ($var as $index => $value) {
                $var[$index] = $this->_formatVarAsJSON($value, $jsonService);
            }
            return $var;
        }
        if (is_object($var)) {
            $this->paramIndex ++;
            switch (get_class($var)) {
                case "MongoDB\BSON\ObjectId":
                    $this->jsonParams[$this->paramIndex] = 'ObjectId("' . $var->__toString() . '")';
                    return $this->_param($this->paramIndex);

                case "MongoInt32":
                    $this->jsonParams[$this->paramIndex] = 'NumberInt(' . $var->__toString() . ')';
                    return $this->_param($this->paramIndex);

                case "MongoInt64":
                    $this->jsonParams[$this->paramIndex] = 'NumberLong(' . $var->__toString() . ')';
                    return $this->_param($this->paramIndex);

                case "MongoDate":
                    $timezone = @date_default_timezone_get();
                    date_default_timezone_set("UTC");
                    $this->jsonParams[$this->paramIndex] = "ISODate(\"" . date("Y-m-d", $var->sec) . "T" . date("H:i:s.", $var->sec) . ($var->usec/1000) . "Z\")";
                    date_default_timezone_set($timezone);
                    return $this->_param($this->paramIndex);

                case "MongoTimestamp":
                    $this->jsonParams[$this->paramIndex] = call_user_func($jsonService, array(
                        "t" => $var->inc * 1000,
                        "i" => $var->sec
                    ));
                    return $this->_param($this->paramIndex);

                case "MongoMinKey":
                    $this->jsonParams[$this->paramIndex] = call_user_func($jsonService, array( '$minKey' => 1 ));
                    return $this->_param($this->paramIndex);

                case "MongoMaxKey":
                    $this->jsonParams[$this->paramIndex] = call_user_func($jsonService, array( '$minKey' => 1 ));
                    return $this->_param($this->paramIndex);

                case "MongoCode":
                    $this->jsonParams[$this->paramIndex] = $var->__toString();
                    return $this->_param($this->paramIndex);

                default:
                    if (method_exists($var, "__toString")) {
                        return $var->__toString();
                    }
                    return '<unknown type>';
            }
        }
    }

    private function _formatVar($var) {
        if (is_scalar($var) || is_null($var)) {
            switch (gettype($var)) {
                case "integer":
                    if (class_exists("MongoInt32")) {
                        // producing MongoInt32 to keep type unchanged (Kyryl Bilokurov <kyryl.bilokurov@gmail.com>)
                        $this->paramIndex ++;
                        $this->phpParams[$this->paramIndex] = 'new MongoInt32(' . $var . ')';
                        return $this->_param($this->paramIndex);
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
            $this->paramIndex ++;
            switch (get_class($var)) {
                case "stdClass":
                    $this->phpParams[$this->paramIndex] = array();
                    return $this->_param($this->paramIndex);

                case "MongoId":
                    $this->phpParams[$this->paramIndex] = 'new MongoId("' . $var->__toString() . '")';
                    return $this->_param($this->paramIndex);

                case "MongoInt32":
                    $this->phpParams[$this->paramIndex] = 'new MongoInt32(' . $var->__toString() . ')';
                    return $this->_param($this->paramIndex);

                case "MongoInt64":
                    $this->phpParams[$this->paramIndex] = 'new MongoInt64(' . $var->__toString() . ')';
                    return $this->_param($this->paramIndex);

                case "MongoDate":
                    $this->phpParams[$this->paramIndex] = 'new MongoDate(' . $var->sec . ', ' . $var->usec . ')';
                    return $this->_param($this->paramIndex);

                case "MongoRegex":
                    $this->phpParams[$this->paramIndex] = 'new MongoRegex(\'/' . $var->regex . '/' . $var->flags . '\')';
                    return $this->_param($this->paramIndex);

                case "MongoTimestamp":
                    $this->phpParams[$this->paramIndex] = 'new MongoTimestamp(' . $var->sec . ', ' . $var->inc . ')';
                    return $this->_param($this->paramIndex);

                case "MongoMinKey":
                    $this->phpParams[$this->paramIndex] = 'new MongoMinKey()';
                    return $this->_param($this->paramIndex);

                case "MongoMaxKey":
                    $this->phpParams[$this->paramIndex] = 'new MongoMaxKey()';
                    return $this->_param($this->paramIndex);

                case "MongoCode":
                    $this->phpParams[$this->paramIndex] = 'new MongoCode("' . addcslashes($var->code, '"') . '", ' . var_export($var->scope, true) . ')';
                    return $this->_param($this->paramIndex);

                default:
                    if (method_exists($var, "__toString")) {
                        return $var->__toString();
                    }
            }
        }
        return $var;
    }

    /**
     * Format JSON to pretty style
     *
     * @param   string  $json   JSON to format
     * @return  string
     */
    public static function json_format($json)
    {
        $tab            = "  ";
        $new_json       = "";
        $indent_level   = 0;
        $in_string      = false;

        $len  = strlen($json);

        for ($c = 0; $c < $len; $c++)
        {
            $char = $json[$c];
            switch($char)
            {
                case '{':
                case '[':
                    if(!$in_string)
                    {
                        $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                        $indent_level++;
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;

                case '}':
                case ']':
                    if(!$in_string)
                    {
                        $indent_level--;
                        $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;

                case ',':
                    if(!$in_string)
                    {
                        $new_json .= ",\n" . str_repeat($tab, $indent_level);
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;

                case ':':
                    if(!$in_string)
                    {
                        $new_json .= ": ";
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;

                case '"':
                    if($c > 0 && $json[$c-1] != '\\')
                    {
                        $in_string = !$in_string;
                    }
                    break;

                default:
                    $new_json .= $char;
                    break;
            }
        }
        return $new_json;
    }

    /**
     * VarExport constructor.
     * @param $db
     * @param $var
     */
    public function __construct( Database $db, $var)
    {
        $this->db  = $db;
        $this->var = $var;
    }

    /**
     * @param string $type
     * @param bool $fieldLabel
     * @return string
     */
    public function export( $type = 'array', $fieldLabel = false)
    {
        if ($fieldLabel) {
            $this->var = $this->_addLabelToArray($this->var);
        }
        if ($type == 'array') {
            return $this->_exportPHP();
        }
        if ($type == 'document') {
            return $this->_exportDocument();
        }
        return $this->_exportJSON();
    }

    /**
     * Used in our export methods
     *
     * @param $document
     * @return array
     */
    public function setObjectId( $document )
    {
        $arr = [];
        foreach ($document as $key => $value) {
            if ($key == '_id') {
                // we need to pass this back for exports instead of ObjectId('r42ry6y54t43r5y6y54t45t54y54ya')
                $arr[ $key ] = array("oid" => $value);

            } else {
                $arr[ $key ] = $value;
            }
        }
        return $arr;
    }
}
