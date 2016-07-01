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


// Get mixed result
$mixedRecords = $connectionManager->getMixedRecords("
  SELECT table1.val 
  FROM table1 
  JOIN table2 ON table1.id = table2.id
  WHERE table1.val = :test_value
",[
    "test_value"    =>  "foo"
]);

// List the result
$mixedRecords->foreachRecords(function (\PandaBase\Record\DatabaseRecord $record) {
    echo $record->get("val");
});