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

Create a class based on database scheme using only inheritance:
```bash

use PandaBase\Connection\ConnectionManager;
use PandaBase\Connection\TableDescriptor;
use PandaBase\Record\SimpleRecord;

class TestRecord extends SimpleRecord {

    function __construct($id, $values = null) {
        parent::__construct(
            new TableDescriptor(
                [
                    TABLE_NAME  =>  "table1",
                    TABLE_ID    =>  "table_id"
                ]),
            $id,$values);
    }

}

// Get a record from database with id=232
$record = new TestRecord(232);

echo $record->get("col1")

```

## Usage
- [How to use ConnectionManager](PandaBase/Documentation/01-connectionmanager.md)


## License
PandaBase is licensed under the Apache 2.0 License
