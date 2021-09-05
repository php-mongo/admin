<?php

/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      ExportClass.php 1001 15/8/21, 5:07 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   ExportClass.php
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2021. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
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

/*
 * Helpers and used classes
 */
use App\Helpers\MongoHelper;
use MongoDB\Database;

/**
 * Class ExportClass
 * Base exporting class
 */
class ExportClass
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @var array $jsonParams
     */
    private $jsonParams = [];

    /**
     * @var int $paramIndex
     */
    private $paramIndex = 0;

    /**
     * @var array $params
     */
    private $params;

    /**
     * @var array $phpParams
     */
    private $phpParams  = [];

    /**
     * @var string|array|object $var
     */
    private $var;

    /**
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }

    /**
     * @param Database $database
     */
    public function setDatabase(Database $database): void
    {
        $this->database = $database;
    }

    /**
     * @return array
     */
    public function getJsonParams(): array
    {
        return $this->jsonParams;
    }

    /**
     * @param   int     $index
     * @param   string  $jsonParams
     */
    public function setJsonParams(int $index, string $jsonParams): void
    {
        $this->jsonParams[$index] = $jsonParams;
    }

    /**
     * @return int
     */
    public function getParamIndex(): int
    {
        return $this->paramIndex;
    }

    /**
     * Reset the $paramIndex value
     */
    public function setParamIndex(): void
    {
        $this->paramIndex = 0;
    }

    /**
     * Increment the $paramIndex value
     */
    public function addParamIndex(): void
    {
        $this->paramIndex++;
    }

    /**
     * @var     $index  string|integer
     * @return  string
     */
    protected function param($index): string
    {
        return "%{MONGO_PARAM_{$index}}";
    }

    /**
     * @return array|object|string
     */
    public function getVar()
    {
        return $this->var;
    }

    /**
     * @param mixed $var
     */
    public function setVar($var): void
    {
        $this->var = $var;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param $array array
     */
    public function setParams(array $array): void
    {
        $this->params = $array;
    }

    /**
     * @param $index
     * @param $value
     */
    public function setParam($index, $value): void
    {
        $this->params["'" . $this->param($index) . "'"] = $value;
    }

    /**
     * @param string $param
     */
    public function addParams(string $param): void
    {
        $this->params = $param;
    }

    /**
     * @return array
     */
    public function getPhpParams(): array
    {
        return $this->phpParams;
    }

    /**
     * @param array $phpParams
     */
    public function setPhpParams(array $phpParams): void
    {
        $this->phpParams = $phpParams;
    }

    /**
     * @param   array $arr
     * @param   string $prev
     * @return  array
     */
    protected function addLabelToArray(array $arr, string $prev = ""): array
    {
        $ret        = array();
        $cutLength  = 150;
        foreach ($arr as $key => $value) {
            if (is_string($key)) {
                $newKey = $prev . ($prev === ""?"":".") . "field." . $key;
                if (is_string($value) && strlen($value) > $cutLength) {
                    $value = MongoHelper::utf8Substr($value, 0, $cutLength);
                    $value = $value . " __more.{$newKey}.more__";
                }
                $ret[$newKey . ".field"] = $value;
                if (is_array($value)) {
                    $ret[$newKey . ".field"] = $this->addLabelToArray($value, $newKey);
                }
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    /*
     * @param $arr
     * @param string $prev
     * @return array
     *
    private function addLabelToArray($arr, $prev = "")
    {
        $ret        = array();
        $cutLength  = 150;
        foreach ($arr as $key => $value) {
            if (is_string($key)) {
                $newKey = $prev . ($prev === ""?"":".") . "field." . $key;
                if (is_string($value) && strlen($value) > $cutLength) {
                    $value = MongoHelper::utf8Substr($value, 0, $cutLength);
                    $value = $value . " __more.{$newKey}.more__";
                }
                $ret[$newKey . ".field"] = $value;
                if (is_array($value)) {
                    $ret[$newKey . ".field"] = $this->addLabelToArray($value, $newKey);
                }
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    } */


    /**
     * @param $var
     * @return array|string
     */
    protected function formatVar($var)
    {
        if (is_scalar($var) || is_null($var)) {
            switch (gettype($var)) {
                case "integer":
                    if (class_exists("MongoInt32")) {
                        // producing MongoInt32 to keep type unchanged (Kyryl Bilokurov <kyryl.bilokurov@gmail.com>)
                        $this->paramIndex ++;
                        $this->phpParams[$this->paramIndex] = 'new MongoInt32(' . $var . ')';
                        return $this->param($this->paramIndex);
                    }
                // fall through to default if no class found
                default:
                    return $var;
            }
        }
        if (is_array($var)) {
            foreach ($var as $index => $value) {
                $var[$index] = $this->formatVar($value);
            }
            return $var;
        }
        if (is_object($var)) {
            $this->paramIndex ++;
            switch (get_class($var)) {
                case "stdClass":
                    $this->phpParams[$this->paramIndex] = array();
                    return $this->param($this->paramIndex);

                case "MongoId":
                    $this->phpParams[$this->paramIndex] = 'new MongoId("' . $var->__toString() . '")';
                    return $this->param($this->paramIndex);

                case "MongoInt32":
                    $this->phpParams[$this->paramIndex] = 'new MongoInt32(' . $var->__toString() . ')';
                    return $this->param($this->paramIndex);

                case "MongoInt64":
                    $this->phpParams[$this->paramIndex] = 'new MongoInt64(' . $var->__toString() . ')';
                    return $this->param($this->paramIndex);

                case "MongoDate":
                    $this->phpParams[$this->paramIndex] = 'new MongoDate(' . $var->sec . ', ' . $var->usec . ')';
                    return $this->param($this->paramIndex);

                case "MongoRegex":
                    $this->phpParams[$this->paramIndex] =
                        'new MongoRegex(\'/' . $var->regex . '/' . $var->flags . '\')';
                    return $this->param($this->paramIndex);

                case "MongoTimestamp":
                    $this->phpParams[$this->paramIndex] = 'new MongoTimestamp(' . $var->sec . ', ' . $var->inc . ')';
                    return $this->param($this->paramIndex);

                case "MongoMinKey":
                    $this->phpParams[$this->paramIndex] = 'new MongoMinKey()';
                    return $this->param($this->paramIndex);

                case "MongoMaxKey":
                    $this->phpParams[$this->paramIndex] = 'new MongoMaxKey()';
                    return $this->param($this->paramIndex);

                case "MongoCode":
                    $this->phpParams[$this->paramIndex] =
                        'new MongoCode("' . addcslashes($var->code, '"') . '", ' . var_export($var->scope, true) . ')';
                    return $this->param($this->paramIndex);

                default:
                    if (method_exists($var, "__toString")) {
                        return $var->__toString();
                    }
            }
        }
        return $var;
    }

    /*private function formatVar($var)
   {
       if (is_scalar($var) || is_null($var)) {
           switch (gettype($var)) {
               case "integer":
                   if (class_exists("MongoInt32")) {
                       // producing MongoInt32 to keep type unchanged (Kyryl Bilokurov <kyryl.bilokurov@gmail.com>)
                       $this->paramIndex ++;
                       $this->phpParams[$this->paramIndex] = 'new MongoInt32(' . $var . ')';
                       return $this->param($this->paramIndex);
                   }
               default:
                   return $var;
           }
       }
       if (is_array($var)) {
           foreach ($var as $index => $value) {
               $var[$index] = $this->formatVar($value);
           }
           return $var;
       }
       if (is_object($var)) {
           $this->paramIndex ++;
           switch (get_class($var)) {
               case "stdClass":
                   $this->phpParams[$this->paramIndex] = array();
                   return $this->param($this->paramIndex);

               case "MongoId":
                   $this->phpParams[$this->paramIndex] = 'new MongoId("' . $var->__toString() . '")';
                   return $this->param($this->paramIndex);

               case "MongoInt32":
                   $this->phpParams[$this->paramIndex] = 'new MongoInt32(' . $var->__toString() . ')';
                   return $this->param($this->paramIndex);

               case "MongoInt64":
                   $this->phpParams[$this->paramIndex] = 'new MongoInt64(' . $var->__toString() . ')';
                   return $this->param($this->paramIndex);

               case "MongoDate":
                   $this->phpParams[$this->paramIndex] = 'new MongoDate(' . $var->sec . ', ' . $var->usec . ')';
                   return $this->param($this->paramIndex);

               case "MongoRegex":
                   $this->phpParams[$this->paramIndex] = 'new MongoRegex(\'/' . $var->regex . '/' . $var->flags . '\')';
                   return $this->param($this->paramIndex);

               case "MongoTimestamp":
                   $this->phpParams[$this->paramIndex] = 'new MongoTimestamp(' . $var->sec . ', ' . $var->inc . ')';
                   return $this->param($this->paramIndex);

               case "MongoMinKey":
                   $this->phpParams[$this->paramIndex] = 'new MongoMinKey()';
                   return $this->param($this->paramIndex);

               case "MongoMaxKey":
                   $this->phpParams[$this->paramIndex] = 'new MongoMaxKey()';
                   return $this->param($this->paramIndex);

               case "MongoCode":
                   $this->phpParams[$this->paramIndex] =
                        'new MongoCode("' . addcslashes($var->code, '"') . '", ' . var_export($var->scope, true) . ')';
                   return $this->param($this->paramIndex);

               default:
                   if (method_exists($var, "__toString")) {
                       return $var->__toString();
                   }
           }
       }
       return $var;
   }*/

    /**
     * @param   mixed $var
     * @param   $jsonService
     * @return  array|string
     */
    protected function formatVarAsJSON($var, $jsonService)
    {
        if (is_scalar($var) || is_null($var)) {
            switch (gettype($var)) {
                case "integer":
                    $this->addParamIndex();
                    $this->setJsonParams($this->getParamIndex(), 'NumberInt(' . $var . ')');
                    return $this->param($this->getParamIndex());
                default:
                    return $var;
            }
        }
        if (is_array($var)) {
            foreach ($var as $index => $value) {
                $var[$index] = $this->formatVarAsJSON($value, $jsonService);
            }
            return $var;
        }
        if (is_object($var)) {
            $this->addParamIndex();
            switch (get_class($var)) {
                case "MongoId":
                    $this->setJsonParams($this->getParamIndex(), 'ObjectId("' . $var->__toString() . '")');
                    return $this->param($this->getParamIndex());

                case "MongoInt32":
                    $this->setJsonParams($this->getParamIndex(), 'NumberInt(' . $var->__toString() . ')');
                    return $this->param($this->getParamIndex());

                case "MongoInt64":
                    $this->setJsonParams($this->getParamIndex(), 'NumberLong(' . $var->__toString() . ')');
                    return $this->param($this->getParamIndex());

                case "MongoDate":
                    $timezone = @date_default_timezone_get();
                    date_default_timezone_set("UTC");
                    $this->setJsonParams(
                        $this->getParamIndex(),
                        "ISODate(\"" .
                        date("Y-m-d", $var->sec) . "T" . date("H:i:s.", $var->sec) .
                        ($var->usec/1000) . "Z\")"
                    );
                    date_default_timezone_set($timezone);
                    return $this->param($this->getParamIndex());

                case "MongoTimestamp":
                    $this->setJsonParams(
                        $this->getParamIndex(),
                        call_user_func(
                            $jsonService,
                            array(
                                "t" => $var->inc * 1000,
                                "i" => $var->sec
                            )
                        )
                    );
                    return $this->param($this->getParamIndex());

                case "MongoMinKey":
                    $this->setJsonParams($this->getParamIndex(), call_user_func($jsonService, array( '$minKey' => 1)));
                    return $this->param($this->getParamIndex());

                case "MongoMaxKey":
                    $this->setJsonParams($this->getParamIndex(), call_user_func($jsonService, array( '$maxKey' => 1)));
                    return $this->param($this->getParamIndex());

                case "MongoCode":
                    $this->setJsonParams($this->getParamIndex(), $var->__toString());
                    return $this->param($this->getParamIndex());

                default:
                    if (method_exists($var, "__toString")) {
                        return $var->__toString();
                    }
                    return '<unknown type>';
            }
        }

        // ToDo: handle this a bit better
        return $var;
    }

    /*private function formatVarAsJSON($var, $jsonService, $key = false)
    {
        if (is_scalar($var) || is_null($var)) {
            switch (gettype($var)) {
                case "integer":
                    $this->paramIndex ++;
                    $this->jsonParams[$this->paramIndex] = 'NumberInt(' . $var . ')';
                    return $this->param($this->paramIndex);
                default:
                    //return $var;
                    return '';
            }
        }
        if (is_array($var)) {
            foreach ($var as $index => $value) {
                $var[$index] = $this->formatVarAsJSON($value, $jsonService);
            }
            return $var;
        }
        if (is_object($var)) {
            $this->paramIndex ++;
            switch (get_class($var)) {
                case "MongoDB\BSON\ObjectId":
                    $this->jsonParams[$this->paramIndex] = 'ObjectId("' . $var->__toString() . '")';
                    return $this->param($this->paramIndex);

                case "MongoInt32":
                    $this->jsonParams[$this->paramIndex] = 'NumberInt(' . $var->__toString() . ')';
                    return $this->param($this->paramIndex);

                case "MongoInt64":
                    $this->jsonParams[$this->paramIndex] = 'NumberLong(' . $var->__toString() . ')';
                    return $this->param($this->paramIndex);

                case "MongoDate":
                    $timezone = @date_default_timezone_get();
                    date_default_timezone_set("UTC");
                    $this->jsonParams[$this->paramIndex] =
                        "ISODate(\"" . date("Y-m-d", $var->sec) . "T" . date("H:i:s.", $var->sec) . ($var->usec/1000) . "Z\")";
                    date_default_timezone_set($timezone);
                    return $this->param($this->paramIndex);

                case "MongoTimestamp":
                    $this->jsonParams[$this->paramIndex] = call_user_func($jsonService, array(
                        "t" => $var->inc * 1000,
                        "i" => $var->sec
                    ));
                    return $this->param($this->paramIndex);

                case "MongoMinKey":
                    $this->jsonParams[$this->paramIndex] = call_user_func($jsonService, array( '$minKey' => 1));
                    return $this->param($this->paramIndex);

                case "MongoMaxKey":
                    $this->jsonParams[$this->paramIndex] = call_user_func($jsonService, array( '$maxKey' => 1));
                    return $this->param($this->paramIndex);

                case "MongoCode":
                    $this->jsonParams[$this->paramIndex] = $var->__toString();
                    return $this->param($this->paramIndex);

                default:
                    if (method_exists($var, "__toString")) {
                        return $var->__toString();
                    }
                    return '<unknown type>';
            }
        }
    }*/

    /*
     * Format JSON to pretty style
     *
     * //@param   string  $json   JSON to format
     * //@return  string
     *
    /*public static function jsonFormat($json): string
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
                    if (!$in_string)
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
                    if (!$in_string)
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
                    if (!$in_string)
                    {
                        $new_json .= ",\n" . str_repeat($tab, $indent_level);
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;

                case ':':
                    if (!$in_string)
                    {
                        $new_json .= ": ";
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;

                case '"':
                    if ($c > 0 && $json[$c-1] != '\\')
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
    }*/
}
