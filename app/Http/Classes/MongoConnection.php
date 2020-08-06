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
 * its not 'MySpace' - maybe its Your Soace?
 */
namespace App\Http\Classes;

/**
 * We need User model to fethc the Server configurations (if any)
 * @uses
 */
use App\Models\User;
use App\Models\Server;
use MongoDB;

/**
 * Class MongoConnection
 * @package App\Http\Classes
 */
class MongoConnection
{
    /** @var MongoDB\Client */
    private $client;

    /** @var MongoDB\Driver\Manager */
    private $manager;

    /** @var App\Model\Server */
    private $server;

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
     * @return App\Model\Server
     */
    public function getServer(): array
    {
        return $this->server;
    }

    /**
     * @param array $server
     */
    public function setServer(array $server): void
    {
        $this->server = $server;
    }

    private function prepareConnection()
    {
        $server = $this->getServer();
        $prefix = strpos($server['host'], 'localhost') !== false ? 'mongodb' : 'mongodb+srv';
        // create the URI
        $uri = $prefix . '://' . $server['host'] . ':' . $server['port'];
        $options = [];
        if (!empty($server['username']) && !empty($server['password'])) {
            $options['username'] = $server['username'];
            $options['password'] = $server['password'];
        }
        return array("uri" => $uri, 'options' => $options);
    }

    /**
     * MongoConnection constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $server = $user->servers()->where('active', 1)->get()[0];
        $this->setServer( $server->getAttributes() );
    }

    /**
     * Run a quick check that required server congid exist
     *
     * @return bool
     */
    public function checkConfig()
    {
        $config = $this->getServer();
        return (isset($config['host'], $config['port'], $config['username'], $config['password']));
    }

    /**
     * Create a client connection and save locally
     * @param boolean|string $db
     */
    public function connectClient()
    {
        $prep = $this->prepareConnection();
        $this->client = new MongoDB\Client( $prep['uri'], $prep['options']);
    }

    /**
     * Create a connection directly to a database
     *
     * @param string $db
     * @return MongoDB\Database
     */
    public function connectClientDb( string $db )
    {
        $prep = $this->prepareConnection();
        return (new MongoDB\Client( $prep['uri'], $prep['options']))->$db;
    }

    /**
     * Create a conection to a collection via a database
     *
     * @param string $db
     * @param string $collection
     * @return MongoDB\Collection
     */
    public function connectClientCollection( string $db, string $collection )
    {
        $prep = $this->prepareConnection();
        return (new MongoDB\Client( $prep['uri'], $prep['options']))->$db->$collection;
    }
}
