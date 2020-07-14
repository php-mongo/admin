<?php
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

