# Create classes based on database scheme

You can create classes based on tables of database. To do this, you have to only extend your classes from SimpleRecord or
HistoryableRecord.

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
In next step you have to add a TableDescriptor to class which contains the name of the database and name of the primary key and
you must overwrite the parent's constructor.
```php
class Transaction extends SimpleRecord {
    function __construct($id, $values = null)
    {
        $tableDescriptor = new TableDescriptor([
            TABLE_NAME  =>  "transactions",
            TABLE_ID    =>  "transaction_id"
        ]);
        parent::__construct($tableDescriptor,$id,$values);
    }
}
```
That's all!

#### Usage of a class inherited from SimpleRecord

##### Create new record
```php
$newRecord = new Transaction(CREATE_INSTANCE,[
            "transaction_value"     =>  5000,
            "user_id"               =>  1234,
            "store_date"            =>  date('Y-m-d H:i:s')
]);
ConnectionManager->getInstance()->persist($newRecord);
```

##### Load record from database
```php
$aTransaction = new Transaction($transactionId);
echo $aTransation->get("store_date").": ".$aTransaction["transaction_value"]; //You can also use object as an array
```

##### Edit record from database
```php
$aTransaction = new Transaction($transactionId);
$aTransation->set("transaction_value",4900);
$aTransation["store_date"] = date('Y-m-d H:i:s'); //You can also use object as an array
ConnectionManager->getInstance()->persist($aTransation);
```

##### Remove record from database
```php
$aTransaction = new Transaction($transactionId);
$aTransation->remove();
```

### HistoryableRecord

You can use HistoryableRecord to store previous state of a record.

#### Implement a HistoryableRecord class

Assume that we have a MySQL table named as transactions and the table has the following columns (all of them required):
* sequence_id (PRIMARY KEY)
* id (record identifier)
* record_status (0|1 -> inactive|active)
* history_from (datetime)
* history_to (datetime)

```mysql
CREATE TABLE `database_name`.`transactions` (
    `transaction_sequence_id` int(11) NOT NULL AUTO_INCREMENT,
	`transaction_id` int(11),
	`record_status` int(1),
	`history_from` datetime,
	`history_to` datetime,
	`transaction_value` int(11),
	`user_id` int(11),
	`store_date` datetime,
	PRIMARY KEY (`transaction_sequence_id`)
) ENGINE=`InnoDB` COMMENT='';
```
Implement Transaction class:
```php
class Transaction extends HistoryableRecord {

}
```
In next step you have to add a TableDescriptor to class which contains the name of the database and name of the primary key and
you must overwrite the parent's constructor.
```php
class Transaction extends HistoryableRecord {
    function __construct($id, $values = null)
    {
        $tableDescriptor = new TableDescriptor([
            TABLE_NAME  =>  "transactions",
            TABLE_SEQ_ID=>  "transaction_sequence_id",
            TABLE_ID    =>  "transaction_id"
        ]);
        parent::__construct($tableDescriptor,$id,$values);
    }
}
```

#### Usage of a class inherited from HistoryableRecord

Same as in case of SimpleRecord.