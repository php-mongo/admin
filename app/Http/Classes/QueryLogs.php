<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      QueryLogs.php 1001 21/8/20, 12:27 am  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   QueryLogs.php
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

/**
 * Class QueryLogs
 * @package App\Http\Classes
 */
class QueryLogs
{
    /**
     * @var string  $storage    Storage path top level
     */
    private $storage;

    /**
     * @var string  $error      Tracking errors
     */
    private $error;

    /**
     * @var boolean $enabled    To log or not to log
     */
    private $enabled;

    /**
     * @var array   $logs       Stores any logs for display
     */
    private $logs = [];

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * Return the full path to a query log file
     *
     * @param string $db
     * @param string $collection
     *
     * @return string
     */
    private function _logFile($db, $collection)
    {
        $user = auth()->guard('api')->user();
        return $this->storage . DIRECTORY_SEPARATOR . urlencode($user->user) . "-query-" . urlencode($db) . "-" . urlencode($collection) . ".php";
    }

    /**
     * QueryLogs constructor.
     */
    public function __construct()
    {
        $this->enabled = config('app.dbQueryLog', false);
        // ToDo: ?? maybe the log path may needs to be provided as a config value ??
        $this->storage = storage_path('app/query-log');
    }

    /**
     * Log a query
     *
     * @param string $db
     * @param string $collection
     * @param string $query
     *
     * @return bool
     */
    public function logQuery( string $db, string $collection, string $query )
    {
        // double check the $enabled status
        if ($this->enabled && isset($db, $collection, $query)) {
            if (!empty($query) && strlen(trim($query, "{} \t\n\r")) > 0) {
                if (!file_exists($this->storage)) {
                    mkdir($this->storage);
                }
                if (is_writable($this->storage)) {
                    $logFile    = $this->_logFile($db, $collection);
                    $fp = null;
                    if (!is_file($logFile)) {
                        // new file
                        $fp = fopen($logFile, "a+");
                        // this prevents the log file from being opened
                        fwrite($fp, '<?php exit("Permission Denied"); ?>' . "\n");
                    }
                    else {
                        // open existing
                        $fp = fopen($logFile, "a+");
                    }
                    // log query
                    fwrite($fp, date("Y-m-d H:i:s") . "\n" . var_export($query, true) . "\n================\n");
                    fclose($fp);

                    // ce la vi
                    return true;
                }
            }
        }
        // opps!
        return false;
    }

    /**
     * Return the query history
     *
     * @param string $db
     * @param string $collection
     *
     * @return array
     */
    public function getQueryHistory(string $db, string $collection) {
        $logs        = array();
        $criterias   = array();
        $this->error = null;

        // only if enabled
        if ($this->enabled) {
            $logFile    = $this->_logFile($db, $collection);
            $this->logs = array();

            // if the file exists
            if (is_file($logFile)) {
                $size = 10240;
                $fp   = fopen($logFile, "r");
                fseek($fp, -$size, SEEK_END);
                $text = fread($fp, $size);
                fclose($fp);

                preg_match_all("/(\\d+\\-\\d+\\-\\d+\\s+\\d+:\\d+:\\d+)\n(.+)(={10,})/sU", $text, $match);

                foreach ($match[1] as $k => $time) {
                    /*$eval   = new VarEval($match[2][$k]);
                    $params = $eval->execute();*/
                    $query = $match[2][$k];
                    if (!in_array($query, $criterias)) {
                        $logs[] = array(
                            "time"   => $time,
                            "params" => $query,
                            "query"  => http_build_query($query)
                        );
                        $criterias[] = $query;
                    }
                }

                if (!is_writeable($logFile)) {
                    $this->error = "To use log_query feature, please make file '{$logFile}' writeable.";
                }

            }
            else {
                $dirname = dirname($logFile);
                if (!is_writeable($dirname)) {
                    $this->error = "To use log_query feature, please make directory '{$dirname}' writeable.";
                }
            }
        }
        $this->logs = array_slice(array_reverse($logs), 0, 10);

        return $this->logs;
    }

    /**
     * Delete the query log for the current database.collection
     *
     * @param string $db
     * @param string $collection
     *
     * @return int
     */
    public function clearQueryHistory(string $db, string $collection)
    {
        if ($this->enabled) {
            $logFile = $this->_logFile($db, $collection);
            if (is_file($logFile)) {
                if (@unlink($logFile)) {
                    return 1;
                }
                else {
                    return 2;
                }
            }
        }
        return 3;
    }
}
