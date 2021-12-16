<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      MongoConnection.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   MongoConnection.php
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
 * It's not 'MySpace' - maybe its YourSpace?
 */
namespace App\Http\Classes;

/**
 * We need User model to fetch the Server configurations (if any)
 * @uses
 */

use App\Exceptions\NoServerConfigurationException;
use App\Exceptions\UnableToConnectMongoDbException;
use App\Http\Traits\PrivilegesTrait;
use App\Models\User;
use App\Models\Server;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use MongoDB;

/**
 * Class MongoConnection
 * @package App\Http\Classes
 */
class MongoConnection
{
    use PrivilegesTrait;

    /**
     * Built in read capable roles
     *
     * @var string[]
     */
    private $readRoles = [
        'root',
        'dbOwner',
        'read',
        'readWrite',
        'readAnyDatabase',
        'readWriteAnyDatabase',
    ];

    /**
     * Built in write capable roles
     *
     * @var string[]
     */
    private $writeRoles = [
        'root',
        'dbOwner',
        'readWrite',
        'readWriteAnyDatabase'
    ];

    /**
     * Built in user admin capable roles
     *
     * @var string[]
     */
    private $userAdminRoles = [
        'root',
        'dbOwner',
        'userAdmin',
        'userAdminAnyDatabase'
    ];

    /**
     * Built in db admin capable roles
     *
     * @var string[]
     */
    private $dbAdminRoles = [
        'dbAdmin',
        'dbAdminAnyDatabase'
    ];

    /**
     * Roles with find capability
     *
     * @var string[]
     */
    private $findPrivilegeOnCollection = [
        array('backup' => '*'),
        array('dbOwner' => '*'),
        array('dbAdmin' => 'system.profile'),
        array('read' => '*'),
        array('readWrite' => '*'),
        array('readAnyDatabase' => '*'),
        array('readWriteAnyDatabase' => '*'),
        array('dbAdminAnyDatabase' => 'system.profile'),
    ];

    /** @var MongoDB\Client */
    private $client;

    /** @var MongoDB\Driver\Manager */
    private $manager;

    /** @var array */
    private $server;

    /** @var string */
    private $userName;

    /**
     * Force verification
     * In case this application connects to a new or insecure mongodb server
     * @var bool
     */
    private $verifyAnonymousConnection;

    /**
     * Track verification result
     * In case this application connects to a new or insecure mongodb server
     * @var bool
     */
    private $isAnonymous = false;

    /** @var string $userDb This defines the scope of the current users privileges */
    private $userDb;

    /** @var array */
    private $userRoles;

    /** @var bool */
    private $mongoCloud;

    /**
     * @return MongoDB\Client
     */
    public function getClient(): MongoDB\Client
    {
        return $this->client;
    }

    /**
     * @param MongoDB\Client $client
     */
    public function setClient(MongoDB\Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return MongoDB\Driver\Manager
     */
    public function getManager(): MongoDB\Driver\Manager
    {
        return $this->manager;
    }

    /**
     * @param MongoDB\Driver\Manager $manager
     */
    public function setManager(MongoDB\Driver\Manager $manager): void
    {
        $this->manager = $manager;
    }

    /**
     * @return array
     */
    public function getServer(): ?array
    {
        return $this->server;
    }

    /**
     * This array is derived either from the Server attributes or manually created
     *
     * @param array $server
     */
    public function setServer(array $server): void
    {
        $this->server = $server;
    }

    public function getServerId()
    {
        return $this->server['id'];
    }

    /**
     * @return array
     */
    public function getUserRoles(): array
    {
        return $this->userRoles ?? array('roles' => []);
    }

    /**
     * Sets the user roles array - mostly used for handling permissions
     *
     * @param array $userRoles
     * @return void
     */
    public function setUserRoles(array $userRoles): void
    {
        $this->userRoles = $userRoles;
    }

    /**
     * @return bool
     */
    public function getMongoCloud(): bool
    {
        return $this->mongoCloud;
    }

    /**
     * @param string $mongoCloud
     */
    public function setMongoCloud(string $mongoCloud = "0"): void
    {
        $this->mongoCloud = (bool)$mongoCloud;
    }

    /**
     * Sets the user primary database
     *
     * @param   string $userDb
     * @return  void
     */
    public function setUserDb(string $userDb): void
    {
        $this->userDb = $userDb;
    }

    /**
     * @param   string|null $db
     * @return  string
     */
    public function getUserDb(string $db = null): string
    {
        return $this->userDb ?? $db;
    }

    /**
     * We need to store this for reference
     * Need to track the DB username used by the current login user
     *
     * @return  string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * Checks both isAnonymous settings
     * @return bool
     */
    public function getIsAnonymous(): bool
    {
        return $this->isAnonymous && $this->userRoles['isAnonymous'];
    }

    /**
     * @param string $host
     * @return string
     */
    public function getPrefix(string $host): string
    {
        return (
            false !== strpos($host, 'localhost') ||
            config('app.isDockerApp') === true
        ) ? 'mongodb' : 'mongodb+srv';
    }

    /**
     * Sets up the mongodb connection options
     * Only uses the server details if they exist
     *
     * @return array
     * @throws NoServerConfigurationException
     */
    private function prepareConnection(): array
    {
        $server = $this->getServer();
        $this->setMongoCloud($server['mongo_cloud']);
        if ($server['mongo_cloud'] === "1") {
            // custom URL for connection to MongoDb Cloud
            $tail = '?retryWrites=true&w=majority';
            // add the database if there is one set
            if (!empty($server['mongo_cloud_database'])) {
                $tail = "/" . $server['mongo_cloud_database'] . $tail;
            }
            $uri = 'mongodb+srv://' .
                $server['username'] . ':' .
                urlencode(Crypt::decryptString($server['password'])) . '@' .
                $server['host'] . $tail;
        }
        if ($server['mongo_cloud'] === "0") {
            $prefix = $this->getPrefix($server['host']);
            $uri = $prefix . '://' . $server['host'] . ':' . $server['port'];
        }
        if (empty($uri)) {
            throw new NoServerConfigurationException('No server configuration found');
        }

        $options = [];
        if (!empty($server['username']) && !empty($server['password'])) {
            $options['username'] = $server['username'];
            // decrypts either the stored user or server password
            $options['password'] = Crypt::decryptString($server['password']);
        }
        return array("uri" => $uri, 'options' => $options);
    }

    /**
     * Method sets up the current local application user
     * Stores current user roles for permission checks
     * ToDo: update to also fetch the users privileges and use those for verification
     *
     * @param bool $anonymous
     */
    private function prepareLocalUser(bool $anonymous): void
    {
        try {
            $user = $this->getUserInfo();
            // ToDo: save this for later
            //$rolesInfo = $this->getRolesInfo();

            if ($user->ok == 1 && !$anonymous) {
                $this->setUserDb($user->users[0]->db);
                $roles = $user->users[0]->roles;
                $arr = array(
                    'dbs' => null,
                    'roles' => null,
                    'hasRoot' => false
                );
                foreach ($roles as $role) {
                    $arr['roles'][] = array(
                        'db' => $role->db,
                        'role' => $role->role
                    );
                    $arr['dbs'][] = $role->db;
                    if ($role->role === 'root') {
                        $arr['hasRoot'] = true;
                    }
                }
                $this->setUserRoles($arr);
            }

            if ($anonymous) {
                // for anonymous insecure access assume the worst!!
                $this->setUserRoles(
                    [
                        'isAnonymous' => $anonymous,
                        'hasRoot' => true,
                        'roles' => [
                            [
                                'db' => 'admin',
                                'role' => 'root'
                            ]
                        ]
                    ]
                );
            }

        } catch (NoServerConfigurationException | MongoDB\Driver\Exception\Exception $e) {
            // todo: implement some logging for all of these cases
            Log::debug($e->getMessage());
        }
    }

    /**
     * Fetches the list of databases
     * If the 'admin' database is included good chance we have an anonymous root connection active
     *
     * @return bool
     */
    public function testAnonymousAccess(): bool
    {
        $databases = $this->client->listDatabases();
        foreach ($databases as $database) {
            if ('admin' === $database->getName()) {
                $this->isAnonymous = true;
                break;
            }
        }
        return $this->isAnonymous;
    }

    /**
     * MongoConnection constructor.
     *
     * @param User|null $user
     */
    public function __construct(?User $user)
    {
        if (empty($user->exists)) {
            return false;
        }

        /** @var Server $server */
        $servers = $user->servers()->where('active', 1)->get();

        // This provides the default mechanism for handle MongoDB server access
        if (isset($servers[0])) {
            $server = $servers[0];
            $server->setAttribute('is_current', 1);
            $server->save();
            $server         = $server->getAttributes();
            $this->userName = $server['username'];
        }

        // When there is no Server associated with the user, we assume that the user has been created using 'both', login and database
        // Or, the Control User has been created with a Username & Password that will provide direct access to the local MongoDB database
        if (empty($servers[0])) {
            $server = array(
                'id' => 0,
                'host' => config('app.mongoDbHost'),
                'mongo_cloud' => "0", // fake this so we don't break other checks
                'port' => 27017,
                'username' => $user->getAttribute('user'),
                'password' => $user->getAttribute('encrypted_password')
            );

            // save the username
            $this->userName = $user->getAttribute('user');
        }

        /**
         * This block creates the ability to have a username & password defined in the .env file
         * This activates a single user mode
         */
        if ('demo' == env('APP_ENV')) {
            define('SINGLE_USER_MODE', 1);
            // predefined environment: default is 'demo'
            $server = array(
                'id' => 0,
                'host' => 'localhost',
                'mongo_cloud' => "0",
                'port' => 27017,
                'username' => config('app.dbUser'),
                'password' => Crypt::encryptString(config('app.dbPasswd')), // so we don't break downstream
            );

            $this->userName = config('app.dbUser');
        }

        $this->setServer($server);
        $this->prepareLocalUser(false);
    }

    /**
     * Run a quick check that required server config exist
     *
     * @return bool
     */
    public function checkConfig(): bool
    {
        $config = $this->getServer();
        if ($config['host'] === 'localhost') {
            // we need allow connections to insecure local servers
            // ToDo: it would be nice to create a process to secure a new installation from within the application
            $this->verifyAnonymousConnection = (empty($config['username']) && empty($config['password']));
            return (isset($config['host'], $config['port']));
        }
        return (isset($config['host'], $config['port'], $config['username'], $config['password']));
    }

    /**
     * Create a client connection and save locally
     * @throws NoServerConfigurationException
     */
    public function connectClient(): void
    {
        if (!$this->client instanceof MongoDB\Client) {
            $prep = $this->prepareConnection();
            $this->client = $this->getMongoCloud() ?
                new MongoDB\Client($prep['uri']) :
                new MongoDB\Client($prep['uri'], $prep['options']);
        }

        // if anonymous connection test and notify
        $anonymous = $this->verifyAnonymousConnection && $this->testAnonymousAccess();
        // prepare the local user permissions data

        $this->prepareLocalUser($anonymous);
    }

    /**
     * @return MongoDB\Client
     * @throws UnableToConnectMongoDbException|NoServerConfigurationException
     */
    public function connectAndGetClient(): MongoDB\Client
    {
        if ($this->checkConfig()) {
            $this->connectClient();
            return $this->getClient();
        }
        throw new UnableToConnectMongoDbException('Unable to connect with the MongoDb server');
    }

    /**
     * Create a connection directly to a database
     *
     * @param string $db
     * @return MongoDB\Database
     * @throws NoServerConfigurationException
     */
    public function connectClientDb(string $db): MongoDB\Database
    {
        $prep = $this->prepareConnection();

        return $this->getMongoCloud() ?
            (new MongoDB\Client($prep['uri']))->$db :
            (new MongoDB\Client($prep['uri'], $prep['options']))->$db;
    }

    /**
     * Create a connection to a collection via a database
     *
     * @param string $db
     * @param string $collection
     * @return MongoDB\Collection
     * @throws NoServerConfigurationException
     */
    public function connectClientCollection(string $db, string $collection): MongoDB\Collection
    {
        $prep = $this->prepareConnection();
        return $this->getMongoCloud() ?
            (new MongoDB\Client($prep['uri']))->$db->$collection :
            (new MongoDB\Client($prep['uri'], $prep['options']))->$db->$collection;
    }

    /**
     * Create a manager connection and save locally
     * @throws NoServerConfigurationException
     */
    public function connectManager(): void
    {
        if (!$this->manager instanceof MongoDB\Driver\Manager) {
            $prep = $this->prepareConnection();
            $this->manager = $this->getMongoCloud() ?
                new MongoDB\Driver\Manager($prep['uri']) :
                new MongoDB\Driver\Manager($prep['uri'], $prep['options']);
        }
    }

    /**
     * Runs the MongoDB\Driver\Manager::executeCommand method
     *
     * @param $db
     * @param $command
     *
     * @return MongoDB\Driver\Cursor|string
     *
     * @throws MongoDB\Driver\Exception\Exception
     */
    public function managerCommand($db, $command)
    {
        if ($this->manager) {
            try {
                $command = new MongoDB\Driver\Command($command);
                return $this->manager->executeCommand($db, $command);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        return '{}';
    }
}
