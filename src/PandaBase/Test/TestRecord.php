<?php

namespace PandaBase\Test;

use PandaBase\Connection\TableDescriptor;
use PandaBase\Record\SimpleRecord;

class TestRecord extends SimpleRecord {

    /**
     * @param int $id
     * @param null $values
     */
    function __construct($id, $values = null)
    {
        $tableDescriptor = new TableDescriptor([
            TABLE_NAME  =>  "pp_simple_table",
            TABLE_ID    =>  "table_id",
        ]);
        parent::__construct($tableDescriptor,$id,$values);
    }

} 