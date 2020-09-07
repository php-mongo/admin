<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      api.php 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      api.php
 * @subpackage   Id
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See COPYRIGHT.php for copyright notices and further details.
 */

/**
 * Default use statements
 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function() {

    /*
    |-------------------------------------------------------------------------------
    | Get User
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user
    | Controller:     Api\UsersController@getUser
    | Method:         GET
    | Description:    Gets the authenticated user
    */
    Route::get('/user', 'Api\UsersController@getUser');

    /*
    |-------------------------------------------------------------------------------
    | Login (POST) a User's Credentials for authentication
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user/login
    | Controller:     Auth\LoginController@login
    | Method:         POST
    | Description:    Login a user using an AJAX post
    */
    Route::post('/user/login', 'Auth\LoginController@login');

    /*
    |-------------------------------------------------------------------------------
    | Check (GET) an email address
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user/email/{email}
    | Controller:     API\UsersController@checkEmail;
    | Method:         GET
    | Description:    Checks application DB for an email during the registration process
    */
    Route::get('/user/email/{email}', 'Api\UsersController@checkEmail');

    /*
    |-------------------------------------------------------------------------------
    | Get (GET) the users location data
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user/location
    | Controller:     Api\UsersController@getUserLocation
    | Method:         GET
    | Description:    Uses the IpInfo service to get the visitors location data
    */
    Route::get('/user/location', 'Api\UsersController@getUserLocation');

    /*
    |-------------------------------------------------------------------------------
    | Get (GET) the users states for their country (only AU and USA available)
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user/states/{country}
    | Controller:     Api\UsersController@getUserStates
    | Method:         GET
    | Description:    Get the states for the user's country
    */
    Route::get('/user/states/{country}', 'Api\UsersController@getUserStates');

});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function() {

    /*
    |-------------------------------------------------------------------------------
    | Updates a User's Profile
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user
    | Controller:     Api\UsersController@putUpdateUser
    | Method:         PUT
    | Description:    Updates the authenticated user's profile
    */
    Route::put('/user', 'Api\UsersController@putUpdateUser');

    /*
    |-------------------------------------------------------------------------------
    | Logout (GET) a User
    |-------------------------------------------------------------------------------
    | URL:            /api/v1/user/logout/{uid}
    | Controller:     Auth\LoginController@logout
    | Method:         GET
    | Description:    Logout a user using an AJAX post
    */
    Route::get('/user/logout/{uid}', 'Auth\LoginController@logout');

    /*
    * -------------------------------------------------------
    * Get all Dbs (private route) Summary only for Left NAV
    * -------------------------------------------------------
    * URL:         /api/v1/dbs
    * Controller:  API/DbsController@getDbs
    * Method:      GET
    * Description: Gets all the dbs (databases) summary
    */
    Route::get('/dbs', 'Api\DbsController@getDbs');

    /*
    * -------------------------------------------------------
    * Get all Servers configurations (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/servers
    * Controller:  API/ServersController@index
    * Method:      GET
    * Description: Gets all of the current Servers for current user
    */
    Route::get('/servers', 'Api\ServersController@index');
    // Todo: resources are not returning the format we need (as yet)
    /*Route::get('/servers', function() {
        return ServerResource::collection(Server::all());
    });*/

    /*
    * -------------------------------------------------------
    * Create a new Server configurations (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/servers
    * Controller:  API/ServersController@index
    * Method:      GET
    * Description: Gets all of the current Servers for current user
    */
    Route::post('/servers', 'Api\ServersController@store');

    /*
    * -------------------------------------------------------
    * Activate a Server configuration (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/servers
    * Controller:  API/ServersController@activate
    * Method:      GET
    * Description: Activates a server configuration
    */
    Route::post('/servers/activate', 'Api\ServersController@activate');

    /*
    * -------------------------------------------------------
    * Delete a Server configuration (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/servers/{server}
    * Controller:  API/ServersController@destroy
    * Method:      GET
    * Description: Activates a server configuration
    */
    Route::delete('/servers/{id}', 'Api\ServersController@destroy');

    /*
    * -------------------------------------------------------
    * Get a Server's details (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/server
    * Controller:  API/ServerController@getServer
    * Method:      GET
    * Description: Gets all of the current Server information
    */
    Route::get('/server', 'Api\ServerController@getServer');

    /*
    * -------------------------------------------------------
    * Get all Databases (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/databases
    * Controller:  API/DatabasesController@getDatabases
    * Method:      GET
    * Description: Gets all the databases
    */
    Route::get('/databases', 'Api\DatabasesController@getDatabases');

    /*
    * -------------------------------------------------------
    * Get one Database (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/databases/{name}
    * Controller:  API/DatabasesController@getDatabase
    * Method:      GET
    * Description: Gets one databases with all associated data
    */
    Route::get('/databases/{name}', 'Api\DatabasesController@getDatabase');

    /*
    * -------------------------------------------------------
    * Create new Database (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/databases/create
    * Controller:  API/DatabasesController@createDatabase
    * Method:      POST
    * Description: Creates a new databases and returns the database data
    */
    Route::post('/databases/create', 'Api\DatabasesController@createDatabase');

    /*
    * -------------------------------------------------------
    * Update a Database (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/databases/update
    * Controller:  API/DatabasesController@updatDeatabase
    * Method:      PUT
    * Description: Updates a databases and returns the database data
    */
    Route::put('/databases/update', 'Api\DatabasesController@updateDatabase');

    /*
    * -------------------------------------------------------
    * Delete a Database (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/databases/delete
    * Controller:  API/DatabasesController@deleteDatabase
    * Method:      POST
    * Description: Deletes a new databases and returns s success status
    */
    Route::post('/databases/delete', 'Api\DatabasesController@deleteDatabase');

    /*
    * -------------------------------------------------------
    * Get one Collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/{database}/{collection}
    * Controller:  API/CollectionController@getCollection
    * Method:      GET
    * Description: Gets one collection with all associated data
    */
    Route::get('/collection/{database}/{collection}', 'Api\CollectionController@getCollection');

    /*
    * -------------------------------------------------------
    * Create new Collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/create
    * Controller:  API/CollectionController@createCollection
    * Method:      POST
    * Description: Creates a new collection and returns the collection data
    */
    Route::post('/collection/create', 'Api\CollectionController@createCollection');

    /*
    * -------------------------------------------------------
    * Update a Collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/update
    * Controller:  API/CollectionController@updatCollection
    * Method:      PUT
    * Description: Updates a collection and returns the collection data
    */
    Route::put('/collection/update', 'Api\CollectionController@updateCollection');

    /*
    * -------------------------------------------------------
    * Query a Collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/query
    * Controller:  API/CollectionController@queryCollection
    * Method:      POST
    * Description: Queries a collection and returns matching documents
    */
    Route::post('/collection/query', 'Api\CollectionController@queryCollection');

    /*
    * -------------------------------------------------------
    * Get all query logs for a database.collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/query/logs/{database}/{collection}
    * Controller:  API/CollectionController@getQueryLogs
    * Method:      GET
    * Description: Gets all query logs for given database & collection
    */
    Route::get('/collection/query/logs/{database}/{collection}', 'Api\CollectionController@getQueryLogs');

    /*
    * -------------------------------------------------------
    * Get a query explanation (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/query/explain
    * Controller:  API/CollectionController@getQueryExplain
    * Method:      POST
    * Description: Processes a query explain command and return the analysis
    */
    Route::post('/collection/query/explain', 'Api\CollectionController@getQueryExplain');

    /*
    * -------------------------------------------------------
    * Delete a Collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/delete
    * Controller:  API/CollectionController@deleteCollection
    * Method:      POST
    * Description: Deletes a collection and returns s success status
    */
    Route::post('/collection/delete', 'Api\CollectionController@deleteCollection');

    /*
    * -------------------------------------------------------
    * Clear a Collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/clear
    * Controller:  API/CollectionController@clearCollection
    * Method:      POST
    * Description: Clear all documents from a collection and returns s success status
    */
    Route::post('/collection/clear', 'Api\CollectionController@clearCollection');

    /*
    * -------------------------------------------------------
    * Export one or more Collections (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/export
    * Controller:  API/CollectionController@exportCollection
    * Method:      POST
    * Description: Export one or more collections from the provided database
    */
    Route::post('/collection/export', 'Api\CollectionController@exportCollection');

    /*
    * -------------------------------------------------------
    * Import one or more Collections (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/import
    * Controller:  API/CollectionController@importCollection
    * Method:      POST
    * Description: Import one or more collections into the provided database
    */
    Route::post('/collection/import', 'Api\CollectionController@importCollection');

    /*
    * -------------------------------------------------------
    * Update one Collection properties (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/properties
    * Controller:  API/CollectionController@propertiesCollection
    * Method:      POST
    * Description: Update one collection properties
    */
    Route::post('/collection/properties', 'Api\CollectionController@propertiesCollection');

    /*
    * -------------------------------------------------------
    * Add an Index to a Collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/index
    * Controller:  API/CollectionController@indexCollection
    * Method:      POST
    * Description: Add a new Index to a collection (standard or 2d)
    */
    Route::post('/collection/index', 'Api\CollectionController@indexCollection');

    /*
    * -------------------------------------------------------
    * Rename a Collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/rename
    * Controller:  API/CollectionController@renameCollection
    * Method:      POST
    * Description: Rename a single collection
    */
    Route::post('/collection/rename', 'Api\CollectionController@renameCollection');

    /*
    * -------------------------------------------------------
    * Duplicate a Collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/duplicate
    * Controller:  API/CollectionController@duplicateCollection
    * Method:      POST
    * Description: Duplicate a collection (options include: overwrite exiting, include indexes)
    */
    Route::post('/collection/duplicate', 'Api\CollectionController@duplicateCollection');

    /*
    * -------------------------------------------------------
    * Validate a Collection (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/validate
    * Controller:  API/CollectionController@validateCollection
    * Method:      GET
    * Description: Validate a collection and return the result
    */
    Route::get('/collection/validate/{database}/{collection}', 'Api\CollectionController@validateCollection');

    /*
    * -------------------------------------------------------
    * Create a Document (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/document/create
    * Controller:  API/DocumentController@createDocument
    * Method:      POST
    * Description: Creates a document and returns the document data
    */
    Route::post('/document/create', 'Api\DocumentController@createDocument');

    /*
    * -------------------------------------------------------
    * Duplicate a Document (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/document/duplicate
    * Controller:  API/DocumentController@duplicateDocument
    * Method:      POST
    * Description: Duplicate a document and returns the document data
    */
    Route::post('/document/duplicate', 'Api\DocumentController@duplicateDocument');

    /*
    * -------------------------------------------------------
    * Update a Document (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/document/update
    * Controller:  API/DocumentController@updateDocument
    * Method:      PUT
    * Description: Updates a document and returns the document data
    */
    Route::put('/document/update/{id}', 'Api\DocumentController@updateDocument');

    /*
    * -------------------------------------------------------
    * Delete a Document (private route)
    * -------------------------------------------------------
    * URL:         /api/v1/document/{database}/{collection}/{id}
    * Controller:  API/DocumentController@destroy
    * Method:      DELETE
    * Description: Delete a document and returns the status
    */
    Route::delete('/document/{database}/{collection}/{id}', 'Api\DocumentController@destroy');
});
