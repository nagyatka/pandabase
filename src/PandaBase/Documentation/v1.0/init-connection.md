# How to use ConnectionManager

### Get ConnectionManager instance
You can reach the ConnectionManager instance globally via `getInstance()` method
```php
$connectionManager = ConnectionManager::getInstance();
```

### Add connection to manager object
```php
$connectionManager->initializeConnection([
    "name"      =>  "test_connection",  // Connection's name. You can use it for referring when you use more parallel connection
    "driver"    =>  "mysql",            // Same as PDO parameter
    "dbname"    =>  "test_dbname",      // Same as PDO parameter
    "host"      =>  "127.0.0.1",        // Same as PDO parameter
    "user"      =>  "root",             // Same as PDO parameter
    "password"  =>  ""                  // Same as PDO parameter
]);
```

### Add more connection to manager object
```php
$connectionManager->initializeConnections(
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
```

### Get connection
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
$connection = $connectionManager->getConnection(); //test_connection2
```

### Fetch result from default connection as MixedRecords
```php
$mixedRecords = $connectionManager->getMixedRecords("SELECT * FROM table1");
```

### Fetch result from a specified connection as MixedRecords
```php
$mixedRecords = $connectionManager->getMixedRecords("SELECT * FROM table1",[],"test_connection2");
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