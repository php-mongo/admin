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
     * Returns a document from the given collection
     *
     * @param   string  $db             string DB Name
     * @param   string  $collection     string Collection name
     * @return  array
     */
    private function getObject($db, $collection, $document)
    {
        // no errors this way
        $arr = array(
            "objects" => [],
            "count" => 0
        );
        $cursor   = $this->client->$db->selectCollection($collection);
        $object   = $cursor->find( $document );
        return    $object->toArray();
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
     * @param string $name
     * @param array $result
     * @return array
     */
    private function setDeleteStatus(string $id, array $result)
    {
        if ($result['dropped'] == $id && $result['ok'] == 1) {
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
     * @return \Illuminate\Http\JsonResponse
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
     * @param EditCollectionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDocument(Request $request, $database, $collection)
    {
        /*$data = $request->validated();
        $name       = $data['name'];
        $database   = $data['database'];
        $collection = $data['name'];
        $capped     = $data['capped'];
        $count      = $data['count'];
        $size       = $data['size'];*/

        $documemnt = $request->get('document');

        // get the collection
        $collection = $this->mongo->connectClientCollection( $database, $collection );

        // insert doc
        $result     = $collection->insertOne( $document );
        $doc        = $this->getObject( $database, $collection, $result->getInsertedId() );

        return response()->success('success', array( 'document' => $doc ));
    }

    /**
     * Creating a new MongoDB document
     *
     * URL:         /api/v1/document/create
     * Method:      POST
     * Description: Create a new document
     *
     * @param EditCollectionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDocument(Request $request, $id)
    {
        try {
            // our values
            $database   = $request->get('database');
            $collection = $request->get('collection');
            $document   = $request->get('document');

            // decode the document as we need an array
            $document   = json_decode($document, true);

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
     * Deleting (destroy) a MongoDB document
     *
     * URL:         /api/v1/document/delete
     * Method:      POST
     * Description: Delete the database matching the given name
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $database    = $request->get('database');
        $collections = $request->get('collection', false);

        // get the collection
        $collection = $this->mongo->connectClientCollection( $database, $collection );

        // delete doc
        $status   = array();
        $result   = $collection->deleteOne([ '_id' => $id ]);
        $status[] = $this->setDeleteStatus( $id, $result->getArrayCopy());

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
