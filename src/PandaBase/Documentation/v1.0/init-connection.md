# How to use ConnectionManager

### Get ConnectionManager instance

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

### Get result from default connection
```php
$mixedRecords = $connectionManager->getMixedRecords("SELECT * FROM table1");
```

### Get result from a specified connection
```php
$mixedRecords = $connectionManager->getMixedRecords("SELECT * FROM table1",[],"test_connection2");
```

