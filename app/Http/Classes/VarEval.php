<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      VarEval.php 1001 14/8/20, 7:54 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App\Http\Classes
 * @subpackage   VarEval.php
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
namespace App\Http\Classes;

use App\Http\Classes\MongoConnection;
use MongoDB\Collection;
use stdClass;

/**
 * Class VarEval
 * @package App\Http\Classes
 */
class VarEval
{
    /**
     * Source to run
     *
     * @var string
     */
    private $_source;

    /**
     * Source Format
     *
     * @var string
     */
    private $_format;

    /**
     * current MongoDB
     *
     * @var MongoConnection
     */
    private $_mongo;

    /**
     * @var string $_database The target database
     */
    private $_database;

    private function _fixEmptyObject(&$object) {
        if (is_array($object)) {
            foreach ($object as &$v) {
                $this->_fixEmptyObject($v);
            }
        }
        else if (is_string($object) && $object === "__EMPTYOBJECT__") {
            $object = new stdClass();
        }
    }

    private function _runPHP() {
        $this->_source = "return " . $this->_source . ";";
        // tokenizer extension may be disabled
        if (function_exists("token_get_all")) {
            $php = "<?php\n" . $this->_source . "\n?>";
            $tokens = token_get_all($php);
            foreach ($tokens as $token) {
                $type = $token[0];
                if (is_long($type)) {
                    if (in_array($type, array(
                        T_OPEN_TAG,
                        T_RETURN,
                        T_WHITESPACE,
                        T_ARRAY,
                        T_LNUMBER,
                        T_DNUMBER,
                        T_CONSTANT_ENCAPSED_STRING,
                        T_DOUBLE_ARROW,
                        T_CLOSE_TAG,
                        T_NEW,
                        T_DOUBLE_COLON
                    ))) {
                        continue;
                    }

                    if ($type == T_STRING) {
                        $func = strtolower($token[1]);
                        if (in_array($func, array(
                            //keywords allowed
                            "mongoid",
                            "mongocode",
                            "mongodate",
                            "mongoregex",
                            "mongobindata",
                            "mongoint32",
                            "mongoint64",
                            "mongodbref",
                            "mongominkey",
                            "mongomaxkey",
                            "mongotimestamp",
                            "true",
                            "false",
                            "null",
                            "__set_state",
                            "stdclass"
                        ))) {
                            continue;
                        }
                    }
                    exit("For your security, we stoped data parsing at '(" . token_name($type) . ") " . $token[1] . "'.");
                }
            }
        }
        return eval($this->_source);
    }

    /**
     * @return mixed
     */
    private function _runJson() {
        // Handle timezone efficiency
        $timezone = @date_default_timezone_get();
        date_default_timezone_set("UTC");

        // this is returned from the db.execute() function
        $command = array('eval' => 'function () {
			if (typeof(ISODate) == "undefined") {
				function ISODate (isoDateStr) {
				    if (!isoDateStr) {
				        return new Date;
				    }
				    var isoDateRegex = /(\d{4})-?(\d{2})-?(\d{2})([T ](\d{2})(:?(\d{2})(:?(\d{2}(\.\d+)?))?)?(Z|([+-])(\d{2}):?(\d{2})?)?)?/;
				    var res = isoDateRegex.exec(isoDateStr);
				    if (!res) {
				        throw "invalid ISO date";
				    }
				    var year = parseInt(res[1], 10) || 1970;
				    var month = (parseInt(res[2], 10) || 1) - 1;
				    var date = parseInt(res[3], 10) || 0;
				    var hour = parseInt(res[5], 10) || 0;
				    var min = parseInt(res[7], 10) || 0;
				    var sec = parseFloat(res[9]) || 0;
				    var ms = Math.round(sec % 1 * 1000);
				    sec -= ms / 1000;
				    var time = Date.UTC(year, month, date, hour, min, sec, ms);
				    if (res[11] && res[11] != "Z") {
				        var ofs = 0;
				        ofs += (parseInt(res[13], 10) || 0) * 60 * 60 * 1000;
				        ofs += (parseInt(res[14], 10) || 0) * 60 * 1000;
				        if (res[12] == "+") {
				            ofs *= -1;
				        }
				        time += ofs;
				    }
				    return new Date(time);
				};
			};

			function util_convert_empty_object_to_string(obj) {
				if (util_is_empty(obj)) {
					return "__EMPTYOBJECT__";
				}
				if (typeof(obj) == "object") {
					for (var k in obj) {
						obj[k] = util_convert_empty_object_to_string(obj[k]);
					}
				}
				return obj;
			};

			function util_is_empty(obj) {
				if (obj == null || typeof(obj) != "object" || (obj.constructor != Object)) {
					return false;
				}
			    for (var k in obj) {
			        if(obj.hasOwnProperty(k)) {
			            return false;
					}
			    }

			    return true;
			};
			var o = ' . $this->_source . '; return util_convert_empty_object_to_string(o); }'
        );

        $this->_mongo->connectManager();
        $ret = $this->_mongo->managerCommand($this->_database, $command);

        echo '<pre>'; var_dump($ret); echo '</pre>'; die;

        $this->_fixEmptyObject($ret);

        date_default_timezone_set($timezone);

        if ( $ret["ok"] ) {
            return $ret["retval"];
        }
        return json_decode($this->_source, true);
    }

    /**
     * VarEval constructor.
     *
     * @param $source
     * @param string $format
     * @param string $database
     */
    public function __construct($source, $format = "array", $database = null) {
        // this is the 'target' data
        $this->_source = $source;

        // handle format disscrepancies
        $this->_format = $format;
        if (!$this->_format) {
            $this->_format = "array";
        }

        // save our DB for later
        $user            = auth()->guard('api')->user();
        $this->_mongo    = new MongoConnection($user);
        $this->_database = $database;
    }

    /**
     * Execute the code
     *
     * @return mixed
     */
    public function execute() {
        if ($this->_format == "array") {
            return $this->_runPHP();
        }
        else if ($this->_format == "json") {
            return $this->_runJson();
        }
    }
}
