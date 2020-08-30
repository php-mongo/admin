<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      DocumentHelper.php 1001 14/8/20, 7:25 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App\Helpers
 * @subpackage   DocumentHelper.php
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
 * Children! It's time for class
 */
use App\Http\Classes\VarEval;

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
 * Class DocumentHelper
 * @package App\Helpers
 */
class DocumentHelper
{
    /**
     * Run a basic validation on the JSON string
     *
     * @param   string $mixed This should be a JSON string
     * @return  string|bool
     */
    public static function varEval( $mixed )
    {
        if ($mixed) {
            // there should be an equal number of opposite braces and square brackets
            $leftResult  = preg_match_all('/({|\[)/', $mixed, $left);
            $rightResult = preg_match_all('/(}|\])/', $mixed, $right);
            // we need to make suet there are an even number of quotes
            $quoteResult = preg_match_all('/(")/', $mixed, $quote);

            if ($leftResult == $rightResult && $quoteResult % 2 == 0) {
                return $mixed;
            }
            else {
                return false;
            }
        }
        return false;
    }

    /**
     * convert variable from string values
     *
     * @param string $database The target database
     * @param string $dataType data type
     * @param string $format string format
     * @param string|integer|double|float|mixed $value Valee may be string, integer, long, float|double, mixed (array or object)
     * @return mixed
     * @throws Exception
     */
    public static function convertValue($database, $dataType, $format, $value)
    {
        $realValue = null;
        switch ($dataType) {
            case "integer":
                if (class_exists("MongoInt32")) {
                    $realValue = new MongoInt32($value);
                }
                else {
                    $realValue = intval($value);
                }
                break;

            case "long":
                if (class_exists("MongoInt64")) {
                    $realValue = new MongoInt64($value);
                }
                else {
                    $realValue = $value;
                }
                break;

            case "float":
            case "double":
                $realValue = doubleval($value);
                break;

            case "string":
                $realValue = $value;
                break;

            case "boolean":
                $realValue = ($value == "true");
                break;

            case "null":
                $realValue = NULL;
                break;

            case "mixed":
                // ToDo: !! as the 'eval' method is being deprecated - lets opt for something simpler and more PHP oriented !!
                /*$eval = new VarEval($value, $format, $database);
                $realValue = $eval->execute();*/
                $realValue = self::varEval( $value );
                if ($realValue === false) {
                    throw new Exception("Unable to parse mixed value, check your syntax!");
                }
                break;
        }
        return $realValue;
    }

    /**
     * Format JSON to pretty (indented) style
     *
     * @param   string  $json   JSON to format
     * @return  string
     */
    public static function jsonFormat($json)
    {
        $tab          = "  ";
        $string       = "";
        $indent_level = 0;
        $in_string    = false;

        // we expect JSON - decode then encode
        $json_obj = json_decode($json);
        if ($json_obj === false) {
            //. opps !!
            return false;
        }
        $json = json_encode($json_obj);

        $len  = strlen($json);

        // $c is our INDEX
        for ($c = 0; $c < $len; $c++)
        {
            // handle each char
            $char = $json[ $c ];
            switch ( $char ) {
                case '{':
                case '[':
                    if ( !$in_string ) {
                        $string .= $char . "\n" . str_repeat($tab, $indent_level+1);
                        $indent_level++;
                    }
                    else {
                        $string .= $char;
                    }
                    break;

                case '}':
                case ']':
                    if ( !$in_string ) {
                        $indent_level--;
                        $string .= "\n" . str_repeat($tab, $indent_level) . $char;
                    }
                    else {
                        $string .= $char;
                    }
                    break;

                case ',':
                    if ( !$in_string ) {
                        $string .= ",\n" . str_repeat($tab, $indent_level);
                    }
                    else
                    {
                        $string .= $char;
                    }
                    break;

                case ':':
                    if ( !$in_string ) {
                        $string .= ": ";
                    }
                    else
                    {
                        $string .= $char;
                    }
                    break;

                case '"':
                    if ( $c > 0 && $json[ $c-1 ] != '\\' ) {
                        $in_string = !$in_string;
                    }
                    break;

                default:
                    $string .= $char;
                    break;
            }
        }
        return $string;
    }
}
