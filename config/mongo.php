<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      mongo.php 1001 6/8/20, 1:01 am  Gilbert Rehling $
 * @package      mongo.php
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
 * These are our default MONGO configuration parameters
 *
 * @see more details at http://rockmongo.com/wiki/configuration?lang=en_us
 *
 * The ['server'] config is repeated and stored for each new  Server connection created
 */

$index = 0;
return [
    "features" => [
        "log_query" => "on", // log queries
        "theme" => "default", // theme
        "plugins" => "off" // plugins
    ],
    "servers" => [
        $index => [
            "name"          => "Localhost", // mongo server name
            "host"          => "127.0.0.1", // mongo host
            "port"          => "27017", // mongo port
            "ssl"           => "false", // whether to require SSL / TLS for the connection
            "timeout"       => 0, // mongo connection timeout
            "auth"          => false, // enable mongo authentication?
            "control_auth"  => true, // enable control users, works only if mongo_auth=false
            "control_users" => [
                "admin" => "admin"  // one of control users ["USERNAME"]=PASSWORD, works only if mongo_auth=false
            ], // ToDo !! the control user is created during the initial setup
            "ui_only_dbs"   => "", // databases to display
            "ui_hide_dbs"   => "", // databases to hide
            "ui_hide_collections" => "", // collections to hides//
            "docs_native_order" => false, // whether to show documents by native order, default is by _id field
            "docs_render"   => "default" // document highlight render, can be "default" or "plain"
        ]
    ],
    "plugins" => [
        "plugin_name" => [ // this array only provides the plugins template
            "enabled" => 1 // 1 (true) 0 (false)
        ]
    ]
];

