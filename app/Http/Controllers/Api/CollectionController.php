<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      CollectionController.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   CollectionController.php
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

/*
 *  Defines a namespace for the controller.
 */
namespace App\Http\Controllers\Api;

/**
 *   Defines the requests used by the controller.
 */

use App\Http\Classes\VarExport;
use Illuminate\Http\Request;

/**
 *  Defined application classes
 */
use App\Http\Controllers\Controller;
use App\Http\Classes\ExportDocument;
use App\Http\Classes\HighlightDocument;
use App\Http\Classes\MongoConnection as Mongo;
use App\Helpers\MongoHelper;
use App\Http\Requests\EditCollectionRequest;
use App\Http\Classes\QueryLogs;

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
     * This has no affect during the QueryCollection method result processing
     *
     * @param   string      $search
     *
     * @return  mixed
     */
    private function findCollectionStatisticsValue( $search )
    {
        if (is_array($this->collectionStatistics)) {
            foreach ($this->collectionStatistics as $key => $value) {
                if ($key == $search && !is_array( $value)) {
                    return $value;
                }
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

    private function prepareObjects( $objectsArr, $documentsArray, &$arr)
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
            $data = $docHighlight->highlight( $documentsArray[$i], $this->format, true);

            $obj['raw']  = $raw;
            $obj['text'] = $text;
            $obj['data'] = $data;

            // set the 'can_delete' bool
            $obj['can_delete']    = (isset($obj['_id']) && $this->findCollectionStatisticsValue('capped') == false);

            // set the 'can_modify' bool
            $obj['can_modify']    = (isset($obj['_id']));

            // set the 'can_duplicate' bool
            $obj['can_duplicate'] = (isset($obj['_id']));

            // set the 'can_add_field' bool
            $obj['can_add_field'] = (isset($obj['_id']) && $this->findCollectionStatisticsValue('capped') == false);

            // set the 'can_refresh' bool
            $obj['can_refresh']   = (isset($obj['_id']));

            $arr[$key] = $obj;

            $i++;
        }
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
        $objects['count'] = $objectsObj['count'];

        $documentsArray = [];
        foreach ($objectsArr as $document) {
            MongoHelper::prepareDocument( $document, $documentsArray,$this->fields );
        }

        $this->prepareObjects($objectsArr, $documentsArray, $arr);

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
        $array     = $objects->toArray();

        foreach ($array as $object) {
            $arr['objects'][] = $object;
        }
        $arr['count'] = count($arr['objects']);
        return $arr;
    }

    /**
     * Used to confirm that a collection has been dropped
     *
     * @param  string $name
     * @param  array  $result
     * @param  bool   $ns
     * @return array
     */
    private function setDeleteStatus(string $name, array $result, $ns = false)
    {
        if ($result['ok'] == 1) {
            if (@$result['dropped'] == $name ||  @$result['ns'] == $ns) {
                return array($name => 'success');
            }
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
        try {
            $this->database   = $database;
            $this->collection = $collection;
            if (isset($database, $collection)) {
                // get the collection
                $collection = $this->getOneCollection($database, $collection);

                return response()->success('success', array('collection' => $collection));
            }
            return response()->error('failed', array('message' => 'required parameters missing'));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    public function queryCollection(Request $request)
    {
        try {
            $this->database   = $request->get('database');
            $this->collection = $request->get('collection');
            $params           = $request->get('params');
            $format           = $request->get('format');
            $criteria         = $params['criteria'];
            $query            = $format == 'json' ? json_decode($criteria[ $format ], true) : $criteria[ $format ];
            $collection       = $this->mongo->connectClientCollection($this->database, $this->collection);
            $results          = $collection->find( $query );
            $results          = $results->toArray();
            $documentsArray   = [];
            $documents        = [];
            foreach ($results as $document) {
                MongoHelper::prepareDocument( $document, $documentsArray,$this->fields );
            }
            $this->prepareObjects($results, $documentsArray, $documents);
            $log = new QueryLogs();
            if ($log->isEnabled()) {
                // ToDo ?? do we want to limit Query Logs to successful queries only ??
                $log->logQuery($this->database, $this->collection, $criteria[ $format ]);
            }
            return response()->success('success', array('documents' => $documents));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Creating a new MongoDB collection
     *
     * URL:         /api/v1/collection/create
     * Method:      POST
     * Description: Create a new collection using the given name
     *
     * @param EditCollectionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCollection(EditCollectionRequest $request)
    {
        try {
            $data = $request->validated();
            $name       = $data['name'];
            $database   = $data['database'];
            $collection = $data['name'];
            $capped     = $data['capped'];
            $count      = $data['count'];
            $size       = $data['size'];

            // default options
            $options = [];

            if ($capped == true && $size >= 1) {
                $options['capped'] = true;
                $options['size']   = $size;
                if ($count) {
                    $options['count'] = $count;
                }
            }

            // create the collection
            $database = $this->mongo->connectClientDb( $database );
            $database->createCollection( $collection, $options );
            $coll     = $this->getOneCollection ($database, $collection );

            return response()->success('success', array( 'collection' => $coll ));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
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
        try {
            $database   = $request->get('database');
            $collection = $request->get('collection', false);
            $ns = $database . '.' . $collection;
            if (!is_array($collection)) {
                $collection = array($collection);
            }
            $status     = array();
            if ($collection && is_array($collection)) {
                foreach ($collection as $name) {
                    if (!empty($name)) {
                        $collection = $this->mongo->connectClientCollection($database, $name);
                        /** @var MongoDB\Model\BSONDocument $result */
                        $result = $collection->drop();
                    //    echo '<pre>'; var_dump(is_object($result)); echo '</pre>';
                    //    echo '<pre>'; var_dump($result); echo '</pre>';
                    //    echo '<pre>'; var_dump($ns); echo '</pre>';
                    //    echo '<pre>'; var_dump($name); echo '</pre>'; die;
                        // ToDo: !! getting an odd error from front-end - request is sent twice
                        if ($result->errmsg) {
                            return response()->error('failed', array('message' => $result->errmsg));

                        } else {
                            $status[] = $this->setDeleteStatus( $name, $result->getArrayCopy(), $ns);
                        }
                    }
                }
            }
            return response()->success('success', array('status' => $status ));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Clearing all documents from a MongoDB collection
     *
     * URL:         /api/v1/collection/clear
     * Method:      POST
     * Description: Clear all documents from collection matching the given name
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCollection(Request $request)
    {
        try {
            $database   = $request->get('database');
            $collection = $request->get('collection', false);
            $status     = array();
            if (isset($database, $collection)) {
                /** @var MongoDB\Collection $collection */
                $collection = $this->mongo->connectClientCollection($database, $collection);
                $result = $collection->deleteMany([]);
                $status = array("collection" => $collection, "deleted" => $result->getDeletedCount());
            }
            return response()->success('success', array('status' => $status ));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Export all documents from one or more collections
     *
     * URL:         /api/v1/collection/export
     * Method:      POST
     * Description: Export all documents within collection(s) matching the given database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportCollection(Request $request)
    {
        try {
            $database = $request->get('database');
            $params   = $request->get('params', false);
            if (isset($database, $params)) {
                $collections = $params['collections'];
                /** @var MongoDB\Database $database */
                $database = $this->mongo->connectClientDb($database);

                // value containers
                $contents       = "";
                $countDocuments = 0;

                // handle indexes
                foreach ($collections as $collectionName) {
                    /** @var MongoDB\Collection $collection */
                    $collection  = $database->selectCollection( $collectionName );
                    $information = $collection->listIndexes();
                    // for now : we are not importing the indexes
                    foreach ($information as $info) {
                        $options   = array();
                        $exporter  =  new VarExport( $database, $info['key']);
                        $contents .= "\n/** {$collection} indexes **/\ndb.getCollection(\"" . addslashes($collectionName) . "\").ensureIndex(" . $exporter->export('json') . ");\n";
                    }
                }

                // handle data
                foreach ($collections as $collectionName) {
                    $documents  = $database->selectCollection( $collectionName )->find();
                    $contents .= "\n/** " . $collectionName  . " records **/\n";
                    foreach ($documents as $document) {
                        $countDocuments ++;
                        $exporter = new VarExport($database, $document);
                        $doc = MongoHelper::extractDocument($document);
                        $contents .= "db.getCollection(\"" . addslashes($collectionName) . "\").insert(" . json_encode($exporter->setObjectId($doc)) . ");\n";
                    }
                }

                if ($params['download'] == true) {
                    // set the file name
                    $filePrefix = "mongodb-" . urldecode($database) . "-" . date("Ymd-His");

                    if ($params['gzip'] == true) {
                        header("Content-type: application/x-gzip");
                        header("Content-Disposition: attachment; filename=\"{$filePrefix}.gz\"");
                        echo gzcompress($contents, 9);

                    } else {
                        header("Content-type: application/octet-stream");
                        header("Content-Disposition: attachment; filename=\"{$filePrefix}.js\"");
                        echo $contents;
                    }
                    exit;
                }
                return response()->success('success', array('export' => $contents ));
            }
            return response()->error('failed', array('message' => "missing parameters"));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Import documents from one or more collections
     *
     * URL:         /api/v1/collection/export
     * Method:      POST
     * Description: Import documents from one or ore collection(s) into the given database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importCollection(Request $request)
    {
        try {
            $database       = $request->get('database');
            $collection     = $request->get('collection');
            $type           = $request->get('type', 'admin');
            $gzip           = $request->get('gzip', false);
            $useCollection  = $request->get('useCurrentCollection', false);

            if (isset($database, $type)) {
                // get the file
                foreach ($_FILES AS $f) {
                    $file = $f;
                }

                function ns($db, $coll) {
                    return $db . '.' . $coll;
                }

                // for zipped files
                if ($gzip === true) {
                    $body = gzuncompress( file_get_contents( $file['tmp_name'] ) );

                } else {
                    $body = file_get_contents( $file['tmp_name'] );
                }

                // connect the manager
                $this->mongo->connectManager();
                /** @var MongoDB\Driver\Manager $manager */
                $manager = $this->mongo->getManager();

                // return the inserted count to the front-end
                $inserted = 0;
                // explode the body into an array - we'll use thus for both file formats
                $arr = explode("\n", $body);

                // this is for file exported from PhpMongoAdmin *.js
                if ($type == 'admin') {
                    $insertArray = [];
                    // iterate the import array
                    foreach ($arr as $insert) {
                        // ignore the comments, index insert etc
                        if (strpos( $insert, "insert") !== false) {
                            // we're good to go! get the collection(s)
                            // track using collections as primary array key
                            $insertArray[ MongoHelper::getCollectionNameFromInsert( $insert ) ][] = MongoHelper::getDateFromInsert( $insert );
                        }
                    }

                    // this will handle multiple collection inserts
                    foreach ($insertArray as $coll => $inserts) {
                        $bulk = new MongoDB\Driver\BulkWrite();

                        // if $useCollection is TRUE : all inserts will use the 'current collection' in the namespace
                        $ns   = $useCollection == false ? ns( $database, $coll) : ns($database, $collection);
                        // iterate the insert and add to the $bulk write object
                        foreach ($inserts as $insert) {
                            // expects an array
                            $bulk->insert( json_decode($insert, true) );
                            $inserted++;
                        }
                        $manager->executeBulkWrite( $ns, $bulk );
                    }

                } else {
                    die("mongodb file...");
                }
                return response()->success('success', array('import' => $inserted ));
            }
            return response()->error('failed', array('message' => 'parameters missing'));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * @param $database
     * @param $collection
     *
     * @return mixed
     */
    public function getQueryLogs($database, $collection)
    {
        try {
            $log = new QueryLogs();
            if ($log->isEnabled()) {
                $logs = $log->getQueryHistory($database, $collection);
                return response()->success('success', array('logs' => $logs));
            }
            return response()->success('success', array('logs' => []));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
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
