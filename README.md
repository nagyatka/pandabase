# PandaBase

## Installation

```bash
$ composer require nagyatka/pandabase
```
We recommend that use version above v0.20.0, because of significant API and performance changes.

## How to use ConnectionManager
### Get ConnectionManager instance
You can reach the ConnectionManager singleton instance globally via `getInstance()` method
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
    "attributes"=>  [
        attributeName => value,
        ...
    ]                                   // Optional, PDO attributes
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
The `getConnection` method returns with the default connection if you leave the name parameter empty. The default connection
will be the firstly set connections.
```php
$connection = $connectionManager->getConnection();
```

### Get connection by name
```php
$connection = $connectionManager->getConnection("test_connection2");
```

### Set the default connection by name
```php
// Set the 'test_connection2' Connection instance as the default
$connectionManager->setDefault("test_connection2");

// Returns with the instance of 'test_connection2' if exists
$connection = $connectionManager->getConnection();
```

### Execute queries using ConnectionManager
```php

// Fetch a result row as an associative array
$queryResult = ConnectionManager::fetchAssoc("SELECT * FROM table1 WHERE table_id = :_id", [
    "_id" => 11
]); 

// Fetch result from default connection
$queryResult1 = ConnectionManager::fetchAll("SELECT * FROM table1");

// Fetch result from default connection with parameters
$queryResult2 = ConnectionManager::fetchAll("SELECT * FROM table1 WHERE store_date > :actual_date",[
    "actual_date" => date("Y-m-d H:i:s")
]);

// Fetch result from specified connection (without parameters)
$queryResult3 = ConnectionManager::fetchAll("SELECT * FROM table1",[],"test_connection2");

```


# How to use Connection

Connection is a PDO wrapper (all PDO function is callable) and provides a modified fetchAssoc and fetchAll methods for
better usability. Although the ConnectionManager instance provides wrapper function for Connection instance's function
so we recommend to use these wrapper function instead of calling them directly.

### Get connection
```php
$connection = $connectionManager->getConnection();
```

### Fetch a result row as an associative array
```php
$result = $connection->fetchAssoc("SELECT * FROM table1 WHERE id = :id",["id" => $id]);
```

### Returns with an array containing all of the result set rows as an associative array
```php
$result = $connection->fetchAll("SELECT * FROM table1",[]);
```

## Create classes based on database scheme

You can create classes based on tables of database. To achieve this, you have to only extend your classes from SimpleRecord or
HistoryableRecord and register them to the specified connection.

### SimpleRecord

#### Implement a SimpleRecord class

Assume that we have a MySQL table named as transactions and it has a primary key.

```mysql
CREATE TABLE `database_name`.`transactions` (
	`transaction_id` int(11) NOT NULL AUTO_INCREMENT,
	`transaction_value` int(11),
	`user_id` int(11),
	`store_date` datetime,
	PRIMARY KEY (`transaction_id`)
) ENGINE=`InnoDB` COMMENT='';
```
Implement Transaction class:
```php
class Transaction extends SimpleRecord {

}
```
In next step you have to add a Table object (this is a table descriptor class) to the specified Connection instance in
the following way when you initialize the connection: 
```php
$connectionManager->initializeConnection([
    "name"      =>  "test_connection",  // Connection's name.
    "driver"    =>  "mysql",            // Same as PDO parameter
    "dbname"    =>  "database_name",    // Same as PDO parameter
    "host"      =>  "127.0.0.1",        // Same as PDO parameter
    "user"      =>  "root",             // Same as PDO parameter
    "password"  =>  ""                  // Same as PDO parameter
    "attributes"=>  [
        attributeName => value,
        ...
    ],                                  // Optional, PDO attributes
    "tables"    =>  [
        Transaction::class  => new Table([
            Table::TABLE_NAME => "transactions",
            Table::TABLE_ID   => "transaction_id",
        ]),
        ...
    ]
]);
```
And that's all! Now you can create, update and delete records from the table:
```php
// Create a new empty record (if your table scheme allows it)
$emptyRecord = new Transaction();
// Create a new record with values
$newRecord = new Transaction([
            "transaction_value"     =>  5000,
            "user_id"               =>  1234,
            "store_date"            =>  date('Y-m-d H:i:s')
]);
// To create new records in table you have to call ConnectionManager's persist function
ConnectionManager::persist($emptyRecord);
ConnectionManager::persist($newRecord);

// An other option is to use persistAll function
ConnectionManager::persistAll([
    $emptyRecord,
    $newRecord
]);

// Now $emptyRecord and $newRecord have transaction_id attribute
echo $emptyRecord["transaction_id"]." ".$newRecord["transaction_id"]."\n";



// Load record
$transaction = new Transaction($transactionId);
echo $transation->get("store_date").": ".$transaction["transaction_value"]; // You can use object as an array

// Load multiple record from transaction table (get all transaction of an user)
$transactions = ConnectionManager::getInstanceRecords(
    Transaction::class,
    "SELECT * FROM transactions WHERE user_id = :user_id",
    [
        "user_id"   =>  1234
    ]
);



// Update record
$transaction = new Transaction($transactionId);
$transation->set("transaction_value",4900);
$transation["store_date"] = date('Y-m-d H:i:s'); //You can use object as an array
ConnectionManager::persist($transation);



// Remove record
$transaction = new Transaction($transactionId);
$transation->remove();
```

### HistoryableRecord

HistoryableRecord has the same features as SimpleRecord but it also storea the previous state of a record.

#### Implement a HistoryableRecord class

Assume that we have a MySQL table named as transactions and the table has the following columns (all of them required):
* sequence_id (PRIMARY KEY)
* id (record identifier, you can use it as ID in your code)
* record_status (0|1 -> inactive|active)
* history_from (datetime)
* history_to (datetime)

```mysql
CREATE TABLE `database_name`.`orders` (
    `order_sequence_id` int(11) NOT NULL AUTO_INCREMENT,
	`order_id` int(11),
	`record_status` int(1),
	`history_from` datetime,
	`history_to` datetime,
	`order_status` int(11),
	`user_id` int(11),
	`store_date` datetime,
	PRIMARY KEY (`order_sequence_id`)
) ENGINE=`InnoDB` COMMENT='';
```
Implement Order class:
```php
class Order extends HistoryableRecord {
    const Pending       = 0;	
    const Processing    = 1;	
    const Completed	    = 2;
    const Declined      = 3;	
    const Cancelled     = 4;
    
    /**
     * Constructor
     */
    public function __construct($parameters) {
        $parameters["order_status"] = Order::Pending;
        parent::__construct($parameters);
    }
    
    ...
}
```
In next step you have to add a Table object (this is a table descriptor class) to the specified Connection instance in
the following way when you initialize the connection: 
```php
$connectionManager->initializeConnection([
    "name"      =>  "test_connection",  // Connection's name.
    "driver"    =>  "mysql",            // Same as PDO parameter
    "dbname"    =>  "database_name",    // Same as PDO parameter
    "host"      =>  "127.0.0.1",        // Same as PDO parameter
    "user"      =>  "root",             // Same as PDO parameter
    "password"  =>  ""                  // Same as PDO parameter
    "attributes"=>  [
        attributeName => value,
        ...
    ],                                  // Optional, PDO attributes
    "tables"    =>  [
        Transaction::class  => new Table([
            Table::TABLE_NAME => "orders",
            Table::TABLE_ID   => "order_id",
            Table::TABLE_SEQ_ID => ""
        ]),
        ...
    ]
]);
```
Now you can use HistoryableRecord as a SimpleRecord, but you can get also historical information about the instance:
```php
    $order = new Order($order_id);
    
    // Get full history
    $orderHistory = $order->getHistory();
    
    // You can also specify a date interval
    $orderHistory = $order->getHistoryBetweenDates("2017-01-05","2017-01-08");
```


#### TODO:

LazyAttributes
AccessManagement


## License
PandaBase is licensed under the Apache 2.0 License
