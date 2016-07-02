#PandaBase

Documentation is under development.

## Installation
Install the latest version with

```bash
$ composer require nagyatka/pandabase
```

## Basic Usage

Run queries:

```php
use PandaBase\Connection\ConnectionManager;

// Get the manager instance
$connectionManager = ConnectionManager::getInstance();

// Get mixed result
$mixedRecords = $connectionManager->getMixedRecords("SELECT * FROM table1");

// List the result
$mixedRecords->foreachRecords(function (DatabaseRecord $record) {
    echo $record->get("column_1").": ".$record->get("column_2")."\n";
});
```

Create a class based on database scheme using only inheritance:
```php

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

## Detailed Usage
- [Principles](src/PandaBase/Documentation/v1.0/principles.md)
- [How to use ConnectionManager](src/PandaBase/Documentation/v1.0/init-connection.md)
- [Create classes based on database scheme](src/PandaBase/Documentation/v1.0/create-classes.md)
- [How to use the QueryBuilder](src/PandaBase/Documentation/v1.0/query-builder.md)
- [How to use the DatabaseContainer](src/PandaBase/Documentation/v1.0/database-container.md)


## License
PandaBase is licensed under the Apache 2.0 License
