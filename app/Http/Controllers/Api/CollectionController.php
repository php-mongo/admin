<?php

/*
 *  Defines a namespace for the controller.
 */
namespace App\Http\Controllers\Api;

/**
 *   Defines the requests used by the controller.
 */
use Illuminate\Http\Request;

/**
 *  Defined application classes
 */
use App\Http\Controllers\Controller;
use App\Http\Classes\ExportDocument;
use App\Http\Classes\HighlightDocument;
use App\Http\Classes\MongoConnection as Mongo;
use App\Helpers\MongoHelper;

/**
 * Vendors
 */
use MongoDB\BSON\Unserializable;
use MongoDB;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Class CollectionController
 * @package App\Http\Controllers\Api
 */
class CollectionController extends Controller implements Unserializable
{
    /**
     * @var     int         $limit      how many documents should be fetched for each page
     */
    private $limit = 30;

    /**
     * ToDo: Set a default here for now - we'll be passing this a config -> or as a request parameter
     *
     * @var     string      $format
     */
    private $format = 'json';

    /**
     * ToDo: Set a default here for now - we'll be passing this a config -> or as a request parameter
     *
     * @var     string      $render
     */
    private $render = 'default';

    /**
     * @var     array       $fields
     */
    private $fields = [];

    /**
     * @var Mongo $mongo
     */
    private $mongo;

    /**
     * @var     MongoDB\Client  $client
     */
    private $client;

    /**
     * @var     MongoDB\Model\BSONArray $unserialised
     */
    private $unserialised;

    /**
     * @var     string      $database       mongo database name
     */
    private $database;

    /**
     * @var     string      $collection     mongo collection name
     */
    private $collection;

    /**
     * @var     array       $collectionStatistics
     */
    private $collectionStatistics;

    /**
     * @param   string      $search
     *
     * @return  mixed
     */
    private function findCollectionStatisticsValue( $search )
    {
        foreach ($this->collectionStatistics as $key => $value) {
            if ($key == $search && !is_array( $value)) {
                return $value;
            }
        }
        return '';
    }

    /**
     * @return array
     */
    private function getCollectionStatistics( ): array
    {
        return $this->collectionStatistics;
    }

    /**
     * @param array $collectionStatistics
     */
    private function setCollectionStatistics(array $collectionStatistics): void
    {
        $this->collectionStatistics = $collectionStatistics;
    }

    /**
     * Returns the collection
     * Todo: !! taking the long road with this request as we want 'ALL' available data
     * @todo consider using $objects = new MongoDB\Client->$db->$collection to fetch objects seperately
     *
     * @param   string    $database     string DB Name
     * @param   string    $collection   string Collection Name
     * @return  array
     */
    private function getOneCollection($database, $collection)
    {
        /** @var MongoDB\Collection $collection */
        $collection = $this->mongo->connectClientCollection( $database, $collection );

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
        $db         = $this->mongo->connectClientDb( $database );

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

        // save the stats for reference
        $this->setCollectionStatistics( $statistics );

        /**
         * Extract the BSON docs from $objects
         */
        $objects    = [];
        $objectsArr = $objectsObj['objects'];
        $arr        = [];

        /** @var ExportDocument $docExport */
        $docExport  = new ExportDocument();

        /** @var HighlightDocument $docHighlight */
        $docHighlight = new HighlightDocument();
        $objects['count'] = $objectsObj['count'];

        $documentsArray = [];
        foreach ($objectsArr as $document) {
            MongoHelper::prepareDocument( $document, $documentsArray,$this->fields );
        }
        $i = 0;
        foreach ($objectsArr as $key => $obj) {
            // set the text key value -> default as php
            $docExport->setVar($obj);
            $docExport->setParams([]);
            // always set 'array' as the default for this
            $text = $docExport->export($this->format);

            // set the data key
            $data = $docHighlight->highlight( $documentsArray[$i], $this->format, true);

            $obj['text'] = $text;
            $obj['data'] = $data;

            // set the 'can_delete' bool
            $obj['can_delete'] = (isset($obj['_id']) && $this->findCollectionStatisticsValue('capped') == false);

            // set the 'can_modify' bool
            $obj['can_modify'] = (isset($obj['_id']));

            // set the 'can_duplicate' bool
            $obj['can_duplicate'] = (isset($obj['_id']));

            // set the 'can_add_field' bool
            $obj['can_add_field'] = (isset($obj['_id']) && $this->findCollectionStatisticsValue('capped') == false);

            // set the 'can_refresh' bool
            $obj['can_refresh'] = (isset($obj['_id']));

            $arr[$key] = $obj;

            $i++;

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
        // no errors this way
        $arr = array(
            "objects" => [],
            "count" => 0
        );
        $cursor    = $this->client->$db->selectCollection($collection);
        $objects   = $cursor->find();
        $array     =  $objects->toArray();
        foreach ($array as $object) {
            $arr['objects'][] = $object;
        }
        $arr['count'] = count($arr['objects']);
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
        /** @var \App\Models\User $user */
        $user = auth()->guard('api')->user();
        $this->mongo = new Mongo($user);
        if ($this->mongo->checkConfig()) {
            $this->mongo->connectClient();
            $this->client = $this->mongo->getClient();
        }
    }

    /**
     * Display a single collection.
     *
     * URL:         /api/v1/collection/{database}/{collection}
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
        $database = $this->mongo->connectClientDb($database);
        $database->createCollection($collection);
        $arr      = $this->$this->getOneCollection($database, $collection);

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
        $status      = array();
        if ($collections && is_array($collections)) {
            foreach ($collections as $name) {
                if (!empty($name)) {
                    $collection = $this->mongo->connectClientCollection($database, $name);
                    /** @var MongoDB\Model\BSONDocument $result */
                    $result   = $collection->drop();
                    $status[] = $this->setDeleteStatus( $name, $result->getArrayCopy());
                }
            }
        }
        return response()->success('success', array('status' => $status ));
    }

    /**
     * Mostly we dont need this as we want to strip out all the bits individually
     * In most cases ->getArrayCopy() or ->__toString() suffice
     *
     * @inheritDoc
     */
    public function bsonUnserialize(array $data)
    {
        // TODO: Implement bsonUnserialize() method.
        $this->unserialised = $data;
    }
}
