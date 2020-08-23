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

use App\Helpers\DocumentHelper;
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

/**
 *  Defined models
 */
use App\Models\User;

/**
 * Vendors
 */
use MongoDB\BSON\Unserializable;
use MongoDB;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Exceptions
 */
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
     * @var     array       $types          these are the data types we nat to treat nicely
     */
    private $types = ['double', 'integer', 'long', 'mixed'];

    /**
     * Returns a document from the given collection
     *
     * @param string $db string DB Name
     * @param string $collection string Collection name
     * @param array $document
     * @return  array
     */
    private function getObject($db, $collection, $document)
    {
        try {
             // no errors this way
             $collection = $this->client->$db->selectCollection( $collection );
             $object     = $collection->findOne( $document );

             $documentsArray = [];
             $fields = [];
             MongoHelper::prepareDocument( $object, $documentsArray,$fields );

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
             $data = $docHighlight->highlight( $documentsArray[0], $this->format, true);

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

             return    $object;
        }
        catch (\Exception $e) {
            throw new DocumentObjectException($e->getMessage());
        }
    }

    /**
     * Used to confirm that a document has been updated
     *
     * @param string $name
     * @param array $result
     * @return array
     */
    private function setUpdatetatus(string $id, array $result)
    {
        if ($result['dropped'] == $id && $result['ok'] == 1) {
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
    private function setDeleteStatus(string $id, MongoDB\DeleteResult $result)
    {
        if ($result->getDeletedCount() == 1 && $result->isAcknowledged() == true) {
            return array($id => 'success');
        }
        return array($id => 'failed');
    }

    /**
     * DocumentController constructor.
     */
    public function __construct()
    {
        /** @var User $user */
        $user = auth()->guard('api')->user();
        $this->mongo = new Mongo($user);
        if ($this->mongo->checkConfig()) {
            $this->mongo->connectClient();
            $this->client = $this->mongo->getClient();
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
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws DocumentObjectException
     */
    public function getDocument(Request $request, $database, $collection, $document)
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
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws DocumentObjectException
     */
    public function createDocument(Request $request)
    {
        $database   = $request->get('database', null);
        $collection = $request->get('collection', null);
        $document   = $request->get('document', null);

        try {
            // doc should arrive as JSON
            $document = json_decode( $document, true);
            if (count($document) >= 1) {
                // get the collection
                $cursor = $this->mongo->connectClientCollection( $database, $collection );

                // insert doc
                $result     = $cursor->insertOne( $document );

                // get the ObjectId and then the new object with extras intact
                $insertId = $result->getInsertedId();
                $arr = array(
                    '_id' => new MongoDB\BSON\ObjectId( $insertId->__toString() )
                );
                $doc = $this->getObject( $database, $collection, $arr );

                return response()->success('success', array( 'document' => $doc ));
            }
            return response()->success('failed', array( 'message' => 'document has errors' ));

        }
        catch( DocumentObjectException $e) {
            return response()->success('failed', array( 'message' => $e->getMessage() ));
        }
        catch(\Exception $e) {
            return response()->success('failed', array( 'message' => $e->getMessage() ));
        }
    }

    /**
     * Creating a new MongoDB document
     *
     * URL:         /api/v1/document/create
     * Method:      POST
     * Description: Create a new document
     *
     * @param   Request     $request
     * @param   string      $id MongoDB ObjectId()
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function updateDocument(Request $request, $id)
    {
        try {
            // our values
            $database   = $request->get('database');
            $collection = $request->get('collection');
            /** @var $document JSON  We'll always be expecting a JSON document from the front-end */
            $document   = $request->get('document');
            $type       = $request->get('type', null);

            // decode the document as we may need an array
            $document = json_decode($document, true);

            // if type exists and matches, and we have field and value create the correct MongoDB data type
            if ($type && in_array($type, $this->types)) {
                $field = $request->get( 'field', null );
                $value = $request->get( 'value', null );
                if ($field && $value) {

                    // ToDo: !! for now any 'mixed' type value will be JSON - the default format
                    $realValue = DocumentHelper::convertValue( 'admin', $type, $this->format, $value );
                    $document[ $field ] = $type == 'mixed' ? json_decode($realValue, true) : $realValue;
                }
                // ToDo: ? do we need to set and return an errro in this case ?
            }

            // get the collection
            $collection = $this->mongo->connectClientCollection( $database, $collection );

            // We need to build the object ID
            // ToDo: !! for now all of our updates will use the _id !!
            $id = array('_id' => new MongoDB\BSON\ObjectId($id));

            //
            $result     = $collection->replaceOne( $id, $document );

            // $doc = $this->getObject( $database, $collection, $result->getUpsertedId() );
            // possible result values
            /*echo '<pre>'; var_dump($result->isAcknowledged()); echo '</pre>';
            echo '<pre>'; var_dump($result->getModifiedCount()); echo '</pre>';
            echo '<pre>'; var_dump($result->getUpsertedId()); echo '</pre>';
            echo '<pre>'; var_dump($result->getMatchedCount()); echo '</pre>';
            echo '<pre>'; var_dump($result->getUpsertedCount()); echo '</pre>';
            die;*/

            if ($result->isAcknowledged() == true && $result->getModifiedCount() == 1) {
                return response()->success('success', array( 'document' => $document ));

            } else {
                return response()->success('failed', array( 'message' => 'update failed' ));
            }
        }
        catch (\Exception $e) {
            return response()->success('failed', array( 'message' => $e->getMessage() ));
        }
    }

    /**
     * Duplicatinga new MongoDB document
     *
     * URL:         /api/v1/document/duplicate
     * Method:      POST
     * Description: Create a new document
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicateDocument(Request $request)
    {
        $database   = $request->get('database', null);
        $collection = $request->get('collection', null);
        $document   = $request->get('document', null);

        try {
            // doc should arrive as JSON
            $document = json_decode( $document, true);
            if (count($document) >= 1) {
                // get the collection
                $cursor = $this->mongo->connectClientCollection( $database, $collection );

                // insert doc
                $result     = $cursor->insertOne( $document );

                // get the ObjectId and then the new object with extras intact
                $insertId = $result->getInsertedId();
                $arr = array(
                    '_id' => new MongoDB\BSON\ObjectId( $insertId->__toString() )
                );
                $doc      = $this->getObject( $database, $collection, $arr );

                return response()->success('success', array( 'document' => $doc ));
            }
            return response()->success('failed', array( 'message' => 'document has errors' ));

        }
        catch( DocumentObjectException $e) {
            return response()->success('failed', array( 'message' => $e->getMessage() ));
        }
        catch(\Exception $e) {
            return response()->success('failed', array( 'message' => $e->getMessage() ));
        }
    }

    /**
     * Deleting (destroy) a MongoDB document
     *
     * URL:         /api/v1/document/delete
     * Method:      POST
     * Description: Delete the database matching the given name
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $database, $collection, $oid)
    {
        try {
            // get the collection
            $collection = $this->mongo->connectClientCollection( $database, $collection );

            // generate the OID object
            $arr = array(
                '_id' => new MongoDB\BSON\ObjectId( $oid )
            );

            // delete doc
            $status   = array();
            /** @var MongoDB\DeleteResult $result */
            $result   = $collection->deleteOne( $arr );
            $status[] = $this->setDeleteStatus( $oid, $result);

            return response()->success('success', array('status' => $status ));
        }
        catch(\Exception $e) {
            return response()->success('failed', array( 'message' => $e->getMessage() ));
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
