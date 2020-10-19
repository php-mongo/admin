<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      MongoHelper.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   MongoHelper.php
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
 * Every good name deserves a space
 */
namespace App\Helpers;

/**
 * We are handling MongoDB based functionality
 */
use MongoDB;

/**
 * These are specific MongoDB types we ant to implement
 */
use MongoId;
use MongoInt32;
use MongoInt64;

/**
 * Always be prepared to accept failure !!
 */
use Exception;

/**
 * Class MongoHelper
 * @package App\Helpers
 */
class MongoHelper
{
    /**
     * Substr utf-8 version
     *
     * @param   mixed   $str
     * @param   mixed   $from
     * @param   mixed   $len
     *
     * @return  mixed
     *
     * @author sajjad at sajjad dot biz (copied from PHP manual)
     */
    public static function utf8_substr( $str, $from, $len )
    {
        return function_exists('mb_substr') ?
            mb_substr( $str, $from, $len, 'UTF-8' ) :
            preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $from .'}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $len .'}).*#s','$1', $str);
    }

    /**
     * Convert unicode in json to utf-8 chars
     *
     * @param   string  $json   String to convert
     *
     * @return  string  utf-8 string
     */
    public static function json_unicode_to_utf8( $json )
    {
        $json = preg_replace_callback("/\\\\u([0-9a-f]{4})/", function($match) {
                $val = intval($match[1], 16);
                $c   = "";
                if ($val < 0x7F) {        // 0000-007F
                    $c .= chr($val);

                } elseif ($val < 0x800) { // 0080-0800
                    $c .= chr(0xC0 | ($val / 64));
                    $c .= chr(0x80 | ($val % 64));

                } else {                  // 0800-FFFF
                    $c .= chr(0xE0 | (($val / 64) / 64));
                    $c .= chr(0x80 | (($val / 64) % 64));
                    $c .= chr(0x80 | ($val % 64));
                }
                return $c;

            }, $json);

       /* $json = preg_replace_callback("/\\\\u([0-9a-f]{4})/", create_function('$match', '
            $val = intval($match[1], 16);
            $c = "";
            if($val < 0x7F){        // 0000-007F
                $c .= chr($val);
            } elseif ($val < 0x800) { // 0080-0800
                $c .= chr(0xC0 | ($val / 64));
                $c .= chr(0x80 | ($val % 64));
            } else {                // 0800-FFFF
                $c .= chr(0xE0 | (($val / 64) / 64));
                $c .= chr(0x80 | (($val / 64) % 64));
                $c .= chr(0x80 | ($val % 64));
            }
            return $c;
        '), $json);*/

        return $json;
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

        // we expect JSON - decode then encode
        $json_obj = json_decode($json);
        if (false === $json_obj) {
            return false;
        }
        $json = json_encode($json_obj);
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
                    if ('\\' != $json[$c-1] && $c > 0)
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
     * Format JSON to pretty html
     *
     * @param string $json JSON to format
     * @return string
     */
    public static function json_format_html($json)
    {
        $json         = self::json_unicode_to_utf8($json);
        $tab          = "&nbsp;&nbsp;";
        $new_json     = "";
        $indent_level = 0;
        $in_string    = false;

        $len = strlen($json);

        for ($c = 0; $c < $len; $c++)
        {
            $char = $json[$c];
            switch ($char)
            {
                case '{':
                case '[':
                    $char = "<span style=\"color: green\">" . $char . "</span>";//iwind
                    if (!$in_string) {
                        $new_json .= $char . "<br/>" . str_repeat($tab, $indent_level+1);
                        $indent_level++;
                    }
                    else {
                        $new_json .= "[";
                    }
                    break;

                case '}':
                case ']':
                    $char = "<span style=\"color: green\">" . $char . "</span>";//iwind
                    if (!$in_string)
                    {
                        $indent_level--;
                        $new_json .= "<br/>" . str_repeat($tab, $indent_level) . $char;
                    }
                    else
                    {
                        $new_json .= "]";
                    }
                    break;

                case ',':
                    $char = "<span style=\"color: green\">" . $char . "</span>";//iwind
                    if (!$in_string) {
                        $new_json .= ",<br/>" . str_repeat($tab, $indent_level);
                    }
                    else {
                        $new_json .= ",";
                    }
                    break;

                case ':':
                    $char = "<span style=\"color:green\">" . $char . "</span>";//iwind
                    if ($in_string) {
                        $new_json .= ":";
                    }
                    else {
                        $new_json .= $char;
                    }
                    break;

                case '"':
                    if ( '\\' != $json[$c-1] && $c > 0 ) {
                        $in_string = !$in_string;
                        if ($in_string) {
                            $new_json .= "<span style=\"color: #DD0000\" class=\"string_var\">" . $char;
                        }
                        else {
                            $new_json .= $char . "</span>";
                        }
                        break;
                    }
                    else if (0 == $c) {
                        $in_string = !$in_string;
                        $new_json .= "<span style=\"color: red\">" . $char;
                        break;
                    }

                default:
                    if (!$in_string && "" !== trim($char)) {
                        $char = "<span style=\"color: blue\">" . $char . "</span>";
                    }
                    else {
                        if ("&" == $char || "'" == $char || "\"" == $char || "<" == $char || ">" == $char) {
                            $char = htmlspecialchars($char);
                        }
                    }
                    $new_json .= $char;
                    break;
            }
        }
        $new_json = preg_replace_callback("{(<span style=\"color: blue\">([\\da-zA-Z_\\.]+)</span>)+}", function($match) {
                $string = str_replace("<span style=\"color: blue\">", "", $match[0]);
                $string = str_replace("</span>", "", $string);
                return "<span class=\"no_string_var\" style=\"color: blue\">" . $string . "</span>";
            }, $new_json);

        return $new_json;
    }

    /**
     * Format ID to string
     *
     * @param mixed $id object ID
     *
     * @return string
     */
    public static function id_string($id)
    {
        if (is_object($id) && $id instanceof MongoId) {
            return "rid_object:" . $id->__toString();
        }
        if (is_object($id)) {
            return "rid_" . get_class($id) . ":" . $id->__toString();
        }
        if (is_scalar($id)) {
            return "rid_" . gettype($id) . ":" . $id;
        }
        return "rid_mixed:" . base64_encode(var_export($id, true));
    }

    /**
     * @param $document
     * @param $documentArr
     * @param $fields
     */
    public static function prepareDocument( $document, & $documentArr, & $fields )
    {
        $arr    = [];
        $level  = 0;
        // a function to handle recursive document levels
        // iterate the document
        foreach ($document as $key => $value) {
            if ('_id' == $key) {
                if (is_object($value)) {
                    $oid = $value->__toString();
                } else {
                    $oid = $value;
                }
                $arr['key.' . $key . '.key'] = 'id.' . $oid . '.id';

            } else {
                if ($value instanceof MongoDB\Model\BSONDocument) {
                    /** @var MongoDB\Model\BSONDocument  $value */
                    $array = $value->getArrayCopy();
                    $level++;
                    $arr['key.' . $key . '.key'] = self::iterateObject($array, $level, $key, $fields);

                } elseif ($value instanceof MongoDB\BSON\Binary) {
                    /** @var MongoDB\BSON\Binary $value */
                    $data = bin2hex($value-> getData());
                    $arr['key.' . $key . '.key'] = 'value.' . $data . '.value';
                    if (is_string($key)) {
                        if (!in_array($key, $fields)) {
                            $fields[] = $key;
                        }
                    }

                } elseif ($value instanceof MongoDB\Model\BSONArray) {
                    /** @var MongoDB\Model\BSONArray $value */
                    $array = $value->getArrayCopy();
                    $level++;
                    $arr['key.' . $key . '.key'] = self::iterateObject($array, $level, $key, $fields);

                } else {
                    $arr['key.' . $key . '.key'] = 'value.' . $value . '.value';
                    if (is_string($key)) {
                        if (!in_array($key, $fields)) {
                            $fields[] = $key;
                        }
                    }
                }
            }
        }
        // add each processed document to the parent array
        $documentArr[] = $arr;
    }

    /**
     * @param $array
     * @param $level
     * @param $key
     * @param $fields
     *
     * @return array
     */
    public static function iterateObject( $array, $level, $key, & $fields )
    {
        $arr = [];
        foreach ($array as $k => $v) {
            if ($v instanceof MongoDb\Model\BSONDocument) {
                $level++;
                $arr['key.' . $k . '.key'] = self::iterateObject($v, $level, $k, $fields);

            } elseif ($v instanceof MongoDb\Model\BSONArray) {
                /** @var MongoDb\Model\BSONArray $v */
                $arr = $v->getArrayCopy();
              //  $level++;
                if (0 == count($v)) {
                    $arr['key.' . $k . '.key'] = [];
                } else {
                    $arr['key.' . $k . '.key'] = $v; //self::iterateObject($v, $level, $k, $fields);
                }

            } else {
                if (is_string($k)) {
                    if (!in_array($key . '.' . $k, $fields)) {
                        $fields[] = $key . '.' . $k;
                    }
                }
                $arr['key.' . $k . '.key'] = 'value.' . $v . '.value';
            }
        }
        return $arr;
    }

    /**
     * Extract the document into an array
     * Here we attempt to track the field keys that are found
     *
     * @param $document
     * @param array $fields
     * @param bool  $OID
     *
     * @return array
     */
    public static function extractDocument( $document, &$fields = [], $OID = false )
    {
        $arr    = [];
        $level  = 0;
        // a function to handle recursive document levels
        // iterate the document
        foreach ($document as $key => $value) {
            if ('_id' == $key) {
                if (false == $OID && is_object($value)) {
                    $oid = $value->__toString();
                }
                elseif (true == $OID && !is_string($value)) {
                    $oid = array("oid" => $value->__toString());
                }
                else {
                    $oid = $value;
                }
                $arr[ $key ] =  $oid;

            } else {
                if ($value instanceof MongoDB\Model\BSONDocument) {
                    /** @var MongoDB\Model\BSONDocument  $value */
                    $array = $value->getArrayCopy();
                    $level++;
                    $arr[ $key ] = self::iterateDocument($array, $level);

                } elseif ($value instanceof MongoDB\BSON\Binary) {
                    /** @var MongoDB\BSON\Binary $value */
                    $data = bin2hex($value-> getData());
                    $arr[ $key ] =  $data;
                    if (is_string($key)) {
                        if (!in_array($key, $fields)) {
                            $fields[] = $key;
                        }
                    }

                } elseif ($value instanceof MongoDB\Model\BSONArray) {
                    /** @var MongoDB\Model\BSONArray $value */
                    $array = $value->getArrayCopy();
                    $level++;
                    $arr[ $key ] = self::iterateDocument($array, $level);

                } else {
                    $arr[ $key ] = $value ;
                }
            }
        }
        // return array
        return $arr;
    }

    /**
     * @param $array
     * @param $level
     * @param $key
     * @param $fields
     *
     * @return array
     */
    public static function iterateDocument( array $array, int $level )
    {
        $arr = [];
        foreach ($array as $k => $v) {
            if ($v instanceof MongoDb\Model\BSONDocument) {
                $level++;
                $arr[ $k ] = self::iterateDocument($v->getArrayCopy(), $level);

            } elseif ($v instanceof MongoDb\Model\BSONArray) {
                /** @var MongoDb\Model\BSONArray $v */
                $arr = $v->getArrayCopy();
                //  $level++;
                if (0 == count($v)) {
                    $arr[ $k ] = [];
                } else {
                    $arr[ $k ] = $v;
                }

            } else {
                $arr[ $k ] = $v;
            }
        }
        return $arr;
    }

    /**
     * Returns the objects for the given collection
     *
     * @param   MongoDB\Client $client          Mongo Client
     * @param   string         $db              string DB Name
     * @param   string         $collection      string Collection name
     * @return  array
     */
    public static function getObjects($client, $db, $collection)
    {
        // no errors this way
        $arr = array(
            "objects" => [],
            "count" => 0
        );

        $cursor    = $client->$db->selectCollection($collection);
        $objects   = $cursor->find();
        $array     = $objects->toArray();

        foreach ($array as $object) {
            $arr['objects'][] = $object;
        }
        $arr['count'] = count($arr['objects']);
        return $arr;
    }

    /**
     * This is used in the IMPORT method
     *
     * @param $insert
     * @return string|string[]
     */
    public static function getCollectionNameFromInsert( $insert )
    {
        $str = substr( $insert, 0, strpos( $insert, ".insert") );
        return str_replace(array('db.getCollection("', '")'), "", $str );
    }

    /**
     * Ths is used in the IMPORT method
     *
     * @param $insert
     * @return string|string[]
     */
    public static function getDateFromInsert( $insert )
    {
        $str = substr( $insert, strpos( $insert, "{"), strlen( $insert ));
        return str_replace( array(');', ''), "", $str );
    }

    /**
     * Generate a mongodb namespace
     *
     * @param  string $db
     * @param  string $coll
     * @return string
     */
    public static function ns(string $db, string $coll) : string
    {
        return $db . '.' . $coll;
    }

    /**
     * Handle Bulk Inserts
     *
     * @param MongoDB\Driver\Manager $manager
     * @param string                 $database
     * @param array                  $array
     * @param string                 $collection
     * @param boolean                $useCollection
     * @param integer                $inserted
     * @param boolean                $isJson         Enforces use of the provided $collection value
     */
    public static function handleBulkInsert( $manager, $database, $array, $collection, $useCollection, &$inserted, $isJson = false )
    {
        foreach ($array as $coll => $inserts) {
            // renew for each collection insert
            $bulk = new MongoDB\Driver\BulkWrite();

            // If $useCollection is TRUE : all inserts will use the 'current collection' in the namespace
            // If $isJson is TRUE we must use the provided $collection
            $ns = false == $useCollection && false == $isJson ? self::ns( $database, $coll) : self::ns($database, $collection);

            if ($isJson) {
                // form JSON insert use only the internal iteration
                $inserts = $array;
            }

            if (is_array($inserts)) {
                // iterate the insert and add to the $bulk write object
                foreach ($inserts as $insert) {
                    if (is_string($insert)) {
                        // decode the insert only is its a string
                        $insert = json_decode($insert, true);
                    }
                    // if the ObjectId has been included as $oid => 'blah blah blah'
                    $isOID = $insert['_id'] instanceof MongoDB\BSON\ObjectId;
                    // this fits our local admin export
                    if (false == $isOID && isset($insert['_id']['oid'])) {
                        $insert['_id'] = new MongoDB\BSON\ObjectId ( $insert['_id']['oid'] );
                    }
                    // this fits the Compass JSON export
                    if (isset($insert['_id']['$oid'])) {
                        $insert['_id'] = new MongoDB\BSON\ObjectId ( $insert['_id']['$oid'] );
                    }
                    // expects an array
                    $bulk->insert($insert);
                    $inserted++;
                }
                $manager->executeBulkWrite( $ns, $bulk );

                if ($isJson) {
                    // iterations should be complete
                    break;
                }
            }
        }
    }

    /**
     * @param  array $properties
     * @param  array $errors
     * @return array|bool
     */
    public static function validateCollectionProperties( array $properties, &$errors )
    {
        if (true == $properties['capped']) {
            // validate the params
            // size must be set if collection is capped
            if (!empty($properties['size']) && is_numeric($properties['size']) && $properties['size'] >= 1) {
                // ToDo: !! 1 is a ridiculously small test - but will suffice for now
                if (!empty($properties['max']) && !is_numeric($properties['max'])) {
                    $errors[] = 'max document count is invalid';
                    return false;
                }
                return array(
                    "capped" => $properties['capped'],
                    "size" => (int) $properties['size'],
                    "max" => (int) $properties['max']
                );
            }
            $errors[] = 'size must be a positive value if capped is true';
            return false;
        }
        // Capped is false! Ensure all values are empty
        return array(
            "capped" => '',
            "size" => '',
            "max" => ''
        );
    }

    /**
     * Format bytes to human size
     *
     * @param integer $bytes Size in byte
     * @param integer $precision Precision
     *
     * @return string size in k, m, g..
     *
     * @since 1.0.0
     */
    public static function readHumanBytes($bytes, $precision = 2)
    {
        if (0 == $bytes) {
            return 0;
        }
        if ($bytes < 1024) {
            return $bytes . "B";
        }
        if ($bytes < 1024 * 1024) {
            return round($bytes/1024, $precision) . "k";
        }
        if ($bytes < 1024 * 1024 * 1024) {
            return round($bytes/1024/1024, $precision) . "m";
        }
        if ($bytes < 1024 * 1024 * 1024 * 1024) {
            return round($bytes/1024/1024/1024, $precision) . "g";
        }
        return $bytes;
    }
}
