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
 *   Defines the requests used by the controller.
 */

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Response used by controller
 */
use Illuminate\Http\Response;

/**
 *  Defined application classes
 */
use App\Http\Classes\ExportDocument;
use App\Http\Classes\HighlightDocument;
use App\Http\Classes\MongoConnection as Mongo;
use App\Helpers\MongoHelper;
use App\Helpers\DocumentHelper;

/**
 *  Defined models
 */
use App\Models\User;

/**
 * Vendors
 */

use Illuminate\Support\Facades\Log;
use MongoDB\BSON\Unserializable;
use MongoDB;

/**
 * Exceptions
 */
use Exception;
use App\Exceptions\DocumentObjectException;

/**
 * Class DocumentController
 * @package App\Http\Controllers\Api
 */
class DocumentController extends Controller implements Unserializable
{
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
     * @var Mongo $mongo
     */
    private $mongo;

    /**
     * @var MongoDB\Client  $client
     */
    private $client;

    /**
     * @var string  $database   mongo database name
     */
    private $database;

    /**
     * @var string  $collection mongo collection name
     */
    private $collection;

    /**
     * @var array   $types  these are the data types we do not to treat nicely
     */
    private $types = ['double', 'integer', 'long', 'mixed'];

    /**
     * @var User|null
     */
    private $user;

    /**
     * Split the exception error message - we usually only want the first part
     * Ensure that there is a closing brace if and opening brace exists
     *
     * @param string $input
     * @return string
     */
    private function scrapeError(string $input): string
    {
        $error = ucfirst(explode(",", $input)[0]);
        if (strpos($error, "{") !== false && strpos($error, "}") === false) {
            return $error . " }";
        }
        return $error;
    }

    /**
     * Returns a document from the given collection
     *
     * @param   string  $db string DB Name
     * @param   string  $collection string Collection name
     * @param   array   $document
     * @return  array|object
     * @throws  DocumentObjectException
     */
    private function getObject(string $db, string $collection, array $document)
    {
        try {
             // no errors this way
             $collection = $this->client->$db->selectCollection($collection);
             $object     = $collection->findOne($document);

             $documentsArray = [];
             $fields = [];
             MongoHelper::prepareDocument($object, $documentsArray, $fields);

             /** @var MongoDB\BSON\ObjectId $id */
             $id            = $object['_id'];
             $object['_id'] = $id->__toString();

             // we need a raw version - easier to updated and manipulates with JS
             $object['raw'] = MongoHelper::extractDocument($object);

             /** @var ExportDocument $docExport */
             $docExport  = new ExportDocument();

             // set the text key value -> default as php
             $docExport->setVar($object);
             $docExport->setParams([]);

             // always set 'json' as the default for this
             $text = $docExport->export($this->format);

             /** @var HighlightDocument $docHighlight */
             $docHighlight = new HighlightDocument();

             // set the data key
             $data = $docHighlight->highlight($documentsArray[0], $this->format, true);

             $object['text'] = $text;
             $object['data'] = $data;

             // set the 'can_delete' bool
             $object['can_delete']    = true;

             // set the 'can_modify' bool
             $object['can_modify']    = (isset($object['_id']));

             // set the 'can_duplicate' bool
             $object['can_duplicate'] = (isset($object['_id']));

             // set the 'can_add_field' bool
             $object['can_add_field'] = true;

             // set the 'can_refresh' bool
             $object['can_refresh']   = (isset($object['_id']));

             return $object;

        } catch (\Exception $e) {
            throw new DocumentObjectException($e->getMessage());
        }
    }

    /**
     * Used to confirm that a document has been updated
     *
     * @param string $id
     * @param array $result
     * @return array
     */
    private function setUpdateStatus(string $id, array $result): array
    {
        if ($id == $result['dropped'] && 1 == $result['ok']) {
            return array($id => 'success');
        }
        return array($id => 'failed');
    }

    /**
     * Used to confirm that a document has been deleted
     *
     * @param  string $id
     * @param  MongoDB\DeleteResult  $result
     * @return array
     */
    private function setDeleteStatus(string $id, MongoDB\DeleteResult $result): array
    {
        if (true == $result->isAcknowledged() && 1 == $result->getDeletedCount()) {
            return array($id => 'success');
        }
        return array($id => 'failed');
    }

    /**
     * DocumentController constructor.
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
     * Display a single document.
     *
     * URL:         /api/v1/document/{database}/{collection}/{document}
     * Method:      GET
     * Description: Fetches a document
     *
     * @param Request $request
     * @param $database
     * @param $collection
     * @param $document
     *
     * @return Response
     *
     * @throws DocumentObjectException
     */
    public function getDocument(Request $request, $database, $collection, $document): Response
    {
        $this->database   = $database;
        $this->collection = $collection;
        if (isset($database, $collection, $document)) {
            // get the document
            $doc = $this->getObject($database, $collection, $document);
            return response()->success('success', array('document' => $doc));
        }

        return response()->error('failed', array('message' => 'required parameters missing'));
    }

    /**
     * Creating a new MongoDB document
     *
     * URL:         /api/v1/document/create
     * Method:      POST
     * Description: Create a new document
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createDocument(Request $request): Response
    {
        $database   = $request->get('database', null);
        $collection = $request->get('collection', null);
        $document   = $request->get('document', null);
        $document   = trim($document, '"');

        try {
            // doc should arrive as JSON or ARRAY
            if (strpos($document, "array(") !== false) {
                // ToDo:  get rid of this ASAP:  try ->
                //file_put_contents('php://memory', $string);
                //$array = include 'php://memory';
                //print_r($array);
                eval("\$document = $document;");
            }

            if (!is_array($document)) {
                // remove windoes goobies
                $document = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $document);
                $document = json_decode(str_replace("'", "\"", $document), true);
            }

            if (count($document) > 0) {
                // get the collection
                $cursor = $this->mongo->connectClientCollection($database, $collection);

                // insert doc
                $result = $cursor->insertOne($document);

                // get the ObjectId and then the new object with extras intact
                $insertId = $result->getInsertedId();
                $arr = array(
                    '_id' => new MongoDB\BSON\ObjectId($insertId->__toString())
                );
                $doc = $this->getObject($database, $collection, $arr);

                return response()->success('success', array('document' => $doc));
            }
            return response()->error('failed', array('message' => 'document has errors'));

        } catch (DocumentObjectException $e) {
            return response()->error('failed', array('message' => $e->getMessage()));

        } catch (Exception $e) {
            return response()->error('failed', array('message' => $this->scrapeError($e->getMessage())));
        }
    }

    /**
     * Updating a MongoDB document
     *
     * URL:         /api/v1/document/create
     * Method:      POST
     * Description: Create a new document
     *
     * @param   Request     $request
     * @param   string      $id MongoDB ObjectId()
     *
     * @return  Response
     */
    public function updateDocument(Request $request, string $id): Response
    {
        try {
            // our values
            $database   = $request->get('database');
            $collection = $request->get('collection');
            /** @var $document We'll always be expecting a JSON document from the front-end */
            $document   = $request->get('document');
            $type       = $request->get('type', null);

            // decode the document as we may need an array
            $document = json_decode($document, true);

            // if type exists and matches, and we have a field and value create the correct MongoDB data type
            if ($type && in_array($type, $this->types)) {
                $field = $request->get('field', null);
                $value = $request->get('value', null);
                if ($field && $value) {
                    // ToDo: !! for now any 'mixed' type value will be JSON - the default format
                    $realValue = DocumentHelper::convertValue('admin', $type, $this->format, $value);
                    $document[ $field ] = $type == 'mixed' ? json_decode($realValue, true) : $realValue;
                }
                // ToDo: ? do we need to set and return an error in this case ?
            }

            // get the collection
            $collection = $this->mongo->connectClientCollection($database, $collection);

            // We need to build the object ID
            // ToDo: !! for now all of our updates will use the _id !!
            $id = array('_id' => new MongoDB\BSON\ObjectId($id));

            //
            $result = $collection->replaceOne($id, $document);

            // $doc = $this->getObject($database, $collection, $result->getUpsertedId() );
            // possible result values
            /*echo '<pre>'; var_dump($result->isAcknowledged()); echo '</pre>';
            echo '<pre>'; var_dump($result->getModifiedCount()); echo '</pre>';
            echo '<pre>'; var_dump($result->getUpsertedId()); echo '</pre>';
            echo '<pre>'; var_dump($result->getMatchedCount()); echo '</pre>';
            echo '<pre>'; var_dump($result->getUpsertedCount()); echo '</pre>';
            die;*/

            if (true == $result->isAcknowledged() && 1 == $result->getModifiedCount()) {
                return response()->success('success', array('document' => $document));

            } else {
                return response()->error('failed', array('message' => 'update failed'));
            }

        } catch (\Exception $e) {
            return response()->error('failed', array('message' => $this->scrapeError($e->getMessage())));
        }
    }

    /**
     * Duplicating a new MongoDB document
     *
     * URL:         /api/v1/document/duplicate
     * Method:      POST
     * Description: Create a new document
     *
     * @param   Request $request
     *
     * @return  Response
     */
    public function duplicateDocument(Request $request): Response
    {
        $database   = $request->get('database', null);
        $collection = $request->get('collection', null);
        $document   = $request->get('document', null);

        try {
            // doc should arrive as JSON
            $document = json_decode($document, true);
            if (count($document) >= 1) {
                // get the collection
                $cursor = $this->mongo->connectClientCollection($database, $collection);

                // insert doc
                $result     = $cursor->insertOne($document);

                // get the ObjectId and then the new object with extras intact
                $insertId = $result->getInsertedId();
                $arr = array(
                    '_id' => new MongoDB\BSON\ObjectId($insertId->__toString())
                );
                $doc = $this->getObject($database, $collection, $arr);

                return response()->success('success', array( 'document' => $doc ));
            }
            return response()->error('failed', array( 'message' => 'document has errors' ));

        } catch (DocumentObjectException $e) {
            return response()->error('failed', array( 'message' => $e->getMessage() ));

        } catch (\Exception $e) {
            return response()->error('failed', array( 'message' => $this->scrapeError($e->getMessage())));
        }
    }

    /**
     * Deleting (destroy) a MongoDB document
     *
     * URL:         /api/v1/document/delete
     * Method:      POST
     * Description: Delete the database matching the given name
     *
     * @param   Request $request
     * @param   $database
     * @param   $collection
     * @param   $oid
     * @return  Response
     */
    public function destroy(Request $request, $database, $collection, $oid)
    {
        try {
            // get the collection
            $collection = $this->mongo->connectClientCollection($database, $collection);

            try {
                // generate the OID object
                $arr = array(
                    '_id' => new MongoDB\BSON\ObjectId($oid)
                );

            } catch (\Exception $e) {
                if (false !== strpos($e->getMessage(), "Error parsing ObjectId string:")) {
                    $arr = array("_id" => $oid);
                }
            }

            // delete doc
            $status   = array();
            /** @var MongoDB\DeleteResult $result */
            $result   = $collection->deleteOne($arr);
            $status[] = $this->setDeleteStatus($oid, $result);

            return response()->success('success', array('status' => $status));

        } catch (\Exception $e) {
            return response()->error('failed', array( 'message' => $e->getMessage() ));
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
