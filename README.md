#PandaBase

## Installation
Install the latest version with

```bash
$ composer require nagyatka/pandabase
```
We recommend that use version above v0.20.0.

## How to use ConnectionManager

### Get ConnectionManager instance
You can reach the ConnectionManager instance globally via `getInstance()` method
```php
$connectionManager = ConnectionManager::getInstance();
```

### Add connection to manager object
You can easily set a new database connection in Pandabase.
```php
$connectionManager->initializeConnection([
    "name"      =>  "test_connection",  // Connection's name.
    "driver"    =>  "mysql",            // Same as PDO parameter
    "dbname"    =>  "test_dbname",      // Same as PDO parameter
    "host"      =>  "127.0.0.1",        // Same as PDO parameter
    "user"      =>  "root",             // Same as PDO parameter
    "password"  =>  ""                  // Same as PDO parameter
    "attributes"=>  [...]               // Optional, PDO attributes
]);
```


### Add more connection to manager object
ConnectionManager is able to handle more connection at time. The connections can be distinguished via the name parameter,
for example you can use `"test_connection1"` and `"test_connection2"` in the following example:
```php
$connectionManager->initializeConnections(
    [
        [
            "name"      =>  "test_connection1", // Connection's name.
            "driver"    =>  "mysql",            // Same as PDO parameter
            "dbname"    =>  "test_dbname1",     // Same as PDO parameter
            "host"      =>  "127.0.0.1",        // Same as PDO parameter
            "user"      =>  "root",             // Same as PDO parameter
            "password"  =>  ""                  // Same as PDO parameter
        ],
        [
            "name"      =>  "test_connection2", // Connection's name.
            "driver"    =>  "mysql",            // Same as PDO parameter
            "dbname"    =>  "test_dbname2",     // Same as PDO parameter
            "host"      =>  "127.0.0.1",        // Same as PDO parameter
            "user"      =>  "root",             // Same as PDO parameter
            "password"  =>  ""                  // Same as PDO parameter
        ],

    ]
);
```

### Get connection
The `getConnection` method returns with the default connection if you leave the name parameter empty.
```php
$connection = $connectionManager->getConnection();
```

### Get connection by name
```php
$connection = $connectionManager->getConnection("test_connection2");
```

### Set the default connection by name
```php
$connection = $connectionManager->setDefault("test_connection2");
$connection = $connectionManager->getConnection(); //returns with test_connection2
```

### Fetch result from connection
```php
// Fetch result from default connection
$queryResult1 = ConnectionManager::getQueryResult("SELECT * FROM table1");

// Fetch result from default connection with parameters
$queryResult2 = ConnectionManager::getQueryResult("SELECT * FROM table1 WHERE store_date > :actual_date",[
    "actual_date" => date("Y-m-d H:i:s")
]);

// Fetch result from specified connection
$queryResult3 = ConnectionManager::getQueryResult("SELECT * FROM table1",[],"test_connection2");

```


# How to use Connection

Connection is a PDO wrapper (all PDO function is callable) and provides a modified fetchAssoc and fetchAll methods.

### Get connection
```php
$connection = $connectionManager->getConnection();
```

### Fetch a result row as an associative array
```php
$result = $connection->fetchAssoc("SELECT * FROM table1 WHERE id = :id",["id" => $id]);
```

### Returns an array containing all of the result set rows as an associative array
```php
$result = $connection->fetchAll("SELECT * FROM table1",[]);
```

### TODO


## License
PandaBase is licensed under the Apache 2.0 License
