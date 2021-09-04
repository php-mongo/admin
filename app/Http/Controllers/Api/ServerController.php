<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      ServerController.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   ServerController.php
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
 *   Defines a namespace for the controller.
 */
namespace App\Http\Controllers\Api;

/**
 *   Defines the requests used by the controller.
 */
use Illuminate\Http\Request;

/**
 * Response object
 */
use Illuminate\Http\Response;

/**
 * Models
 */
use App\Models\User;

/**
 *   Defined controllers used by the controller
 */
use App\Http\Controllers\Controller;

/**
 *  Internal classes etc etc
 */
use App\Http\Classes\MongoConnection as Mongo;

/**
 * MongoDB
 */
use MongoDB;

/**
 * Used to read the composer file
 */
use Eloquent\Composer\Configuration\ConfigurationReader;

/**
 * All good things come to an end !!
 */
use Exception;

/**
 * Class ServerController
 * @package App\Http\Controllers\Api
 */
class ServerController extends Controller
{
    /**
     * @var int
     */
    private $limit = 30;

    /**
     * @var     string  $db Reference to the DB we'll use to fetch data
     */
    private $db;

    /**
     * @var     Mongo   $mongo
     */
    private $mongo;

    /**
     * @var     MongoDB\Driver\Manager
     */
    private $manager;

    /**
     * @var     MongoDB\Database $database (new MongoDB\Client)->admin;
     */
    private $database;

    /**
     * @var     string  $commandLine
     */
    private $commandLine;

    /**
     * @var     array   $webServers
     */
    private $webServers;

    /**
     * @var     string  $version
     */
    private $version;

    /**
     * @var     array   $directives
     */
    private $directives;

    /**
     * @var     array   $buildInfo
     */
    private $buildInfo;

    /**
     * @var     array   $connection
     */
    private $connection;

    /**
     * @var     array   $composer
     */
    private $composer;

    /**
     * @var     string|array|null $errorMessage
     */
    private $errorMessage = null;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @return string|array|null
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    private function getVersion()
    {
        try {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
            //$url      = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/version.php';
            $url      =  './version.php'; // this handles a relative PATH
            // initialise curl
            $ch = curl_init();

            // set url
            curl_setopt($ch, CURLOPT_URL, $url);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // $output contains the output string
            $output = curl_exec($ch);

            // close curl resource to free up system resources
            curl_close($ch);

            return $output;
        }
        catch(Exception $e) {
            $this->setErrorMessage($e->getMessage());
        }
    }

    /**
     * @param string|array|null $errorMessage
     * @return mixed|null
     */
    public function setErrorMessage($errorMessage): ?mixed
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Runs the command function on MongoDB
     */
    private function getCommandline()
    {
        // command line - run on admin table
        try {
            $arr    = [];
            $cursor = $this->database->command(array("getCmdLineOpts" => 1));
            foreach ($cursor as $document) {
                if (isset($document['argv'])) {
                    foreach ($document['argv'] as $argv) {
                        $arr[] = $argv;
                    }
                    break;
                }
            }
            $this->commandLine = implode(" ", $arr);

        } catch (Exception $e) {
            $this->commandLine = "Unable to run {getCmdLineOpts} on DB " . $this->db;
        }
    }

    /**
     *
     */
    private function getWebServer()
    {
        try {
            // MONGODB_VERSION
            // this successfully gets the extension version
            $version = phpversion("mongodb");
            $this->webServers = array();
            if (isset($_SERVER["SERVER_SOFTWARE"])) {
                list($webServer) = explode(" ", $_SERVER["SERVER_SOFTWARE"]);
                $this->webServers["server"] = $webServer;
            }
            $this->webServers['phpv'] = array('version' => '<a href="http://www.php.net" target="_blank">PHP</a> ' . PHP_VERSION);
            $this->webServers['phpe'] = array('extension' => '<a href="http://www.php.net/mongodb" target="_blank">Extension</a> - <a href="http://pecl.php.net/package/mongodb" target="_blank">mongodb</a>/' . $version);
        }
        catch (Exception $e) {
            $this->webServers = [];
        }
    }

    /**
     *
     */
    private function getDirectives()
    {
        try {
            $directives = ini_get_all("mongodb");
            $arr = [];
            $index = 0;
            /**
             * we need to grab the 'alpha keys' and re-index the array with a numeric index
             */
            foreach ($directives as $key => $array) {
                if (empty($array['global_value'])) {
                    $array['global_value'] = 0;
                }
                if (empty($array['local_value'])) {
                    $array['local_value'] = 0;
                }
                $arr[$index] = array($key => $array);
            }
            $this->directives =  $arr;
        }
        catch (Exception $e) {
            $this->directives = [];
        }
    }

    /**
     * Runs the command function on MongoDB
     */
    private function getBuildInfo()
    {
        $this->buildInfo = array();
        try {
            $cursor = $this->database->command(array("buildinfo" => 1));
            foreach ($cursor as $document) {
                $this->version = $document['version'];
                if (isset($document['ok'])) {
                    unset($document["ok"]);
                    $this->buildInfo = $document;
                }
            }

        } catch (Exception $e) {
            //
        }
    }

    /**
     * Runs the command function on MongoDB
     * We can only get the PORT using this
     */
    private function getConnection()
    {
        try {
            $port   = '';
            $host   = 'localhost';
            $cursor = $this->database->command(array("getCmdLineOpts" => 1));
            foreach ($cursor as $document) {
                if (isset($document['parsed']['net']['port'])) {
                    $port = $document['parsed']['net']['port'];
                }
            }
            $this->connection = array(
                "Host"      => $host,
                "Port"      => $port,
                "Username"  => "******",
                "Password"  => "******"
            );

        } catch (Exception $e) {
            $this->connection = array(
                "Host"      => 'localhost',
                "Port"      => 27017,
                "Username"  => "******",
                "Password"  => "******"
            );
        }
    }

    /**
     * Fetched the data from the Composer config
     */
    private function getComposerData()
    {
        try {
            $reader                  = new ConfigurationReader;
            $composer                = base_path('composer.json');
            $data                    = $reader->read($composer);
            $obj                     = $data->rawData();
            $composer                = [];
            $composer['name']        = $data->name();
            $composer['description'] = $data->description();
            $composer['keywords']    = join(" , ", $data->keywords());
            $composer['homepage']    = $data->homepage();
            $arr                     = $obj->authors;
            $authors                 = [];
            foreach ($arr as $author) {
                $authors[]      = array (
                    "name"      => $author->name,
                    "email"     => $author->email,
                    "homepage"  => $author->homepage,
                    "role"      => $author->role
                );
            }
            $composer['authors']    = $authors;
            $composer['support']    = array(
                "docs"              => $obj->support->docs,
                "email"             => $obj->support->email,
                "issues"            => $obj->support->issues,
                "source"            => $obj->support->source,
                "authentication"    => $obj->support->authentication,
                "users"             => $obj->support->users,
                "configuration"     => $obj->support->configuration
            );
            $composer['license']    = $data->license();
            $composer['version']    = $this->getVersion();
            $this->composer         = $composer;
        }
        catch (Exception $e) {
            $this->composer = [];
        }
    }

    /**
     * DbsController constructor.
     */
    public function __construct()
    {
        /** @var User $user */
        // Set the default DB
        // ToDo: admin wont work for non ROOT users
        $this->db    = 'admin';
        $this->user  = auth()->guard('api')->user();
        $this->mongo = new Mongo($this->user);
        if ($this->mongo->checkConfig()) {
            $this->database = $this->mongo->connectClientDb($this->db);
        }

        // ToDo: for now just use the config value = these need to be reading the 'current server' once we implement server configs
        // ToDo: Server configs implemented - keeping these here for reference...
        //$uri = "mongodb://" . config('mongo.servers.0.host') . ":" . config('mongo.servers.0.port');
        //$this->manager = new MongoDB\Driver\Manager($uri);
        //$this->database = (new MongoDB\Client)->admin;
    }

    /**
     * Fetches all available Server and diagnostic data
     * Limitations exist for user without 'root' MongoDB access
     *
     * URL:         /api/v1/server
     * Method:      GET
     * Description: Web and DB server data
     *
     * @return Response
     */
    public function getServer(): Response
    {
        // get the server data
        $arr = [];

        // commandline
        $this->getCommandline();

        // web server
        $this->getWebServer();

        // directives
        $this->getDirectives();

        // build info
        $this->getBuildInfo();

        // connection
        $this->getConnection();

        // composer data
        $this->getComposerData();

        $arr['commandline'] = $this->commandLine;
        $arr['connection']  = $this->connection;
        $arr['webserver']   = $this->webServers;
        $arr['directives']  = $this->directives;
        $arr['buildinfo']   = $this->buildInfo;
        $arr['composer']    = $this->composer;
        $arr['errors']      = $this->getErrorMessage();

        return response()->success('success', array('server' => $arr));
    }

    /**
     * Fetches all available Server status data
     * Limitations exist for user without 'root' MongoDB access
     *
     * URL:         /api/v1/server/{database}/status
     * Method:      GET
     * Description: Returns all available server status data
     *
     * @param  Request $request
     * @param  $database
     * @return mixed
     */
    public function getServerStatus(Request $request, $database)
    {
        try {
            if ($database) {
                // get connection manager
                $this->mongo->connectManager();
                /** @var MongoDB\Driver\Manager $manager */
                $manager = $this->mongo->getManager();

                $command = array(
                    "serverStatus" => 1
                );
                $results = $manager->executeCommand(
                    $database,
                    new MongoDb\Driver\Command($command)
                );

                //dd($results->toArray()[0]);

                return response()->success('success', array('status' => $results->toArray()[0]));
            }
            else {
                return response()->error('failed', array('error' => 'database name missing'));
            }
        }
        catch (\Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));

        } catch (MongoDB\Driver\Exception\Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));
        }

    }

    /**
     * Fetches all active Server processes data
     * Limitations exist for users without 'root' MongoDB access
     *
     * URL:         /api/v1/server/processes
     * Method:      GET`
     * Description: Returns information on all active (currently running) server processes
     *
     * @param  Request $request
     * @return mixed
     */
    public function getServerProcesses(Request $request)
    {
        try {
            $database = 'admin';
            // get connection manager
            $this->mongo->connectManager();
            /** @var MongoDB\Driver\Manager $manager */
            $manager = $this->mongo->getManager();

            $command = array(
                "currentOp" => 1
            );
            $results = $manager->executeCommand(
                $database,
                new MongoDb\Driver\Command($command)
            );

            $results = $results->toArray()[0];
            //dd($results->inprog[0]);

            return response()->success('success', array('processes' => array("inprog" => $results->inprog, "ok" => $results->ok)));
        }
        catch (\Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));

        } catch (MongoDB\Driver\Exception\Exception $e) {
            return response()->error('failed', array('error' => $e->getMessage()));
        }
    }
}
