<?php

/*
    Defines a namespace for the controller.
*/
namespace App\Http\Controllers\Api;

/*
    Defines the requests used by the controller.
*/
use Illuminate\Http\Request;

/*
    Defined controllers used by the controller
*/
use App\Http\Controllers\Controller;

use App\Models\Collection;

use MongoDB;

use MongoDB\BSON\Unserializable;

use App\Http\Classes\UnserialiseDocument;


class CollectionController extends Controller implements Unserializable
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
     * @var MongoDB\Client
     */
    private $client;

    /**
     * @var $unserialised MongoDB\Model\BSONArray
     */
    private $unserialised;

    /**
     * @var $database
     */
    private $database;

    /**
     * @var $collection
     */
    private $collection;

    /**
     * Returns the collection
     *
     * @param   string    $database     string DB Name
     * @param   string    $collection   string Collection Name
     * @return  array
     */
    private function getOneCollection($database, $collection)
    {
        /** @var MongoDB\Collection $collection */
        $collection = (new MongoDB\Client)->$database->$collection;
        $objectsObj = $this->getObjects($database, $collection->getCollectionName());
        $data       = $collection->__debugInfo();

        /** @var  MongoDB\Driver\Manager $manager */
        $manager    = $data['manager'];

        /** @var MongoDB\Driver\Server $server */
        $server    = $manager->getServers()[0];
        $serverData['host'] = $server->getHost();
        $serverData['port'] = $server->getPort();
        $serverData['info'] = $server->getInfo();

        // get the database
        $db         = (new MongoDB\Client)->$database;

        /** @var MongoDB\Driver\Cursor $stats Fetch the collection statistics*/
        $stats      = $db->command( array("collStats" => $collection->getCollectionName()) );

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

        /**
         * Extract the BSON docs from $objects
         */
        $objects = [];
        $objects['count'] = $objectsObj['count'];
        $objectsArr = $objectsObj['objects'];
        $arr = [];
        foreach ($objectsArr as $key => $obj) {
            if ($obj instanceof MongoDB\Model\BSONDocument) {
                $obj = $obj->getArrayCopy();
                // ToDo !! convert the _id into aa array
                /** @var MongoDB\BSON\ObjectId $id */
                $id         = $obj['_id'];
                $oid        = $id->serialize();
                $obj['_id'] =  array('oid' => $id);
            }
            $arr[$key] = $obj;
        }
        $objects['objects'] = $arr;
        $arr = array("collection" => $collection->__debugInfo(), "objects" => $objects, "stats" => $statistics, "server" => $serverData);

        return $arr;
    }

    /**
     * Returns the objects for the given collection
     *
     * @param   string  $db             string DB Name
     * @param   string  $collection     string Collection name
     * @return  array
     */
    private function getObjects($db, $collection)
    {
        $arr     = [];
        $cursor  = (new MongoDB\Client)->$db->selectCollection($collection);
        $objects = $cursor->find();
        $arr['objects'] = $objects->toArray();
        $arr['count']   = count($arr['objects']);
        return $arr;
    }

    /**
     * Used to confirm that a collection has been dropped
     *
     * @param string $name
     * @param array $result
     * @return array
     */
    private function setDeleteStatus(string $name, array $result)
    {
        if ($result['dropped'] == $name && $result['ok'] == 1) {
            return array($name => 'success');
        }
        return array($name => 'failed');
    }

    /**
     * DatabasesController constructor.
     */
    public function __construct()
    {
        $this->client = new MongoDB\Client;
    }

    /**
     * Display a single collection.
     *
     * URL:         /api/v1/collection/{database}/{$collection}
     * Method:      GET
     * Description: Fetches a collection with objects and stats
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCollection(Request $request, $database, $collection)
    {
        //$database = $request->get('database');
        $this->database   = $database;
        $this->collection = $collection;
        if (isset($database, $collection)) {
            // get the collection
            $collection = $this->getOneCollection($database, $collection);
            return response()->success('success', array('collection' => $collection));
        }
        return response()->error('failed', array('message' => 'required parameters missing'));
    }

    /**
     * Creating a new MongoDB collection
     *
     * URL:         /api/v1/collection/create
     * Method:      POST
     * Description: Create a new collection using the given name
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCollection(Request $request)
    {
        $database   = $request->get('database');
        $collection = $request->get('collection');

        // create the collection
        $database = (new MongoDB\Client)->$database;
        $database->createCollection($collection);

        $arr = $this->$this->getOneCollection($database, $collection);
        return response()->success('success', array('database' => $arr ));
    }

    /**
     * Deleting a MongoDB collection
     *
     * URL:         /api/v1/collection/delete
     * Method:      POST
     * Description: Delete the database matching the given name
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCollection(Request $request)
    {
        $database    = $request->get('database');
        $collections = $request->get('collection', false);
        $status = array();
        if ($collections && is_array($collections)) {
            foreach ($collections as $name) {
                if (!empty($name)) {
                    $collection = (new MongoDB\Client)->$database->$name;
                    /** @var MongoDB\Model\BSONDocument $result */
                    $result = $collection->drop();
                    $status[] = $this->setDeleteStatus( $name, $result->getArrayCopy());
                }
            }
        }
        return response()->success('success', array('status' => $status ));
    }

    /**
     *
     * @inheritDoc
     */
    public function bsonUnserialize(array $data)
    {
        // TODO: Implement bsonUnserialize() method.
        $this->unserialised = $data;
    }
}
