<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 16. 02. 28.
 * Time: 14:46
 */

namespace PandaBase\Test;

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
            TABLE_NAME  =>  "pp_simple_table",
            TABLE_ID    =>  "table_id",
        ]);
        parent::__construct(TestRecord::$tableDescriptor, $id, $values);
    }

} 