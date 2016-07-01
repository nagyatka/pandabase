#PandaBase


## Installation
Install the latest version with

```bash
$ composer require nagyatka/pandabase
```

## Basic Usage

Run queries:

```bash
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
```

Create classes:
```bash

use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\TableDescriptor;
use PandaBase\Record\SimpleRecord;

class TestRecord extends SimpleRecord {

    /**
     * @var TableDescriptor
     */
    private static $tableDescriptor;

    /**
     * @param int $id
     * @param null $values
     */
    function __construct($id, $values = null)
    {
        TestRecord::$tableDescriptor = new TableDescriptor([
            TABLE_NAME  =>  "table1",
            TABLE_ID    =>  "id",
        ]);
        parent::__construct(TestRecord::$tableDescriptor, $id, $values);
    }

}

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

// Get a record from database with id=232
$record = new TestRecord(232);

```

## Usage
- [How to use ConnectionManager](PandaBase/Documentation/01-connectionmanager.md)


## License
PandaBase is licensed under the Apache 2.0 License
