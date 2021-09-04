<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      MongoHelper.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   Helpers
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

use App\Exceptions\UnableToDeleteUserException;
use App\Http\Classes\MongoConnection;
use MongoDB;

/**
 * These are specific MongoDB types we want to implement
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
     * Returns true if user roles do not include the 'root' role
     * Prevents accidental deletion of a root assigned user
     *
     * @param MongoDB\Client $client
     * @param array $arr
     * @return bool
     */
    private static function notRootUser(MongoDb\Client $client, array $arr): bool
    {
        if ($arr[0] !== 'admin') {
            // ignore any users not aligned with the admin table
            return true;
        }

        $db = 'admin';
        $collection = $client->$db->selectCollection('system.users');
        $result = $collection->find(['_id' => implode('.', $arr)]);

        $roles = self::extractDocument($result->toArray()[0])['roles'];
        foreach ($roles as $role) {
            if ($role['role'] === 'root') {
                // 'root' role found for this user
                return false;
            }
        }

        return true;
    }

    /**
     * @param   string $id
     * @return  mixed|string
     */
    private static function getDbFromId(string $id)
    {
        return strpos($id, ".") !== false ? explode(".", $id)[0] : 'admin';
    }

    /**
     * @param array $updated
     * @param string $key
     * @return bool
     */
    private static function findUpdateKey(array $updated, string $key): bool
    {
        if (in_array($key, $updated)) {
            return true;
        }
        return false;
    }

    /**
     * Generic static function to create a MongoDB user
     *
     * @param MongoConnection $mongoDb
     * @param array $data
     * @return array|string
     */
    public static function generateMongoDbUser(MongoConnection $mongoDb, array $data)
    {
        if ($mongoDb->checkConfig()) {
            $mongoDb->connectClient();
            $mongo = $mongoDb->getClient();
            $roleDb = !empty($data['database']) ? $data['database'] : 'admin';
            $db = $mongo->selectDatabase($roleDb);

            // core command
            $command = array(
                "createUser" => $data['user'],
                "pwd" => $data['password'],
                "roles" => null
            );

            // build the roles array
            $roles = [];
            foreach ($data['roles'] as $role) {
                switch ($role) {
                    case 'readAnyDatabase':
                    case 'readWriteAnyDatabase':
                    case 'userAdminAnyDatabase':
                    case 'dbAdminAnyDatabase':
                        $roles[] = array(
                            "role" => $role, "db" => $roleDb
                        );
                        break;

                    default:
                        $roles[] = array(
                            "role" => $role, "db" => $data['database']
                        );
                }
            }
            $command['roles'] = $roles;

            /*@var MongoDB\Driver\Cursor $result */
            /** @var MongoDB\Model\BSONDocument $result */
            $result = $db->command($command)->toArray()[0];

            if (isset($result->getArrayCopy()['ok']) && $result->getArrayCopy()['ok'] == 1.0) {
                // get the user
                $collection = $db->selectCollection('system.users');
                $user = $collection->find(['_id' => $db . '.' . $data['user']]);
                $user = $user->toArray()[0]->getArrayCopy();
                unset($user['credentials']);
                return self::extractDocument($user);
            }
        }
        return '';
    }

    /**
     * @param MongoConnection $mongoDb
     * @param array $data
     * @return array[]|null[]
     */
    public static function updateMongodbUser(MongoConnection $mongoDb, array $data)
    {
        if ($mongoDb->checkConfig()) {
            $mongoDb->connectClient();
            $client = $mongoDb->getClient();
            $roleDb = self::getDbFromId($data['id']);
            //$collection = $client->$roleDb->selectCollection('system.users');
            //$user = $collection->find(['_id' => $data['id']]);
            $db = $client->selectDatabase($roleDb);

            // core command
            $command = array(
                "updateUser" => $data['user']
            );

            //dd($data['updated']);

            if (self::findUpdateKey($data['updated'], 'password')) {
                $command['pwd'] = $data['password'];
            }

            if (self::findUpdateKey($data['updated'], 'roles')) {
                // build the roles array
                $roles = [];
                foreach ($data['roles'] as $role) {
                    switch ($role) {
                        case 'readAnyDatabase':
                        case 'readWriteAnyDatabase':
                        case 'userAdminAnyDatabase':
                        case 'dbAdminAnyDatabase':
                            $roles[] = array(
                                "role" => $role, "db" => $roleDb
                            );
                            break;

                        default:
                            $roles[] = array(
                                "role" => $role, "db" => $data['database']
                            );
                    }
                }
                $command['roles'] = $roles;
            }

        //    dd($command);

            /*@var MongoDB\Driver\Cursor $result */
            /** @var MongoDB\Model\BSONDocument $result */
            $result = $db->command($command)->toArray()[0];

            if (isset($result->getArrayCopy()['ok']) && $result->getArrayCopy()['ok'] == 1.0) {
                // get the user
                $collection = $db->selectCollection('system.users');
                $user = $collection->find(['_id' => $db . '.' . $data['user']]);
                $user = $user->toArray()[0]->getArrayCopy();
                unset($user['credentials']);
                return array('keys' => $data['updated'], 'user' => self::extractDocument($user));
            }
        }
        return array('keys' => null);
    }


    /**
     * Generic method to fetch MongoDB users
     *
     * @param MongoConnection $mongoDb
     * @return array
     */
    public static function getMongoDbUsers(MongoConnection $mongoDb): array
    {
        if ($mongoDb->checkConfig() && $mongoDb->hasUserAdminRoleOnDatabase($mongoDb->getUserDb('admin'))) {
            $db = 'admin';
            $mongoDb->connectClient();
            $client = $mongoDb->getClient();
            $collection = $client->$db->selectCollection('system.users');
            $objectsObj = self::getObjects($client, $db, $collection->getCollectionName());

            /**
             * Extract the BSON docs from $objects
             */
            $users    = [];
            $fields     = [];
            // extract the raw document
            foreach ($objectsObj['objects'] as $key => $obj) {
                // we need a raw version - easier to updated and manipulates with JS
                $user = self::extractDocument($obj, $fields, true);
                // don't return the passwords
                unset($user['credentials']);
                $user['message'] = ''; // add this to ensure the param is initialised in the vue component
                $users[]  = $user;
            }
            return $users;
        }
        return [];
    }

    /**
     * @param   MongoConnection $mongoDb
     * @param   string|null $oid
     * @return  bool|void
     */
    public static function deleteMongoDbUser(MongoConnection $mongoDb, ?string $oid)
    {
        $arr        = explode(".", $oid);
        $targetDb   = $arr[0];
        $user       = $arr[1];

        if ($mongoDb->checkConfig()) {
            $mongoDb->connectClient();
            $client = $mongoDb->getClient();
            if (self::notRootUser($client, $arr)) {
                $db = $client->selectDatabase($targetDb);

                $command = array(
                    "dropUser" => $user
                );

                $result = $db->command($command)->toArray()[0];
                return (isset($result->getArrayCopy()['ok']) && $result->getArrayCopy()['ok'] == 1.0);
            }
            throw new UnableToDeleteUserException('Unable to delete database user with a root role');
        }
        return false;
    }

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
    public static function utf8Substr($str, $from, $len)
    {
        return function_exists('mb_substr') ?
            mb_substr($str, $from, $len, 'UTF-8') :
            preg_replace(
                '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' .
                $from . '}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' .
                $len . '}).*#s',
                '$1',
                $str
            );
    }

    /**
     * Convert unicode in json to utf-8 chars
     *
     * @param   string $json   String to convert
     *
     * @return  string          utf-8 string
     */
    public static function jsonUnicodeToUtf8(string $json): string
    {
        return preg_replace_callback(
            "/\\\\u([0-9a-f]{4})/",
            function ($match) {
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
            },
            $json
        );

       /* return preg_replace_callback("/\\\\u([0-9a-f]{4})/", create_function('$match', '
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
    }

    /**
     * Format JSON to pretty style
     *
     * @param   string $json   JSON to format
     * @return  string
     */
    public static function jsonFormat(string $json): string
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
        for ($c = 0; $c < $len; $c++) {
            $char = $json[$c];
            switch ($char) {
                case '{':
                case '[':
                    if (!$in_string) {
                        $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                        $indent_level++;
                    } else {
                        $new_json .= $char;
                    }
                    break;

                case '}':
                case ']':
                    if (!$in_string) {
                        $indent_level--;
                        $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                    } else {
                        $new_json .= $char;
                    }
                    break;

                case ',':
                    if (!$in_string) {
                        $new_json .= ",\n" . str_repeat($tab, $indent_level);
                    } else {
                        $new_json .= $char;
                    }
                    break;

                case ':':
                    if (!$in_string) {
                        $new_json .= ": ";
                    } else {
                        $new_json .= $char;
                    }
                    break;

                case '"':
                    if ('\\' != $json[$c-1] && $c > 0) {
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
    public static function jsonFormatHtml(string $json): string
    {
        $json         = self::jsonUnicodeToUtf8($json);
        $tab          = "&nbsp;&nbsp;";
        $new_json     = "";
        $indent_level = 0;
        $in_string    = false;

        $len = strlen($json);

        for ($c = 0; $c < $len; $c++) {
            $char = $json[$c];
            switch ($char) {
                case '{':
                case '[':
                    $char = "<span style=\"color: green\">" . $char . "</span>";//iwind
                    if (!$in_string) {
                        $new_json .= $char . "<br/>" . str_repeat($tab, $indent_level + 1);
                        $indent_level++;
                    } else {
                        $new_json .= "[";
                    }
                    break;

                case '}':
                case ']':
                    $char = "<span style=\"color: green\">" . $char . "</span>";//iwind
                    if (!$in_string) {
                        $indent_level--;
                        $new_json .= "<br/>" . str_repeat($tab, $indent_level) . $char;
                    } else {
                        $new_json .= "]";
                    }
                    break;

                case ',':
                    $char = "<span style=\"color: green\">" . $char . "</span>";//iwind
                    if (!$in_string) {
                        $new_json .= ",<br/>" . str_repeat($tab, $indent_level);
                    } else {
                        $new_json .= ",";
                    }
                    break;

                case ':':
                    $char = "<span style=\"color:green\">" . $char . "</span>";//iwind
                    if ($in_string) {
                        $new_json .= ":";
                    } else {
                        $new_json .= $char;
                    }
                    break;

                case '"':
                    if ('\\' != $json[$c - 1] && $c > 0) {
                        $in_string = !$in_string;
                        if ($in_string) {
                            $new_json .= "<span style=\"color: #DD0000\" class=\"string_var\">" . $char;
                        } else {
                            $new_json .= $char . "</span>";
                        }
                        break;
                    } elseif (0 == $c) {
                        $in_string = !$in_string;
                        $new_json .= "<span style=\"color: red\">" . $char;
                        break;
                    }
                    // fall through to next case if no match occurs

                default:
                    if (!$in_string && "" !== trim($char)) {
                        $char = "<span style=\"color: blue\">" . $char . "</span>";
                    } else {
                        if ("&" == $char || "'" == $char || "\"" == $char || "<" == $char || ">" == $char) {
                            $char = htmlspecialchars($char);
                        }
                    }
                    $new_json .= $char;
                    break;
            }
        }
        return preg_replace_callback(
            "{(<span style=\"color: blue\">([\\da-zA-Z_\\.]+)</span>)+}",
            function ($match) {
                $string = str_replace("<span style=\"color: blue\">", "", $match[0]);
                $string = str_replace("</span>", "", $string);
                return "<span class=\"no_string_var\" style=\"color: blue\">" . $string . "</span>";
            },
            $new_json
        );
    }

    /**
     * Format ID to string
     *
     * @param mixed $id object ID
     *
     * @return string
     */
    public static function id_string($id): string
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
     * ToDo: we want to build a dedicated Class to better handle this
     *
     * @param   object  $document
     * @param   array   $documentArr
     * @param   array   $fields
     */
    public static function prepareDocument(object $document, array &$documentArr, array &$fields = []): void
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
                    $data = bin2hex($value->getData());
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
     * ToDo: we want to build a dedicated Class to better handle this
     *
     * @param $array
     * @param $level
     * @param $key
     * @param $fields
     *
     * @return array
     */
    public static function iterateObject($array, $level, $key, &$fields): array
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
     * ToDo: we want to build a dedicated Class to better handle this
     *
     * @param $document
     * @param array $fields
     *
     * @return array
     */
    public static function extractDocumentAsArray($document, array &$fields = []): array
    {
        $arr    = [];
        $level  = 0;
        // a function to handle recursive document levels
        // iterate the document
        foreach ($document->getArrayCopy() as $key => $value) {
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
        // return array
        return $arr;
    }

    /**
     * Extract the document into an array
     * Here we attempt to track the field keys that are found
     * ToDo: we want to build a dedicated Class to better handle this
     *
     * @param $document
     * @param array $fields
     * @param bool  $OID
     *
     * @return array
     */
    public static function extractDocument($document, array &$fields = [], bool $OID = false): array
    {
        $arr    = [];
        $level  = 0;
        // a function to handle recursive document levels
        // iterate the document
        foreach ($document as $key => $value) {
            if ('_id' === $key) {
                if (false == $OID && is_object($value)) {
                    $oid = $value->__toString();
                } elseif (true == $OID && !is_string($value)) {
                    $oid = array("oid" => $value->__toString());
                } else {
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
     * ToDo: we want to build a dedicated Class to better handle this
     *
     * @param array $array
     * @param int   $level
     *
     * @return array
     */
    public static function iterateDocument(array $array, int $level): array
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
    public static function getObjects(MongoDB\Client $client, string $db, string $collection): array
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
     * @param   string  $insert
     * @return  string|string[]
     */
    public static function getCollectionNameFromInsert(string $insert)
    {
        $str = substr($insert, 0, strpos($insert, ".insert"));
        return str_replace(array('db.getCollection("', '")'), "", $str);
    }

    /**
     * Ths is used in the IMPORT method
     *
     * @param $insert
     * @return string|string[]
     */
    public static function getDateFromInsert($insert)
    {
        $str = substr($insert, strpos($insert, "{"), strlen($insert));
        return str_replace(array(');', ''), "", $str);
    }

    /**
     * Generate a mongodb namespace
     *
     * @param  string $db
     * @param  string $coll
     * @return string
     */
    public static function ns(string $db, string $coll): string
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
    public static function handleBulkInsert(
        MongoDB\Driver\Manager $manager,
        string $database,
        array $array,
        string $collection,
        bool $useCollection,
        int &$inserted,
        bool $isJson = false
    ): void {
        foreach ($array as $coll => $inserts) {
            // renew for each collection insert
            $bulk = new MongoDB\Driver\BulkWrite();

            // If $useCollection is TRUE : all inserts will use the 'current collection' in the namespace
            // If $isJson is TRUE we must use the provided $collection
            $ns = false == $useCollection && false == $isJson ? self::ns($database, $coll) : self::ns($database, $collection);

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
                        $insert['_id'] = new MongoDB\BSON\ObjectId($insert['_id']['oid']);
                    } elseif (is_array($insert['_id']) && isset($insert['_id']['$oid'])) {
                        // this fits the Compass JSON export
                        $insert['_id'] = new MongoDB\BSON\ObjectId($insert['_id']['$oid']);
                    }
                    // expects an array
                    $bulk->insert($insert);
                    $inserted++;
                }
                $manager->executeBulkWrite($ns, $bulk);

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
    public static function validateCollectionProperties(array $properties, array &$errors)
    {
        if (true === $properties['capped']) {
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
    public static function readHumanBytes(int $bytes, int $precision = 2): string
    {
        if (0 === $bytes) {
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

    /**
     * Handle creating the remote connections
     *
     * @param   array           $params Connection parameters array
     * @return  MongoDB\Client
     */
    public static function remoteConnection(array $params): MongoDB\Client
    {
        $params['replicaSet'] = 'Cluster0-shard-0';
        $prefix = true === $params['dns'] ? 'mongodb+srv' : 'mongodb';
        $options = [];
        if (true === $params['atlas']) {
            // MongoDB Atlas server sets
            $uri = $prefix . '://' . $params['host'] . '/';
            $options['retryWrites'] = true;
            $options['replicaSet'] = $params['replicaSet'];
            // ToDo: for now we will enforce this value to alleviate errors
            $options['authSource'] = 'admin';
            $options['username'] = $params['username'];
            $options['password'] = $params['password'];
        } else {
            // All other servers
            $uri = $prefix . '://' . $params['host'] . ':' . $params['port'];
            if (true === $params['authenticate'] && !empty($params['username']) && !empty($params['password'])) {
                $options['username'] = $params['username'];
                $options['password'] = $params['password'];
            }
            if (true === $params['tls']) {
                $options['tls'] = true;
            }
            if (true === $params['ssl']) {
                $options['ssl'] = true;
            }
        }

        // A single connection call should handle all cases
        return new MongoDB\Client(
            $uri,
            $options
        );

        /*$client = new MongoDB\Client(
            'mongodb+srv://<username>:<password>@cluster0.kwrrj.mongodb.net/<dbname>?retryWrites=true&w=majority');
        $db = $client->test;*/
    }

    /**
     * Handle remote manager connections
     * ToDo: in most cases we will use - $dbLink->getManager() - instead
     *
     * @param $params
     * @return MongoDB\Driver\Manager
     * @throws Exception
     */
    public static function remoteManager($params): ?MongoDB\Driver\Manager
    {
        $prefix = true === $params['dns'] ? 'mongodb' : 'mongodb+srv';
        // create the URI
        if (true === $params['atlas']) {
            if (empty($params['username']) || empty($params['password']) || empty($params['remoteDatabase'])) {
                throw new Exception('Cannot connect to Mongo Atlas without a username, password and database reference');
            }
            $uri = $prefix . '://' . $params['username'] . ':' . $params['password'] . '@' . $params['host'] . '/' . $params['remoteDatabase'] . '?retryWrites=true&w=majority';
            return new MongoDB\Driver\Manager(
                $uri
            );

        } else {
            $uri = $prefix . '://' . $params['host'] . ':' . $params['port'];
            $options = [];
            if (true === $params['authenticate'] && !empty($params['username']) && !empty($params['password'])) {
                $options['username'] = $params['username'];
                $options['password'] = $params['password'];
            }
            if (true === $params['tls']) {
                $options['tls'] = true;
            }
            if (true === $params['ssl']) {
                $options['ssl'] = true;
            }
            return new MongoDB\Driver\Manager( $uri, $options);
        }
    }

    /**
     * Handle remote bulk write actions
     *
     * @param MongoDB\Driver\Manager $conn
     * @param $documents
     * @param $ns
     * @param $inserted
     */
    public static function remoteBulkWrite($conn, $documents, $ns, &$inserted) : void
    {
        $bulk = new MongoDB\Driver\BulkWrite();
        foreach ($documents as $insert) {
            $inserted++;
            if (is_string($insert)) {
                // decode the insert only is its a string
                $insert = json_decode($insert, true);
            }
            // if the ObjectId has been included as $oid => 'blah blah blah'
            $isOID = $insert['_id'] instanceof MongoDB\BSON\ObjectId;
            // this fits our local admin export
            if (false == $isOID && isset($insert['_id']['oid'])) {
                $insert['_id'] = new MongoDB\BSON\ObjectId ( $insert['_id']['oid']);
            }
            elseif (is_array($insert['_id']) && isset($insert['_id']['$oid'])) {
                // this fits the Compass JSON export
                $insert['_id'] = new MongoDB\BSON\ObjectId ( $insert['_id']['$oid']);
            }
            // expects an array
            $bulk->insert($insert);
        }
        $conn->executeBulkWrite( $ns, $bulk);
    }
}
