<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      DbsController.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   DbsController.php
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
    Defines a namespace for the controller.
*/
namespace App\Http\Controllers\Api;

/*
 *   Defines the requests used by the controller.
 */
use Illuminate\Http\Request;

/*
 *   Defined controllers used by the controller
 */
use App\Http\Controllers\Controller;

/*
 * Used models
 */
use App\Models\Db;

/*
 * Used libraries
 */
use MongoDB;


/**
 * Class DbsController
 *
 * @package App\Http\Controllers\Api
 */
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
        return response()->json( array('dbs' => $dbs));
    }
}
