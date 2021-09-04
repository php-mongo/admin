<?php

/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      DatabaseTrait.php 1001 24/8/21, 9:42 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   DatabaseTrait.php
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2021. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
 *  See COPYRIGHT.php for copyright notices and further details.
 */

namespace App\Http\Traits;

use MongoDB;
use Exception;

trait DatabaseTrait
{
    /**
     * We need a global method to monitor which database can be read for stats etc
     * For now its just for the demo website
     * ToDo: !! this can be extended and implemented further later on !!
     *
     * @param string $db
     * @return  bool
     */
    private function handleExclusions(string $db): bool
    {
        $env = env('APP_ENV');
        if ('demo' === $env && !in_array($db, $this->excludedDemo, true)) {
            // basic demo site exclusions
            return true;
        }
        // Still here??
        // Check if user has root role
        if ($this->mongo->hasRootRole()) {
            return true;
        }
        // for now always restrict these for non-root accounts
        if (in_array($db, $this->excludedAll, true)) {
            return false;
        }
        // Check user has role on DB
        if ($this->mongo->hasReadRoleOnDatabase($db)) {
            return true;
        }
        // last them pass
        return true;
    }

    /**
     * @param string $collection
     * @return  bool
     */
    private function handleCollectionExclusions(string $collection): bool
    {
        if ($this->mongo->hasRootRole()) {
            return true;
        }

        if ($this->mongo->hasReadRoleOnDatabase($collection)) {
            // read roles cannot read system.profile
            if (in_array($collection, $this->excludedCollections, true)) {
                return false;
            }
            return true;
        }

        if ($this->mongo->hasFindPrivilegeOnCollection($collection)) {
            return true;
        }

        if ($this->mongo->isDbAdmin(false)) {
            // db admin cannot read from user collections
            return false;
        }

        // Last call - fallback
        if (in_array($collection, $this->excludedCollections, true)) {
            return false;
        }

        // fallback to 'allow'
        return true;
    }

    /**
     * Get one or All databases
     *
     * @return  array|null
     */
    private function getAllDatabases(): ?array
    {
        try {
            $arr = [];
            $index = 0;
            foreach ($this->client->listDatabases() as $db) {
                $dbn = $db->getName();
                // Todo: need to verify which connection method is the best path for future usage - going with 1) for now
                // 1) $this->mongo->connectClientDb($dbn)  =  (new MongoDB\Client())->database
                // 2) $this->client->selectDatabase($dbn)  = (new MongoDB\Client())->selectDatabase('database')
                $database = $this->mongo->connectClientDb($dbn);
                $stats = [];
                if ($this->handleExclusions($dbn)) {
                    // only populate if DB allowed
                    $stats = $database->command(array('dbstats' => 1))->toArray()[0];
                }
                $statistics = [];
                // break out the stats into an array
                foreach ($stats as $key => $value) {
                    $statistics[$key] = $value;
                }
                // this set of collections won't need the objects
                $collections = $this->getCollections($db->getName());
                $arr[] = array("id" => $index, "db" => $db->__debugInfo(), "stats" => $statistics, "collections" => $collections);
                $index++;
            }
            // !! one result fits all
            return $arr;

        } catch (Exception $e) {

            $this->setErrorMessage($e->getMessage());
            return [];
        }
    }

    /**
     * @param string $database
     * @return array
     */
    private function getOneDatabase(string $database): array
    {
        try {
            $database = $this->client->selectDatabase($database);
            $stats = $database->command(array('dbstats' => 1))->toArray()[0];
            $statistics = [];
            // break out the stats into an array
            foreach ($stats as $key => $value) {
                $statistics[$key] = $value;
            }
            // the collections object should contain any relative objects
            $collections = $this->getCollections($database, true);
            return array("db" => $database->__debugInfo(), "stats" => $statistics, "collections" => $collections);

        } catch (Exception $e) {
            $this->setErrorMessage($e->getMessage());
            return [];
        }
    }

    /**
     * Returns the collections belonging to each given database
     *
     * @param string $db string DB Name
     * @param bool $getObjects
     * @return  array
     */
    private function getCollections(string $db, bool $getObjects = false): ?array
    {
        try {
            if (!$this->handleExclusions($db)) {
                // we get errors when users without correct permissions try to read collections on restricted dbs
                return [];
            }
            $arr = [];
            $index = 0;
            $database = $this->client->selectDatabase($db);
            /** @var MongoDB\Model\CollectionInfo $collection */
            foreach ($database->listCollections() as $collection) {
                // we only need to get full objects when its database view
                if ($getObjects) {
                    $arr[] = array(
                        "id" => $index,
                        "collection" => $collection->__debugInfo(),
                        "objects" => $this->getObjects($db, $collection->getName())
                    );
                } else {
                    // for the DB's load we still would like the object count :)
                    $arr[] = array(
                        "id" => $index,
                        "collection" => $collection->__debugInfo(),
                        "objects" => $this->getObjectsCount($db, $collection->getName())
                    );
                }
                $index++;
            }
            return $arr;

        } catch (Exception $e) {
            $this->setErrorMessage($e->getMessage());
            return [];
        }
    }

    /**
     * Returns the objects for the given collection
     *
     * @param string $db string DB Name
     * @param string $collection string Collection name
     * @return  array
     */
    private function getObjects(string $db, string $collection): ?array
    {
        try {
            $arr = array('objects' => [], 'count' => 0);
            $cursor = $this->mongo->connectClientDb($db)->selectCollection($collection);
            if ($this->handleCollectionExclusions($collection)) {
                $objects = $cursor->find();
                $arr['objects'] = $objects->toArray();
                $arr['count'] = count($arr['objects']);
            }

            return $arr;

        } catch (Exception $e) {
            $this->setErrorMessage($e->getMessage());
            return [];
        }
    }

    /**
     * Returns the objects for the given collection
     *
     * @param string $db string DB Name
     * @param string $collection string Collection name
     * @return  array
     */
    private function getObjectsCount(string $db, string $collection): ?array
    {
        try {
            $arr = array('objects' => [], 'count' => 0);
            $cursor = $this->mongo->connectClientDb($db)->selectCollection($collection);
            if ($this->handleCollectionExclusions($collection) && $this->handleExclusions($db)) {
                $objects = $cursor->find();
                $arr['count'] = count($objects->toArray());
            }
            return $arr;

        } catch (Exception $e) {
            $this->setErrorMessage($e->getMessage());
            return [];
        }
    }

    /**
     * Used to confirm that a database has been dropped
     *
     * @param string $name
     * @param array $result
     * @return array
     */
    private function setDeleteStatus(string $name, array $result): array
    {
        if (1 === $result['ok'] && $name === $result['dropped']) {
            return array($name => 'success');
        }
        return array($name => 'failed');
    }
}
