<?php

/**
 *   Defines a namespace for the controller.
 */
namespace App\Http\Controllers\Api;

/**
 *   Defines the requests used by the controller.
 */
use Illuminate\Http\Request;

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
 * Used ti reard the compooser file
 */
use Eloquent\Composer\Configuration\ConfigurationReader;

/**
 * All good things coie to an end !!
 */
use Exception;

/**
 * Class ServerController
 * @package App\Http\Controllers\Api
 */
class ServerController extends Controller
{
    /**
     * @var null|string $slug
     */
    private $slug = null;

    /**
     * @var int
     */
    private $limit = 30;

    /**
     * @var MongoConnection
     */
    private $mongo;

    /**
     * @var MongoDB\Driver\Manager
     */
    private $manager;

    /**
     * @var $database MongoDB\Database (new MongoDB\Client)->admin;
     */
    private $database;

    /**
     * @var $commandLine string
     */
    private $commandLine;

    /**
     * @var $webServers array
     */
    private $webServers;

    /**
     * @var $version string
     */
    private $version;

    /**
     * @var $directives array
     */
    private $directives;

    /**
     * @var $buildInfo array
     */
    private $buildInfo;

    /**
     * @var $connection array
     */
    private $connection;

    /**
     * @var $composer array
     */
    private $composer;

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
            $this->commandLine = "";
        }
    }

    /**
     *
     */
    private function getWebServer()
    {
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

    /**
     *
     */
    private function getDirectives()
    {
        $directives = ini_get_all("mongodb");
        $arr = [];
        $index = 0;
        /**
         * we ned to grab the 'alpha keys' and re-index the array with a numeric index
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
                "Host"      =>  $host,
                "Port"      => $port,
                "Username"  => "******",
                "Password"  => "******"
            );

        } catch (Exception $e) {
            $this->connection = [];
        }
    }

    /**
     * Fetched the data from the Composer config
     */
    private function getComposerData()
    {
        $reader                  = new ConfigurationReader;
        $data                    = $reader->read('/var/hosting/sites/php-mongo-admin/composer.json');
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
        $composer['version']    = $data->version();
        $this->composer         = $composer;
    }

    /**
     * DbsController constructor.
     */
    public function __construct()
    {
        /** @var \App\Models\User $user */
        $user        = auth()->guard('api')->user();
        $this->mongo = new Mongo($user);
        if ($this->mongo->checkConfig()) {
            $this->database = $this->mongo->connectClientDb('admin');
        }

        // ToDo: for now just use the config value = these need to be reading the 'current server' once we implement server configs
        //$uri = "mongodb://" . config('mongo.servers.0.host') . ":" . config('mongo.servers.0.port');
        //$this->manager = new MongoDB\Driver\Manager($uri);
        //$this->database = (new MongoDB\Client)->admin;
    }

    /**
     * Display a listing of all dbs.
     *
     * URL:         /api/v1/dbs
     * Method:      GET
     * Description: Fetches all dbs in alphanumeric order
     *
     * @param Request $request
     * @param null $id
     * @param null $slug
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServer()
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

        return response()->success('success',  array('server' => $arr));
    }
}
