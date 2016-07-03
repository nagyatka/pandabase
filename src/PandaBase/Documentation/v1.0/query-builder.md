# How to use QueryBuilder

QueryBuilder helps to write complex MYSQL queries. It does not contain any black magic. It simply assembles different
parts of a query. It is really useful when you have to implement a search functionality.

### Create QueryBuilder instance
`$sql` must contain SELECT, FROM and JOIN parts of a query string.
```php
$sql = "
    SELECT *
    FROM table1
    JOIN table2 ON table1.id = table2.t1_id
";
$queryBuilder = new QueryBuilder($sql,[
    "param1"    =>  $param1,
    "param2"    =>  $param2,
    "param3"    =>  $param3
]);
```