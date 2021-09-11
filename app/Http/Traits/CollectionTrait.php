<?php

/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      CollectionTrait.php 1001 24/8/21, 1:19 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   CollectionTrait.php
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

use App\Helpers\MongoHelper;
use App\Http\Classes\ExportDocument;
use App\Http\Classes\HighlightDocument;
use App\Exceptions\UnableToLoadCollectionException;
use MongoDB;
use Illuminate\Support\Facades\Log;

trait CollectionTrait
{
    /**
     * Returns the collection
     * Todo: !! taking the long road with this request as we want 'ALL' available data
     * @param   MongoDB\Collection $collection Collection object
     * @return  array
     * @throws  UnableToLoadCollectionException
     * @todo consider using $objects = new MongoDB\Client->$db->$collection to fetch objects separately
     */
    private function getOneCollection(MongoDB\Collection $collection): array
    {
        try {
            // retrieve names
            $database   = $collection->getDatabaseName();
            $collectionName = $collection->getCollectionName();

            // get the server data
            $data       = $collection->__debugInfo();
            $serverData = $this->getServerData($data['manager']);

            // get the database
            $db         = $this->mongo->connectClientDb($database);

            /** @var MongoDB\Driver\Cursor $stats Fetch the collection statistics*/
            $stats      = $db->command(array("collStats" => $collection->getCollectionName()));

            /** @var MongoDB\Model\BSONDocument $bsonObj */
            $bsonObj    = $stats->toArray()[0];
            $array      = $bsonObj->getArrayCopy();

            /**
             * Extract BSON doc down to 3 levels
             */
            $statistics = [];
            foreach ($array as $key => $value) {
                if ($value instanceof MongoDB\Model\BSONDocument) {
                    $value = $value->getArrayCopy();
                }
                if (is_array($value)) {
                    $arr = [];
                    foreach ($value as $k => $v) {
                        if ($v instanceof MongoDB\Model\BSONDocument) {
                            $v = $v->getArrayCopy();
                        }
                        if (is_array($v)) {
                            $a = [];
                            foreach ($v as $index => $val) {
                                if ($val instanceof MongoDB\Model\BSONDocument) {
                                    $val = $val->getArrayCopy();
                                }
                                $a[$index] = $val;
                            }
                            $arr[$k] = $a;
                        } else {
                            $arr[$k] = $v;
                        }
                    }
                    $value = $arr;
                }
                $statistics[ $key ] = $value;
            }

            // save the stats for reference
            $this->setCollectionStatistics($statistics);

            /**
             * Some of these action need have restrictions place on dbAdmin baseed roles
             */
            $isDbAdmin = false;
            if (
                $this->mongo->hasAdminRoleOnDatabase($collection->getDatabaseName()) &&
                !$this->mongo->hasReadRoleOnDatabase($collection->getDatabaseName())
            ) {
                $isDbAdmin = true;
            }

            if ($isDbAdmin === true) {
                // this will be the default data block for dbAdmin~ roles
                $objectsObj = array(
                    'objects' => [],
                    'count' => 0
                );
                if (in_array($collectionName, $this->dbAdminCollections)) {
                    $objectsObj = MongoHelper::getObjects($this->client, $database, $collection->getCollectionName());
                }
            }

            if ($isDbAdmin === false) {
                $objectsObj = MongoHelper::getObjects($this->client, $database, $collection->getCollectionName());
            }

            /**
             * Extract the BSON docs from $objects
             */
            $objects    = [];
            $objectsArr = $objectsObj['objects'];
            $arr        = [];
            $objects['count'] = $objectsObj['count'];

            $documentsArray = []; // collect this for the 'prepareObjects' method
            foreach ($objectsArr as $document) {
                MongoHelper::prepareDocument($document, $documentsArray, $this->fields);
            }

            // dbAdmin~ roles
            if ($isDbAdmin === true) {
                // need to handle this differently
                $this->prepareObjectsDbAdmin($objectsArr, $documentsArray, $arr);
            }

            // all other roles
            if ($isDbAdmin === false) {
                $this->prepareObjects($objectsArr, $documentsArray, $arr);
            }

            // apply the referenced data
            $objects['objects'] = $arr;
            return array(
                "collection" => $collection->__debugInfo(), "objects" => $objects, "stats" => $statistics, "server" => $serverData
            );

        } catch (\Throwable $t) {
            Log::debug('getOneCollection exception: ' . $t->getMessage());
            throw new UnableToLoadCollectionException($t->getMessage());
        }
    }

    /**
     * @param array $objectsArr
     * @param array $documentsArray
     * @param $arr
     */
    private function prepareObjects(array $objectsArr, array $documentsArray, &$arr): void
    {
        /** @var ExportDocument $docExport */
        $docExport    = new ExportDocument();

        /** @var HighlightDocument $docHighlight */
        $docHighlight = new HighlightDocument();

        $i = 0;
        foreach ($objectsArr as $key => $obj) {
            // set the text key value -> default as php
            $docExport->setVar($obj);
            $docExport->setParams([]);

            /** @var MongoDB\BSON\ObjectId $id */
            $id         = $obj['_id'];
            if (is_object($id)) {
                $obj['_id'] = $id->__toString();
            }

            // we need a raw version - easier to updated and manipulates with JS
            $raw  = MongoHelper::extractDocument($obj);

            // always set 'json' as the default for this
            $text = $docExport->export($this->format);

            // set the data key
            $data = $docHighlight->highlight($documentsArray[$i], $this->format, true);

            $obj['raw']  = $raw;
            $obj['text'] = $text;
            $obj['data'] = $data;

            // set the 'can_delete' bool
            $obj['can_delete']    = (isset($obj['_id']) && false == $this->findCollectionStatisticsValue('capped'));

            // set the 'can_modify' bool
            $obj['can_modify']    = (isset($obj['_id']));

            // set the 'can_duplicate' bool
            $obj['can_duplicate'] = (isset($obj['_id']));

            // set the 'can_add_field' bool
            $obj['can_add_field'] = (isset($obj['_id']) && false == $this->findCollectionStatisticsValue('capped'));

            // set the 'can_refresh' bool
            $obj['can_refresh']   = (isset($obj['_id']));

            $arr[$key] = $obj;

            $i++;
        }
    }

    /**
     * @param array $objectsArr
     * @param array $documentsArray
     * @param $arr
     */
    private function prepareObjectsDbAdmin(array $objectsArr, array $documentsArray, &$arr): void
    {
        /** @var ExportDocument $docExport */
        $docExport    = new ExportDocument();

        /** @var HighlightDocument $docHighlight */
        $docHighlight = new HighlightDocument();

        $i = 0;
        foreach ($objectsArr as $key => $obj) {
            // set the text key value -> default as php
            $docExport->setVar($obj);
            $docExport->setParams([]);
;
            // we need a raw version - easier to updated and manipulates with JS
            $raw  = MongoHelper::extractDocument($obj);

            // always set 'json' as the default for this
            $text = $docExport->export($this->format);

            // set the data key
            $data = $docHighlight->highlight($documentsArray[$i], $this->format, true);

            $obj['raw']  = $raw;
            $obj['text'] = $text;
            $obj['data'] = $data;

            // set the 'can_delete' bool
            $obj['can_delete']    = (isset($obj['_id']) && false == $this->findCollectionStatisticsValue('capped'));

            // set the 'can_modify' bool
            $obj['can_modify']    = (isset($obj['_id']));

            // set the 'can_duplicate' bool
            $obj['can_duplicate'] = (isset($obj['_id']));

            // set the 'can_add_field' bool
            $obj['can_add_field'] = (isset($obj['_id']) && false == $this->findCollectionStatisticsValue('capped'));

            // set the 'can_refresh' bool
            $obj['can_refresh']   = (isset($obj['_id']));

            $arr[$key] = $obj;

            $i++;
        }
    }

    /**
     * Used to confirm that a collection has been dropped
     *
     * @param   string $name
     * @param   array  $result
     * @param   bool $ns
     * @return  array
     */
    private function setDeleteStatus(string $name, array $result, bool $ns): array
    {
        if (1 == $result['ok']) {
            if (@$result['dropped'] == $name || @$result['ns'] == $ns) {
                return array($name => 'success');
            }
        }
        return array($name => 'failed');
    }

    /**
     * Returns the objects for the given collection
     *
     * @param   string $db             string DB Name
     * @param   string $collection     string Collection name
     * @return  array
     */
    private function getObjects(string $db, string $collection): array
    {
        // no errors this way
        $arr = array(
            "objects" => [],
            "count" => 0
        );

        $cursor    = $this->client->$db->selectCollection($collection);
        $objects   = $cursor->find();
        $array     = $objects->toArray();

        foreach ($array as $object) {
            $arr['objects'][] = $object;
        }
        $arr['count'] = count($arr['objects']);
        return $arr;
    }

    /**
     * @param MongoDB\Driver\Manager $manager
     * @return array
     */
    private function getServerData(MongoDB\Driver\Manager $manager): array
    {
        /** @var MongoDB\Driver\Server $server */
        $server     = $manager->getServers();
        if (empty($server[0])) {
            return [];
        }
        $serverData['host'] = $server[0]->getHost();
        $serverData['port'] = $server[0]->getPort();
        $serverData['info'] = $server[0]->getInfo();

        return $serverData;
    }
}
