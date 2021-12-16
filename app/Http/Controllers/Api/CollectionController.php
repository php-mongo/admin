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
 *  Defines the controllers used by controller.
 */
use App\Http\Controllers\Controller;

/**
 *  Defines the requests used by the controller.
 */
use Illuminate\Http\Request;
use App\Http\Requests\EditCollectionRequest;

/**
 * Models
 */
use App\Models\User;

use App\Http\Traits\CollectionTrait;

/**
 *  Defined application classes
 */
use App\Http\Classes\VarExport;
use App\Http\Classes\ExportDocument;
use App\Http\Classes\HighlightDocument;
use App\Http\Classes\MongoConnection as Mongo;
use App\Helpers\MongoHelper;
use App\Http\Classes\QueryLogs;
use App\Http\Classes\UnserialiseDocument;

/**
 * Vendors
 */
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use MongoDB;

/**
 * Class CollectionController
 *
 * @package App\Http\Controllers\Api
 */
class CollectionController extends Controller
{
    use CollectionTrait;

    /**
     * Add any arrays that the dbAdmin! roles can read
     *
     * @var string[]
     */
    private $dbAdminCollections = [
        'system.profile'
    ];

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
     * @var User|null
     */
    private $user;

    /**
     * @var array
     */
    private $unserialised;

    /**
     * @return array
     */
    private function getCollectionStatistics(): array
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
     * This has no effect during the QueryCollection method result processing
     *
     * @param   string $search
     *
     * @return  mixed
     */
    private function findCollectionStatisticsValue(string $search)
    {
        if (is_array($this->collectionStatistics)) {
            foreach ($this->collectionStatistics as $key => $value) {
                if ($key == $search && !is_array($value)) {
                    return $value;
                }
            }
        }
        return '';
    }

    /**
     * Mostly we don't need this as we want to strip out all the bits individually
     * In most ces ->getArrayCopy() or ->__toString() will suffice
     *
     */
    public function bsonUnserialize(string $data)
    {
        // TODO: Implement bsonUnserialize() method.
        $this->unserialised = MongoDB\BSON\toPHP($data, ['root' => 'UnserialiseDocument']);
    }

    /**
     * DatabasesController constructor.
     */
    public function __construct()
    {
        try {
            /** @var User $user */
            $this->user = auth()->guard('api')->user();
            $this->mongo = new Mongo($this->user);
            $this->client = $this->mongo->connectAndGetClient();
        } catch (\Throwable $t) {
            Log::debug($t->getMessage());
        }

        if ($this->user && $this->user->exists) {
            parent::__construct($this->user, $this->mongo);
        }
    }

    /**
     * Display a single collection.
     *
     * URL:         /api/v1/collection/{database}/{collection}
     * Method:      GET
     * Description: Fetches a collection with objects and stats
     *
     * @param   Request $request
     * @param   string $database
     * @param   string $collection
     * @return  Response
     */
    public function getCollection(Request $request, string $database, string $collection): Response
    {
        try {
            $this->database   = $database;
            $this->collection = $collection;
            if (isset($database, $collection)) {
                // get the collection
                $collection = $this->getOneCollection(
                    $this->mongo->connectClientCollection($database, $collection)
                );
                return response()->success('success', array('collection' => $collection));
            }
            return response()->error('failed', array('message' => 'required parameters missing'));
        } catch (\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Run a query on a collection
     *
     * URL:         /api/v1/collection/query
     * Method:      POST
     * Description: Runs a query using provide params and returns result
     *
     * @param   Request $request
     * @return  Response
     */
    public function queryCollection(Request $request): Response
    {
        try {
            $this->database   = $request->get('database');
            $this->collection = $request->get('collection');
            $params           = $request->get('params');
            $format           = $request->get('format');
            $criteria         = $params['criteria'];

            // ToDo: !! implement a sanity check if the format is array and we need to use EVIL eval() !!
            $query            = 'json' == $format ?
                json_decode($criteria[ $format ], true) :
                eval("return " . $criteria[ $format ] . ";");

            $collection       = $this->mongo->connectClientCollection($this->database, $this->collection);
            $results          = $collection->find($query);
            $results          = $results->toArray();
            $documentsArray   = [];
            $documents        = [];
            foreach ($results as $document) {
                MongoHelper::prepareDocument($document, $documentsArray, $this->fields);
            }
            $this->prepareObjects($results, $documentsArray, $documents);
            $log = new QueryLogs();
            if ($log->isEnabled()) {
                // ToDo ?? do we want to limit Query Logs to successful queries only ??
                $log->logQuery($this->database, $this->collection, $criteria[ $format ]);
            }
            return response()->success('success', array('documents' => $documents));
        } catch (\Exception $e) {
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
     * @param   EditCollectionRequest $request
     * @return  Response
     */
    public function createCollection(EditCollectionRequest $request): Response
    {
        try {
            $data = $request->validated();
            $database   = $data['database'];
            $collection = $data['name'];
            $capped     = $data['capped'];
            $count      = $data['count'];
            $size       = $data['size'];

            // default options
            $options = [];

            if (true == $capped && $size >= 1) {
                $options['capped'] = true;
                $options['size']   = $size;
                if ($count) {
                    $options['count'] = $count;
                }
            }

            // create the collection
            $database = $this->mongo->connectClientDb($database);
            $database->createCollection($collection, $options);
            $coll     = $this->getOneCollection(
                $this->mongo->connectClientCollection($database, $collection)
            );

            return response()->success('success', array( 'collection' => $coll ));
        } catch (\Exception $e) {
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
     * @param   Request $request
     * @return  Response
     */
    public function exportCollection(Request $request): Response
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

                // handle some variations on the export theme
                if (true == $params['json']) {
                    // ensure 1st collection is not null
                    $coll = $collections[0] ?: $collections[1];
                    // only available for a single collection
                    $documents  = $database->selectCollection($coll)->find();
                    $array  = [];
                    $fields = [];
                    foreach ($documents as $document) {
                        $array[] = MongoHelper::extractDocument($document, $fields, true);
                    }
                    $contents = json_encode($array);
                } else {
                    // handle indexes
                    foreach ($collections as $collectionName) {
                        if (!$collectionName) {
                            continue;
                        }
                        $collection  = $database->selectCollection($collectionName);
                        $information = $collection->listIndexes();
                        // for now : we are not importing the indexes
                        foreach ($information as $info) {
                            $options   = array();
                            $exporter  =  new VarExport($database, $info['key']);
                            $contents .= "\n/** {$collection} indexes **/\ndb.getCollection(\"" .
                                addslashes($collectionName) . "\").ensureIndex(" . $exporter->export('json') . ");\n";
                        }
                    }

                    // handle data
                    foreach ($collections as $collectionName) {
                        if (!$collectionName) {
                            continue;
                        }
                        $documents  = $database->selectCollection($collectionName)->find();
                        $contents .= "\n/** " . $collectionName  . " records **/\n";
                        foreach ($documents as $document) {
                            $countDocuments++;
                            $exporter = new VarExport($database, $document);
                            $doc = MongoHelper::extractDocument($document);
                            $contents .= "db.getCollection(\"" .
                                addslashes($collectionName) . "\").insert(" . json_encode($exporter->setObjectId($doc)) . ");\n";
                        }
                    }
                }

                if (true == $params['download']) {
                    // set the file name // ToDo !! this is actually handled in the front-end : we are using AJAX downloads !!
                    $filePrefix = "mongodb-" . urldecode($database) . "-" . date("Ymd-His");

                    if (true == $params['gzip']) {
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

        } catch (\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Import documents from one or more collections
     *
     * URL:         /api/v1/collection/import
     * Method:      POST
     * Description: Import documents from one or ore collection(s) into the given database
     *
     * @param   Request $request
     * @return  Response
     */
    public function importCollection(Request $request): Response
    {
        try {
            $database       = $request->get('database');
            $collection     = $request->get('collection');
            $type           = $request->get('type', 'admin');
            $gzip           = $request->get('gzip', false);
            $useImportColl  = boolval($request->boolean('useImportCollection', false));
            $fileName       = $request->file('file')->getClientOriginalName();
            // ToDo: implement this behaviour/action
            //$replace        = $request->boolean('replace', false);

            if (isset($database, $type)) {
                // get the file
                foreach ($_FILES as $f) {
                    $file = $f;
                }

                // for zipped files
                if (true === $gzip) {
                    $body = gzuncompress(file_get_contents($file['tmp_name']));
                } else {
                    $body = file_get_contents($file['tmp_name']);
                }

                // connect the manager
                $this->mongo->connectManager();
                $manager = $this->mongo->getManager();

                // return the inserted count to the front-end
                $inserted = 0;

                // ToDo: build some Imports classes and traits to move this all away from MongoHelper
                // this is for files exported from PhpMongoAdmin *.js or default JS import scripts
                // our JSON export will only produce a single element array
                if ('admin' == $type) {
                    $insertArray = [];

                    // run the correct method to retrieve the inserts
                    if (strpos($body, "insertMany") !== false) {
                        MongoHelper::getInsertManyContent($body, $insertArray, $collection);
                    }

                    if (strpos($body, "getCollection") !== false) {
                        MongoHelper::getInserts($body, $insertArray);
                    }

                    // this will handle multiple collection inserts
                    MongoHelper::handleBulkInsert($manager, $database, $insertArray, $collection, $useImportColl, $inserted, false);

                } else {
                    // remove spaces and new lines
                    $body = str_replace([" ", "\\n"], "", $body);
                    $body = trim(preg_replace('/\s+/', '', $body));

                    // explode the body into an array - we'll use this for both file formats
                    $arr = explode(";", $body);

                    // ToDo: !! for now - we accept the JSON file exported by PhpMongoAdmin and also JSON export from Compass
                    if (strpos($fileName, ".json")) {
                        $arr = json_decode(trim($body, '"'), true);
                    } else {
                        $arr = $arr[0];
                        $arr = json_decode($arr, true);
                    }
                    MongoHelper::handleBulkInsert($manager, $database, $arr, $collection, $useImportColl, $inserted, true);
                }
                return response()->success('success', array('import' => $inserted ));
            }
            return response()->error('failed', array('message' => 'parameters missing'));
        } catch (\Exception $e) {
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
     * @param   Request $request
     * @return  Response
     */
    public function propertiesCollection(Request $request): Response
    {
        try {
            $database       = $request->get('database');
            $collection     = $request->get('collection');
            $params         = $request->get('params');
            $errors         = [];
            if (isset($database, $collection, $params)) {
                // get the database
                /** @var MongoDB\Database $db */
                $db = $this->mongo->connectClientDb($database);

                // temp collection name
                $tempCollectionName = 'phpMongoAdmin_' . time();
                if ($options = MongoHelper::validateCollectionProperties($params, $errors)) {
                    // get the document from current collection
                    /** @var MongoDB\Collection $documents */
                    $documents = MongoHelper::getObjects($this->client, $database, $collection)['objects'];

                    // convert to array -> make compatible with primary BulkInsert action
                    $arr[ $tempCollectionName ] = [];
                    foreach ($documents as $doc) {
                        $arr[ $tempCollectionName ][] = $doc;
                    }

                    // create the TEMP collection -> continue on success
                    $result = $db->createCollection($tempCollectionName, $options)->getArrayCopy();
                    if ($result['ok'] == 1) {
                        // track
                        $inserted = 0;
                        // connect the manager
                        $this->mongo->connectManager();
                        /** @var MongoDB\Driver\Manager $manager */
                        $manager = $this->mongo->getManager();
                        // this will handle multiple collection inserts
                        MongoHelper::handleBulkInsert($manager, $database, $arr, $tempCollectionName, false, $inserted);

                        if ($inserted == count($arr[ $tempCollectionName ])) {
                            // temp collection populated successfully -> drop the old collection
                            $result = $db->dropCollection($collection)->getArrayCopy();
                            if ($result['ok'] == 1) {
                                /** @var MongoDB\Collection $coll */
                                $command = new MongoDB\Driver\Command([
                                    'renameCollection' => $database . '.' . $tempCollectionName,
                                    'to' => $database . '.' . $collection
                                ]);
                                // ToDo !! analyse result and act accordingly !!
                                $result = $manager->executeCommand('admin', $command);

                                $collection = $this->getOneCollection(
                                    $this->mongo->connectClientCollection($database, $collection)
                                );
                                return response()->success('success', array( 'collection' => $collection ));
                            }
                        }
                    }
                    return response()->error('failed', array('message' => 'unhandled errors have occurred'));
                }
                return response()->error('failed', array('message' => $errors));
            }
        } catch (\MongoDB\Driver\Exception\Exception $exception) {
            return response()->error('failed', array('message' => $exception->getMessage()));
        } catch (\Exception $e) {
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
     * @param   Request $request
     * @return  Response
     */
    public function indexCollection(Request $request): Response
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

            // get the collection
            /** @var MongoDB\Collection $coll */
            $coll = $this->mongo->connectClientCollection($database, $collection);

            if (true == $params['create']) {
                $keys    = [];
                $options = [];

                // set the options
                if (true == $params['unique']) {
                    $options['unique'] = true;
                }
                if (true == $params['sparse']) {
                    $options['sparse'] = true;
                }

                // extract the keys
                $fields = $params['fields'];
                foreach ($fields as $field) {
                    $keys[ $field['field'] ] = $directions[$field['direction']];
                }

                // create
                $indexName = $coll->createIndex($keys, $options);

                return response()->success('success', array('index' => $indexName));
            }
        } catch (\Exception $e) {
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
     * @param   Request $request
     * @return  Response
     */
    public function renameCollection(Request $request): Response
    {
        try {
            $database       = $request->get('database');
            $collection     = $request->get('collection');
            $params         = $request->get('params');

            if (!empty($params['newName'])) {
                // connect the manager
                $this->mongo->connectManager();
                /** @var MongoDB\Driver\Manager $manager */
                $manager = $this->mongo->getManager();

                /** @var MongoDB\Collection $coll */
                $command = new MongoDB\Driver\Command([
                    'renameCollection' => $database . '.' . $collection,
                    'to' => $database . '.' . $params['newName']
                ]);
                // ToDo: analyse this result and acto accordingly
                $result = $manager->executeCommand('admin', $command);

                return response()->success('success', array( 'collection' => $collection ));
            }
            return response()->error('failed', array('message' => 'missing required parameters'));
        } catch (MongoDB\Driver\Exception\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        } catch (\Exception $e) {
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
     * @param   Request $request
     * @return  Response
     */
    public function duplicateCollection(Request $request): Response
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
                $db         = $this->mongo->connectClientDb($database);

                if (true == $overwrite) {
                    // get the destination
                    $destination = $this->mongo->connectClientCollection($database, $duplicateName);
                    // confirm we have a collection
                    if ($destination instanceof MongoDB\Collection) {
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
                $db->createCollection($duplicateName, $options);

                // get the document from current collection
                /** @var MongoDB\Collection $documents */
                $documents = MongoHelper::getObjects($this->client, $database, $collection)['objects'];

                // connect the manager
                $this->mongo->connectManager();
                /** @var MongoDB\Driver\Manager $manager */
                $manager = $this->mongo->getManager();

                $inserted = 0;
                // enforce the use of our provided collection name - also passing the documents array within an indexed array container
                MongoHelper::handleBulkInsert($manager, $database, array(0 => $documents), $duplicateName, true, $inserted);

                if (true == $indexes) {
                    $keys = [];
                    $i    = 0;  // might be able to use (~ as $i => $index) instead
                    // copy the indexes from source to duplicate
                    /** @var MongoDB\Collection $source */
                    $source  = $this->mongo->connectClientCollection($database, $collection);
                    foreach ($source->listIndexes() as $index) {
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
                        $coll = $this->mongo->connectClientCollection($database, $duplicateName);
                        $coll->createIndex($keys, $options);
                    }
                }
                $duplicate = $this->getOneCollection(
                    $this->mongo->connectClientCollection($database, $collection)
                );
                return response()->success('success', array( 'collection' => $duplicate ));
            }
            return response()->error('failed', array('message' => 'missing required parameters'));
        } catch (\Exception $e) {
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
     * @param   Request $request
     * @param   string $database
     * @param   string $collection
     * @return  Response
     */
    public function validateCollection(Request $request, string $database, string $collection): Response
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
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        } catch (\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }

    /**
     * Exlain a mongodb query
     *
     * URL:         /api/v1/collection/query/explain
     * Method:      POSY
     * Description: Validate a single collection
     *
     * @param   Request $request
     * @return  Response
     */
    public function getQueryExplain(Request $request): Response
    {
        try {
            $database       = $request->get('database');
            $collection     = $request->get('collection');
            $format         = $request->get('format');
            $query          = $request->get('query');

            $query = json_decode($query, true);

            /** @var MongoDB\Collection $coll */
            $coll = $this->mongo->connectClientCollection($database, $collection);

            $query = new MongoDb\Operation\Find(
                $coll->getDatabaseName(),
                $coll->getCollectionName(),
                $query
            );

            /** @var MongoDB\Model\BSONDocument $result */
            $result = $coll->explain($query);
            $array  = $result->getArrayCopy();

            $result = [];
            foreach ($array as $section) {
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

            // ToDo: !! Build a Document extraction class
            // - the MongoHelper is not that good as yet - may as well create a dedicated Class for the task
            if (isset($result[0]['parsedQuery']['x']) && $result[0]['parsedQuery']['x'] instanceof MongoDB\Model\BSONDocument) {
                $result[0]['parsedQuery']['x'] = $result[0]['parsedQuery']['x']->getArrayCopy();
            }

            if (
                isset($result[0]['winningPlan']['inputStage']) &&
                $result[0]['winningPlan']['inputStage'] instanceof MongoDB\Model\BSONDocument
            ) {
                $result[0]['winningPlan']['inputStage'] = $result[0]['winningPlan']['inputStage']->getArrayCopy();
            }

            if (
                isset($result[0]['winningPlan']['inputStage']['keyPattern']) &&
                $result[0]['winningPlan']['inputStage']['keyPattern'] instanceof MongoDB\Model\BSONDocument
            ) {
                $result[0]['winningPlan']['inputStage']['keyPattern'] =
                    $result[0]['winningPlan']['inputStage']['keyPattern']->getArrayCopy();
            }

            if (
                isset($result[0]['winningPlan']['inputStage']['multiKeyPaths']) &&
                $result[0]['winningPlan']['inputStage']['multiKeyPaths'] instanceof MongoDB\Model\BSONDocument
            ) {
                $result[0]['winningPlan']['inputStage']['multiKeyPaths'] =
                    $result[0]['winningPlan']['inputStage']['multiKeyPaths']->getArrayCopy();
            }

            if (
                isset($result[0]['winningPlan']['inputStage']['multiKeyPaths']['x']) &&
                $result[0]['winningPlan']['inputStage']['multiKeyPaths']['x'] instanceof MongoDB\Model\BSONArray
            ) {
                $result[0]['winningPlan']['inputStage']['multiKeyPaths']['x'] =
                    $result[0]['winningPlan']['inputStage']['multiKeyPaths']['x']->getArrayCopy();
            }

            if (
                isset($result[0]['winningPlan']['inputStage']['indexBounds']) &&
                $result[0]['winningPlan']['inputStage']['indexBounds'] instanceof MongoDB\Model\BSONDocument
            ) {
                $result[0]['winningPlan']['inputStage']['indexBounds'] =
                    $result[0]['winningPlan']['inputStage']['indexBounds']->getArrayCopy();
            }

            if (
                isset($result[0]['winningPlan']['inputStage']['indexBounds']['x']) &&
                $result[0]['winningPlan']['inputStage']['indexBounds']['x'] instanceof MongoDB\Model\BSONArray
            ) {
                $result[0]['winningPlan']['inputStage']['indexBounds']['x'] =
                    $result[0]['winningPlan']['inputStage']['indexBounds']['x']->getArrayCopy();
            }

            if (
                isset($result[0]['rejectedPlans']) &&
                $result[0]['rejectedPlans'] instanceof MongoDB\Model\BSONArray
            ) {
                $result[0]['rejectedPlans'] = $result[0]['rejectedPlans']->getArrayCopy();
            }

            if (
                isset($result[1]['executionStages']['inputStage']) &&
                $result[1]['executionStages']['inputStage'] instanceof MongoDB\Model\BSONDocument
            ) {
                $result[1]['executionStages']['inputStage'] = $result[1]['executionStages']['inputStage']->getArrayCopy();
            }

            if (
                isset($result[1]['executionStages']['inputStage']['keyPattern']) &&
                $result[1]['executionStages']['inputStage']['keyPattern'] instanceof MongoDB\Model\BSONDocument
            ) {
                $result[1]['executionStages']['inputStage']['keyPattern'] =
                    $result[1]['executionStages']['inputStage']['keyPattern']->getArrayCopy();
            }

            if (
                isset($result[1]['executionStages']['inputStage']['multiKeyPaths']) &&
                $result[1]['executionStages']['inputStage']['multiKeyPaths'] instanceof MongoDB\Model\BSONDocument
            ) {
                $result[1]['executionStages']['inputStage']['multiKeyPaths'] =
                    $result[1]['executionStages']['inputStage']['multiKeyPaths']->getArrayCopy();
            }

            if (
                isset($result[1]['executionStages']['inputStage']['multiKeyPaths']['x']) &&
                $result[1]['executionStages']['inputStage']['multiKeyPaths']['x'] instanceof MongoDB\Model\BSONArray
            ) {
                $result[1]['executionStages']['inputStage']['multiKeyPaths']['x'] =
                    $result[1]['executionStages']['inputStage']['multiKeyPaths']['x']->getArrayCopy();
            }

            if (
                isset($result[1]['executionStages']['inputStage']['indexBounds']) &&
                $result[1]['executionStages']['inputStage']['indexBounds'] instanceof MongoDB\Model\BSONDocument
            ) {
                $result[1]['executionStages']['inputStage']['indexBounds'] =
                    $result[1]['executionStages']['inputStage']['indexBounds']->getArrayCopy();
            }

            if (
                isset($result[1]['executionStages']['inputStage']['indexBounds']['x']) &&
                $result[1]['executionStages']['inputStage']['indexBounds']['x'] instanceof MongoDB\Model\BSONArray
            ) {
                $result[1]['executionStages']['inputStage']['indexBounds']['x'] =
                    $result[1]['executionStages']['inputStage']['indexBounds']['x']->getArrayCopy();
            }

            if (isset($result[1]['allPlansExecution']) && $result[1]['allPlansExecution'] instanceof MongoDB\Model\BSONArray) {
                $result[1]['allPlansExecution'] = $result[1]['allPlansExecution']->getArrayCopy();
            }
            return response()->success('success', array( 'explain' => $result ));

        } catch (\Exception $e) {
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
     * @param   string $database
     * @param   string $collection
     *
     * @return  Response
     */
    public function getQueryLogs(string $database, string $collection): Response
    {
        try {
            $log = new QueryLogs();
            if ($log->isEnabled()) {
                $logs = $log->getQueryHistory($database, $collection);
                return response()->success('success', array('logs' => $logs));
            }
            return response()->success('success', array('logs' => []));
        } catch (\Exception $e) {
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
     * @param   Request $request
     * @return  Response
     */
    public function clearCollection(Request $request): Response
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
        } catch (\Exception $e) {
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
     * @param   Request $request
     * @return  Response
     */
    public function deleteCollection(Request $request): Response
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

                        // ToDo: !! getting an odd error from front-end - request is sent twice
                        if (isset($result->errmsg)) {
                            return response()->error('failed', array('message' => $result->errmsg));
                        } else {
                            $status[] = $this->setDeleteStatus($name, $result->getArrayCopy(), $ns);
                        }
                    }
                }
            }
            return response()->success('success', array('status' => $status ));
        } catch (\Exception $e) {
            return response()->error('failed', array('message' => $e->getMessage()));
        }
    }
}
