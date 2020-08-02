<?php

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
    * Update a Collection(private route)
    * -------------------------------------------------------
    * URL:         /api/v1/collection/update
    * Controller:  API/CollectionController@updatCollection
    * Method:      PUT
    * Description: Updates a collection and returns the collection data
    */
    Route::put('/collection/update', 'Api\CollectionController@updateCollection');

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
    * Get a single Ads (public route)
    * -------------------------------------------------------
    * URL:         /api/v1/latest/{slug}
    * Controller:  Api/PublicController@getAd
    * Method:      GET
    * Description: Gets a single ad
    */
    //Route::get('/latest/{slug}', 'Api\PublicController@getAd');

    /*
    * -------------------------------------------------------
    * Get a single Ads - usually a new post (public route)
    * -------------------------------------------------------
    * URL:         /api/v1/latest/ad/{id}
    * Controller:  Api/PublicController@getNewAd
    * Method:      GET
    * Description: Gets a single ad
    */
    //Route::get('/latest/ad/{id}', 'Api\PublicController@getNewAd');

});
