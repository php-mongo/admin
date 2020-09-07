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

            //    echo '<pre>'; var_dump($collections); echo '</pre>';

                // handle some variations on the export theme
                if ($params['json'] == true) {
                    // only available for a single collection
                    $documents  = $database->selectCollection( $collections[0] )->find();
                    $array  = [];
                    $fields = [];
                    foreach ($documents as $document) {
                        $array[] = MongoHelper::extractDocument($document, $fields, true);
                    }
                    $contents = json_encode($array);
                }
                else {
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
                }

                if ($params['download'] == true) {
                    // set the file name // ToDo !! this is actually handled in the front-end : we are using AJAX downloads !!
                    $filePrefix = "mongodb-" . urldecode($database) . "-" . date("Ymd-His");

                    if ($params['gzip'] == true) {
                        header("Content-type: application/x-gzip");
                        header("Content-Disposition: attachment; filename=\"{$filePrefix}.gz\"");
                        echo gzcompress($contents, 9);

                    } else {
                        header("Content-type: application/octet-stream");
                      //  header("Content-type: application/json");
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

                // for zipped files
                if ($gzip == true) {
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
                // our JSON export will only produce a single element array
                if ($type == 'admin' && count($arr) >= 3) {
                    $insertArray = [];

                    // iterate the import array
                    foreach ($arr as $insert) {
                        // ignore the comments, index insert etc
                        if (strpos( $insert, "insert") !== false) {
                            // we're good to go! get the collection(s)
                            // create array using collections as primary array key
                            $insertArray[ MongoHelper::getCollectionNameFromInsert( $insert ) ][] = MongoHelper::getDateFromInsert( $insert );
                        }
                    }

                    // this will handle multiple collection inserts
                    MongoHelper::handleBulkInsert($manager, $database, $insertArray, $collection, $useCollection, $inserted);

                } else {
                    // ToDo: !! for now - we accept the JSON file exported by PhpMongoAdmin and also JSON export from Compass
                    $arr = $arr[0];
                //    dd(json_decode($arr, true));
                    $arr = json_decode($arr, true);
                    MongoHelper::handleBulkInsert($manager, $database, $arr, $collection, $useCollection, $inserted, true);
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
     * Update the properties on one collection
     *
     * URL:         /api/v1/collection/properties
     * Method:      POST
     * Description: Update properties (capped, size, max)
     *
     * @param Request $request
     * @return mixed
     */
    public function propertiesCollection(Request $request)
    {
        try {
            $database       = $request->get('database');
            $collection     = $request->get('collection');
            $params         = $request->get('params');
            $errors         = [];
            if (isset($database, $collection, $params)) {
                // get the database
                /** @var MongoDB\Database $db */
                $db         = $this->mongo->connectClientDb( $database );

                // temp collection name
                $tempCollectionName = 'phpMongoAdmin_' . time();
                if ($options   = MongoHelper::validateCollectionProperties( $params, $errors )) {
                    // get the document from current collection
                    /** @var MongoDB\Collection $documents */
                    $documents = MongoHelper::getObjects( $this->client, $database, $collection )['objects'];

                    // convert to array -> make compatible with primary BulkInsert action
                    $arr[ $tempCollectionName ] = [];
                    foreach ( $documents as $doc ) {
                        $arr[ $tempCollectionName ][] = $doc;
                    }

                    // create the TEMP collection -> continue on success
                    $result = $db->createCollection( $tempCollectionName, $options )->getArrayCopy();
                    if ( $result['ok'] == 1 ) {
                        // track
                        $inserted = 0;
                        // connect the manager
                        $this->mongo->connectManager();
                        /** @var MongoDB\Driver\Manager $manager */
                        $manager = $this->mongo->getManager();
                        // this will handle multiple collection inserts
                        MongoHelper::handleBulkInsert($manager, $database, $arr, $tempCollectionName, false, $inserted);

                        if  ( $inserted == count($arr[ $tempCollectionName ]) ) {
                            // temp collection populated successfully -> drop the old collection
                            $result = $db->dropCollection( $collection )->getArrayCopy();
                            if ( $result['ok'] == 1 ) {
                                /** @var MongoDB\Collection $coll */
                                $command = new MongoDB\Driver\Command([
                                    'renameCollection' => $database.'.'.$tempCollectionName,
                                    'to' => $database.'.'.$collection
                                ]);
                                $result = $manager->executeCommand('admin', $command);

                        //        dd($result);

                                $collection = $this->getOneCollection ($database, $collection );
                                return response()->success('success', array( 'collection' => $collection ));
                            }
                        }
                    }
                    return response()->error('failed', array('message' => 'unhandled errors have occurred'));
                }
                return response()->error('failed', array('message' => $errors));
            }
        }
        catch(\MongoDB\Driver\Exception\Exception $exception) {
         //   dd($exception);
            return response()->error('failed', array('message' => $exception->getMessage()));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     *Add an Index to one collection
     *
     * URL:         /api/v1/collection/index
     * Method:      POST
     * Description: Add an new Index to a collection (standard or 2d)
     *
     * @param Request $request
     * @return mixed
     */
    public function indexCollection(Request $request)
    {
        try {
            $database       = $request->get('database');
            $collection     = $request->get('collection');
            $params         = $request->get('params');
            $errors         = [];
            $directions     = array(
                "ASC" => 1,
                "DESC" => -1
            );

         //   dd($params);

            // get the collection
            /** @var MongoDB\Collection $coll */
            $coll         = $this->mongo->connectClientCollection( $database, $collection );

            if ($params['create'] == true) {
                $keys    = [];
                $options = [];

                // set the options
                if ($params['unique'] == true) {
                    $options['unique'] = true;
                }
                if ($params['sparse'] == true) {
                    $options['sparse'] = true;
                }

                // extract the keys
                $fields = $params['fields'];
                foreach ($fields as $field) {
                    $keys[ $field['field'] ] = $directions[ $field['direction'] ];
                }

                //dd($keys);

                // create
                $indexName = $coll->createIndex($keys, $options);

             //   dd($indexName);

                return response()->success('success', array( 'index' => $indexName ));
            }
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Rename a collection
     *
     * URL:         /api/v1/collection/rename
     * Method:      POST
     * Description: Rename a single collection
     *
     * @param Request $request
     * @return mixed
     */
    public function renameCollection(Request $request)
    {
        try {
            $database       = $request->get('database');
            $collection     = $request->get('collection');
            $params         = $request->get('params');

         //   dd($params);

            if (!empty($params['newName'])) {
                // connect the manager
                $this->mongo->connectManager();
                /** @var MongoDB\Driver\Manager $manager */
                $manager = $this->mongo->getManager();

                /** @var MongoDB\Collection $coll */
                $command = new MongoDB\Driver\Command([
                    'renameCollection' => $database.'.'.$collection,
                    'to' => $database.'.'.$params['newName']
                ]);
                $result = $manager->executeCommand('admin', $command);

            //    dd($result);

                return response()->success('success', array( 'collection' => $collection ));
            }
            return response()->error('failed', array('message' => 'missing required parameters'));
        }
        catch(MongoDB\Driver\Exception\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
        catch (\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Rename a collection
     *
     * URL:         /api/v1/collection/duplicate
     * Method:      POST
     * Description: Duplicate a single collection
     *
     * @param Request $request
     * @return mixed
     */
    public function duplicateCollection(Request $request)
    {
        try {
            $database       = $request->get('database');
            $collection     = $request->get('collection');
            $params         = $request->get('params');

            if (!empty($params['duplicateName'])) {
                $duplicateName = $params['duplicateName'];
                $overwrite     = $params['overwrite'];
                $indexes       = $params['indexes'];

                // get the database
                /** @var MongoDB\Database $db */
                $db         = $this->mongo->connectClientDb( $database );

                if ($overwrite == true) {
                    // get the destination
                    $destination = $this->mongo->connectClientCollection( $database, $duplicateName );
                    // confirm we have a collection
                    if ( $destination instanceof MongoDB\Collection ) {
                        /** @var MongoDB\Model\BSONDocument $result */
                        $result = $destination->drop();
                        if ($result->errmsg) {
                            return response()->error('failed', array('message' => $result->errmsg));
                        }
                    }
                }

                // default options
                // ToDo: we will introduce an option to 'preserve' the options from the source collections
                $options = [];
                $db->createCollection( $duplicateName, $options );

                // ToDo:  modify the getObjects() so that we can onlt have the 'objects' data returned
                $documents = MongoHelper::getObjects( $this->client, $database, $collection )['objects'];

                // connect the manager
                $this->mongo->connectManager();
                /** @var MongoDB\Driver\Manager $manager */
                $manager = $this->mongo->getManager();

                $inserted = 0;
                // enforce the use of our provided collection name - also passing the documents array within an indexed array container
                MongoHelper::handleBulkInsert($manager, $database, array(0 => $documents), $duplicateName, true, $inserted);

                if ($indexes == true) {
                    $keys = [];
                    $i    = 0;  // might be able to use (~ as $i => $index) instead
                    // copy the indexes from source to duplicate
                    /** @var MongoDB\Collection $source */
                    $source  = $this->mongo->connectClientCollection( $database, $collection );
                    foreach ( $source->listIndexes() as $index ) {
                        // we dont need the _id_ index as its created along with the collection
                        if ($i >= 1) {
                            foreach ($index['key'] as $k => $v) {
                                $keys[ $k ] = $v;
                            }
                        }
                        $i++;
                    }

                    if (count($keys) >= 1) {
                        /** @var MongoDB\Collection $coll */
                        $coll = $this->mongo->connectClientCollection( $database, $duplicateName );
                        $coll->createIndex($keys, $options);
                    }
                }
                $duplicate = $this->getOneCollection( $database, $duplicateName );
                return response()->success('success', array( 'collection' => $duplicate ));
            }
            return response()->error('failed', array('message' => 'missing required parameters'));
        }
        catch (\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Validate a collection
     *
     * URL:         /api/v1/collection/validate/{database}/{collection}
     * Method:      GET
     * Description: Validate a single collection
     *
     * @param Request $request
     * @param $database
     * @param $collection
     * @return mixed
     */
    public function validateCollection(Request $request, $database, $collection)
    {
        try {
            if (isset($database, $collection)) {
                // connect the manager
                $this->mongo->connectManager();
                /** @var MongoDB\Driver\Manager $manager */
                $manager = $this->mongo->getManager();

                /** @var MongoDB\Collection $coll */
                $command = new MongoDB\Driver\Command([
                    'validate' => $collection,
                    'full' => true
                ]);
                $validation = $manager->executeCommand($database, $command);

                return response()->success('success', array( 'validation' => $validation->toArray() ));
            }
        }
        catch(\MongoDB\Driver\Exception\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Exlain a mongodb query
     *
     * URL:         /api/v1/collection/query/explain
     * Method:      POSY
     * Description: Validate a single collection
     * @return mixed
     * @throws MongoDB\Driver\Exception\Exception
     */
    public function getQueryExplain(Request $request)
    {
        try {
            $database       = $request->get('database');
            $collection     = $request->get('collection');
            $format         = $request->get('format');
            $query          = $request->get('query');

            $query = json_decode($query, true);

            /** @var MongoDB\Collection $coll */
            $coll = $this->mongo->connectClientCollection( $database, $collection );

            $query = new MongoDb\Operation\Find(
                $coll->getDatabaseName(),
                $coll->getCollectionName(),
                $query
            );

            /** @var MongoDB\Model\BSONDocument $result */
            $result = $coll->explain( $query );

        //    $fields = [];
        //    $array  = MongoHelper::extractDocument( $result->getArrayCopy(), $fields); // $result->getArrayCopy();
        //    dd($array);
       //     echo '<pre>'; var_dump($array); echo '</pre>'; die;

            $array  = $result->getArrayCopy();
            $result = [];
            foreach ($array as $section) {
                /** M */
                if ($section instanceof MongoDB\Model\BSONDocument) {
                    $arr = [];
                    $elements = $section->getArrayCopy();
                    foreach ($elements as $key => $element) {
                        if ($element instanceof MongoDB\Model\BSONDocument) {
                            $value = $element->getArrayCopy();
                            if ($value instanceof MongoDB\Model\BSONDocument || $value instanceof MongoDB\Model\BSONArray) {
                                $value = $value->getArrayCopy();
                            }
                            $arr[ $key ] = $value;
                        } else {
                            $arr[ $key ] = $element;
                        }
                    }
                    $result[] = $arr;

                } else {
                    $result[] = $section;
                }
            }

         //   dd($result);

            if (isset($result[0]['parsedQuery']['x']) && $result[0]['parsedQuery']['x'] instanceof MongoDB\Model\BSONDocument) {
                $result[0]['parsedQuery']['x'] = $result[0]['parsedQuery']['x']->getArrayCopy();
            }

            if (isset($result[0]['winningPlan']['inputStage']) && $result[0]['winningPlan']['inputStage'] instanceof MongoDB\Model\BSONDocument) {
                $result[0]['winningPlan']['inputStage'] = $result[0]['winningPlan']['inputStage']->getArrayCopy();
            }

            if (isset($result[0]['winningPlan']['inputStage']['keyPattern']) && $result[0]['winningPlan']['inputStage']['keyPattern'] instanceof MongoDB\Model\BSONDocument) {
                $result[0]['winningPlan']['inputStage']['keyPattern'] = $result[0]['winningPlan']['inputStage']['keyPattern']->getArrayCopy();
            }

            if (isset($result[0]['winningPlan']['inputStage']['multiKeyPaths']) && $result[0]['winningPlan']['inputStage']['multiKeyPaths'] instanceof MongoDB\Model\BSONDocument) {
                $result[0]['winningPlan']['inputStage']['multiKeyPaths'] = $result[0]['winningPlan']['inputStage']['multiKeyPaths']->getArrayCopy();
            }

            if (isset($result[0]['winningPlan']['inputStage']['multiKeyPaths']['x']) && $result[0]['winningPlan']['inputStage']['multiKeyPaths']['x'] instanceof MongoDB\Model\BSONArray) {
                $result[0]['winningPlan']['inputStage']['multiKeyPaths']['x'] = $result[0]['winningPlan']['inputStage']['multiKeyPaths']['x']->getArrayCopy();
            }

            if (isset($result[0]['winningPlan']['inputStage']['indexBounds']) && $result[0]['winningPlan']['inputStage']['indexBounds'] instanceof MongoDB\Model\BSONDocument) {
                $result[0]['winningPlan']['inputStage']['indexBounds'] = $result[0]['winningPlan']['inputStage']['indexBounds']->getArrayCopy();
            }

            if (isset($result[0]['winningPlan']['inputStage']['indexBounds']['x']) && $result[0]['winningPlan']['inputStage']['indexBounds']['x'] instanceof MongoDB\Model\BSONArray) {
                $result[0]['winningPlan']['inputStage']['indexBounds']['x'] = $result[0]['winningPlan']['inputStage']['indexBounds']['x']->getArrayCopy();
            }

            if (isset($result[0]['rejectedPlans']) && $result[0]['rejectedPlans'] instanceof MongoDB\Model\BSONArray) {
                $result[0]['rejectedPlans']= $result[0]['rejectedPlans']->getArrayCopy();
            }

            if (isset($result[1]['executionStages']['inputStage']) && $result[1]['executionStages']['inputStage'] instanceof MongoDB\Model\BSONDocument) {
                $result[1]['executionStages']['inputStage'] = $result[1]['executionStages']['inputStage']->getArrayCopy();
            }

            if (isset($result[1]['executionStages']['inputStage']['keyPattern']) && $result[1]['executionStages']['inputStage']['keyPattern'] instanceof MongoDB\Model\BSONDocument) {
                $result[1]['executionStages']['inputStage']['keyPattern'] = $result[1]['executionStages']['inputStage']['keyPattern']->getArrayCopy();
            }

            if (isset($result[1]['executionStages']['inputStage']['multiKeyPaths']) && $result[1]['executionStages']['inputStage']['multiKeyPaths'] instanceof MongoDB\Model\BSONDocument) {
                $result[1]['executionStages']['inputStage']['multiKeyPaths'] = $result[1]['executionStages']['inputStage']['multiKeyPaths']->getArrayCopy();
            }

            if (isset($result[1]['executionStages']['inputStage']['multiKeyPaths']['x']) && $result[1]['executionStages']['inputStage']['multiKeyPaths']['x'] instanceof MongoDB\Model\BSONArray) {
                $result[1]['executionStages']['inputStage']['multiKeyPaths']['x'] = $result[1]['executionStages']['inputStage']['multiKeyPaths']['x']->getArrayCopy();
            }

            if (isset($result[1]['executionStages']['inputStage']['indexBounds']) && $result[1]['executionStages']['inputStage']['indexBounds'] instanceof MongoDB\Model\BSONDocument) {
                $result[1]['executionStages']['inputStage']['indexBounds'] = $result[1]['executionStages']['inputStage']['indexBounds']->getArrayCopy();
            }

            if (isset($result[1]['executionStages']['inputStage']['indexBounds']['x']) && $result[1]['executionStages']['inputStage']['indexBounds']['x'] instanceof MongoDB\Model\BSONArray) {
                $result[1]['executionStages']['inputStage']['indexBounds']['x'] = $result[1]['executionStages']['inputStage']['indexBounds']['x']->getArrayCopy();
            }

            if (isset($result[1]['allPlansExecution']) && $result[1]['allPlansExecution'] instanceof MongoDB\Model\BSONArray) {
                $result[1]['allPlansExecution'] = $result[1]['allPlansExecution']->getArrayCopy();
            }

         //   dd($result);

            return response()->success('success', array( 'explain' => $result ));
        }
        catch(\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Fetch query logs
     *
     * URL:         /api/v1/collection/query/logs/{database}/{collection}
     * Method:      GET
     * Description: Get query logs for a given database and collection and current user
     *
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
}
