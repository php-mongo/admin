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

use App\Models\Db;

use MongoDB;


class DbsController extends Controller
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
     * @return array
     */
    private function getAllDbs()
    {
        $arr   = [];
        $index = 0;
        foreach ($this->client->listDatabases() as $db) {
            $collections = $this->getCollections($db->getName());
            $arr[] = array("id" => $index, "db" => $db->__debugInfo(), "collections" => $collections);
            $index++;
        }
        return $arr;
    }

    /**
     * @param $db string DB Name
     * @return array
     */
    private function getCollections($db)
    {
        $arr      = [];
        $index    = 0;
        $database = (new MongoDB\Client)->$db;
        foreach ($database->listCollections() as $collection) {
            $arr[] = array("id" => $index, "collection" => $collection->__debugInfo());
            $index++;
        }
        return $arr;
    }

    /**
     * DbsController constructor.
     */
    public function __construct()
    {
        $this->client = new MongoDB\Client;
    }

    /**
     * Display a listing of all dbs.
     *
     * URL:         /api/v1/dbs
     * Method:      GET
     * Description: Fetches all dbs in alphanumeric order
     *
     * @param Request $request
     * @param null $id
     * @param null $slug
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDbs()
    {
        // get the dbs
        $dbs = $this->getAllDbs();
    //     echo '<pre>'; var_dump($dbs); echo '</pre>'; die;
        return response()->json( array('dbs' => $dbs));
    }
}
