<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      MongoConnectionHelper.php 1001 28/8/21, 8:53 pm  Gilbert Rehling $
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
use App\Http\Classes\MongoConnection;
use Illuminate\Support\Facades\Crypt;
use MongoDB;

/**
 * Always be prepared to accept failure !!
 */
use Exception;

/**
 * Class MongoConnectionHelper
 * @package App\Helpers
 */
class MongoConnectionHelper
{
    /**
     * @param int $port
     * @param string $host
     * @param string $username
     * @param string $password
     * @return bool
     */
    public static function checkConnectionConfig(int $port, string $host, string $username, string $password): bool
    {
        $prefix = (new MongoConnection(null))->getPrefix($host);
        // create the URI
        $uri = $prefix . '://' . $host . ':' . $port;
        $options = array(
            'username' => $username,
            'password' => Crypt::decryptString($password)
        );

        /** @var MongoDB\Client $client */
        $client = new MongoDB\Client($uri, $options);

        try {
            // return a boolean
            /** @var MongoDB\Model\DatabaseInfoLegacyIterator $databases */
            $databases = $client->listDatabases();

            return is_string($databases->current()->getName());
        } catch (Exception $e) {
            return false;
        }
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
            $uri = $prefix . '://' .
                $params['username'] . ':' .
                $params['password'] . '@' .
                $params['host'] . '/' .
                $params['remoteDatabase'] . '?retryWrites=true&w=majority';

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
            return new MongoDB\Driver\Manager($uri, $options);
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
    public static function remoteBulkWrite($conn, $documents, $ns, &$inserted): void
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
                $insert['_id'] = new MongoDB\BSON\ObjectId($insert['_id']['oid']);

            } elseif (is_array($insert['_id']) && isset($insert['_id']['$oid'])) {
                // this fits the Compass JSON export
                $insert['_id'] = new MongoDB\BSON\ObjectId($insert['_id']['$oid']);
            }
            // expects an array
            $bulk->insert($insert);
        }
        $conn->executeBulkWrite($ns, $bulk);
    }
}
