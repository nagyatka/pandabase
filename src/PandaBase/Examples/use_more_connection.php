<?php

use PandaBase\Connection\ConnectionManager;

// Get the manager instance
$connectionManager = ConnectionManager::getInstance();

// Add a connection to manager object
$connectionManager->initializeConnection([
    "name"      =>  "test_connection",  // Connection's name. You can use it for referring when you use more parallel connection
    "driver"    =>  "mysql",            // Same as PDO parameter
    "dbname"    =>  "test_dbname",      // Same as PDO parameter
    "host"      =>  "127.0.0.1",        // Same as PDO parameter
    "user"      =>  "root",             // Same as PDO parameter
    "password"  =>  ""                  // Same as PDO parameter
]);

// Add more connection to manager object
$connectionManager->initializeConnection(
    [
        [
            "name"      =>  "test_connection1", // Connection's name. You can use it for referring when you use more parallel connection
            "driver"    =>  "mysql",            // Same as PDO parameter
            "dbname"    =>  "test_dbname1",     // Same as PDO parameter
            "host"      =>  "127.0.0.1",        // Same as PDO parameter
            "user"      =>  "root",             // Same as PDO parameter
            "password"  =>  ""                  // Same as PDO parameter
        ],
        [
            "name"      =>  "test_connection2", // Connection's name. You can use it for referring when you use more parallel connection
            "driver"    =>  "mysql",            // Same as PDO parameter
            "dbname"    =>  "test_dbname2",     // Same as PDO parameter
            "host"      =>  "127.0.0.1",        // Same as PDO parameter
            "user"      =>  "root",             // Same as PDO parameter
            "password"  =>  ""                  // Same as PDO parameter
        ],

    ]
);